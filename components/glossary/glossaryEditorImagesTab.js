const glossaryEditorImagesTab = {
    template: `
        <div class="q-pa-md column q-gutter-sm">
            <div class="q-mt-sm column q-gutter-sm">
                <div class="row justify-between q-gutter-sm">
                    <div class="text-h6 text-bold">Images</div>
                    <div>
                        <q-btn color="secondary" @click="openEditorPopup(0);" label="Add Image" tabindex="0" />
                    </div>
                </div>
                <template v-if="imageArr.length > 0">
                    <template v-for="imageData in imageArr">
                        <q-card>
                            <q-card-section>
                                <div class="full-width row justify-between q-col-gutter-sm">
                                    <div class="col-3 column no-wrap">
                                        <div class="full-width row justify-center">
                                            <q-img :src="(imageData.url.startsWith('/') ? (clientRoot + imageData.url) : imageData.url)" :fit="contain" class="media-thumbnail"></q-img>
                                        </div>
                                    </div>
                                    <div class="col-8 column no-wrap">
                                        <div v-if="imageData.createdby">
                                            <span class="text-bold">Created By: </span>{{ imageData.createdby }}
                                        </div>
                                        <div v-if="imageData.structures">
                                            <span class="text-bold">Structures: </span>{{ imageData.structures }}
                                        </div>
                                        <div v-if="imageData.notes">
                                            <span class="text-bold">Notes: </span>{{ imageData.notes }}
                                        </div>
                                    </div>
                                    <div v-if="editor" class="col-1 row justify-end">
                                        <div>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openEditorPopup(imageData['glimgid']);" icon="fas fa-edit" dense aria-label="Edit image record" tabindex="0">
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Edit image record
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                    </template>
                </template>
                <template v-else>
                    <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                    <div class="q-pa-md row justify-center text-subtitle1 text-bold">
                        There are currently no images associated with this glossary term
                    </div>
                </template>
            </div>
        </div>
    `,
    setup(_, context) {
        const baseStore = useBaseStore();
        const glossaryStore = useGlossaryStore();

        const clientRoot = baseStore.getClientRoot;
        const imageArr = Vue.computed(() => glossaryStore.getGlossaryImageArr);

        function openEditorPopup(id) {
            context.emit('open:image-editor', id);
        }

        return {
            clientRoot,
            imageArr,
            openEditorPopup
        }
    }
};
