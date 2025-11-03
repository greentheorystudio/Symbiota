const multipleScientificCommonNameAutoComplete = {
    props: {
        acceptedTaxaOnly: {
            type: Boolean,
            default: false
        },
        clearable: {
            type: Boolean,
            default: true
        },
        definition: {
            type: Object,
            default: null
        },
        disabled: {
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
        limitToOptions: {
            type: Boolean,
            default: false
        },
        optionLimit: {
            type: Number,
            default: 10
        },
        options: {
            type: Array,
            default: null
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
        scinameArr: {
            type: Array,
            default: []
        },
        tabindex: {
            type: Number,
            default: 0
        },
        taxonType: {
            type: Number,
            default: null
        }
    },
    template: `
        <q-select ref="autocompleteRef" v-model="scinameArr" use-input fill-input outlined dense options-dense hide-dropdown-icon multiple use-chips popup-content-class="z-max" behavior="menu" input-debounce="0" bg-color="white" @new-value="createValue" :options="autocompleteOptions" @filter="getOptions" @blur="blurAction" @update:model-value="processChange" @keyup.enter="processEnterClick" :label="label" :tabindex="tabindex" :disable="disabled">
            <template v-if="!disabled && (scinameArr.length > 0 || definition)" v-slot:append>
                <q-icon role="button" v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();" @keyup.enter="openDefinitionPopup();" aria-label="See field definition" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        See field definition
                    </q-tooltip>
                </q-icon>
                <q-icon role="button" v-if="clearable && scinameArr.length > 0" name="cancel" class="cursor-pointer" @click="clearAction();" @keyup.enter="clearAction();" aria-label="Clear value" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Clear value
                    </q-tooltip>
                </q-icon>
            </template>
        </q-select>
        <template v-if="definition">
            <q-dialog class="z-top" v-model="displayDefinitionPopup" persistent aria-label="Definition pop up">
                <q-card class="sm-popup">
                    <div class="row justify-end items-start map-sm-popup">
                        <div>
                            <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="displayDefinitionPopup = false" aria-label="Close definition pop up" :tabindex="tabindex"></q-btn>
                        </div>
                    </div>
                    <div class="q-pa-sm column q-gutter-sm">
                        <div class="text-h6">{{ label }}</div>
                        <template v-if="definition.definition">
                            <div>
                                <span class="text-bold">Definition: </span>{{ definition.definition }}
                            </div>
                        </template>
                        <template v-if="definition.comments">
                            <div>
                                <span class="text-bold">Comments: </span>{{ definition.comments }}
                            </div>
                        </template>
                        <template v-if="definition.examples">
                            <div>
                                <span class="text-bold">Examples: </span>{{ definition.examples }}
                            </div>
                        </template>
                        <template v-if="definition.source">
                            <div>
                                <a :href="definition.source" target="_blank" aria-label="External link: Go to source - Opens in separate tab" :tabindex="tabindex"><span class="text-bold">Go to source</span></a>
                            </div>
                        </template>
                    </div>
                </q-card>
            </q-dialog>
        </template>
    `,
    setup(props, context) {
        const { showNotification } = useCore();

        const autocompleteOptions = Vue.ref([]);
        const autocompleteRef = Vue.ref(null);
        const displayDefinitionPopup = Vue.ref(false);

        function blurAction(val) {
            if(val.target.value){
                const optionObj = autocompleteOptions.value.find(option => option['sciname'].toLowerCase() === val.target.value.trim().toLowerCase());
                if(optionObj){
                    const currentScinameArr = props.scinameArr.slice();
                    currentScinameArr.push(optionObj);
                    processChange(currentScinameArr);
                }
                else if(!props.limitToOptions){
                    const currentScinameArr = props.scinameArr.slice();
                    currentScinameArr.push({
                        label: val.target.value,
                        sciname: val.target.value,
                        tid: null,
                        family: null,
                        author: null
                    });
                    processChange(currentScinameArr);
                }
                else if(props.options && props.options.length > 0){
                    showNotification('negative', 'That name was not found in the taxa list');
                }
                else{
                    showNotification('negative', 'That name was not found in the Taxonomic Thesaurus');
                }
            }
        }

        function clearAction() {
            processChange([]);
        }

        function createValue(val, done) {
            if(val.length > 0) {
                const optionObj = autocompleteOptions.value.find(option => option['sciname'].toLowerCase() === val.trim().toLowerCase());
                if(optionObj){
                    done(optionObj, 'add');
                }
                else if(!props.limitToOptions){
                    done({
                        label: val,
                        sciname: val,
                        tid: null,
                        family: null,
                        author: null
                    }, 'add');
                }
                else{
                    showNotification('negative', 'That name was not found in the Taxonomic Thesaurus.');
                }
            }
        }

        function getOptions(val, update) {
            update(() => {
                if(val.length > 2) {
                    if(props.options){
                        setOptionsFromProps(val);
                    }
                    else{
                        setOptionsFromFetch(val);
                    }
                }
                else{
                    autocompleteOptions.value = [];
                }
            });
        }

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processChange(taxonObj) {
            context.emit('update:sciname', taxonObj);
            autocompleteRef.value.updateInputValue('');
        }

        function processEnterClick() {
            autocompleteRef.value.blur();
            context.emit('click:enter');
        }

        function setOptionsFromFetch(val) {
            let action = 'getAutocompleteSciNameList';
            let rankLimit, rankLow, rankHigh;
            let dataSource = taxaApiUrl;
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
                else if(Number(props.taxonType) === 5){
                    action = 'getAutocompleteVernacularList';
                    dataSource = taxonVernacularApiUrl;
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
            formData.append('kingdomid', (props.kingdomId ? props.kingdomId.toString() : ''));
            formData.append('hideauth', props.hideAuthor);
            formData.append('hideprotected', props.hideProtected);
            formData.append('acceptedonly', props.acceptedTaxaOnly);
            formData.append('rlimit', rankLimit);
            formData.append('rlow', rankLow);
            formData.append('rhigh', rankHigh);
            formData.append('limit', props.optionLimit);
            fetch(dataSource, {
                method: 'POST',
                body: formData
            })
            .then((response) => response.json())
            .then((result) => {
                autocompleteOptions.value = result;
            });
        }

        function setOptionsFromProps(val) {
            const newOptions = [];
            props.options.forEach(option => {
                if(option['sciname'].startsWith(val)){
                    newOptions.push(option);
                }
            });
            autocompleteOptions.value = newOptions;
        }

        return {
            autocompleteOptions,
            autocompleteRef,
            displayDefinitionPopup,
            blurAction,
            clearAction,
            createValue,
            getOptions,
            openDefinitionPopup,
            processChange,
            processEnterClick
        }
    }
};
