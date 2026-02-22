const occurrenceGeneticRecordLinkageEditorPopup = {
    props: {
        geneticLinkageId: {
            type: Number,
            default: 0
        },
        showPopup: {
            type: Boolean,
            default: false
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
                                    <template v-if="geneticLinkageId > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <template v-if="geneticLinkageId > 0">
                                        <q-btn color="secondary" @click="saveGeneticLinkageEdits();" label="Save Edits" :disabled="!editsExist || !geneticLinkageValid" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="addGeneticLinkage();" label="Add Genetic Record Linkage" :disabled="!geneticLinkageValid" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element :definition="occurrenceGeneticFieldDefinitions['sourcename']" label="Source Name" field="sourcename" :value="geneticLinkageData['sourcename']" @update:value="(value) => updateGeneticLinkageData('sourcename', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element :definition="occurrenceGeneticFieldDefinitions['sourceidentifier']" label="Source Identifier" field="sourceidentifier" :value="geneticLinkageData['sourceidentifier']" @update:value="(value) => updateGeneticLinkageData('sourceidentifier', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element :definition="occurrenceGeneticFieldDefinitions['description']" label="Description" field="description" :value="geneticLinkageData['description']" @update:value="(value) => updateGeneticLinkageData('description', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" :definition="occurrenceGeneticFieldDefinitions['targetgene']" label="Target Gene (Locus)" field="targetgene" :value="geneticLinkageData['targetgene']" @update:value="(value) => updateGeneticLinkageData('targetgene', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" :definition="occurrenceGeneticFieldDefinitions['targetsubfragment']" label="Target Subfragment" field="targetsubfragment" :value="geneticLinkageData['targetsubfragment']" @update:value="(value) => updateGeneticLinkageData('targetsubfragment', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" :definition="occurrenceGeneticFieldDefinitions['dnasequence']" label="DNA Sequence" field="dnasequence" :value="geneticLinkageData['dnasequence']" @update:value="(value) => updateGeneticLinkageData('dnasequence', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" :definition="occurrenceGeneticFieldDefinitions['url']" label="URL" field="url" :value="geneticLinkageData['url']" @update:value="(value) => updateGeneticLinkageData('url', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" :definition="occurrenceGeneticFieldDefinitions['notes']" field="notes" label="Notes" :value="geneticLinkageData['notes']" @update:value="(value) => updateGeneticLinkageData('notes', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" :definition="occurrenceGeneticFieldDefinitions['authors']" field="authors" label="Authors" :value="geneticLinkageData['authors']" @update:value="(value) => updateGeneticLinkageData('authors', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" :definition="occurrenceGeneticFieldDefinitions['authorinstitution']" field="authorinstitution" label="Author Institution" :value="geneticLinkageData['authorinstitution']" @update:value="(value) => updateGeneticLinkageData('authorinstitution', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" :definition="occurrenceGeneticFieldDefinitions['reference']" field="reference" label="Reference" :value="geneticLinkageData['reference']" @update:value="(value) => updateGeneticLinkageData('reference', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div v-if="Number(geneticLinkageId) > 0" class="row justify-end q-gutter-md">
                                <div>
                                    <q-btn color="negative" @click="deleteGeneticLinkage();" label="Delete Genetic Record Linkage" tabindex="0" />
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
        const occurrenceStore = useOccurrenceStore();

        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const geneticLinkageData = Vue.computed(() => occurrenceStore.getGeneticLinkData);
        const geneticLinkageValid = Vue.computed(() => occurrenceStore.getGeneticLinkValid);
        const editsExist = Vue.computed(() => occurrenceStore.getGeneticLinkEditsExist);
        const occurrenceGeneticFieldDefinitions = Vue.computed(() => occurrenceStore.getOccurrenceGeneticFieldDefinitions);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addGeneticLinkage() {
            occurrenceStore.createOccurrenceGeneticLinkageRecord((newLinkId) => {
                if(newLinkId > 0){
                    showNotification('positive','Genetic record linkage added successfully.');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error adding the new genetic record linkage.');
                }
            });
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function deleteGeneticLinkage() {
            const confirmText = 'Are you sure you want to delete this genetic record linkage? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    occurrenceStore.deleteGeneticLinkageRecord((res) => {
                        if(res === 1){
                            showNotification('positive','Genetic record linkage has been deleted.');
                            context.emit('close:popup');
                        }
                        else{
                            showNotification('negative', 'There was an error deleting the genetic record linkage.');
                        }
                    });
                }
            }});
        }

        function saveGeneticLinkageEdits() {
            showWorking('Saving edits...');
            occurrenceStore.updateOccurrenceGeneticLinkageRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the genetic record linkage edits.');
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

        function updateGeneticLinkageData(key, value) {
            occurrenceStore.updateGeneticLinkageEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            occurrenceStore.setCurrentGeneticLinkageRecord(props.geneticLinkageId);
        });

        return {
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            geneticLinkageData,
            geneticLinkageValid,
            occurrenceGeneticFieldDefinitions,
            addGeneticLinkage,
            closePopup,
            deleteGeneticLinkage,
            saveGeneticLinkageEdits,
            updateGeneticLinkageData
        }
    }
};
