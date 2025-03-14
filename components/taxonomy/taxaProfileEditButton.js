const taxaProfileEditButton = {
    template: `
        <div>
            <a :href="(clientRoot + '/taxa/profile/tpeditor.php?tid=' + taxon['tid'])" title="Edit Taxon Data">
                <q-icon name="far fa-edit" size="20px" class="cursor-pointer" />
            </a>
        </div>
    `,
    setup() {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const taxon = Vue.computed(() => taxaStore.getTaxaData);

        const clientRoot = baseStore.getClientRoot;

        return {
            clientRoot,
            taxon
        }
    }
};
