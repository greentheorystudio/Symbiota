const singleLanguageAutoComplete = {
    props: {
        language: {
            type: Object
        },
        label: {
            type: String,
            default: 'Scientific Name'
        },
        disable: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-select v-model="language" :use-input="inputAllowed" outlined dense options-dense hide-dropdown-icon clearable use-input input-debounce="0" @new-value="createValue" :options="autocompleteOptions" option-value="iso" option-label="name" @filter="getOptions" @blur="blurAction" @clear="clearAction" @update:model-value="processChange" :label="label" :disable="disable"></q-select>
        <q-dialog v-model="warning">
            <q-card style="width: 300px">
                <q-card-section>
                    That language was not found in the database.
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
        processChange(languageObj) {
            if(languageObj){
                this.inputAllowed = false;
            }
            this.$emit('update:language', languageObj);
        },
        getOptions(val, update) {
            update(() => {
                if(val.length > 0) {
                    const formData = new FormData();
                    formData.append('action', 'getAutocompleteLanguageList');
                    formData.append('term', val);
                    fetch(languageApiUrl, {
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
            if(this.language === null && val.target.value && !this.clearInput){
                const optionObj = this.autocompleteOptions.find(option => option['name'].toLowerCase() === val.target.value.toLowerCase());
                if(optionObj){
                    this.processChange(optionObj);
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
                const optionObj = this.autocompleteOptions.find(option => option['name'].toLowerCase() === val.toLowerCase());
                if(optionObj){
                    done(optionObj, 'add');
                }
                else{
                    this.warning = true;
                }
            }
        },
        setLanguage() {
            let url;
            if(this.language && (!this.language.hasOwnProperty('iso') || !this.language['iso']) && this.language.hasOwnProperty('name') && this.language['name']){
                url = languageApiUrl + '?action=getLanguageByName&name=' + this.language['name'];
            }
            else if(this.language && (!this.language.hasOwnProperty('name') || !this.language['name']) && this.language.hasOwnProperty('iso') && this.language['iso']){
                url = languageApiUrl + '?action=getLanguageByIso&iso=' + this.language['iso'];
            }
            else{
                url = languageApiUrl + '?action=getLanguageByIso&iso=' + DEFAULT_LANG;
            }
            fetch(url)
            .then((response) => {
                if(response.ok){
                    return response.json();
                }
            })
            .then((data) => {
                this.$emit('update:language', data);
            });
        }
    },
    mounted() {
        this.setLanguage();
    }
};