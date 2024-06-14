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
                                    <single-scientific-common-name-auto-complete :sciname="determinationEditData['sciname']" label="Scientific Name" @update:sciname="processScientificNameChange"></single-scientific-common-name-auto-complete>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <text-field-input-element :disabled="Number(determinationEditData['tid']) > 0" label="Author" :value="determinationEditData['scientificnameauthorship']" @update:value="(value) => processDeterminationEditDataChange('scientificnameauthorship', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <text-field-input-element label="ID Qualifier" :value="determinationEditData['identificationqualifier']" @update:value="(value) => processDeterminationEditDataChange('identificationqualifier', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-8">
                                    <text-field-input-element label="Identified By" :value="determinationEditData['identifiedby']" @update:value="(value) => processDeterminationEditDataChange('identifiedby', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <text-field-input-element label="Date Identified" :value="determinationEditData['dateidentified']" @update:value="(value) => processDeterminationEditDataChange('dateidentified', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="ID References" :value="determinationEditData['identificationreferences']" @update:value="(value) => processDeterminationEditDataChange('identificationreferences', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="ID Remarks" :value="determinationEditData['identificationremarks']" @update:value="(value) => processDeterminationEditDataChange('identificationremarks', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div v-if="Number(determinationId) === 0" class="row justify-start">
                                <div>
                                    <checkbox-input-element label="Make this the current determination" :value="determinationEditData['iscurrent']" @update:value="updateMakeIsCurrent"></checkbox-input-element>
                                </div>
                            </div>
                            <div class="row justify-start">
                                <div>
                                    <checkbox-input-element label="Add to Annotation Queue" :value="determinationEditData['printqueue']" @update:value="updateAddToAnnotationQueue"></checkbox-input-element>
                                </div>
                            </div>
                            <div v-if="Number(determinationId) > 0" class="row justify-end q-gutter-md">
                                <div v-if="Number(determinationEditData['iscurrent']) !== 1">
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
        const { showNotification } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const blankDetermination = {
            detid: 0,
            identifiedby: null,
            dateidentified: null,
            sciname: null,
            verbatimscientificname: null,
            tid: null,
            scientificnameauthorship: null,
            identificationqualifier: null,
            iscurrent: 0,
            identificationreferences: null,
            identificationremarks: null,
            sortsequence: 10,
            printqueue: 0
        };
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const determinationData = Vue.ref({});
        const determinationEditData = Vue.ref({});
        const determinationValid = Vue.computed(() => {
            return (
                determinationEditData.value['sciname'] &&
                determinationEditData.value['identifiedby'] &&
                determinationEditData.value['dateidentified'] &&
                Number(determinationEditData.value['sortsequence']) > 0
            );
        });
        const editsExist = Vue.computed(() => {
            let retValue = false;
            const dataKeys = Object.keys(determinationEditData.value);
            dataKeys.forEach(key => {
                if(determinationEditData.value[key] !== determinationData.value[key]){
                    retValue = true;
                }
            });
            return retValue;
        });

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addDetermination() {

        }

        function closePopup() {
            context.emit('close:popup');
        }

        function deleteDetermination() {

        }

        function makeDeterminationCurrent() {

        }

        function processDeterminationEditDataChange(key, value) {
            determinationEditData.value[key] = value;
        }

        function processScientificNameChange(taxon) {
            determinationEditData.value['sciname'] = taxon ? taxon.sciname : null;
            determinationEditData.value['tid'] = taxon ? taxon.tid : null;
            determinationEditData.value['scientificnameauthorship'] = taxon ? taxon.author : null;
        }

        function saveDeterminationEdits() {

        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setDeterminationData() {
            if(Number(props.determinationId) > 0){
                determinationData.value = Object.assign({}, occurrenceStore.getDeterminationData(props.determinationId));
            }
            else{
                determinationData.value = Object.assign({}, blankDetermination);
            }
            determinationEditData.value = Object.assign({}, determinationData.value);
        }

        function updateAddToAnnotationQueue(value) {
            determinationEditData.value['printqueue'] = value ? 1 : 0;
        }

        function updateCollectingEventData(key, value) {
            occurrenceStore.updateCollectingEventEditData(key, value);
        }

        function updateMakeIsCurrent(value) {
            determinationEditData.value['iscurrent'] = value ? 1 : 0;
        }

        Vue.onMounted(() => {
            setContentStyle();
            setDeterminationData();
        });

        return {
            contentRef,
            contentStyle,
            determinationEditData,
            determinationValid,
            editsExist,
            addDetermination,
            closePopup,
            deleteDetermination,
            makeDeterminationCurrent,
            processDeterminationEditDataChange,
            processScientificNameChange,
            saveDeterminationEdits,
            updateAddToAnnotationQueue,
            updateCollectingEventData,
            updateMakeIsCurrent
        }
    }
};
