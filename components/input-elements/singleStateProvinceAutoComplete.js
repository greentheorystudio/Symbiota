const singleStateProvinceAutoComplete = {
    props: {
        country: {
            type: String,
            default: null
        },
        definition: {
            type: Object,
            default: null
        },
        disabled: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: 'State/Province'
        },
        maxlength: {
            type: Number,
            default: null
        },
        showCounter: {
            type: Boolean,
            default: true
        },
        value: {
            type: String,
            default: null
        }
    },
    template: `
        <template v-if="!disabled && maxlength && Number(maxlength) > 0">
            <q-select v-model="value" use-input hide-selected fill-input outlined dense options-dense hide-dropdown-icon input-debounce="0" @blur="blurAction" :options="autocompleteOptions" option-label="name" @filter="getOptions" @update:model-value="processValueChange" :counter="showCounter" :maxlength="maxlength" :label="label" :disable="disabled">
                <template v-if="definition" v-slot:append>
                    <q-icon name="cancel" class="cursor-pointer" @click="processValueChange(null);"></q-icon>
                    <q-icon name="help" class="cursor-pointer" @click="openDefinitionPopup();"></q-icon>
                </template>
                <template v-else v-slot:append>
                    <q-icon name="cancel" class="cursor-pointer" @click="processValueChange(null);"></q-icon>
                </template>
            </q-select>
        </template>
        <template v-else>
            <q-select v-model="value" use-input hide-selected fill-input outlined dense options-dense hide-dropdown-icon input-debounce="0" @blur="blurAction" :options="autocompleteOptions" option-label="name" @filter="getOptions" @update:model-value="processValueChange" :counter="showCounter" :maxlength="maxlength" :label="label" :disable="disabled">
                <template v-if="!disabled && definition" v-slot:append>
                    <q-icon name="cancel" class="cursor-pointer" @click="processValueChange(null);"></q-icon>
                    <q-icon name="help" class="cursor-pointer" @click="openDefinitionPopup();"></q-icon>
                </template>
                <template v-else-if="!disabled" v-slot:append>
                    <q-icon name="cancel" class="cursor-pointer" @click="processValueChange(null);"></q-icon>
                </template>
            </q-select>
        </template>
        <template v-if="definition">
            <q-dialog class="z-top" v-model="displayDefinitionPopup" persistent>
                <q-card class="sm-popup">
                    <div class="row justify-end items-start map-sm-popup">
                        <div>
                            <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="displayDefinitionPopup = false"></q-btn>
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
                                <a :href="definition.source" target="_blank"><span class="text-bold">Go to source</span></a>
                            </div>
                        </template>
                    </div>
                </q-card>
            </q-dialog>
        </template>
    `,
    setup(props, context) {
        const autocompleteOptions = Vue.ref([]);
        const displayDefinitionPopup = Vue.ref(false);

        function blurAction(val) {
            context.emit('update:value', ((val.target.value.length > 0) ? val.target.value : null));
        }

        function getOptions(val, update) {
            update(() => {
                if(val.length > 2) {
                    const formData = new FormData();
                    formData.append('action', 'getAutocompleteStateProvinceList');
                    formData.append('country', props.country);
                    formData.append('term', val);
                    fetch(geographyApiUrl, {
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

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processValueChange(selectedObj) {
            context.emit('update:value', (selectedObj ? selectedObj.name : null));
        }

        return {
            autocompleteOptions,
            displayDefinitionPopup,
            blurAction,
            getOptions,
            openDefinitionPopup,
            processValueChange
        }
    }
};
