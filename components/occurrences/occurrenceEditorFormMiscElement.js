const occurrenceEditorFormMiscElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-xs">
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-11">
                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['associatedtaxa']" label="Associated Taxa" :maxlength="occurrenceFields['associatedtaxa'] ? occurrenceFields['associatedtaxa']['length'] : 0" :value="occurrenceData.associatedtaxa" @update:value="(value) => updateOccurrenceData('associatedtaxa', value)"></text-field-input-element>
                    </div>
                    <div class="col-1 row justify-end self-center">
                        <div>
                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="showAssociatedTaxaToolPopup = true" icon="fas fa-tools" dense>
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    Open Associated Taxa Entry Tool
                                </q-tooltip>
                            </q-btn>
                        </div>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-12 col-sm-6 col-md-grow">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['lifestage']" label="Life Stage" :maxlength="occurrenceFields['lifestage'] ? occurrenceFields['lifestage']['length'] : 0" :value="occurrenceData.lifestage" @update:value="(value) => updateOccurrenceData('lifestage', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-grow">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['sex']" label="Sex" :maxlength="occurrenceFields['sex'] ? occurrenceFields['sex']['length'] : 0" :value="occurrenceData.sex" @update:value="(value) => updateOccurrenceData('sex', value)"></text-field-input-element>
                    </div>
                    <template v-if="occurrenceEntryFormat === 'benthic'">
                        <div class="col-12 col-sm-6 col-md-grow">
                            <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['rep']" label="Rep" :maxlength="occurrenceFields['rep'] ? occurrenceFields['rep']['length'] : 0" min-value="1" :max-value="eventData.repcount" :value="occurrenceData.rep" @update:value="(value) => updateOccurrenceData('rep', value)"></text-field-input-element>
                        </div>
                    </template>
                    <div class="col-12 col-sm-6 col-md-grow">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['individualcount']" label="Individual Count" :maxlength="occurrenceFields['individualcount'] ? occurrenceFields['individualcount']['length'] : 0" :value="occurrenceData.individualcount" @update:value="(value) => updateOccurrenceData('individualcount', value)"></text-field-input-element>
                    </div>
                </div>
                <div class="row">
                    <div class="col-grow">
                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['occurrenceremarks']" label="Occurrence Remarks" :value="occurrenceData.occurrenceremarks" @update:value="(value) => updateOccurrenceData('occurrenceremarks', value)"></text-field-input-element>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-12 col-sm-4">
                        <occurrence-basis-of-record-selector :definition="occurrenceFieldDefinitions['basisofrecord']" label="Basis of Record" :value="occurrenceData.basisofrecord" @update:value="(value) => updateOccurrenceData('basisofrecord', value)"></occurrence-basis-of-record-selector>
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
                    <div class="row justify-between q-col-gutter-xs">
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
        <template v-if="showAssociatedTaxaToolPopup">
            <occurrence-editor-associated-taxa-tool-popup
                    :associated-taxa-value="occurrenceData.associatedtaxa"
                    :show-popup="showAssociatedTaxaToolPopup"
                    @close:popup="showAssociatedTaxaToolPopup = false"
            ></occurrence-editor-associated-taxa-tool-popup>
        </template>
        <confirmation-popup ref="confirmationPopupRef" @confirmation:click="processConfirmation"></confirmation-popup>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'confirmation-popup': confirmationPopup,
        'occurrence-basis-of-record-selector': occurrenceBasisOfRecordSelector,
        'occurrence-editor-associated-taxa-tool-popup': occurrenceEditorAssociatedTaxaToolPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const confirmationPopupRef = Vue.ref(null);
        const eventData = Vue.computed(() => occurrenceStore.getCollectingEventData);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const showAssociatedTaxaToolPopup = Vue.ref(false);
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
            }
            else{
                updateOccurrenceData('cultivationstatus', '0');
            }
        }

        function updateOccurrenceData(key, value) {
            if(occurrenceEntryFormat.value === 'benthic' && key === 'individualcount' && Number(value) === 0){
                const confirmText = 'Changing the Individual Count to 0 for this record will result in its deletion. Do you wish to continue?';
                confirmationPopupRef.value.openPopup(confirmText, {cancel: true, trueText: 'Continue'});
            }
            else{
                occurrenceStore.updateOccurrenceEditData(key, value);
            }
        }

        return {
            confirmationPopupRef,
            eventData,
            occurrenceData,
            occurrenceEntryFormat,
            occurrenceFields,
            occurrenceFieldDefinitions,
            showAssociatedTaxaToolPopup,
            showExtendedForm,
            processConfirmation,
            updateCultivationStatusSetting,
            updateOccurrenceData
        }
    }
};
