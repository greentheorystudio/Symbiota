const taxaProfileTaxonSynonyms = {
    template: `
        <template v-if="synonymArr.length">
            <div>
                <template v-if="synonymArr.length > 1">
                    <template v-if="!showAll">
                        <span class="text-italic">{{ synonymArr[0].sciname }}</span><template v-if="synonymArr[0].author">&nbsp;{{ synonymArr[0].author }}</template>
                        <span role="button" @click="showAll = true" class="cursor-pointer" aria-label="Click here to show more synonyms" tabindex="0">&nbsp;&nbsp;[more...]</span>
                    </template>
                    <template v-else>
                        <template v-for="(val,index) in synonymArr">
                            <span class="text-italic">{{ val.sciname }}</span>
                            <template v-if="val.author">&nbsp;{{ val.author }}</template>
                            <template v-if="index != synonymArr.length - 1">,&nbsp;</template>
                        </template>
                        <span role="button" @click="showAll = false" class="cursor-pointer" aria-label="Click here to show less synonyms" tabindex="0">&nbsp;&nbsp;[less]</span>
                    </template>
                </template>
                <template v-else>
                    <span class="text-italic">{{ synonymArr[0].sciname }}</span><template v-if="synonymArr[0].author">&nbsp;{{ synonymArr[0].author }}</template>
                </template>
            </div>
        </template>
    `,
    setup() {
        const taxaStore = useTaxaStore();

        const showAll = Vue.ref(false);
        const synonymArr = Vue.computed(() => taxaStore.getTaxaSynonyms);

        return {
            showAll,
            synonymArr
        }
    }
};
