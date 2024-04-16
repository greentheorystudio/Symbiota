const textFieldInputElement = {
    props: {
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
            default: ''
        },
        maxlength: {
            type: Number,
            default: null
        },
        showcounter: {
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
            <template v-if="showcounter">
                <q-input outlined v-model="value" :label="label" counter :maxlength="maxlength" @update:model-value="processValueChange" dense>
                    <template v-if="definition" v-slot:append>
                        <q-icon name="cancel" class="cursor-pointer" @click="processValueChange(null);"></q-icon>
                        <q-icon name="help" class="cursor-pointer" @click="openDefinitionPopup();"></q-icon>
                    </template>
                    <template v-else v-slot:append>
                        <q-icon name="cancel" class="cursor-pointer" @click="processValueChange(null);"></q-icon>
                    </template>
                </q-input>
            </template>
            <template v-else>
                <q-input outlined v-model="value" :label="label" :maxlength="maxlength" @update:model-value="processValueChange" dense>
                    <template v-if="definition" v-slot:append>
                        <q-icon name="cancel" class="cursor-pointer" @click="processValueChange(null);"></q-icon>
                        <q-icon name="help" class="cursor-pointer" @click="openDefinitionPopup();"></q-icon>
                    </template>
                    <template v-else v-slot:append>
                        <q-icon name="cancel" class="cursor-pointer" @click="processValueChange(null);"></q-icon>
                    </template>
                </q-input>
            </template>
        </template>
        <template v-else>
            <q-input outlined v-model="value" :label="label" @update:model-value="processValueChange" :readonly="disabled" dense>
                <template v-if="!disabled && definition" v-slot:append>
                    <q-icon name="cancel" class="cursor-pointer" @click="processValueChange(null);"></q-icon>
                    <q-icon name="help" class="cursor-pointer" @click="openDefinitionPopup();"></q-icon>
                </template>
                <template v-else-if="!disabled" v-slot:append>
                    <q-icon name="cancel" class="cursor-pointer" @click="processValueChange(null);"></q-icon>
                </template>
            </q-input>
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
    setup(_, context) {
        const displayDefinitionPopup = Vue.ref(false);

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processValueChange(val) {
            context.emit('update:value', val);
        }

        return {
            displayDefinitionPopup,
            openDefinitionPopup,
            processValueChange
        }
    }
};
