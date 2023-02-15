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
                        {{ firstSynonym }}<span @click="showAll = true" class="cursor-pointer" title="Click here to show more synonyms">,&nbsp;&nbsp;[more...]</span>
                    </template>
                    <template v-else>
                        {{ synonymStr }}<span @click="showAll = false" class="cursor-pointer" title="Click here to show less synonyms">&nbsp;&nbsp;[less]</span>
                    </template>
                </template>
                <template v-else>
                    {{ firstSynonym }}
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
                this.firstSynonym = this.synonyms[0];
                this.synonymStr = this.synonyms.join(', ');
            }
        }
    }
};
