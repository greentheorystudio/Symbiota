const occurrenceCloneRecordModule = {
    template: `
        <q-card flat bordered class="q-mt-sm black-border">
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                    Clone Record
                </div>
                <div class="row justify-between q-col-gutter-sm">
                    <div class="col-12 col-sm-6 col-md-3">
                        <selector-input-element label="Data to include" :options="includeDataOptions" :value="selectedIncludeDataOption" @update:value="(value) => selectedIncludeDataOption = value"></selector-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-5">
                        <checkbox-input-element label="Include media linkages" :value="includeMediaLinkages" @update:value="(value) => includeMediaLinkages = value"></checkbox-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <text-field-input-element data-type="int" label="Number of clones" :value="cloneQuantity" min-value="1" :clearable="false" @update:value="(value) => cloneQuantity = value"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2 row justify-end">
                        <div>
                            <q-btn color="secondary" @click="processCloneRecord();" label="Create Records" tabindex="0" dense />
                        </div>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { hideWorking, showNotification, showWorking } = useCore();
        const occurrenceStore = useOccurrenceStore();

        const blankCollectingEventRecord = Vue.computed(() => occurrenceStore.getBlankCollectingEventRecord);
        const blankLocationRecord = Vue.computed(() => occurrenceStore.getBlankLocationRecord);
        const cloneData = Vue.computed(() => {
            const returnData = {};
            Object.keys(occurrenceData.value).forEach(key => {
                if(key !== 'occid' && key !== 'dbpk' && key !== 'occurrenceid' && key !== 'datelastmodified'){
                    if(selectedIncludeDataOption.value === 'event' && (Object.keys(blankCollectingEventRecord.value).includes(key) || Object.keys(blankLocationRecord.value).includes(key))){
                        returnData[key] = occurrenceData.value[key];
                    }
                    else if(selectedIncludeDataOption.value === 'location' && Object.keys(blankLocationRecord.value).includes(key)){
                        returnData[key] = occurrenceData.value[key];
                    }
                    else if(selectedIncludeDataOption.value === 'all'){
                        returnData[key] = occurrenceData.value[key];
                    }
                }
            });
            return returnData;
        });
        const cloneQuantity = Vue.ref(1);
        const currentImageIndex = Vue.ref(0);
        const currentMediaIndex = Vue.ref(0);
        const currentOccurrenceIndex = Vue.ref(0);
        const imageArr = Vue.computed(() => occurrenceStore.getImageArr);
        const includeDataOptions = [
            {value: 'event', label: 'Event and Location Data'},
            {value: 'location', label: 'Location Data'},
            {value: 'all', label: 'All Data'}
        ];
        const includeMediaLinkages = Vue.ref(false);
        const mediaArr = Vue.computed(() => occurrenceStore.getMediaArr);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceData = Vue.computed(() => occurrenceStore.getCurrentOccurrenceData);
        const selectedIncludeDataOption = Vue.ref('event');

        Vue.watch(occId, () => {
            resetCounts();
        });

        function processCloneImageAssociations(occid) {
            if(imageArr.value.length > 0 || imageArr.value.length > currentImageIndex.value){

            }
            else{
                processCloneMediaAssociations();
            }
        }

        function processCloneMediaAssociations() {
            if(imageArr.value.length > 0){

            }
        }

        function processCloneRecord() {
            showWorking();
            console.log(imageArr.value);
            occurrenceStore.createOccurrenceRecord((newOccid) => {
                if(newOccid > 0){
                    if(includeMediaLinkages.value){
                        resetMediaCounts();
                        processCloneImageAssociations(newOccid);
                    }
                    else{
                        currentOccurrenceIndex.value++;
                        if(currentOccurrenceIndex.value < cloneQuantity.value){
                            processCloneRecord();
                        }
                        else{
                            hideWorking();
                        }
                    }
                }
                else{
                    showNotification('negative', 'There was an error creating the cloned occurrence record.');
                }
            }, cloneData.value);
        }

        function resetCounts() {
            cloneQuantity.value = 1;
            currentOccurrenceIndex.value = 0;
        }

        function resetMediaCounts() {
            currentImageIndex.value = 0;
            currentMediaIndex.value = 0;
        }

        return {
            cloneQuantity,
            includeDataOptions,
            includeMediaLinkages,
            selectedIncludeDataOption,
            processCloneRecord
        }
    }
};
