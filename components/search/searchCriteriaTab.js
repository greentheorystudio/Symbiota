const searchCriteriaTab = {
    props: {
        collectionId: {
            type: Number,
            default: null
        },
        showSpatial: {
            type: Boolean,
            default: true
        }
    },
    template: `
        <div class="fit column q-pa-sm q-gutter-y-sm">
            <div class="row justify-end q-gutter-sm">
                <div>
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="resetCriteria();" label="Reset" dense />
                </div>
                <div>
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="loadPoints();" label="Load Records" :disabled="!searchTermsValid" dense />
                </div>
            </div>
            <div class="column">
                <div>
                    <checkbox-input-element label="Include Synonyms from Taxonomic Thesaurus" :value="searchTerms.usethes" @update:value="(value) => updateSearchTerms('usethes', null)"></checkbox-input-element>
                </div>
                <div class="row q-col-gutter-sm">
                    <div class="col-3">
                        <selector-input-element label="Taxon Type" :options="taxonTypeOptions" :value="searchTerms.taxontype" @update:value="updateTaxonType"></selector-input-element>
                    </div>
                    <div class="col-9">
                        <multiple-scientific-common-name-auto-complete :label="scinameFieldLabel" :sciname-arr="scinameArr" :taxon-type="searchTerms.taxontype" @update:sciname="processScientificNameChange"></multiple-scientific-common-name-auto-complete>
                    </div>
                </div>
            </div>
        </div>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'multiple-scientific-common-name-auto-complete': multipleScientificCommonNameAutoComplete,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const searchStore = useSearchStore();

        const scinameArr = Vue.ref([]);
        const scinameFieldLabel = Vue.ref('Scientific Names');
        const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
        const searchTermsValid = Vue.computed(() => searchStore.getSearchTermsValid);
        const taxonTypeOptions = [
            {value: '1', label: 'Family or Scientific Name'},
            {value: '2', label: 'Family only'},
            {value: '3', label: 'Scientific Name only'},
            {value: '4', label: 'Taxonomic group'},
            {value: '5', label: 'Common Name'}
        ];

        const loadPoints = Vue.inject('loadPoints');

        function processScientificNameChange(taxonArr) {
            scinameArr.value = taxonArr;
            if(scinameArr.value.length > 0){
                const nameArr = [];
                scinameArr.value.forEach((taxon) => {
                    nameArr.push(taxon.label);
                });
                updateSearchTerms('taxa', nameArr.join(';'));
            }
            else{
                updateSearchTerms('taxa', null);
            }
        }

        function setScinameArrFromSearchTerms() {
            const searchTermsScinameArr = searchTerms.value['taxa'].split(';');
            searchTermsScinameArr.forEach((sciname) => {
                scinameArr.value.push({
                    label: sciname.trim(),
                    sciname: sciname.trim()
                });
            });
        }

        function resetCriteria() {
            searchStore.clearSearchTerms();
        }

        function updateSearchTerms(prop, value) {
            searchStore.updateSearchTerms(prop, value);
        }

        function updateTaxonType(value) {
            if(Number(value) === 5){
                scinameFieldLabel.value = 'Common Names';
            }
            else{
                scinameFieldLabel.value = 'Scientific Names';
            }
            updateSearchTerms('taxontype', value);
        }

        Vue.onMounted(() => {
            if(searchTerms.value['taxa'] && searchTerms.value['taxa'] !== ''){
                setScinameArrFromSearchTerms();
            }
        });

        return {
            scinameArr,
            scinameFieldLabel,
            searchTerms,
            searchTermsValid,
            taxonTypeOptions,
            loadPoints,
            processScientificNameChange,
            resetCriteria,
            updateSearchTerms,
            updateTaxonType
        }
    }
};
