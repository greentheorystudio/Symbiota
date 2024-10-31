const searchAdvancedBlock = {
    template: `
        <div>
            <q-card flat bordered>
                <q-card-section>
                    <div class="full-width row justify-between">
                        <div class="text-body1 text-bold">Advanced Query Builder</div>
                        <div>
                            <q-btn color="primary" @click="addCriteriaObjToArr();" label="Add Criteria" :disabled="addCriteriaDisabled" />
                        </div>
                    </div>
                    <div v-if="!parenthesisValid" class="text-body1 text-bold text-red self-center">
                        There are an unequal amount of open parenthesis and closing parenthesis in your query. If this is left as-is 
                        parenthesis will be ignored.
                    </div>
                    <div class="q-pa-md column q-gutter-md">
                        <template v-for="criteria in criteriaArr">
                            <div>
                                <q-card>
                                    <q-card-section class="row q-gutter-xs">
                                        <div class="col-1 q-pt-xs" v-if="criteria['field'] && (criteria['operator'] === 'IS NULL' || criteria['operator'] === 'IS NOT NULL' || criteria['value'])">
                                            <q-btn color="negative" @click="deleteCriteria(criteria['index']);" text-color="white" icon="delete" dense>
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Delete this query line
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div class="col-1" v-if="criteria['index'] > 1">
                                            <selector-input-element :options="concatenatorOptions" :value="criteria['concatenator']" @update:value="(value) => updateCriteriaParameters(criteria['index'], 'concatenator', value)"></selector-input-element>
                                        </div>
                                        <div class="col-1">
                                            <selector-input-element :options="openParenthesisOptions" :value="criteria['openParens']" @update:value="(value) => updateCriteriaParameters(criteria['index'], 'openParens', value)" :clearable="true"></selector-input-element>
                                        </div>
                                        <div class="col-2">
                                            <selector-input-element label="Field" :options="fieldOptions" option-value="field" option-label="label" :value="criteria['field']" @update:value="(value) => updateCriteriaParameters(criteria['index'], 'field', value)"></selector-input-element>
                                        </div>
                                        <div class="col-2">
                                            <selector-input-element label="Operator" :options="operatorOptions" :value="criteria['operator']" @update:value="(value) => updateCriteriaParameters(criteria['index'], 'operator', value)"></selector-input-element>
                                        </div>
                                        <div class="col-2">
                                            <text-field-input-element label="Value" :value="criteria['value']" @update:value="(value) => updateCriteriaParameters(criteria['index'], 'value', value)"></text-field-input-element>
                                        </div>
                                        <div class="col-1">
                                            <selector-input-element :options="closeParenthesisOptions" :value="criteria['closeParens']" @update:value="(value) => updateCriteriaParameters(criteria['index'], 'closeParens', value)" :clearable="true"></selector-input-element>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </div>
                        </template>
                    </div>
                </q-card-section>
            </q-card>
        </div>
    `,
    components: {
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const searchStore = useSearchStore();

        const addCriteriaDisabled = Vue.computed(() => {
            return !(criteriaArr.length > 0 && criteriaArr.length < 8 && newestCriteriaValid.value);
        });
        const blankCriteriaObj = {
            index: null,
            concatenator: 'AND',
            openParens: null,
            field: null,
            operator: 'EQUALS',
            value: null,
            closeParens: null
        };
        const closeParenthesisOptions = [')', '))', ')))'];
        const concatenatorOptions = ['AND', 'OR'];
        const criteriaArr = Vue.reactive([]);
        const fieldOptions = Vue.computed(() => searchStore.getQueryBuilderFieldOptions);
        const newestCriteriaValid = Vue.computed(() => {
            return (criteriaArr[(criteriaArr.length - 1)]['field'] && (criteriaArr[(criteriaArr.length - 1)]['operator'] === 'IS NULL' || criteriaArr[(criteriaArr.length - 1)]['operator'] === 'IS NOT NULL' || criteriaArr[(criteriaArr.length - 1)]['value']));
        });
        const openParenthesisOptions = ['(', '((', '((('];
        const operatorOptions = ['EQUALS', 'NOT EQUALS', 'STARTS WITH', 'ENDS WITH', 'CONTAINS', 'DOES NOT CONTAIN', 'GREATER THAN', 'LESS THAN', 'IS NULL', 'IS NOT NULL'];
        const parenthesisValid = Vue.computed(() => {
            let openCnt = 0;
            let closedCnt = 0;
            if(criteriaArr.length > 0){
                criteriaArr.forEach((criteriaObj) => {
                    if(criteriaObj['openParens'] === '('){
                        openCnt = openCnt + 1;
                    }
                    else if(criteriaObj['openParens'] === '(('){
                        openCnt = openCnt + 2;
                    }
                    else if(criteriaObj['openParens'] === '((('){
                        openCnt = openCnt + 3;
                    }
                    if(criteriaObj['closeParens'] === ')'){
                        closedCnt = closedCnt + 1;
                    }
                    else if(criteriaObj['closeParens'] === '))'){
                        closedCnt = closedCnt + 2;
                    }
                    else if(criteriaObj['closeParens'] === ')))'){
                        closedCnt = closedCnt + 3;
                    }
                });
            }
            return (openCnt === closedCnt);
        });
        const searchTerms = Vue.computed(() => searchStore.getSearchTerms);

        function addCriteriaObjToArr() {
            criteriaArr.push(Object.assign({}, blankCriteriaObj));
            setCriteriaArrIndexVals();
        }

        function deleteCriteria(index) {
            criteriaArr.splice((index - 1), 1);
            if(criteriaArr.length === 0){
                criteriaArr.push(Object.assign({}, blankCriteriaObj));
            }
            setCriteriaArrIndexVals();
            updateSearchTerms();
        }

        function setCriteriaArrIndexVals() {
            criteriaArr.forEach((criteriaObj, index) => {
                criteriaObj['index'] = (index + 1);
            });
        }

        function updateCriteriaParameters(index, param, value) {
            criteriaArr[(index - 1)][param] = value;
            if(criteriaArr.length > 0 || newestCriteriaValid.value){
                updateSearchTerms();
            }
        }

        function updateSearchTerms() {
            const updateArr = [];
            criteriaArr.forEach((criteriaObj, index) => {
                if(criteriaObj['field'] && (criteriaObj['operator'] === 'IS NULL' || criteriaObj['operator'] === 'IS NOT NULL' || criteriaObj['value'])){
                    updateArr.push({
                        concatenator: (index > 0 ? criteriaObj['concatenator'] : null),
                        openParens: (parenthesisValid.value ? criteriaObj['openParens'] : null),
                        field: criteriaObj['field'],
                        operator: criteriaObj['operator'],
                        value: criteriaObj['value'],
                        closeParens: (parenthesisValid.value ? criteriaObj['closeParens'] : null)
                    });
                }
            });
            searchStore.updateSearchTerms('advanced', updateArr);
        }

        Vue.onMounted(() => {
            searchTerms.value['advanced'].forEach((criteriaObj) => {
                criteriaArr.push(criteriaObj);
            });
            if(criteriaArr.length === 0){
                criteriaArr.push(Object.assign({}, blankCriteriaObj));
            }
            setCriteriaArrIndexVals();
        });

        return {
            addCriteriaDisabled,
            closeParenthesisOptions,
            concatenatorOptions,
            criteriaArr,
            fieldOptions,
            openParenthesisOptions,
            operatorOptions,
            parenthesisValid,
            searchTerms,
            addCriteriaObjToArr,
            deleteCriteria,
            updateCriteriaParameters
        }
    }
};
