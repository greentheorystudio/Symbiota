const taxaProfileEditButton = {
    props: [
        'edit-link'
    ],
    template: `
        <div>
            <a :href="editLink" title="Edit Taxon Data">
                <q-icon name="far fa-edit" size="20px" class="cursor-pointer" />
            </a>
        </div>
    `
};
