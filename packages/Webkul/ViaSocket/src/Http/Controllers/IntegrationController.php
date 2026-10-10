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
use Webkul\ViaSocket\Repositories\ViaSocketFlowRepository;
use Webkul\ViaSocket\Services\CatalogService;
use Webkul\ViaSocket\Services\EmbedTokenService;
use Webkul\ViaSocket\Support\KrayinEvents;

class IntegrationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected EmbedTokenService $embedTokenService,
        protected CatalogService $catalogService,
        protected ViaSocketConnectionRepository $viaSocketConnectionRepository,
        protected ViaSocketFlowRepository $viaSocketFlowRepository,
    ) {}

    /**
     * Display the integrations page: the user's flows and the flow builder.
     */
    public function index(): View
    {
        return view('viasocket::index', [
            'connectScript' => config('viasocket.connect_script'),
            'krayinEvents' => KrayinEvents::all(),
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
     * The apps to pick from.
     */
    public function apps(Request $request): JsonResponse
    {
        return response()->json(['apps' => $this->catalogService->apps($request->query('q'))]);
    }

    /**
     * One app with its actions, triggers and whether the user has connected it.
     */
    public function app(string $serviceId): JsonResponse
    {
        $app = $this->catalogService->app($serviceId);

        abort_unless($app, 404);

        $app['connected'] = ! $app['requires_auth'] || (bool) $this->connection($serviceId);

        return response()->json($app);
    }

    /**
     * The input fields of one action or trigger.
     */
    public function schema(string $serviceId, string $kind, string $versionId): JsonResponse
    {
        $schema = $this->catalogService->schema($serviceId, $kind, $versionId);

        abort_unless($schema, 404);

        return response()->json($schema);
    }

    /**
     * The choices of a dropdown field, read through the user's own connection.
     */
    public function options(Request $request, string $serviceId): JsonResponse
    {
        $data = $request->validate([
            'version_id' => ['required', 'string', 'max:64'],
            'field_key' => ['required', 'string', 'max:191'],
            'existing_fields' => ['nullable', 'array'],
        ]);

        abort_unless($this->catalogService->hasVersion($serviceId, $data['version_id']), 404);

        $connection = $this->connection($serviceId);

        abort_unless($connection, 422);

        try {
            $response = Http::timeout(30)
                ->withHeaders(['authorization' => $this->embedTokenService->make($this->identifier())])
                ->post(config('viasocket.api_url').'/embed/list-options/'.$data['version_id'], [
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
     * The apps the signed-in user has connected, with the flows that use each one.
     */
    public function connections(): JsonResponse
    {
        $connections = $this->viaSocketConnectionRepository
            ->findWhere(['user_id' => $this->userId()])
            ->sortByDesc('id')
            ->map(function ($connection) {
                $app = $this->catalogService->app($connection->service_id);

                return [
                    'service_id' => $connection->service_id,
                    'name' => $app['name'] ?? $connection->service_id,
                    'icon' => $app['icon'] ?? null,
                    'connected_at' => $connection->created_at?->toDateTimeString(),
                    'used_by' => $this->flowsUsing($connection->service_id),
                ];
            })
            ->values();

        return response()->json(['connections' => $connections]);
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

        abort_unless($this->catalogService->app($data['service_id']), 404);

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
        if (! $connection = $this->connection($serviceId)) {
            return response()->json(['ok' => true]);
        }

        if ($flows = $this->flowsUsing($serviceId)) {
            return response()->json(['message' => 'Still used by: '.implode(', ', $flows).'. Delete or change those flows first.'], 422);
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
     * Titles of the signed-in user's flows that use an app.
     */
    protected function flowsUsing(string $serviceId): array
    {
        return $this->viaSocketFlowRepository
            ->findWhere(['user_id' => $this->userId()])
            ->filter(fn ($flow) => ($flow->trigger['service_id'] ?? null) === $serviceId
                || collect($flow->steps)->contains('service_id', $serviceId))
            ->pluck('title')
            ->values()
            ->all();
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
