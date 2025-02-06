const occurrenceEditorFormIdentifierElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                    Collection Identifiers
                </div>
                <div class="row justify-between q-col-gutter-sm">
                    <div class="col-12 col-sm-3">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['catalognumber']" label="Catalog Number" :maxlength="occurrenceFields['catalognumber'] ? occurrenceFields['catalognumber']['length'] : 0" :value="occurrenceData.catalognumber" @update:value="(value) => updateOccurrenceData('catalognumber', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-9">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['othercatalognumbers']" label="Other Catalog Numbers" :maxlength="occurrenceFields['othercatalognumbers'] ? occurrenceFields['othercatalognumbers']['length'] : 0" :value="occurrenceData.othercatalognumbers" @update:value="(value) => updateOccurrenceData('othercatalognumbers', value)"></text-field-input-element>
                    </div>
                </div>
            </q-card-section>
        </q-card>
        <template v-if="showCollectionListPopup">
            <occurrence-collection-list-popup
                :collection-arr="duplicateArr"
                :show-popup="showCollectionListPopup"
                @close:popup="closeCollectionListPopup"
            ></occurrence-collection-list-popup>
        </template>
    `,
    components: {
        'occurrence-collection-list-popup': occurrenceCollectionListPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const occurrenceStore = useOccurrenceStore();

        const duplicateArr = Vue.ref([]);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const showCollectionListPopup = Vue.ref(false);

        function closeCollectionListPopup() {
            showCollectionListPopup.value = false;
            duplicateArr.value = [];
        }

        function processDuplicateIdentifierSearch(field, value) {
            duplicateArr.value.length = 0;
            if(value && value !== ''){
                occurrenceStore.getOccurrenceDuplicateIdentifierRecordArr(field, value, (dupArr) => {
                    if(dupArr.length > 0){
                        duplicateArr.value = dupArr;
                        showCollectionListPopup.value = true;
                    }
                });
            }
        }

        function updateOccurrenceData(key, value) {
            occurrenceStore.updateOccurrenceEditData(key, value);
            processDuplicateIdentifierSearch(key, value);
        }

        return {
            duplicateArr,
            occurrenceData,
            occurrenceFields,
            occurrenceFieldDefinitions,
            showCollectionListPopup,
            closeCollectionListPopup,
            updateOccurrenceData
        }
    }
};
