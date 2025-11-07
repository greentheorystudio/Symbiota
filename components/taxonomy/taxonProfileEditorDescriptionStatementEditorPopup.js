const taxonProfileEditorDescriptionStatementEditorPopup = {
    props: {
        blockId: {
            type: Number,
            default: 0
        },
        showPopup: {
            type: Boolean,
            default: false
        },
        statementId: {
            type: Number,
            default: 0
        }
    },
    template: `
        <q-dialog class="z-max" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
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
                                    <template v-if="statementId > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <template v-if="statementId > 0">
                                        <q-btn color="secondary" @click="saveStatementEdits();" label="Save Description Statement Edits" :disabled="!editsExist || !statementValid" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="addStatement();" label="Add Description Statement" :disabled="!statementValid" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Heading" :value="statementData['heading']" @update:value="(value) => updateStatementData('heading', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <checkbox-input-element label="Display Header" :value="statementData['displayheader']" @update:value="(value) => updateStatementData('displayheader', value)"></checkbox-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <wysiwyg-input-element :value="statementData['statement']" @update:value="(value) => updateStatementData('statement', value)"></wysiwyg-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="int" label="Sort Sequence" :value="statementData['sortsequence']" min-value="1" :clearable="false" @update:value="(value) => updateStatementData('sortsequence', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div v-if="Number(statementId) > 0" class="row justify-end q-gutter-md">
                                <div>
                                    <q-btn color="negative" @click="deleteStatement();" label="Delete Description Statement" tabindex="0" />
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
        'checkbox-input-element': checkboxInputElement,
        'confirmation-popup': confirmationPopup,
        'text-field-input-element': textFieldInputElement,
        'wysiwyg-input-element': wysiwygInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const taxaStore = useTaxaStore();

        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => taxaStore.getTaxaDescriptionStatementEditsExist);
        const statementData = Vue.computed(() => taxaStore.getTaxaDescriptionStatementData);
        const statementValid = Vue.computed(() => taxaStore.getTaxaDescriptionStatementValid);
        
        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addStatement() {
            taxaStore.createTaxaDescriptionStatementRecord(props.blockId, (newBlockId) => {
                if(newBlockId > 0){
                    showNotification('positive','Description statement added successfully.');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error adding the new description statement.');
                }
            });
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function deleteStatement() {
            const confirmText = 'Are you sure you want to delete this description statement? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    taxaStore.deleteTaxaDescriptionStatementRecord((res) => {
                        if(res === 1){
                            showNotification('positive','Description statement has been deleted.');
                            context.emit('close:popup');
                        }
                        else{
                            showNotification('negative', 'There was an error deleting the description statement.');
                        }
                    });
                }
            }});
        }

        function saveStatementEdits() {
            showWorking('Saving edits...');
            taxaStore.updateTaxaDescriptionStatementRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the description statement edits.');
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

        function updateStatementData(key, value) {
            taxaStore.updateTaxaDescriptionStatementEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            taxaStore.setCurrentTaxaDescriptionStatementRecord(props.blockId, props.statementId);
        });

        return {
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            statementData,
            statementValid,
            addStatement,
            closePopup,
            deleteStatement,
            saveStatementEdits,
            updateStatementData
        }
    }
};
