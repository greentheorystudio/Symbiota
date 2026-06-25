const glossaryTermAutoComplete = {
    props: {
        clearable: {
            type: Boolean,
            default: true
        },
        disabled: {
            type: Boolean,
            default: false
        },
        glossaryIdArr: {
            type: Array,
            default: []
        },
        label: {
            type: String,
            default: 'Term'
        },
        language: {
            type: String,
            default: null
        },
        relationType: {
            type: String,
            default: null
        },
        tabindex: {
            type: Number,
            default: 0
        },
        value: {
            type: String,
            default: null
        }
    },
    template: `
        <q-select v-model="value" use-input fill-input hide-selected outlined dense options-dense hide-dropdown-icon use-input popup-content-class="z-top" behavior="menu" input-debounce="0" bg-color="white" @new-value="createValue" :options="autocompleteOptions" option-value="glossid" option-label="label" @filter="getOptions" @blur="blurAction" @update:model-value="processChange" :label="label" :tabindex="tabindex" :disable="disabled">
            <template v-if="!disabled && value" v-slot:append>
                <q-icon role="button" v-if="clearable && value" name="cancel" class="cursor-pointer" @click="clearAction();" @keyup.enter="clearAction();" aria-label="Clear value" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Clear value
                    </q-tooltip>
                </q-icon>
            </template>
        </q-select>
    `,
    setup(props, context) {
        const { showNotification } = useCore();

        const autocompleteOptions = Vue.ref([]);

        function blurAction(val) {
            if(val.target.value && val.target.value !== props.value) {
                const optionObj = autocompleteOptions.value.find(option => option['term'].toLowerCase() === val.target.value.toLowerCase());
                if(optionObj){
                    processChange(optionObj);
                }
                else{
                    showNotification('negative','That term was not found in the database.');
                }
            }
        }

        function clearAction() {
            processChange(null);
        }

        function createValue(val, done) {
            if(val.length > 0) {
                const optionObj = autocompleteOptions.value.find(option => option['term'].toLowerCase() === val.toLowerCase());
                if(optionObj){
                    done(optionObj, 'add');
                }
                else{
                    showNotification('negative','That term was not found in the database.');
                }
            }
        }

        function getOptions(val, update) {
            update(() => {
                if(val.length > 1) {
                    const formData = new FormData();
                    formData.append('relationtype', props.relationType);
                    formData.append('language', props.language);
                    formData.append('glossIdArr', JSON.stringify(props.glossaryIdArr));
                    formData.append('term', val);
                    formData.append('action', 'getAutocompleteTermList');
                    fetch(glossaryApiUrl, {
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

        function processChange(termObj) {
            context.emit('update:term', termObj);
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
