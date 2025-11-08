const singleLanguageAutoComplete = {
    props: {
        clearable: {
            type: Boolean,
            default: true
        },
        disabled: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: 'Language'
        },
        language: {
            type: String,
            default: null
        },
        tabindex: {
            type: Number,
            default: 1
        }
    },
    template: `
        <q-select v-model="language" use-input fill-input hide-selected outlined dense options-dense hide-dropdown-icon use-input popup-content-class="z-max" behavior="menu" input-debounce="0" bg-color="white" @new-value="createValue" :options="autocompleteOptions" option-value="iso" option-label="name" @filter="getOptions" @blur="blurAction" @update:model-value="processChange" :label="label" :tabindex="tabindex" :disable="disabled">
            <template v-if="!disabled && language" v-slot:append>
                <q-icon role="button" v-if="clearable && language" name="cancel" class="cursor-pointer" @click="clearAction();" @keyup.enter="clearAction();" aria-label="Clear value" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Clear value
                    </q-tooltip>
                </q-icon>
            </template>
        </q-select>
    `,
    setup(props, context) {
        const { showNotification } = useCore();
        const baseStore = useBaseStore();
        
        const autocompleteOptions = Vue.ref([]);
        const defaultLanguage = baseStore.getDefaultLanguage;

        function blurAction(val) {
            if(val.target.value && val.target.value !== props.language) {
                const optionObj = autocompleteOptions.value.find(option => option['name'].toLowerCase() === val.target.value.toLowerCase());
                if(optionObj){
                    processChange(optionObj);
                }
                else{
                    showNotification('negative','That language was not found in the database.');
                }
            }
        }

        function clearAction() {
            processChange(null);
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
            context.emit('update:language', languageObj);
        }

        return {
            autocompleteOptions,
            blurAction,
            clearAction,
            createValue,
            getOptions,
            processChange
        }
    }
};
