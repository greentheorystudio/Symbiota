const multipleLanguageAutoComplete = {
    props: {
        languageArr: {
            type: Array
        },
        label: {
            type: String,
            default: 'Languages'
        },
        disable: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-select ref="inputRef" v-model="languageArr" outlined dense options-dense hide-dropdown-icon clearable use-input multiple use-chips popup-content-class="z-max" behavior="menu" input-debounce="0" bg-color="white" @new-value="createValue" :options="autocompleteOptions" option-value="iso" option-label="name" @filter="getOptions" @blur="blurAction" @clear="clearAction" @update:model-value="processChange" :label="label" :disable="disable"></q-select>
    `,
    setup(props, context) {
        const { showNotification } = useCore();
        const baseStore = useBaseStore();

        const autocompleteOptions = Vue.ref([]);
        const clearInput = Vue.ref(false);
        const defaultLanguage = baseStore.getDefaultLanguage;
        const inputRef = Vue.ref(null);

        function blurAction(val) {
            if(val.target.value && !clearInput.value){
                const optionObj = autocompleteOptions.value.find(option => option['name'].toLowerCase() === val.target.value.toLowerCase());
                if(optionObj){
                    const currLanguageArr = props.languageArr.slice();
                    currLanguageArr.push(optionObj);
                    processChange(currLanguageArr);
                }
                else{
                    showNotification('negative','That language was not found in the database.');
                }
            }
            clearInput.value = false;
        }

        function clearAction() {
            clearInput.value = true;
        }

        function createValue(val, done) {
            if(val.length > 0) {
                const optionObj = autocompleteOptions.value.find(option => option['name'].toLowerCase() === val.toLowerCase());
                if(optionObj){
                    done(optionObj, 'add');
                }
                else{
                    showNotification('negative','That language was not found in the database.');
                }
            }
        }

        function getOptions(val, update) {
            update(() => {
                if(val.length > 1) {
                    const formData = new FormData();
                    formData.append('action', 'getAutocompleteLanguageList');
                    formData.append('term', val);
                    fetch(languageApiUrl, {
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

        function processChange(languageObj) {
            const newValArr = [];
            if(languageObj){
                languageObj.forEach((lang) => {
                    const existingObj = newValArr.find(obj => obj['name'] === lang['name']);
                    if(!existingObj){
                        newValArr.push(lang);
                    }
                });
            }
            context.emit('update:language', newValArr);
            inputRef.value.updateInputValue('');
        }

        function setLanguage() {
            const url = languageApiUrl + '?action=getLanguageByIso&iso=' + defaultLanguage;
            fetch(url)
            .then((response) => {
                if(response.ok){
                    return response.json();
                }
            })
            .then((data) => {
                context.emit('update:language', [data]);
            });
        }

        Vue.onMounted(() => {
            setLanguage();
        });

        return {
            autocompleteOptions,
            inputRef,
            blurAction,
            clearAction,
            createValue,
            getOptions,
            processChange
        }
    }
};
