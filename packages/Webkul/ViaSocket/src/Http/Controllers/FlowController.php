<?php

namespace Webkul\ViaSocket\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\ViaSocket\Repositories\ViaSocketConnectionRepository;
use Webkul\ViaSocket\Repositories\ViaSocketFlowRepository;
use Webkul\ViaSocket\Services\CatalogService;
use Webkul\ViaSocket\Services\CodeBuilder;
use Webkul\ViaSocket\Services\FlowService;
use Webkul\ViaSocket\Support\KrayinEvents;

class FlowController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected FlowService $flowService,
        protected CodeBuilder $codeBuilder,
        protected CatalogService $catalogService,
        protected ViaSocketFlowRepository $viaSocketFlowRepository,
        protected ViaSocketConnectionRepository $viaSocketConnectionRepository,
    ) {}

    /**
     * The signed-in user's flows.
     */
    public function index(): JsonResponse
    {
        $flows = $this->viaSocketFlowRepository
            ->findWhere(['user_id' => $this->userId()])
            ->sortByDesc('id')
            ->values()
            ->map(fn ($flow) => $this->present($flow));

        return response()->json(['flows' => $flows]);
    }

    /**
     * Create a flow and publish it.
     */
    public function store(Request $request): JsonResponse
    {
        return $this->save($request, null);
    }

    /**
     * Change a flow and publish it again.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->save($request, $this->owned($id));
    }

    /**
     * Pause or resume a flow.
     */
    public function status(Request $request, int $id): JsonResponse
    {
        $flow = $this->owned($id);

        $active = $request->boolean('active');

        try {
            $this->flowService->setActive($this->identifier(), $flow->script_id, $active);
        } catch (Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 502);
        }

        $flow->update(['status' => $active ? 'active' : 'paused']);

        return response()->json(['flow' => $this->present($flow)]);
    }

    /**
     * Pause a flow on the provider and forget it here.
     */
    public function destroy(int $id): JsonResponse
    {
        $flow = $this->owned($id);

        try {
            $this->flowService->setActive($this->identifier(), $flow->script_id, false);
        } catch (Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 502);
        }

        $flow->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * Validate the definition, publish it and store it.
     */
    protected function save(Request $request, $flow): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'trigger' => ['required', 'array'],
            'trigger.kind' => ['required', 'in:krayin,schedule,app'],
            'trigger.label' => ['nullable', 'string', 'max:200'],
            'trigger.event' => ['required_if:trigger.kind,krayin', 'nullable', 'string', 'max:64'],
            'trigger.cron' => ['required_if:trigger.kind,schedule', 'nullable', 'string', 'max:120'],
            'trigger.service_id' => ['required_if:trigger.kind,app', 'nullable', 'string', 'max:64'],
            'trigger.trigger_id' => ['required_if:trigger.kind,app', 'nullable', 'string', 'max:64'],
            'trigger.values' => ['nullable', 'array'],
            'trigger.fields' => ['nullable', 'array'],
            'trigger.sample' => ['nullable'],
            'steps' => ['required', 'array', 'min:1', 'max:10'],
            'steps.*.service_id' => ['required', 'string', 'max:64'],
            'steps.*.action_id' => ['required', 'string', 'max:64'],
            'steps.*.name' => ['required', 'string', 'max:200'],
            'steps.*.app_name' => ['nullable', 'string', 'max:200'],
            'steps.*.icon' => ['nullable', 'string', 'max:500'],
            'steps.*.values' => ['nullable', 'array'],
            'steps.*.fields' => ['nullable', 'array'],
        ]);

        try {
            $remoteTrigger = $this->remoteTrigger($data['trigger']);

            $remoteSteps = [];

            foreach ($data['steps'] as $index => $step) {
                $remoteSteps[] = $this->codeBuilder->step($index, $step, $this->authFor($step['service_id'], $step['action_id'], 'actions'));
            }

            $scriptId = $this->flowService->publish($this->identifier(), $flow?->script_id, $data['title'], $remoteTrigger, $remoteSteps);
        } catch (Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        $attributes = [
            'user_id' => $this->userId(),
            'script_id' => $scriptId,
            'title' => $data['title'],
            'trigger' => $data['trigger'],
            'steps' => $data['steps'],
            'status' => 'active',
        ];

        $flow = $flow ? tap($flow)->update($attributes) : $this->viaSocketFlowRepository->create($attributes);

        return response()->json(['flow' => $this->present($flow)]);
    }

    /**
     * The trigger in the form the provider expects.
     */
    protected function remoteTrigger(array $trigger): array
    {
        if ($trigger['kind'] === 'krayin') {
            abort_unless(isset(KrayinEvents::all()[$trigger['event']]), 422, 'Unknown event.');

            return ['triggerType' => 'webhook'];
        }

        if ($trigger['kind'] === 'schedule') {
            return ['triggerType' => 'cron', 'cronStatement' => $trigger['cron']];
        }

        return [
            'triggerType' => 'plugin',
            'trigger_id' => $trigger['trigger_id'],
            'auth_id' => $this->authFor($trigger['service_id'], $trigger['trigger_id'], 'triggers'),
            'inputData' => (object) ($trigger['values'] ?? []),
        ];
    }

    /**
     * The connection an app needs, or "NoAuth" for an app without sign-in.
     */
    protected function authFor(string $serviceId, string $itemId, string $kind): string
    {
        $app = $this->catalogService->app($serviceId);

        abort_unless($app && collect($app[$kind])->contains('id', $itemId), 422, 'Unknown app or action.');

        if (! $app['requires_auth']) {
            return 'NoAuth';
        }

        $connection = $this->viaSocketConnectionRepository->findOneWhere([
            'user_id' => $this->userId(),
            'service_id' => $serviceId,
        ]);

        abort_unless($connection, 422, 'Connect '.$app['name'].' first.');

        return $connection->auth_id;
    }

    /**
     * A flow of the signed-in user.
     */
    protected function owned(int $id)
    {
        $flow = $this->viaSocketFlowRepository->findOneWhere(['id' => $id, 'user_id' => $this->userId()]);

        abort_unless($flow, 404);

        return $flow;
    }

    /**
     * A flow as the page shows it. The provider's flow id stays on the server.
     */
    protected function present($flow): array
    {
        return [
            'id' => $flow->id,
            'title' => $flow->title,
            'status' => $flow->status,
            'trigger' => $flow->trigger,
            'steps' => $flow->steps,
            'created_at' => $flow->created_at?->toDateTimeString(),
        ];
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
