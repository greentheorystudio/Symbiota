const configuredDataFieldRow = {
    props: {
        fields: {
            type: Array,
            default: []
        }
    },
    template: `
        <div v-if="fields.length > 0" class="row justify-between q-col-gutter-sm">
            <template v-for="field in fields">
                <div :ref="(element) => setElementRef(element, field.fieldName)">
                    <template v-if="configuredDataFields[field.fieldName]['dataType'] === 'boolean'">
                        <checkbox-input-element 
                            :definition="configuredDataFields[field.fieldName]['definition'] ? configuredDataFields[field.fieldName]['definition'] : null" 
                            :label="configuredDataFields[field.fieldName]['label']" 
                            :value="configuredEditData[field.fieldName]" 
                            @update:value="(value) => updateConfiguredEditData(field.fieldName, value)"
                        ></checkbox-input-element>
                    </template>
                    <template v-else-if="configuredDataFields[field.fieldName]['dataType'] === 'select'">
                        <selector-input-element 
                            :definition="configuredDataFields[field.fieldName]['definition'] ? configuredDataFields[field.fieldName]['definition'] : null" 
                            :options="configuredDataFields[field.fieldName]['options']" 
                            :label="configuredDataFields[field.fieldName]['label']" 
                            :value="configuredEditData[field.fieldName]" 
                            @update:value="(value) => updateConfiguredEditData(field.fieldName, value)"
                        ></selector-input-element>
                    </template>
                    <template v-else-if="configuredDataFields[field.fieldName]['dataType'] === 'int' || configuredDataFields[field.fieldName]['dataType'] === 'number' || configuredDataFields[field.fieldName]['dataType'] === 'string'">
                        <text-field-input-element 
                            :definition="configuredDataFields[field.fieldName]['definition'] ? configuredDataFields[field.fieldName]['definition'] : null" 
                            :data-type="configuredDataFields[field.fieldName]['dataType']" 
                            :label="configuredDataFields[field.fieldName]['label']" 
                            :max-value="configuredDataFields[field.fieldName]['maxValue'] ? configuredDataFields[field.fieldName]['maxValue'] : null" 
                            :min-value="configuredDataFields[field.fieldName]['minValue'] ? configuredDataFields[field.fieldName]['minValue'] : null" 
                            :value="configuredEditData[field.fieldName]" 
                            @update:value="(value) => updateConfiguredEditData(field.fieldName, value)"
                        ></text-field-input-element>
                    </template>
                </div>
            </template>
        </div>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup(props) {
        const configuredEditData = Vue.inject('configuredEditData');
        const configuredDataFields = Vue.inject('configuredDataFields');
        const dataFieldRefObject = Vue.ref({});

        const updateConfiguredEditData = Vue.inject('updateConfiguredEditData');

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

        Vue.onMounted(() => {
            setStyling();
        });

        return {
            configuredEditData,
            configuredDataFields,
            dataFieldRefObject,
            setElementRef,
            updateConfiguredEditData
        }
    }
};
