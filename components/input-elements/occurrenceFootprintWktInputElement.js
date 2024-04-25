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
        value: {
            type: String,
            default: null
        }
    },
    template: `
        <template v-if="showFootprintWktText">
            <div class="col-grow">
                <q-input outlined v-model="value" type="textarea" :label="label" @update:model-value="processValueChange" :readonly="disabled" autogrow dense>
                    <template v-if="!disabled && definition" v-slot:append>
                        <q-icon v-if="value" name="cancel" class="cursor-pointer" @click="processValueChange(null);">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Clear value
                            </q-tooltip>
                        </q-icon>
                        <q-icon name="hide_source" class="cursor-pointer" @click="showFootprintWktText = false">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Hide text display
                            </q-tooltip>
                        </q-icon>
                        <q-icon name="help" class="cursor-pointer" @click="openDefinitionPopup();">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                See field definition
                            </q-tooltip>
                        </q-icon>
                    </template>
                    <template v-else-if="!disabled" v-slot:append>
                        <q-icon v-if="value" name="cancel" class="cursor-pointer" @click="processValueChange(null);">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Clear value
                            </q-tooltip>
                        </q-icon>
                        <q-icon name="keyboard_off" class="cursor-pointer" @click="showFootprintWktText = false">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Hide text display
                            </q-tooltip>
                        </q-icon>
                    </template>
                </q-input>
            </div>
        </template>
        <template v-else>
            <div class="q-ml-md text-bold self-center">
                <div>
                    <template v-if="value">
                        Footprint WKT saved
                    </template>
                    <template v-else>
                        No Footprint WKT saved
                    </template>
                </div>
            </div>
            <div v-if="!disabled" class="row justify-start q-gutter-sm">
                <div class="self-center">
                    <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openSpatialPopup('input-polygon,wkt');" icon="fas fa-globe" dense>
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Open Mapping Aid
                        </q-tooltip>
                    </q-btn>
                </div>
                <div v-if="value" class="self-center">
                    <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="processValueChange(null);" icon="cancel" dense>
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Clear value
                        </q-tooltip>
                    </q-btn>
                </div>
                <div class="self-center">
                    <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="showFootprintWktText = true" icon="keyboard" dense>
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Show text display
                        </q-tooltip>
                    </q-btn>
                </div>
                <template v-if="definition">
                    <div class="self-center">
                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openDefinitionPopup();" icon="help" dense>
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                See field definition
                            </q-tooltip>
                        </q-btn>
                    </div>
                </template>
            </div>
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
        const displayDefinitionPopup = Vue.ref(false);
        const showFootprintWktText = Vue.ref(false);

        const openSpatialPopup = Vue.inject('openSpatialPopup');

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
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
