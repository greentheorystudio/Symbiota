const mofDataFieldRow = {
    props: {
        configuredData: {
            type: Object,
            default: {}
        },
        configuredDataFields: {
            type: Object,
            default: {}
        },
        editor: {
            type: Boolean,
            default: true
        },
        fields: {
            type: Array,
            default: []
        }
    },
    template: `
        <template v-if="editor">
            <div v-if="fields.length > 0" class="row justify-between q-col-gutter-sm">
                <template v-for="field in fields">
                    <div :ref="(element) => setElementRef(element, field.fieldName)">
                        <template v-if="configuredDataFields[field.fieldName]['dataType'] === 'boolean'">
                            <checkbox-input-element 
                                :definition="configuredDataFields[field.fieldName]['definition'] ? configuredDataFields[field.fieldName]['definition'] : null" 
                                :label="configuredDataFields[field.fieldName]['label']" 
                                :value="configuredData[field.fieldName]" 
                                @update:value="(value) => updateConfiguredEditData(field.fieldName, value)"
                            ></checkbox-input-element>
                        </template>
                        <template v-else-if="configuredDataFields[field.fieldName]['dataType'] === 'select'">
                            <selector-input-element 
                                :definition="configuredDataFields[field.fieldName]['definition'] ? configuredDataFields[field.fieldName]['definition'] : null" 
                                :options="configuredDataFields[field.fieldName]['options']" 
                                :label="configuredDataFields[field.fieldName]['label']" 
                                :value="configuredData[field.fieldName]" 
                                :clearable="true" 
                                @update:value="(value) => updateConfiguredEditData(field.fieldName, value)"
                            ></selector-input-element>
                        </template>
                        <template v-if="configuredDataFields[field.fieldName]['dataType'] === 'date'">
                            <date-input-element 
                                :definition="configuredDataFields[field.fieldName]['definition'] ? configuredDataFields[field.fieldName]['definition'] : null" 
                                :label="configuredDataFields[field.fieldName]['label']" 
                                :value="configuredData[field.fieldName]" 
                                @update:value="(value) => updateConfiguredEditData(field.fieldName, value.date)"
                            ></date-input-element>
                        </template>
                        <template v-else-if="configuredDataFields[field.fieldName]['dataType'] === 'int' || configuredDataFields[field.fieldName]['dataType'] === 'number' || configuredDataFields[field.fieldName]['dataType'] === 'string'">
                            <text-field-input-element 
                                :definition="configuredDataFields[field.fieldName]['definition'] ? configuredDataFields[field.fieldName]['definition'] : null" 
                                :data-type="configuredDataFields[field.fieldName]['dataType']" 
                                :label="configuredDataFields[field.fieldName]['label']" 
                                :max-value="configuredDataFields[field.fieldName]['maxValue'] ? configuredDataFields[field.fieldName]['maxValue'] : null" 
                                :min-value="configuredDataFields[field.fieldName]['minValue'] ? configuredDataFields[field.fieldName]['minValue'] : null" 
                                :value="configuredData[field.fieldName]" 
                                @update:value="(value) => updateConfiguredEditData(field.fieldName, value)"
                            ></text-field-input-element>
                        </template>
                    </div>
                </template>
            </div>
        </template>
        <template v-else-if="fields.length > 0">
            <template v-for="field in fields">
                <div class="row justify-start q-gutter-sm">
                    <div class="text-bold">
                        {{ (configuredDataFields[field.fieldName]['label'] ? configuredDataFields[field.fieldName]['label'] : field.fieldName) + ':' }}
                    </div>
                    <div>
                        {{ configuredData[field.fieldName] ? configuredData[field.fieldName] : '' }}
                    </div>
                </div>
            </template>
        </template>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'date-input-element': dateInputElement,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const dataFieldRefObject = {};

        function getClassName(size, value) {
            let className = '';
            if(size === 'sm'){
                className = 'col-' + value.toString();
            }
            else if(size === 'md'){
                className = 'col-sm-' + value.toString();
            }
            else if(size === 'lg'){
                className = 'col-md-' + value.toString();
            }
            return className;
        }

        function setElementRef(element, refIndex) {
            dataFieldRefObject[refIndex] = element;
        }

        function setStyling() {
            props.fields.forEach((field) => {
                if(field.hasOwnProperty('sm-col-width') && field['sm-col-width']){
                    dataFieldRefObject[field.fieldName].classList.add(getClassName('sm', field['sm-col-width']));
                }
                if(field.hasOwnProperty('md-col-width') && field['md-col-width']){
                    dataFieldRefObject[field.fieldName].classList.add(getClassName('md', field['md-col-width']));
                }
                if(field.hasOwnProperty('lg-col-width') && field['lg-col-width']){
                    dataFieldRefObject[field.fieldName].classList.add(getClassName('lg', field['lg-col-width']));
                }
            });
        }

        function updateConfiguredEditData(key, value) {
            context.emit('update:configured-edit-data', {key: key, value: value});
        }

        Vue.onMounted(() => {
            if(props.editor){
                setStyling();
            }
        });

        return {
            setElementRef,
            updateConfiguredEditData
        }
    }
};
