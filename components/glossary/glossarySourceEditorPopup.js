const glossarySourceEditorPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        },
        taxonId: {
            type: Number,
            default: 0
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="md-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-col-gutter-sm">
                            <div class="row justify-between">
                                <div>
                                    <template v-if="glossarySourceId > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <template v-if="glossarySourceId > 0">
                                        <q-btn color="secondary" @click="saveGlossarySourceEdits();" label="Save Source Edits" :disabled="!editsExist || !glossarySourceValid" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="addGlossarySource();" label="Add Sources" :disabled="!glossarySourceValid" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Terms and definitions contributed by" :value="glossarySourceData['contributorterm']" @update:value="(value) => updateGlossarySourceData('contributorterm', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Images contributed by" :value="glossarySourceData['contributorimage']" @update:value="(value) => updateGlossarySourceData('contributorimage', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Translations by" :value="glossarySourceData['translator']" @update:value="(value) => updateGlossarySourceData('translator', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Translations and images were also sourced from the following references" :value="glossarySourceData['additionalsources']" @update:value="(value) => updateGlossarySourceData('additionalsources', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div v-if="Number(glossarySourceId) > 0" class="row justify-end q-gutter-md">
                                <div>
                                    <q-btn color="negative" @click="deleteGlossarySource();" label="Delete Sources" tabindex="0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const glossaryStore = useGlossaryStore();

        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => glossaryStore.getGlossarySourceEditsExist);
        const glossarySourceData = Vue.computed(() => glossaryStore.getGlossarySourceData);
        const glossarySourceId = Vue.computed(() => glossaryStore.getGlossarySourceID);
        const glossarySourceValid = Vue.computed(() => glossaryStore.getGlossarySourceValid);
        
        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addGlossarySource() {
            updateGlossarySourceData('tid', props.taxonId);
            if(glossaryStore.getGlossarySourceEditsExist){
                glossaryStore.createGlossarySourceRecord((newGlossarySourceId) => {
                    if(newGlossarySourceId > 0){
                        showNotification('positive','Sources added successfully.');
                        context.emit('close:popup');
                    }
                    else{
                        showNotification('negative', 'There was an error adding the new sources.');
                    }
                });
            }
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function deleteGlossarySource() {
            const confirmText = 'Are you sure you want to delete the sources? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    glossaryStore.deleteGlossarySourceRecord((res) => {
                        if(res === 1){
                            glossaryStore.setGlossarySourceData(props.taxonId);
                            showNotification('positive','Sources has been deleted.');
                            context.emit('close:popup');
                        }
                        else{
                            showNotification('negative', 'There was an error deleting the sources.');
                        }
                    });
                }
            }});
        }

        function saveGlossarySourceEdits() {
            showWorking('Saving edits...');
            glossaryStore.updateGlossarySourceRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the source edits.');
                }
                context.emit('close:popup');
            });
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function updateGlossarySourceData(key, value) {
            glossaryStore.updateGlossarySourceEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            glossarySourceData,
            glossarySourceId,
            glossarySourceValid,
            addGlossarySource,
            closePopup,
            deleteGlossarySource,
            saveGlossarySourceEdits,
            updateGlossarySourceData
        }
    }
};
