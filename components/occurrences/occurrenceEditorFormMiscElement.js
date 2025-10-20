const occurrenceEditorFormMiscElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                    Occurrence
                </div>
                <div class="row justify-between">
                    <div class="col-grow">
                        <occurrence-associated-taxa-input-element :definition="occurrenceFieldDefinitions['associatedtaxa']" label="Associated Taxa" :maxlength="occurrenceFields['associatedtaxa'] ? occurrenceFields['associatedtaxa']['length'] : 0" :value="occurrenceData.associatedtaxa" @update:value="(value) => updateOccurrenceData('associatedtaxa', value)"></occurrence-associated-taxa-input-element>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-sm">
                    <div class="col-12 col-sm-6 col-md-grow">
                        <template v-if="controlledVocabularies.hasOwnProperty('lifestage') && controlledVocabularies['lifestage'] && controlledVocabularies['lifestage'].length > 0 && (!occurrenceData.lifestage || controlledVocabularies['lifestage'].includes(occurrenceData.lifestage))">
                            <selector-input-element :definition="occurrenceFieldDefinitions['lifestage']" label="Life Stage" :options="controlledVocabularies['lifestage']" :value="occurrenceData.lifestage" @update:value="(value) => updateOccurrenceData('lifestage', value)" :clearable="true"></selector-input-element>
                        </template>
                        <template v-else>
                            <text-field-input-element :definition="occurrenceFieldDefinitions['lifestage']" label="Life Stage" :maxlength="occurrenceFields['lifestage'] ? occurrenceFields['lifestage']['length'] : 0" :value="occurrenceData.lifestage" @update:value="(value) => updateOccurrenceData('lifestage', value)"></text-field-input-element>
                        </template>
                    </div>
                    <div class="col-12 col-sm-6 col-md-grow">
                        <template v-if="controlledVocabularies.hasOwnProperty('sex') && controlledVocabularies['sex'] && controlledVocabularies['sex'].length > 0 && (!occurrenceData.sex || controlledVocabularies['sex'].includes(occurrenceData.sex))">
                            <selector-input-element :definition="occurrenceFieldDefinitions['sex']" label="Sex" :options="controlledVocabularies['sex']" :value="occurrenceData.sex" @update:value="(value) => updateOccurrenceData('sex', value)" :clearable="true"></selector-input-element>
                        </template>
                        <template v-else>
                            <text-field-input-element :definition="occurrenceFieldDefinitions['sex']" label="Sex" :maxlength="occurrenceFields['sex'] ? occurrenceFields['sex']['length'] : 0" :value="occurrenceData.sex" @update:value="(value) => updateOccurrenceData('sex', value)"></text-field-input-element>
                        </template>
                    </div>
                    <template v-if="occurrenceEntryFormat === 'benthic'">
                        <div class="col-12 col-sm-6 col-md-grow">
                            <text-field-input-element :disabled="true" data-type="int" :definition="occurrenceFieldDefinitions['rep']" label="Rep" :maxlength="occurrenceFields['rep'] ? occurrenceFields['rep']['length'] : 0" min-value="1" :max-value="eventData.repcount" :value="occurrenceData.rep" @update:value="(value) => updateOccurrenceData('rep', value)"></text-field-input-element>
                        </div>
                    </template>
                    <div class="col-12 col-sm-6 col-md-grow">
                        <template v-if="occurrenceEntryFormat === 'benthic'">
                            <text-field-input-element :disabled="true" data-type="int" :definition="occurrenceFieldDefinitions['individualcount']" label="Individual Count" :maxlength="occurrenceFields['individualcount'] ? occurrenceFields['individualcount']['length'] : 0" :value="occurrenceData.individualcount" min-value="0" @update:value="(value) => updateOccurrenceData('individualcount', value)"></text-field-input-element>
                        </template>
                        <template v-else>
                            <text-field-input-element :definition="occurrenceFieldDefinitions['individualcount']" label="Individual Count" :maxlength="occurrenceFields['individualcount'] ? occurrenceFields['individualcount']['length'] : 0" :value="occurrenceData.individualcount" @update:value="(value) => updateOccurrenceData('individualcount', value)"></text-field-input-element>
                        </template>
                    </div>
                </div>
                <div class="row">
                    <div class="col-grow">
                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['occurrenceremarks']" label="Occurrence Remarks" :value="occurrenceData.occurrenceremarks" @update:value="(value) => updateOccurrenceData('occurrenceremarks', value)"></text-field-input-element>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-sm">
                    <div class="col-12 col-sm-4">
                        <selector-input-element :definition="occurrenceFieldDefinitions['basisofrecord']" label="Basis of Record" :options="basisOfRecordOptions" :value="occurrenceData.basisofrecord" @update:value="(value) => updateOccurrenceData('basisofrecord', value)"></selector-input-element>
                    </div>
                    <div class="col-11 col-sm-7">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['typestatus']" label="Type Status" :maxlength="occurrenceFields['typestatus'] ? occurrenceFields['typestatus']['length'] : 0" :value="occurrenceData.typestatus" @update:value="(value) => updateOccurrenceData('typestatus', value)"></text-field-input-element>
                    </div>
                    <div class="col-1 row justify-end self-center">
                        <div>
                            <template v-if="showExtendedForm">
                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = false" icon="fas fa-minus" dense></q-btn>
                            </template>
                            <template v-else>
                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = true" icon="fas fa-plus" dense></q-btn>
                            </template>
                        </div>
                    </div>
                </div>
                <template v-if="showExtendedForm">
                    <div class="row justify-between q-col-gutter-sm">
                        <div class="col-12 col-sm-6 col-md-5">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['reproductivecondition']" label="Reproductive Condition" :maxlength="occurrenceFields['reproductivecondition'] ? occurrenceFields['reproductivecondition']['length'] : 0" :value="occurrenceData.reproductivecondition" @update:value="(value) => updateOccurrenceData('reproductivecondition', value)"></text-field-input-element>
                        </div>
                        <div class="col-12 col-sm-6 col-md-5">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['establishmentmeans']" label="Establishment Means" :maxlength="occurrenceFields['establishmentmeans'] ? occurrenceFields['establishmentmeans']['length'] : 0" :value="occurrenceData.establishmentmeans" @update:value="(value) => updateOccurrenceData('establishmentmeans', value)"></text-field-input-element>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <checkbox-input-element :definition="occurrenceFieldDefinitions['cultivationstatus']" label="Cultivated" :value="occurrenceData.cultivationstatus" @update:value="updateCultivationStatusSetting"></checkbox-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['dynamicproperties']" label="Dynamic Properties" :maxlength="occurrenceFields['dynamicproperties'] ? occurrenceFields['dynamicproperties']['length'] : 0" :value="occurrenceData.dynamicproperties" @update:value="(value) => updateOccurrenceData('dynamicproperties', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['verbatimattributes']" label="Verbatim Attributes" :maxlength="occurrenceFields['verbatimattributes'] ? occurrenceFields['verbatimattributes']['length'] : 0" :value="occurrenceData.verbatimattributes" @update:value="(value) => updateOccurrenceData('verbatimattributes', value)"></text-field-input-element>
                        </div>
                    </div>
                </template>
            </q-card-section>
        </q-card>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'occurrence-associated-taxa-input-element': occurrenceAssociatedTaxaInputElement,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const occurrenceStore = useOccurrenceStore();
        const searchStore = useSearchStore();

        const basisOfRecordOptions = Vue.computed(() => occurrenceStore.getBasisOfRecordOptions);
        const controlledVocabularies = Vue.computed(() => occurrenceStore.getOccurrenceFieldControlledVocabularies);
        const eventData = Vue.computed(() => occurrenceStore.getCollectingEventData);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const showExtendedForm = Vue.ref(false);

        function processConfirmation(confirmed) {
            if(confirmed){
                occurrenceStore.evaluateOccurrenceForDeletion(occId.value, (data) => {
                    if(Number(data['images']) === 0 && Number(data['media']) === 0 && Number(data['checklists']) === 0 && Number(data['genetic']) === 0){
                        occurrenceStore.deleteOccurrenceRecord(occId.value, (res) => {
                            if(res === 0){
                                showNotification('negative', ('An error occurred while deleting this record.'));
                            }
                            else{
                                searchStore.removeOccidFromOccidArrs(occId.value);
                                occurrenceStore.setCollectingEventBenthicData();
                            }
                        });
                    }
                    else{
                        showNotification('negative', ('This record cannot be deleted because it has associated images, media, checklists, or genetic linkages.'));
                    }
                });
            }
        }

        function updateCultivationStatusSetting(value) {
            if(Number(value) === 1){
                updateOccurrenceData('cultivationstatus', value);
                if(!occurrenceData.value['establishmentmeans']){
                    updateOccurrenceData('establishmentmeans', 'Cultivated');
                }
            }
            else{
                updateOccurrenceData('cultivationstatus', '0');
                if(occurrenceData.value['establishmentmeans'] === 'Cultivated'){
                    updateOccurrenceData('establishmentmeans', null);
                }
            }
        }

        function updateOccurrenceData(key, value) {
            occurrenceStore.updateOccurrenceEditData(key, value);
        }

        return {
            basisOfRecordOptions,
            controlledVocabularies,
            eventData,
            occurrenceData,
            occurrenceEntryFormat,
            occurrenceFields,
            occurrenceFieldDefinitions,
            showExtendedForm,
            processConfirmation,
            updateCultivationStatusSetting,
            updateOccurrenceData
        }
    }
};
