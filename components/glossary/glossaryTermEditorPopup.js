const glossaryTermEditorPopup = {
    props: {
        glossaryId: {
            type: Number,
            default: 0
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" v-if="!showImageEditorPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <template v-if="Number(glossaryId) > 0">
                            <q-tabs v-model="tab" content-class="bg-grey-3" active-bg-color="grey-4" align="justify">
                                <q-tab name="details" label="Info" no-caps></q-tab>
                                <q-tab name="relatedterms" label="Synonyms/Translations" no-caps></q-tab>
                                <q-tab name="images" label="Images" no-caps></q-tab>
                                <q-tab name="admin" label="Admin" no-caps></q-tab>
                            </q-tabs>
                            <q-separator></q-separator>
                            <q-tab-panels v-model="tab" :style="tabStyle">
                                <q-tab-panel class="q-pa-none" name="details">
                                    <glossary-field-module></glossary-field-module>
                                </q-tab-panel>
                                <q-tab-panel class="q-pa-none" name="relatedterms">
                                    <glossary-editor-related-terms-tab></glossary-editor-related-terms-tab>
                                </q-tab-panel>
                                <q-tab-panel class="q-pa-none" name="images">
                                    <glossary-editor-images-tab @open:image-editor="openImageEditorPopup"></glossary-editor-images-tab>
                                </q-tab-panel>
                                <q-tab-panel class="q-pa-none" name="admin">
                                    <glossary-editor-admin-tab @close:popup="closePopup"></glossary-editor-admin-tab>
                                </q-tab-panel>
                            </q-tab-panels>
                        </template>
                        <template v-else>
                            <glossary-field-module></glossary-field-module>
                        </template>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <template v-if="showImageEditorPopup">
            <glossary-image-editor-popup
                :image-id="editImageId"
                :show-popup="showImageEditorPopup"
                @close:popup="closeImageEditorPopup"
            ></glossary-image-editor-popup>
        </template>
    `,
    components: {
        'glossary-editor-admin-tab': glossaryEditorAdminTab,
        'glossary-editor-images-tab': glossaryEditorImagesTab,
        'glossary-editor-related-terms-tab': glossaryEditorRelatedTermsTab,
        'glossary-field-module': glossaryFieldModule,
        'glossary-image-editor-popup': glossaryImageEditorPopup
    },
    setup(props, context) {
        const glossaryStore = useGlossaryStore();

        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editImageId = Vue.ref(0);
        const showImageEditorPopup = Vue.ref(false);
        const tab = Vue.ref('details');
        const tabStyle = Vue.ref(null);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closeImageEditorPopup() {
            editImageId.value = 0;
            showImageEditorPopup.value = false;
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function openImageEditorPopup(id) {
            editImageId.value = id;
            showImageEditorPopup.value = true;
        }

        function setContentStyle() {
            contentStyle.value = null;
            tabStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
                tabStyle.value = 'height: ' + (contentRef.value.clientHeight - 90) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            glossaryStore.setCurrentGlossaryRecord(props.glossaryId);
        });

        return {
            contentRef,
            contentStyle,
            editImageId,
            showImageEditorPopup,
            tab,
            tabStyle,
            closeImageEditorPopup,
            closePopup,
            openImageEditorPopup
        }
    }
};
