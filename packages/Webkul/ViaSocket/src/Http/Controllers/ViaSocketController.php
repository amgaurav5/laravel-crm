<?php

namespace Webkul\ViaSocket\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Throwable;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\ViaSocket\Repositories\ViaSocketConnectionRepository;
use Webkul\ViaSocket\Services\CatalogService;
use Webkul\ViaSocket\Services\EmbedTokenService;
use Webkul\ViaSocket\Support\IntegrationRegistry;

class ViaSocketController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected EmbedTokenService $embedTokenService,
        protected CatalogService $catalogService,
        protected ViaSocketConnectionRepository $viaSocketConnectionRepository,
    ) {}

    /**
     * Display one app with its Connect or Disconnect button and, once connected, its input fields.
     */
    public function show(string $serviceId): View
    {
        $app = IntegrationRegistry::find($serviceId);

        abort_unless($app, 404);

        $details = $this->catalogService->find($serviceId);

        return view('viasocket::show', [
            'serviceId' => $serviceId,
            'name' => $details['name'] ?? $app['label'],
            'breadcrumb' => 'settings.integrations.'.$app['slug'],
            'connected' => (bool) $this->connection($serviceId),
            'connectScript' => config('viasocket.connect_script'),
        ]);
    }

    /**
     * Sign an embed token for the signed-in admin user.
     */
    public function token(): Response
    {
        return response($this->embedTokenService->make($this->identifier()), 200, [
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'no-store',
        ]);
    }

    /**
     * Store the connection a user just made in the popup.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_id' => ['required', 'string', 'max:64'],
            'auth_id' => ['required', 'string', 'max:191', 'regex:/^[A-Za-z0-9_]+$/'],
        ]);

        abort_unless(IntegrationRegistry::find($data['service_id']), 404);

        abort_unless(str_ends_with($data['auth_id'], '_'.$data['service_id']), 422);

        $this->viaSocketConnectionRepository->updateOrCreate(
            ['user_id' => $this->userId(), 'service_id' => $data['service_id']],
            ['auth_id' => $data['auth_id'], 'script_id' => null]
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Revoke the connection, then forget it. A connection the provider no longer knows is simply forgotten.
     */
    public function destroy(string $serviceId): JsonResponse
    {
        abort_unless(IntegrationRegistry::find($serviceId), 404);

        if (! $connection = $this->connection($serviceId)) {
            return response()->json(['ok' => true]);
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['authorization' => $this->embedTokenService->make($this->identifier())])
                ->delete(config('viasocket.api_url').'/embed/authentications/revoke/'.$connection->auth_id);
        } catch (Throwable) {
            return response()->json(['message' => 'Could not disconnect this app. Please try again.'], 502);
        }

        if ($response->serverError()) {
            return response()->json(['message' => 'Could not disconnect this app. Please try again.'], 502);
        }

        $connection->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * The actions the app offers.
     */
    public function actions(string $serviceId): JsonResponse
    {
        abort_unless(IntegrationRegistry::find($serviceId), 404);

        return response()->json(['actions' => $this->catalogService->actions($serviceId)]);
    }

    /**
     * The input fields of one action.
     */
    public function schema(string $serviceId, string $actionVersionId): JsonResponse
    {
        abort_unless(IntegrationRegistry::find($serviceId), 404);

        $schema = $this->catalogService->schema($serviceId, $actionVersionId);

        abort_unless($schema, 404);

        return response()->json($schema);
    }

    /**
     * The choices of a dropdown field, read through the user's own connection.
     */
    public function options(Request $request, string $serviceId): JsonResponse
    {
        $data = $request->validate([
            'action_version_id' => ['required', 'string', 'max:64'],
            'field_key' => ['required', 'string', 'max:191'],
            'existing_fields' => ['nullable', 'array'],
        ]);

        abort_unless(IntegrationRegistry::find($serviceId) && $this->catalogService->schema($serviceId, $data['action_version_id']), 404);

        $connection = $this->connection($serviceId);

        abort_unless($connection, 422);

        try {
            $response = Http::timeout(30)
                ->withHeaders(['authorization' => $this->embedTokenService->make($this->identifier())])
                ->post(config('viasocket.api_url').'/embed/list-options/'.$data['action_version_id'], [
                    'fieldKey' => $data['field_key'],
                    'auth_id' => $connection->auth_id,
                    'existingFields' => (object) ($data['existing_fields'] ?? []),
                ]);
        } catch (Throwable) {
            return response()->json(['message' => 'Could not load the choices.'], 502);
        }

        $list = $response->json('data');

        if (is_array($list) && isset($list['response']['status']) && (int) $list['response']['status'] >= 400) {
            return response()->json(['message' => $list['response']['data']['message'] ?? 'Could not load the choices.'], 422);
        }

        if (! $response->successful() || ! is_array($list)) {
            return response()->json(['message' => 'Could not load the choices.'], 502);
        }

        return response()->json(['options' => array_is_list($list) ? $list : ($list['data'] ?? [])]);
    }

    /**
     * Run the action with the values the user filled in.
     */
    public function run(Request $request, string $serviceId): JsonResponse
    {
        $data = $request->validate([
            'action_version_id' => ['required', 'string', 'max:64'],
            'input_data' => ['nullable', 'array'],
        ]);

        abort_unless(IntegrationRegistry::find($serviceId) && $this->catalogService->schema($serviceId, $data['action_version_id']), 404);

        $connection = $this->connection($serviceId);

        abort_unless($connection, 422);

        try {
            if (! $connection->script_id) {
                $enabled = Http::timeout(20)
                    ->withHeaders(['authorization' => $this->embedTokenService->make($this->identifier())])
                    ->post(config('viasocket.api_url').'/embed/enable/'.$serviceId.'/'.$connection->auth_id);

                $scriptId = $enabled->json('data.script_id');

                if (! $enabled->successful() || ! $scriptId) {
                    return response()->json(['message' => 'Could not prepare this app. Please reconnect it and try again.'], 502);
                }

                $connection->update(['script_id' => $scriptId]);
            }

            $response = Http::timeout(60)
                ->acceptJson()
                ->post(config('viasocket.run_url').'/'.$connection->script_id, [
                    'action_version_id' => $data['action_version_id'],
                    'inputData' => (object) ($data['input_data'] ?? []),
                ]);
        } catch (Throwable) {
            return response()->json(['message' => 'Could not reach the app. Please try again.'], 502);
        }

        return response()->json([
            'ok' => $response->successful() && $response->json('success') !== false,
            'result' => $response->json() ?? $response->body(),
        ]);
    }

    /**
     * The signed-in user's connection to an app.
     */
    protected function connection(string $serviceId)
    {
        return $this->viaSocketConnectionRepository->findOneWhere([
            'user_id' => $this->userId(),
            'service_id' => $serviceId,
        ]);
    }

    /**
     * The signed-in admin user's id.
     */
    protected function userId(): int
    {
        return (int) auth()->guard('user')->id();
    }

    /**
     * The stable identifier for the signed-in admin user.
     */
    protected function identifier(): string
    {
        return 'krayin-user-'.$this->userId();
    }
}
