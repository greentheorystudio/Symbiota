const singleScientificCommonNameAutoComplete = {
    props: {
        acceptedTaxaOnly: {
            type: Boolean,
            default: false
        },
        disable: {
            type: Boolean,
            default: false
        },
        hideAuthor: {
            type: Boolean,
            default: true
        },
        hideProtected: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: 'Scientific Name'
        },
        limitToThesaurus: {
            type: Boolean,
            default: false
        },
        optionLimit: {
            type: Number,
            default: 10
        },
        rankHigh: {
            type: Number,
            default: null
        },
        rankLimit: {
            type: Number,
            default: null
        },
        rankLow: {
            type: Number,
            default: null
        },
        sciname: {
            type: Object,
            default: null
        },
        taxonType: {
            type: Number,
            default: null
        }
    },
    template: `
        <q-select v-model="sciname" :use-input="inputAllowed" outlined dense options-dense hide-dropdown-icon clearable input-debounce="0" @new-value="createValue" :options="autocompleteOptions" option-value="tid" @filter="getOptions" @blur="blurAction" @clear="clearAction" @update:model-value="processChange" :label="label" :disable="disable"></q-select>
    `,
    setup(props, context) {
        const { showNotification } = useCore();
        const autocompleteOptions = Vue.ref([]);
        const clearInput = Vue.ref(false);
        const inputAllowed = Vue.ref(true);

        function blurAction(val) {
            if(props.sciname === null && val.target.value && !clearInput.value){
                const optionObj = autocompleteOptions.value.find(option => option['name'] === val.target.value);
                if(optionObj){
                    processChange(optionObj);
                }
                else if(!props.limitToThesaurus){
                    processChange({tid: null, label: val.target.value, name: val.target.value});
                }
                else{
                    showNotification('negative', 'That name was not found in the Taxonomic Thesaurus.');
                }
            }
            clearInput.value = false;
        }

        function clearAction() {
            clearInput.value = true;
            inputAllowed.value = true;
        }

        function createValue(val, done) {
            if(val.length > 0) {
                const optionObj = autocompleteOptions.value.find(option => option['name'] === val);
                if(optionObj){
                    done(optionObj, 'add');
                }
                else if(!props.limitToThesaurus){
                    done({tid: null, label: val, name: val}, 'add');
                }
                else{
                    showNotification('negative', 'That name was not found in the Taxonomic Thesaurus.');
                }
            }
        }

        function getOptions(val, update) {
            update(() => {
                if(val.length > 2) {
                    let action = 'getAutocompleteSciNameList';
                    let rankLimit, rankLow, rankHigh;
                    if(props.taxonType){
                        if(Number(props.taxonType) === 1){
                            rankLow = 140;
                        }
                        else if(Number(props.taxonType) === 2){
                            rankLimit = 140;
                        }
                        else if(Number(props.taxonType) === 3){
                            rankLow = 180;
                        }
                        else if(Number(props.taxonType) === 4){
                            rankLow = 10;
                            rankHigh = 130;
                        }
                        else if(Number(props.taxonType) === 6){
                            action = 'getAutocompleteVernacularList';
                        }
                    }
                    else{
                        rankLimit = props.rankLimit;
                        rankLow = props.rankLow;
                        rankHigh = props.rankHigh;
                    }
                    const formData = new FormData();
                    formData.append('action', action);
                    formData.append('term', val);
                    formData.append('hideauth', props.hideAuthor);
                    formData.append('hideprotected', props.hideProtected);
                    formData.append('acceptedonly', props.acceptedTaxaOnly);
                    formData.append('rlimit', rankLimit);
                    formData.append('rlow', rankLow);
                    formData.append('rhigh', rankHigh);
                    formData.append('limit', props.optionLimit);
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => response.json())
                    .then((result) => {
                        autocompleteOptions.value = result;
                    });
                }
                else{
                    autocompleteOptions.value = [];
                }
            });
        }

        function processChange(taxonObj) {
            if(taxonObj){
                inputAllowed.value = false;
            }
            context.emit('update:sciname', taxonObj);
        }

        return {
            autocompleteOptions,
            inputAllowed,
            blurAction,
            clearAction,
            createValue,
            getOptions,
            processChange
        }
    }
};
