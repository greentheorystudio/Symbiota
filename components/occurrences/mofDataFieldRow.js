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
            <div v-if="fields.length > 0" class="row justify-start q-col-gutter-sm">
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
                        <template v-else-if="configuredDataFields[field.fieldName]['dataType'] === 'date'">
                            <date-input-element 
                                :definition="configuredDataFields[field.fieldName]['definition'] ? configuredDataFields[field.fieldName]['definition'] : null" 
                                :label="configuredDataFields[field.fieldName]['label']" 
                                :value="configuredData[field.fieldName]" 
                                @update:value="(value) => updateConfiguredEditData(field.fieldName, (value ? value.date : null))"
                            ></date-input-element>
                        </template>
                        <template v-else-if="configuredDataFields[field.fieldName]['dataType'] === 'int' || configuredDataFields[field.fieldName]['dataType'] === 'number' || configuredDataFields[field.fieldName]['dataType'] === 'increment' || configuredDataFields[field.fieldName]['dataType'] === 'string' || configuredDataFields[field.fieldName]['dataType'] === 'textarea'">
                            <text-field-input-element 
                                :definition="configuredDataFields[field.fieldName]['definition'] ? configuredDataFields[field.fieldName]['definition'] : null" 
                                :data-type="configuredDataFields[field.fieldName]['dataType']" 
                                :label="configuredDataFields[field.fieldName]['label']" 
                                :field="field.fieldName"
                                :field-hint="configuredDataFields[field.fieldName]['fieldHint'] ? configuredDataFields[field.fieldName]['fieldHint'] : null"
                                :max-value="configuredDataFields[field.fieldName]['maxValue'] ? configuredDataFields[field.fieldName]['maxValue'] : null" 
                                :min-value="configuredDataFields[field.fieldName]['minValue'] ? configuredDataFields[field.fieldName]['minValue'] : null"
                                :round-value="configuredDataFields[field.fieldName]['roundValue'] ? configuredDataFields[field.fieldName]['roundValue'] : null" 
                                :max-length="configuredDataFields[field.fieldName]['maxlength'] ? configuredDataFields[field.fieldName]['maxlength'] : null"
                                :show-counter="configuredDataFields[field.fieldName]['showCounter'] ? configuredDataFields[field.fieldName]['showCounter'] : false"
                                :step="configuredDataFields[field.fieldName]['step'] ? configuredDataFields[field.fieldName]['step'] : 1"
                                :value="configuredData[field.fieldName]" 
                                @update:value="(value) => updateConfiguredEditData(field.fieldName, value)"
                            ></text-field-input-element>
                        </template>
                        <template v-else-if="configuredDataFields[field.fieldName]['dataType'] === 'single-taxon-auto-complete'">
                            <single-scientific-common-name-auto-complete 
                                :accepted-taxa-only="configuredDataFields[field.fieldName]['acceptedTaxaOnly'] ? configuredDataFields[field.fieldName]['acceptedTaxaOnly'] : false"
                                :definition="configuredDataFields[field.fieldName]['definition'] ? configuredDataFields[field.fieldName]['definition'] : null" 
                                :hide-author="configuredDataFields[field.fieldName]['hideAuthor'] ? configuredDataFields[field.fieldName]['hideAuthor'] : true"
                                :hide-protected="configuredDataFields[field.fieldName]['hideProtected'] ? configuredDataFields[field.fieldName]['hideProtected'] : false"
                                :identifier-name="configuredDataFields[field.fieldName]['identifierName'] ? configuredDataFields[field.fieldName]['identifierName'] : null"
                                :identifier-value="configuredDataFields[field.fieldName]['identifierValue'] ? configuredDataFields[field.fieldName]['identifierValue'] : null"
                                :kingdom-id="configuredDataFields[field.fieldName]['kingdomId'] ? configuredDataFields[field.fieldName]['kingdomId'] : 0" 
                                :label="configuredDataFields[field.fieldName]['label']" 
                                :limit-to-options="configuredDataFields[field.fieldName]['limitToOptions'] ? configuredDataFields[field.fieldName]['limitToOptions'] : false"
                                :option-limit="configuredDataFields[field.fieldName]['optionLimit'] ? configuredDataFields[field.fieldName]['optionLimit'] : 10" 
                                :options="configuredDataFields[field.fieldName]['options'] ? configuredDataFields[field.fieldName]['options'] : null"
                                :parent-tid="configuredDataFields[field.fieldName]['parentTid'] ? configuredDataFields[field.fieldName]['parentTid'] : null"
                                :rank-high="configuredDataFields[field.fieldName]['rankHigh'] ? configuredDataFields[field.fieldName]['rankHigh'] : null"
                                :rank-limit="configuredDataFields[field.fieldName]['rankLimit'] ? configuredDataFields[field.fieldName]['rankLimit'] : null"
                                :rank-low="configuredDataFields[field.fieldName]['rankLow'] ? configuredDataFields[field.fieldName]['rankLow'] : null" 
                                :sciname="configuredData[field.fieldName]"
                                :taxon-type="configuredDataFields[field.fieldName]['taxonType'] ? configuredDataFields[field.fieldName]['taxonType'] : null" 
                                @update:value="(value) => updateConfiguredEditData(field.fieldName, (value ? value['sciname'] : null))"
                            ></single-scientific-common-name-auto-complete>
                        </template>
                        <template v-else-if="configuredDataFields[field.fieldName]['dataType'] === 'multi-taxon-auto-complete'">
                            <multiple-scientific-common-name-auto-complete 
                                :accepted-taxa-only="configuredDataFields[field.fieldName]['acceptedTaxaOnly'] ? configuredDataFields[field.fieldName]['acceptedTaxaOnly'] : false"
                                :concatenator="configuredDataFields[field.fieldName]['concatenator'] ? configuredDataFields[field.fieldName]['concatenator'] : ';'"
                                :definition="configuredDataFields[field.fieldName]['definition'] ? configuredDataFields[field.fieldName]['definition'] : null" 
                                :hide-author="configuredDataFields[field.fieldName]['hideAuthor'] ? configuredDataFields[field.fieldName]['hideAuthor'] : true"
                                :hide-protected="configuredDataFields[field.fieldName]['hideProtected'] ? configuredDataFields[field.fieldName]['hideProtected'] : false"
                                :identifier-name="configuredDataFields[field.fieldName]['identifierName'] ? configuredDataFields[field.fieldName]['identifierName'] : null"
                                :identifier-value="configuredDataFields[field.fieldName]['identifierValue'] ? configuredDataFields[field.fieldName]['identifierValue'] : null"
                                :kingdom-id="configuredDataFields[field.fieldName]['kingdomId'] ? configuredDataFields[field.fieldName]['kingdomId'] : 0" 
                                :label="configuredDataFields[field.fieldName]['label']" 
                                :limit-to-options="configuredDataFields[field.fieldName]['limitToOptions'] ? configuredDataFields[field.fieldName]['limitToOptions'] : false"
                                :option-limit="configuredDataFields[field.fieldName]['optionLimit'] ? configuredDataFields[field.fieldName]['optionLimit'] : 10" 
                                :options="configuredDataFields[field.fieldName]['options'] ? configuredDataFields[field.fieldName]['options'] : null"
                                :parent-tid="configuredDataFields[field.fieldName]['parentTid'] ? configuredDataFields[field.fieldName]['parentTid'] : null"
                                :rank-high="configuredDataFields[field.fieldName]['rankHigh'] ? configuredDataFields[field.fieldName]['rankHigh'] : null"
                                :rank-limit="configuredDataFields[field.fieldName]['rankLimit'] ? configuredDataFields[field.fieldName]['rankLimit'] : null"
                                :rank-low="configuredDataFields[field.fieldName]['rankLow'] ? configuredDataFields[field.fieldName]['rankLow'] : null" 
                                :sciname="configuredData[field.fieldName]"
                                :taxon-type="configuredDataFields[field.fieldName]['taxonType'] ? configuredDataFields[field.fieldName]['taxonType'] : null" 
                                @update:value="(value) => updateConfiguredEditData(field.fieldName, value)"
                            ></multiple-scientific-common-name-auto-complete>
                        </template>
                        <template v-else-if="configuredDataFields[field.fieldName]['dataType'] === 'calculated' || configuredDataFields[field.fieldName]['dataType'] === 'taxon-identifier'">
                            <computed-value-input-element 
                                :label="configuredDataFields[field.fieldName]['label']" 
                                :definition="configuredDataFields[field.fieldName]['definition'] ? configuredDataFields[field.fieldName]['definition'] : null" 
                                :max-value="configuredDataFields[field.fieldName]['maxValue'] ? configuredDataFields[field.fieldName]['maxValue'] : null" 
                                :min-value="configuredDataFields[field.fieldName]['minValue'] ? configuredDataFields[field.fieldName]['minValue'] : null"
                                :value="configuredData[field.fieldName]" 
                            ></computed-value-input-element>
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
        'computed-value-input-element': computedValueInputElement,
        'date-input-element': dateInputElement,
        'multiple-scientific-common-name-auto-complete': multipleScientificCommonNameAutoComplete,
        'selector-input-element': selectorInputElement,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
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
