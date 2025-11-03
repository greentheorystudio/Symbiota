const taxaProfileEditButton = {
    props: {
        taxonEditor: {
            type: Boolean,
            default: false
        },
        taxonProfileEditor: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <div class="column q-gutter-sm">
            <div v-if="taxonEditor">
                <q-btn role="link" color="grey-4" text-color="black" class="black-border text-bold" size="sm" :href="(clientRoot + '/taxa/taxonomy/taxonomyeditor.php?tid=' + taxon['tid'])" label="Edit Taxon" no-wrap tabindex="0"></q-btn>
            </div>
            <div v-if="taxonProfileEditor">
                <q-btn role="link" color="grey-4" text-color="black" class="black-border text-bold" size="sm" :href="(clientRoot + '/taxa/profile/tpeditor.php?tid=' + acceptedTaxon['tid'])" label="Edit Taxon Profile" no-wrap tabindex="0"></q-btn>
            </div>
        </div>
    `,
    setup() {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const acceptedTaxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);
        const clientRoot = baseStore.getClientRoot;
        const taxon = Vue.computed(() => taxaStore.getTaxaData);

        return {
            acceptedTaxon,
            clientRoot,
            taxon
        }
    }
};
