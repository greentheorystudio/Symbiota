const singleScientificCommonNameAutoComplete = {
    props: {
        sciname: {
            type: Object
        },
        label: {
            type: String,
            default: 'Scientific Name'
        },
        optionLimit: {
            type: Number,
            default: 10
        },
        hideAuthor: {
            type: Boolean,
            default: true
        },
        hideProtected: {
            type: Boolean,
            default: false
        },
        limitToThesaurus: {
            type: Boolean,
            default: false
        },
        acceptedTaxaOnly: {
            type: Boolean,
            default: false
        },
        taxonType: {
            type: Number
        },
        rankLimit: {
            type: Number
        },
        rankLow: {
            type: Number
        },
        rankHigh: {
            type: Number
        },
        disable: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-select v-model="sciname" :use-input="inputAllowed" outlined dense options-dense hide-dropdown-icon clearable use-input input-debounce="0" @new-value="createValue" :options="autocompleteOptions" option-value="tid" @filter="getOptions" @blur="blurAction" @clear="clearAction" @update:model-value="processChange" :label="label" :disable="disable"></q-select>
        <q-dialog v-model="warning">
            <q-card style="width: 300px">
                <q-card-section>
                    Name was not found in the Taxonomic Thesaurus.
                </q-card-section>
                <q-card-actions align="right" class="bg-white text-teal">
                    <q-btn flat label="OK" v-close-popup></q-btn>
                </q-card-actions>
            </q-card>
        </q-dialog>
    `,
    data() {
        return {
            warning: Vue.ref(false),
            inputAllowed: Vue.ref(true),
            clearInput: Vue.ref(false),
            autocompleteOptions: Vue.ref([])
        }
    },
    methods: {
        processChange(taxonObj) {
            if(taxonObj){
                this.inputAllowed = false;
            }
            this.$emit('update:sciname', taxonObj);
        },
        getOptions(val, update) {
            update(() => {
                if(val.length > 2) {
                    let action = 'getAutocompleteSciNameList';
                    let rankLimit, rankLow, rankHigh;
                    if(this.taxonType){
                        if(Number(this.taxonType) === 1){
                            rankLow = 140;
                        }
                        else if(Number(this.taxonType) === 2){
                            rankLimit = 140;
                        }
                        else if(Number(this.taxonType) === 3){
                            rankLow = 180;
                        }
                        else if(Number(this.taxonType) === 4){
                            rankLow = 10;
                            rankHigh = 130;
                        }
                        else if(Number(this.taxonType) === 6){
                            action = 'getAutocompleteVernacularList';
                        }
                    }
                    else{
                        rankLimit = this.rankLimit;
                        rankLow = this.rankLow;
                        rankHigh = this.rankHigh;
                    }
                    const formData = new FormData();
                    formData.append('action', action);
                    formData.append('term', val);
                    formData.append('hideauth', this.hideAuthor);
                    formData.append('hideprotected', this.hideProtected);
                    formData.append('acceptedonly', this.acceptedTaxaOnly);
                    formData.append('rlimit', rankLimit);
                    formData.append('rlow', rankLow);
                    formData.append('rhigh', rankHigh);
                    formData.append('limit', this.optionLimit);
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => response.json())
                    .then((result) => {
                        this.autocompleteOptions = result;
                    });
                }
                else{
                    this.autocompleteOptions = [];
                }
            });
        },
        blurAction(val) {
            if(this.sciname === null && val.target.value && !this.clearInput){
                const optionObj = this.autocompleteOptions.find(option => option['name'] === val.target.value);
                if(optionObj){
                    this.processChange(optionObj);
                }
                else if(!this.limitToThesaurus){
                    this.processChange({tid: null,label:val.target.value,name:val.target.value});
                }
                else{
                    this.warning = true;
                }
            }
            this.clearInput = false;
        },
        clearAction() {
            this.clearInput = true;
            this.inputAllowed = true;
        },
        createValue(val, done) {
            if(val.length > 0) {
                const optionObj = this.autocompleteOptions.find(option => option['name'] === val);
                if(optionObj){
                    done(optionObj, 'add');
                }
                else if(!this.limitToThesaurus){
                    done({tid: null,label:val,name:val}, 'add');
                }
                else{
                    this.warning = true;
                }
            }
        }
    }
};
