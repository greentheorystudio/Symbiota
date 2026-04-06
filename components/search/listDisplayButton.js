const listDisplayButton = {
    props: {
        navigatorMode: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <div class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="processRedirect();" icon="fas fa-list" dense aria-label="Open in List Display" tabindex="0">
                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                    List Display
                </q-tooltip>
            </q-btn>
        </div>
    `,
    setup(props) {
        const searchStore = useSearchStore();

        function processRedirect() {
            if(props.navigatorMode){
                searchStore.setDisplayInterface('list');
            }
            else{
                searchStore.redirectWithQueryId('/collections/occurrenceNavigator.php', 'list')
            }
        }

        return {
            processRedirect
        }
    }
};
