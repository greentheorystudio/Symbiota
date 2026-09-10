const spatialDisplayButton = {
    props: {
        navigatorMode: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <div class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="processRedirect();" icon="fas fa-globe" dense aria-label="Open in Map Display" tabindex="0">
                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                    Map Display
                </q-tooltip>
            </q-btn>
        </div>
    `,
    setup(props) {
        const { hideWorking, showNotification, showWorking } = useCore();

        const searchStore = useSearchStore();

        const searchRecordCount = Vue.computed(() => searchStore.getSearchRecordCount);
        function processRedirect() {
            if(searchRecordCount.value === 0){
                showWorking('Loading...');
                searchStore.setSearchOccidArr(() => {
                    hideWorking();
                    searchStore.setDisplayInterface('spatial');
                    if(Number(searchRecordCount.value) === 0) {
                        showNotification('negative', 'There were no records matching your query.');
                    }
                });
            }else{
                searchStore.setDisplayInterface('spatial');
            }
        }

        return {
            processRedirect
        }
    }
};
