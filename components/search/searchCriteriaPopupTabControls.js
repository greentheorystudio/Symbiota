const searchCriteriaPopupTabControls = {
    props: {
        popupType: {
            type: String,
            default: 'search'
        }
    },
    template: `
        <div class="row justify-between q-gutter-md">
            <div class="text-body1 text-bold self-center q-pl-md">
                <template v-if="popupType === 'checklist'">
                    Enter criteria to dynamically build a taxa checklist from occurrences matching what is entered
                </template>
            </div>
            <div class="row justify-end q-col-gutter-sm">
                <div>
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="resetCriteria();" label="Reset" dense />
                </div>
                <div>
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="processSearchButtonClick();" :label="searchButtonLabel" :disabled="!searchTermsValid" dense>
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            {{ searchRecordsTooltip }}
                        </q-tooltip>
                    </q-btn>
                </div>
            </div>
        </div>
        <div>
            <q-separator></q-separator>
        </div>
    `,
    setup(props, context) {
        const searchStore = useSearchStore();

        const searchButtonLabel = Vue.computed(() => {
            if(props.popupType === 'checklist'){
                return 'Build checklist';
            }
            else{
                return 'Search Records';
            }
        });
        const searchRecordsTooltip = Vue.computed(() => {
            if(!searchTermsValid.value){
                return 'Set search criteria under any of the tabs here';
            }
            else if(props.popupType === 'checklist'){
                return 'Build a taxa checklist based on occurrences matching the search criteria';
            }
            else{
                return 'Search for records matching the criteria';
            }
        });
        const searchTermsValid = Vue.computed(() => searchStore.getSearchTermsValid);

        function loadRecords() {
            context.emit('process:search-load-records');
        }

        function processSearchButtonClick() {
            if(props.popupType === 'checklist'){
                context.emit('process:build-checklist');
            }
            else{
                context.emit('process:search-load-records');
            }
        }

        function resetCriteria() {
            context.emit('reset:search-criteria');
        }

        return {
            searchButtonLabel,
            searchRecordsTooltip,
            searchTermsValid,
            loadRecords,
            processSearchButtonClick,
            resetCriteria
        }
    }
};
