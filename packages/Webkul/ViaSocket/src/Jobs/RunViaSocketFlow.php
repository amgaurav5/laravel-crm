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
    public function handle(ViaSocketFlowRepository $viaSocketFlowRepository): void
    {
        $flow = $viaSocketFlowRepository->find($this->flowId);

        if (! $flow || $flow->status !== 'active') {
            return;
        }

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->post(config('viasocket.flow_url').'/'.$flow->script_id, $this->payload);

            if ($response->failed()) {
                Log::warning('An integration flow returned an error.', ['flow' => $flow->id, 'status' => $response->status()]);
            }
        } catch (Throwable $exception) {
            Log::warning('An integration flow could not be reached.', ['flow' => $flow->id, 'error' => $exception->getMessage()]);
        }
    }
}
