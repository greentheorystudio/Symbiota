const imageDisplayButton = {
    props: {
        navigatorMode: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <div class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="processRedirect();" icon="fas fa-camera" dense aria-label="Open in Image Display" tabindex="0">
                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                    Image Display
                </q-tooltip>
            </q-btn>
        </div>
    `,
    setup(props) {
        const { hideWorking, showNotification, showWorking } = useCore();

        const searchStore = useSearchStore();

        const searchRecordCount = Vue.computed(() => searchStore.getSearchImgCount);
        function processRedirect() {
            if(searchRecordCount.value === 0){
                showWorking('Loading...');
                searchStore.setSearchImgidArr(() => {
                    hideWorking();
                    searchStore.setDisplayInterface('image');
                    if(Number(searchRecordCount.value) === 0) {
                        showNotification('negative', 'There were no records matching your query.');
                    }
                });
            }else{
                searchStore.setDisplayInterface('image');
            }
        }

        return {
            processRedirect
        }
    }
};
