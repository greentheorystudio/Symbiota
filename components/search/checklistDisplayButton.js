const checklistDisplayButton = {
    template: `
        <div class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="redirectWithSearchTermsJson();" icon="fas fa-list" dense>
                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                    Open in interactive checklist
                </q-tooltip>
            </q-btn>
        </div>
    `,
    setup() {
        const searchStore = useSearchStore();

        function redirectWithSearchTermsJson() {
            searchStore.redirectWithSearchTermsJson('/collections/checklistnative.php', {prop: 'interface', propValue: 'checklist'}, true);
        }

        return {
            redirectWithSearchTermsJson
        }
    }
};
