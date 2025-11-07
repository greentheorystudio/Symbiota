const taxonProfileEditorDescriptionBlockEditorPopup = {
    props: {
        blockId: {
            type: Number,
            default: 0
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-max" v-model="showPopup" persistent>
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
                                    <template v-if="blockId > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <template v-if="blockId > 0">
                                        <q-btn color="secondary" @click="saveBlockEdits();" label="Save Description Block Edits" :disabled="!editsExist || !blockValid" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="addBlock();" label="Add Description Block" :disabled="!blockValid" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <single-language-auto-complete label="Language" :language="blockData['language']" @update:language="processLanguageChange"></single-language-auto-complete>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Caption" :value="blockData['caption']" @update:value="(value) => updateBlockData('caption', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Source" :value="blockData['source']" @update:value="(value) => updateBlockData('source', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Source URL" :value="blockData['sourceurl']" @update:value="(value) => updateBlockData('sourceurl', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Notes" :value="blockData['notes']" @update:value="(value) => updateBlockData('notes', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="int" label="Display Level" :value="blockData['displaylevel']" min-value="1" :clearable="false" @update:value="(value) => updateBlockData('displaylevel', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div v-if="Number(blockId) > 0" class="row justify-end q-gutter-md">
                                <div>
                                    <q-btn color="negative" @click="deleteBlock();" label="Delete Description Block" tabindex="0" />
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
        'single-language-auto-complete': singleLanguageAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const taxaStore = useTaxaStore();

        const blockData = Vue.computed(() => taxaStore.getTaxaDescriptionBlockData);
        const blockValid = Vue.computed(() => taxaStore.getTaxaDescriptionBlockValid);
        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => taxaStore.getTaxaDescriptionBlockEditsExist);
        
        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addBlock() {
            taxaStore.createTaxaDescriptionBlockRecord((newBlockId) => {
                if(newBlockId > 0){
                    showNotification('positive','Description block added successfully.');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error adding the new description block.');
                }
            });
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function deleteBlock() {
            const confirmText = 'Are you sure you want to delete this description block? This will delete both the description block and any statements associated with it, and cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    taxaStore.deleteTaxaDescriptionBlockRecord((res) => {
                        if(res === 1){
                            showNotification('positive','Description block has been deleted.');
                            context.emit('close:popup');
                        }
                        else{
                            showNotification('negative', 'There was an error deleting the description block.');
                        }
                    });
                }
            }});
        }

        function processLanguageChange(langObj) {
            updateBlockData('language', langObj['iso-1']);
            updateBlockData('langid', langObj['id']);
        }

        function saveBlockEdits() {
            showWorking('Saving edits...');
            taxaStore.updateTaxaDescriptionBlockRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the description block edits.');
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

        function updateBlockData(key, value) {
            taxaStore.updateTaxaDescriptionBlockEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            taxaStore.setCurrentTaxaDescriptionBlockRecord(props.blockId);
        });

        return {
            blockData,
            blockValid,
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            addBlock,
            closePopup,
            deleteBlock,
            processLanguageChange,
            saveBlockEdits,
            updateBlockData
        }
    }
};
