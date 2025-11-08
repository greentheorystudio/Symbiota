const taxonProfileEditorVernacularEditorPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        },
        vernacularId: {
            type: Number,
            default: 0
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
                                    <template v-if="vernacularId > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <template v-if="vernacularId > 0">
                                        <q-btn color="secondary" @click="saveVernacularEdits();" label="Save Common Name Edits" :disabled="!editsExist || !vernacularValid" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="addVernacular();" label="Add Common Name" :disabled="!vernacularValid" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Common Name" :value="vernacularData['vernacularname']" @update:value="(value) => updateVernacularData('vernacularname', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <single-language-auto-complete label="Language" :language="vernacularData['language']" @update:language="processLanguageChange"></single-language-auto-complete>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Notes" :value="vernacularData['notes']" @update:value="(value) => updateVernacularData('notes', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Source" :value="vernacularData['source']" @update:value="(value) => updateVernacularData('source', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="int" label="Sort Sequence" :value="vernacularData['sortsequence']" min-value="1" :clearable="false" @update:value="(value) => updateVernacularData('sortsequence', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div v-if="Number(vernacularId) > 0" class="row justify-end q-gutter-md">
                                <div>
                                    <q-btn color="negative" @click="deleteVernacular();" label="Delete Description Statement" tabindex="0" />
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

        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => taxaStore.getTaxaVernacularEditsExist);
        const vernacularData = Vue.computed(() => taxaStore.getTaxaVernacularData);
        const vernacularValid = Vue.computed(() => taxaStore.getTaxaVernacularValid);
        
        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addVernacular() {
            taxaStore.createTaxaVernacularRecord((newBlockId) => {
                if(newBlockId > 0){
                    showNotification('positive','Common name added successfully.');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error adding the new common name.');
                }
            });
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function deleteVernacular() {
            const confirmText = 'Are you sure you want to delete this common name? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    taxaStore.deleteTaxaVernacularRecord((res) => {
                        if(res === 1){
                            showNotification('positive','Common name has been deleted.');
                            context.emit('close:popup');
                        }
                        else{
                            showNotification('negative', 'There was an error deleting the common name.');
                        }
                    });
                }
            }});
        }

        function processLanguageChange(langObj) {
            if(langObj){
                updateVernacularData('language', langObj['name']);
                updateVernacularData('langid', langObj['id']);
            }
            else{
                updateVernacularData('language', null);
                updateVernacularData('langid', null);
            }
        }

        function saveVernacularEdits() {
            showWorking('Saving edits...');
            taxaStore.updateTaxaVernacularRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the common name edits.');
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

        function updateVernacularData(key, value) {
            taxaStore.updateTaxaVernacularEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            taxaStore.setCurrentTaxaVernacularRecord(props.vernacularId);
        });

        return {
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            vernacularData,
            vernacularValid,
            addVernacular,
            closePopup,
            deleteVernacular,
            processLanguageChange,
            saveVernacularEdits,
            updateVernacularData
        }
    }
};
