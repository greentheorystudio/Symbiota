const taxaProfileTaxonVernaculars = {
    template: `
        <template v-if="vernacularArr.length">
            <div>
                <template v-if="vernacularArr.length > 1">
                    <template v-if="!showAll">
                        {{ firstVernacular }}<span role="button" @click="showAll = true" class="cursor-pointer" aria-label="Click here to show more common names" tabindex="0">&nbsp;&nbsp;[more...]</span>
                    </template>
                    <template v-else>
                        {{ vernacularStr }}<span role="button" @click="showAll = false" class="cursor-pointer" aria-label="Click here to show less common names" tabindex="0">&nbsp;&nbsp;[less]</span>
                    </template>
                </template>
                <template v-else>
                    {{ firstVernacular }}
                </template>
            </div>
        </template>
    `,
    setup() {
        const taxaStore = useTaxaStore();

        const firstVernacular = Vue.computed(() => {
            let firstVern = null;
            if(vernacularArr.value.length > 0){
                firstVern = vernacularArr.value[0]['vernacularname'];
            }
            return firstVern;
        });
        const showAll = Vue.ref(false);
        const vernacularArr = Vue.computed(() => taxaStore.getTaxaVernacularArr);
        const vernacularStr = Vue.computed(() => {
            const vernArr = [];
            vernacularArr.value.forEach((vern) => {
                vernArr.push(vern['vernacularname']);
            });
            return vernArr.join(', ');
        });

        return {
            firstVernacular,
            showAll,
            vernacularArr,
            vernacularStr
        }
    }
};
