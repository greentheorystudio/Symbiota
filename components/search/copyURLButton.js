const copyURLButton = {
    props: {
        pageNumber: {
            type: Number,
            default: 1
        }
    },
    template: `
        <div v-if="secureOrigin" class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="copySearchUrlToClipboard(pageNumber);" icon="fas fa-link" dense>
                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                    Copy URL to Clipboard
                </q-tooltip>
            </q-btn>
        </div>
    `,
    setup() {
        const { showNotification } = useCore();
        const searchStore = useSearchStore();

        const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
        const searchTermsCollId = Vue.computed(() => searchStore.getSearchTermsCollId);
        const searchTermsRecordSortDirection = Vue.computed(() => searchStore.getSearchTermsRecordSortDirection);
        const searchTermsRecordSortField = Vue.computed(() => searchStore.getSearchTermsRecordSortField);
        const secureOrigin = Vue.computed(() => {
            return (window.location.host === 'localhost' || window.location.protocol === 'https:');
        });

        function copySearchUrlToClipboard(index) {
            const currentSearchTerms = Object.assign({}, searchTerms.value);
            currentSearchTerms.recordPage = index;
            if(searchTermsRecordSortField.value){
                currentSearchTerms.sortDirection = searchTermsRecordSortDirection.value;
                currentSearchTerms.sortField = searchTermsRecordSortField.value;
            }
            if(Number(searchTermsCollId.value) > 0){
                currentSearchTerms.collId = searchTermsCollId.value;
            }
            const searchTermsJson = JSON.stringify(currentSearchTerms);
            let copyUrl = window.location.href + '?starr=' + searchTermsJson.replaceAll("'", '%squot;');
            navigator.clipboard.writeText(copyUrl)
            .then(() => {
                showNotification('positive','URL copied successfully');
            })
            .catch(() => {
                showNotification('negative', 'An error occurred while copying the URL to the clipboard');
            });
        }

        return {
            secureOrigin,
            copySearchUrlToClipboard
        }
    }
};
