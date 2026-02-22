const copyURLButton = {
    template: `
        <div v-if="secureOrigin" class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="copySearchUrlToClipboard();" icon="fas fa-link" dense aria-label="Copy URL to Clipboard" tabindex="0">
                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                    Copy URL to Clipboard
                </q-tooltip>
            </q-btn>
        </div>
    `,
    setup() {
        const { showNotification } = useCore();
        const searchStore = useSearchStore();

        const searchTermsJson = Vue.computed(() => searchStore.getSearchTermsJson);
        const secureOrigin = Vue.computed(() => {
            return (window.location.host === 'localhost' || window.location.protocol === 'https:');
        });

        function copySearchUrlToClipboard() {
            const urlPrefix = window.location.href.includes('?') ? window.location.href.substring(0, window.location.href.indexOf('?')) : window.location.href;
            const copyUrl = urlPrefix + '?starr=' + searchTermsJson.value;
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
