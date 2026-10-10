<?php

namespace Webkul\ViaSocket\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;
use Webkul\ViaSocket\Repositories\ViaSocketFlowRepository;
use Webkul\ViaSocket\Repositories\ViaSocketFlowRunRepository;

class RunViaSocketFlow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * The number of seconds the job may run.
     */
    public int $timeout = 20;

    /**
     * Create a new job instance.
     */
    public function __construct(protected int $flowId, protected array $payload) {}

    /**
     * Post the payload to the flow. A failing flow is logged and never breaks the CRM.
     */
    public function handle(ViaSocketFlowRepository $viaSocketFlowRepository, ViaSocketFlowRunRepository $viaSocketFlowRunRepository): void
    {
        $flow = $viaSocketFlowRepository->find($this->flowId);

        if (! $flow || $flow->status !== 'active') {
            return;
        }

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->post(config('viasocket.flow_url').'/'.$flow->script_id, $this->payload);

            $ok = $response->successful() && $response->json('success') !== false;

            $viaSocketFlowRunRepository->create([
                'flow_id' => $flow->id,
                'event' => $this->payload['event'] ?? null,
                'ok' => $ok,
                'status_code' => $response->status(),
                'message' => mb_substr((string) ($response->json('message') ?: $response->body()), 0, 500),
            ]);

            if (! $ok) {
                Log::warning('An integration flow returned an error.', ['flow' => $flow->id, 'status' => $response->status()]);
            }
        } catch (Throwable $exception) {
            $viaSocketFlowRunRepository->create([
                'flow_id' => $flow->id,
                'event' => $this->payload['event'] ?? null,
                'ok' => false,
                'message' => mb_substr($exception->getMessage(), 0, 500),
            ]);

            Log::warning('An integration flow could not be reached.', ['flow' => $flow->id, 'error' => $exception->getMessage()]);
        }
    }
}
