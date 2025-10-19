const copyURLButton = {
    template: `
        <div v-if="secureOrigin" class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="copySearchUrlToClipboard();" icon="fas fa-link" dense>
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
        const secureOrigin = Vue.computed(() => {
            return (window.location.host === 'localhost' || window.location.protocol === 'https:');
        });

        function copySearchUrlToClipboard() {
            const currentSearchTerms = Object.assign({}, searchTerms.value);
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
