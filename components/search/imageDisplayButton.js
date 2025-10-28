const imageDisplayButton = {
    template: `
        <div class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="redirectWithQueryId('/media/search.php');" icon="fas fa-camera" dense>
                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                    Image Display
                </q-tooltip>
            </q-btn>
        </div>
    `,
    setup() {
        const searchStore = useSearchStore();

        return {
            redirectWithQueryId: searchStore.redirectWithQueryId
        }
    }
};
