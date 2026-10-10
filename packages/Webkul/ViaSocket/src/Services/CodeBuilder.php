<?php

namespace Webkul\ViaSocket\Services;

use Illuminate\Support\Str;

class CodeBuilder
{
    /**
     * One flow step that runs one action, filling {{path}} placeholders from the trigger's data.
     */
    public function step(int $index, array $step, string $authId): array
    {
        $name = 'step_'.($index + 1).'_'.Str::of($step['name'])->snake()->replaceMatches('/[^a-z0-9_]+/', '_')->trim('_')->limit(30, '');

        $input = json_encode((object) ($step['values'] ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);

        $code = <<<'JS'
const body = context?.req?.body || {};
const lookup = (path) => String(path).split('.').reduce((node, key) => node?.[key], body);
const fill = (value) => typeof value === 'string'
    ? value.replace(/\{\{\s*([^}]+?)\s*\}\}/g, (match, path) => {
        const found = lookup(path);

        return found === undefined || found === null ? '' : (typeof found === 'object' ? JSON.stringify(found) : String(found));
    })
    : Array.isArray(value)
        ? value.map(fill)
        : (value && typeof value === 'object')
            ? Object.fromEntries(Object.entries(value).map(([key, item]) => [key, fill(item)]))
            : value;

JS;

        return [
            'stepname' => (string) $name,
            'code' => $code.'return await act({ inputData: fill('.$input.') });',
            'service_action_functions' => [
                ['name' => 'act', 'auth_id' => $authId, 'action_id' => $step['action_id']],
            ],
        ];
    }
}
