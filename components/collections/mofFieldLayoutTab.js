const mofFieldLayoutFieldRowElement = {
    props: {
        fieldRowData: {
            type: Object,
            default: {}
        }
    },
    template: `
        <q-card class="cursor-move mof-field-row-container" :class="fieldRowData['fields'].length > 0 ? 'q-pt-md' : ''">
            <q-card-section v-if="fieldRowData['fields'].length === 0" class="q-pa-none q-mb-xs row justify-end">
                <q-btn square color="red" text-color="white" size="sm" @click="deleteFieldRow(fieldRowData);" icon="fas fa-times" dense aria-label="Delete field row" tabindex="0">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Delete field row
                    </q-tooltip>
                </q-btn>
            </q-card-section>
            <q-card-section class="q-pa-sm">
                <div>
                    <draggable v-model="fieldRowData['fields']" v-bind="dragOptions" class="row justify-start q-col-gutter-sm mof-field-row" group="configArrItem" :move="validateDragDrop">
                        <template #item="{ element: field }">
                            <div :class="getFieldClassStr(field)">
                                <q-card flat bordered class="cursor-move black-border">
                                    <q-card-section class="q-pa-xs row q-col-gutter-sm">
                                        <div class="text-subtitle1 text-bold">{{ field['fieldName'] }}</div>
                                        <div>
                                            <q-btn color="grey-4" text-color="black" size="sm" @click="openFieldEditPopup(field);" icon="fas fa-edit" dense aria-label="Edit field size" tabindex="0">
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Edit field size
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div>
                                            <q-btn color="grey-4" text-color="black" size="sm" @click="deleteField(fieldRowData['fields'], field);" icon="fas fa-times" dense aria-label="Remove field" tabindex="0">
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Remove field
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </div>
                        </template>
                    </draggable>
                </div>
            </q-card-section>
        </q-card>
    `,
    components: {
        'draggable': draggable
    },
    setup(_, context) {
        const dragOptions = Vue.computed(() => {
            return {
                animation: 200,
                ghostClass: 'ghost'
            };
        });

        function deleteField(row, field) {
            context.emit('delete:field', {row: row, field: field});
        }

        function deleteFieldRow(row) {
            context.emit('delete:field-row', row);
        }

        function getClassName(size, value) {
            let className;
            if(size === 'xs'){
                className = 'col-' + value.toString();
            }
            else{
                className = 'col-' + size + '-' + value.toString();
            }
            return className;
        }

        function getFieldClassStr(field) {
            const strArr = [];
            if(field.hasOwnProperty('xs-col-width') && field['xs-col-width']){
                strArr.push(getClassName('xs', field['xs-col-width']));
            }
            else{
                strArr.push(getClassName('xs', 12));
            }
            if(field.hasOwnProperty('sm-col-width') && field['sm-col-width']){
                strArr.push(getClassName('sm', field['sm-col-width']));
            }
            if(field.hasOwnProperty('md-col-width') && field['md-col-width']){
                strArr.push(getClassName('md', field['md-col-width']));
            }
            if(field.hasOwnProperty('lg-col-width') && field['lg-col-width']){
                strArr.push(getClassName('lg', field['lg-col-width']));
            }
            if(field.hasOwnProperty('xl-col-width') && field['xl-col-width']){
                strArr.push(getClassName('xl', field['xl-col-width']));
            }
            return strArr.join(' ');
        }

        function openFieldEditPopup(field) {
            context.emit('open:field-edit', field);
        }

        function validateDragDrop(evt){
            return evt.to.classList.contains('mof-field-row');
        }

        return {
            dragOptions,
            deleteField,
            deleteFieldRow,
            getFieldClassStr,
            openFieldEditPopup,
            validateDragDrop
        }
    }
};

