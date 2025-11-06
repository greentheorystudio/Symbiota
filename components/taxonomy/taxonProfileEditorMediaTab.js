const taxonProfileEditorMediaTab = {
    template: `
        <div class="column q-gutter-sm">
            <media-file-upload-input-element :taxon="taxon" :taxon-id="tId" @upload:complete="processMediaUpdate"></media-file-upload-input-element>
            <div class="q-mt-sm">
                <template v-if="imageArr.length > 0 || mediaArr.length > 0">
                    <template v-if="imageArr.length > 0">
                        <div class="q-mt-sm column q-gutter-sm">
                            <div class="text-h6 text-bold">Images</div>
                            <template v-for="image in imageArr">
                                <image-record-info-block :image-data="image" :editor="true" @open:image-editor="openImageEditorPopup"></image-record-info-block>
                            </template>
                        </div>
                    </template>
                    <template v-if="mediaArr.length > 0">
                        <div class="q-mt-sm column q-gutter-sm">
                            <div class="text-h6 text-bold">Media</div>
                            <template v-for="media in mediaArr">
                                <media-record-info-block :media-data="media" :editor="true" @open:media-editor="openMediaEditorPopup"></media-record-info-block>
                            </template>
                        </div>
                    </template>
                </template>
                <template v-else>
                    <div class="q-mt-sm text-body1 text-bold">There are no media files associated with this taxon.</div>
                </template>
            </div>
        </div>
        <template v-if="showImageEditorPopup">
            <image-editor-popup
                :image-id="editImageId"
                :show-popup="showImageEditorPopup" 
                @image:updated="processMediaUpdate" 
                @close:popup="showImageEditorPopup = false"
            ></image-editor-popup>
        </template>
        <template v-if="showMediaEditorPopup">
            <media-editor-popup
                :media-id="editMediaId"
                :show-popup="showMediaEditorPopup"
                :taxon="taxon"
                :upload-path="uploadPath"
                @media:updated="processMediaUpdate"
                @close:popup="showMediaEditorPopup = false"
            ></media-editor-popup>
        </template>
    `,
    components: {
        'image-editor-popup': imageEditorPopup,
        'image-record-info-block': imageRecordInfoBlock,
        'media-editor-popup': mediaEditorPopup,
        'media-record-info-block': mediaRecordInfoBlock,
        'media-file-upload-input-element': mediaFileUploadInputElement
    },
    setup() {
        const taxaStore = useTaxaStore();

        const editImageId = Vue.ref(0);
        const editMediaId = Vue.ref(0);
        const imageArr = Vue.computed(() => taxaStore.getTaxaImageArr);
        const mediaArr = Vue.computed(() => taxaStore.getTaxaMediaArr);
        const showImageEditorPopup = Vue.ref(false);
        const showMediaEditorPopup = Vue.ref(false);
        const taxon = Vue.computed(() => taxaStore.getTaxaData);
        const tId = Vue.computed(() => taxaStore.getTaxaID);

        function openImageEditorPopup(id) {
            editImageId.value = id;
            showImageEditorPopup.value = true;
        }

        function openMediaEditorPopup(id) {
            editMediaId.value = id;
            showMediaEditorPopup.value = true;
        }

        function processMediaUpdate() {
            taxaStore.setTaxaImageArr(taxon.value['tid'], false);
            taxaStore.setTaxaMediaArr(taxon.value['tid'], false);
        }

        return {
            editImageId,
            editMediaId,
            imageArr,
            mediaArr,
            showImageEditorPopup,
            showMediaEditorPopup,
            taxon,
            tId,
            openImageEditorPopup,
            openMediaEditorPopup,
            processMediaUpdate
        }
    }
};
