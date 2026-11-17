const collectingEventFieldModule = {
    props: {
        autoSearch: {
            type: Boolean,
            default: false
        },
        data: {
            type: Object,
            default: null
        },
        disabled: {
            type: Boolean,
            default: false
        },
        eventMode: {
            type: Boolean,
            default: false
        },
        fieldDefinitions: {
            type: Object,
            default: null
        }
    },
    template: `
        <div v-if="!editorHideFields.includes('recordedby') || !editorHideFields.includes('recordnumber')" class="row justify-between q-gutter-sm">
            <div v-if="!editorHideFields.includes('recordedby')" class="col-12 col-sm-6 col-md-grow">
                <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['recordedby']" field="recordedby" :label="fieldDefinitions['recordedby']['label']" :maxlength="fieldDefinitions['recordedby']['length']" :value="data.recordedby" @update:value="(value) => updateData('recordedby', value)"></text-field-input-element>
            </div>
            <div v-if="!editorHideFields.includes('recordnumber')" class="col-12 col-sm-6 col-md-3">
                <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['recordnumber']" :label="fieldDefinitions['recordnumber']['label']" :maxlength="fieldDefinitions['recordnumber']['length']" :value="data.recordnumber" @update:value="(value) => updateData('recordnumber', value)"></text-field-input-element>
            </div>
        </div>
        <div v-if="!editorHideFields.includes('associatedcollectors')" class="row justify-between q-col-gutter-sm">
            <div class="col-grow">
                <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['associatedcollectors']" :label="fieldDefinitions['associatedcollectors']['label']" field="associatedcollectors" :maxlength="fieldDefinitions['associatedcollectors']['length']" :value="data.associatedcollectors" @update:value="(value) => updateData('associatedcollectors', value)"></text-field-input-element>
            </div>
        </div>
        <template v-if="imageCount === 0">
            <div class="row justify-between q-col-gutter-sm">
                <div v-if="!editorHideFields.includes('eventdate')" class="col-12 col-sm-6 col-md-4">
                    <date-input-element :disabled="disabled" :definition="fieldDefinitions['eventdate']" :label="fieldDefinitions['eventdate']['label']" :value="data.eventdate" @update:value="updateDateData"></date-input-element>
                </div>
                <div v-if="!editorHideFields.includes('eventtime')" class="col-12 col-sm-6 col-md-3">
                    <time-input-element :disabled="disabled" :definition="fieldDefinitions['eventtime']" :label="fieldDefinitions['eventtime']['label']" :value="data.eventtime" @update:value="(value) => updateData('eventtime', value)"></time-input-element>
                </div>
                <div v-if="!editorHideFields.includes('verbatimeventdate')" class="col-10 col-sm-6 col-md-3">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['verbatimeventdate']" :label="fieldDefinitions['verbatimeventdate']['label']" field="verbatimeventdate" :maxlength="fieldDefinitions['verbatimeventdate']['length']" :value="data.verbatimeventdate" @update:value="(value) => updateData('verbatimeventdate', value)"></text-field-input-element>
                </div>
                <div class="col-2 row justify-end q-col-gutter-sm self-center">
                    <div v-if="eventMode">
                        <template v-if="showLocationForm">
                            <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showLocationForm = false" icon="fas fa-globe" icon-right="fas fa-minus" dense aria-label="Hide location fields" tabindex="0"></q-btn>
                        </template>
                        <template v-else>
                            <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showLocationForm = true" icon="fas fa-globe" icon-right="fas fa-plus" dense aria-label="Show location fields" tabindex="0"></q-btn>
                        </template>
                    </div>
                    <div>
                        <template v-if="showExtendedForm">
                            <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = false" icon="fas fa-minus" dense aria-label="Hide additional fields" tabindex="0"></q-btn>
                        </template>
                        <template v-else>
                            <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = true" icon="fas fa-plus" dense aria-label="Show additional fields" tabindex="0"></q-btn>
                        </template>
                    </div>
                </div>
            </div>
        </template>
        <template v-else>
            <div v-if="!editorHideFields.includes('eventdate') || !editorHideFields.includes('eventtime')" class="row justify-between q-col-gutter-sm">
                <div v-if="!editorHideFields.includes('eventdate')" class="col-12 col-sm-6">
                    <date-input-element :disabled="disabled" :definition="fieldDefinitions['eventdate']" :label="fieldDefinitions['eventdate']['label']" :value="data.eventdate" @update:value="updateDateData"></date-input-element>
                </div>
                <div v-if="!editorHideFields.includes('eventtime')" class="col-12 col-sm-6">
                    <time-input-element :disabled="disabled" :definition="fieldDefinitions['eventtime']" :label="fieldDefinitions['eventtime']['label']" :value="data.eventtime" @update:value="(value) => updateData('eventtime', value)"></time-input-element>
                </div>
            </div>
            <div class="row justify-between q-col-gutter-sm">
                <div v-if="!editorHideFields.includes('verbatimeventdate')" class="col-10 col-sm-7">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['verbatimeventdate']" :label="fieldDefinitions['verbatimeventdate']['label']" field="verbatimeventdate" :maxlength="fieldDefinitions['verbatimeventdate']['length']" :value="data.verbatimeventdate" @update:value="(value) => updateData('verbatimeventdate', value)"></text-field-input-element>
                </div>
                <div class="col-5 row justify-end q-col-gutter-sm self-center">
                    <div v-if="eventMode">
                        <template v-if="showLocationForm">
                            <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showLocationForm = false" icon="fas fa-globe" icon-right="fas fa-minus" dense aria-label="Hide location fields" tabindex="0"></q-btn>
                        </template>
                        <template v-else>
                            <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showLocationForm = true" icon="fas fa-globe" icon-right="fas fa-plus" dense aria-label="Show location fields" tabindex="0"></q-btn>
                        </template>
                    </div>
                    <div>
                        <template v-if="showExtendedForm">
                            <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = false" icon="fas fa-minus" dense aria-label="Hide additional fields" tabindex="0"></q-btn>
                        </template>
                        <template v-else>
                            <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = true" icon="fas fa-plus" dense aria-label="Show additional fields" tabindex="0"></q-btn>
                        </template>
                    </div>
                </div>
            </div>
        </template>
        <template v-if="showLocationForm">
            <location-field-module :disabled="disabled" :event-mode="true" :data="data" :field-definitions="fieldDefinitions" @update:location-data="(data) => updateData(data.key, data.value)"></location-field-module>
        </template>
        <template v-if="showExtendedForm">
            <div v-if="!editorHideFields.includes('minimumdepthinmeters') || !editorHideFields.includes('maximumdepthinmeters') || !editorHideFields.includes('verbatimdepth')" class="row justify-between q-col-gutter-sm">
                <div v-if="!editorHideFields.includes('minimumdepthinmeters') || !editorHideFields.includes('maximumdepthinmeters')" class="col-12 col-sm-6 row justify-start q-col-gutter-md">
                    <div v-if="!editorHideFields.includes('minimumdepthinmeters')" class="col-12 col-sm-6">
                        <text-field-input-element :disabled="disabled" data-type="number" :definition="fieldDefinitions['minimumdepthinmeters']" :label="fieldDefinitions['minimumdepthinmeters']['label']" :maxlength="fieldDefinitions['minimumdepthinmeters']['length']" :value="data.minimumdepthinmeters" @update:value="(value) => updateData('minimumdepthinmeters', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('maximumdepthinmeters')" class="col-12 col-sm-6">
                        <text-field-input-element :disabled="disabled" data-type="number" :definition="fieldDefinitions['maximumdepthinmeters']" :label="fieldDefinitions['maximumdepthinmeters']['label']" :maxlength="fieldDefinitions['maximumdepthinmeters']['length']" :value="data.maximumdepthinmeters" @update:value="(value) => updateData('maximumdepthinmeters', value)"></text-field-input-element>
                    </div>
                </div>
                <div v-if="!editorHideFields.includes('verbatimdepth')" class="col-12 col-sm-6">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['verbatimdepth']" :label="fieldDefinitions['verbatimdepth']['label']" :maxlength="fieldDefinitions['verbatimdepth']['length']" :value="data.verbatimdepth" @update:value="(value) => updateData('verbatimdepth', value)"></text-field-input-element>
                </div>
            </div>
            <div v-if="occurrenceEntryFormat === 'replicate' && !editorHideFields.includes('habitat')" class="row">
                <div class="col-grow">
                    <text-field-input-element :disabled="disabled" data-type="textarea" :definition="fieldDefinitions['habitat']" :label="fieldDefinitions['habitat']['label']" field="habitat" :value="data.habitat" @update:value="(value) => updateData('habitat', value)"></text-field-input-element>
                </div>
            </div>
            <div v-if="!editorHideFields.includes('fieldnumber') || !editorHideFields.includes('samplingprotocol') || !editorHideFields.includes('samplingeffort')" class="row justify-between q-col-gutter-sm">
                <div v-if="!editorHideFields.includes('fieldnumber')" class="col-12 col-sm-6 col-md-4">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['fieldnumber']" :label="fieldDefinitions['fieldnumber']['label']" :maxlength="fieldDefinitions['fieldnumber']['length']" :value="data.fieldnumber" @update:value="(value) => updateData('fieldnumber', value)"></text-field-input-element>
                </div>
                <div v-if="!editorHideFields.includes('samplingprotocol')" class="col-12 col-sm-6 col-md-4">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['samplingprotocol']" :label="fieldDefinitions['samplingprotocol']['label']" field="samplingprotocol" :maxlength="fieldDefinitions['samplingprotocol']['length']" :value="data.samplingprotocol" @update:value="(value) => updateData('samplingprotocol', value)"></text-field-input-element>
                </div>
                <div v-if="!editorHideFields.includes('samplingeffort')" class="col-12 col-sm-6 col-md-4">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['samplingeffort']" :label="fieldDefinitions['samplingeffort']['label']" field="samplingeffort" :maxlength="fieldDefinitions['samplingeffort']['length']" :value="data.samplingeffort" @update:value="(value) => updateData('samplingeffort', value)"></text-field-input-element>
                </div>
            </div>
            <div v-if="!editorHideFields.includes('fieldnotes')" class="row">
                <div class="col-grow">
                    <text-field-input-element :disabled="disabled" data-type="textarea" :definition="fieldDefinitions['fieldnotes']" :label="fieldDefinitions['fieldnotes']['label']" field="fieldnotes" :value="data.fieldnotes" @update:value="(value) => updateData('fieldnotes', value)"></text-field-input-element>
                </div>
            </div>
            <div v-if="occurrenceEntryFormat === 'replicate' && !editorHideFields.includes('labelproject')" class="row">
                <div class="col-grow">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['labelproject']" :label="fieldDefinitions['labelproject']['label']" field="labelproject" :maxlength="fieldDefinitions['labelproject']['length']" :value="data.labelproject" @update:value="(value) => updateData('labelproject', value)"></text-field-input-element>
                </div>
            </div>
            <template v-if="imageCount === 0">
                <div v-if="!editorHideFields.includes('year') || !editorHideFields.includes('month') || !editorHideFields.includes('day') || !editorHideFields.includes('startdayofyear') || !editorHideFields.includes('enddayofyear')" class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('year') || !editorHideFields.includes('month') || !editorHideFields.includes('day')" class="col-6 row justify-start q-col-gutter-sm">
                        <div v-if="!editorHideFields.includes('year')" class="col-12 col-sm-4">
                            <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['year']" :label="fieldDefinitions['year']['label']" :maxlength="fieldDefinitions['year']['length']" :value="data.year" @update:value="(value) => updateData('year', value)"></text-field-input-element>
                        </div>
                        <div v-if="!editorHideFields.includes('month')" class="col-12 col-sm-4">
                            <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['month']" :label="fieldDefinitions['month']['label']" :maxlength="fieldDefinitions['month']['length']" :value="data.month" @update:value="(value) => updateData('month', value)"></text-field-input-element>
                        </div>
                        <div v-if="!editorHideFields.includes('day')" class="col-12 col-sm-4">
                            <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['day']" :label="fieldDefinitions['day']['label']" :maxlength="fieldDefinitions['day']['length']" :value="data.day" @update:value="(value) => updateData('day', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div v-if="!editorHideFields.includes('startdayofyear') || !editorHideFields.includes('enddayofyear')" class="col-6 row justify-end q-col-gutter-sm">
                        <div v-if="!editorHideFields.includes('startdayofyear')" class="col-12 col-sm-5">
                            <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['startdayofyear']" :label="fieldDefinitions['startdayofyear']['label']" :maxlength="fieldDefinitions['startdayofyear']['length']" :value="data.startdayofyear" @update:value="(value) => updateData('startdayofyear', value)"></text-field-input-element>
                        </div>
                        <div v-if="!editorHideFields.includes('enddayofyear')" class="col-12 col-sm-5">
                            <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['enddayofyear']" :label="fieldDefinitions['enddayofyear']['label']" :maxlength="fieldDefinitions['enddayofyear']['length']" :value="data.enddayofyear" @update:value="(value) => updateData('enddayofyear', value)"></text-field-input-element>
                        </div>
                    </div>
                </div>
            </template>
            <template v-else>
                <div v-if="!editorHideFields.includes('year') || !editorHideFields.includes('month') || !editorHideFields.includes('day')" class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('year')" class="col-12 col-sm-4">
                        <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['year']" :label="fieldDefinitions['year']['label']" :maxlength="fieldDefinitions['year']['length']" :value="data.year" @update:value="(value) => updateData('year', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('month')" class="col-12 col-sm-4">
                        <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['month']" :label="fieldDefinitions['month']['label']" :maxlength="fieldDefinitions['month']['length']" :value="data.month" @update:value="(value) => updateData('month', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('day')" class="col-12 col-sm-4">
                        <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['day']" :label="fieldDefinitions['day']['label']" :maxlength="fieldDefinitions['day']['length']" :value="data.day" @update:value="(value) => updateData('day', value)"></text-field-input-element>
                    </div>
                </div>
                <div v-if="!editorHideFields.includes('startdayofyear') || !editorHideFields.includes('enddayofyear')" class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('startdayofyear')" class="col-12 col-sm-5">
                        <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['startdayofyear']" :label="fieldDefinitions['startdayofyear']['label']" :maxlength="fieldDefinitions['startdayofyear']['length']" :value="data.startdayofyear" @update:value="(value) => updateData('startdayofyear', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('enddayofyear')" class="col-12 col-sm-5">
                        <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['enddayofyear']" :label="fieldDefinitions['enddayofyear']['label']" :maxlength="fieldDefinitions['enddayofyear']['length']" :value="data.enddayofyear" @update:value="(value) => updateData('enddayofyear', value)"></text-field-input-element>
                    </div>
                </div>
            </template>
        </template>
    `,
    components: {
        'date-input-element': dateInputElement,
        'location-field-module': locationFieldModule,
        'text-field-input-element': textFieldInputElement,
        'time-input-element': timeInputElement
    },
    setup(props, context) {
        const occurrenceStore = useOccurrenceStore();

        const editorHideFields = Vue.computed(() => occurrenceStore.getEditorHideFields);
        const imageCount = Vue.computed(() => occurrenceStore.getImageCount);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
        const propsRefs = Vue.toRefs(props);
        const showExtendedForm = Vue.ref(false);
        const showLocationForm = Vue.ref(false);

        Vue.watch(propsRefs.data, () => {
            if(!props.disabled || imageCount.value > 0){
                setExtendedView();
            }
        });

        function processCollectingEventSearch(silent = true) {
            context.emit('process-event-search', silent);
        }

        function setExtendedView() {
            if(props.data.fieldnotes ||
                props.data.fieldnumber ||
                props.data.habitat ||
                props.data.minimumdepthinmeters ||
                props.data.maximumdepthinmeters ||
                props.data.verbatimdepth ||
                props.data.samplingprotocol ||
                props.data.samplingeffort ||
                props.data.labelproject ||
                (!props.eventMode && imageCount.value > 0)
            ){
                showExtendedForm.value = true;
            }
        }

        function updateData(key, value) {
            context.emit('update:collecting-event-data', {key: key, value: value});
        }

        function updateDateData(dateData) {
            context.emit('update:collecting-event-data', {key: 'eventdate', value: (dateData ? dateData['date'] : null)});
            context.emit('update:collecting-event-data', {key: 'year', value: (dateData ? dateData['year'] : null)});
            context.emit('update:collecting-event-data', {key: 'month', value: (dateData ? dateData['month'] : null)});
            context.emit('update:collecting-event-data', {key: 'day', value: (dateData ? dateData['day'] : null)});
            context.emit('update:collecting-event-data', {key: 'startdayofyear', value: (dateData ? dateData['startdayofyear'] : null)});
            context.emit('update:collecting-event-data', {key: 'enddayofyear', value: (dateData ? dateData['enddayofyear'] : null)});
            if(!props.eventMode && props.autoSearch){
                processCollectingEventSearch();
            }
        }

        Vue.onMounted(() => {
            if(!props.disabled || imageCount.value > 0){
                setExtendedView();
            }
        });

        return {
            editorHideFields,
            imageCount,
            occurrenceEntryFormat,
            showExtendedForm,
            showLocationForm,
            updateData,
            updateDateData
        }
    }
};
