const taxaKingdomSelector = {
    props: {
        clearable: {
            type: Boolean,
            default: false
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
            default: null
        },
        setOptions: {
            type: Array,
            default: []
        },
        selectedKingdom: {
            type: Object,
            default: null
        },
        tabindex: {
            type: Number,
            default: 0
        }
    },
    template: `
        <q-select v-model="selectedKingdom" outlined dense options-dense input-debounce="500" bg-color="white" popup-content-class="z-top" behavior="menu" input-class="z-top" :options="kingdomOpts" option-value="id" option-label="name" :label="label" @update:model-value="processChange" :tabindex="tabindex" :readonly="disabled">
            <template v-if="!disabled && (definition || (clearable && selectedKingdom))" v-slot:append>
                <q-icon role="button" v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();" @keyup.enter="openDefinitionPopup();" aria-label="See field definition" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        See field definition
                    </q-tooltip>
                </q-icon>
                <q-icon role="button" v-if="clearable && selectedKingdom" name="cancel" class="cursor-pointer" @click="clearValue();" @keyup.enter="clearValue();" aria-label="Clear value" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Clear value
                    </q-tooltip>
                </q-icon>
            </template>
        </q-select>
        <template v-if="definition">
            <q-dialog class="z-top" v-model="displayDefinitionPopup" persistent aria-label="Definition pop up">
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
        const kingdomOpts = Vue.ref([]);

        function clearValue() {
            processChange(null);
        }

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processChange(kingdomobj) {
            context.emit('update:selected-kingdom', kingdomobj);
        }

        function setKingdomOptions() {
            const url = taxonKingdomApiUrl + '?action=getKingdomArr';
            fetch(url)
            .then((response) => {
                if(response.ok){
                    return response.json();
                }
            })
            .then((data) => {
                if(props.setOptions.length > 0){
                    data.forEach((taxa) => {
                        if(props.setOptions.includes(taxa.name)){
                            kingdomOpts.value.push(taxa);
                        }
                    });
                } else {
                    kingdomOpts.value = data;
                }
            });
        }

        Vue.onMounted(() => {
            setKingdomOptions();
        });

        return {
            displayDefinitionPopup,
            kingdomOpts,
            clearValue,
            openDefinitionPopup,
            processChange
        }
    }
};
