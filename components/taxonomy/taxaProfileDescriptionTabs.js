const taxaProfileDescriptionTabs = {
    props: [
        'description-arr'
    ],
    watch: {
        descriptionArr: function(){
            this.processTabs();
        }
    },
    template: `
        <q-card>
            <template v-if="descriptionArr.length">
                <div class="desctabs">
                    <q-tabs v-model="selectedDescTab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                        <template v-for="desc in descriptionArr">
                            <q-tab :name="desc.tdbid" :label="desc.caption" no-caps></q-tab>
                        </template>
                    </q-tabs>
                    <q-separator></q-separator>
                    <q-tab-panels v-model="selectedDescTab">
                        <template v-for="desc in descriptionArr">
                            <q-tab-panel :name="desc.tdbid">
                                <div class="desctabpanels">
                                    <template v-if="desc.source || desc.sourceurl">
                                        <div id="descsource">
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
                    </q-tab-panels>
                </div>
            </template>
            <template v-else>
                <div id="nodesc">Description Not Yet Available</div>
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
