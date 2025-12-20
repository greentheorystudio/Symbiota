const textFieldInputElement = {
    props: {
        clearable: {
            type: Boolean,
            default: true
        },
        dataType: {
            type: String,
            default: 'text'
        },
        debounce: {
            type: Number,
            default: 700
        },
        definition: {
            type: Object,
            default: null
        },
        disabled: {
            type: Boolean,
            default: false
        },
        field: {
            type: String,
            default: ''
        },
        fieldHint: {
            type: String,
            default: null
        },
        label: {
            type: String,
            default: ''
        },
        maxlength: {
            type: Number,
            default: null
        },
        maxValue: {
            type: Number,
            default: null
        },
        minValue: {
            type: Number,
            default: null
        },
        showCounter: {
            type: Boolean,
            default: false
        },
        step: {
            type: Number,
            default: 1
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
        <template v-if="fieldHint">
            <template v-if="!disabled && maxlength && Number(maxlength) > 0">
                <q-input outlined v-model="value" :type="inputType" :step="step" :label="label" :debounce="debounce" bg-color="white" :counter="(showCounter && dataType !== 'int' && dataType !== 'increment' && dataType !== 'number')" :maxlength="maxlength" @update:model-value="processValueChange" @keyup.enter="processEnterClick" :autogrow="inputType === 'textarea'" :hint="fieldHint" :tabindex="tabindex" :name="field" :autocomplete="field" dense>
                    <template v-if="(value && clearable) || definition" v-slot:append>
                        <q-icon role="button" v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();" @keyup.enter="openDefinitionPopup();" aria-label="See field definition" :tabindex="tabindex">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                See field definition
                            </q-tooltip>
                        </q-icon>
                        <q-icon role="button" v-if="value && clearable" name="cancel" class="cursor-pointer" @click="processValueChange(null);" @keyup.enter="processValueChange(null);" aria-label="Clear value" :tabindex="tabindex">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Clear value
                            </q-tooltip>
                        </q-icon>
                    </template>
                </q-input>
            </template>
            <template v-else>
                <q-input outlined v-model="value" :type="inputType" :step="step" :label="label" :debounce="debounce" bg-color="white" @update:model-value="processValueChange" @keyup.enter="processEnterClick" :readonly="disabled" :autogrow="inputType === 'textarea'" :hint="fieldHint" :tabindex="tabindex" :name="field" :autocomplete="field" dense>
                    <template v-if="!disabled && ((value && clearable) || definition)" v-slot:append>
                        <q-icon role="button" v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();" @keyup.enter="openDefinitionPopup();" aria-label="See field definition" :tabindex="tabindex">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                See field definition
                            </q-tooltip>
                        </q-icon>
                        <q-icon role="button" v-if="value && clearable" name="cancel" class="cursor-pointer" @click="processValueChange(null);" @keyup.enter="processValueChange(null);" aria-label="Clear value" :tabindex="tabindex">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Clear value
                            </q-tooltip>
                        </q-icon>
                    </template>
                </q-input>
            </template>
        </template>
        <template v-else>
            <template v-if="!disabled && maxlength && Number(maxlength) > 0">
                <q-input outlined v-model="value" :type="inputType" :step="step" :label="label" :debounce="debounce" bg-color="white" :counter="(showCounter && dataType !== 'int' && dataType !== 'increment' && dataType !== 'number')" :maxlength="maxlength" @update:model-value="processValueChange" @keyup.enter="processEnterClick" :autogrow="inputType === 'textarea'" :tabindex="tabindex" :name="field" :autocomplete="field" dense>
                    <template v-if="(value && clearable) || definition" v-slot:append>
                        <q-icon role="button" v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();" @keyup.enter="openDefinitionPopup();" aria-label="See field definition" :tabindex="tabindex">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                See field definition
                            </q-tooltip>
                        </q-icon>
                        <q-icon role="button" v-if="value && clearable" name="cancel" class="cursor-pointer" @click="processValueChange(null);" @keyup.enter="processValueChange(null);" aria-label="Clear value" :tabindex="tabindex">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Clear value
                            </q-tooltip>
                        </q-icon>
                    </template>
                </q-input>
            </template>
            <template v-else>
                <q-input outlined v-model="value" :type="inputType" :step="step" :label="label" :debounce="debounce" bg-color="white" @update:model-value="processValueChange" @keyup.enter="processEnterClick" :readonly="disabled" :autogrow="inputType === 'textarea'" :tabindex="tabindex" :name="field" :autocomplete="field" dense>
                    <template v-if="!disabled && ((value && clearable) || definition)" v-slot:append>
                        <q-icon role="button" v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();" @keyup.enter="openDefinitionPopup();" aria-label="See field definition" :tabindex="tabindex">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                See field definition
                            </q-tooltip>
                        </q-icon>
                        <q-icon role="button" v-if="value && clearable" name="cancel" class="cursor-pointer" @click="processValueChange(null);" @keyup.enter="processValueChange(null);" aria-label="Clear value" :tabindex="tabindex">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Clear value
                            </q-tooltip>
                        </q-icon>
                    </template>
                </q-input>
            </template>
        </template>
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
        const { showNotification } = useCore();

        const displayDefinitionPopup = Vue.ref(false);
        const inputType = Vue.computed(() => {
            let returnVal;
            if(props.dataType === 'int' || props.dataType === 'increment'){
                returnVal = 'number';
            }
            else if(props.dataType === 'text' || props.dataType === 'number'){
                returnVal = 'text';
            }
            else{
                returnVal = props.dataType;
            }
            return returnVal;
        });

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processEnterClick() {
            context.emit('click:enter');
        }

        function processValueChange(val) {
            if(props.dataType === 'int' || props.dataType === 'number' || props.dataType === 'increment'){
                if(val && isNaN(val)){
                    showNotification('negative', (props.label + ' must be a number.'));
                }
                else if(val && props.minValue && Number(props.minValue) > Number(val)){
                    showNotification('negative', (props.label + ' cannot be less than ' + props.minValue + '.'));
                    if(props.dataType === 'int' || props.dataType === 'increment'){
                        context.emit('update:value', props.minValue);
                    }
                }
                else if(val && props.maxValue && Number(props.maxValue) < Number(val)){
                    showNotification('negative', (props.label + ' cannot be greater than ' + props.maxValue + '.'));
                    if(props.dataType === 'int' || props.dataType === 'increment'){
                        context.emit('update:value', props.maxValue);
                    }
                }
                else{
                    context.emit('update:value', val);
                }
            }
            else{
                context.emit('update:value', val);
            }
        }

        return {
            displayDefinitionPopup,
            inputType,
            openDefinitionPopup,
            processEnterClick,
            processValueChange
        }
    }
};
