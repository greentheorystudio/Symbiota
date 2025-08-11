const occurrenceMediaFileUploadModule = {
    props: {
        collid: {
            type: Number,
            default: null
        }
    },
    template: `
        <div class="column q-col-gutter-sm">
            <div class="row justify-between q-col-gutter-sm">
                <div class="col-12 col-sm-9">
                    <template v-if="collectionMediaUploadParametersArr.length > 0">
                        <selector-input-element label="Select Upload Profile" :options="collectionMediaUploadParametersArr" option-value="spprid" option-label="title" :value="collectionMediaUploadParametersId" @update:value="(value) => processParameterProfileSelection(value)"></selector-input-element>
                    </template>
                </div>
                <div class="col-12 col-sm-3 row justify-end">
                    <div>
                        <q-btn color="secondary" @click="showCollectionMediaUploadParametersEditorPopup = true" :label="Number(collectionMediaUploadParametersId) > 0 ? 'Edit' : 'Create'" dense />
                    </div>
                </div>
            </div>
            <collection-media-upload-parameters-field-module></collection-media-upload-parameters-field-module>
            <div class="q-mt-sm">
                <media-file-upload-input-element :collection="collectionData" :create-occurrence="configurationData.createOccurrence" :identifier-field="profileData.patternmatchfield" :identifier-reg-ex="profileData.filenamepatternmatch" @upload:complete="processMediaUpdate"></media-file-upload-input-element>
            </div>
        </div>
        <template v-if="showCollectionMediaUploadParametersEditorPopup">
            <collection-media-upload-parameters-editor-popup
                :show-popup="showCollectionMediaUploadParametersEditorPopup"
                @close:popup="showCollectionMediaUploadParametersEditorPopup = false"
            ></collection-media-upload-parameters-editor-popup>
        </template>
    `,
    components: {
        'collection-media-upload-parameters-editor-popup': collectionMediaUploadParametersEditorPopup,
        'collection-media-upload-parameters-field-module': collectionMediaUploadParametersFieldModule,
        'media-file-upload-input-element': mediaFileUploadInputElement,
        'selector-input-element': selectorInputElement
    },
    setup(props) {
        const { showNotification } = useCore();
        const collectionMediaUploadParametersStore = useCollectionMediaUploadParametersStore();
        const collectionStore = useCollectionStore();

        const collectionData = Vue.computed(() => collectionStore.getCollectionData);
        const collectionMediaUploadParametersArr = Vue.computed(() => collectionMediaUploadParametersStore.getCollectionMediaUploadParametersArr);
        const collectionMediaUploadParametersId = Vue.computed(() => collectionMediaUploadParametersStore.getCollectionMediaUploadParametersID);
        const configurationData = Vue.computed(() => collectionMediaUploadParametersStore.getConfigurations);
        const profileData = Vue.computed(() => collectionMediaUploadParametersStore.getCollectionMediaUploadParametersData);
        const showCollectionMediaUploadParametersEditorPopup = Vue.ref(false);

        function processMediaUpdate() {
            showNotification('positive','Upload successful.');
        }

        function processParameterProfileSelection(spprid) {
            collectionMediaUploadParametersStore.setCurrentCollectionMediaUploadParametersRecord(spprid);
        }

        Vue.onMounted(() => {
            if(Number(props.collid) > 0){
                collectionMediaUploadParametersStore.setCollectionMediaUploadParametersArr(props.collid);
            }
        });
        
        return {
            collectionData,
            collectionMediaUploadParametersArr,
            collectionMediaUploadParametersId,
            configurationData,
            profileData,
            showCollectionMediaUploadParametersEditorPopup,
            processMediaUpdate,
            processParameterProfileSelection
        }
    }
};
