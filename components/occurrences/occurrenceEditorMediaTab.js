const occurrenceEditorMediaTab = {
    template: `
        <div class="column q-gutter-sm">
            <media-file-upload-input-element :collection="collectionData" :occ-id="occId" :taxon-id="occurrenceData.tid" @upload:complete="processMediaUpdate"></media-file-upload-input-element>
            <div class="q-mt-sm">
                <template v-if="imageArr.length > 0 || mediaArr.length > 0">
                    <template v-if="imageArr.length > 0">
                        <div class="q-mt-sm column q-gutter-sm">
                            <div class="text-h6 text-bold">Images</div>
                            <template v-for="image in imageArr">
                                <image-record-info-block :coll-id="collId" :image-data="image" :editor="true" @image:updated="processMediaUpdate" @open:image-editor="openImageEditorPopup"></image-record-info-block>
                            </template>
                        </div>
                    </template>
                    <template v-if="mediaArr.length > 0">
                        <div class="q-mt-sm column q-gutter-sm">
                            <div class="text-h6 text-bold">Media</div>
                            <template v-for="media in mediaArr">
                                <media-record-info-block :coll-id="collId" :media-data="media" :editor="true" @media:updated="processMediaUpdate" @open:media-editor="openMediaEditorPopup"></media-record-info-block>
                            </template>
                        </div>
                    </template>
                </template>
                <template v-else>
                    <div class="q-mt-sm text-body1 text-bold">There are no media files associated with this record.</div>
                </template>
            </div>
        </div>
        <template v-if="showImageEditorPopup">
            <image-editor-popup
                :coll-id="collId"
                :image-id="editImageId"
                :show-popup="showImageEditorPopup" 
                @image:updated="processMediaUpdate" 
                @close:popup="showImageEditorPopup = false"
            ></image-editor-popup>
        </template>
        <template v-if="showMediaEditorPopup">
            <media-editor-popup
                :coll-id="collId"
                :collection="collectionData"
                :media-id="editMediaId"
                :show-popup="showMediaEditorPopup"
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
        const occurrenceStore = useOccurrenceStore();

        const collectionData = Vue.computed(() => occurrenceStore.getCollectionData);
        const collId = Vue.computed(() => occurrenceStore.getCollId);
        const editImageId = Vue.ref(0);
        const editMediaId = Vue.ref(0);
        const imageArr = Vue.computed(() => occurrenceStore.getImageArr);
        const mediaArr = Vue.computed(() => occurrenceStore.getMediaArr);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const showImageEditorPopup = Vue.ref(false);
        const showMediaEditorPopup = Vue.ref(false);

        function openImageEditorPopup(id) {
            editImageId.value = id;
            showImageEditorPopup.value = true;
        }

        function openMediaEditorPopup(id) {
            editMediaId.value = id;
            showMediaEditorPopup.value = true;
        }

        function processMediaUpdate() {
            occurrenceStore.setOccurrenceImageArr();
            occurrenceStore.setOccurrenceMediaArr();
        }

        return {
            collectionData,
            collId,
            editImageId,
            editMediaId,
            imageArr,
            mediaArr,
            occId,
            occurrenceData,
            showImageEditorPopup,
            showMediaEditorPopup,
            openImageEditorPopup,
            openMediaEditorPopup,
            processMediaUpdate
        }
    }
};
