const checkboxInputElement = {
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
        <div class="row inline q-gutter-x-xs">
            <div>
                <q-checkbox v-model="checkboxValue" :label="label" :disable="disabled" @update:model-value="processValueChange" :tabindex="tabindex" dense></q-checkbox>
            </div>
            <div v-if="!disabled && definition" class="self-center">
                <q-icon role="button" name="help" size="sm" class="cursor-pointer q-ma-none" color="grey-7" @click="openDefinitionPopup();" @keyup.enter="openDefinitionPopup();" aria-label="See field definition" :tabindex="tabindex"></q-icon>
            </div>
        </div>
        <template v-if="definition">
            <q-dialog class="z-max" v-model="displayDefinitionPopup" persistent aria-label="Definition pop up">
                <q-card class="sm-popup">
                    <div class="row justify-end items-start map-sm-popup">
                        <div>
                            <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="displayDefinitionPopup = false" aria-label="Close definition pop up" :tabindex="tabindex"></q-btn>
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
                                <a :href="definition.source" target="_blank" aria-label="External link: Go to source - Opens in separate tab" :tabindex="tabindex"><span class="text-bold">Go to source</span></a>
                            </div>
                        </template>
                    </div>
                </q-card>
            </q-dialog>
        </template>
    `,
    setup(props, context) {
        const checkboxValue = Vue.ref(false);
        const displayDefinitionPopup = Vue.ref(false);
        const inputType = Vue.ref('text');
        const propsRefs = Vue.toRefs(props);

        Vue.watch(propsRefs.value, () => {
            setCheckboxValue();
        });

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processValueChange(val) {
            setCheckboxValue();
            context.emit('update:value', (val ? 1 : null));
        }

        function setCheckboxValue() {
            checkboxValue.value = Number(props.value) === 1;
        }

        Vue.onMounted(() => {
            setCheckboxValue();
        });

        return {
            checkboxValue,
            displayDefinitionPopup,
            inputType,
            openDefinitionPopup,
            processValueChange
        }
    }
};
