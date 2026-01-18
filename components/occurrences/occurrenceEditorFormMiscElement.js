const occurrenceEditorFormMiscElement = {
    template: `
        <q-card v-if="showElement" flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                    Occurrence
                </div>
                <div v-if="occurrenceEntryFormat !== 'replicate' && !editorHideFields.includes('habitat')" class="row">
                    <div class="col-grow">
                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['habitat']" label="Habitat" field="habitat" :value="occurrenceData.habitat" @update:value="(value) => updateOccurrenceData('habitat', value)"></text-field-input-element>
                    </div>
                </div>
                <div v-if="!editorHideFields.includes('substrate')" class="row">
                    <div class="col-grow">
                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['substrate']" label="Substrate" :value="occurrenceData.substrate" @update:value="(value) => updateOccurrenceData('substrate', value)"></text-field-input-element>
                    </div>
                </div>
                <div v-if="!editorHideFields.includes('associatedtaxa')" class="row">
                    <div class="col-grow">
                        <occurrence-associated-taxa-input-element :definition="occurrenceFieldDefinitions['associatedtaxa']" label="Associated Taxa" field="associatedtaxa" :maxlength="occurrenceFields['associatedtaxa'] ? occurrenceFields['associatedtaxa']['length'] : 0" :value="occurrenceData.associatedtaxa" @update:value="(value) => updateOccurrenceData('associatedtaxa', value)"></occurrence-associated-taxa-input-element>
                    </div>
                </div>
                <div v-if="!editorHideFields.includes('lifestage') || !editorHideFields.includes('sex') || (occurrenceEntryFormat === 'replicate' && !editorHideFields.includes('rep')) || !editorHideFields.includes('individualcount')" class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('lifestage')" class="col-12 col-sm-6 col-md-grow">
                        <template v-if="controlledVocabularies.hasOwnProperty('lifestage') && controlledVocabularies['lifestage'] && controlledVocabularies['lifestage'].length > 0 && (!occurrenceData.lifestage || controlledVocabularies['lifestage'].includes(occurrenceData.lifestage))">
                            <selector-input-element :definition="occurrenceFieldDefinitions['lifestage']" label="Life Stage" :options="controlledVocabularies['lifestage']" :value="occurrenceData.lifestage" @update:value="(value) => updateOccurrenceData('lifestage', value)" :clearable="true"></selector-input-element>
                        </template>
                        <template v-else>
                            <text-field-input-element :definition="occurrenceFieldDefinitions['lifestage']" label="Life Stage" field="lifestage" :maxlength="occurrenceFields['lifestage'] ? occurrenceFields['lifestage']['length'] : 0" :value="occurrenceData.lifestage" @update:value="(value) => updateOccurrenceData('lifestage', value)"></text-field-input-element>
                        </template>
                    </div>
                    <div v-if="!editorHideFields.includes('sex')" class="col-12 col-sm-6 col-md-grow">
                        <template v-if="controlledVocabularies.hasOwnProperty('sex') && controlledVocabularies['sex'] && controlledVocabularies['sex'].length > 0 && (!occurrenceData.sex || controlledVocabularies['sex'].includes(occurrenceData.sex))">
                            <selector-input-element :definition="occurrenceFieldDefinitions['sex']" label="Sex" :options="controlledVocabularies['sex']" :value="occurrenceData.sex" @update:value="(value) => updateOccurrenceData('sex', value)" :clearable="true"></selector-input-element>
                        </template>
                        <template v-else>
                            <text-field-input-element :definition="occurrenceFieldDefinitions['sex']" label="Sex" field="sex" :maxlength="occurrenceFields['sex'] ? occurrenceFields['sex']['length'] : 0" :value="occurrenceData.sex" @update:value="(value) => updateOccurrenceData('sex', value)"></text-field-input-element>
                        </template>
                    </div>
                    <template v-if="occurrenceEntryFormat === 'replicate' && !editorHideFields.includes('rep')">
                        <div class="col-12 col-sm-6 col-md-grow">
                            <text-field-input-element :disabled="true" data-type="int" :definition="occurrenceFieldDefinitions['rep']" label="Rep" :maxlength="occurrenceFields['rep'] ? occurrenceFields['rep']['length'] : 0" min-value="1" :max-value="eventData.repcount" :value="occurrenceData.rep" @update:value="(value) => updateOccurrenceData('rep', value)"></text-field-input-element>
                        </div>
                    </template>
                    <div v-if="!editorHideFields.includes('individualcount')" class="col-12 col-sm-6 col-md-grow">
                        <template v-if="occurrenceEntryFormat === 'replicate'">
                            <text-field-input-element :disabled="true" data-type="int" :definition="occurrenceFieldDefinitions['individualcount']" label="Individual Count" :maxlength="occurrenceFields['individualcount'] ? occurrenceFields['individualcount']['length'] : 0" :value="occurrenceData.individualcount" min-value="0" @update:value="(value) => updateOccurrenceData('individualcount', value)"></text-field-input-element>
                        </template>
                        <template v-else>
                            <text-field-input-element :definition="occurrenceFieldDefinitions['individualcount']" label="Individual Count" :maxlength="occurrenceFields['individualcount'] ? occurrenceFields['individualcount']['length'] : 0" :value="occurrenceData.individualcount" @update:value="(value) => updateOccurrenceData('individualcount', value)"></text-field-input-element>
                        </template>
                    </div>
                </div>
                <div v-if="occurrenceEntryFormat !== 'replicate' && !editorHideFields.includes('labelproject')" class="row">
                    <div class="col-grow">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['labelproject']" label="Label Project" :maxlength="occurrenceFields['labelproject'] ? occurrenceFields['labelproject']['length'] : 0" :value="occurrenceData.labelproject" @update:value="(value) => updateOccurrenceData('labelproject', value)"></text-field-input-element>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('occurrenceremarks')" class="col-11">
                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['occurrenceremarks']" label="Occurrence Remarks" field="occurrenceremarks" :value="occurrenceData.occurrenceremarks" @update:value="(value) => updateOccurrenceData('occurrenceremarks', value)"></text-field-input-element>
                    </div>
                    <div class="col-1 row justify-end self-center">
                        <div>
                            <template v-if="showExtendedForm">
                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = false" icon="fas fa-minus" dense aria-label="Hide additional fields" tabindex="0"></q-btn>
                            </template>
                            <template v-else>
                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = true" icon="fas fa-plus" dense aria-label="Show additional fields" tabindex="0"></q-btn>
                            </template>
                        </div>
                    </div>
                </div>
                <template v-if="showExtendedForm">
                    <div v-if="!editorHideFields.includes('reproductivecondition') || !editorHideFields.includes('establishmentmeans') || !editorHideFields.includes('cultivationstatus')" class="row justify-between q-col-gutter-sm">
                        <div v-if="!editorHideFields.includes('reproductivecondition')" class="col-12 col-sm-6 col-md-5">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['reproductivecondition']" label="Reproductive Condition" field="reproductivecondition" :maxlength="occurrenceFields['reproductivecondition'] ? occurrenceFields['reproductivecondition']['length'] : 0" :value="occurrenceData.reproductivecondition" @update:value="(value) => updateOccurrenceData('reproductivecondition', value)"></text-field-input-element>
                        </div>
                        <div v-if="!editorHideFields.includes('establishmentmeans')" class="col-12 col-sm-6 col-md-5">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['establishmentmeans']" label="Establishment Means" field="establishmentmeans" :maxlength="occurrenceFields['establishmentmeans'] ? occurrenceFields['establishmentmeans']['length'] : 0" :value="occurrenceData.establishmentmeans" @update:value="(value) => updateOccurrenceData('establishmentmeans', value)"></text-field-input-element>
                        </div>
                        <div v-if="!editorHideFields.includes('cultivationstatus')" class="col-12 col-sm-6 col-md-2">
                            <checkbox-input-element :definition="occurrenceFieldDefinitions['cultivationstatus']" label="Cultivated" :value="occurrenceData.cultivationstatus" @update:value="updateCultivationStatusSetting"></checkbox-input-element>
                        </div>
                    </div>
                    <div v-if="!editorHideFields.includes('dynamicproperties')" class="row">
                        <div class="col-grow">
                            <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['dynamicproperties']" label="Dynamic Properties" field="dynamicproperties" :maxlength="occurrenceFields['dynamicproperties'] ? occurrenceFields['dynamicproperties']['length'] : 0" :value="occurrenceData.dynamicproperties" @update:value="(value) => updateOccurrenceData('dynamicproperties', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div v-if="!editorHideFields.includes('verbatimattributes')" class="row">
                        <div class="col-grow">
                            <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['verbatimattributes']" label="Verbatim Attributes" field="verbatimattributes" :maxlength="occurrenceFields['verbatimattributes'] ? occurrenceFields['verbatimattributes']['length'] : 0" :value="occurrenceData.verbatimattributes" @update:value="(value) => updateOccurrenceData('verbatimattributes', value)"></text-field-input-element>
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

        const controlledVocabularies = Vue.computed(() => occurrenceStore.getOccurrenceFieldControlledVocabularies);
        const editorHideFields = Vue.computed(() => occurrenceStore.getEditorHideFields);
        const eventData = Vue.computed(() => occurrenceStore.getCollectingEventData);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const showElement = Vue.computed(() => {
            return (
                !editorHideFields.value.includes('habitat') ||
                !editorHideFields.value.includes('substrate') ||
                !editorHideFields.value.includes('associatedtaxa') ||
                !editorHideFields.value.includes('lifestage') ||
                !editorHideFields.value.includes('sex') ||
                !editorHideFields.value.includes('rep') ||
                !editorHideFields.value.includes('individualcount') ||
                !editorHideFields.value.includes('occurrenceremarks') ||
                !editorHideFields.value.includes('labelproject') ||
                !editorHideFields.value.includes('reproductivecondition') ||
                !editorHideFields.value.includes('establishmentmeans') ||
                !editorHideFields.value.includes('cultivationstatus') ||
                !editorHideFields.value.includes('dynamicproperties') ||
                !editorHideFields.value.includes('verbatimattributes')
            );
        });
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
                                occurrenceStore.setCollectingEventReplicateData();
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
            controlledVocabularies,
            editorHideFields,
            eventData,
            occurrenceData,
            occurrenceEntryFormat,
            occurrenceFields,
            occurrenceFieldDefinitions,
            showElement,
            showExtendedForm,
            processConfirmation,
            updateCultivationStatusSetting,
            updateOccurrenceData
        }
    }
};
