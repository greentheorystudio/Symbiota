const glossaryFieldModule = {
    template: `
        <div class="q-pa-md column q-col-gutter-sm">
            <div class="row justify-between">
                <div>
                    <template v-if="glossaryId > 0">
                        <template v-if="termExists">
                            <span class="q-ml-md text-h6 text-bold text-red self-center">Term already exists</span>
                        </template>
                        <template v-else-if="editsExist">
                            <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                        </template>
                    </template>
                </div>
                <div class="row justify-end">
                    <template v-if="glossaryId > 0">
                        <q-btn color="secondary" @click="saveGlossaryEdits();" label="Save Edits" :disabled="!editsExist || !glossaryValid || termExists" tabindex="0" />
                    </template>
                    <template v-else>
                        <q-btn color="secondary" @click="createGlossary();" label="Create" :disabled="!glossaryValid || termExists" aria-label="Create glossary term" tabindex="0" />
                    </template>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Term" :value="glossaryData['term']" maxlength="150" @update:value="(value) => updateGlossaryData('term', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Definition" :value="glossaryData['definition']" maxlength="2000" @update:value="(value) => updateGlossaryData('definition', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <single-language-auto-complete label="Language" :language="glossaryData['language']" @update:language="processLanguageChange"></single-language-auto-complete>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Author" :value="glossaryData['author']" maxlength="250" @update:value="(value) => updateGlossaryData('author', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Translator" :value="glossaryData['translator']" maxlength="250" @update:value="(value) => updateGlossaryData('translator', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Source" :value="glossaryData['source']" maxlength="1000" @update:value="(value) => updateGlossaryData('source', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Notes" :value="glossaryData['notes']" maxlength="250" @update:value="(value) => updateGlossaryData('notes', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Resource URL" :value="glossaryData['resourceurl']" maxlength="600" @update:value="(value) => updateGlossaryData('resourceurl', value)"></text-field-input-element>
                </div>
            </div>
        </div>
    `,
    components: {
        'single-language-auto-complete': singleLanguageAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { hideWorking, showNotification, showWorking } = useCore();
        const glossaryStore = useGlossaryStore();

        const editsExist = Vue.computed(() => glossaryStore.getGlossaryEditsExist);
        const glossaryArr = Vue.computed(() => glossaryStore.getGlossaryArr);
        const glossaryData = Vue.computed(() => glossaryStore.getGlossaryData);
        const glossaryId = Vue.computed(() => glossaryStore.getGlossaryID);
        const glossaryValid = Vue.computed(() => glossaryStore.getGlossaryValid);
        const termExists = Vue.computed(() => {
            let returnVal = false;
            if(glossaryData.value['term'] && glossaryData.value['language']){
                const existingTerm = glossaryArr.value.find(term => (term['term'] === glossaryData.value['term'] && term['language'] === glossaryData.value['language']));
                if(existingTerm && Number(glossaryData.value['glossid']) !== Number(existingTerm['glossid'])){
                    returnVal = true;
                }
            }
            return returnVal;
        });
        
        function createGlossary() {
            glossaryStore.createGlossaryRecord((newGlossaryId) => {
                if(newGlossaryId > 0){
                    showNotification('positive','Glossary term added successfully.');
                }
                else{
                    showNotification('negative', 'There was an error adding the glossary term');
                }
            });
        }

        function processLanguageChange(langObj) {
            if(langObj){
                updateGlossaryData('language', langObj['name']);
            }
            else{
                updateGlossaryData('language', null);
            }
        }

        function saveGlossaryEdits() {
            showWorking('Saving edits...');
            glossaryStore.updateGlossaryRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the glossary edits.');
                }
            });
        }

        function updateGlossaryData(key, value) {
            glossaryStore.updateGlossaryEditData(key, value);
        }

        return {
            editsExist,
            glossaryData,
            glossaryId,
            glossaryValid,
            termExists,
            createGlossary,
            processLanguageChange,
            saveGlossaryEdits,
            updateGlossaryData
        }
    }
};
