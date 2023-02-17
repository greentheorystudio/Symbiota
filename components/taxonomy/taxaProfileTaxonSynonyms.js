const taxaProfileTaxonSynonyms = {
    props: [
        'synonyms'
    ],
    watch: {
        synonyms: function(){
            this.processSynonyms();
        }
    },
    template: `
        <template v-if="synonyms.length">
            <div id="synonyms">
                <template v-if="synonyms.length > 1">
                    <template v-if="!showAll">
                        <span v-html="firstSynonym"></span><span @click="showAll = true" class="cursor-pointer" title="Click here to show more synonyms">,&nbsp;&nbsp;[more...]</span>
                    </template>
                    <template v-else>
                        <span v-html="synonymStr"></span><span @click="showAll = false" class="cursor-pointer" title="Click here to show less synonyms">&nbsp;&nbsp;[less]</span>
                    </template>
                </template>
                <template v-else>
                    <span v-html="firstSynonym"></span>
                </template>
            </div>
        </template>
    `,
    data() {
        return {
            synonymStr: Vue.ref(null),
            firstSynonym: Vue.ref(null),
            showAll: Vue.ref(false)
        };
    },
    mounted(){
        this.processSynonyms();
    },
    methods: {
        processSynonyms() {
            if(this.synonyms.length > 0){
                const synStringArr = [];
                this.synonyms.forEach((syn) => {
                    let str = '<span style="font-style: italic;">' + syn['sciname'] + '</span> ' + syn['author'];
                    synStringArr.push(str);
                });
                this.firstSynonym = synStringArr[0];
                this.synonymStr = synStringArr.join(', ');
            }
        }
    }
};
