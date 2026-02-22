const taxaProfileDescriptionTabs = {
    template: `
        <div class="full-width">
            <q-card>
                <template v-if="descriptionArr.length || glossaryArr.length">
                    <div class="desc-tabs">
                        <q-tabs v-model="selectedDescTab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                            <template v-for="desc in descriptionArr">
                                <q-tab :name="desc.tdbid" :label="desc.caption" no-caps></q-tab>
                            </template>
                            <template v-if="glossaryArr.length">
                                <q-tab name="glossaryTab" label="Glossary" no-caps></q-tab>
                            </template>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="selectedDescTab">
                            <template v-for="desc in descriptionArr">
                                <q-tab-panel :name="desc.tdbid">
                                    <div class="desc-tab-panels">
                                        <template v-if="desc.source || desc.sourceurl">
                                            <div class="desc-source">
                                                <template v-if="desc.sourceurl">
                                                    <a :href="desc.sourceurl" target="_blank" aria-label="External link: View description source - Opens in separate tab" tabindex="0">{{ desc.source }}</a>
                                                </template>
                                                <template v-else>
                                                    {{ desc.source }}
                                                </template>
                                            </div>
                                        </template>
                                        <div>
                                            <template v-for="text in desc.stmts">
                                                <p v-html="text.statement"></p>
                                            </template>
                                        </div>
                                    </div>
                                </q-tab-panel>
                            </template>
                            <template v-if="glossaryArr.length">
                                <q-tab-panel name="glossaryTab">
                                    <div class="desc-tab-panels">
                                        <p v-for="term in glossaryArr">
                                            <span class="text-weight-bold">{{ term.term }}</span>: {{ term.definition }}
                                        </p>
                                    </div>
                                </q-tab-panel>
                            </template>
                        </q-tab-panels>
                    </div>
                </template>
                <template v-else>
                    <div class="no-desc">Description Not Yet Available</div>
                </template>
            </q-card>
        </div>
    `,
    setup() {
        const taxaStore = useTaxaStore();

        const acceptedTid = Vue.computed(() => taxaStore.getAcceptedTaxonTid);
        const descriptionArr = Vue.computed(() => {
            const displayArr = [];
            if(descriptionBlockArr.value.length > 0 && Object.keys(descriptionStatementData.value).length > 0){
                descriptionBlockArr.value.forEach((desc) => {
                    if(descriptionStatementData.value.hasOwnProperty(desc['tdbid']) && descriptionStatementData.value[desc['tdbid']].length > 0){
                        const description = Object.assign({}, desc);
                        if((!description['source'] || description['source'] === '') && description['sourceurl'] && description['sourceurl'] !== ''){
                            description['source'] = description['sourceurl'];
                        }
                        description['stmts'] = [];
                        descriptionStatementData.value[desc['tdbid']].forEach((stmt) => {
                            if(stmt['statement'] && stmt['statement'] !== ''){
                                const statement = Object.assign({}, stmt);
                                if(statement['statement'].startsWith('<p>')){
                                    statement['statement'] = statement['statement'].slice(3);
                                }
                                if(statement['statement'].endsWith('</p>')){
                                    statement['statement'] = statement['statement'].substring(0, statement['statement'].length - 4);
                                }
                                if(Number(statement['displayheader']) === 1 && statement['heading'] && statement['heading'] !== ''){
                                    const headingText = '<span class="desc-statement-heading">' + statement['heading'] + '</span>: ';
                                    statement['statement'] = headingText + statement['statement'];
                                }
                                description['stmts'].push(statement);
                            }
                        });
                        displayArr.push(description);
                    }
                });
            }
            return displayArr;
        });
        const descriptionBlockArr = Vue.computed(() => taxaStore.getTaxaDescriptionBlockArr);
        const descriptionStatementData = Vue.computed(() => taxaStore.getTaxaDescriptionStatementArr);
        const glossaryArr = Vue.ref([]);
        const selectedDescTab = Vue.ref(null);

        Vue.watch(acceptedTid, () => {
            setGlossary();
        });

        Vue.watch(descriptionArr, () => {
            processTabs();
        });

        function processTabs() {
            if(descriptionArr.value.length > 0){
                selectedDescTab.value = descriptionArr.value[0]['tdbid'];
            }
            else if(glossaryArr.value.length > 0){
                selectedDescTab.value = 'glossaryTab';
            }
        }

        function setGlossary() {
            if(Number(acceptedTid.value) > 0){
                const formData = new FormData();
                formData.append('tid', acceptedTid.value);
                formData.append('action', 'getTaxonGlossary');
                fetch(glossaryApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((resObj) => {
                    glossaryArr.value = resObj;
                    processTabs();
                });
            }
        }

        Vue.onMounted(() => {
            setGlossary();
        });

        return {
            descriptionArr,
            glossaryArr,
            selectedDescTab
        }
    }
};
