<x-admin::layouts>
    <x-slot:title>
        {{ $name }}
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs :name="$breadcrumb" />

                <div class="text-xl font-bold dark:text-white">
                    {{ $name }}
                </div>
            </div>
        </div>

        <v-integration-connect
            service-id="{{ $serviceId }}"
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
                    v-text="messages.connectButton"
                ></button>

                <template v-else>
                    <div class="mb-6 flex flex-col gap-4">
                        <div v-if="actions.length">
                            <label class="mb-1.5 flex items-center gap-1 text-sm font-normal text-gray-800 dark:text-white">
                                <span v-text="messages.action"></span>
                            </label>

                            <select
                                v-model="actionVersionId"
                                class="custom-select w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                @change="selectAction"
                            >
                                <option
                                    v-for="action in actions"
                                    :key="action.id"
                                    :value="action.id"
                                    v-text="action.name"
                                ></option>
                            </select>
                        </div>

                        <form
                            v-if="schema"
                            class="flex flex-col gap-4"
                            @submit.prevent="run"
                        >
                            <template
                                v-for="field in fields"
                                :key="field.key"
                            >
                                <p
                                    v-if="field.block.type == 'help'"
                                    class="text-xs text-gray-600 dark:text-gray-300"
                                    v-text="field.block.label || field.block.help"
                                ></p>

                                <p
                                    v-else-if="field.group"
                                    class="mt-2 font-semibold text-gray-600 dark:text-gray-300"
                                    v-text="field.block.label"
                                ></p>

                                <div
                                    v-else
                                    :style="{ marginLeft: field.depth * 16 + 'px' }"
                                >
                                    <label class="mb-1.5 flex items-center gap-1 text-sm font-normal text-gray-800 dark:text-white">
                                        <span v-text="field.block.label"></span>

                                        <span
                                            v-if="field.block.required"
                                            class="text-red-600"
                                        >*</span>
                                    </label>

                                    <select
                                        v-if="isChoice(field.block)"
                                        :multiple="field.block.type == 'multiselect'"
                                        :value="values[field.key]"
                                        class="custom-select w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                        @focus="loadOptions(field.key)"
                                        @change="onSelect(field, $event)"
                                    >
                                        <option
                                            v-if="field.block.type != 'multiselect'"
                                            value=""
                                            v-text="loading[field.key] ? messages.loading : (field.block.placeholder || messages.choose)"
                                        ></option>

                                        <option
                                            v-for="option in choicesOf(field)"
                                            :key="String(option.value)"
                                            :value="option.value"
                                            :selected="isSelected(field.key, option.value)"
                                            v-text="option.label"
                                        ></option>
                                    </select>

                                    <select
                                        v-else-if="field.block.type == 'boolean'"
                                        :value="values[field.key]"
                                        class="custom-select w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                        @change="setValue(field, $event.target.value === '' ? '' : $event.target.value === 'true')"
                                    >
                                        <option value="" v-text="messages.choose"></option>
                                        <option :value="true" v-text="messages.yes"></option>
                                        <option :value="false" v-text="messages.no"></option>
                                    </select>

                                    <textarea
                                        v-else-if="isLongText(field.block)"
                                        rows="5"
                                        :value="values[field.key]"
                                        :placeholder="field.block.placeholder"
                                        class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                        @input="setValue(field, $event.target.value)"
                                    ></textarea>

                                    <input
                                        v-else
                                        :type="inputType(field.block)"
                                        :value="values[field.key]"
                                        :placeholder="field.block.placeholder"
                                        class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                        @input="setValue(field, $event.target.value)"
                                    >

                                    <p
                                        v-if="field.block.help"
                                        class="mt-1 text-xs text-gray-600 dark:text-gray-300"
                                        v-text="field.block.help"
                                    ></p>

                                    <p
                                        v-if="optionErrors[field.key]"
                                        class="mt-1 text-xs text-red-600"
                                        v-text="optionErrors[field.key]"
                                    ></p>
                                </div>
                            </template>

                            <div v-if="hiddenOptional > 0">
                                <button
                                    type="button"
                                    class="text-sm text-brandColor"
                                    @click="showMore = ! showMore"
                                    v-text="showMore ? messages.fewer : messages.more"
                                ></button>
                            </div>

                            <div class="flex items-center gap-4">
                                <button
                                    type="submit"
                                    class="primary-button"
                                    :disabled="busy"
                                    v-text="actionName"
                                ></button>
                            </div>
                        </form>
                    </div>

                    <pre
                        v-if="result"
                        class="mb-4 overflow-auto rounded border border-gray-300 p-3 text-xs text-gray-800 dark:border-gray-800 dark:text-gray-300"
                        v-text="result"
                    ></pre>

                    <button
                        type="button"
                        class="secondary-button"
                        :disabled="busy"
                        @click="disconnect"
                        v-text="messages.disconnectButton"
                    ></button>
                </template>

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

                props: ['serviceId', 'connected'],

                data() {
                    return {
                        isConnected: this.connected,

                        busy: false,

                        error: '',

                        embedToken: null,

                        actions: [],

                        actionVersionId: '',

                        schema: null,

                        values: {},

                        options: {},

                        optionErrors: {},

                        loading: {},

                        showMore: false,

                        result: '',

                        messages: {
                            connected: @json(trans('viasocket::app.show.connected-info', ['name' => $name])),
                            notConnected: @json(trans('viasocket::app.show.not-connected-info', ['name' => $name])),
                            connectButton: @json(trans('viasocket::app.show.connect-btn', ['name' => $name])),
                            disconnectButton: @json(trans('viasocket::app.show.disconnect-btn')),
                            action: @json(trans('viasocket::app.show.action')),
                            choose: @json(trans('viasocket::app.show.choose')),
                            loading: @json(trans('viasocket::app.show.loading')),
                            yes: @json(trans('viasocket::app.show.yes')),
                            no: @json(trans('viasocket::app.show.no')),
                            more: @json(trans('viasocket::app.show.more')),
                            fewer: @json(trans('viasocket::app.show.fewer')),
                            required: @json(trans('viasocket::app.show.required')),
                            error: @json(trans('viasocket::app.show.error')),
                        },

                        urls: {
                            token: @json(route('admin.settings.integrations.token')),
                            store: @json(route('admin.settings.integrations.connections.store')),
                            destroy: @json(route('admin.settings.integrations.connections.destroy', ['serviceId' => $serviceId])),
                            actions: @json(route('admin.settings.integrations.api.actions', ['serviceId' => $serviceId])),
                            schema: @json(route('admin.settings.integrations.api.schema', ['serviceId' => $serviceId, 'actionVersionId' => '__ID__'])),
                            options: @json(route('admin.settings.integrations.api.options', ['serviceId' => $serviceId])),
                            run: @json(route('admin.settings.integrations.api.run', ['serviceId' => $serviceId])),
                        },
                    };
                },

                computed: {
                    actionName() {
                        return this.actions.find((action) => action.id === this.actionVersionId)?.name || '';
                    },

                    ordered() {
                        const out = [];

                        const walk = (keys, depth) => (keys || []).forEach((key) => {
                            const block = this.schema.blocks[key];

                            if (! block) {
                                return;
                            }

                            const group = block.type === 'input groups' && ! block.source;

                            out.push({ key, block, depth, group });

                            if (group) {
                                walk(this.schema.steps[key], depth + 1);
                            }
                        });

                        walk(this.schema?.steps?.root, 0);

                        return out;
                    },

                    state() {
                        const visible = {};
                        const input = {};

                        this.ordered.forEach((field) => {
                            const block = field.block;

                            let show = ! block.isHidden && ! (block.type === 'help' && ! block.label && ! block.help);

                            if (show && (block.dependsOn || []).some((key) => ! this.hasValue(key))) {
                                show = false;
                            }

                            if (show && block.visibilityCondition) {
                                try {
                                    show = !! new Function('context', 'return !!(' + block.visibilityCondition + ')')({ inputData: input });
                                } catch (exception) {
                                    show = false;
                                }
                            }

                            visible[field.key] = show;

                            if (show && ! field.group && block.type !== 'help' && this.hasValue(field.key)) {
                                const path = field.key.split('.');

                                path.slice(0, -1).reduce((node, part) => node[part] ??= {}, input)[path.at(-1)] = this.values[field.key];
                            }
                        });

                        return { visible, input };
                    },

                    fields() {
                        return this.ordered.filter((field) => this.isShown(field));
                    },

                    hiddenOptional() {
                        return this.ordered.filter((field) => this.isVisible(field) && ! this.isEarned(field)).length;
                    },
                },

                mounted() {
                    this.loadToken().catch(() => {});

                    window.addEventListener('message', this.onMessage);

                    if (this.isConnected) {
                        this.loadActions();
                    }
                },

                beforeUnmount() {
                    window.removeEventListener('message', this.onMessage);
                },

                methods: {
                    async loadToken() {
                        if (! this.embedToken) {
                            const response = await fetch(this.urls.token, { credentials: 'same-origin' });

                            if (! response.ok) {
                                throw new Error('token');
                            }

                            this.embedToken = await response.text();
                        }

                        return this.embedToken;
                    },

                    async request(url, method, body) {
                        const response = await fetch(url, {
                            method,
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token()),
                            },
                            body: body ? JSON.stringify(body) : undefined,
                        });

                        return { ok: response.ok, data: await response.json().catch(() => ({})) };
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

                        const response = await this.request(this.urls.destroy, 'DELETE').catch(() => ({ ok: false, data: {} }));

                        if (response.ok) {
                            this.isConnected = false;
                            this.schema = null;
                            this.actions = [];
                            this.result = '';
                        } else {
                            this.error = response.data.message || this.messages.error;
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

                        const response = await this.request(this.urls.store, 'POST', {
                            service_id: this.serviceId,
                            auth_id: message.data.id,
                        }).catch(() => ({ ok: false, data: {} }));

                        if (response.ok) {
                            this.isConnected = true;
                            this.error = '';
                            this.loadActions();
                        } else {
                            this.error = this.messages.error;
                        }

                        this.busy = false;
                    },

                    async loadActions() {
                        const response = await this.request(this.urls.actions, 'GET').catch(() => ({ ok: false, data: {} }));

                        if (! response.ok || ! response.data.actions?.length) {
                            return;
                        }

                        this.actions = response.data.actions;
                        this.actionVersionId = this.actions[0].id;

                        this.selectAction();
                    },

                    async selectAction() {
                        this.schema = null;
                        this.values = {};
                        this.options = {};
                        this.optionErrors = {};
                        this.result = '';
                        this.error = '';

                        const response = await this.request(this.urls.schema.replace('__ID__', encodeURIComponent(this.actionVersionId)), 'GET').catch(() => ({ ok: false, data: {} }));

                        if (! response.ok) {
                            this.error = this.messages.error;

                            return;
                        }

                        Object.entries(response.data.blocks || {}).forEach(([key, block]) => {
                            if (block.defaultValue !== undefined && block.defaultValue !== null) {
                                this.values[key] = block.defaultValue?.value ?? block.defaultValue;
                            }
                        });

                        this.schema = response.data;
                    },

                    hasValue(key) {
                        const value = this.values[key];

                        return value !== undefined && value !== null && value !== '' && ! (Array.isArray(value) && ! value.length);
                    },

                    nested() {
                        return this.state.input;
                    },

                    isVisible(field) {
                        return !! this.state.visible[field.key];
                    },

                    isEarned(field) {
                        const block = field.block;

                        return block.required || (block.dependsOn || []).length || block.visibilityCondition || this.hasValue(field.key) || field.group;
                    },

                    isShown(field) {
                        return this.isVisible(field) && (this.isEarned(field) || this.showMore);
                    },

                    isChoice(block) {
                        return block.type === 'dropdown' || block.type === 'multiselect';
                    },

                    isLongText(block) {
                        return ['aifield', 'html', 'markdown', 'dictionary'].includes(block.type) || block.list;
                    },

                    inputType(block) {
                        return block.type === 'number' ? 'number' : (block.type === 'date' ? 'date' : 'text');
                    },

                    choicesOf(field) {
                        return field.block.options || this.options[field.key] || [];
                    },

                    isSelected(key, value) {
                        const current = this.values[key];

                        return Array.isArray(current) ? current.includes(value) : current === value;
                    },

                    setValue(field, value) {
                        this.values[field.key] = field.block.type === 'number' && value !== '' ? Number(value) : value;

                        this.clearDependents(field.key);
                    },

                    onSelect(field, event) {
                        const select = event.target;
                        const chosen = [...select.selectedOptions].map((option) => option._value ?? option.value);

                        const value = field.block.type === 'multiselect'
                            ? chosen.filter((item) => item !== '')
                            : (chosen[0] ?? '');

                        this.setValue(field, value);
                    },

                    clearDependents(key) {
                        Object.entries(this.schema.blocks).forEach(([other, block]) => {
                            if ((block.dependsOn || []).includes(key)) {
                                delete this.values[other];
                                delete this.options[other];
                                delete this.optionErrors[other];

                                this.clearDependents(other);
                            }
                        });
                    },

                    async loadOptions(key) {
                        const block = this.schema.blocks[key];

                        if (block.options || this.options[key] || this.loading[key] || (! block.source && ! block.enableSearchApi)) {
                            return;
                        }

                        this.loading[key] = true;
                        this.optionErrors[key] = '';

                        const response = await this.request(this.urls.options, 'POST', {
                            action_version_id: this.actionVersionId,
                            field_key: key,
                            existing_fields: this.nested(),
                        }).catch(() => ({ ok: false, data: {} }));

                        if (response.ok) {
                            this.options[key] = response.data.options;
                        } else {
                            this.optionErrors[key] = response.data.message || this.messages.error;
                        }

                        this.loading[key] = false;
                    },

                    async run() {
                        this.error = '';
                        this.result = '';

                        const missing = this.ordered.find((field) => ! field.group && field.block.required && this.isVisible(field) && ! this.hasValue(field.key));

                        if (missing) {
                            this.error = this.messages.required.replace(':field', missing.block.label);

                            return;
                        }

                        this.busy = true;

                        const response = await this.request(this.urls.run, 'POST', {
                            action_version_id: this.actionVersionId,
                            input_data: this.nested(),
                        }).catch(() => ({ ok: false, data: {} }));

                        if (response.ok && response.data.ok) {
                            this.result = JSON.stringify(response.data.result, null, 2);
                        } else {
                            this.error = response.data.message || response.data.result?.message || this.messages.error;
                        }

                        this.busy = false;
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
