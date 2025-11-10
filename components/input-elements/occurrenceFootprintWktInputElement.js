const occurrenceFootprintWktInputElement = {
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
        <template v-if="showFootprintWktText">
            <div class="col-grow">
                <q-input outlined v-model="value" type="textarea" :label="label" bg-color="white" @update:model-value="processValueChange" :readonly="disabled" autogrow dense :tabindex="tabindex">
                    <template v-if="!disabled" v-slot:append>
                        <q-icon role="button" v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();" @keyup.enter="openDefinitionPopup();" aria-label="See field definition" :tabindex="tabindex">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                See field definition
                            </q-tooltip>
                        </q-icon>
                        <q-icon role="button" v-if="value" name="cancel" class="cursor-pointer" @click="processValueChange(null);" @keyup.enter="processValueChange(null);" aria-label="Clear value" :tabindex="tabindex">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Clear value
                            </q-tooltip>
                        </q-icon>
                        <q-icon role="button" name="hide_source" class="cursor-pointer" @click="showFootprintWktText = false" @keyup.enter="showFootprintWktText = false" aria-label="Hide text display" :tabindex="tabindex">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Hide text display
                            </q-tooltip>
                        </q-icon>
                    </template>
                </q-input>
            </div>
        </template>
        <template v-else>
            <div class="text-bold self-center">
                <div>
                    <template v-if="value">
                        <span class="text-green-9">Footprint WKT saved</span>
                    </template>
                    <template v-else>
                        <span class="text-red-9">No Footprint WKT saved</span>
                    </template>
                </div>
            </div>
            <div v-if="!disabled" class="row justify-start q-gutter-sm no-wrap">
                <template v-if="definition">
                    <div class="self-center">
                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openDefinitionPopup();" icon="help" dense aria-label="Open definition pop up" :tabindex="tabindex">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                See field definition
                            </q-tooltip>
                        </q-btn>
                    </div>
                </template>
                <div v-if="value" class="self-center">
                    <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="processValueChange(null);" icon="cancel" dense aria-label="Clear value" :tabindex="tabindex">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Clear value
                        </q-tooltip>
                    </q-btn>
                </div>
                <div class="self-center">
                    <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openSpatialPopup('input-polygon,wkt');" icon="fas fa-globe" dense aria-label="Open Mapping Aid" :tabindex="tabindex">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Open Mapping Aid
                        </q-tooltip>
                    </q-btn>
                </div>
                <div class="self-center">
                    <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="showFootprintWktText = true" icon="keyboard" dense aria-label="Show text display" :tabindex="tabindex">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Show text display
                        </q-tooltip>
                    </q-btn>
                </div>
            </div>
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
        const displayDefinitionPopup = Vue.ref(false);
        const showFootprintWktText = Vue.ref(false);

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function openSpatialPopup(type) {
            context.emit('open:spatial-popup', type);
        }

        function processValueChange(val) {
            context.emit('update:value', val);
        }

        return {
            displayDefinitionPopup,
            showFootprintWktText,
            openDefinitionPopup,
            openSpatialPopup,
            processValueChange
        }
    }
};
