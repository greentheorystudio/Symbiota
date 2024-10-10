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
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-col-gutter-sm">
                            <div class="row justify-between">
                                <div>
                                    <template v-if="geneticLinkageId > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <template v-if="geneticLinkageId > 0">
                                        <q-btn color="secondary" @click="saveGeneticLinkageEdits();" label="Save Edits" :disabled="!editsExist || !geneticLinkageValid" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="addGeneticLinkage();" label="Add Genetic Record Linkage" :disabled="!geneticLinkageValid" />
                                    </template>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Name" :value="geneticLinkageData['resourcename']" @update:value="(value) => updateGeneticLinkageData('resourcename', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Identifier" :value="geneticLinkageData['identifier']" @update:value="(value) => updateGeneticLinkageData('identifier', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Locus" :value="geneticLinkageData['locus']" @update:value="(value) => updateGeneticLinkageData('locus', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="URL" :value="geneticLinkageData['resourceurl']" @update:value="(value) => updateGeneticLinkageData('resourceurl', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Notes" :value="geneticLinkageData['notes']" @update:value="(value) => updateGeneticLinkageData('notes', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div v-if="Number(geneticLinkageId) > 0" class="row justify-end q-gutter-md">
                                <div>
                                    <q-btn color="negative" @click="deleteGeneticLinkage();" label="Delete Genetic Record Linkage" />
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
            addGeneticLinkage,
            closePopup,
            deleteGeneticLinkage,
            saveGeneticLinkageEdits,
            updateGeneticLinkageData
        }
    }
};
