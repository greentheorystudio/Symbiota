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

        function processRedirect() {
            if(searchStore.getSearchImgCount === 0){
                showWorking('Loading...');
                searchStore.setSearchImgidArr(() => {
                    hideWorking();
                    if(Number(searchStore.getSearchImgCount) === 0) {
                        showNotification('negative', 'There are no image records matching your query.');
                    }
                    else{
                        searchStore.setDisplayInterface('image');
                    }
                });
            }
            else{
                searchStore.setDisplayInterface('image');
            }
        }

        return {
            processRedirect
        }
    }
};
