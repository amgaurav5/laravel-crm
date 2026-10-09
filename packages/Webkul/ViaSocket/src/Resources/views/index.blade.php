<x-admin::layouts>
    <x-slot:title>
        @lang('viasocket::app.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="text-xl font-bold dark:text-white">
                    @lang('viasocket::app.index.title')
                </div>

                <p class="text-gray-600 dark:text-gray-300">
                    @lang('viasocket::app.index.description')
                </p>
            </div>
        </div>

        <div class="rounded-lg border border-gray-300 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div id="viasocket-integrations" style="height: 640px"></div>
        </div>
    </div>

    @pushOnce('scripts')
        <script src="{{ $embedScript }}"></script>

        <script>
            (async () => {
                const embedOrigin = @json($embedOrigin);
                const csrfToken = @json(csrf_token());

                const embedToken = await fetch(@json(route('admin.settings.viasocket.token')), {
                    credentials: 'same-origin',
                }).then((response) => response.text());

                viaSocket.mount({
                    embedToken,
                    parent: '#viasocket-integrations',
                    config: {},
                });

                window.addEventListener('message', (event) => {
                    if (event.origin !== embedOrigin) {
                        return;
                    }

                    const flow = event.data;

                    if (! flow?.id) {
                        return;
                    }

                    fetch(@json(route('admin.settings.viasocket.flows.store')), {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            id: flow.id,
                            action: flow.action,
                            title: flow.title,
                            webhookurl: flow.webhookurl,
                            payload: flow.payload,
                        }),
                    });
                });
            })();
        </script>
    @endPushOnce
</x-admin::layouts>
