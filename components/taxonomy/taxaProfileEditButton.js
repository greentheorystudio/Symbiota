const taxaProfileEditButton = {
    props: {
        tid: {
            type: Number,
            default: 0
        }
    },
    template: `
        <div>
            <a :href="(clientRoot + '/taxa/profile/tpeditor.php?tid=' + tid)" title="Edit Taxon Data">
                <q-icon name="far fa-edit" size="20px" class="cursor-pointer" />
            </a>
        </div>
    `,
    setup() {
        const store = useBaseStore();
        const clientRoot = store.getClientRoot;

        return {
            clientRoot
        }
    }
};
