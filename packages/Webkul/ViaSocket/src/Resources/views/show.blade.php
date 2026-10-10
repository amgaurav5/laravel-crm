<x-admin::layouts>
    <x-slot:title>
        {{ $name }}
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex items-center gap-3">
                @if ($icon)
                    <img src="{{ $icon }}" alt="" width="32" height="32" class="rounded">
                @endif

                <div class="flex flex-col gap-1">
                    <div class="text-xl font-bold dark:text-white">
                        {{ $name }}
                    </div>

                    <p class="text-gray-600 dark:text-gray-300">
                        {{ $category }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-300 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p
                id="viasocket-status"
                class="mb-4 text-gray-600 dark:text-gray-300"
            >
                {{ $connected
                    ? trans('viasocket::app.show.connected-info', ['name' => $name])
                    : trans('viasocket::app.show.not-connected-info', ['name' => $name]) }}
            </p>

            <button
                type="button"
                id="viasocket-connect"
                class="primary-button"
                @if ($connected) style="display: none" @endif
            >
                @if ($icon)
                    <img src="{{ $icon }}" alt="" width="20" height="20" style="display: inline-block; vertical-align: middle; margin-right: 6px">
                @endif

                @lang('viasocket::app.show.connect-btn', ['name' => $name])
            </button>

            <button
                type="button"
                id="viasocket-disconnect"
                class="secondary-button"
                @if (! $connected) style="display: none" @endif
            >
                @lang('viasocket::app.show.disconnect-btn')
            </button>

            <p
                id="viasocket-error"
                class="mt-3 text-sm text-red-600"
                style="display: none"
            ></p>

            <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                @lang('viasocket::app.show.powered-by')
            </p>
        </div>
    </div>

    @pushOnce('scripts')
        <script src="{{ $connectScript }}"></script>

        <script>
            (() => {
                const serviceId = @json($serviceId);
                const csrfToken = @json(csrf_token());
                const messages = {
                    connected: @json(trans('viasocket::app.show.connected-info', ['name' => $name])),
                    notConnected: @json(trans('viasocket::app.show.not-connected-info', ['name' => $name])),
                    error: @json(trans('viasocket::app.show.error')),
                };

                const connectButton = document.getElementById('viasocket-connect');
                const disconnectButton = document.getElementById('viasocket-disconnect');
                const status = document.getElementById('viasocket-status');
                const error = document.getElementById('viasocket-error');

                const request = (url, method, body) => fetch(url, {
                    method,
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: body ? JSON.stringify(body) : undefined,
                });

                const show = (connected) => {
                    error.style.display = 'none';
                    status.textContent = connected ? messages.connected : messages.notConnected;
                    connectButton.style.display = connected ? 'none' : '';
                    disconnectButton.style.display = connected ? '' : 'none';
                };

                const fail = (message) => {
                    error.textContent = message || messages.error;
                    error.style.display = '';
                };

                connectButton.addEventListener('click', async () => {
                    const embedToken = await fetch(@json(route('admin.settings.integrations.token')), {
                        credentials: 'same-origin',
                    }).then((response) => response.text());

                    openViasocketConnection(embedToken, serviceId);
                });

                disconnectButton.addEventListener('click', async () => {
                    const response = await request(@json(route('admin.settings.integrations.connections.destroy', ['serviceId' => $serviceId])), 'DELETE');

                    response.ok ? show(false) : fail((await response.json().catch(() => ({}))).message);
                });

                window.addEventListener('message', async (event) => {
                    const message = event.data;

                    if (message?.type === 'viasocket_connection_success' && message.serviceId === serviceId) {
                        const response = await request(@json(route('admin.settings.integrations.connections.store')), 'POST', {
                            service_id: serviceId,
                            auth_id: message.data.id,
                        });

                        response.ok ? show(true) : fail();
                    } else if (message?.type === 'viasocket_connection_error') {
                        fail(message.error?.message);
                    }
                });
            })();
        </script>
    @endPushOnce
</x-admin::layouts>
