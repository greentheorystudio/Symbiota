const singleLanguageAutoComplete = {
    props: {
        language: {
            type: Object,
            default: null
        },
        label: {
            type: String,
            default: 'Language'
        },
        disable: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-select v-model="language" :use-input="inputAllowed" outlined dense options-dense hide-dropdown-icon clearable use-input input-debounce="0" @new-value="createValue" :options="autocompleteOptions" option-value="iso" option-label="name" @filter="getOptions" @blur="blurAction" @clear="clearAction" @update:model-value="processChange" :label="label" :disable="disable"></q-select>
    `,
    setup(props, context) {
        const { showNotification } = useCore();
        const store = useBaseStore();
        const autocompleteOptions = Vue.ref([]);
        const clearInput = Vue.ref(false);
        const defaultLanguage = store.getDefaultLanguage;
        const inputAllowed = Vue.ref(true);

        function blurAction(val) {
            if(props.language === null && val.target.value && !clearInput.value){
                const optionObj = autocompleteOptions.value.find(option => option['name'].toLowerCase() === val.target.value.toLowerCase());
                if(optionObj){
                    processChange(optionObj);
                }
                else{
                    showNotification('negative','That language was not found in the database.');
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
            if(languageObj){
                inputAllowed.value = false;
            }
            context.emit('update:language', languageObj);
        }

        function setLanguage() {
            let url;
            if(props.language && (!props.language.hasOwnProperty('iso') || !props.language['iso']) && props.language.hasOwnProperty('name') && props.language['name']){
                url = languageApiUrl + '?action=getLanguageByName&name=' + props.language['name'];
            }
            else if(props.language && (!props.language.hasOwnProperty('name') || !props.language['name']) && props.language.hasOwnProperty('iso') && props.language['iso']){
                url = languageApiUrl + '?action=getLanguageByIso&iso=' + props.language['iso'];
            }
            else{
                url = languageApiUrl + '?action=getLanguageByIso&iso=' + defaultLanguage;
            }
            fetch(url)
            .then((response) => {
                if(response.ok){
                    return response.json();
                }
            })
            .then((data) => {
                context.emit('update:language', data);
            });
        }

        Vue.onMounted(() => {
            setLanguage();
        });

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
