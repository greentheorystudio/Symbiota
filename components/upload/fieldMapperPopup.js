const fieldMapperPopup = {
    props: {
        fieldMapping: {
            type: Object,
            default: {}
        },
        showPopup: {
            type: Boolean,
            default: false
        },
        sourceFields: {
            type: Object,
            default: {}
        },
        targetFields: {
            type: Array,
            default: []
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="md-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="full-width q-pa-md">
                            <div class="row">
                                <div class="col-4 row upload-field-mapper-grid-cell text-body1 text-bold justify-center content-center">
                                    <div>Source Field</div>
                                </div>
                                <div class="col-8 row upload-field-mapper-grid-cell text-body1 text-bold justify-center content-center">
                                    Target Field
                                </div>
                            </div>
                            <template v-for="sourceField in Object.keys(sourceFields)">
                                <template v-if="fieldMapping[sourceField.toLowerCase()] !== 'dbpk' && fieldMapping[sourceField.toLowerCase()] !== 'eventdbpk'">
                                    <div class="row">
                                        <div class="col-4 q-pl-md upload-field-mapper-grid-cell text-body1 content-center" :class="fieldMapping[sourceField.toLowerCase()] === 'unmapped' ? 'bg-grey-5' : ''">
                                            {{ sourceFields[sourceField] }}
                                        </div>
                                        <div class="col-8 q-pl-sm upload-field-mapper-grid-cell" :class="fieldMapping[sourceField.toLowerCase()] === 'unmapped' ? 'bg-grey-5' : ''">
                                            <selector-input-element :options="targetFields" :value="fieldMapping[sourceField.toLowerCase()]" @update:value="(value) => updateFieldMapping(sourceFields[sourceField], value)"></selector-input-element>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'selector-input-element': selectorInputElement
    },
    setup(props, context) {
        const { showNotification } = useCore();

        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function updateFieldMapping(field, targetFieldValue) {
            const usedField = Object.keys(props.fieldMapping).find(field => props.fieldMapping[field] === targetFieldValue);
            if(!usedField || targetFieldValue === 'unmapped'){
                context.emit('update:field-mapping', {sourceField: field.toLowerCase(), targetField: targetFieldValue});
            }
            else{
                showNotification('negative', 'That Target Field is already mapped to a Source Field.');
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            contentRef,
            contentStyle,
            closePopup,
            updateFieldMapping
        }
    }
};
