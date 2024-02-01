const copyURLButton = {
    props: {
        pageNumber: {
            type: Number,
            default: 1
        }
    },
    template: `
        <div class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="copySearchUrlToClipboard();" icon="fas fa-link" dense>
                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                    Copy URL to Clipboard
                </q-tooltip>
            </q-btn>
        </div>
    `,
    setup() {
        const searchStore = Vue.inject('searchStore');

        return {
            copySearchUrlToClipboard: searchStore.copySearchUrlToClipboard
        }
    }
};
