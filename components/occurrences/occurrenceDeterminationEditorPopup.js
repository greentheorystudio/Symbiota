const occurrenceDeterminationEditorPopup = {
    props: {
        determinationId: {
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
                                    <template v-if="determinationId > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <template v-if="determinationId > 0">
                                        <q-btn color="secondary" @click="saveDeterminationEdits();" label="Save Determination Edits" :disabled="!editsExist || !determinationValid" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="addDetermination();" label="Add Determination" :disabled="!determinationValid" />
                                    </template>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-5">
                                    <single-scientific-common-name-auto-complete :sciname="determinationData['sciname']" label="Scientific Name" @update:sciname="processScientificNameChange"></single-scientific-common-name-auto-complete>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <text-field-input-element :disabled="Number(determinationData['tid']) > 0" label="Author" :value="determinationData['scientificnameauthorship']" @update:value="(value) => updateDeterminationData('scientificnameauthorship', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <text-field-input-element label="ID Qualifier" :value="determinationData['identificationqualifier']" @update:value="(value) => updateDeterminationData('identificationqualifier', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-8">
                                    <text-field-input-element label="Identified By" :value="determinationData['identifiedby']" @update:value="(value) => updateDeterminationData('identifiedby', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <text-field-input-element label="Date Identified" :value="determinationData['dateidentified']" @update:value="(value) => updateDeterminationData('dateidentified', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="ID References" :value="determinationData['identificationreferences']" @update:value="(value) => updateDeterminationData('identificationreferences', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="ID Remarks" :value="determinationData['identificationremarks']" @update:value="(value) => updateDeterminationData('identificationremarks', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div v-if="Number(determinationId) === 0" class="row justify-start">
                                <div>
                                    <checkbox-input-element label="Make this the current determination" :value="determinationData['iscurrent']" @update:value="(value) => updateDeterminationData('iscurrent', (value ? 1 : 0))"></checkbox-input-element>
                                </div>
                            </div>
                            <div class="row justify-start">
                                <div>
                                    <checkbox-input-element label="Add to Annotation Queue" :value="determinationData['printqueue']" @update:value="(value) => updateDeterminationData('printqueue', (value ? 1 : 0))"></checkbox-input-element>
                                </div>
                            </div>
                            <div v-if="Number(determinationId) > 0 && Number(determinationData['iscurrent']) !== 1" class="row justify-end q-gutter-md">
                                <div>
                                    <q-btn color="primary" @click="makeDeterminationCurrent();" label="Make Determination Current" />
                                </div>
                                <div>
                                    <q-btn color="negative" @click="deleteDetermination();" label="Delete Determination" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const determinationData = Vue.computed(() => occurrenceStore.getDeterminationData);
        const determinationValid = Vue.computed(() => occurrenceStore.getDeterminationValid);
        const editsExist = Vue.computed(() => occurrenceStore.getDeterminationEditsExist);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addDetermination() {
            occurrenceStore.createOccurrenceDeterminationRecord((newDetId) => {
                if(newDetId > 0){
                    showNotification('positive','Determination added successfully.');
                }
                else{
                    showNotification('negative', 'There was an error adding the new determination.');
                }
            });
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function deleteDetermination() {
            occurrenceStore.deleteOccurrenceDeterminationRecord((res) => {
                if(res === 1){
                    showNotification('positive','Determination has been deleted.');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error deleting the determination.');
                }
            });
        }

        function makeDeterminationCurrent() {
            showWorking('Making current determination...');
            occurrenceStore.makeDeterminationCurrent((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Current determination changed.');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error changing the current determination.');
                }
            });
        }

        function processScientificNameChange(taxon) {
            updateDeterminationData('sciname', (taxon ? taxon.sciname : null));
            updateDeterminationData('tid', (taxon ? taxon.tid : null));
            updateDeterminationData('scientificnameauthorship', (taxon ? taxon.author : null));
        }

        function saveDeterminationEdits() {
            showWorking('Saving edits...');
            occurrenceStore.updateOccurrenceDeterminationRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the determination edits.');
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

        function updateDeterminationData(key, value) {
            occurrenceStore.updateDeterminationEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            occurrenceStore.setCurrentDeterminationRecord(props.determinationId);
        });

        return {
            contentRef,
            contentStyle,
            determinationData,
            determinationValid,
            editsExist,
            addDetermination,
            closePopup,
            deleteDetermination,
            makeDeterminationCurrent,
            processScientificNameChange,
            saveDeterminationEdits,
            updateDeterminationData
        }
    }
};
