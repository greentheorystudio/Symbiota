const tableDisplayButton = {
    props: {
        navigatorMode: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <div class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="processRedirect();" icon="fas fa-table" dense aria-label="Open in Table Display" tabindex="0">
                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                    Table Display
                </q-tooltip>
            </q-btn>
        </div>
    `,
    setup(props) {
        const { hideWorking, showNotification, showWorking } = useCore();

        const searchStore = useSearchStore();

        function processRedirect() {
            if(searchStore.getSearchRecordCount === 0){
                showWorking('Loading...');
                searchStore.setSearchOccidArr(() => {
                    hideWorking();
                    searchStore.setDisplayInterface('table');
                    if(Number(searchStore.getSearchRecordCount) === 0) {
                        showNotification('negative', 'There were no records matching your query.');
                    }
                });
            }else{
                searchStore.setDisplayInterface('table');
            }
        }

        return {
            processRedirect
        }
    }
};
