const checklistEditorAppConfigTab = {
    template: `
        <div class="q-pa-md column q-gutter-sm">
            <div class="row justify-end q-gutter-sm">
                <div v-if="checklistData['appconfigjson'] && checklistData['appconfigjson'].hasOwnProperty('dataArchiveFilename') && checklistData['appconfigjson']['dataArchiveFilename']">
                    <q-btn color="negative" @click="deleteAppData();" label="Delete App Data" aria-label="Delete App Data" tabindex="0" />
                </div>
                <div>
                    <q-btn color="secondary" @click="initializePrepareAppData();" label="Prepare/Update App Data" :disabled="!checklistData['appconfigjson'] || !checklistData['appconfigjson'].hasOwnProperty('descSourceTab') || !checklistData['appconfigjson']['descSourceTab']" aria-label="Prepare and Update App Data" tabindex="0" />
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <taxon-description-source-tab-auto-complete :value="(checklistData['appconfigjson'] && checklistData['appconfigjson'].hasOwnProperty('descSourceTab')) ? checklistData['appconfigjson']['descSourceTab'] : null" @update:value="(value) => updateAppConfigData('descSourceTab', value)"></taxon-description-source-tab-auto-complete>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="int" label="Max Amount of Autoload Images Per Taxon" :value="(checklistData['appconfigjson'] && checklistData['appconfigjson'].hasOwnProperty('maxImagesPerTaxon')) ? checklistData['appconfigjson']['maxImagesPerTaxon'] : null" min-value="0"  @update:value="(value) => updateAppConfigData('maxImagesPerTaxon', value)"></text-field-input-element>
                </div>
            </div>
        </div>
    `,
    components: {
        'taxon-description-source-tab-auto-complete': taxonDescriptionSourceTabAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { hideWorking, showNotification, showWorking } = useCore();
        const checklistStore = useChecklistStore();

        const checklistData = Vue.computed(() => checklistStore.getChecklistData);
        const checklistId = Vue.computed(() => checklistStore.getChecklistID);
        const clidArr = Vue.computed(() => checklistStore.getClidArr);
        const dataArchiveFilename = Vue.ref(null);
        const editsExist = Vue.computed(() => checklistStore.getChecklistEditsExist);
        const tidAcceptedArr = Vue.computed(() => checklistStore.getChecklistTaxaTidAcceptedArr);

        function deleteAppData() {
            showWorking();
            processDeleteAppDataArchive((res) => {
                if(res === 1){
                    checklistStore.updateChecklistEditData('appconfigjson', null);
                    if(editsExist.value){
                        checklistStore.updateChecklistRecord((res) => {
                            hideWorking();
                            if(res === 1){
                                showNotification('positive','App data has been deleted.');
                            }
                            else{
                                showNotification('negative', 'There was an error deleting the app data configurations.');
                            }
                        });
                    }
                }
                else{
                    hideWorking();
                    showNotification('negative', 'There was an error deleting the app data archive.');
                }
            });
        }

        function initializePrepareAppData() {
            showWorking();
            if(checklistData.value['appconfigjson'] && checklistData.value['appconfigjson'].hasOwnProperty('dataArchiveFilename') && checklistData.value['appconfigjson']['dataArchiveFilename']){
                processDeleteAppDataArchive((res) => {
                    if(res === 1){
                        initializeAppDataArchive();
                    }
                    else{
                        hideWorking();
                        showNotification('negative', 'There was an error deleting the previous app data archive.');
                    }
                });
            }
            else{
                initializeAppDataArchive();
            }
        }

        function initializeAppDataArchive() {
            console.log('initializeAppDataArchive');
            const formData = new FormData();
            formData.append('clid', checklistId.value.toString());
            formData.append('action', 'initializeAppDataArchive');
            fetch(checklistPackagingServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(res !== ''){
                    dataArchiveFilename.value = res;
                    packageChecklistTaggedImages();
                }
                else{
                    hideWorking();
                    showNotification('negative', 'There was an error creating the new app data archive.');
                }
            });
        }

        function packageChecklistTaggedImages() {
            console.log(dataArchiveFilename.value);
            const formData = new FormData();
            formData.append('clidArr', JSON.stringify(clidArr.value));
            formData.append('tidArr', JSON.stringify(tidAcceptedArr.value));
            formData.append('archiveFile', dataArchiveFilename.value);
            formData.append('action', 'packageChecklistTaggedImages');
            fetch(checklistPackagingServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                console.log(data);
                hideWorking();
            });
        }

        function processDeleteAppDataArchive(callback) {
            const formData = new FormData();
            formData.append('clid', checklistId.value.toString());
            formData.append('action', 'deleteAppDataArchive');
            fetch(checklistPackagingServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
            });
        }

        function updateAppConfigData(key, value) {
            checklistStore.updateChecklistEditAppConfigData(key, value);
        }

        return {
            checklistData,
            deleteAppData,
            initializePrepareAppData,
            updateAppConfigData
        }
    }
};
