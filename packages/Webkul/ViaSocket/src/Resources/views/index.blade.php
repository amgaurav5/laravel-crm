<x-admin::layouts>
    <x-slot:title>
        @lang('viasocket::app.menu.title')
    </x-slot>

    <v-integrations></v-integrations>

    @pushOnce('styles')
        <style>
            .vs-card, .vs-flow, .vs-panel, .vs-empty, .vs-bar { border: 1px solid #d1d5db; border-radius: 8px; background: #fff; }
            .vs-card { display: flex; align-items: center; gap: 12px; padding: 12px 14px; }
            .vs-panel { padding: 16px; }
            .vs-flow { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 16px; }
            .vs-flow-main { display: flex; flex-direction: column; gap: 4px; flex: 1; min-width: 0; }
            .vs-flow-actions { display: flex; align-items: center; gap: 8px; }
            .vs-list { display: flex; flex-direction: column; gap: 12px; }
            .vs-chain { display: flex; align-items: center; gap: 6px; flex: none; }
            .vs-chain-sep { color: #9ca3af; font-size: 14px; }
            .vs-icon { width: 32px; height: 32px; border-radius: 6px; object-fit: contain; background: #f3f4f6; padding: 3px; flex: none; }
            .vs-letter { display: inline-flex; align-items: center; justify-content: center; background: #0e90d9; color: #fff; font-weight: 600; font-size: 13px; padding: 0; }
            .vs-pill { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: #6b7280; }
            .vs-pill::before { content: ''; width: 8px; height: 8px; border-radius: 9999px; background: #9ca3af; }
            .vs-pill.on { color: #15803d; }
            .vs-pill.on::before { background: #22c55e; }
            .vs-switch { position: relative; width: 38px; height: 22px; border-radius: 9999px; border: 0; background: #d1d5db; cursor: pointer; transition: background .15s; flex: none; }
            .vs-switch span { position: absolute; top: 3px; left: 3px; width: 16px; height: 16px; border-radius: 9999px; background: #fff; transition: left .15s; }
            .vs-switch.on { background: #22c55e; }
            .vs-switch.on span { left: 19px; }
            .vs-switch:disabled { opacity: .6; cursor: default; }
            .vs-empty { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 48px 16px; text-align: center; }
            .vs-ideas { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; margin-top: 8px; }
            .vs-chip { border: 1px solid #d1d5db; border-radius: 9999px; padding: 4px 12px; font-size: 12px; color: #6b7280; background: transparent; cursor: pointer; transition: border-color .15s, color .15s; }
            .vs-chip:hover { border-color: #0e90d9; color: #0e90d9; }
            .vs-timeline { display: flex; flex-direction: column; }
            .vs-step { display: flex; gap: 14px; }
            .vs-rail { display: flex; flex-direction: column; align-items: center; width: 32px; flex: none; }
            .vs-badge { width: 32px; height: 32px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; background: #0e90d9; color: #fff; flex: none; }
            .vs-badge.muted { background: #e5e7eb; color: #6b7280; }
            .vs-line { width: 2px; flex: 1; min-height: 20px; background: #d1d5db; margin: 4px 0; }
            .vs-body { flex: 1; min-width: 0; padding-bottom: 16px; }
            .vs-label { margin: 6px 0 8px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; }
            .vs-title { font-size: 14px; font-weight: 600; color: #1f2937; }
            .vs-sub { font-size: 12px; color: #6b7280; }
            .vs-grow { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
            .vs-add { width: 100%; text-align: left; border: 1px dashed #9ca3af; border-radius: 8px; padding: 12px 14px; font-size: 14px; color: #0e90d9; background: transparent; cursor: pointer; }
            .vs-add:hover { background: rgba(14, 144, 217, .06); }
            .vs-bar { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 16px; }
            .vs-hint { font-size: 13px; color: #6b7280; }
            .vs-hint.ready { color: #15803d; }
            .vs-actions { display: flex; align-items: center; gap: 8px; }
            .vs-tile { display: flex; align-items: center; gap: 10px; border: 1px solid #d1d5db; border-radius: 8px; padding: 12px; text-align: left; background: transparent; cursor: pointer; transition: border-color .15s, background .15s; }
            .vs-tile:hover { border-color: #0e90d9; background: rgba(14, 144, 217, .06); }
            .vs-tile-text { display: flex; flex-direction: column; min-width: 0; }
            .vs-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
            @media (max-width: 900px) { .vs-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .vs-flow { flex-direction: column; align-items: flex-start; } }
            .vs-stepper { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #9ca3af; }
            .vs-stepper b { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 9999px; background: #e5e7eb; color: #6b7280; font-size: 12px; margin-right: 4px; }
            .vs-stepper .active { color: #1f2937; font-weight: 600; }
            .vs-stepper .active b, .vs-stepper .done b { background: #0e90d9; color: #fff; }
            .vs-stepper .done { color: #6b7280; }
            .vs-stepper i { color: #d1d5db; font-style: normal; }
            .vs-toast { position: fixed; top: 76px; right: 24px; z-index: 50; padding: 10px 16px; border-radius: 8px; background: #166534; color: #fff; font-size: 14px; box-shadow: 0 4px 14px rgba(0, 0, 0, .2); }
            .vs-select { position: relative; }
            .vs-select-box { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; min-height: 38px; padding: 4px 10px; border: 1px solid #d1d5db; border-radius: 4px; background: #fff; cursor: pointer; font-size: 14px; color: #1f2937; }
            .vs-select-box:hover { border-color: #9ca3af; }
            .vs-select-box.disabled { opacity: .6; cursor: not-allowed; }
            .vs-select-placeholder { color: #9ca3af; }
            .vs-select-caret { margin-left: auto; color: #6b7280; font-size: 11px; }
            .vs-chipx { display: inline-flex; align-items: center; gap: 4px; background: #e0f2fe; color: #075985; border-radius: 4px; padding: 2px 6px; font-size: 12px; }
            .vs-chipx b { cursor: pointer; font-weight: 400; }
            .vs-select-menu { position: absolute; z-index: 40; left: 0; right: 0; top: calc(100% + 4px); background: #fff; border: 1px solid #d1d5db; border-radius: 6px; box-shadow: 0 8px 24px rgba(0, 0, 0, .12); max-height: 280px; overflow: auto; }
            .vs-select-search { width: 100%; padding: 8px 10px; border: 0; border-bottom: 1px solid #e5e7eb; font-size: 13px; outline: none; background: transparent; color: inherit; }
            .vs-option { display: flex; align-items: center; gap: 8px; padding: 8px 10px; font-size: 14px; cursor: pointer; color: #1f2937; }
            .vs-option:hover { background: rgba(14, 144, 217, .08); }
            .vs-option.selected { background: rgba(14, 144, 217, .14); font-weight: 600; }
            .vs-select-empty { padding: 10px; font-size: 13px; color: #6b7280; }
            .vs-link { font-size: 12px; color: #0e90d9; background: transparent; border: 0; cursor: pointer; padding: 0; margin-top: 4px; }
            .vs-rows { display: flex; flex-direction: column; gap: 8px; }
            .vs-row { display: flex; align-items: flex-start; gap: 8px; }
            .vs-row > * { flex: 1; min-width: 0; }
            .vs-row > button { flex: none; }
            .vs-banner { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 14px; border: 1px solid #fcd34d; border-radius: 8px; background: #fffbeb; color: #92400e; font-size: 13px; }
            html.dark .vs-select-box, html.dark .vs-select-menu { background: #111827; border-color: #374151; color: #e5e7eb; }
            html.dark .vs-option { color: #e5e7eb; }
            html.dark .vs-banner { background: #3b2f0b; border-color: #92400e; color: #fde68a; }
            html.dark .vs-card, html.dark .vs-flow, html.dark .vs-panel, html.dark .vs-empty, html.dark .vs-bar { background: #111827; border-color: #1f2937; }
            html.dark .vs-title { color: #f3f4f6; }
            html.dark .vs-icon { background: #1f2937; }
            html.dark .vs-line { background: #374151; }
            html.dark .vs-tile, html.dark .vs-chip, html.dark .vs-add { border-color: #374151; }
            html.dark .vs-stepper .active { color: #f3f4f6; }
        </style>
    @endPushOnce

    @pushOnce('scripts')
        <script src="{{ $connectScript }}"></script>

        <script
            type="text/x-template"
            id="v-vs-select-template"
        >
            <div class="vs-select">
                <input
                    v-if="manual"
                    type="text"
                    class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                    :value="manualText"
                    :placeholder="customPlaceholder"
                    @input="onManual($event.target.value)"
                >

                <template v-else>
                    <div
                        class="vs-select-box"
                        :class="{ disabled }"
                        tabindex="0"
                        @click="toggle"
                        @keydown.enter.prevent="toggle"
                        @keydown.esc="open = false"
                    >
                        <span
                            v-if="! selectedLabels.length"
                            class="vs-select-placeholder"
                            v-text="loading ? texts.loading : (disabled ? texts.connect_to_load : (placeholder || texts.select))"
                        ></span>

                        <template v-else>
                            <span
                                v-if="! multiple"
                                v-text="selectedLabels[0]"
                            ></span>

                            <template v-else>
                                <span
                                    v-for="(label, index) in selectedLabels"
                                    :key="index"
                                    class="vs-chipx"
                                >
                                    <span v-text="label"></span>

                                    <b @click.stop="remove(index)">&times;</b>
                                </span>
                            </template>
                        </template>

                        <span class="vs-select-caret">&#9662;</span>
                    </div>

                    <div
                        v-if="open"
                        class="vs-select-menu"
                    >
                        <input
                            v-model="search"
                            type="text"
                            class="vs-select-search"
                            :placeholder="texts.search"
                            @click.stop
                        >

                        <div
                            v-if="loading"
                            class="vs-select-empty"
                            v-text="texts.loading"
                        ></div>

                        <div
                            v-else-if="error"
                            class="vs-select-empty"
                            style="color: #dc2626"
                        >
                            <span v-text="error"></span>

                            <button
                                type="button"
                                class="vs-link"
                                @click.stop="$emit('load', true)"
                                v-text="texts.retry"
                            ></button>
                        </div>

                        <div
                            v-else-if="! filtered.length"
                            class="vs-select-empty"
                            v-text="texts.no_options"
                        ></div>

                        <div
                            v-for="(option, index) in filtered"
                            :key="index"
                            class="vs-option"
                            :class="{ selected: isSelected(option) }"
                            @click.stop="pick(option)"
                        >
                            <input
                                v-if="multiple"
                                type="checkbox"
                                tabindex="-1"
                                style="pointer-events: none"
                                :checked="isSelected(option)"
                            >

                            <span v-text="option.label"></span>
                        </div>
                    </div>
                </template>

                <button
                    v-if="allowCustom"
                    type="button"
                    class="vs-link"
                    @click="manual = ! manual"
                    v-text="manual ? texts.choose_from_list : (customLabel || texts.enter_manually)"
                ></button>
            </div>
        </script>

        <script
            type="text/x-template"
            id="v-vs-editor-template"
        >
            <div>
                <v-vs-select
                    v-if="kind === 'choice' || kind === 'boolean'"
                    :model-value="modelValue"
                    :options="kind === 'boolean' ? booleanOptions : options"
                    :multiple="block.type === 'multiselect'"
                    :placeholder="block.placeholder"
                    :loading="loading"
                    :error="error"
                    :disabled="disabled"
                    :allow-custom="kind === 'choice' && !! (block.customInputLabel || block.customPlaceholder)"
                    :custom-label="block.customInputLabel"
                    :custom-placeholder="block.customPlaceholder"
                    :texts="texts"
                    @update:model-value="$emit('update:modelValue', $event)"
                    @load="$emit('load', $event)"
                ></v-vs-select>

                <textarea
                    v-else-if="kind === 'textarea'"
                    rows="5"
                    class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                    :value="modelValue"
                    :placeholder="block.placeholder"
                    @input="$emit('update:modelValue', $event.target.value)"
                ></textarea>

                <div
                    v-else-if="kind === 'dictionary'"
                    class="vs-rows"
                >
                    <div
                        v-for="(row, index) in rows"
                        :key="index"
                        class="vs-row"
                    >
                        <input
                            type="text"
                            class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                            :value="row.k"
                            :placeholder="texts.key"
                            @input="row.k = $event.target.value; emitRows()"
                        >

                        <input
                            type="text"
                            class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                            :value="row.v"
                            :placeholder="texts.value"
                            @input="row.v = $event.target.value; emitRows()"
                        >

                        <button
                            type="button"
                            class="secondary-button"
                            @click="rows.splice(index, 1); emitRows()"
                            v-text="texts.remove"
                        ></button>
                    </div>

                    <div>
                        <button
                            type="button"
                            class="secondary-button"
                            @click="rows.push({ k: '', v: '' })"
                            v-text="texts.add_row"
                        ></button>
                    </div>
                </div>

                <div
                    v-else-if="kind === 'sourceGroup'"
                    class="vs-rows"
                >
                    <p
                        v-if="loading"
                        class="vs-hint"
                        v-text="texts.loading"
                    ></p>

                    <p
                        v-else-if="error"
                        class="vs-hint"
                        style="color: #dc2626"
                        v-text="error"
                    ></p>

                    <div
                        v-for="option in options"
                        :key="keyOf(option)"
                    >
                        <label class="mb-1 flex items-center justify-between gap-1 text-xs text-gray-700 dark:text-gray-300">
                            <span>
                                <span v-text="option.label || keyOf(option)"></span>

                                <span
                                    v-if="option.required"
                                    class="text-red-600"
                                >*</span>
                            </span>

                            <select
                                v-if="variables && variables.length && childIsText(option)"
                                class="rounded border border-gray-300 px-1 py-0.5 text-xs text-gray-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                @change="insertInto(option, $event)"
                            >
                                <option
                                    value=""
                                    v-text="texts.insert_value"
                                ></option>

                                <option
                                    v-for="variable in variables"
                                    :key="variable.value"
                                    :value="variable.value"
                                    v-text="variable.label"
                                ></option>
                            </select>
                        </label>

                        <v-vs-editor
                            :block="childBlock(option)"
                            :model-value="(modelValue || {})[keyOf(option)]"
                            :options="option.options || []"
                            :texts="texts"
                            @update:model-value="setGroupValue(keyOf(option), $event)"
                        ></v-vs-editor>

                        <p
                            v-if="option.help"
                            class="mt-1 text-xs text-gray-600 dark:text-gray-300"
                            v-text="option.help"
                        ></p>
                    </div>
                </div>

                <div
                    v-else-if="kind === 'list'"
                    class="vs-rows"
                >
                    <div
                        v-for="(item, index) in (modelValue || [])"
                        :key="index"
                        class="vs-row"
                    >
                        <v-vs-editor
                            :block="rowBlock"
                            :model-value="item"
                            :options="options"
                            :loading="loading"
                            :error="error"
                            :disabled="disabled"
                            :texts="texts"
                            @update:model-value="setRow(index, $event)"
                            @load="$emit('load', $event)"
                        ></v-vs-editor>

                        <button
                            type="button"
                            class="secondary-button"
                            @click="removeRow(index)"
                            v-text="texts.remove"
                        ></button>
                    </div>

                    <div>
                        <button
                            type="button"
                            class="secondary-button"
                            @click="addRow"
                            v-text="texts.add_row"
                        ></button>
                    </div>
                </div>

                <input
                    v-else
                    class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                    :type="kind === 'number' ? 'number' : (kind === 'date' ? 'date' : 'text')"
                    :value="modelValue"
                    :placeholder="block.placeholder"
                    @input="onInput($event.target.value)"
                >
            </div>
        </script>

        <script
            type="text/x-template"
            id="v-integrations-template"
        >
            <div class="flex flex-col gap-4">
                <div
                    v-if="toast"
                    class="vs-toast"
                    v-text="toast"
                ></div>

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

                    <span
                        v-if="view === 'list' || view === 'connections'"
                        class="vs-actions"
                    >
                        <button
                            type="button"
                            class="secondary-button"
                            @click="openConnections"
                            v-text="messages.ui.connected_apps"
                        ></button>

                        <button
                            v-if="flows.length"
                            type="button"
                            class="primary-button"
                            @click="newFlow"
                            v-text="messages.ui.new_flow"
                        ></button>
                    </span>
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
                        class="vs-panel text-gray-600 dark:text-gray-300"
                        v-text="messages.ui.loading"
                    ></div>

                    <div
                        v-else-if="! flows.length"
                        class="vs-empty"
                    >
                        <span
                            class="icon-settings-webhooks"
                            style="font-size: 44px; color: #0e90d9"
                        ></span>

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

                        <div class="vs-ideas">
                            <button
                                v-for="template in templates"
                                :key="template.key"
                                type="button"
                                class="vs-chip"
                                @click="startTemplate(template)"
                                v-text="messages.ui.templates[template.key].label"
                            ></button>
                        </div>
                    </div>

                    <div
                        v-else
                        class="vs-list"
                    >
                        <div
                            v-for="flow in flows"
                            :key="flow.id"
                            class="vs-flow"
                        >
                            <div class="vs-chain">
                                <template
                                    v-for="(node, position) in chain(flow)"
                                    :key="position"
                                >
                                    <span
                                        v-if="position"
                                        class="vs-chain-sep"
                                    >&rsaquo;</span>

                                    <img
                                        v-if="node.icon"
                                        :src="node.icon"
                                        alt=""
                                        class="vs-icon"
                                    >

                                    <span
                                        v-else
                                        class="vs-icon vs-letter"
                                        v-text="node.letter"
                                    ></span>
                                </template>
                            </div>

                            <div class="vs-flow-main">
                                <span
                                    class="vs-title"
                                    v-text="flow.title"
                                ></span>

                                <span
                                    class="vs-sub"
                                    v-text="summary(flow)"
                                ></span>

                                <span
                                    class="vs-pill"
                                    :class="{ on: flow.status === 'active' }"
                                    v-text="flow.status === 'active' ? messages.ui.active : messages.ui.paused"
                                ></span>
                            </div>

                            <div class="vs-flow-actions">
                                <button
                                    type="button"
                                    role="switch"
                                    class="vs-switch"
                                    :class="{ on: flow.status === 'active' }"
                                    :aria-checked="flow.status === 'active'"
                                    :title="flow.status === 'active' ? messages.ui.pause : messages.ui.resume"
                                    :disabled="busy"
                                    @click="toggleFlow(flow)"
                                ><span></span></button>

                                <button
                                    type="button"
                                    class="secondary-button"
                                    @click="openHistory(flow)"
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
                                    @click="removeFlow(flow)"
                                    v-text="messages.ui.delete"
                                ></button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Connected apps -->
                <div
                    v-else-if="view === 'connections'"
                    class="flex flex-col gap-4"
                >
                    <div class="vs-bar">
                        <span class="vs-grow">
                            <span
                                class="vs-title"
                                v-text="messages.ui.connected_apps"
                            ></span>

                            <span
                                class="vs-sub"
                                v-text="messages.ui.connected_apps_info"
                            ></span>
                        </span>

                        <button
                            type="button"
                            class="secondary-button"
                            @click="view = 'list'; error = ''"
                            v-text="messages.ui.back_to_flows"
                        ></button>
                    </div>

                    <div class="vs-panel">
                        <p
                            v-if="connectionsLoading"
                            class="vs-hint"
                            v-text="messages.ui.loading"
                        ></p>

                        <p
                            v-else-if="! connections.length"
                            class="vs-hint"
                            v-text="messages.ui.no_connections"
                        ></p>

                        <div
                            v-else
                            class="vs-list"
                        >
                            <div
                                v-for="connection in connections"
                                :key="connection.service_id"
                                class="vs-card"
                            >
                                <img
                                    v-if="connection.icon"
                                    :src="connection.icon"
                                    alt=""
                                    class="vs-icon"
                                >

                                <span
                                    v-else
                                    class="vs-icon vs-letter"
                                    v-text="connection.name.charAt(0)"
                                ></span>

                                <span class="vs-grow">
                                    <span
                                        class="vs-title"
                                        v-text="connection.name"
                                    ></span>

                                    <span
                                        class="vs-sub"
                                        v-text="connection.used_by.length ? messages.ui.in_use.replace(':flows', connection.used_by.join(', ')) : messages.ui.not_in_use"
                                    ></span>

                                    <span
                                        v-if="connection.connected_at"
                                        class="vs-sub"
                                        v-text="messages.ui.connected_since.replace(':date', connection.connected_at)"
                                    ></span>
                                </span>

                                <button
                                    type="button"
                                    class="secondary-button"
                                    :disabled="busy || connection.used_by.length > 0"
                                    :title="connection.used_by.length ? messages.ui.in_use.replace(':flows', connection.used_by.join(', ')) : ''"
                                    @click="disconnectApp(connection)"
                                    v-text="messages.ui.disconnect"
                                ></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History -->
                <div
                    v-else-if="view === 'history'"
                    class="flex flex-col gap-4"
                >
                    <div class="vs-bar">
                        <span
                            class="vs-title"
                            v-text="messages.ui.history + ' - ' + historyFlow.title"
                        ></span>

                        <button
                            type="button"
                            class="secondary-button"
                            @click="view = 'list'"
                            v-text="messages.ui.back_to_flows"
                        ></button>
                    </div>

                    <div class="vs-panel">
                        <p
                            v-if="historyFlow.trigger.kind !== 'krayin'"
                            class="vs-hint"
                            v-text="messages.ui.history_other"
                        ></p>

                        <p
                            v-else-if="historyLoading"
                            class="vs-hint"
                            v-text="messages.ui.loading"
                        ></p>

                        <p
                            v-else-if="! runs.length"
                            class="vs-hint"
                            v-text="messages.ui.history_empty"
                        ></p>

                        <div
                            v-else
                            class="vs-list"
                        >
                            <div
                                v-for="run in runs"
                                :key="run.id"
                                class="vs-card"
                            >
                                <span
                                    class="vs-pill"
                                    :class="{ on: run.ok }"
                                    v-text="run.ok ? messages.ui.success : messages.ui.failed"
                                ></span>

                                <span class="vs-grow">
                                    <span
                                        class="vs-title"
                                        v-text="(krayinEvents[run.event] || {}).name || run.event"
                                    ></span>

                                    <span
                                        class="vs-sub"
                                        v-text="run.message || ''"
                                    ></span>
                                </span>

                                <span
                                    class="vs-sub"
                                    v-text="run.at"
                                ></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Builder -->
                <div
                    v-else-if="! picker"
                    class="flex flex-col gap-4"
                >
                    <div class="vs-panel">
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

                    <div class="vs-panel">
                        <div class="vs-timeline">
                            <div class="vs-step">
                                <div class="vs-rail">
                                    <span class="vs-badge">1</span>

                                    <span
                                        v-if="draft.trigger"
                                        class="vs-line"
                                    ></span>
                                </div>

                                <div class="vs-body">
                                    <p
                                        class="vs-label"
                                        v-text="messages.ui.trigger"
                                    ></p>

                                    <div
                                        v-if="draft.trigger"
                                        class="vs-card"
                                    >
                                        <img
                                            v-if="draft.trigger.icon"
                                            :src="draft.trigger.icon"
                                            alt=""
                                            class="vs-icon"
                                        >

                                        <span
                                            v-else
                                            class="vs-icon vs-letter"
                                            v-text="triggerLetter(draft.trigger)"
                                        ></span>

                                        <span class="vs-grow">
                                            <span
                                                class="vs-title"
                                                v-text="entryTitle(draft.trigger)"
                                            ></span>

                                            <span
                                                class="vs-sub"
                                                v-text="entrySubtitle(draft.trigger) + (draft.trigger.polled ? ' - ' + messages.ui.polled_hint : '')"
                                            ></span>
                                        </span>

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
                                        class="vs-add"
                                        @click="openPicker('trigger')"
                                        v-text="'+  ' + messages.ui.choose_trigger"
                                    ></button>
                                </div>
                            </div>

                            <div
                                v-for="(step, index) in draft.steps"
                                :key="index"
                                class="vs-step"
                            >
                                <div class="vs-rail">
                                    <span
                                        class="vs-badge"
                                        v-text="index + 2"
                                    ></span>

                                    <span class="vs-line"></span>
                                </div>

                                <div class="vs-body">
                                    <p
                                        v-if="index === 0"
                                        class="vs-label"
                                        v-text="messages.ui.actions"
                                    ></p>

                                    <div class="vs-card">
                                        <img
                                            v-if="step.icon"
                                            :src="step.icon"
                                            alt=""
                                            class="vs-icon"
                                        >

                                        <span
                                            v-else
                                            class="vs-icon vs-letter"
                                            v-text="stepLetter(step)"
                                        ></span>

                                        <span class="vs-grow">
                                            <span
                                                class="vs-title"
                                                v-text="step.name"
                                            ></span>

                                            <span
                                                class="vs-sub"
                                                v-text="step.app_name"
                                            ></span>
                                        </span>

                                        <span class="vs-actions">
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
                            </div>

                            <div
                                v-if="draft.trigger"
                                class="vs-step"
                            >
                                <div class="vs-rail">
                                    <span class="vs-badge muted">+</span>
                                </div>

                                <div class="vs-body">
                                    <p
                                        v-if="! draft.steps.length"
                                        class="vs-label"
                                        v-text="messages.ui.actions"
                                    ></p>

                                    <button
                                        type="button"
                                        class="vs-add"
                                        @click="openPicker('action')"
                                        v-text="'+  ' + messages.ui.add_action"
                                    ></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="vs-bar">
                        <span
                            v-if="error"
                            class="vs-hint"
                            style="color: #dc2626"
                            v-text="error"
                        ></span>

                        <span
                            v-else
                            class="vs-hint"
                            :class="{ ready: ! publishHint }"
                            v-text="publishHint || messages.ui.hint_ready"
                        ></span>

                        <span class="vs-actions">
                            <button
                                type="button"
                                class="secondary-button"
                                @click="cancelBuilder"
                                v-text="messages.ui.cancel"
                            ></button>

                            <button
                                type="button"
                                class="primary-button"
                                :disabled="busy || !! publishHint"
                                @click="saveFlow"
                                v-text="busy ? messages.ui.publishing : (draft.id ? messages.ui.update : messages.ui.publish)"
                            ></button>
                        </span>
                    </div>
                </div>

                <!-- Picker -->
                <div
                    v-else
                    class="flex flex-col gap-4 rounded-lg border border-gray-300 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="vs-stepper">
                        <template
                            v-for="(label, position) in stepperLabels"
                            :key="position"
                        >
                            <i v-if="position">&rsaquo;</i>

                            <span :class="{ active: position === stepIndex, done: position < stepIndex }">
                                <b v-text="position + 1"></b><span v-text="label"></span>
                            </span>
                        </template>
                    </div>

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
                                @click="closePicker"
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

                        <template v-if="visibleTools.length">
                            <p
                                class="vs-label"
                                v-text="messages.ui.tools_title"
                            ></p>

                            <div class="vs-grid">
                                <button
                                    v-for="tool in visibleTools"
                                    :key="tool.key"
                                    type="button"
                                    class="vs-tile"
                                    @click="chooseTool(tool)"
                                >
                                    <span
                                        class="vs-icon vs-letter"
                                        v-text="messages.ui.tools[tool.key].name.charAt(0)"
                                    ></span>

                                    <span class="vs-tile-text">
                                        <span
                                            class="vs-title"
                                            v-text="messages.ui.tools[tool.key].name"
                                        ></span>

                                        <span
                                            class="vs-sub"
                                            v-text="messages.ui.tools[tool.key].description"
                                        ></span>
                                    </span>
                                </button>
                            </div>

                            <p
                                class="vs-label"
                                v-text="messages.ui.apps_title"
                            ></p>
                        </template>

                        <div class="vs-grid">
                            <button
                                v-for="app in picker.apps"
                                :key="app.id"
                                type="button"
                                class="vs-tile"
                                @click="chooseApp(app)"
                            >
                                <img
                                    v-if="app.icon"
                                    :src="app.icon"
                                    alt=""
                                    class="vs-icon"
                                >

                                <span
                                    v-else
                                    class="vs-icon vs-letter"
                                    v-text="app.special ? (app.id === '__schedule__' ? 'S' : 'K') : app.name.charAt(0)"
                                ></span>

                                <span class="vs-tile-text">
                                    <span
                                        class="vs-title"
                                        v-text="app.name"
                                    ></span>

                                    <span
                                        v-if="app.special"
                                        class="vs-sub"
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

                    <!-- Fields -->
                    <form
                        v-else-if="picker.stage === 'form'"
                        class="flex flex-col gap-4"
                        @submit.prevent="confirmForm"
                    >
                        <div
                            v-if="! picker.app.connected"
                            class="vs-banner"
                        >
                            <span v-text="messages.ui.connect_banner.replace(':name', picker.app.name)"></span>

                            <button
                                type="button"
                                class="primary-button"
                                @click="connectApp"
                                v-text="messages.ui.connect.replace(':name', picker.app.name)"
                            ></button>
                        </div>

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
                                            v-text="pretty(path)"
                                        ></option>
                                    </select>
                                </label>

                                <v-vs-editor
                                    :block="field.block"
                                    :model-value="picker.values[field.key]"
                                    :options="choicesOf(field)"
                                    :loading="!! picker.loading[field.key]"
                                    :error="picker.optionErrors[field.key] || ''"
                                    :disabled="needsConnection(field)"
                                    :variables="picker.kind === 'action' ? variableOptions : []"
                                    :texts="messages.ui"
                                    @update:model-value="setValue(field, $event)"
                                    @load="loadOptions(field.key, $event)"
                                ></v-vs-editor>

                                <p
                                    v-if="field.block.help"
                                    class="mt-1 text-xs text-gray-600 dark:text-gray-300"
                                    v-text="field.block.help"
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

            // Existing viaSocket plugs, shown under their own names in a row of their own.
            const TOOLS = [
                { key: 'delay', service_id: 'row29ruc9gs1', action: 'Pause Workflow' },
                { key: 'ai', service_id: 'row29ruc9gs1', action: 'Run AI Model' },
                { key: 'decision', service_id: 'rowobx9xja6l', action: 'Decision Maker' },
                { key: 'summarize', service_id: 'rowaqdsydi5g', action: 'Summarize Text' },
                { key: 'translate', service_id: 'rowl7uw6muox', action: 'Translate Text' },
                { key: 'json', service_id: 'rowt1a8wzkke', action: 'Convert to JSON' },
                { key: 'extract', service_id: 'rowki67sfgbt', action: 'Extract Website Data' },
                { key: 'flow', service_id: 'row29ruc9gs1', action: 'Flow in Flow' },
                { key: 'storage', service_id: 'rowhc2623dta', action: null },
            ];

            const TOOL_SERVICES = [...new Set(TOOLS.map((tool) => tool.service_id))];

            const TEMPLATES = [
                {
                    key: 'lead_slack',
                    trigger: { kind: 'krayin', event: 'lead.create.after' },
                    action: { service_id: 'rowbu58rc', name: 'Send Message' },
                },
                {
                    key: 'email_sheet',
                    trigger: { kind: 'app', service_id: 'rowo0bqrhj5g', name: 'New Email Arrives' },
                    action: { service_id: 'rowqm5xi2', name: 'Add New Row to Sheet' },
                },
                {
                    key: 'morning_email',
                    trigger: { kind: 'schedule', cron: 'Every day at 9am' },
                    action: { service_id: 'rowo0bqrhj5g', name: 'Send Email' },
                },
            ];

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

            const same = (a, b) => JSON.stringify(a) === JSON.stringify(b);

            app.component('v-vs-select', {
                template: '#v-vs-select-template',

                props: ['modelValue', 'options', 'multiple', 'placeholder', 'loading', 'error', 'disabled', 'allowCustom', 'customLabel', 'customPlaceholder', 'texts'],

                emits: ['update:modelValue', 'load'],

                data() {
                    return {
                        open: false,

                        search: '',

                        manual: false,

                        manualText: Array.isArray(this.modelValue) ? JSON.stringify(this.modelValue) : String(this.modelValue ?? ''),
                    };
                },

                computed: {
                    list() {
                        return this.options || [];
                    },

                    filtered() {
                        const term = this.search.trim().toLowerCase();

                        return term ? this.list.filter((option) => String(option.label).toLowerCase().includes(term)) : this.list;
                    },

                    selectedLabels() {
                        const values = this.multiple ? (Array.isArray(this.modelValue) ? this.modelValue : []) : (this.modelValue === undefined || this.modelValue === null || this.modelValue === '' ? [] : [this.modelValue]);

                        return values.map((value) => this.list.find((option) => same(option.value, value))?.label ?? String(value));
                    },
                },

                mounted() {
                    document.addEventListener('click', this.onOutside);
                },

                beforeUnmount() {
                    document.removeEventListener('click', this.onOutside);
                },

                methods: {
                    onOutside(event) {
                        if (! this.$el.contains(event.target)) {
                            this.open = false;
                        }
                    },

                    toggle() {
                        if (this.disabled) {
                            return;
                        }

                        this.open = ! this.open;
                        this.search = '';

                        if (this.open) {
                            this.$emit('load', false);
                        }
                    },

                    isSelected(option) {
                        return this.multiple
                            ? (Array.isArray(this.modelValue) ? this.modelValue : []).some((value) => same(value, option.value))
                            : same(this.modelValue, option.value);
                    },

                    pick(option) {
                        if (! this.multiple) {
                            this.$emit('update:modelValue', option.value);
                            this.open = false;

                            return;
                        }

                        const current = Array.isArray(this.modelValue) ? this.modelValue : [];

                        this.$emit('update:modelValue', this.isSelected(option) ? current.filter((value) => ! same(value, option.value)) : [...current, option.value]);
                    },

                    remove(index) {
                        this.$emit('update:modelValue', (this.modelValue || []).filter((value, position) => position !== index));
                    },

                    onManual(text) {
                        this.manualText = text;

                        if (! this.multiple) {
                            this.$emit('update:modelValue', text);

                            return;
                        }

                        let parsed = null;

                        if (text.trim().startsWith('[')) {
                            try {
                                parsed = JSON.parse(text);
                            } catch (exception) {
                                parsed = null;
                            }
                        }

                        this.$emit('update:modelValue', parsed ?? text.split(',').map((item) => item.trim()).filter(Boolean));
                    },
                },
            });

            app.component('v-vs-editor', {
                template: '#v-vs-editor-template',

                props: ['block', 'modelValue', 'options', 'loading', 'error', 'disabled', 'texts', 'variables'],

                emits: ['update:modelValue', 'load'],

                data() {
                    return {
                        rows: Object.entries(this.modelValue && ! Array.isArray(this.modelValue) && typeof this.modelValue === 'object' ? this.modelValue : {}).map(([k, v]) => ({ k, v })),

                        booleanOptions: [{ label: this.texts.yes, value: true }, { label: this.texts.no, value: false }],
                    };
                },

                computed: {
                    kind() {
                        const block = this.block;

                        if (block.list && block.type !== 'dictionary') {
                            return 'list';
                        }

                        if (block.type === 'dictionary') {
                            return 'dictionary';
                        }

                        if (block.type === 'input groups') {
                            return 'sourceGroup';
                        }

                        if (block.type === 'dropdown' || block.type === 'multiselect') {
                            return 'choice';
                        }

                        if (['aifield', 'html', 'markdown'].includes(block.type)) {
                            return 'textarea';
                        }

                        return ['boolean', 'number', 'date'].includes(block.type) ? block.type : 'string';
                    },

                    rowBlock() {
                        return { ...this.block, list: false };
                    },
                },

                methods: {
                    onInput(text) {
                        this.$emit('update:modelValue', this.kind === 'number' && text !== '' ? Number(text) : text);
                    },

                    emitRows() {
                        this.$emit('update:modelValue', Object.fromEntries(this.rows.filter((row) => row.k !== '').map((row) => [row.k, row.v])));
                    },

                    keyOf(option) {
                        return String(option.key ?? option.value);
                    },

                    childBlock(option) {
                        return {
                            type: option.type || 'string',
                            label: option.label,
                            placeholder: option.placeholder,
                            options: option.options,
                            list: false,
                        };
                    },

                    childIsText(option) {
                        return ['string', 'aifield', 'html', 'markdown', undefined].includes(option.type);
                    },

                    insertInto(option, event) {
                        const path = event.target.value;

                        event.target.value = '';

                        if (path) {
                            this.setGroupValue(this.keyOf(option), String((this.modelValue || {})[this.keyOf(option)] ?? '') + '{' + '{' + path + '}' + '}');
                        }
                    },

                    setGroupValue(key, value) {
                        const next = { ...(this.modelValue || {}) };

                        value === '' || value === undefined || value === null ? delete next[key] : next[key] = value;

                        this.$emit('update:modelValue', next);
                    },

                    addRow() {
                        this.$emit('update:modelValue', [...(this.modelValue || []), this.block.type === 'boolean' ? false : '']);
                    },

                    setRow(index, value) {
                        this.$emit('update:modelValue', (this.modelValue || []).map((item, position) => position === index ? value : item));
                    },

                    removeRow(index) {
                        this.$emit('update:modelValue', (this.modelValue || []).filter((item, position) => position !== index));
                    },
                },
            });

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

                        template: null,

                        templates: TEMPLATES,

                        connections: [],

                        connectionsLoading: false,

                        historyFlow: null,

                        runs: [],

                        historyLoading: false,

                        toast: '',

                        toastTimer: null,

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
                            connections: {!! json_encode(route('admin.integrations.connections.index'), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},

                            disconnect: {!! json_encode(route('admin.integrations.connections.destroy', ['serviceId' => '__SERVICE__']), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},

                            history: {!! json_encode(route('admin.integrations.flows.history', ['id' => '__ID__']), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                        },
                    };
                },

                computed: {
                    publishHint() {
                        const ui = this.messages.ui;

                        if (! this.draft.title.trim()) {
                            return ui.hint_title;
                        }

                        if (! this.draft.trigger) {
                            return ui.hint_trigger;
                        }

                        return this.draft.steps.length ? '' : ui.hint_action;
                    },

                    stepperLabels() {
                        const ui = this.messages.ui;

                        return [ui.step_app, this.picker.kind === 'trigger' ? ui.step_trigger : ui.step_action, ui.step_setup];
                    },

                    stepIndex() {
                        const stage = this.picker.stage;

                        return stage === 'apps' ? 0 : (['connect', 'form'].includes(stage) ? 2 : 1);
                    },

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

                    visibleTools() {
                        if (this.picker.kind !== 'action') {
                            return [];
                        }

                        const term = this.picker.query.trim().toLowerCase();

                        return TOOLS.filter((tool) => ! term || (this.messages.ui.tools[tool.key].name + ' ' + this.messages.ui.tools[tool.key].description).toLowerCase().includes(term));
                    },

                    variableOptions() {
                        return this.variablePaths.map((path) => ({ value: path, label: this.pretty(path) }));
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

                            const group = block.type === 'input groups' && (schema.steps[key] || []).length > 0;

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

                    stepLetter(step) {
                        return (step.app_name === this.messages.ui.tools_category ? step.name : (step.app_name || '?')).charAt(0);
                    },

                    summary(flow) {
                        const steps = flow.steps.map((step) => (step.app_name === this.messages.ui.tools_category ? '' : step.app_name + ' - ') + step.name).join(', ');

                        return flow.trigger.label + '  >  ' + steps;
                    },

                    chain(flow) {
                        const trigger = flow.trigger;

                        const nodes = [{ icon: trigger.icon || null, letter: trigger.kind === 'schedule' ? 'S' : (trigger.kind === 'krayin' ? 'K' : (trigger.label || '?').charAt(0)) }];

                        flow.steps.forEach((step) => nodes.push({ icon: step.icon || null, letter: this.stepLetter(step) }));

                        return nodes;
                    },

                    closePicker() {
                        this.picker = null;
                        this.template = null;
                    },

                    startTemplate(template) {
                        this.newFlow();

                        this.draft.title = this.messages.ui.templates[template.key].title;
                        this.template = template;

                        this.continueTemplate();
                    },

                    async continueTemplate() {
                        const template = this.template;

                        if (! template) {
                            return;
                        }

                        if (! this.draft.trigger) {
                            const trigger = template.trigger;

                            if (trigger.kind === 'krayin') {
                                this.draft.trigger = { kind: 'krayin', event: trigger.event, label: this.messages.ui.krayin + ' - ' + this.krayinEvents[trigger.event].name };

                                return this.continueTemplate();
                            }

                            if (trigger.kind === 'schedule') {
                                this.draft.trigger = { kind: 'schedule', cron: trigger.cron, label: this.messages.ui.schedule + ' - ' + trigger.cron };

                                return this.continueTemplate();
                            }

                            return this.openTemplateItem('trigger', trigger);
                        }

                        if (! this.draft.steps.length) {
                            return this.openTemplateItem('action', template.action);
                        }

                        this.template = null;
                    },

                    async openTemplateItem(kind, wanted) {
                        this.openPicker(kind);

                        await this.chooseApp({ id: wanted.service_id });

                        const item = this.pickerItems.find((candidate) => candidate.name.trim().toLowerCase() === wanted.name.toLowerCase());

                        if (item) {
                            await this.chooseItem(item);
                        }
                    },

                    async chooseTool(tool) {
                        await this.chooseApp({ id: tool.service_id });

                        if (! tool.action) {
                            return;
                        }

                        const item = this.pickerItems.find((candidate) => candidate.name === this.messages.ui.tools[tool.key].name);

                        if (item) {
                            await this.chooseItem(item);
                        }
                    },

                    triggerLetter(trigger) {
                        return trigger.kind === 'schedule' ? 'S' : (trigger.kind === 'krayin' ? 'K' : (this.entrySubtitle(trigger) || '?').charAt(0));
                    },

                    async openConnections() {
                        this.error = '';
                        this.connections = [];
                        this.view = 'connections';
                        this.connectionsLoading = true;

                        const response = await this.request(this.urls.connections);

                        this.connections = response.ok ? response.data.connections : [];
                        this.connectionsLoading = false;
                    },

                    async disconnectApp(connection) {
                        if (! window.confirm(this.messages.ui.disconnect_confirm.replace(':name', connection.name))) {
                            return;
                        }

                        this.busy = true;
                        this.error = '';

                        const response = await this.request(this.url(this.urls.disconnect, { SERVICE: connection.service_id }), 'DELETE');

                        this.busy = false;

                        if (! response.ok) {
                            this.fail(response);

                            return;
                        }

                        this.notify(this.messages.ui.disconnected);

                        await this.openConnections();
                    },

                    async openHistory(flow) {
                        this.error = '';
                        this.historyFlow = flow;
                        this.runs = [];
                        this.view = 'history';

                        if (flow.trigger.kind !== 'krayin') {
                            return;
                        }

                        this.historyLoading = true;

                        const response = await this.request(this.url(this.urls.history, { ID: flow.id }));

                        this.runs = response.ok ? response.data.runs : [];
                        this.historyLoading = false;
                    },

                    entryTitle(trigger) {
                        return trigger.name || (trigger.label || '').split(' - ').slice(1).join(' - ') || trigger.label;
                    },

                    entrySubtitle(trigger) {
                        return trigger.kind === 'krayin' ? this.messages.ui.krayin : (trigger.kind === 'schedule' ? this.messages.ui.schedule : (trigger.label || '').split(' - ')[0]);
                    },

                    pretty(path) {
                        return path.replace(/^data\./, '').split('.').map((part) => part.replace(/_/g, ' ').replace(/^./, (letter) => letter.toUpperCase())).join(' \u203a ');
                    },

                    notify(message) {
                        clearTimeout(this.toastTimer);

                        this.toast = message;
                        this.toastTimer = setTimeout(() => this.toast = '', 3000);
                    },

                    cancelBuilder() {
                        if ((this.draft.trigger || this.draft.steps.length || this.draft.title.trim()) && ! window.confirm(this.messages.ui.leave_confirm)) {
                            return;
                        }

                        this.template = null;
                        this.view = 'list';
                        this.error = '';
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

                        this.notify(this.draft.id ? this.messages.ui.updated : this.messages.ui.published);

                        this.view = 'list';
                    },

                    async toggleFlow(flow) {
                        this.busy = true;
                        this.error = '';

                        const response = await this.request(this.url(this.urls.status, { ID: flow.id }), 'POST', { active: flow.status !== 'active' });

                        this.busy = false;

                        if (response.ok) {
                            await this.loadFlows();

                            this.notify(flow.status === 'active' ? this.messages.ui.paused_toast : this.messages.ui.resumed_toast);
                        } else {
                            this.fail(response);
                        }
                    },

                    async removeFlow(flow) {
                        if (! window.confirm(this.messages.ui.delete_confirm)) {
                            return;
                        }

                        this.busy = true;
                        this.error = '';

                        const response = await this.request(this.url(this.urls.destroy, { ID: flow.id }), 'DELETE');

                        this.busy = false;

                        if (response.ok) {
                            await this.loadFlows();

                            this.notify(this.messages.ui.deleted_toast);
                        } else {
                            this.fail(response);
                        }
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
                            pending: {},
                            loaded: {},
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
                        const stages = { events: 'apps', schedule: 'apps', items: 'apps', form: 'items' };

                        if (this.picker.stage === 'apps') {
                            this.closePicker();

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

                        this.picker.apps = special.concat(response.ok ? response.data.apps.filter((app) => ! TOOL_SERVICES.includes(app.id)) : []);
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

                        const tools = TOOLS.filter((tool) => tool.service_id === app.id);

                        if (tools.length) {
                            const names = Object.fromEntries(tools.filter((tool) => tool.action).map((tool) => [tool.action, this.messages.ui.tools[tool.key].name]));

                            response.data.name = tools.some((tool) => tool.action) ? this.messages.ui.tools_category : this.messages.ui.tools[tools[0].key].name;
                            response.data.icon = null;

                            if (Object.keys(names).length) {
                                response.data.actions = response.data.actions.filter((action) => names[action.name]).map((action) => ({ ...action, name: names[action.name] }));
                            }
                        }

                        this.picker.app = response.data;
                        this.picker.stage = 'items';
                    },

                    chooseEvent(key) {
                        this.draft.trigger = { kind: 'krayin', event: key, label: this.messages.ui.krayin + ' - ' + this.krayinEvents[key].name };
                        this.picker = null;

                        this.continueTemplate();
                    },

                    confirmSchedule() {
                        if (! this.picker.cron.trim()) {
                            this.error = this.messages.ui.required.replace(':field', this.messages.ui.cron_label);

                            return;
                        }

                        this.draft.trigger = { kind: 'schedule', cron: this.picker.cron.trim(), label: this.messages.ui.schedule + ' - ' + this.picker.cron.trim() };
                        this.picker = null;

                        this.continueTemplate();
                    },

                    async chooseItem(item, existing = {}) {
                        this.picker.item = item;
                        this.picker.values = { ...existing };

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
                        this.picker.options = {};
                        this.picker.optionErrors = {};

                        this.preloadOptions();
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

                        this.preloadOptions();
                    },

                    confirmForm() {
                        this.error = '';

                        if (! this.picker.app.connected) {
                            this.error = this.messages.ui.connect_first.replace(':name', this.picker.app.name);

                            return;
                        }

                        const missing = this.ordered.find((field) => ! field.group && field.block.required && this.state.visible[field.key] && ! this.hasValue(field.key));

                        if (missing) {
                            this.error = this.messages.ui.required.replace(':field', missing.block.label);

                            return;
                        }

                        const emptyChild = this.ordered
                            .filter((field) => ! field.group && field.block.type === 'input groups' && this.state.visible[field.key])
                            .flatMap((field) => (this.picker.options[field.key] || []).map((option) => ({ field, option })))
                            .find(({ field, option }) => option.required && ! (this.picker.values[field.key] || {})[String(option.key ?? option.value)]);

                        if (emptyChild) {
                            this.error = this.messages.ui.required.replace(':field', emptyChild.option.label || emptyChild.option.key);

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
                                polled: !! item.polled,
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

                        this.continueTemplate();
                    },

                    hasValue(key) {
                        const value = this.picker?.values[key];

                        return value !== undefined && value !== null && value !== ''
                            && ! (Array.isArray(value) && ! value.length)
                            && ! (typeof value === 'object' && ! Array.isArray(value) && ! Object.keys(value).length);
                    },

                    isEarned(field) {
                        const block = field.block;

                        return block.required || (block.dependsOn || []).length || block.visibilityCondition || this.hasValue(field.key) || field.group;
                    },

                    needsOptions(block) {
                        return block.type === 'dropdown' || block.type === 'multiselect' || block.type === 'input groups';
                    },

                    needsConnection(field) {
                        return ! this.picker.app.connected && this.needsOptions(field.block) && ! field.block.options;
                    },

                    isText(block) {
                        return ['string', 'aifield', 'html', 'markdown'].includes(block.type) && ! block.list;
                    },

                    choicesOf(field) {
                        return field.block.options || this.picker.options[field.key] || [];
                    },

                    setValue(field, value) {
                        this.picker.values[field.key] = value;

                        this.clearDependents(field.key);
                        this.preloadOptions();
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
                                if (block.type !== 'input groups') {
                                    delete this.picker.values[other];
                                }

                                delete this.picker.options[other];
                                delete this.picker.optionErrors[other];
                                delete this.picker.loaded[other];

                                this.clearDependents(other);
                            }
                        });
                    },

                    preloadOptions() {
                        this.$nextTick(() => {
                            if (! this.picker?.schema) {
                                return;
                            }

                            this.ordered.forEach((field) => {
                                if (! field.group && this.needsOptions(field.block) && this.state.visible[field.key]) {
                                    this.loadOptions(field.key);
                                }
                            });
                        });
                    },

                    async loadOptions(key, force = false) {
                        const picker = this.picker;
                        const block = picker.schema.blocks[key];

                        if (block.options || ! picker.app.connected) {
                            return;
                        }

                        const signature = JSON.stringify((block.dependsOn || []).map((dependency) => picker.values[dependency] ?? null));

                        if (! force && (picker.pending[key] === signature || (picker.options[key] && picker.loaded[key] === signature))) {
                            return;
                        }

                        picker.pending[key] = signature;
                        picker.loading[key] = true;
                        picker.optionErrors[key] = '';

                        const response = await this.request(this.url(this.urls.options, { SERVICE: picker.app.id }), 'POST', {
                            version_id: picker.item.version,
                            field_key: key,
                            existing_fields: this.state.input,
                        });

                        if (this.picker !== picker || picker.pending[key] !== signature) {
                            return;
                        }

                        picker.pending[key] = null;
                        picker.loading[key] = false;

                        if (! response.ok) {
                            picker.optionErrors[key] = response.data.message || this.messages.ui.error;

                            return;
                        }

                        picker.options[key] = response.data.options;
                        picker.loaded[key] = signature;

                        if (block.type === 'input groups' && picker.values[key]) {
                            const allowed = response.data.options.map((option) => String(option.key ?? option.value));

                            picker.values[key] = Object.fromEntries(Object.entries(picker.values[key]).filter(([name]) => allowed.includes(name)));
                        }
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
