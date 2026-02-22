const keyCharacterAutoComplete = {
    props: {
        disabled: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: 'Character'
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
        <q-select v-model="value" use-input hide-selected fill-input outlined dense options-dense hide-dropdown-icon popup-content-class="z-top" behavior="menu" input-class="z-top" input-debounce="0" bg-color="white" @new-value="createValue" @blur="blurAction" :options="autocompleteOptions" option-label="charactername" @filter="getOptions" @update:model-value="processChange" :label="label" :tabindex="tabindex" :disable="disabled">
            <template v-if="!disabled && value" v-slot:append>
                <q-icon role="button" v-if="value" name="cancel" class="cursor-pointer" @click="clearAction();" @keyup.enter="clearAction();" aria-label="Clear value" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Clear value
                    </q-tooltip>
                </q-icon>
            </template>
        </q-select>
    `,
    setup(props, context) {
        const autocompleteOptions = Vue.ref([]);

        function blurAction(val) {
            if(val.target.value && val.target.value !== props.value){
                const optionObj = autocompleteOptions.value.find(option => option['charactername'].toLowerCase() === val.target.value.trim().toLowerCase());
                if(optionObj){
                    processChange(optionObj);
                }
            }
        }

        function clearAction() {
            processChange(null);
        }

        function createValue(val, done) {
            if(val.length > 0) {
                const optionObj = autocompleteOptions.value.find(option => option['charactername'].toLowerCase() === val.trim().toLowerCase());
                if(optionObj){
                    done(optionObj, 'add');
                }
            }
        }

        function getOptions(val, update) {
            update(() => {
                if(val.length > 2) {
                    const formData = new FormData();
                    formData.append('action', 'getAutocompleteCharacterList');
                    formData.append('term', val);
                    fetch(keyCharacterApiUrl, {
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

        function processChange(selectedObj) {
            context.emit('update:value', selectedObj);
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
