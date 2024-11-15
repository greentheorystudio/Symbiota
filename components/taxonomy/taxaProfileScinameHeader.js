const taxaProfileScinameHeader = {
    props: {
        styleClass: {
            type: String,
            default: ''
        },
        taxon: {
            type: Object,
            default: {}
        }
    },
    template: `
        <div>
            <span class="taxon-profile-sciname"><span :class="styleClass">{{ taxon.sciName }}</span></span> <span>{{ taxon.author }}</span>
            <a :href="(clientRoot + '/taxa/index.php?taxon=' + taxon.parentTid + '&cl=' + (taxon.clid ? taxon.clid : ''))" class="parent-link" title="Go to Parent">
                <q-icon name="fas fa-level-up-alt" size="15px" class="cursor-pointer" />
            </a>
            <template v-if="taxon.submittedTid !== taxon.tid">
                <span class="redirected-from"> (redirected from: <span class="text-italic">{{ taxon.submittedSciName }}</span>)</span>
            </template>
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
