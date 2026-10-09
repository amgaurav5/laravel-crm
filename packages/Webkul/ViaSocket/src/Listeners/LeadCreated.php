<?php

namespace Webkul\ViaSocket\Listeners;

use Webkul\ViaSocket\Jobs\RunViaSocketFlow;
use Webkul\ViaSocket\Repositories\ViaSocketFlowRepository;

class LeadCreated
{
    /**
     * Create a new listener instance.
     */
    public function __construct(protected ViaSocketFlowRepository $viaSocketFlowRepository) {}

    /**
     * Run every active viaSocket flow when a lead is created.
     */
    public function handle($lead): void
    {
        $flows = $this->viaSocketFlowRepository->findWhere(['status' => 'active']);

        if ($flows->isEmpty()) {
            return;
        }

        $payload = [
            'event' => 'lead.create.after',
            'lead' => [
                'id' => $lead->id,
                'title' => $lead->title,
                'description' => $lead->description,
                'lead_value' => $lead->lead_value,
                'expected_close_date' => $lead->expected_close_date,
                'person' => $lead->person ? [
                    'name' => $lead->person->name,
                    'emails' => $lead->person->emails,
                    'contact_numbers' => $lead->person->contact_numbers,
                ] : null,
            ],
        ];

        foreach ($flows as $flow) {
            RunViaSocketFlow::dispatch($flow->id, $payload);
        }
    }
}
