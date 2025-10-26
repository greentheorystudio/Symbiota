const layerConfigurationsLayerEditorPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="sm-popup">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div class="q-pa-md column q-col-gutter-sm">
                    <div class="row justify-between">
                        <div>
                            <template v-if="checklistTaxaId > 0 && editsExist">
                                <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                            </template>
                        </div>
                        <div class="row justify-end">
                            <template v-if="checklistTaxaId > 0">
                                <q-btn color="secondary" @click="saveChecklistTaxaEdits();" label="Save Edits" :disabled="!editsExist || !checklistTaxaValid" />
                            </template>
                            <template v-else>
                                <q-btn color="secondary" @click="addChecklistTaxon();" label="Add Taxon" :disabled="!checklistTaxaValid" />
                            </template>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element label="Habitat" :value="checklistTaxaData['habitat']" maxlength="250" @update:value="(value) => updateChecklistTaxonData('habitat', value)" @click:enter="processEnterClick"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element label="Abundance" :value="checklistTaxaData['abundance']" maxlength="50" @update:value="(value) => updateChecklistTaxonData('abundance', value)" @click:enter="processEnterClick"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element data-type="textarea" label="Notes" :value="checklistTaxaData['notes']" maxlength="2000" @update:value="(value) => updateChecklistTaxonData('notes', value)" @click:enter="processEnterClick"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element label="Source" :value="checklistTaxaData['source']" maxlength="250" @update:value="(value) => updateChecklistTaxonData('source', value)" @click:enter="processEnterClick"></text-field-input-element>
                        </div>
                    </div>
                    <div v-if="Number(checklistTaxaId) > 0" class="row justify-end q-gutter-md">
                        <div>
                            <q-btn color="negative" @click="deleteChecklistTaxon();" label="Remove Taxon" />
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'text-field-input-element': textFieldInputElement
    },
    setup(_, context) {

        
        return {
            closePopup

        }
    }
};
