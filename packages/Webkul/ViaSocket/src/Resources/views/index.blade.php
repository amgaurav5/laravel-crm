<x-admin::layouts>
    <x-slot:title>
        @lang('viasocket::app.menu.title')
    </x-slot>

    <v-integrations></v-integrations>

    @pushOnce('scripts')
        <script src="{{ $connectScript }}"></script>

        <script
            type="text/x-template"
            id="v-integrations-template"
        >
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                    <div class="flex flex-col gap-1">
                        <div
                            class="text-xl font-bold dark:text-white"
                            v-text="messages.ui.title"
                        ></div>

                        <p
                            class="text-gray-600 dark:text-gray-300"
                            v-text="messages.ui.subtitle"
                        ></p>
                    </div>

                    <button
                        v-if="view === 'list' && flows.length"
                        type="button"
                        class="primary-button"
                        @click="newFlow"
                        v-text="messages.ui.new_flow"
                    ></button>
                </div>

                <div
                    v-if="error"
                    class="rounded-lg border border-red-300 bg-white p-3 text-sm text-red-600 dark:border-red-800 dark:bg-gray-900"
                    v-text="error"
                ></div>

                <!-- Flow list -->
                <template v-if="view === 'list'">
                    <div
                        v-if="! loaded"
                        class="rounded-lg border border-gray-300 bg-white p-4 text-gray-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                        v-text="messages.ui.loading"
                    ></div>

                    <div
                        v-else-if="! flows.length"
                        class="flex flex-col items-center gap-3 rounded-lg border border-gray-300 bg-white p-10 text-center dark:border-gray-800 dark:bg-gray-900"
                    >
                        <p
                            class="text-lg font-semibold text-gray-800 dark:text-white"
                            v-text="messages.ui.empty_title"
                        ></p>

                        <p
                            class="text-gray-600 dark:text-gray-300"
                            v-text="messages.ui.empty_text"
                        ></p>

                        <button
                            type="button"
                            class="primary-button"
                            @click="newFlow"
                            v-text="messages.ui.start"
                        ></button>
                    </div>

                    <div
                        v-else
                        class="flex flex-col gap-3"
                    >
                        <div
                            v-for="flow in flows"
                            :key="flow.id"
                            class="flex items-center justify-between gap-4 rounded-lg border border-gray-300 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"
                        >
                            <div class="flex flex-col gap-1">
                                <p
                                    class="font-semibold text-gray-800 dark:text-white"
                                    v-text="flow.title"
                                ></p>

                                <p
                                    class="text-xs text-gray-600 dark:text-gray-300"
                                    v-text="summary(flow)"
                                ></p>

                                <span
                                    class="text-xs"
                                    :class="flow.status === 'active' ? 'text-green-600' : 'text-gray-500'"
                                    v-text="flow.status === 'active' ? messages.ui.active : messages.ui.paused"
                                ></span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="secondary-button"
                                    disabled
                                    :title="messages.ui.history_soon"
                                    v-text="messages.ui.history"
                                ></button>

                                <button
                                    type="button"
                                    class="secondary-button"
                                    @click="editFlow(flow)"
                                    v-text="messages.ui.edit"
                                ></button>

                                <button
                                    type="button"
                                    class="secondary-button"
                                    :disabled="busy"
                                    @click="toggleFlow(flow)"
                                    v-text="flow.status === 'active' ? messages.ui.pause : messages.ui.resume"
                                ></button>

                                <button
                                    type="button"
                                    class="secondary-button"
                                    :disabled="busy"
                                    @click="removeFlow(flow)"
                                    v-text="messages.ui.delete"
                                ></button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Builder -->
                <div
                    v-else-if="! picker"
                    class="flex flex-col gap-6 rounded-lg border border-gray-300 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div>
                        <label
                            class="mb-1.5 flex items-center gap-1 text-sm font-normal text-gray-800 dark:text-white"
                            v-text="messages.ui.flow_title"
                        ></label>

                        <input
                            v-model="draft.title"
                            type="text"
                            :placeholder="messages.ui.flow_title_placeholder"
                            class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                        >
                    </div>

                    <div>
                        <p
                            class="mb-2 font-semibold text-gray-600 dark:text-gray-300"
                            v-text="messages.ui.trigger"
                        ></p>

                        <div
                            v-if="draft.trigger"
                            class="flex items-center justify-between rounded border border-gray-300 p-3 dark:border-gray-800"
                        >
                            <span
                                class="text-sm text-gray-800 dark:text-white"
                                v-text="draft.trigger.label"
                            ></span>

                            <button
                                type="button"
                                class="secondary-button"
                                @click="openPicker('trigger')"
                                v-text="messages.ui.change"
                            ></button>
                        </div>

                        <button
                            v-else
                            type="button"
                            class="primary-button"
                            @click="openPicker('trigger')"
                            v-text="messages.ui.choose_trigger"
                        ></button>
                    </div>

                    <div v-if="draft.trigger">
                        <p
                            class="mb-2 font-semibold text-gray-600 dark:text-gray-300"
                            v-text="messages.ui.actions"
                        ></p>

                        <div class="mb-3 flex flex-col gap-2">
                            <div
                                v-for="(step, index) in draft.steps"
                                :key="index"
                                class="flex items-center justify-between rounded border border-gray-300 p-3 dark:border-gray-800"
                            >
                                <span
                                    class="text-sm text-gray-800 dark:text-white"
                                    v-text="(index + 1) + '. ' + step.app_name + ' - ' + step.name"
                                ></span>

                                <span class="flex gap-2">
                                    <button
                                        type="button"
                                        class="secondary-button"
                                        @click="openPicker('action', index)"
                                        v-text="messages.ui.edit"
                                    ></button>

                                    <button
                                        type="button"
                                        class="secondary-button"
                                        @click="draft.steps.splice(index, 1)"
                                        v-text="messages.ui.delete"
                                    ></button>
                                </span>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="secondary-button"
                            @click="openPicker('action')"
                            v-text="messages.ui.add_action"
                        ></button>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="primary-button"
                            :disabled="busy"
                            @click="saveFlow"
                            v-text="draft.id ? messages.ui.update : messages.ui.publish"
                        ></button>

                        <button
                            type="button"
                            class="secondary-button"
                            @click="view = 'list'; error = ''"
                            v-text="messages.ui.cancel"
                        ></button>
                    </div>
                </div>

                <!-- Picker -->
                <div
                    v-else
                    class="flex flex-col gap-4 rounded-lg border border-gray-300 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="flex items-center justify-between">
                        <p
                            class="font-semibold text-gray-800 dark:text-white"
                            v-text="pickerTitle"
                        ></p>

                        <span class="flex gap-2">
                            <button
                                type="button"
                                class="secondary-button"
                                @click="back"
                                v-text="messages.ui.back"
                            ></button>

                            <button
                                type="button"
                                class="secondary-button"
                                @click="picker = null"
                                v-text="messages.ui.cancel"
                            ></button>
                        </span>
                    </div>

                    <!-- Apps -->
                    <template v-if="picker.stage === 'apps'">
                        <input
                            v-model="picker.query"
                            type="text"
                            :placeholder="messages.ui.search_apps"
                            class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                            @input="searchApps"
                        >

                        <p
                            v-if="picker.loadingApps"
                            class="text-sm text-gray-600 dark:text-gray-300"
                            v-text="messages.ui.loading"
                        ></p>

                        <div class="grid grid-cols-3 gap-3 max-md:grid-cols-2">
                            <button
                                v-for="app in picker.apps"
                                :key="app.id"
                                type="button"
                                class="flex items-center gap-2 rounded-lg border border-gray-300 p-3 text-left transition-all hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                @click="chooseApp(app)"
                            >
                                <img
                                    v-if="app.icon"
                                    :src="app.icon"
                                    alt=""
                                    width="28"
                                    height="28"
                                    class="rounded"
                                >

                                <span class="grid">
                                    <span
                                        class="text-sm font-semibold text-gray-800 dark:text-white"
                                        v-text="app.name"
                                    ></span>

                                    <span
                                        v-if="app.special"
                                        class="text-xs text-gray-600 dark:text-gray-300"
                                        v-text="app.description"
                                    ></span>
                                </span>
                            </button>
                        </div>

                        <p
                            v-if="! picker.loadingApps && ! picker.apps.length"
                            class="text-sm text-gray-600 dark:text-gray-300"
                            v-text="messages.ui.no_apps"
                        ></p>
                    </template>

                    <!-- Krayin events -->
                    <template v-else-if="picker.stage === 'events'">
                        <button
                            v-for="(event, key) in krayinEvents"
                            :key="key"
                            type="button"
                            class="rounded-lg border border-gray-300 p-3 text-left text-sm text-gray-800 transition-all hover:bg-gray-100 dark:border-gray-800 dark:text-white dark:hover:bg-gray-950"
                            @click="chooseEvent(key)"
                            v-text="event.name"
                        ></button>
                    </template>

                    <!-- Schedule -->
                    <template v-else-if="picker.stage === 'schedule'">
                        <div>
                            <label
                                class="mb-1.5 flex items-center gap-1 text-sm font-normal text-gray-800 dark:text-white"
                                v-text="messages.ui.cron_label"
                            ></label>

                            <input
                                v-model="picker.cron"
                                type="text"
                                :placeholder="messages.ui.cron_placeholder"
                                class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                            >
                        </div>

                        <div>
                            <button
                                type="button"
                                class="primary-button"
                                @click="confirmSchedule"
                                v-text="messages.ui.use"
                            ></button>
                        </div>
                    </template>

                    <!-- Items of an app -->
                    <template v-else-if="picker.stage === 'items'">
                        <p
                            v-if="picker.app"
                            class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300"
                        >
                            <img
                                v-if="picker.app.icon"
                                :src="picker.app.icon"
                                alt=""
                                width="20"
                                height="20"
                            >

                            <span v-text="picker.app.name"></span>
                        </p>

                        <button
                            v-for="item in pickerItems"
                            :key="item.version"
                            type="button"
                            class="rounded-lg border border-gray-300 p-3 text-left transition-all hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                            @click="chooseItem(item)"
                        >
                            <span
                                class="block text-sm font-semibold text-gray-800 dark:text-white"
                                v-text="item.name"
                            ></span>

                            <span
                                v-if="item.description"
                                class="block text-xs text-gray-600 dark:text-gray-300"
                                v-text="item.description"
                            ></span>
                        </button>

                        <p
                            v-if="! pickerItems.length"
                            class="text-sm text-gray-600 dark:text-gray-300"
                            v-text="messages.ui.no_apps"
                        ></p>
                    </template>

                    <!-- Connect -->
                    <template v-else-if="picker.stage === 'connect'">
                        <p
                            class="text-gray-600 dark:text-gray-300"
                            v-text="messages.ui.connect_to_continue.replace(':name', picker.app.name)"
                        ></p>

                        <div>
                            <button
                                type="button"
                                class="primary-button"
                                @click="connectApp"
                                v-text="messages.ui.connect.replace(':name', picker.app.name)"
                            ></button>
                        </div>
                    </template>

                    <!-- Fields -->
                    <form
                        v-else-if="picker.stage === 'form'"
                        class="flex flex-col gap-4"
                        @submit.prevent="confirmForm"
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
                                <label class="mb-1.5 flex items-center justify-between gap-1 text-sm font-normal text-gray-800 dark:text-white">
                                    <span>
                                        <span v-text="field.block.label"></span>

                                        <span
                                            v-if="field.block.required"
                                            class="text-red-600"
                                        >*</span>
                                    </span>

                                    <select
                                        v-if="picker.kind === 'action' && variablePaths.length && isText(field.block)"
                                        class="rounded border border-gray-300 px-1 py-0.5 text-xs text-gray-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                        @change="insertVariable(field, $event)"
                                    >
                                        <option
                                            value=""
                                            v-text="messages.ui.insert_value"
                                        ></option>

                                        <option
                                            v-for="path in variablePaths"
                                            :key="path"
                                            :value="path"
                                            v-text="path"
                                        ></option>
                                    </select>
                                </label>

                                <select
                                    v-if="isChoice(field.block)"
                                    :multiple="field.block.type == 'multiselect'"
                                    :value="picker.values[field.key]"
                                    class="custom-select w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                    @focus="loadOptions(field.key)"
                                    @change="onSelect(field, $event)"
                                >
                                    <option
                                        v-if="field.block.type != 'multiselect'"
                                        value=""
                                        v-text="picker.loading[field.key] ? messages.ui.loading : (field.block.placeholder || messages.ui.select)"
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
                                    :value="picker.values[field.key]"
                                    class="custom-select w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                    @change="setValue(field, $event.target.value === '' ? '' : $event.target.value === 'true')"
                                >
                                    <option value="" v-text="messages.ui.select"></option>
                                    <option :value="true" v-text="messages.ui.yes"></option>
                                    <option :value="false" v-text="messages.ui.no"></option>
                                </select>

                                <textarea
                                    v-else-if="isLongText(field.block)"
                                    rows="5"
                                    :value="picker.values[field.key]"
                                    :placeholder="field.block.placeholder"
                                    class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                    @input="setValue(field, $event.target.value)"
                                ></textarea>

                                <input
                                    v-else
                                    :type="inputType(field.block)"
                                    :value="picker.values[field.key]"
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
                                    v-if="picker.optionErrors[field.key]"
                                    class="mt-1 text-xs text-red-600"
                                    v-text="picker.optionErrors[field.key]"
                                ></p>
                            </div>
                        </template>

                        <div v-if="hiddenOptional > 0">
                            <button
                                type="button"
                                class="text-sm text-brandColor"
                                @click="picker.showMore = ! picker.showMore"
                                v-text="picker.showMore ? messages.ui.fewer : messages.ui.more"
                            ></button>
                        </div>

                        <div>
                            <button
                                type="submit"
                                class="primary-button"
                                v-text="messages.ui.use"
                            ></button>
                        </div>
                    </form>
                </div>
            </div>
        </script>

        <script type="module">
            const OPEN = '{' + '{';
            const CLOSE = '}' + '}';
            const KRAYIN = '__krayin__';
            const SCHEDULE = '__schedule__';

            const leafPaths = (value, prefix = '', out = []) => {
                if (Array.isArray(value)) {
                    if (value.length) {
                        leafPaths(value[0], prefix ? prefix + '.0' : '0', out);
                    }
                } else if (value && typeof value === 'object') {
                    Object.entries(value).forEach(([key, item]) => leafPaths(item, prefix ? prefix + '.' + key : key, out));
                } else if (prefix) {
                    out.push(prefix);
                }

                return out;
            };

            app.component('v-integrations', {
                template: '#v-integrations-template',

                data() {
                    return {
                        view: 'list',

                        loaded: false,

                        busy: false,

                        error: '',

                        flows: [],

                        draft: { id: null, title: '', trigger: null, steps: [] },

                        picker: null,

                        embedToken: null,

                        searchTimer: null,

                        messages: {!! json_encode(trans('viasocket::app'), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},

                        krayinEvents: {!! json_encode($krayinEvents, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},

                        urls: {
                            token: {!! json_encode(route('admin.integrations.token'), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                            apps: {!! json_encode(route('admin.integrations.apps'), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                            app: {!! json_encode(route('admin.integrations.app', ['serviceId' => '__SERVICE__']), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                            schema: {!! json_encode(route('admin.integrations.schema', ['serviceId' => '__SERVICE__', 'kind' => '__KIND__', 'versionId' => '__VERSION__']), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                            options: {!! json_encode(route('admin.integrations.options', ['serviceId' => '__SERVICE__']), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                            connect: {!! json_encode(route('admin.integrations.connections.store'), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                            flows: {!! json_encode(route('admin.integrations.flows.index'), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                            flow: {!! json_encode(route('admin.integrations.flows.update', ['id' => '__ID__']), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                            status: {!! json_encode(route('admin.integrations.flows.status', ['id' => '__ID__']), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                            destroy: {!! json_encode(route('admin.integrations.flows.destroy', ['id' => '__ID__']), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                        },
                    };
                },

                computed: {
                    pickerTitle() {
                        const ui = this.messages.ui;
                        const trigger = this.picker.kind === 'trigger';

                        if (this.picker.stage === 'apps') {
                            return trigger ? ui.choose_trigger_app : ui.choose_action_app;
                        }

                        if (this.picker.stage === 'events') {
                            return ui.choose_event;
                        }

                        if (this.picker.stage === 'schedule') {
                            return ui.schedule;
                        }

                        return trigger ? ui.choose_trigger_item : ui.choose_action_item;
                    },

                    pickerItems() {
                        if (! this.picker.app) {
                            return [];
                        }

                        return this.picker.kind === 'trigger' ? this.picker.app.triggers : this.picker.app.actions;
                    },

                    variablePaths() {
                        const trigger = this.draft.trigger;

                        if (! trigger) {
                            return [];
                        }

                        if (trigger.kind === 'krayin') {
                            return leafPaths(this.krayinEvents[trigger.event]?.sample, 'data');
                        }

                        return trigger.kind === 'app' ? leafPaths(trigger.sample) : [];
                    },

                    ordered() {
                        const schema = this.picker?.schema;
                        const out = [];

                        if (! schema) {
                            return out;
                        }

                        const walk = (keys, depth) => (keys || []).forEach((key) => {
                            const block = schema.blocks[key];

                            if (! block) {
                                return;
                            }

                            const group = block.type === 'input groups' && ! block.source;

                            out.push({ key, block, depth, group });

                            if (group) {
                                walk(schema.steps[key], depth + 1);
                            }
                        });

                        walk(schema.steps?.root, 0);

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

                                path.slice(0, -1).reduce((node, part) => node[part] ??= {}, input)[path.at(-1)] = this.picker.values[field.key];
                            }
                        });

                        return { visible, input };
                    },

                    fields() {
                        return this.ordered.filter((field) => this.state.visible[field.key] && (this.isEarned(field) || this.picker.showMore));
                    },

                    hiddenOptional() {
                        return this.ordered.filter((field) => this.state.visible[field.key] && ! this.isEarned(field)).length;
                    },
                },

                mounted() {
                    this.loadFlows();

                    window.addEventListener('message', this.onMessage);
                },

                beforeUnmount() {
                    window.removeEventListener('message', this.onMessage);
                },

                methods: {
                    url(template, values) {
                        return Object.entries(values).reduce((url, [key, value]) => url.replace('__' + key + '__', encodeURIComponent(value)), template);
                    },

                    async request(url, method = 'GET', body = null) {
                        const response = await fetch(url, {
                            method,
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': {!! json_encode(csrf_token(), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                            },
                            body: body ? JSON.stringify(body) : undefined,
                        }).catch(() => null);

                        if (! response) {
                            return { ok: false, data: { message: this.messages.ui.error } };
                        }

                        return { ok: response.ok, data: await response.json().catch(() => ({})) };
                    },

                    fail(response) {
                        const errors = response.data.errors ? Object.values(response.data.errors).flat() : [];

                        this.error = errors[0] || response.data.message || this.messages.ui.error;
                    },

                    summary(flow) {
                        const steps = flow.steps.map((step) => step.app_name + ' - ' + step.name).join(', ');

                        return flow.trigger.label + '  >  ' + steps;
                    },

                    async loadFlows() {
                        const response = await this.request(this.urls.flows);

                        this.flows = response.ok ? response.data.flows : [];
                        this.loaded = true;
                    },

                    newFlow() {
                        this.error = '';
                        this.draft = { id: null, title: '', trigger: null, steps: [] };
                        this.view = 'builder';
                    },

                    editFlow(flow) {
                        this.error = '';
                        this.draft = JSON.parse(JSON.stringify({ id: flow.id, title: flow.title, trigger: flow.trigger, steps: flow.steps }));
                        this.view = 'builder';
                    },

                    async saveFlow() {
                        this.error = '';

                        if (! this.draft.title.trim()) {
                            this.error = this.messages.ui.need_title;

                            return;
                        }

                        if (! this.draft.trigger) {
                            this.error = this.messages.ui.need_trigger;

                            return;
                        }

                        if (! this.draft.steps.length) {
                            this.error = this.messages.ui.need_action;

                            return;
                        }

                        this.busy = true;

                        const body = { title: this.draft.title, trigger: this.draft.trigger, steps: this.draft.steps };

                        const response = this.draft.id
                            ? await this.request(this.url(this.urls.flow, { ID: this.draft.id }), 'PUT', body)
                            : await this.request(this.urls.flows, 'POST', body);

                        this.busy = false;

                        if (! response.ok) {
                            this.fail(response);

                            return;
                        }

                        await this.loadFlows();

                        this.view = 'list';
                    },

                    async toggleFlow(flow) {
                        this.busy = true;
                        this.error = '';

                        const response = await this.request(this.url(this.urls.status, { ID: flow.id }), 'POST', { active: flow.status !== 'active' });

                        this.busy = false;

                        response.ok ? await this.loadFlows() : this.fail(response);
                    },

                    async removeFlow(flow) {
                        if (! window.confirm(this.messages.ui.delete_confirm)) {
                            return;
                        }

                        this.busy = true;
                        this.error = '';

                        const response = await this.request(this.url(this.urls.destroy, { ID: flow.id }), 'DELETE');

                        this.busy = false;

                        response.ok ? await this.loadFlows() : this.fail(response);
                    },

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

                    openPicker(kind, index = null) {
                        this.error = '';

                        this.picker = {
                            kind,
                            index,
                            stage: 'apps',
                            query: '',
                            apps: [],
                            loadingApps: false,
                            app: null,
                            item: null,
                            cron: '',
                            schema: null,
                            values: {},
                            options: {},
                            optionErrors: {},
                            loading: {},
                            showMore: false,
                        };

                        this.loadApps('');

                        const entry = kind === 'trigger' ? this.draft.trigger : (index === null ? null : this.draft.steps[index]);

                        if (entry) {
                            this.openEntry(kind, entry);
                        }
                    },

                    async openEntry(kind, entry) {
                        if (kind === 'trigger' && entry.kind === 'krayin') {
                            this.picker.stage = 'events';

                            return;
                        }

                        if (kind === 'trigger' && entry.kind === 'schedule') {
                            this.picker.cron = entry.cron;
                            this.picker.stage = 'schedule';

                            return;
                        }

                        await this.chooseApp({ id: entry.service_id });

                        const item = this.pickerItems.find((candidate) => candidate.id === (kind === 'trigger' ? entry.trigger_id : entry.action_id));

                        if (item) {
                            await this.chooseItem(item, entry.fields || {});
                        }
                    },

                    back() {
                        const stages = { events: 'apps', schedule: 'apps', items: 'apps', connect: 'items', form: 'items' };

                        if (this.picker.stage === 'apps') {
                            this.picker = null;

                            return;
                        }

                        this.picker.stage = stages[this.picker.stage];
                    },

                    searchApps() {
                        clearTimeout(this.searchTimer);

                        this.searchTimer = setTimeout(() => this.loadApps(this.picker.query), 300);
                    },

                    async loadApps(query) {
                        this.picker.loadingApps = true;

                        const response = await this.request(this.urls.apps + '?q=' + encodeURIComponent(query));

                        if (! this.picker) {
                            return;
                        }

                        const special = this.picker.kind === 'trigger' && ! query ? [
                            { id: KRAYIN, name: this.messages.ui.krayin, description: this.messages.ui.krayin_info, special: true },
                            { id: SCHEDULE, name: this.messages.ui.schedule, description: this.messages.ui.schedule_info, special: true },
                        ] : [];

                        this.picker.apps = special.concat(response.ok ? response.data.apps : []);
                        this.picker.loadingApps = false;
                    },

                    async chooseApp(app) {
                        if (app.id === KRAYIN) {
                            this.picker.stage = 'events';

                            return;
                        }

                        if (app.id === SCHEDULE) {
                            this.picker.stage = 'schedule';

                            return;
                        }

                        const response = await this.request(this.url(this.urls.app, { SERVICE: app.id }));

                        if (! response.ok) {
                            this.fail(response);

                            return;
                        }

                        this.picker.app = response.data;
                        this.picker.stage = 'items';
                    },

                    chooseEvent(key) {
                        this.draft.trigger = { kind: 'krayin', event: key, label: this.messages.ui.krayin + ' - ' + this.krayinEvents[key].name };
                        this.picker = null;
                    },

                    confirmSchedule() {
                        if (! this.picker.cron.trim()) {
                            this.error = this.messages.ui.required.replace(':field', this.messages.ui.cron_label);

                            return;
                        }

                        this.draft.trigger = { kind: 'schedule', cron: this.picker.cron.trim(), label: this.messages.ui.schedule + ' - ' + this.picker.cron.trim() };
                        this.picker = null;
                    },

                    async chooseItem(item, existing = {}) {
                        this.picker.item = item;
                        this.picker.values = { ...existing };

                        if (! this.picker.app.connected) {
                            this.picker.stage = 'connect';

                            return;
                        }

                        await this.loadSchema();
                    },

                    async connectApp() {
                        if (typeof openViasocketConnection !== 'function') {
                            this.error = this.messages.ui.error;

                            return;
                        }

                        try {
                            openViasocketConnection(await this.loadToken(), this.picker.app.id);
                        } catch (exception) {
                            this.error = this.messages.ui.error;
                        }
                    },

                    async onMessage(event) {
                        const message = event.data;

                        if (message?.type === 'viasocket_connection_error') {
                            this.error = message.error?.message || this.messages.ui.error;

                            return;
                        }

                        if (message?.type !== 'viasocket_connection_success' || ! this.picker?.app || message.serviceId !== this.picker.app.id) {
                            return;
                        }

                        const response = await this.request(this.urls.connect, 'POST', {
                            service_id: this.picker.app.id,
                            auth_id: message.data.id,
                        });

                        if (! response.ok) {
                            this.fail(response);

                            return;
                        }

                        this.error = '';
                        this.picker.app.connected = true;

                        await this.loadSchema();
                    },

                    async loadSchema() {
                        const kind = this.picker.kind === 'trigger' ? 'triggers' : 'actions';

                        const response = await this.request(this.url(this.urls.schema, { SERVICE: this.picker.app.id, KIND: kind, VERSION: this.picker.item.version }));

                        if (! response.ok) {
                            this.fail(response);

                            return;
                        }

                        Object.entries(response.data.blocks || {}).forEach(([key, block]) => {
                            if (this.picker.values[key] === undefined && block.defaultValue !== undefined && block.defaultValue !== null) {
                                this.picker.values[key] = block.defaultValue?.value ?? block.defaultValue;
                            }
                        });

                        this.picker.schema = response.data;
                        this.picker.stage = 'form';
                    },

                    confirmForm() {
                        this.error = '';

                        const missing = this.ordered.find((field) => ! field.group && field.block.required && this.state.visible[field.key] && ! this.hasValue(field.key));

                        if (missing) {
                            this.error = this.messages.ui.required.replace(':field', missing.block.label);

                            return;
                        }

                        const app = this.picker.app;
                        const item = this.picker.item;
                        const values = this.state.input;
                        const fields = { ...this.picker.values };

                        if (this.picker.kind === 'trigger') {
                            this.draft.trigger = {
                                kind: 'app',
                                service_id: app.id,
                                trigger_id: item.id,
                                name: item.name,
                                label: app.name + ' - ' + item.name,
                                icon: app.icon,
                                sample: item.sample || null,
                                values,
                                fields,
                            };
                        } else {
                            const step = { service_id: app.id, action_id: item.id, name: item.name, app_name: app.name, icon: app.icon, values, fields };

                            if (this.picker.index === null) {
                                this.draft.steps.push(step);
                            } else {
                                this.draft.steps.splice(this.picker.index, 1, step);
                            }
                        }

                        this.picker = null;
                    },

                    hasValue(key) {
                        const value = this.picker?.values[key];

                        return value !== undefined && value !== null && value !== '' && ! (Array.isArray(value) && ! value.length);
                    },

                    isEarned(field) {
                        const block = field.block;

                        return block.required || (block.dependsOn || []).length || block.visibilityCondition || this.hasValue(field.key) || field.group;
                    },

                    isChoice(block) {
                        return block.type === 'dropdown' || block.type === 'multiselect';
                    },

                    isLongText(block) {
                        return ['aifield', 'html', 'markdown', 'dictionary'].includes(block.type) || block.list;
                    },

                    isText(block) {
                        return ['string', 'aifield', 'html', 'markdown'].includes(block.type) && ! block.list;
                    },

                    inputType(block) {
                        return block.type === 'number' ? 'number' : (block.type === 'date' ? 'date' : 'text');
                    },

                    choicesOf(field) {
                        return field.block.options || this.picker.options[field.key] || [];
                    },

                    isSelected(key, value) {
                        const current = this.picker.values[key];

                        return Array.isArray(current) ? current.includes(value) : current === value;
                    },

                    setValue(field, value) {
                        this.picker.values[field.key] = field.block.type === 'number' && value !== '' ? Number(value) : value;

                        this.clearDependents(field.key);
                    },

                    onSelect(field, event) {
                        const chosen = [...event.target.selectedOptions].map((option) => option._value ?? option.value);

                        this.setValue(field, field.block.type === 'multiselect' ? chosen.filter((item) => item !== '') : (chosen[0] ?? ''));
                    },

                    insertVariable(field, event) {
                        const path = event.target.value;

                        event.target.value = '';

                        if (path) {
                            this.picker.values[field.key] = (this.picker.values[field.key] || '') + OPEN + path + CLOSE;
                        }
                    },

                    clearDependents(key) {
                        Object.entries(this.picker.schema.blocks).forEach(([other, block]) => {
                            if ((block.dependsOn || []).includes(key)) {
                                delete this.picker.values[other];
                                delete this.picker.options[other];
                                delete this.picker.optionErrors[other];

                                this.clearDependents(other);
                            }
                        });
                    },

                    async loadOptions(key) {
                        const picker = this.picker;
                        const block = picker.schema.blocks[key];

                        if (block.options || picker.options[key] || picker.loading[key] || (! block.source && ! block.enableSearchApi)) {
                            return;
                        }

                        picker.loading[key] = true;
                        picker.optionErrors[key] = '';

                        const response = await this.request(this.url(this.urls.options, { SERVICE: picker.app.id }), 'POST', {
                            version_id: picker.item.version,
                            field_key: key,
                            existing_fields: this.state.input,
                        });

                        if (response.ok) {
                            picker.options[key] = response.data.options;
                        } else {
                            picker.optionErrors[key] = response.data.message || this.messages.ui.error;
                        }

                        picker.loading[key] = false;
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
