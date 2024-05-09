const occurrenceEditorFormCollectingEventElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-xs">
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-12 col-sm-6 col-md-grow">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['recordedby']" label="Collector/Observer" :maxlength="occurrenceFields['recordedby'] ? occurrenceFields['recordedby']['length'] : 0" :value="occurrenceData.recordedby" @update:value="(value) => updateOccurrenceData('recordedby', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['recordnumber']" label="Number" :maxlength="occurrenceFields['recordnumber'] ? occurrenceFields['recordnumber']['length'] : 0" :value="occurrenceData.recordnumber" @update:value="(value) => updateOccurrenceData('recordnumber', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <date-input-element :definition="occurrenceFieldDefinitions['eventdate']" label="Date" :value="occurrenceData.eventdate" @update:value="updateOccurrenceDateData"></date-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <time-input-element :definition="occurrenceFieldDefinitions['eventtime']" label="Time" :value="occurrenceData.eventtime" @update:value="(value) => updateOccurrenceData('eventtime', value)"></time-input-element>
                    </div>
                    <div v-if="!collectionEventAutoSearch" class="row justify-end self-center">
                        <div>
                            <q-btn color="secondary" size="md" @click="processCollectingEventSearch(false);" label="Search for Event" dense/>
                        </div>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-12 col-sm-7">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['associatedcollectors']" label="Associated Collectors" :maxlength="occurrenceFields['associatedcollectors'] ? occurrenceFields['associatedcollectors']['length'] : 0" :value="occurrenceData.associatedcollectors" @update:value="(value) => updateOccurrenceData('associatedcollectors', value)"></text-field-input-element>
                    </div>
                    <div class="col-11 col-sm-4">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['verbatimeventdate']" label="Verbatim Date" :maxlength="occurrenceFields['verbatimeventdate'] ? occurrenceFields['verbatimeventdate']['length'] : 0" :value="occurrenceData.verbatimeventdate" @update:value="(value) => updateOccurrenceData('verbatimeventdate', value)"></text-field-input-element>
                    </div>
                    <div class="col-1 row justify-end self-center">
                        <div>
                            <template v-if="showExtendedForm">
                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = false" icon="fas fa-minus" dense></q-btn>
                            </template>
                            <template v-else>
                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = true" icon="fas fa-plus" dense></q-btn>
                            </template>
                        </div>
                    </div>
                </div>
                <template v-if="showExtendedForm">
                    <div class="row justify-between q-col-gutter-xs">
                        <div class="col-12 col-sm-6 row justify-start q-col-gutter-md">
                            <div class="col-12 col-sm-6">
                                <text-field-input-element data-type="number" :definition="occurrenceFieldDefinitions['minimumdepthinmeters']" label="Minimum Depth (m)" :maxlength="occurrenceFields['minimumdepthinmeters'] ? occurrenceFields['minimumdepthinmeters']['length'] : 0" :value="occurrenceData.minimumdepthinmeters" @update:value="(value) => updateOccurrenceData('minimumdepthinmeters', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-6">
                                <text-field-input-element data-type="number" :definition="occurrenceFieldDefinitions['maximumdepthinmeters']" label="Maximum Depth (m)" :maxlength="occurrenceFields['maximumdepthinmeters'] ? occurrenceFields['maximumdepthinmeters']['length'] : 0" :value="occurrenceData.maximumdepthinmeters" @update:value="(value) => updateOccurrenceData('maximumdepthinmeters', value)"></text-field-input-element>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['verbatimdepth']" label="Verbatim Depth" :maxlength="occurrenceFields['verbatimdepth'] ? occurrenceFields['verbatimdepth']['length'] : 0" :value="occurrenceData.verbatimdepth" @update:value="(value) => updateOccurrenceData('verbatimdepth', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['habitat']" label="Habitat" :value="occurrenceData.habitat" @update:value="(value) => updateOccurrenceData('habitat', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['substrate']" label="Substrate" :value="occurrenceData.substrate" @update:value="(value) => updateOccurrenceData('substrate', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row justify-between q-col-gutter-xs">
                        <div class="col-12 col-sm-6 col-md-4">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['fieldnumber']" label="Field Number" :maxlength="occurrenceFields['fieldnumber'] ? occurrenceFields['fieldnumber']['length'] : 0" :value="occurrenceData.fieldnumber" @update:value="(value) => updateOccurrenceData('fieldnumber', value)"></text-field-input-element>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['samplingprotocol']" label="Sampling Protocol" :maxlength="occurrenceFields['samplingprotocol'] ? occurrenceFields['samplingprotocol']['length'] : 0" :value="occurrenceData.samplingprotocol" @update:value="(value) => updateOccurrenceData('samplingprotocol', value)"></text-field-input-element>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['samplingeffort']" label="Sampling Effort" :maxlength="occurrenceFields['samplingeffort'] ? occurrenceFields['samplingeffort']['length'] : 0" :value="occurrenceData.samplingeffort" @update:value="(value) => updateOccurrenceData('samplingeffort', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['fieldnotes']" label="Field Notes" :value="occurrenceData.fieldnotes" @update:value="(value) => updateOccurrenceData('fieldnotes', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['labelproject']" label="Label Project" :maxlength="occurrenceFields['labelproject'] ? occurrenceFields['labelproject']['length'] : 0" :value="occurrenceData.labelproject" @update:value="(value) => updateOccurrenceData('labelproject', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row justify-between q-col-gutter-xs">
                        <div class="col-6 row justify-start q-col-gutter-xs">
                            <div class="col-12 col-sm-4">
                                <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['year']" label="Year" :maxlength="occurrenceFields['year'] ? occurrenceFields['year']['length'] : 0" :value="occurrenceData.year" @update:value="(value) => updateOccurrenceData('year', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-4">
                                <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['month']" label="Month" :maxlength="occurrenceFields['month'] ? occurrenceFields['month']['length'] : 0" :value="occurrenceData.month" @update:value="(value) => updateOccurrenceData('month', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-4">
                                <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['day']" label="Day" :maxlength="occurrenceFields['day'] ? occurrenceFields['day']['length'] : 0" :value="occurrenceData.day" @update:value="(value) => updateOccurrenceData('day', value)"></text-field-input-element>
                            </div>
                        </div>
                        <div class="col-6 row justify-end q-col-gutter-xs">
                            <div class="col-12 col-sm-5">
                                <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['startdayofyear']" label="Start Day of Year" :maxlength="occurrenceFields['startdayofyear'] ? occurrenceFields['startdayofyear']['length'] : 0" :value="occurrenceData.startdayofyear" @update:value="(value) => updateOccurrenceData('startdayofyear', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-5">
                                <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['enddayofyear']" label="End Day of Year" :maxlength="occurrenceFields['enddayofyear'] ? occurrenceFields['enddayofyear']['length'] : 0" :value="occurrenceData.enddayofyear" @update:value="(value) => updateOccurrenceData('enddayofyear', value)"></text-field-input-element>
                            </div>
                        </div>
                    </div>
                </template>
            </q-card-section>
        </q-card>
        <template v-if="showCollectingEventListPopup">
            <occurrence-collecting-event-list-popup
                    :event-arr="collectingEventArr"
                    :show-popup="showCollectingEventListPopup"
                    @close:popup="closeCollectingEventListPopup();"
            ></occurrence-collecting-event-list-popup>
        </template>
    `,
    components: {
        'date-input-element': dateInputElement,
        'occurrence-collecting-event-list-popup': occurrenceCollectingEventListPopup,
        'text-field-input-element': textFieldInputElement,
        'time-input-element': timeInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const collectingEventArr = Vue.ref([]);
        const collectionEventAutoSearch = Vue.computed(() => occurrenceStore.getCollectionEventAutoSearch);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const showCollectingEventListPopup = Vue.ref(false);
        const showExtendedForm = Vue.ref(false);

        Vue.watch(occurrenceData, () => {
            setExtendedView();
        });

        function closeCollectingEventListPopup() {
            showCollectingEventListPopup.value = false;
            collectingEventArr.value = [];
        }

        function processCollectingEventSearch(silent = true) {
            if(occurrenceData.value.recordedby && ((occurrenceData.value.recordnumber && !isNaN(occurrenceData.value.recordnumber)) || occurrenceData.value.eventdate)){
                occurrenceStore.getCollectingEvents('occurrence', (listArr) => {
                    if(listArr.length > 0){
                        collectingEventArr.value = listArr;
                        showCollectingEventListPopup.value = true;
                    }
                    else{
                        showNotification('negative', 'There were no probable events found matching this data.');
                    }
                });
            }
            else if(!silent){
                showNotification('negative', 'To search for the event a collector/observer value must be entered, as well as a numeric number value or a date.');
            }
        }

        function setExtendedView() {
            if(occurrenceData.value.fieldnotes ||
                occurrenceData.value.fieldnumber ||
                occurrenceData.value.habitat ||
                occurrenceData.value.substrate ||
                occurrenceData.value.minimumdepthinmeters ||
                occurrenceData.value.maximumdepthinmeters ||
                occurrenceData.value.verbatimdepth ||
                occurrenceData.value.samplingprotocol ||
                occurrenceData.value.samplingeffort ||
                occurrenceData.value.labelproject
            ){
                showExtendedForm.value = true;
            }
        }

        function updateOccurrenceData(key, value) {
            occurrenceStore.updateOccurrenceEditData(key, value);
            if(collectionEventAutoSearch.value && (key === 'recordedby' || key === 'recordnumber')){
                processCollectingEventSearch();
            }
        }

        function updateOccurrenceDateData(data) {
            updateOccurrenceData('eventdate', data['date']);
            updateOccurrenceData('year', data['year']);
            updateOccurrenceData('month', data['month']);
            updateOccurrenceData('day', data['day']);
            updateOccurrenceData('startdayofyear', data['startDayOfYear']);
            updateOccurrenceData('enddayofyear', data['endDayOfYear']);
            if(collectionEventAutoSearch.value){
                processCollectingEventSearch();
            }
        }

        Vue.onMounted(() => {
            setExtendedView();
        });

        return {
            collectingEventArr,
            collectionEventAutoSearch,
            occurrenceData,
            occurrenceFields,
            occurrenceFieldDefinitions,
            showCollectingEventListPopup,
            showExtendedForm,
            closeCollectingEventListPopup,
            processCollectingEventSearch,
            updateOccurrenceData,
            updateOccurrenceDateData
        }
    }
};
