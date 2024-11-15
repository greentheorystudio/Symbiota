const taxaProfileTaxonSynonyms = {
    props: {
        synonyms: {
            type: Array,
            default: []
        }
    },
    template: `
        <template v-if="synonyms.length">
            <div>
                <template v-if="synonyms.length > 1">
                    <template v-if="!showAll">
                        <span class="text-italic">{{ synonyms[0].sciname }}</span><template v-if="synonyms[0].author">&nbsp;{{ synonyms[0].author }}</template>
                        <span @click="showAll = true" class="cursor-pointer" title="Click here to show more synonyms">&nbsp;&nbsp;[more...]</span>
                    </template>
                    <template v-else>
                        <template v-for="(val,index) in synonyms">
                            <span class="text-italic">{{ val.sciname }}</span>
                            <template v-if="val.author">&nbsp;{{ val.author }}</template>
                            <template v-if="index != synonyms.length - 1">,&nbsp;</template>
                        </template>
                        <span @click="showAll = false" class="cursor-pointer" title="Click here to show less synonyms">&nbsp;&nbsp;[less]</span>
                    </template>
                </template>
                <template v-else>
                    <span class="text-italic">{{ synonyms[0].sciname }}</span><template v-if="synonyms[0].author">&nbsp;{{ synonyms[0].author }}</template>
                </template>
            </div>
        </template>
    `,
    setup() {
        const showAll = Vue.ref(false);

        return {
            showAll
        }
    }
};
