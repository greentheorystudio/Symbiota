const keyDisplayButton = {
    template: `
        <div class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="redirectWithQueryId();" icon="fas fa-key" dense>
                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                    Open in interactive key
                </q-tooltip>
            </q-btn>
        </div>
    `,
    setup() {
        const searchStore = useSearchStore();

        function redirectWithQueryId() {
            searchStore.redirectWithQueryId('/ident/key.php', null, true);
        }

        return {
            redirectWithQueryId
        }
    }
};
