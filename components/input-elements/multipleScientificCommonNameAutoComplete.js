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
        concatenator: {
            type: String,
            default: ';'
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
        identifierName: {
            type: String,
            default: null
        },
        identifierValue: {
            type: String,
            default: null
        },
        kingdomId: {
            type: Number,
            default: 0
        },
        label: {
            type: String,
            default: 'Scientific Name'
        },
        limitToOptions: {
            type: Boolean,
            default: false
        },
        nameStringMode: {
            type: Boolean,
            default: true
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
        sciname: {
            type: String,
            default: null
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
        <q-select ref="autocompleteRef" v-model="scinameArr" use-input fill-input outlined dense options-dense hide-dropdown-icon multiple use-chips popup-content-class="z-top" behavior="menu" input-debounce="0" bg-color="white" @new-value="createValue" :options="autocompleteOptions" @filter="getOptions" @blur="blurAction" @update:model-value="processChange" @keyup.enter="processEnterClick" :label="label" :tabindex="tabindex" :disable="disabled">
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
        const scinameArr = Vue.ref([]);

        function blurAction(val) {
            if(val.target.value){
                let optionObj;
                if(Number(props.taxonType) === 5){
                    const fixedVal = val.target.value.replaceAll(' ', '').replaceAll('-', '').replaceAll("'", '');
                    optionObj = autocompleteOptions.value.find(option => option['sciname'].replaceAll(' ', '').replaceAll('-', '').replaceAll("'", '').toLowerCase() === fixedVal.trim().toLowerCase());
                }
                else{
                    optionObj = autocompleteOptions.value.find(option => option['sciname'].toLowerCase() === val.target.value.trim().toLowerCase());
                }
                if(optionObj){
                    scinameArr.value.push(optionObj);
                    processChange();
                }
                else if(!props.limitToOptions){
                    scinameArr.value.push({
                        label: val.target.value,
                        sciname: val.target.value,
                        tid: null,
                        family: null,
                        author: null
                    });
                    processChange();
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
            scinameArr.value.length = 0;
            processChange();
        }

        function createValue(val, done) {
            if(val.length > 0) {
                let optionObj;
                if(Number(props.taxonType) === 5){
                    const fixedVal = val.replaceAll(' ', '').replaceAll('-', '').replaceAll("'", '');
                    optionObj = autocompleteOptions.value.find(option => option['sciname'].replaceAll(' ', '').replaceAll('-', '').replaceAll("'", '').toLowerCase() === fixedVal.trim().toLowerCase());
                }
                else{
                    optionObj = autocompleteOptions.value.find(option => option['sciname'].toLowerCase() === val.trim().toLowerCase());
                }
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

        function processChange() {
            if(props.nameStringMode){
                const nameArr = [];
                scinameArr.value.forEach((taxon) => {
                    nameArr.push(taxon.sciname);
                });
                context.emit('update:sciname', (nameArr.length > 0 ? nameArr.join(props.concatenator) : null));
            }
            else{
                context.emit('update:sciname', scinameArr.value);
            }
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
            formData.append('kingdomid', props.kingdomId.toString());
            formData.append('identifiername', (props.identifierName ? props.identifierName : ''));
            formData.append('identifiervalue', (props.identifierValue ? props.identifierValue : ''));
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

        function setScinameArrFromScinameVal() {
            if(props.sciname && props.sciname.length > 0){
                const nameArr = props.sciname.split(props.concatenator);
                nameArr.forEach((sciname) => {
                    scinameArr.value.push({
                        label: sciname.trim(),
                        sciname: sciname.trim()
                    });
                });
            }
        }

        Vue.onMounted(() => {
            if(props.sciname && props.sciname !== ''){
                setScinameArrFromScinameVal();
            }
        });

        return {
            autocompleteOptions,
            autocompleteRef,
            displayDefinitionPopup,
            scinameArr,
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
