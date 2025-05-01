const fieldMapperPopup = {
    props: {
        disabled: {
            type: Boolean,
            default: false
        },
        fieldMapping: {
            type: Object,
            default: {}
        },
        mappingType: {
            type: String,
            default: 'occurrence'
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
            <q-card class="overflow-hidden" :class="(mappingType === 'occurrence' || mappingType === 'flat-file') ? 'lg-popup' : 'md-popup'">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="full-width q-pa-md">
                            <div class="row">
                                <template v-if="mappingType === 'occurrence' || mappingType === 'flat-file'">
                                    <div class="col-2 row upload-field-mapper-grid-cell text-body1 text-bold justify-center content-center">
                                        Source Field
                                    </div>
                                    <div class="col-5 row upload-field-mapper-grid-cell text-body1 text-bold justify-center content-center">
                                        Primary Target Field
                                    </div>
                                    <div class="col-5 row upload-field-mapper-grid-cell text-body1 text-bold justify-center content-center">
                                        Secondary Target Field
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="col-4 row upload-field-mapper-grid-cell text-body1 text-bold justify-center content-center">
                                        Source Field
                                    </div>
                                    <div class="col-8 row upload-field-mapper-grid-cell text-body1 text-bold justify-center content-center">
                                        Target Field
                                    </div>
                                </template>
                            </div>
                            <template v-for="sourceField in Object.keys(sourceFields)">
                                <template v-if="fieldMapping[sourceFields[sourceField].toLowerCase()] !== 'dbpk' && fieldMapping[sourceFields[sourceField].toLowerCase()] !== 'eventdbpk'">
                                    <div class="row">
                                        <template v-if="mappingType === 'occurrence' || mappingType === 'flat-file'">
                                            <template v-if="sourceFields[sourceField].toLowerCase() !== sourceEventPrimaryKey.toLowerCase() && sourceFields[sourceField].toLowerCase() !== sourcePrimaryKey.toLowerCase()">
                                                <div class="col-2 q-pl-md upload-field-mapper-grid-cell text-body1 content-center" :class="(primaryFieldMapping[sourceFields[sourceField].toLowerCase()] === 'unmapped' && secondaryFieldMapping[sourceFields[sourceField].toLowerCase()] === 'unmapped') ? 'bg-grey-5' : ''">
                                                    {{ sourceFields[sourceField] }}
                                                </div>
                                                <div class="col-5 q-pl-sm upload-field-mapper-grid-cell" :class="(primaryFieldMapping[sourceFields[sourceField].toLowerCase()] === 'unmapped' && secondaryFieldMapping[sourceFields[sourceField].toLowerCase()] === 'unmapped') ? 'bg-grey-5' : ''">
                                                    <selector-input-element :disabled="disabled" :options="targetFieldOptions" :value="primaryFieldMapping[sourceFields[sourceField].toLowerCase()]" @update:value="(value) => updateFieldMapping(sourceFields[sourceField], value)"></selector-input-element>
                                                </div>
                                                <div class="col-5 q-pl-sm upload-field-mapper-grid-cell" :class="(primaryFieldMapping[sourceFields[sourceField].toLowerCase()] === 'unmapped' && secondaryFieldMapping[sourceFields[sourceField].toLowerCase()] === 'unmapped') ? 'bg-grey-5' : ''">
                                                    <selector-input-element :disabled="disabled" :options="targetFieldOptions" :value="secondaryFieldMapping[sourceFields[sourceField].toLowerCase()]" @update:value="(value) => updateFieldMapping(sourceFields[sourceField], value, false)"></selector-input-element>
                                                </div>
                                            </template>
                                        </template>
                                        <template v-else>
                                            <template v-if="sourceFields[sourceField].toLowerCase() !== sourceEventPrimaryKey.toLowerCase() && sourceFields[sourceField].toLowerCase() !== sourcePrimaryKey.toLowerCase()">
                                                <div class="col-4 q-pl-md upload-field-mapper-grid-cell text-body1 content-center" :class="fieldMapping[sourceFields[sourceField].toLowerCase()] === 'unmapped' ? 'bg-grey-5' : ''">
                                                    {{ sourceFields[sourceField] }}
                                                </div>
                                                <div class="col-8 q-pl-sm upload-field-mapper-grid-cell" :class="fieldMapping[sourceFields[sourceField].toLowerCase()] === 'unmapped' ? 'bg-grey-5' : ''">
                                                    <selector-input-element :disabled="disabled" :options="targetFieldOptions" :value="fieldMapping[sourceFields[sourceField].toLowerCase()]" @update:value="(value) => updateFieldMapping(sourceFields[sourceField], value)"></selector-input-element>
                                                </div>
                                            </template>
                                        </template>
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
        const primaryFieldMapping = Vue.computed(() => {
            return props.fieldMapping['primary'];
        });
        const secondaryFieldMapping = Vue.computed(() => {
            return props.fieldMapping['secondary'];
        });
        const sourceEventPrimaryKey = Vue.computed(() => {
            let returnVal;
            if(props.mappingType === 'occurrence' || props.mappingType === 'flat-file'){
                returnVal = Object.keys(primaryFieldMapping.value).find(field => primaryFieldMapping.value[field] === 'eventdbpk');
            }
            else{
                returnVal = Object.keys(props.fieldMapping).find(field => props.fieldMapping[field] === 'eventdbpk');
            }
            return (returnVal ? returnVal : '');
        });
        const sourcePrimaryKey = Vue.computed(() => {
            let returnVal;
            if(props.mappingType === 'occurrence' || props.mappingType === 'flat-file'){
                returnVal = Object.keys(primaryFieldMapping.value).find(field => primaryFieldMapping.value[field] === 'dbpk');
            }
            else{
                returnVal = Object.keys(props.fieldMapping).find(field => props.fieldMapping[field] === 'dbpk');
            }
            return (returnVal ? returnVal : '');
        });
        const targetFieldOptions = Vue.computed(() => {
            const initialArr = [
                {value: 'unmapped', label: 'UNMAPPED'}
            ];
            return initialArr.concat(props.targetFields);
        });

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

        function updateFieldMapping(field, targetFieldValue, primary = true) {
            let usedField;
            if(props.mappingType === 'occurrence' || props.mappingType === 'flat-file'){
                usedField = Object.keys(primaryFieldMapping.value).find(field => primaryFieldMapping.value[field] === targetFieldValue);
                if(!usedField){
                    usedField = Object.keys(secondaryFieldMapping.value).find(field => secondaryFieldMapping.value[field] === targetFieldValue);
                }
            }
            else{
                usedField = Object.keys(props.fieldMapping).find(field => props.fieldMapping[field] === targetFieldValue);
            }
            if(!usedField || targetFieldValue === 'unmapped'){
                context.emit('update:field-mapping', {sourceField: field.toLowerCase(), targetField: targetFieldValue, primary: primary});
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
            primaryFieldMapping,
            secondaryFieldMapping,
            sourceEventPrimaryKey,
            sourcePrimaryKey,
            targetFieldOptions,
            closePopup,
            updateFieldMapping
        }
    }
};
