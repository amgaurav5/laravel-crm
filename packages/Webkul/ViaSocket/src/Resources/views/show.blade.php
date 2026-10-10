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

        <v-integration-connect
            service-id="{{ $serviceId }}"
            icon="{{ $icon }}"
            :connected="{{ $connected ? 'true' : 'false' }}"
        ></v-integration-connect>
    </div>

    @pushOnce('scripts')
        <script src="{{ $connectScript }}"></script>

        <script
            type="text/x-template"
            id="v-integration-connect-template"
        >
            <div class="rounded-lg border border-gray-300 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p
                    class="mb-4 text-gray-600 dark:text-gray-300"
                    v-text="isConnected ? messages.connected : messages.notConnected"
                ></p>

                <button
                    v-if="! isConnected"
                    type="button"
                    class="primary-button"
                    :disabled="busy"
                    @click="connect"
                >
                    <img
                        v-if="icon"
                        :src="icon"
                        alt=""
                        width="20"
                        height="20"
                        style="display: inline-block; vertical-align: middle; margin-right: 6px"
                    >

                    <span v-text="messages.connectButton"></span>
                </button>

                <button
                    v-else
                    type="button"
                    class="secondary-button"
                    :disabled="busy"
                    @click="disconnect"
                    v-text="messages.disconnectButton"
                ></button>

                <p
                    v-if="error"
                    class="mt-3 text-sm text-red-600"
                    v-text="error"
                ></p>
            </div>
        </script>

        <script type="module">
            app.component('v-integration-connect', {
                template: '#v-integration-connect-template',

                props: ['serviceId', 'icon', 'connected'],

                data() {
                    return {
                        isConnected: this.connected,

                        busy: false,

                        error: '',

                        embedToken: null,

                        messages: {
                            connected: @json(trans('viasocket::app.show.connected-info', ['name' => $name])),
                            notConnected: @json(trans('viasocket::app.show.not-connected-info', ['name' => $name])),
                            connectButton: @json(trans('viasocket::app.show.connect-btn', ['name' => $name])),
                            disconnectButton: @json(trans('viasocket::app.show.disconnect-btn')),
                            error: @json(trans('viasocket::app.show.error')),
                        },
                    };
                },

                mounted() {
                    this.loadToken().catch(() => {});

                    window.addEventListener('message', this.onMessage);
                },

                beforeUnmount() {
                    window.removeEventListener('message', this.onMessage);
                },

                methods: {
                    async loadToken() {
                        if (! this.embedToken) {
                            const response = await fetch(@json(route('admin.settings.integrations.token')), {
                                credentials: 'same-origin',
                            });

                            if (! response.ok) {
                                throw new Error('token');
                            }

                            this.embedToken = await response.text();
                        }

                        return this.embedToken;
                    },

                    request(url, method, body) {
                        return fetch(url, {
                            method,
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token()),
                            },
                            body: body ? JSON.stringify(body) : undefined,
                        });
                    },

                    async connect() {
                        this.error = '';

                        if (typeof openViasocketConnection !== 'function') {
                            this.error = this.messages.error;

                            return;
                        }

                        try {
                            openViasocketConnection(await this.loadToken(), this.serviceId);
                        } catch (exception) {
                            this.error = this.messages.error;
                        }
                    },

                    async disconnect() {
                        this.busy = true;
                        this.error = '';

                        try {
                            const response = await this.request(@json(route('admin.settings.integrations.connections.destroy', ['serviceId' => $serviceId])), 'DELETE');

                            if (response.ok) {
                                this.isConnected = false;
                            } else {
                                this.error = (await response.json().catch(() => ({}))).message || this.messages.error;
                            }
                        } catch (exception) {
                            this.error = this.messages.error;
                        }

                        this.busy = false;
                    },

                    async onMessage(event) {
                        const message = event.data;

                        if (message?.type === 'viasocket_connection_error') {
                            this.error = message.error?.message || this.messages.error;

                            return;
                        }

                        if (message?.type !== 'viasocket_connection_success' || message.serviceId !== this.serviceId) {
                            return;
                        }

                        this.busy = true;

                        try {
                            const response = await this.request(@json(route('admin.settings.integrations.connections.store')), 'POST', {
                                service_id: this.serviceId,
                                auth_id: message.data.id,
                            });

                            if (response.ok) {
                                this.isConnected = true;
                                this.error = '';
                            } else {
                                this.error = this.messages.error;
                            }
                        } catch (exception) {
                            this.error = this.messages.error;
                        }

                        this.busy = false;
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
