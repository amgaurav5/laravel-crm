<?php

namespace Webkul\ViaSocket\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FlowService
{
    /**
     * Create a new flow service instance.
     */
    public function __construct(protected EmbedTokenService $embedTokenService) {}

    /**
     * Create or update a flow and publish it. Saving the trigger and the steps publishes it.
     *
     * @param  array  $trigger  The remote trigger fields.
     * @param  array  $steps  The remote steps.
     */
    public function publish(string $identifier, ?string $scriptId, string $title, array $trigger, array $steps): string
    {
        $token = $this->proxyToken($identifier);

        $projectId = config('viasocket.project_id');

        if (! $scriptId) {
            $created = $this->call('create_flow', [
                'title' => $title,
                'description' => 'Created in Krayin',
                'projectId' => $projectId,
                'proxy_auth_token' => $token,
            ]);

            $scriptId = $created->json('data.script_id');

            if (! $scriptId) {
                throw new RuntimeException('The flow could not be created.');
            }
        }

        $this->call('set_trigger', $trigger + [
            'scriptId' => $scriptId,
            'projectId' => $projectId,
            'proxy_auth_token' => $token,
        ]);

        $this->call('save_steps', [
            'scriptId' => $scriptId,
            'steps' => $steps,
            'proxy_auth_token' => $token,
        ]);

        return $scriptId;
    }

    /**
     * Pause or resume a published flow.
     */
    public function setActive(string $identifier, string $scriptId, bool $active): void
    {
        $response = Http::timeout(20)
            ->withHeaders(['authorization' => $this->embedTokenService->make($identifier)])
            ->put(config('viasocket.api_url').'/embed/updatestatus/'.$scriptId.'?status='.($active ? 1 : 0));

        if ($response->failed()) {
            throw new RuntimeException('The flow could not be updated.');
        }
    }

    /**
     * Short-lived token for building flows as one user. Derived on every use and never stored.
     */
    protected function proxyToken(string $identifier): string
    {
        $response = Http::timeout(20)
            ->withHeaders(['Authorization' => $this->embedTokenService->make($identifier)])
            ->get(config('viasocket.api_url').'/users/register');

        $token = $response->json('data.token');

        if (! $response->successful() || ! $token) {
            throw new RuntimeException('Could not start a session.');
        }

        return $token;
    }

    /**
     * Post one of the flow-building calls.
     */
    protected function call(string $script, array $body): Response
    {
        $response = Http::timeout(45)
            ->acceptJson()
            ->asJson()
            ->post(config('viasocket.flow_url').'/'.config('viasocket.scripts.'.$script), $body);

        if ($response->failed() || $response->json('success') === false) {
            throw new RuntimeException($response->json('message') ?: 'The flow could not be saved.');
        }

        return $response;
    }
}
