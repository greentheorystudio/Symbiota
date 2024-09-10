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
                                <image-record-info-block :coll-id="collId" :image-data="image" :editor="true" @image:updated="processMediaUpdate"></image-record-info-block>
                            </template>
                        </div>
                    </template>
                    <template v-if="mediaArr.length > 0">
                        <div class="q-mt-sm column q-gutter-sm">
                            <div class="text-h6 text-bold">Media</div>
                            <template v-for="media in mediaArr">
                                <media-record-info-block :coll-id="collId" :media-data="media" :editor="true" @media:updated="processMediaUpdate"></media-record-info-block>
                            </template>
                        </div>
                    </template>
                </template>
                <template v-else>
                    <span class="text-h6 text-bold">There are no media files uploaded for this record.</span>
                </template>
            </div>
        </div>
    `,
    components: {
        'image-record-info-block': imageRecordInfoBlock,
        'media-record-info-block': mediaRecordInfoBlock,
        'media-file-upload-input-element': mediaFileUploadInputElement
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const collectionData = Vue.computed(() => occurrenceStore.getCollectionData);
        const collId = Vue.computed(() => occurrenceStore.getCollId);
        const imageArr = Vue.computed(() => occurrenceStore.getImageArr);
        const mediaArr = Vue.computed(() => occurrenceStore.getMediaArr);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);

        function processMediaUpdate() {
            occurrenceStore.setOccurrenceImageArr();
            occurrenceStore.setOccurrenceMediaArr();
        }

        return {
            collectionData,
            collId,
            imageArr,
            mediaArr,
            occId,
            occurrenceData,
            processMediaUpdate
        }
    }
};
