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
     * Display one app with its Connect or Disconnect button.
     */
    public function show(string $serviceId): View
    {
        $app = IntegrationRegistry::find($serviceId);

        abort_unless($app, 404);

        $details = $this->catalogService->find($serviceId);

        $connection = $this->viaSocketConnectionRepository->findOneWhere([
            'user_id' => $this->userId(),
            'service_id' => $serviceId,
        ]);

        return view('viasocket::show', [
            'serviceId' => $serviceId,
            'name' => $details['name'] ?? $app['label'],
            'icon' => $details['icon'] ?? null,
            'category' => $app['category'],
            'connected' => (bool) $connection,
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
     * Store the connection a user just made in the viaSocket popup.
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
            ['auth_id' => $data['auth_id']]
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Revoke the connection on viaSocket, then forget it. A connection viaSocket no longer knows is simply forgotten.
     */
    public function destroy(string $serviceId): JsonResponse
    {
        abort_unless(IntegrationRegistry::find($serviceId), 404);

        $connection = $this->viaSocketConnectionRepository->findOneWhere([
            'user_id' => $this->userId(),
            'service_id' => $serviceId,
        ]);

        if (! $connection) {
            return response()->json(['ok' => true]);
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['authorization' => $this->embedTokenService->make($this->identifier())])
                ->delete(config('viasocket.api_url').'/embed/authentications/revoke/'.$connection->auth_id);
        } catch (Throwable) {
            return response()->json(['message' => 'Could not reach viaSocket.'], 502);
        }

        if ($response->serverError()) {
            return response()->json(['message' => 'viaSocket could not disconnect this app.'], 502);
        }

        $connection->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * The signed-in admin user's id.
     */
    protected function userId(): int
    {
        return (int) auth()->guard('user')->id();
    }

    /**
     * The stable viaSocket identifier for the signed-in admin user.
     */
    protected function identifier(): string
    {
        return 'krayin-user-'.$this->userId();
    }
}
