const externalMediaFileImportModule = {
    props: {
        collectionId: {
            type: Number,
            default: null
        },
        mediaType: {
            type: String,
            default: 'taxa'
        }
    },
    template: `
        <div class="processor-container">
            <div class="processor-control-container">
                <q-card class="processor-control-card">
                    <q-card-section class="column q-gutter-sm">
                        <q-card class="q-my-sm" flat bordered>
                            <q-card-section>
                                <div class="column q-col-gutter-sm">
                                    <div class="text-bold text-subtitle1">
                                        Image record queue: {{ totalImageCount }}
                                    </div>
                                    <div class="text-bold text-subtitle1">
                                        Sound & video record queue: {{ totalMediaCount }}
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                        <q-card class="q-my-sm" flat bordered>
                            <q-card-section>
                                <div class="q-pa-md column q-col-gutter-sm">
                                    <div>
                                        This module will import externally hosted {{ mediaType }} media files onto the local server and 
                                        update the media records with the local urls.
                                    </div>
                                    <div class="row">
                                        <div class="col-grow">
                                            <selector-input-element :disabled="currentProcess" :options="importTypeOptions" label="Media Type" :value="selectedImportType" @update:value="(value) => selectedImportType = value"></selector-input-element>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-grow">
                                            <text-field-input-element :disabled="currentProcess" data-type="int" label="Process File Limit (blank for unlimited)" :value="processLimit" @update:value="processLimitChange"></text-field-input-element>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-grow">
                                            <checkbox-input-element :disabled="currentProcess" label="Remove records with broken media links" :value="removeBrokenLinksVal" @update:value="(value) => removeBrokenLinksVal = Number(value) === 1"></checkbox-input-element>
                                        </div>
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                        <div class="processor-tool-control-container">
                            <div class="processor-cancel-message-container text-negative text-bold">
                                <template v-if="processCancelling">
                                    Cancelling, please wait
                                </template>
                            </div>
                            <div class="processor-tool-button-container">
                                <div>
                                    <q-btn color="secondary" @click="initializeProcess();" label="Start Process" dense aria-label="Start Process" :disabled="currentProcess" tabindex="0" />
                                </div>
                                <div>
                                    <q-btn v-if="currentProcess" :disabled="processCancelling" color="red" @click="cancelProcess();" label="Cancel" dense aria-label="Cancel" tabindex="0" />
                                </div>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>

            <div class="processor-display-container">
                <q-card class="bg-grey-3 q-pa-sm">
                    <q-scroll-area ref="procDisplayScrollAreaRef" class="bg-grey-1 processor-display" @scroll="setScroller">
                        <q-list dense>
                            <template v-if="!currentProcess && processorDisplayCurrentIndex > 0">
                                <q-item>
                                    <q-item-section>
                                        <div><a role="button" class="text-bold cursor-pointer" @click="processorDisplayScrollUp();" @keyup.enter="processorDisplayScrollUp();" aria-label="Show previous 100 entries" tabindex="0">Show previous 100 entries</a></div>
                                    </q-item-section>
                                </q-item>
                            </template>
                            <q-item v-for="proc in processorDisplayArr">
                                <q-item-section>
                                    <div>{{ proc.procText }} <q-spinner v-if="proc.loading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner></div>
                                    <template v-if="!proc.loading && proc.resultText">
                                        <div v-if="proc.result === 'success'" class="q-ml-sm text-weight-bold text-green-9">
                                            {{proc.resultText}}
                                        </div>
                                        <div v-if="proc.result === 'error'" class="q-ml-sm text-weight-bold text-negative">
                                            {{proc.resultText}}
                                        </div>
                                    </template>
                                    <template v-if="proc.type === 'multi' && proc.subs.length">
                                        <div class="q-ml-sm">
                                            <div v-for="subproc in proc.subs">
                                                <template v-if="subproc.type === 'text' || subproc.type === 'undo'">
                                                    <div>{{ subproc.procText }} <q-spinner v-if="subproc.loading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner></div>
                                                    <template v-if="!subproc.loading && subproc.resultText">
                                                        <div v-if="subproc.result === 'success' && subproc.type === 'text'" class="text-weight-bold text-green-9">
                                                            <span class="text-weight-bold text-green-9">{{subproc.resultText}}</span>
                                                        </div>
                                                        <div v-if="subproc.result === 'error'" class="text-weight-bold text-negative">
                                                            {{subproc.resultText}}
                                                        </div>
                                                    </template>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </q-item-section>
                            </q-item>
                            <template v-if="!currentProcess && processorDisplayCurrentIndex < processorDisplayIndex">
                                <q-item>
                                    <q-item-section>
                                        <div><a role="button" class="text-bold cursor-pointer" @click="processorDisplayScrollDown();" @keyup.enter="processorDisplayScrollDown();" aria-label="Show next 100 entries" tabindex="0">Show next 100 entries</a></div>
                                    </q-item-section>
                                </q-item>
                            </template>
                        </q-list>
                    </q-scroll-area>
                </q-card>
            </div>
        </div>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { getImageFilenameFromUrl, getMediaFilenameFromUrl, getUrlTargetFilename } = useCore();

        const currentImageData = Vue.ref({});
        const currentImageDataArr = Vue.ref([]);
        const currentImageEditData = Vue.ref({});
        const currentImageIdArr = Vue.ref([]);
        const currentMediaData = Vue.ref({});
        const currentMediaDataArr = Vue.ref([]);
        const currentMediaIdArr = Vue.ref([]);
        const currentProcess = Vue.ref(null);
        const idLoadingCnt = Vue.computed(() => {
            let returnVal;
            if(processLimit.value) {
                const currentRemaining = processLimit.value - totalFiles.value;
                returnVal = currentRemaining > 250000 ? 250000 : currentRemaining;
            }
            else{
                returnVal = 250000;
            }
            return returnVal;
        });
        const imageIdArr = Vue.ref([]);
        const importTypeOptions = [
            {value: 'all', label: 'All Media'},
            {value: 'images', label: 'Images Only'},
            {value: 'media', label: 'Sound & Video Only'},
            {value: 'thumbnail', label: 'Image Thumbnails Only'},
            {value: 'web', label: 'Web Images Only'},
            {value: 'original', label: 'Image Originals Only'}
        ];
        const loadingIndex = Vue.ref(0);
        const mediaIdArr = Vue.ref([]);
        const options = Vue.computed(() => {
            return {
                numRows: idLoadingCnt.value.toString(),
                index: loadingIndex.value.toString(),
                importType: selectedImportType.value,
                mediaType: props.mediaType,
                collid: props.collectionId
            };
        });
        const procDisplayScrollAreaRef = Vue.ref(null);
        const procDisplayScrollHeight = Vue.ref(0);
        const processCancelling = Vue.ref(false);
        const processingArr = Vue.ref([]);
        const processLimit = Vue.ref(null);
        const processorDisplayArr = Vue.reactive([]);
        let processorDisplayDataArr = [];
        const processorDisplayCurrentIndex = Vue.ref(0);
        const processorDisplayIndex = Vue.ref(0);
        const removeBrokenLinksVal = Vue.ref(false);
        const scrollProcess = Vue.ref(null);
        const selectedImportType = Vue.ref('all');
        const totalFiles = Vue.computed(() => {
            return imageIdArr.value.length + mediaIdArr.value.length;
        });
        const totalImageCount = Vue.ref(0);
        const totalMediaCount = Vue.ref(0);

        function addProcessToProcessorDisplay(processObj) {
            processorDisplayArr.push(processObj);
            if(processorDisplayArr.length > 100){
                if(processorDisplayDataArr.length > 900){
                    processorDisplayDataArr.splice(0, 100);
                }
                const precessorArrSegment = processorDisplayArr.slice(0, 100);
                processorDisplayDataArr = processorDisplayDataArr.concat(precessorArrSegment);
                processorDisplayArr.splice(0, 100);
                processorDisplayIndex.value++;
                processorDisplayCurrentIndex.value = processorDisplayIndex.value;
            }
        }

        function addSubprocessToProcessorDisplay(id, type, text) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === id);
            parentProcObj['subs'].push(getNewSubprocessObject(id, type, text));
            const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === id);
            if(dataParentProcObj){
                dataParentProcObj['subs'].push(getNewSubprocessObject(id, type, text));
            }
        }

        function adjustUIEnd() {
            currentProcess.value = null;
            processCancelling.value = false;
            context.emit('update:loading', false);
            processorDisplayDataArr = processorDisplayDataArr.concat(processorDisplayArr);
        }

        function adjustUIStart() {
            currentProcess.value = null;
            processingArr.value = [];
            processorDisplayArr.length = 0;
            processorDisplayDataArr = [];
            processorDisplayCurrentIndex.value = 0;
            processorDisplayIndex.value = 0;
            imageIdArr.value.length = 0;
            mediaIdArr.value.length = 0;
            loadingIndex.value = 0;
            totalImageCount.value = 0;
            totalMediaCount.value = 0;
        }

        function cancelProcess() {
            processCancelling.value = true;
        }

        function getMediaUploadPath(mediaData) {
            let path = '';
            if(props.mediaType === 'taxa'){
                if(mediaData.family){
                    path += mediaData.family;
                }
                else{
                    path += mediaData['unitname1'];
                }
            }
            else{
                if(mediaData.institutioncode){
                    path += mediaData.institutioncode;
                }
                if(mediaData.institutioncode && mediaData.collectioncode){
                    path += '_';
                }
                if(mediaData.collectioncode){
                    path += mediaData.collectioncode;
                }
            }
            return path;
        }

        function getNewProcessObject(id, type, text) {
            const procObj = {
                id: id,
                procText: text,
                type: type,
                loading: true,
                current: true,
                result: '',
                resultText: ''
            };
            if(type === 'multi'){
                procObj['subs'] = [];
            }
            return procObj;
        }

        function getNewSubprocessObject(id, type, text) {
            return {
                id: id,
                procText: text,
                type: type,
                loading: true,
                result: '',
                undoOrigName: '',
                undoChangedName: '',
                changedTid: 0,
                resultText: ''
            };
        }

        function initializeProcess() {
            adjustUIStart();
            if(selectedImportType.value !== 'media'){
                setImageIdArr();
            }
            else{
                setMediaIdArr();
            }
        }

        function processCurrentImageDataArr() {
            if(processCancelling.value){
                adjustUIEnd();
            }
            else{
                currentImageEditData.value = Object.assign({}, {});
                if(currentImageDataArr.value.length > 0){
                    currentImageData.value = Object.assign({}, currentImageDataArr.value[0]);
                    currentImageDataArr.value.splice(0, 1);
                    currentProcess.value = ('image' + currentImageData.value['imgid']);
                    const text = 'Processing image ID: ' + currentImageData.value['imgid'];
                    addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value,'multi', text));
                    processSuccessResponse(false);
                    processCurrentImageThumbnail();
                }
                else{
                    processImageIdArr();
                }
            }
        }

        function processCurrentImageDelete() {
            const text = 'Removing image record due to broken links';
            addSubprocessToProcessorDisplay(currentProcess.value, 'text', text);
            const formData = new FormData();
            formData.append('collid', (props.collectionId ? props.collectionId.toString() : ''));
            formData.append('imgid', currentImageData.value['imgid'].toString());
            formData.append('action', 'deleteImageRecord');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(res && Number(res) === 1){
                    processSubprocessSuccessResponse(currentProcess.value, true, 'Complete');
                }
                else{
                    processSubprocessErrorResponse(currentProcess.value, 'Error removing image record', true);
                }
                totalImageCount.value--;
                processCurrentImageDataArr();
            });
        }

        function processCurrentImageEditData() {
            if(
                removeBrokenLinksVal.value &&
                (!currentImageData.value['url'] || !currentImageData.value['url'].startsWith('/')) &&
                (!currentImageData.value['originalurl'] || !currentImageData.value['originalurl'].startsWith('/')) &&
                !currentImageEditData.value.hasOwnProperty('url') &&
                !currentImageEditData.value.hasOwnProperty('originalurl')
            ){
                processCurrentImageDelete();
            }
            else if(Object.keys(currentImageEditData.value).length > 0){
                const text = 'Saving urls for imported files';
                addSubprocessToProcessorDisplay(currentProcess.value, 'text', text);
                const formData = new FormData();
                formData.append('collid', (props.collectionId ? props.collectionId.toString() : ''));
                formData.append('imgid', currentImageData.value['imgid'].toString());
                formData.append('imageData', JSON.stringify(currentImageEditData.value));
                formData.append('action', 'updateImageRecord');
                fetch(imageApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    if(res && Number(res) === 1){
                        processSubprocessSuccessResponse(currentProcess.value, true, 'Complete');
                    }
                    else{
                        processSubprocessErrorResponse(currentProcess.value, 'Error saving data', true);
                    }
                    totalImageCount.value--;
                    processCurrentImageDataArr();
                });
            }
            else{
                totalImageCount.value--;
                processSuccessResponse(true);
                processCurrentImageDataArr();
            }
        }

        function processCurrentImageOriginal() {
            if((selectedImportType.value === 'all' || selectedImportType.value === 'images' || selectedImportType.value === 'original') && currentImageData.value['originalurl'] && !currentImageData.value['originalurl'].startsWith('/')){
                let filename = getImageFilenameFromUrl(currentImageData.value['originalurl']);
                if(filename){
                    uploadImage(currentImageData.value['originalurl'], filename, getMediaUploadPath(currentImageData.value), (res) => {
                        if(res){
                            currentImageEditData.value['originalurl'] = res;
                            currentImageEditData.value['sourceurl'] = currentImageData.value['originalurl'];
                        }
                        processCurrentImageEditData();
                    });
                }
                else{
                    getUrlTargetFilename(currentImageData.value['originalurl'], (name) => {
                        if(name){
                            uploadImage(currentImageData.value['originalurl'], name, getMediaUploadPath(currentImageData.value), (res) => {
                                if(res){
                                    currentImageEditData.value['originalurl'] = res;
                                    currentImageEditData.value['sourceurl'] = currentImageData.value['originalurl'];
                                }
                                processCurrentImageEditData();
                            });
                        }
                        else{
                            processCurrentImageEditData();
                        }
                    });
                }
            }
            else{
                processCurrentImageEditData();
            }
        }

        function processCurrentImageThumbnail() {
            if((selectedImportType.value === 'all' || selectedImportType.value === 'images' || selectedImportType.value === 'thumbnail') && currentImageData.value['thumbnailurl'] && !currentImageData.value['thumbnailurl'].startsWith('/')){
                let filename = getImageFilenameFromUrl(currentImageData.value['thumbnailurl']);
                if(filename){
                    uploadImage(currentImageData.value['thumbnailurl'], filename, getMediaUploadPath(currentImageData.value), (res) => {
                        if(res){
                            currentImageEditData.value['thumbnailurl'] = res;
                        }
                        processCurrentImageWeb();
                    });
                }
                else{
                    getUrlTargetFilename(currentImageData.value['thumbnailurl'], (name) => {
                        if(name){
                            uploadImage(currentImageData.value['thumbnailurl'], name, getMediaUploadPath(currentImageData.value), (res) => {
                                if(res){
                                    currentImageEditData.value['thumbnailurl'] = res;
                                }
                                processCurrentImageWeb();
                            });
                        }
                        else{
                            processCurrentImageWeb();
                        }
                    });
                }
            }
            else{
                processCurrentImageWeb();
            }
        }

        function processCurrentImageWeb() {
            if((selectedImportType.value === 'all' || selectedImportType.value === 'images' || selectedImportType.value === 'web') && currentImageData.value['url'] && !currentImageData.value['url'].startsWith('/')){
                let filename = getImageFilenameFromUrl(currentImageData.value['url']);
                if(filename){
                    uploadImage(currentImageData.value['url'], filename, getMediaUploadPath(currentImageData.value), (res) => {
                        if(res){
                            currentImageEditData.value['url'] = res;
                            if(!currentImageData.value['originalurl']){
                                currentImageEditData.value['sourceurl'] = currentImageData.value['url'];
                            }
                        }
                        processCurrentImageOriginal();
                    });
                }
                else{
                    getUrlTargetFilename(currentImageData.value['url'], (name) => {
                        if(name){
                            uploadImage(currentImageData.value['url'], name, getMediaUploadPath(currentImageData.value), (res) => {
                                if(res){
                                    currentImageEditData.value['url'] = res;
                                    if(!currentImageData.value['originalurl']){
                                        currentImageEditData.value['sourceurl'] = currentImageData.value['url'];
                                    }
                                }
                                processCurrentImageOriginal();
                            });
                        }
                        else{
                            processCurrentImageOriginal();
                        }
                    });
                }
            }
            else{
                processCurrentImageOriginal();
            }
        }

        function processCurrentMediaDataArr() {
            if(processCancelling.value){
                adjustUIEnd();
            }
            else{
                if(currentMediaDataArr.value.length > 0){
                    currentMediaData.value = Object.assign({}, currentMediaDataArr.value[0]);
                    currentMediaDataArr.value.splice(0, 1);
                    currentProcess.value = ('media' + currentMediaData.value['mediaid']);
                    const text = 'Processing media ID: ' + currentMediaData.value['mediaid'];
                    addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value,'multi', text));
                    processSuccessResponse(false);
                    processCurrentMediaUrl();
                }
                else{
                    processMediaIdArr();
                }
            }
        }

        function processCurrentMediaDelete() {
            const text = 'Removing media record due to a broken link';
            addSubprocessToProcessorDisplay(currentProcess.value, 'text', text);
            const formData = new FormData();
            formData.append('collid', (props.collectionId ? props.collectionId.toString() : ''));
            formData.append('mediaid', currentMediaData.value['mediaid'].toString());
            formData.append('action', 'deleteMediaRecord');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(res && Number(res) === 1){
                    processSubprocessSuccessResponse(currentProcess.value, true, 'Complete');
                }
                else{
                    processSubprocessErrorResponse(currentProcess.value, 'Error removing media record', true);
                }
                totalMediaCount.value--;
                processCurrentMediaDataArr();
            });
        }

        function processCurrentMediaEditData(data) {
            if(
                removeBrokenLinksVal.value &&
                (!currentMediaData.value['accessuri'] || !currentMediaData.value['accessuri'].startsWith('/')) &&
                (!data.hasOwnProperty('accessuri') || !data['accessuri'])
            ){
                processCurrentMediaDelete();
            }
            else if(data && data.hasOwnProperty('accessuri') && data['accessuri']){
                const text = 'Saving url for imported file';
                addSubprocessToProcessorDisplay(currentProcess.value, 'text', text);
                const formData = new FormData();
                formData.append('collid', (props.collectionId ? props.collectionId.toString() : ''));
                formData.append('mediaid', currentMediaData.value['mediaid'].toString());
                formData.append('mediaData', JSON.stringify(data));
                formData.append('action', 'updateMediaRecord');
                fetch(mediaApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    if(res && Number(res) === 1){
                        processSubprocessSuccessResponse(currentProcess.value, true, 'Complete');
                    }
                    else{
                        processSubprocessErrorResponse(currentProcess.value, 'Error saving data', true);
                    }
                    totalMediaCount.value--;
                    processCurrentMediaDataArr();
                });
            }
            else{
                totalMediaCount.value--;
                processSuccessResponse(true);
                processCurrentMediaDataArr();
            }
        }

        function processCurrentMediaUrl() {
            if((selectedImportType.value === 'all' || selectedImportType.value === 'media') && currentMediaData.value['accessuri'] && !currentMediaData.value['accessuri'].startsWith('/')){
                let filename = getMediaFilenameFromUrl(currentMediaData.value['accessuri']);
                if(filename){
                    uploadMedia(currentMediaData.value['accessuri'], filename, getMediaUploadPath(currentMediaData.value), (res) => {
                        if(res){
                            processCurrentMediaEditData(res ? {accessuri: res, sourceurl: currentMediaData.value['accessuri']} : null);
                        }
                        else{
                            processCurrentMediaDataArr();
                        }
                    });
                }
                else{
                    getUrlTargetFilename(currentMediaData.value['accessuri'], (name) => {
                        if(name){
                            uploadMedia(currentMediaData.value['accessuri'], name, getMediaUploadPath(currentMediaData.value), (res) => {
                                if(res){
                                    processCurrentMediaEditData(res ? {accessuri: res, sourceurl: currentMediaData.value['accessuri']} : null);
                                }
                                else{
                                    processCurrentMediaDataArr();
                                }
                            });
                        }
                        else{
                            processCurrentMediaDataArr();
                        }
                    });
                }
            }
            else{
                processSuccessResponse(true);
                processCurrentMediaDataArr();
            }
        }

        function processImageIdArr() {
            currentImageIdArr.value.length = 0;
            currentImageDataArr.value.length = 0;
            if(imageIdArr.value.length > 0){
                currentImageIdArr.value = imageIdArr.value.length > 1000 ? imageIdArr.value.slice(0, 1000) : imageIdArr.value.slice();
                if(imageIdArr.value.length > 1000){
                    imageIdArr.value.splice(0, 1000);
                }
                else{
                    imageIdArr.value.length = 0;
                }
                setCurrentImageDataArr();
            }
            else{
                processMediaIdArr();
            }
        }

        function processLimitChange(value) {
            if(Number(value) === 0){
                processLimit.value = null;
            }
            else{
                processLimit.value = Number(value);
            }
        }

        function processMediaIdArr() {
            currentMediaIdArr.value.length = 0;
            currentMediaDataArr.value.length = 0;
            if(mediaIdArr.value.length > 0){
                currentMediaIdArr.value = mediaIdArr.value.length > 1000 ? mediaIdArr.value.slice(0, 1000) : mediaIdArr.value.slice();
                if(mediaIdArr.value.length > 1000){
                    mediaIdArr.value.splice(0, 1000);
                }
                else{
                    mediaIdArr.value.length = 0;
                }
                setCurrentMediaDataArr();
            }
            else{
                adjustUIEnd();
            }
        }

        function processorDisplayScrollDown() {
            scrollProcess.value = 'scrollDown';
            processorDisplayArr.length = 0;
            processorDisplayCurrentIndex.value++;
            const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
            newData.forEach((data) => {
                processorDisplayArr.push(data);
            });
            resetScrollProcess();
        }

        function processorDisplayScrollUp() {
            scrollProcess.value = 'scrollUp';
            processorDisplayArr.length = 0;
            processorDisplayCurrentIndex.value--;
            const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
            newData.forEach((data) => {
                processorDisplayArr.push(data);
            });
            resetScrollProcess();
        }

        function processSubprocessErrorResponse(id, text, complete) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === id);
            if(parentProcObj){
                if(complete){
                    parentProcObj['current'] = false;
                    parentProcObj['loading'] = false;
                }
                const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                if(subProcObj){
                    subProcObj['loading'] = false;
                    subProcObj['result'] = 'error';
                    subProcObj['resultText'] = text;
                }
            }
        }

        function processSubprocessSuccessResponse(id, complete, text = null) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === id);
            if(parentProcObj){
                parentProcObj['current'] = !complete;
                const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                if(subProcObj){
                    subProcObj['loading'] = false;
                    subProcObj['result'] = 'success';
                    subProcObj['resultText'] = text;
                }
            }
        }

        function processSuccessResponse(complete, text = null) {
            const procObj = processorDisplayArr.find(proc => proc['current'] === true);
            if(procObj){
                procObj['current'] = !complete;
                if(procObj['loading'] === true){
                    procObj['loading'] = false;
                    procObj['result'] = 'success';
                    procObj['resultText'] = text;
                }
            }
        }

        function resetScrollProcess() {
            setTimeout(() => {
                scrollProcess.value = null;
            }, 200);
        }

        function setCurrentImageDataArr() {
            if(processCancelling.value){
                adjustUIEnd();
            }
            else{
                const text = 'Getting data for next batch of images';
                currentProcess.value = ('setCurrentImageDataArr' + imageIdArr.value.length);
                addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value, 'single', text));
                const formData = new FormData();
                formData.append('property', 'idArr');
                formData.append('value', JSON.stringify(currentImageIdArr.value));
                formData.append('admin', '1');
                formData.append('action', 'getImageArrByProperty');
                fetch(imageApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    processSuccessResponse(true, 'Complete');
                    currentImageDataArr.value = data;
                    processCurrentImageDataArr();
                });
            }
        }

        function setCurrentMediaDataArr() {
            if(processCancelling.value){
                adjustUIEnd();
            }
            else{
                const text = 'Getting data for next batch of media files';
                currentProcess.value = ('setCurrentMediaDataArr' + mediaIdArr.value.length);
                addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value, 'single', text));
                const formData = new FormData();
                formData.append('property', 'idArr');
                formData.append('value', JSON.stringify(currentMediaIdArr.value));
                formData.append('admin', '1');
                formData.append('action', 'getMediaArrByProperty');
                fetch(mediaApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    processSuccessResponse(true, 'Complete');
                    currentMediaDataArr.value = data;
                    processCurrentMediaDataArr();
                });
            }
        }

        function setImageIdArr() {
            if(processCancelling.value){
                adjustUIEnd();
            }
            else{
                if(currentProcess.value !== 'setImageIdArr'){
                    const text = 'Getting image identifiers for images that need to be imported';
                    currentProcess.value = 'setImageIdArr';
                    addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value, 'single', text));
                }
                const formData = new FormData();
                formData.append('options', JSON.stringify(options.value));
                formData.append('action', 'getExternalImageIdArr');
                fetch(imageApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    const newIdArr = imageIdArr.value.concat(data);
                    imageIdArr.value = newIdArr.slice();
                    if((!processLimit.value && data.length < idLoadingCnt.value) || processLimit.value === totalFiles.value){
                        totalImageCount.value = imageIdArr.value.length;
                        processSuccessResponse(true, 'Complete');
                        loadingIndex.value = 0;
                        if(selectedImportType.value === 'all' && (!processLimit.value || processLimit.value < totalFiles.value)){
                            setMediaIdArr();
                        }
                        else{
                            processImageIdArr();
                        }
                    }
                    else{
                        loadingIndex.value++;
                        setImageIdArr();
                    }
                });
            }
        }

        function setMediaIdArr() {
            if(processCancelling.value){
                adjustUIEnd();
            }
            else{
                if(currentProcess.value !== 'setMediaIdArr'){
                    const text = 'Getting media file identifiers for media files that need to be imported';
                    currentProcess.value = 'setMediaIdArr';
                    addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value, 'single', text));
                }
                const formData = new FormData();
                formData.append('options', JSON.stringify(options.value));
                formData.append('action', 'getExternalMediaIdArr');
                fetch(mediaApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    const newIdArr = mediaIdArr.value.concat(data);
                    mediaIdArr.value = newIdArr.slice();
                    if((!processLimit.value && data.length < idLoadingCnt.value) || processLimit.value === totalFiles.value){
                        totalMediaCount.value = mediaIdArr.value.length;
                        processSuccessResponse(true, 'Complete');
                        loadingIndex.value = 0;
                        processImageIdArr();
                    }
                    else{
                        loadingIndex.value++;
                        setMediaIdArr();
                    }
                });
            }
        }

        function setScroller(info) {
            if((currentProcess.value || scrollProcess.value) && info.hasOwnProperty('verticalSize') && info.verticalSize > 610 && info.verticalSize !== procDisplayScrollHeight.value){
                procDisplayScrollHeight.value = info.verticalSize;
                if(scrollProcess.value === 'scrollDown'){
                    procDisplayScrollAreaRef.value.setScrollPosition('vertical', 0);
                }
                else{
                    procDisplayScrollAreaRef.value.setScrollPosition('vertical', info.verticalSize);
                }
            }
        }

        function uploadImage(sourceurl, filename, uploadpath, callback) {
            const text = 'Importing ' + filename;
            addSubprocessToProcessorDisplay(currentProcess.value, 'text', text);
            const formData = new FormData();
            formData.append('sourceurl', sourceurl);
            formData.append('filename', filename);
            formData.append('uploadpath', uploadpath);
            formData.append('action', 'transferExternalImageFileToServer');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(res){
                    processSubprocessSuccessResponse(currentProcess.value, false, 'Complete');
                }
                else{
                    processSubprocessErrorResponse(currentProcess.value, 'Error importing file', false);
                }
                callback(res);
            });
        }

        function uploadMedia(sourceurl, filename, uploadpath, callback) {
            const text = 'Importing ' + currentMediaData.value['accessuri'].split('/').pop().toString();
            addSubprocessToProcessorDisplay(currentProcess.value, 'text', text);
            const formData = new FormData();
            formData.append('sourceurl', sourceurl);
            formData.append('filename', filename);
            formData.append('uploadpath', uploadpath);
            formData.append('action', 'transferExternalMediaFileToServer');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(res){
                    processSubprocessSuccessResponse(currentProcess.value, false, 'Complete');
                }
                else{
                    processSubprocessErrorResponse(currentProcess.value, 'Error importing file', true);
                }
                callback(res);
            });
        }

        return {
            currentProcess,
            importTypeOptions,
            procDisplayScrollAreaRef,
            processCancelling,
            processLimit,
            processorDisplayArr,
            processorDisplayCurrentIndex,
            processorDisplayIndex,
            removeBrokenLinksVal,
            selectedImportType,
            totalImageCount,
            totalMediaCount,
            cancelProcess,
            initializeProcess,
            processLimitChange,
            processorDisplayScrollDown,
            processorDisplayScrollUp,
            setScroller
        }
    }
};
