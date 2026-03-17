const taxaQuickSearch = {
    props: {
        defaultTaxonType: {
            type: String,
            default: 'common'
        },
        listMode: {
            type: Boolean,
            default: false
        },
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
                        <q-btn-toggle v-model="selectedTaxonType" :options="taxonTypeOptions" class="black-border" size="sm" rounded unelevated toggle-color="primary" color="white" text-color="primary" @update:model-value="processTaxonTypeChange" aria-label="Taxon type" tabindex="0"></q-btn-toggle>
                    </div>
                    <div class="row justify-end">
                        <div>
                            <a class="text-body1 text-bold" :href="(clientRoot + '/taxa/dynamictaxalist.php')" tabindex="0">
                                Advanced Search
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-grow">
                        <q-select v-model="selectedTaxon.label" use-input hide-selected fill-input outlined dense options-dense hide-dropdown-icon popup-content-class="z-max" behavior="menu" input-debounce="0" bg-color="white" @new-value="createValue" :options="autoCompleteOptions" @filter="getOptions" @blur="blurAction" @update:model-value="processChange" @keyup.enter="processEnterClick" :label="autoCompleteLabel" autocomplete="off" tabindex="0">
                            <template v-slot:append>
                                <q-icon role="button" name="search" class="cursor-pointer" @click="processSearch" @keyup.enter="processSearch" aria-label="Search" aria-hidden="false" tabindex="0">
                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                        Search
                                    </q-tooltip>
                                </q-icon>
                            </template>
                        </q-select>
                    </div>
                </div>
            </q-card-section>
        </q-card>
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="md-square-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="showPopup = false" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div v-if="taxaArr.length" class="q-pa-md column q-gutter-sm">
                            <template v-for="taxon in taxaArr">
                                <q-card role="button" class="cursor-pointer" @click="openTaxaProfileTab(taxon['tid']);" @keyup.enter="openTaxaProfileTab(taxon['tid']);" :aria-label="( taxon['sciname'] + ' taxon profile page page')" tabindex="0">
                                    <q-card-section class="q-pa-md text-subtitle1">
                                        <span class="text-italic text-bold">{{ taxon['sciname'] }}</span>
                                        <template v-if="taxon['vernacularData'] && taxon['vernacularData'].length > 0 && getVernacularStrFromArr(taxon['vernacularData'], taxon['tid'])">
                                            <span>{{ getVernacularStrFromArr(taxon['vernacularData'], taxon['tid']) }}</span>
                                        </template>
                                    </q-card-section>
                                </q-card>
                            </template>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    setup(props) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const baseStore = useBaseStore();

        const autoCompleteLabel = Vue.ref('Common Name');
        const autoCompleteOptions = Vue.ref([]);
        const clientRoot = baseStore.getClientRoot;
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const selectedTaxon = Vue.ref({});
        const selectedTaxonType = Vue.ref(null);
        const showPopup = Vue.ref(false);
        const taxaArr = Vue.ref([]);
        const taxonTypeOptions = [
            {label: 'Common Name', value: 'common'},
            {label: 'Scientific Name', value: 'scientific'}
        ];

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function blurAction(val) {
            if(val && selectedTaxon.value && val.target.value !== selectedTaxon.value.label){
                setSelectedTaxonValue(val.target.value);
            }
        }

        function createValue(val, done) {
            if(val.length > 0) {
                let optionObj;
                if(!props.listMode){
                    if(selectedTaxonType.value === 'common'){
                        const fixedVal = val.replaceAll(' ', '').replaceAll('-', '').replaceAll("'", '');
                        optionObj = autoCompleteOptions.value.find(option => option['sciname'].replaceAll(' ', '').replaceAll('-', '').replaceAll("'", '').toLowerCase() === fixedVal.trim().toLowerCase());
                    }
                    else{
                        optionObj = autoCompleteOptions.value.find(option => option['sciname'].toLowerCase() === val.trim().toLowerCase());
                    }
                }
                if(optionObj){
                    done(optionObj, 'add');
                }
                else{
                    done({
                        label: val,
                        sciname: val,
                        tid: null,
                        vid: null,
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

        function getVernacularStrFromArr(vernacularArr, tid) {
            const nameArr = [];
            vernacularArr.forEach(vernacular => {
                if(vernacular['vernacularname'] && Number(tid) === Number(vernacular['vernaculartid'])){
                    nameArr.push(vernacular['vernacularname']);
                }
            });
            return nameArr.length > 0 ? (' - ' + nameArr.join(', ')) : null;
        }

        function openTaxaProfileTab(tid) {
            window.location.href = (clientRoot + '/taxa/index.php?taxon=' + tid);
        }

        function processChange(taxonObj) {
            selectedTaxon.value = Object.assign({}, taxonObj);
        }

        function processEnterClick() {
            processSearch();
        }

        function processSearch(val) {
            showWorking();
            if(!selectedTaxon.value.hasOwnProperty('label')){
                setSelectedTaxonValue(val.target.parentNode.parentElement.parentElement.parentElement.control.value);
            }
            const formData = new FormData();
            if(selectedTaxonType.value === 'common'){
                if(!props.listMode && Number(selectedTaxon.value['vid']) > 0){
                    formData.append('vernacularid', selectedTaxon.value['vid']);
                    formData.append('action', 'getHighestRankingTidByVernacular');
                    fetch(taxonVernacularApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        return response.ok ? response.text() : null;
                    })
                    .then((res) => {
                        hideWorking();
                        openTaxaProfileTab(res);
                    });
                }
                else{
                    formData.append('vernacular', selectedTaxon.value['label']);
                    formData.append('action', 'getTaxaListFromVernacularFuzzySearch');
                    fetch(taxonVernacularApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        return response.ok ? response.json() : null;
                    })
                    .then((resData) => {
                        hideWorking();
                        taxaArr.value = resData;
                        if(taxaArr.value.length > 0){
                            showPopup.value = true;
                        }
                        else{
                            showNotification('negative', 'There are no matching common names in the database');
                        }
                    });
                }
            }
            else{
                hideWorking();
                if(!props.listMode && Number(selectedTaxon.value['tid']) > 0){
                    openTaxaProfileTab(selectedTaxon.value['tid']);
                }
                else{
                    formData.append('sciname', selectedTaxon.value['label']);
                    formData.append('action', 'getTaxaListFromScinameFuzzySearch');
                    fetch(taxaApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        return response.ok ? response.json() : null;
                    })
                    .then((resData) => {
                        hideWorking();
                        taxaArr.value = resData;
                        if(taxaArr.value.length > 0){
                            showPopup.value = true;
                        }
                        else{
                            showNotification('negative', 'There are no matching scientific names in the database');
                        }
                    });
                }
            }
        }

        function processTaxonTypeChange(value) {
            selectedTaxon.value = Object.assign({}, {});
            selectedTaxonType.value = value;
            if(value === 'common'){
                autoCompleteLabel.value = 'Common Name';
            }
            else{
                autoCompleteLabel.value = 'Scientific Name';
            }
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setSelectedTaxonValue(value) {
            let optionObj;
            if(!props.listMode){
                if(selectedTaxonType.value === 'common'){
                    const fixedVal = value.replaceAll(' ', '').replaceAll('-', '').replaceAll("'", '');
                    optionObj = autoCompleteOptions.value.find(option => option['sciname'].replaceAll(' ', '').replaceAll('-', '').replaceAll("'", '').toLowerCase() === fixedVal.trim().toLowerCase());
                }
                else{
                    optionObj = autoCompleteOptions.value.find(option => option['sciname'].toLowerCase() === value.trim().toLowerCase());
                }
            }
            if(optionObj){
                selectedTaxon.value = Object.assign({}, optionObj);
            }
            else{
                selectedTaxon.value = Object.assign({}, {
                    label: value,
                    sciname: value,
                    tid: null,
                    vid: null,
                    family: null,
                    author: null
                });
            }
        }

        Vue.onMounted(() => {
            selectedTaxon.value = Object.assign({}, {});
            processTaxonTypeChange(props.defaultTaxonType);
            window.addEventListener('resize', setContentStyle);
        });

        return {
            autoCompleteLabel,
            autoCompleteOptions,
            clientRoot,
            contentRef,
            contentStyle,
            selectedTaxon,
            selectedTaxonType,
            showPopup,
            taxaArr,
            taxonTypeOptions,
            blurAction,
            createValue,
            getOptions,
            getVernacularStrFromArr,
            openTaxaProfileTab,
            processChange,
            processEnterClick,
            processSearch,
            processTaxonTypeChange
        };
    }
};
