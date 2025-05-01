const taxaQuickSearch = {
    props: {
        quicksearchLabel: {
            type: String,
            default: null
        }
    },
    template: `
        <q-card flat bordered class="black-border bg-grey-3">
            <q-card-section class="q-pa-sm column">
                <div v-if="quicksearchLabel">
                    <span class="text-h6 text-bold">{{ quicksearchLabel }}</span>
                </div>
                <div class="q-mb-md row justify-between q-gutter-md">
                    <div>
                        <q-btn-toggle v-model="selectedTaxonType" :options="taxonTypeOptions" class="black-border" size="sm" rounded unelevated toggle-color="primary" color="white" text-color="primary" @update:model-value="processTaxonTypeChange"></q-btn-toggle>
                    </div>
                    <div class="row justify-end">
                        <div>
                            <a class="text-body1 text-bold" :href="(clientRoot + '/taxa/dynamictaxalist.php')">
                                Advanced Search
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-grow">
                        <q-select v-model="selectedTaxon.label" use-input hide-selected fill-input outlined dense options-dense hide-dropdown-icon popup-content-class="z-max" behavior="menu" input-debounce="0" bg-color="white" @new-value="createValue" :options="autoCompleteOptions" @filter="getOptions" @blur="blurAction" @update:model-value="processChange" :label="autoCompleteLabel">
                            <template v-slot:append>
                                <q-icon name="search" class="cursor-pointer" @click="processSearch();">
                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                        Clear value
                                    </q-tooltip>
                                </q-icon>
                            </template>
                        </q-select>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    `,
    setup(props) {
        const { showNotification } = useCore();
        const baseStore = useBaseStore();

        const autoCompleteLabel = Vue.ref('Common Name');
        const autoCompleteOptions = Vue.ref([]);
        const clientRoot = baseStore.getClientRoot;
        const selectedTaxon = Vue.ref({});
        const selectedTaxonType = Vue.ref('common');
        const taxonTypeOptions = [
            {label: 'Common Name', value: 'common'},
            {label: 'Scientific Name', value: 'scientific'}
        ];

        function blurAction(val) {
            if(val && selectedTaxon.value && val.target.value !== selectedTaxon.value.label){
                const optionObj = autoCompleteOptions.value.find(option => option['sciname'] === val.target.value);
                if(optionObj){
                    processChange(optionObj);
                }
                else{
                    processChange({
                        label: val.target.value,
                        sciname: val.target.value,
                        tid: null,
                        family: null,
                        author: null
                    });
                }
            }
        }

        function createValue(val, done) {
            if(val.length > 0) {
                const optionObj = autoCompleteOptions.value.find(option => option['sciname'] === val);
                if(optionObj){
                    done(optionObj, 'add');
                }
                else{
                    done({
                        label: val,
                        sciname: val,
                        tid: null,
                        family: null,
                        author: null
                    }, 'add');
                }
            }
        }

        function getOptions(val, update) {
            update(() => {
                if(val.length > 2) {
                    let action = 'getAutocompleteSciNameList';
                    let dataSource = taxaApiUrl;
                    if(selectedTaxonType.value === 'common'){
                        action = 'getAutocompleteVernacularList';
                        dataSource = taxonVernacularApiUrl;
                    }
                    const formData = new FormData();
                    formData.append('action', action);
                    formData.append('term', val);
                    formData.append('hideauth', '1');
                    formData.append('hideprotected', '0');
                    formData.append('limit', '10');
                    fetch(dataSource, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => response.json())
                    .then((result) => {
                        autoCompleteOptions.value = result;
                    });
                }
                else{
                    autoCompleteOptions.value = [];
                }
            });
        }

        function processChange(taxonObj) {
            selectedTaxon.value = Object.assign({}, taxonObj);
        }

        function processSearch() {
            if(selectedTaxonType.value === 'common'){
                const formData = new FormData();
                formData.append('vernacular', selectedTaxon.value['label']);
                formData.append('action', 'getHighestRankingTidByVernacular');
                fetch(taxonVernacularApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.text().then((res) => {
                            if(Number(res) > 0){
                                window.location.href = (clientRoot + '/taxa/index.php?taxon=' + res);
                            }
                            else{
                                showNotification('negative', 'That common name was not found in the database');
                            }
                        });
                    }
                });
            }
            else{
                if(selectedTaxon.value.hasOwnProperty('tid') && Number(selectedTaxon.value['tid']) > 0){
                    window.location.href = (clientRoot + '/taxa/index.php?taxon=' + selectedTaxon.value['tid']);
                }
                else{
                    showNotification('negative', 'That scientific name was not found in the database');
                }
            }
        }

        function processTaxonTypeChange(value) {
            selectedTaxon.value = Object.assign({}, {});
            if(value === 'common'){
                autoCompleteLabel.value = 'Common Name';
            }
            else{
                autoCompleteLabel.value = 'Scientific Name';
            }
        }

        Vue.onMounted(() => {
            selectedTaxon.value = Object.assign({}, {});
        });

        return {
            autoCompleteLabel,
            autoCompleteOptions,
            clientRoot,
            selectedTaxon,
            selectedTaxonType,
            taxonTypeOptions,
            blurAction,
            createValue,
            getOptions,
            processChange,
            processSearch,
            processTaxonTypeChange
        };
    }
};
