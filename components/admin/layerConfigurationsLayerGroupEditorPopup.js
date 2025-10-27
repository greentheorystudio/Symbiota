const layerConfigurationsLayerGroupEditorPopup = {
    props: {
        layerGroup: {
            type: Object,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog v-if="layerGroup" class="z-top" v-model="showPopup" persistent>
            <q-card class="sm-popup">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div class="q-pa-md column q-col-gutter-sm">
                    <div class="row justify-between">
                        <div>
                            <template v-if="Number(layerGroup.id) > 0 && editsExist">
                                <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                            </template>
                        </div>
                        <div class="row justify-end q-gutter-sm">
                            <template v-if="Number(layerGroup.id) > 0">
                                <q-btn color="secondary" @click="updateLayerGroup();" label="Save Edits" :disabled="!editsExist || !editDataValid" />
                                <q-btn v-if="layerGroup.layers.length === 0" color="negative" @click="deleteLayerGroup();" label="Remove" />
                            </template>
                            <template v-else>
                                <q-btn color="secondary" @click="addLayerGroup();" label="Add Layer Group" :disabled="!editDataValid" />
                            </template>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element label="Group Name" :value="editData['name']" @update:value="(value) => editData['name'] = value"></text-field-input-element>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const editData = Vue.ref(null);
        const editDataValid = Vue.computed(() => {
            return !!editData.value.name;
        });
        const editsExist = Vue.computed(() => {
            return props.layerGroup.name !== editData.value.name;
        });

        function addLayerGroup() {
            editData.value['id'] = Date.now();
            context.emit('add:layer-group', editData.value);
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function deleteLayerGroup() {
            context.emit('delete:layer-group', editData.value);
        }

        function updateLayerGroup() {
            context.emit('update:layer-group', editData.value);
        }

        Vue.onMounted(() => {
            editData.value = Object.assign({}, props.layerGroup);
        });
        
        return {
            editData,
            editDataValid,
            editsExist,
            closePopup,
            addLayerGroup,
            deleteLayerGroup,
            updateLayerGroup
        }
    }
};
