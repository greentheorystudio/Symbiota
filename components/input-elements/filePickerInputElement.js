const filePickerInputElement = {
    props: {
        acceptedTypes: {
            type: Array,
            default: []
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
            default: 'Choose File'
        },
        validateFileSize: {
            type: Boolean,
            default: true
        },
        value: {
            type: Object,
            default: null
        }
    },
    template: `
        <q-file ref="pickerRef" outlined bg-color="white" v-model="value" :label="label" :disable="disabled" :filter="validateFiles" dense>
            <template v-slot:prepend>
                <q-icon name="upload_file" class="cursor-pointer" @click="pickerRef.pickFiles();"></q-icon>
            </template>
            <template v-if="!disabled" v-slot:append>
                <q-icon v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        See field definition
                    </q-tooltip>
                </q-icon>
                <q-icon v-if="value" name="cancel" class="cursor-pointer" @click="clearValue();">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Clear File
                    </q-tooltip>
                </q-icon>
            </template>
        </q-file>
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
        const { showNotification } = useCore();
        const baseStore = useBaseStore();

        const displayDefinitionPopup = Vue.ref(false);
        const maxUploadFilesize = baseStore.getMaxUploadFilesize;
        const pickerRef = Vue.ref(null);

        function clearValue() {
            context.emit('update:file', null);
        }

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function validateFiles(files) {
            const fileArr = [];
            files.forEach((file) => {
                const fileSizeMb = Number(file.size) > 0 ? Math.round((file.size / 1000000) * 10) / 100 : 0;
                if(!props.validateFileSize || fileSizeMb <= Number(maxUploadFilesize)){
                    if(props.acceptedTypes.includes(file.name.split('.').pop().toLowerCase())){
                        fileArr.push(file);
                    }
                    else{
                        showNotification('negative', (file.name + ' cannot be uploaded because it is not one of the following file types: ' + props.acceptedTypes.join(', ')));
                    }
                }
                else{
                    showNotification('negative', (file.name + ' cannot be uploaded because it is ' + fileSizeMb.toString() + 'MB, which exceeds the server limit of ' + maxUploadFilesize.toString() + 'MB for uploads.'));
                }
            });
            context.emit('update:file', (fileArr.length > 0 ? fileArr : null));
            return fileArr;
        }

        return {
            displayDefinitionPopup,
            pickerRef,
            clearValue,
            openDefinitionPopup,
            validateFiles
        }
    }
};