const mofFieldLayoutTab = {
    props: {
        fieldType: {
            type: String,
            default: 'occurrence'
        }
    },
    template: `
        <div class="fit q-pa-sm column q-gutter-sm">
            <div class="row justify-between">
                <div>
                    <template v-if="editsExist">
                        <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                    </template>
                </div>
                <div class="row justify-end q-gutter-sm">
                    <q-btn color="secondary" @click="processSaveUpdateData();" label="Save Edits" :disabled="!editsExist" tabindex="0" />
                </div>
            </div>
            <div class="row justify-between">
                <div>
                    <div>
                        <q-btn color="primary" @click="openLiveViewPopup();" label="Live View" tabindex="0" />
                    </div>
                </div>
                <div class="row justify-end q-gutter-sm">
                    <div>
                        <q-btn color="primary" @click="addFieldRow();" label="Add Row" tabindex="0" />
                    </div>
                    <div>
                        <q-btn color="primary" @click="addFieldRowGroup();" label="Add Row Group" tabindex="0" />
                    </div>
                    <div>
                        <q-btn color="primary" @click="showAvailableFieldsPopup = true" label="Available Fields" tabindex="0" />
                    </div>
                </div>
            </div>
            <template v-if="editDataArr.length > 0">
                <draggable v-model="editDataArr" v-bind="dragOptions" class="q-gutter-sm items-center mof-field-container" group="configArrItem" :move="validateDragDrop">
                    <template #item="{ element: configObj }">
                        <template v-if="configObj['type'] === 'dataFieldRow'">
                            <mof-field-layout-field-row-element :field-row-data="configObj" @delete:field="deleteField" @delete:field-row="deleteFieldRow" @open:field-edit="openFieldEditPopup"></mof-field-layout-field-row-element>
                        </template>
                        <template v-else-if="configObj['type'] === 'dataFieldRowGroup'">
                            <q-card flat bordered class="full-width q-pa-sm map-configurations-layer-element mof-field-row-group-container">
                                <div class="q-pt-xs row justify-between self-center" :class="(!configObj['expansion'] || (configObj['expansion'] && (configObj['rows'].length === 0 || expandedGroupArr.includes(configObj)))) ? 'q-pb-lg' : 'q-pb-xs'">
                                    <div class="text-bold row justify-start q-gutter-md">
                                        <div>
                                            {{ configObj['label'] }}
                                        </div>
                                        <template v-if="configObj['expansion'] && configObj['rows'].length > 0">
                                            <template v-if="expandedGroupArr.includes(configObj)">
                                                <q-icon role="button" name="arrow_drop_up" class="cursor-pointer" size="sm" @click="hideRowGroup(configObj);" @keyup.enter="hideRowGroup(configObj);" aria-label="Hide row" tabindex="0">
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        Hide rows
                                                    </q-tooltip>
                                                </q-icon>
                                            </template>
                                            <template v-else>
                                                <q-icon role="button" name="arrow_drop_down" class="cursor-pointer text-bold" size="sm" @click="showRowGroup(configObj);" @keyup.enter="showRowGroup(configObj);" aria-label="Show rows" tabindex="0">
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        Show rows
                                                    </q-tooltip>
                                                </q-icon>
                                            </template>
                                        </template>
                                    </div>
                                    <div>
                                        <q-btn color="grey-4" text-color="black" size="sm" @click="openFieldRowGroupEditPopup(configObj);" icon="fas fa-edit" dense aria-label="Edit row group" tabindex="0">
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Edit row group
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </div>
                                <template v-if="!configObj['expansion'] || expandedGroupArr.includes(configObj)">
                                    <draggable v-model="configObj['rows']" v-bind="dragOptions" class="q-pa-sm bg-white q-gutter-y-sm mof-field-row-group" group="configArrItem" :move="validateDragDrop">
                                        <template #item="{ element: row }">
                                            <mof-field-layout-field-row-element :field-row-data="row" @delete:field="deleteField" @delete:field-row="deleteFieldRow" @open:field-edit="openFieldEditPopup"></mof-field-layout-field-row-element>
                                        </template>
                                    </draggable>
                                </template>
                            </q-card>
                        </template>
                    </template>
                </draggable>
            </template>
            <template v-else>
                <div class="q-pa-md row justify-center text-h6 text-bold">
                    There is currently no layout data to display
                </div>
            </template>
        </div>
        <template v-if="showFieldRowGroupEditorPopup">
            <q-dialog class="z-top" v-model="showFieldRowGroupEditorPopup" persistent>
                <q-card class="sm-popup">
                    <div class="row justify-end items-start map-sm-popup">
                        <div>
                            <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="showFieldRowGroupEditorPopup = false" aria-label="Close window" tabindex="0"></q-btn>
                        </div>
                    </div>
                    <div class="q-pa-md column q-col-gutter-sm">
                        <div class="row">
                            <div class="col-grow">
                                <text-field-input-element label="Label" :value="editFieldRowGroup['label']" @update:value="(value) => editFieldRowGroup['label'] = value"></text-field-input-element>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-grow self-center">
                                <checkbox-input-element label="Expansion Group" :value="editFieldRowGroup['expansion']" @update:value="(value) => editFieldRowGroup['expansion'] = Number(value) === 1"></checkbox-input-element>
                            </div>
                        </div>
                    </div>
                </q-card>
            </q-dialog>
        </template>
        <template v-if="showFieldEditorPopup">
            <q-dialog class="z-top" v-model="showFieldEditorPopup" persistent>
                <q-card class="sm-popup">
                    <div class="row justify-end items-start map-sm-popup">
                        <div>
                            <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="showFieldEditorPopup = false" aria-label="Close window" tabindex="0"></q-btn>
                        </div>
                    </div>
                    <div class="q-pa-md column q-col-gutter-sm">
                        <div class="row">
                            <div class="text-subtitle1 text-bold">
                                {{ editField['fieldName'] }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-10">
                                <selector-input-element label="Extra Small Screen Width" :options="fieldWidthOptions" :value="editField['xs-col-width']" @update:value="(value) => editField['xs-col-width'] = value"></selector-input-element>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-10">
                                <selector-input-element :clearable="true" label="Small Screen Width" :options="fieldWidthOptions" :value="editField['sm-col-width']" @update:value="(value) => editField['sm-col-width'] = value"></selector-input-element>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-10">
                                <selector-input-element :clearable="true" label="Medium Screen Width" :options="fieldWidthOptions" :value="editField['md-col-width']" @update:value="(value) => editField['md-col-width'] = value"></selector-input-element>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-10">
                                <selector-input-element :clearable="true" label="Large Screen Width" :options="fieldWidthOptions" :value="editField['lg-col-width']" @update:value="(value) => editField['lg-col-width'] = value"></selector-input-element>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-10">
                                <selector-input-element :clearable="true" label="Extra Large Screen Width" :options="fieldWidthOptions" :value="editField['xl-col-width']" @update:value="(value) => editField['xl-col-width'] = value"></selector-input-element>
                            </div>
                        </div>
                    </div>
                </q-card>
            </q-dialog>
        </template>
        <template v-if="showAvailableFieldsPopup">
            <q-dialog class="z-top" v-model="showAvailableFieldsPopup" seamless position="right">
                <q-card class="side-popup-right overflow-hidden">
                    <div class="row justify-start items-start map-sm-popup">
                        <div>
                            <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="showAvailableFieldsPopup = false" aria-label="Close window" tabindex="0"></q-btn>
                        </div>
                    </div>
                    <div ref="availableFieldsRef" class="fit">
                        <div :style="availableFieldsStyle" class="q-pa-sm overflow-auto">
                            <draggable v-model="availableFields" v-bind="dragOptions" class="column q-gutter-sm mof-field-row" group="configArrItem" :move="validateDragDrop">
                                <template #item="{ element: field }">
                                    <div class="mof-field-container" :class="getFieldClassStr(field)">
                                        <q-card flat bordered class="cursor-move black-border">
                                            <q-card-section class="q-pa-xs row q-col-gutter-sm">
                                                <div class="text-subtitle1 text-bold">{{ field['fieldName'] }}</div>
                                                <div>
                                                    <q-btn color="grey-4" text-color="black" size="sm" @click="openFieldEditPopup(field);" icon="fas fa-edit" dense aria-label="Edit field size" tabindex="0">
                                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                            Edit field size
                                                        </q-tooltip>
                                                    </q-btn>
                                                </div>
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </div>
                </q-card>
            </q-dialog>
        </template>
        <template v-if="showLiveViewPopup">
            <q-dialog class="z-top" v-model="showLiveViewPopup" persistent>
                <q-card class="lg-popup overflow-hidden">
                    <div class="row justify-end items-start map-sm-popup">
                        <div>
                            <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="showLiveViewPopup = false" aria-label="Close window" tabindex="0"></q-btn>
                        </div>
                    </div>
                    <div ref="liveViewContentRef" class="fit">
                        <div :style="liveViewContentStyle" class="overflow-auto">
                            <div>
                                <div class="q-pa-md column q-col-gutter-sm">
                                    <div v-if="editDataArr.length > 0" class="q-mt-sm column q-col-gutter-sm">
                                        <template v-for="layoutElement in editDataArr">
                                            <template v-if="layoutElement.type === 'dataFieldRow'">
                                                <mof-data-field-row :editor="true" :configured-data="liveViewData" :configured-data-fields="editDataFields" :fields="layoutElement.fields" @update:configured-edit-data="updateLiveViewData"></mof-data-field-row>
                                            </template>
                                            <template v-else-if="layoutElement.type === 'dataFieldRowGroup'">
                                                <mof-data-field-row-group :editor="true" :configured-data="liveViewData" :configured-data-fields="editDataFields" :label="layoutElement.label" :rows="layoutElement.rows" :expansion="layoutElement.expansion" @update:configured-edit-data="updateLiveViewData"></mof-data-field-row-group>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </q-card>
            </q-dialog>
        </template>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'draggable': draggable,
        'mof-data-field-row': mofDataFieldRow,
        'mof-data-field-row-group': mofDataFieldRowGroup,
        'mof-field-layout-field-row-element': mofFieldLayoutFieldRowElement,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { showNotification } = useCore();
        const collectionStore = useCollectionStore();

        const availableFields = Vue.computed(() => {
            const returnArr = [];
            Object.keys(editDataFields.value).forEach((field) => {
                if(!usedFields.value.includes(field)){
                    const newFieldObj = Object.assign({}, blankField.value);
                    newFieldObj['fieldName'] = field;
                    returnArr.push(newFieldObj);
                }
            });
            returnArr.sort((a, b) => {
                return a['fieldName'].localeCompare(b['fieldName']);
            });
            return returnArr;
        });
        const availableFieldsRef = Vue.ref(null);
        const availableFieldsStyle = Vue.ref(null);
        const blankField = Vue.ref({
            'fieldName': null,
            'xs-col-width': 12,
            'sm-col-width': 12,
            'md-col-width': 6,
            'lg-col-width': 4,
            'xl-col-width': 4
        });
        const blankFieldRow = Vue.ref({
            type: 'dataFieldRow',
            fields: []
        });
        const blankFieldRowGroup = Vue.ref({
            type: 'dataFieldRowGroup',
            expansion: false,
            label: null,
            rows: []
        });
        const dragOptions = Vue.computed(() => {
            return {
                animation: 200,
                ghostClass: 'ghost'
            };
        });
        const editDataArr = Vue.ref([]);
        const editDataFields = Vue.computed(() => {
            if(props.fieldType === 'occurrence'){
                return occurrenceDataFields.value;
            }
            else if(props.fieldType === 'event'){
                return eventDataFields.value;
            }
            else{
                return locationDataFields.value;
            }
        });
        const editField = Vue.ref(null);
        const editFieldRowGroup = Vue.ref(null);
        const editsExist = Vue.computed(() => {
            return JSON.stringify(editDataArr.value.slice()) !== uneditedJson.value;
        });
        const expandedGroupArr = Vue.ref([]);
        const eventDataFields = Vue.computed(() => collectionStore.getEventMofDataFields);
        const eventDataFieldsLayoutData = Vue.computed(() => collectionStore.getEventMofDataFieldsLayoutData);
        const eventDataLabel = Vue.computed(() => collectionStore.getEventMofDataLabel);
        const fieldWidthOptions = Vue.computed(() => {
            const returnArr = [];
            for(let i = 1; i <= 12; i++){
                returnArr.push({value: i, label: i.toString()});
            }
            return returnArr;
        });
        const liveViewContentRef = Vue.ref(null);
        const liveViewContentStyle = Vue.ref(null);
        const liveViewData = Vue.ref({});
        const locationDataFields = Vue.computed(() => collectionStore.getLocationMofDataFields);
        const locationDataFieldsLayoutData = Vue.computed(() => collectionStore.getLocationMofDataFieldsLayoutData);
        const locationDataLabel = Vue.computed(() => collectionStore.getLocationMofDataLabel);
        const occurrenceDataFields = Vue.computed(() => collectionStore.getOccurrenceMofDataFields);
        const occurrenceDataFieldsLayoutData = Vue.computed(() => collectionStore.getOccurrenceMofDataFieldsLayoutData);
        const occurrenceDataLabel = Vue.computed(() => collectionStore.getOccurrenceMofDataLabel);
        const propsRefs = Vue.toRefs(props);
        const showAvailableFieldsPopup = Vue.ref(false);
        const showFieldEditorPopup = Vue.ref(false);
        const showFieldRowGroupEditorPopup = Vue.ref(false);
        const showLiveViewPopup = Vue.ref(false);
        const uneditedJson = Vue.ref(null);
        const usedFields = Vue.computed(() => {
            const returnArr = [];
            editDataArr.value.forEach((layoutObj) => {
                if(layoutObj['type'] === 'dataFieldRow'){
                    layoutObj['fields'].forEach((field) => {
                        returnArr.push(field['fieldName']);
                    });
                }
                else if(layoutObj['type'] === 'dataFieldRowGroup'){
                    layoutObj['rows'].forEach((row) => {
                        row['fields'].forEach((field) => {
                            returnArr.push(field['fieldName']);
                        });
                    });
                }
            });
            return returnArr;
        });
        
        Vue.watch(propsRefs.fieldType, () => {
            setEditData();
        });

        Vue.watch(availableFieldsRef, () => {
            setAvailableFieldsStyle();
        });

        Vue.watch(liveViewContentRef, () => {
            setLiveViewContentStyle();
        });

        function addFieldRow() {
            const newFieldRow = Object.assign({}, blankFieldRow.value);
            editDataArr.value.push(newFieldRow);
        }

        function addFieldRowGroup() {
            const newFieldRowGroup = Object.assign({}, blankFieldRowGroup.value);
            editDataArr.value.push(newFieldRowGroup);
            openFieldRowGroupEditPopup(newFieldRowGroup);
        }

        function deleteField(data) {
            const index = data.row.indexOf(data.field);
            data.row.splice(index, 1);
        }

        function deleteFieldRow(row) {
            const index = editDataArr.value.indexOf(row);
            editDataArr.value.splice(index, 1);
        }

        function expandLayerGroup(id) {
            expandedGroupArr.value.push(id.toString());
        }

        function getClassName(size, value) {
            let className;
            if(size === 'xs'){
                className = 'col-' + value.toString();
            }
            else{
                className = 'col-' + size + '-' + value.toString();
            }
            return className;
        }

        function getFieldClassStr(field) {
            const strArr = [];
            if(field.hasOwnProperty('xs-col-width') && field['xs-col-width']){
                strArr.push(getClassName('xs', field['xs-col-width']));
            }
            else{
                strArr.push(getClassName('xs', 12));
            }
            if(field.hasOwnProperty('sm-col-width') && field['sm-col-width']){
                strArr.push(getClassName('sm', field['sm-col-width']));
            }
            if(field.hasOwnProperty('md-col-width') && field['md-col-width']){
                strArr.push(getClassName('md', field['md-col-width']));
            }
            if(field.hasOwnProperty('lg-col-width') && field['lg-col-width']){
                strArr.push(getClassName('lg', field['lg-col-width']));
            }
            if(field.hasOwnProperty('xl-col-width') && field['xl-col-width']){
                strArr.push(getClassName('xl', field['xl-col-width']));
            }
            return strArr.join(' ');
        }

        function hideRowGroup(group) {
            const index = expandedGroupArr.value.indexOf(group);
            expandedGroupArr.value.splice(index, 1);
        }

        function openFieldEditPopup(field) {
            editField.value = field;
            showFieldEditorPopup.value = true;
        }

        function openFieldRowGroupEditPopup(fieldRowGroup) {
            editFieldRowGroup.value = fieldRowGroup;
            showFieldRowGroupEditorPopup.value = true;
        }

        function openLiveViewPopup() {
            liveViewData.value = Object.assign({}, {});
            Object.keys(editDataFields.value).forEach((field) => {
                liveViewData.value[field] = null;
            });
            showLiveViewPopup.value = true;
        }

        function processWindowResize() {
            setAvailableFieldsStyle();
            setLiveViewContentStyle();
        }

        function setAvailableFieldsStyle() {
            availableFieldsStyle.value = null;
            if(availableFieldsRef.value){
                availableFieldsStyle.value = 'height: ' + (availableFieldsRef.value.clientHeight - 30) + 'px;width: ' + availableFieldsRef.value.clientWidth + 'px;';
            }
        }

        function setEditData() {
            if(props.fieldType === 'occurrence'){
                editDataArr.value = occurrenceDataFieldsLayoutData.value.slice();
                uneditedJson.value = JSON.stringify(occurrenceDataFieldsLayoutData.value.slice());
            }
            else if(props.fieldType === 'event'){
                editDataArr.value = eventDataFieldsLayoutData.value.slice();
                uneditedJson.value = JSON.stringify(eventDataFieldsLayoutData.value.slice());
            }
            else{
                editDataArr.value = locationDataFieldsLayoutData.value.slice();
                uneditedJson.value = JSON.stringify(locationDataFieldsLayoutData.value.slice());
            }
        }

        function setLiveViewContentStyle() {
            liveViewContentStyle.value = null;
            if(liveViewContentRef.value){
                liveViewContentStyle.value = 'height: ' + (liveViewContentRef.value.clientHeight - 30) + 'px;width: ' + liveViewContentRef.value.clientWidth + 'px;';
            }
        }

        function showRowGroup(group) {
            expandedGroupArr.value.push(group);
        }

        function updateLiveViewData(data) {
            liveViewData.value[data.key] = data.value;
        }

        function validateDragDrop(evt){
            let valid = false;
            if(evt.dragged.classList.contains('mof-field-container') && evt.to.classList.contains('mof-field-row')){
                valid = true;
            }
            else if(evt.dragged.classList.contains('mof-field-row-container') && (evt.to.classList.contains('mof-field-container') || evt.to.classList.contains('mof-field-row-group'))){
                valid = true;
            }
            else if(evt.dragged.classList.contains('mof-field-row-group-container') && evt.to.classList.contains('mof-field-container')){
                valid = true;
            }
            return valid;
        }

        Vue.onMounted(() => {
            setEditData();
            window.addEventListener('resize', processWindowResize);
        });

        return {
            availableFields,
            availableFieldsRef,
            availableFieldsStyle,
            dragOptions,
            editDataArr,
            editDataFields,
            editField,
            editFieldRowGroup,
            editsExist,
            expandedGroupArr,
            fieldWidthOptions,
            liveViewContentRef,
            liveViewContentStyle,
            liveViewData,
            showAvailableFieldsPopup,
            showFieldEditorPopup,
            showFieldRowGroupEditorPopup,
            showLiveViewPopup,
            addFieldRow,
            addFieldRowGroup,
            deleteField,
            deleteFieldRow,
            expandLayerGroup,
            getFieldClassStr,
            hideRowGroup,
            openFieldEditPopup,
            openFieldRowGroupEditPopup,
            openLiveViewPopup,
            showRowGroup,
            updateLiveViewData,
            validateDragDrop
        }
    }
};
