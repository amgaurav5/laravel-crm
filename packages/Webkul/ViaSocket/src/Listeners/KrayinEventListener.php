<?php

namespace Webkul\ViaSocket\Listeners;

use Webkul\ViaSocket\Jobs\RunViaSocketFlow;
use Webkul\ViaSocket\Repositories\ViaSocketFlowRepository;
use Webkul\ViaSocket\Support\KrayinEvents;

class KrayinEventListener
{
    /**
     * Create a new listener instance.
     */
    public function __construct(protected ViaSocketFlowRepository $viaSocketFlowRepository) {}

    /**
     * Run every active flow that starts from this Krayin event.
     */
    public function handle(string $event, mixed $model): void
    {
        $flows = $this->viaSocketFlowRepository->findWhere(['status' => 'active'])
            ->filter(fn ($flow) => ($flow->trigger['kind'] ?? null) === 'krayin' && ($flow->trigger['event'] ?? null) === $event);

        if ($flows->isEmpty()) {
            return;
        }

        $payload = KrayinEvents::payload($event, $model);

        foreach ($flows as $flow) {
            RunViaSocketFlow::dispatch($flow->id, $payload);
        }
    }
}
