<?php

namespace Webkul\ViaSocket\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\ViaSocket\Repositories\ViaSocketFlowRepository;
use Webkul\ViaSocket\Services\EmbedTokenService;

class ViaSocketController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected EmbedTokenService $embedTokenService,
        protected ViaSocketFlowRepository $viaSocketFlowRepository,
    ) {}

    /**
     * Display the viaSocket integrations page.
     */
    public function index(): View
    {
        return view('viasocket::index', [
            'embedScript' => config('viasocket.embed_script'),
            'embedOrigin' => config('viasocket.embed_origin'),
        ]);
    }

    /**
     * Sign an embed token for the signed-in admin user.
     */
    public function token(): Response
    {
        $token = $this->embedTokenService->make('krayin-user-'.auth()->guard('user')->id());

        return response($token, 200, [
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'no-store',
        ]);
    }

    /**
     * Store, pause or remove a flow that a user built in the embedded UI.
     */
    public function storeFlow(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => ['required', 'string', 'max:191'],
            'action' => ['required', 'in:initiated,published,updated,paused,deleted'],
            'title' => ['nullable', 'string', 'max:255'],
            'webhookurl' => ['nullable', 'string', 'max:2048'],
            'payload' => ['nullable', 'array'],
        ]);

        $flow = $this->viaSocketFlowRepository->findOneByField('flow_id', $data['id']);

        switch ($data['action']) {
            case 'initiated':
                break;

            case 'deleted':
                $flow?->delete();

                break;

            case 'paused':
                $flow?->update(['status' => 'paused']);

                break;

            default:
                if (! $this->isFlowUrl($data['webhookurl'] ?? null)) {
                    return response()->json(['message' => 'Invalid flow URL.'], 422);
                }

                $this->viaSocketFlowRepository->updateOrCreate(
                    ['flow_id' => $data['id']],
                    [
                        'user_id' => auth()->guard('user')->id(),
                        'title' => $data['title'] ?? null,
                        'webhook_url' => $data['webhookurl'],
                        'status' => 'active',
                        'payload' => $data['payload'] ?? null,
                    ]
                );
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Only accept viaSocket flow URLs, so the server never posts to an arbitrary host.
     */
    protected function isFlowUrl(?string $url): bool
    {
        return (bool) preg_match('#^https://flow\.sokt\.io/func/[A-Za-z0-9_-]+$#', (string) $url);
    }
}
