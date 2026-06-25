const glossaryEditorRelatedTermsTab = {
    template: `
        <div class="q-pa-md column q-gutter-sm">
            <q-card>
                <q-card-section class="column q-gutter-sm">
                    <div class="text-h6 text-bold">Synonyms</div>
                    <div class="row justify-between q-gutter-sm">
                        <div class="col-9">
                            <glossary-term-auto-complete label="Add Synonym" :value="addSynonymTerm" :glossary-id-arr="synonymGlossidFilterArr" :language="glossaryData['language']" relation-type="synonym" @update:term="processAddSynonymChange"></glossary-term-auto-complete>
                        </div>
                        <div>
                            <q-btn color="primary" @click="addSynonym();" label="Add" dense aria-label="Add synonym" :disabled="Number(addSynonymValue) === 0" tabindex="0" />
                        </div>
                    </div>
                    <template v-if="Object.keys(synonymData).length > 0">
                        <template v-for="glossgrpid in Object.keys(synonymData)">
                            <q-card flat bordered>
                                <q-card-section class="column q-gutter-xs">
                                    <template v-for="term in synonymData[glossgrpid]">
                                        <div class="row justify-start q-gutter-sm">
                                            <div>
                                                {{ term['term'] }}
                                            </div>
                                            <div>
                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deleteRelatedTerm(term['gltlinkid']);" aria-label="Delete synonym" tabindex="0"></q-btn>
                                            </div>
                                        </div>
                                    </template>
                                </q-card-section>
                            </q-card>
                        </template>
                    </template>
                </q-card-section>
            </q-card>
            <q-card>
                <q-card-section class="column q-gutter-sm">
                    <div class="text-h6 text-bold">Translations</div>
                    <div class="row justify-between q-gutter-sm">
                        <div class="col-9">
                            <glossary-term-auto-complete label="Add Translation" :value="addTranslationTerm" :glossary-id-arr="translationGlossidFilterArr" :language="glossaryData['language']" relation-type="translation" @update:term="processAddTranslationChange"></glossary-term-auto-complete>
                        </div>
                        <div>
                            <q-btn color="primary" @click="addTranslation();" label="Add" dense aria-label="Add translation" :disabled="Number(addTranslationValue) === 0" tabindex="0" />
                        </div>
                    </div>
                    <template v-if="Object.keys(translationData).length > 0">
                        <template v-for="glossgrpid in Object.keys(translationData)">
                            <q-card flat bordered>
                                <q-card-section class="column q-gutter-xs">
                                    <template v-for="term in translationData[glossgrpid]">
                                        <div class="row justify-start q-gutter-sm">
                                            <div>
                                                {{ term['term'] + ' (' + term['language'] + ')' }}
                                            </div>
                                            <div>
                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deleteRelatedTerm(term['gltlinkid']);" aria-label="Delete translation" tabindex="0"></q-btn>
                                            </div>
                                        </div>
                                    </template>
                                </q-card-section>
                            </q-card>
                        </template>
                    </template>
                </q-card-section>
            </q-card>
        </div>
    `,
    components: {
        'glossary-term-auto-complete': glossaryTermAutoComplete
    },
    setup() {
        const { showNotification } = useCore();
        const glossaryStore = useGlossaryStore();

        const addSynonymValue = Vue.ref(null);
        const addSynonymTerm = Vue.ref(null);
        const addTranslationLanguage = Vue.ref(null);
        const addTranslationTerm = Vue.ref(null);
        const addTranslationValue = Vue.ref(null);
        const glossaryData = Vue.computed(() => glossaryStore.getGlossaryData);
        const glossaryRelatedTermData = Vue.computed(() => glossaryStore.getGlossaryRelatedTermData);
        const synonymData = Vue.computed(() => {
            const returnData = {};
            Object.keys(glossaryRelatedTermData.value).forEach(glossgrpid => {
                if(glossaryRelatedTermData.value.hasOwnProperty(glossgrpid) && glossaryRelatedTermData.value[glossgrpid].length > 0) {
                    glossaryRelatedTermData.value[glossgrpid].forEach(relTerm => {
                        if(relTerm['relationshiptype'] === 'synonym') {
                            if(!returnData.hasOwnProperty(relTerm['glossgrpid'])){
                                returnData[relTerm['glossgrpid']] = [];
                            }
                            returnData[relTerm['glossgrpid']].push(relTerm);
                            returnData[relTerm['glossgrpid']].sort((a, b) => {
                                return a['term'].localeCompare(b['term']);
                            });
                        }
                    });
                }
            });
            return returnData;
        });
        const synonymGlossidFilterArr = Vue.computed(() => {
            const returnArr = [];
            Object.keys(synonymData.value).forEach(glossgrpid => {
                if(synonymData.value.hasOwnProperty(glossgrpid) && synonymData.value[glossgrpid].length > 0) {
                    synonymData.value[glossgrpid].forEach(term => {
                        returnArr.push(term['glossid']);
                    });
                }
            });
            returnArr.push(glossaryData.value['glossid']);
            return returnArr;
        });
        const translationData = Vue.computed(() => {
            const returnData = {};
            Object.keys(glossaryRelatedTermData.value).forEach(glossgrpid => {
                if(glossaryRelatedTermData.value.hasOwnProperty(glossgrpid) && glossaryRelatedTermData.value[glossgrpid].length > 0) {
                    glossaryRelatedTermData.value[glossgrpid].forEach(relTerm => {
                        if(relTerm['relationshiptype'] === 'translation') {
                            if(!returnData.hasOwnProperty(relTerm['glossgrpid'])){
                                returnData[relTerm['glossgrpid']] = [];
                            }
                            returnData[relTerm['glossgrpid']].push(relTerm);
                            returnData[relTerm['glossgrpid']].sort((a, b) => {
                                return a['language'].localeCompare(b['language']) || a['term'].localeCompare(b['term']);
                            });
                        }
                    });
                }
            });
            return returnData;
        });
        const translationGlossidFilterArr = Vue.computed(() => {
            const returnArr = [];
            Object.keys(translationData.value).forEach(glossgrpid => {
                if(translationData.value.hasOwnProperty(glossgrpid) && translationData.value[glossgrpid].length > 0) {
                    translationData.value[glossgrpid].forEach(term => {
                        returnArr.push(term['glossid']);
                    });
                }
            });
            returnArr.push(glossaryData.value['glossid']);
            return returnArr;
        });

        function addSynonym() {
            let newGroupId;
            const synonymGroupIds = Object.keys(synonymData.value);
            const glossIdArr = [addSynonymValue.value];
            if(synonymGroupIds.length > 0) {
                newGroupId = synonymGroupIds[0];
            }
            else{
                newGroupId = glossaryStore.getNextGlossGroupIdValue();
                glossIdArr.push(glossaryData.value['glossid']);
            }
            glossaryStore.addGlossaryTermRelationship(glossIdArr, newGroupId, 'synonym', (res) => {
                if(res > 0){
                    addSynonymValue.value = null;
                    addSynonymTerm.value = null;
                    showNotification('positive','Synonym added.');
                }
                else{
                    showNotification('negative', 'There was an error while adding the synonym.');
                }
            });
        }

        function addTranslation() {
            let newGroupId;
            const glossIdArr = [addTranslationValue.value];
            Object.keys(translationData.value).forEach(glossgrpid => {
                if(!newGroupId && translationData.value.hasOwnProperty(glossgrpid) && translationData.value[glossgrpid].length > 0) {
                    const existingTranslation = translationData.value[glossgrpid].find(term => term['language'] === addTranslationLanguage.value);
                    if(!existingTranslation){
                        newGroupId = glossgrpid;
                    }
                }
            });
            if(!newGroupId){
                newGroupId = glossaryStore.getNextGlossGroupIdValue();
                glossIdArr.push(glossaryData.value['glossid']);
            }
            glossaryStore.addGlossaryTermRelationship(glossIdArr, newGroupId, 'translation', (res) => {
                if(res > 0){
                    addTranslationValue.value = null;
                    addTranslationTerm.value = null;
                    addTranslationLanguage.value = null;
                    showNotification('positive','Translation added.');
                }
                else{
                    showNotification('negative', 'There was an error while adding the translation.');
                }
            });
        }

        function deleteRelatedTerm(gltlinkid) {
            glossaryStore.deleteGlossaryRelatedTermRecord(gltlinkid, (res) => {
                if(res === 1){
                    showNotification('positive','Relation removed.');
                }
                else{
                    showNotification('negative', 'There was an error while removing the relation.');
                }
            });
        }

        function processAddSynonymChange(value) {
            if(value){
                addSynonymValue.value = value['glossid'];
                addSynonymTerm.value = value['term'];
            }
            else{
                addSynonymValue.value = null;
                addSynonymTerm.value = null;
            }
        }

        function processAddTranslationChange(value) {
            if(value){
                addTranslationValue.value = value['glossid'];
                addTranslationLanguage.value = value['language'];
                addTranslationTerm.value = value['label'];
            }
            else{
                addTranslationValue.value = null;
                addTranslationLanguage.value = null;
                addTranslationTerm.value = null;
            }
        }

        return {
            addSynonymTerm,
            addSynonymValue,
            addTranslationTerm,
            addTranslationValue,
            glossaryData,
            synonymData,
            synonymGlossidFilterArr,
            translationData,
            translationGlossidFilterArr,
            addSynonym,
            addTranslation,
            deleteRelatedTerm,
            processAddSynonymChange,
            processAddTranslationChange
        }
    }
};
