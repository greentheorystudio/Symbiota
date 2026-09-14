const singleLocationAutoComplete = {
    props: {
        disabled: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: 'Location Name'
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
        <q-select v-model="value" use-input hide-selected fill-input outlined dense options-dense hide-dropdown-icon popup-content-class="z-top" behavior="menu" input-debounce="0" bg-color="white" @blur="blurAction" @new-value="createValue" :options="autocompleteOptions" @filter="getOptions" @update:model-value="processValueChange" :label="label" :tabindex="tabindex" :disable="disabled">
            <template v-if="!disabled && value" v-slot:append>
                <q-icon role="button" v-if="value" name="cancel" class="cursor-pointer" @click="processValueChange(null);" @keyup.enter="processValueChange(null);" aria-label="Clear value" :tabindex="tabindex">
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
            if(autocompleteOptions.value.length > 0 && !props.value){
                showNotification('negative', 'Please select one of the locations from the dropdown list');
            }
        }

        function createValue(val, done) {
            if(val.length > 0) {
                showNotification('negative', 'Please select one of the locations from the dropdown list');
            }
        }

        function getOptions(val, update) {
            update(() => {
                if(val.length > 0) {
                    const formData = new FormData();
                    formData.append('action', 'getAutocompleteLocationList');
                    formData.append('term', val);
                    fetch(institutionsApiUrl, {
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

        function processValueChange(selectedObj) {
            context.emit('update:value', (selectedObj ? selectedObj : null));
        }

        return {
            autocompleteOptions,
            blurAction,
            createValue,
            getOptions,
            processValueChange
        }
    }
};
