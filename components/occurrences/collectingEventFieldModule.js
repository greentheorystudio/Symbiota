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
        fields: {
            type: Object,
            default: null
        },
        fieldDefinitions: {
            type: Object,
            default: null
        }
    },
    template: `
        <div v-if="!editorHideFields.includes('recordedby') || !editorHideFields.includes('recordnumber')" class="row justify-between q-gutter-sm">
            <div v-if="!editorHideFields.includes('recordedby')" class="col-12 col-sm-6 col-md-grow">
                <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['recordedby']" label="Collector/Observer" :maxlength="fields['recordedby'] ? fields['recordedby']['length'] : 0" :value="data.recordedby" @update:value="(value) => updateData('recordedby', value)"></text-field-input-element>
            </div>
            <div v-if="!editorHideFields.includes('recordnumber')" class="col-12 col-sm-6 col-md-3">
                <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['recordnumber']" label="Number" :maxlength="fields['recordnumber'] ? fields['recordnumber']['length'] : 0" :value="data.recordnumber" @update:value="(value) => updateData('recordnumber', value)"></text-field-input-element>
            </div>
        </div>
        <div v-if="!editorHideFields.includes('associatedcollectors')" class="row justify-between q-col-gutter-sm">
            <div class="col-grow">
                <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['associatedcollectors']" label="Associated Collectors" :maxlength="fields['associatedcollectors'] ? fields['associatedcollectors']['length'] : 0" :value="data.associatedcollectors" @update:value="(value) => updateData('associatedcollectors', value)"></text-field-input-element>
            </div>
        </div>
        <template v-if="imageCount === 0">
            <div class="row justify-between q-col-gutter-sm">
                <div v-if="!editorHideFields.includes('eventdate')" class="col-12 col-sm-6 col-md-4">
                    <date-input-element :disabled="disabled" :definition="fieldDefinitions['eventdate']" label="Date" :value="data.eventdate" @update:value="updateDateData"></date-input-element>
                </div>
                <div v-if="!editorHideFields.includes('eventtime')" class="col-12 col-sm-6 col-md-3">
                    <time-input-element :disabled="disabled" :definition="fieldDefinitions['eventtime']" label="Time" :value="data.eventtime" @update:value="(value) => updateData('eventtime', value)"></time-input-element>
                </div>
                <div v-if="!editorHideFields.includes('verbatimeventdate')" class="col-10 col-sm-6 col-md-3">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['verbatimeventdate']" label="Verbatim Date" :maxlength="fields['verbatimeventdate'] ? fields['verbatimeventdate']['length'] : 0" :value="data.verbatimeventdate" @update:value="(value) => updateData('verbatimeventdate', value)"></text-field-input-element>
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
                    <date-input-element :disabled="disabled" :definition="fieldDefinitions['eventdate']" label="Date" :value="data.eventdate" @update:value="updateDateData"></date-input-element>
                </div>
                <div v-if="!editorHideFields.includes('eventtime')" class="col-12 col-sm-6">
                    <time-input-element :disabled="disabled" :definition="fieldDefinitions['eventtime']" label="Time" :value="data.eventtime" @update:value="(value) => updateData('eventtime', value)"></time-input-element>
                </div>
            </div>
            <div class="row justify-between q-col-gutter-sm">
                <div v-if="!editorHideFields.includes('verbatimeventdate')" class="col-10 col-sm-7">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['verbatimeventdate']" label="Verbatim Date" :maxlength="fields['verbatimeventdate'] ? fields['verbatimeventdate']['length'] : 0" :value="data.verbatimeventdate" @update:value="(value) => updateData('verbatimeventdate', value)"></text-field-input-element>
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
            <location-field-module :disabled="disabled" :event-mode="true" :data="data" :fields="fields" :field-definitions="fieldDefinitions" @update:location-data="(data) => updateData(data.key, data.value)"></location-field-module>
        </template>
        <template v-if="showExtendedForm">
            <div v-if="!editorHideFields.includes('minimumdepthinmeters') || !editorHideFields.includes('maximumdepthinmeters') || !editorHideFields.includes('verbatimdepth')" class="row justify-between q-col-gutter-sm">
                <div v-if="!editorHideFields.includes('minimumdepthinmeters') || !editorHideFields.includes('maximumdepthinmeters')" class="col-12 col-sm-6 row justify-start q-col-gutter-md">
                    <div v-if="!editorHideFields.includes('minimumdepthinmeters')" class="col-12 col-sm-6">
                        <text-field-input-element :disabled="disabled" data-type="number" :definition="fieldDefinitions['minimumdepthinmeters']" label="Minimum Depth (m)" :maxlength="fields['minimumdepthinmeters'] ? fields['minimumdepthinmeters']['length'] : 0" :value="data.minimumdepthinmeters" @update:value="(value) => updateData('minimumdepthinmeters', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('maximumdepthinmeters')" class="col-12 col-sm-6">
                        <text-field-input-element :disabled="disabled" data-type="number" :definition="fieldDefinitions['maximumdepthinmeters']" label="Maximum Depth (m)" :maxlength="fields['maximumdepthinmeters'] ? fields['maximumdepthinmeters']['length'] : 0" :value="data.maximumdepthinmeters" @update:value="(value) => updateData('maximumdepthinmeters', value)"></text-field-input-element>
                    </div>
                </div>
                <div v-if="!editorHideFields.includes('verbatimdepth')" class="col-12 col-sm-6">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['verbatimdepth']" label="Verbatim Depth" :maxlength="fields['verbatimdepth'] ? fields['verbatimdepth']['length'] : 0" :value="data.verbatimdepth" @update:value="(value) => updateData('verbatimdepth', value)"></text-field-input-element>
                </div>
            </div>
            <div v-if="!editorHideFields.includes('fieldnumber') || !editorHideFields.includes('samplingprotocol') || !editorHideFields.includes('samplingeffort')" class="row justify-between q-col-gutter-sm">
                <div v-if="!editorHideFields.includes('fieldnumber')" class="col-12 col-sm-6 col-md-4">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['fieldnumber']" label="Field Number" :maxlength="fields['fieldnumber'] ? fields['fieldnumber']['length'] : 0" :value="data.fieldnumber" @update:value="(value) => updateData('fieldnumber', value)"></text-field-input-element>
                </div>
                <div v-if="!editorHideFields.includes('samplingprotocol')" class="col-12 col-sm-6 col-md-4">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['samplingprotocol']" label="Sampling Protocol" :maxlength="fields['samplingprotocol'] ? fields['samplingprotocol']['length'] : 0" :value="data.samplingprotocol" @update:value="(value) => updateData('samplingprotocol', value)"></text-field-input-element>
                </div>
                <div v-if="!editorHideFields.includes('samplingeffort')" class="col-12 col-sm-6 col-md-4">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['samplingeffort']" label="Sampling Effort" :maxlength="fields['samplingeffort'] ? fields['samplingeffort']['length'] : 0" :value="data.samplingeffort" @update:value="(value) => updateData('samplingeffort', value)"></text-field-input-element>
                </div>
            </div>
            <div v-if="!editorHideFields.includes('fieldnotes')" class="row">
                <div class="col-grow">
                    <text-field-input-element :disabled="disabled" data-type="textarea" :definition="fieldDefinitions['fieldnotes']" label="Field Notes" :value="data.fieldnotes" @update:value="(value) => updateData('fieldnotes', value)"></text-field-input-element>
                </div>
            </div>
            <template v-if="imageCount === 0">
                <div v-if="!editorHideFields.includes('year') || !editorHideFields.includes('month') || !editorHideFields.includes('day') || !editorHideFields.includes('startdayofyear') || !editorHideFields.includes('enddayofyear')" class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('year') || !editorHideFields.includes('month') || !editorHideFields.includes('day')" class="col-6 row justify-start q-col-gutter-sm">
                        <div v-if="!editorHideFields.includes('year')" class="col-12 col-sm-4">
                            <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['year']" label="Year" :maxlength="fields['year'] ? fields['year']['length'] : 0" :value="data.year" @update:value="(value) => updateData('year', value)"></text-field-input-element>
                        </div>
                        <div v-if="!editorHideFields.includes('month')" class="col-12 col-sm-4">
                            <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['month']" label="Month" :maxlength="fields['month'] ? fields['month']['length'] : 0" :value="data.month" @update:value="(value) => updateData('month', value)"></text-field-input-element>
                        </div>
                        <div v-if="!editorHideFields.includes('day')" class="col-12 col-sm-4">
                            <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['day']" label="Day" :maxlength="fields['day'] ? fields['day']['length'] : 0" :value="data.day" @update:value="(value) => updateData('day', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div v-if="!editorHideFields.includes('startdayofyear') || !editorHideFields.includes('enddayofyear')" class="col-6 row justify-end q-col-gutter-sm">
                        <div v-if="!editorHideFields.includes('startdayofyear')" class="col-12 col-sm-5">
                            <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['startdayofyear']" label="Start Day of Year" :maxlength="fields['startdayofyear'] ? fields['startdayofyear']['length'] : 0" :value="data.startdayofyear" @update:value="(value) => updateData('startdayofyear', value)"></text-field-input-element>
                        </div>
                        <div v-if="!editorHideFields.includes('enddayofyear')" class="col-12 col-sm-5">
                            <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['enddayofyear']" label="End Day of Year" :maxlength="fields['enddayofyear'] ? fields['enddayofyear']['length'] : 0" :value="data.enddayofyear" @update:value="(value) => updateData('enddayofyear', value)"></text-field-input-element>
                        </div>
                    </div>
                </div>
            </template>
            <template v-else>
                <div v-if="!editorHideFields.includes('year') || !editorHideFields.includes('month') || !editorHideFields.includes('day')" class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('year')" class="col-12 col-sm-4">
                        <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['year']" label="Year" :maxlength="fields['year'] ? fields['year']['length'] : 0" :value="data.year" @update:value="(value) => updateData('year', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('month')" class="col-12 col-sm-4">
                        <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['month']" label="Month" :maxlength="fields['month'] ? fields['month']['length'] : 0" :value="data.month" @update:value="(value) => updateData('month', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('day')" class="col-12 col-sm-4">
                        <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['day']" label="Day" :maxlength="fields['day'] ? fields['day']['length'] : 0" :value="data.day" @update:value="(value) => updateData('day', value)"></text-field-input-element>
                    </div>
                </div>
                <div v-if="!editorHideFields.includes('startdayofyear') || !editorHideFields.includes('enddayofyear')" class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('startdayofyear')" class="col-12 col-sm-5">
                        <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['startdayofyear']" label="Start Day of Year" :maxlength="fields['startdayofyear'] ? fields['startdayofyear']['length'] : 0" :value="data.startdayofyear" @update:value="(value) => updateData('startdayofyear', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('enddayofyear')" class="col-12 col-sm-5">
                        <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['enddayofyear']" label="End Day of Year" :maxlength="fields['enddayofyear'] ? fields['enddayofyear']['length'] : 0" :value="data.enddayofyear" @update:value="(value) => updateData('enddayofyear', value)"></text-field-input-element>
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
                props.data.substrate ||
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
            if(props.eventMode){
                occurrenceStore.updateCollectingEventEditDataDate(dateData);
            }
            else{
                occurrenceStore.updateOccurrenceEditDataDate(dateData);
                if(props.autoSearch){
                    processCollectingEventSearch();
                }
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
            showExtendedForm,
            showLocationForm,
            updateData,
            updateDateData
        }
    }
};
