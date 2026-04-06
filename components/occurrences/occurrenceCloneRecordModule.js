const occurrenceCloneRecordModule = {
    template: `
        <q-card flat bordered>
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
        const searchStore = useSearchStore();

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
        const collId = Vue.computed(() => occurrenceStore.getCollId);
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

        function createImageRecord(imageData, callback) {
            const formData = new FormData();
            formData.append('collid', collId.value.toString());
            formData.append('image', JSON.stringify(imageData));
            formData.append('action', 'addImage');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(res);
            });
        }

        function createMediaRecord(mediaData, callback) {
            const formData = new FormData();
            formData.append('collid', collId.value.toString());
            formData.append('media', JSON.stringify(mediaData));
            formData.append('action', 'addMedia');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(res);
            });
        }
        function processCloneImageAssociations(occid) {
            if(imageArr.value.length > 0 || imageArr.value.length > currentImageIndex.value){
                const imageData = Object.assign({}, imageArr.value[currentImageIndex.value]);
                delete imageData.imgid;
                delete imageData.initialtimestamp;
                delete imageData.tagArr;
                imageData.sortsequence = Number(imageData.sortsequence) > 0 ? imageData.sortsequence : 50;
                imageData.occid = occid;
                createImageRecord(imageData, (id) => {
                    if(Number(id) > 0){
                        currentImageIndex.value++;
                        processCloneImageAssociations(occid);
                    }
                    else{
                        hideWorking();
                        showNotification('negative', 'There was an error associating image records with the newly cloned occurrence record.');
                    }
                });
            }
            else{
                processCloneMediaAssociations(occid);
            }
        }

        function processCloneMediaAssociations(occid) {
            if(mediaArr.value.length > 0 || mediaArr.value.length > currentMediaIndex.value){
                const mediaData = Object.assign({}, mediaArr.value[currentMediaIndex.value]);
                delete mediaData.mediaid;
                mediaData.sortsequence = Number(mediaData.sortsequence) > 0 ? mediaData.sortsequence : 50;
                mediaData.occid = occid;
                createMediaRecord(mediaData, (id) => {
                    if(Number(id) > 0){
                        currentMediaIndex.value++;
                        processCloneMediaAssociations(occid);
                    }
                    else{
                        hideWorking();
                        showNotification('negative', 'There was an error associating media records with the newly cloned occurrence record.');
                    }
                });
            }
            else{
                currentOccurrenceIndex.value++;
                if(currentOccurrenceIndex.value < cloneQuantity.value){
                    processCloneRecord();
                }
                else{
                    hideWorking();
                    showNotification('positive','Cloned successfully');
                }
            }
        }

        function processCloneRecord() {
            showWorking();
            occurrenceStore.createOccurrenceRecord((newOccid) => {
                if(newOccid > 0){
                    searchStore.addNewOccidToOccidArrs(newOccid);
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
                            showNotification('positive','Cloned successfully');
                        }
                    }
                }
                else{
                    hideWorking();
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
