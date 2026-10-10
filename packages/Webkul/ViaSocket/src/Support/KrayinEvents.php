<?php

namespace Webkul\ViaSocket\Support;

class KrayinEvents
{
    /**
     * The Krayin events a flow can start from, with a sample of the data each one carries.
     */
    public static function all(): array
    {
        $emails = [['value' => 'jane@example.com', 'label' => 'work']];

        $numbers = [['value' => '+1 555 0100', 'label' => 'work']];

        return [
            'lead.create.after' => [
                'name' => 'Lead created',
                'entity' => 'lead',
                'sample' => ['id' => 1, 'title' => 'Website redesign', 'description' => 'Inbound lead', 'lead_value' => 5000, 'expected_close_date' => '2026-12-31', 'person' => ['name' => 'Jane Doe', 'emails' => $emails, 'contact_numbers' => $numbers]],
            ],

            'lead.update.after' => [
                'name' => 'Lead updated',
                'entity' => 'lead',
                'sample' => ['id' => 1, 'title' => 'Website redesign', 'description' => 'Inbound lead', 'lead_value' => 6500, 'expected_close_date' => '2026-12-31', 'person' => ['name' => 'Jane Doe', 'emails' => $emails, 'contact_numbers' => $numbers]],
            ],

            'contacts.person.create.after' => [
                'name' => 'Person created',
                'entity' => 'person',
                'sample' => ['id' => 1, 'name' => 'Jane Doe', 'emails' => $emails, 'contact_numbers' => $numbers, 'job_title' => 'CTO'],
            ],

            'contacts.person.update.after' => [
                'name' => 'Person updated',
                'entity' => 'person',
                'sample' => ['id' => 1, 'name' => 'Jane Doe', 'emails' => $emails, 'contact_numbers' => $numbers, 'job_title' => 'CEO'],
            ],

            'quote.create.after' => [
                'name' => 'Quote created',
                'entity' => 'quote',
                'sample' => ['id' => 1, 'subject' => 'Quote for Acme', 'description' => 'Annual plan', 'grand_total' => 1200],
            ],

            'quote.update.after' => [
                'name' => 'Quote updated',
                'entity' => 'quote',
                'sample' => ['id' => 1, 'subject' => 'Quote for Acme', 'description' => 'Annual plan', 'grand_total' => 1500],
            ],

            'activity.create.after' => [
                'name' => 'Activity created',
                'entity' => 'activity',
                'sample' => ['id' => 1, 'title' => 'Call Jane', 'type' => 'call', 'comment' => 'Discuss the proposal', 'schedule_from' => '2026-10-12 10:00:00', 'is_done' => 0],
            ],

            'activity.update.after' => [
                'name' => 'Activity updated',
                'entity' => 'activity',
                'sample' => ['id' => 1, 'title' => 'Call Jane', 'type' => 'call', 'comment' => 'Discuss the proposal', 'schedule_from' => '2026-10-12 10:00:00', 'is_done' => 1],
            ],
        ];
    }

    /**
     * The data posted to a flow when the event happens.
     */
    public static function payload(string $event, mixed $model): array
    {
        $data = collect($model->attributesToArray())
            ->filter(fn ($value) => is_scalar($value) || is_null($value) || is_array($value))
            ->all();

        if ((static::all()[$event]['entity'] ?? null) === 'lead' && $model->person) {
            $data['person'] = [
                'name' => $model->person->name,
                'emails' => $model->person->emails,
                'contact_numbers' => $model->person->contact_numbers,
            ];
        }

        return ['event' => $event, 'data' => $data];
    }
}
