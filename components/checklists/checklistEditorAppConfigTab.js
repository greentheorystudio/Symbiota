const checklistEditorAppConfigTab = {
    template: `
        <div class="q-pa-md column q-gutter-sm">
            <div class="row justify-end q-gutter-sm">
                <div v-if="dataArchiveFilename">
                    <q-btn color="negative" @click="deleteAppData();" label="Delete App Data" aria-label="Delete App Data" tabindex="0" />
                </div>
                <div>
                    <q-btn color="secondary" @click="initializePrepareAppData();" label="Prepare/Update App Data" aria-label="Prepare and Update App Data" tabindex="0" />
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <taxon-description-source-tab-auto-complete :value="(checklistData['appconfigjson'] && checklistData['appconfigjson'].hasOwnProperty('descSourceTab')) ? checklistData['appconfigjson']['descSourceTab'] : null" @update:value="(value) => updateAppConfigData('descSourceTab', value)"></taxon-description-source-tab-auto-complete>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="int" label="Max Amount of Autoload Images Per Taxon" :value="(checklistData['appconfigjson'] && checklistData['appconfigjson'].hasOwnProperty('maxImagesPerTaxon')) ? checklistData['appconfigjson']['maxImagesPerTaxon'] : null" min-value="0" max-value="5"  @update:value="(value) => updateAppConfigData('maxImagesPerTaxon', value)"></text-field-input-element>
                </div>
            </div>
            <div v-if="lastPublishedStr" class="q-mt-sm text-subtitle1 text-bold">
                Last published: {{ lastPublishedStr }}
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
        const csidArr = Vue.ref([]);
        const dataArchiveFilename = Vue.computed(() => {
            return (checklistData.value['appconfigjson'] && checklistData.value['appconfigjson'].hasOwnProperty('dataArchiveFilename') && checklistData.value['appconfigjson']['dataArchiveFilename']) ? checklistData.value['appconfigjson']['dataArchiveFilename'] : null;
        });
        const editsExist = Vue.computed(() => checklistStore.getChecklistEditsExist);
        const imageUploadBatchSize = Vue.computed(() => {
            let returnVal;
            if(checklistData.value['appconfigjson'] && checklistData.value['appconfigjson'].hasOwnProperty('maxImagesPerTaxon') && Number(checklistData.value['appconfigjson']['maxImagesPerTaxon']) > 0){
                if(Number(checklistData.value['appconfigjson']['maxImagesPerTaxon']) === 5){
                    returnVal = 4;
                }
                else if(Number(checklistData.value['appconfigjson']['maxImagesPerTaxon']) === 4){
                    returnVal = 5;
                }
                else if(Number(checklistData.value['appconfigjson']['maxImagesPerTaxon']) === 3){
                    returnVal = 7;
                }
                else if(Number(checklistData.value['appconfigjson']['maxImagesPerTaxon']) === 2){
                    returnVal = 10;
                }
                else{
                    returnVal = 20;
                }
            }
            return returnVal;
        });
        const lastPublishedStr = Vue.computed(() => {
            let returnStr = null;
            if(checklistData.value['appconfigjson'] && checklistData.value['appconfigjson'].hasOwnProperty('datePublished') && checklistData.value['appconfigjson']['datePublished']){
                const lastPubDate = new Date(checklistData.value['appconfigjson']['datePublished']);
                returnStr = lastPubDate.toString();
            }
            return returnStr;
        });
        const taggedTargetImageTidArr = Vue.ref([]);
        const targetImageTidArr = Vue.ref([]);
        const targetMapImageTidArr = Vue.ref([]);
        const taxonLoadingIndex = Vue.ref(0);
        const tidAcceptedArr = Vue.computed(() => checklistStore.getChecklistTaxaTidAcceptedArr);

        function deleteAppData() {
            showWorking();
            checklistStore.deleteAppDataArchive((res) => {
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
            csidArr.value.length = 0;
            targetImageTidArr.value.length = 0;
            taxonLoadingIndex.value = 0;
            taggedTargetImageTidArr.value = tidAcceptedArr.value.slice();
            targetMapImageTidArr.value.length = tidAcceptedArr.value.slice();
            if(checklistData.value['appconfigjson'] && checklistData.value['appconfigjson'].hasOwnProperty('dataArchiveFilename') && checklistData.value['appconfigjson']['dataArchiveFilename']){
                showWorking('Removing previous data archive');
                checklistStore.deleteAppDataArchive((res) => {
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
            showWorking('Initializing app data archive');
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
                    updateAppConfigData('dataArchiveFilename', res);
                    updateAppConfigData('datePublished', Math.floor(Date.now()));
                    if(editsExist.value){
                        checklistStore.updateChecklistRecord((res) => {
                            hideWorking();
                            if(res === 1){
                                packageChecklistTaggedImages();
                            }
                            else{
                                showNotification('negative', 'There was an error saving the app configurations.');
                            }
                        });
                    }
                }
                else{
                    checklistStore.updateChecklistEditData('appconfigjson', null);
                    if(editsExist.value){
                        checklistStore.updateChecklistRecord(() => {
                            hideWorking();
                            showNotification('negative', 'There was an error creating the new app data archive.');
                        });
                    }
                }
            });
        }

        function packageChecklistCharacterData() {
            showWorking('Packaging character data');
            const formData = new FormData();
            formData.append('csidArr', JSON.stringify(csidArr.value));
            formData.append('archiveFile', dataArchiveFilename.value);
            formData.append('action', 'packageChecklistCharacterData');
            fetch(checklistPackagingServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processCompletedDataPackaging();
                }
                else{
                    hideWorking();
                    showNotification('negative', 'There was an error while packaging the character data.');
                }
            });
        }

        function packageChecklistImages() {
            showWorking('Packaging images');
            const targetArr = targetImageTidArr.value.splice(0, imageUploadBatchSize.value);
            const formData = new FormData();
            formData.append('tidArr', JSON.stringify(targetArr));
            formData.append('imageMaxCnt', checklistData.value['appconfigjson']['maxImagesPerTaxon']);
            formData.append('archiveFile', dataArchiveFilename.value);
            formData.append('action', 'packageChecklistImages');
            fetch(checklistPackagingServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then(() => {
                if(targetImageTidArr.value.length > 0){
                    packageChecklistImages();
                }
                else{
                    processCompletedImageDataPackaging();
                }
            });
        }

        function packageChecklistMapImages() {
            showWorking('Packaging taxa map images');
            const targetArr = targetMapImageTidArr.value.splice(0, 5);
            const formData = new FormData();
            formData.append('tidArr', JSON.stringify(targetArr));
            formData.append('archiveFile', dataArchiveFilename.value);
            formData.append('action', 'packageChecklistMapImages');
            fetch(checklistPackagingServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then(() => {
                if(targetMapImageTidArr.value.length > 0){
                    packageChecklistMapImages();
                }
                else{
                    processCompletedMapImageDataPackaging();
                }
            });
        }

        function packageChecklistTaggedImages() {
            showWorking('Packaging tagged images');
            const targetArr = taggedTargetImageTidArr.value.splice(0, 5);
            const formData = new FormData();
            formData.append('clidArr', JSON.stringify(clidArr.value));
            formData.append('tidArr', JSON.stringify(targetArr));
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
                targetArr.forEach(tid => {
                    if(!data.includes(Number(tid))){
                        targetImageTidArr.value.push(tid);
                    }
                });
                if(taggedTargetImageTidArr.value.length > 0){
                    packageChecklistTaggedImages();
                }
                else{
                    if(targetImageTidArr.value.length > 0 && Number(checklistData.value['appconfigjson']['maxImagesPerTaxon']) > 0){
                        packageChecklistImages();
                    }
                    else{
                        processCompletedImageDataPackaging();
                    }
                }
            });
        }

        function packageChecklistTaxaData() {
            const loadingCnt = 25;
            showWorking('Packaging taxa data');
            const formData = new FormData();
            formData.append('clidArr', JSON.stringify(clidArr.value));
            formData.append('index', taxonLoadingIndex.value.toString());
            formData.append('reccnt', loadingCnt.toString());
            formData.append('descTab', ((checklistData.value['appconfigjson'] && checklistData.value['appconfigjson'].hasOwnProperty('descSourceTab') && checklistData.value['appconfigjson']['descSourceTab']) ? checklistData.value['appconfigjson']['descSourceTab'] : null));
            formData.append('archiveFile', dataArchiveFilename.value);
            formData.append('action', 'packageChecklistTaxaData');
            fetch(checklistPackagingServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data['csidArr'].length > 0){
                    csidArr.value = csidArr.value.concat(data['csidArr']);
                }
                if(Number(data['reccnt']) < loadingCnt){
                    csidArr.value = [...new Set(csidArr.value)];
                    processCompletedTaxaDataPackaging();
                }
                else{
                    taxonLoadingIndex.value++;
                    packageChecklistTaxaData();
                }
            });
        }

        function processCompletedDataPackaging() {
            const formData = new FormData();
            formData.append('archiveFile', dataArchiveFilename.value);
            formData.append('action', 'processCompletedDataPackaging');
            fetch(checklistPackagingServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                hideWorking();
                if(Number(res) === 1){
                    showNotification('positive','Packaging process complete!');
                }
                else{
                    showNotification('negative', 'There was an error completing the data packaging.');
                }
            });
        }

        function processCompletedImageDataPackaging() {
            const formData = new FormData();
            formData.append('archiveFile', dataArchiveFilename.value);
            formData.append('action', 'processCompletedImageDataPackaging');
            fetch(checklistPackagingServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then(() => {
                packageChecklistMapImages();
            });
        }

        function processCompletedMapImageDataPackaging() {
            const formData = new FormData();
            formData.append('archiveFile', dataArchiveFilename.value);
            formData.append('action', 'processCompletedMapImageDataPackaging');
            fetch(checklistPackagingServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then(() => {
                packageChecklistTaxaData();
            });
        }

        function processCompletedTaxaDataPackaging() {
            const formData = new FormData();
            formData.append('archiveFile', dataArchiveFilename.value);
            formData.append('action', 'processCompletedTaxaDataPackaging');
            fetch(checklistPackagingServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    if(csidArr.value.length > 0){
                        packageChecklistCharacterData();
                    }
                    else{
                        processCompletedDataPackaging();
                    }
                }
                else{
                    hideWorking();
                    showNotification('negative', 'There was an error completing the image data packaging.');
                }
            });
        }

        function updateAppConfigData(key, value) {
            checklistStore.updateChecklistEditAppConfigData(key, value);
        }

        return {
            checklistData,
            dataArchiveFilename,
            lastPublishedStr,
            deleteAppData,
            initializePrepareAppData,
            updateAppConfigData
        }
    }
};
