const listDisplayButton = {
    template: `
        <div class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="redirectWithQueryId('/search/list.php');" icon="fas fa-list" dense>
                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                    List Display
                </q-tooltip>
            </q-btn>
        </div>
    `,
    setup() {
        const searchStore = Vue.inject('searchStore');

        return {
            redirectWithQueryId: searchStore.redirectWithQueryId
        }
    }
};
