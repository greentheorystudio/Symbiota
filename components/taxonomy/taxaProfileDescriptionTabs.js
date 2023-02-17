const taxaProfileDescriptionTabs = {
    props: [
        'description-arr',
        'glossary-arr'
    ],
    watch: {
        descriptionArr: function(){
            this.processTabs();
        },
        glossaryArr: function(){
            this.processTabs();
        }
    },
    template: `
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
                                                <a :href="desc.sourceurl" target="_blank">{{ desc.source }}</a>
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
    `,
    data() {
        return {
            selectedDescTab: Vue.ref(null)
        }
    },
    mounted(){
        this.processTabs();
    },
    methods: {
        processTabs() {
            if(this.descriptionArr.length > 0){
                this.selectedDescTab = this.descriptionArr[0]['tdbid'];
            }
        }
    }
};
