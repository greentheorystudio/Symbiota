const searchCriteriaPopupTabControls = {
    props: {
        popupType: {
            type: String,
            default: 'search'
        }
    },
    template: `
        <div class="row justify-end q-col-gutter-sm">
            <div>
                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="resetCriteria();" label="Reset" dense />
            </div>
            <div>
                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="loadRecords();" label="Search Records" :disabled="!searchTermsValid" dense>
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        {{ searchRecordsTooltip }}
                    </q-tooltip>
                </q-btn>
            </div>
        </div>
        <div>
            <q-separator></q-separator>
        </div>
    `,
    setup(props, context) {
        const searchStore = useSearchStore();

        const searchRecordsTooltip = Vue.computed(() => {
            if(!searchTermsValid.value){
                return 'Search criteria must be entered or the collection list must be narrowed in order to Search Records';
            }
            return 'Search for records matching the criteria';
        });
        const searchTermsValid = Vue.computed(() => searchStore.getSearchTermsValid);

        const loadRecords = Vue.inject('loadRecords');

        function resetCriteria() {
            context.emit('reset:search-criteria');
        }

        return {
            searchRecordsTooltip,
            searchTermsValid,
            loadRecords,
            resetCriteria
        }
    }
};
