const taxaProfileEditButton = {
    props: [
        'tid'
    ],
    template: `
        <div>
            <a :href="(clientRoot + '/taxa/profile/tpeditor.php?tid=' + tid)" title="Edit Taxon Data">
                <q-icon name="far fa-edit" size="20px" class="cursor-pointer" />
            </a>
        </div>
    `,
    data() {
        return {
            clientRoot: Vue.ref(CLIENT_ROOT)
        }
    }
};
