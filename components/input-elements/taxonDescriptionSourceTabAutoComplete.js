const taxonDescriptionSourceTabAutoComplete = {
    props: {
        label: {
            type: String,
            default: 'Taxon Description Source Tab'
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
        <q-select v-model="value" use-input hide-selected fill-input outlined dense options-dense hide-dropdown-icon popup-content-class="z-max" behavior="menu" input-debounce="0" bg-color="white" @new-value="createValue" @blur="blurAction" :options="autocompleteOptions" option-label="name" @filter="getOptions" @update:model-value="processValueChange" :label="label" :tabindex="tabindex">
            <template v-if="value" v-slot:append>
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
            if(val.target.value && val.target.value !== props.value){
                const optionObj = autocompleteOptions.value.find(option => option['name'].toLowerCase() === val.target.value.trim().toLowerCase());
                if(optionObj){
                    processValueChange(optionObj);
                }
                else{
                    showNotification('negative', 'That source tab does not exist');
                }
            }
        }

        function createValue(val, done) {
            if(val.length > 0) {
                const optionObj = autocompleteOptions.value.find(option => option['name'].toLowerCase() === val.trim().toLowerCase());
                if(optionObj){
                    done(optionObj, 'add');
                }
                else{
                    showNotification('negative', 'That source tab does not exist');
                }
            }
        }

        function getOptions(val, update) {
            update(() => {
                if(val.length > 2) {
                    const formData = new FormData();
                    formData.append('action', 'getAutocompleteCaptionList');
                    formData.append('term', val);
                    fetch(taxonDescriptionBlockApiUrl, {
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
            context.emit('update:value', (selectedObj ? selectedObj.name : null));
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
