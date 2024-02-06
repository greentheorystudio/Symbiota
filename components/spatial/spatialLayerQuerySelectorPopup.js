const spatialLayerQuerySelectorPopup = {
    props: {
        layerId: {
            type: Number,
            default: null
        }
    },
    template: `
        <q-dialog class="z-top" v-model="mapSettings.showLayerQuerySelector" persistent>
            <q-card class="sm-map-popup">
                <div class="row justify-end items-start map-popup-header">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="updateMapSettings('showLayerQuerySelector', false);"></q-btn>
                    </div>
                </div>
                <div class="q-mt-sm q-pa-md column q-gutter-sm">
                    <template v-if="fieldArr.length > 0">
                        <div class="row">
                            <q-select bg-color="white" class="col-6" outlined v-model="selectedField" :options="fieldOptions" option-value="value" option-label="label" popup-content-class="z-max" behavior="menu" dense options-dense />
                        </div>
                        <div class="row">
                            <q-select bg-color="white" class="col-6" outlined v-model="selectedOperator" :options="operatorSelectorOptions" option-value="value" option-label="label" popup-content-class="z-max" behavior="menu" dense options-dense />
                        </div>
                        <template v-if="selectedOperator.value === 'between'">
                            <div class="row justify-around items-center q-gutter-md q-mt-xs">
                                <div>
                                    <q-input type="number" outlined v-model="dualValueLow" bg-color="white" label="Value" class="col-3" dense></q-input>
                                </div>
                                <div>
                                    AND
                                </div>
                                <div>
                                    <q-input type="number" outlined v-model="dualValueHigh" bg-color="white" label="Value" class="col-3" dense></q-input>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <template v-if="selectedOperator.value === 'greaterThan' || selectedOperator.value === 'lessThan'">
                                <div>
                                    <q-input type="number" outlined v-model="singleValue" bg-color="white" label="Value" class="col-6" dense></q-input>
                                </div>
                            </template>
                            <template v-else>
                                <div>
                                    <q-input outlined v-model="singleValue" bg-color="white" label="Value" class="col-6" dense></q-input>
                                </div>
                            </template>
                        </template>
                        <div class="row col-5 justify-end">
                            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="processQuerySelectorQuery();" label="Run Query" />
                        </div>
                    </template>
                    <template v-else>
                        <div class="row text-h6 text-bold">
                            Layer does not contain data
                        </div>
                    </template>
                </div>
            </q-card>
        </q-dialog>
    `,
    setup(props) {
        const blankFieldSelectorOptions = [
            {value: '', label: 'Select attribute'}
        ];
        const dualValueHigh = Vue.ref(null);
        const dualValueLow = Vue.ref(null);
        const fieldArr = Vue.ref([]);
        const fieldOptions = Vue.ref([]);
        const layersObj = Vue.inject('layersObj');
        const mapSettings = Vue.inject('mapSettings');
        const operatorSelectorOptions = [
            {value: 'equals', label: 'EQUALS'},
            {value: 'contains', label: 'CONTAINS'},
            {value: 'greaterThan', label: 'GREATER THAN'},
            {value: 'lessThan', label: 'LESS THAN'},
            {value: 'between', label: 'BETWEEN'}
        ];
        const propsRefs = Vue.toRefs(props);
        const selectedField = Vue.ref(null);
        const selectedOperator = Vue.ref(null);
        const singleValue = Vue.ref(null);

        const updateMapSettings = Vue.inject('updateMapSettings');
        const { hideWorking, showNotification, showWorking } = useCore();

        Vue.watch(propsRefs.layerId, () => {
            primeLayerQuerySelectorFields();
        });

        function primeLayerQuerySelectorFields() {
            fieldArr.value = [];
            dualValueHigh.value = null;
            dualValueLow.value = null;
            fieldOptions.value = blankFieldSelectorOptions.slice();
            selectedField.value = fieldOptions.value[0];
            selectedOperator.value = operatorSelectorOptions[0];
            singleValue.value = null;
            if(props.layerId){
                const layerFeatures = layersObj[props.layerId].getSource().getFeatures();
                layerFeatures.forEach((feature) => {
                    const properties = feature.getKeys();
                    properties.forEach((prop) => {
                        if(!fieldArr.value.includes(String(prop)) && String(prop) !== 'geometry' && String(prop) !== 'OBJECTID'){
                            fieldArr.value.push(String(prop));
                        }
                    });
                });
                if(fieldArr.value.length > 0){
                    fieldArr.value.sort(function (a, b) {
                        return a.toLowerCase().localeCompare(b.toLowerCase());
                    });
                    fieldArr.value.forEach((field) => {
                        fieldOptions.value.push({value: field, label: field});
                    });
                }
            }
        }

        function processQuerySelectorQuery() {
            if(selectedField.value.value === ''){
                showNotification('negative','Please select a field on which to run the query.');
            }
            else if(selectedOperator.value.value !== 'between' && (!singleValue.value || singleValue.value.toString() === '')){
                showNotification('negative','Please enter a value with which to run the query.');
            }
            else if(selectedOperator.value.value === 'between' && (!dualValueLow.value || !dualValueHigh.value)){
                showNotification('negative','Two numeric values must be entered for a between query.');
            }
            else {
                runQuerySelectorQuery();
            }
        }

        function runQuerySelectorQuery() {
            showWorking();
            const addFeatures = [];
            const layerFeatures = layersObj[props.layerId].getSource().getFeatures();
            layerFeatures.forEach((feature) => {
                if(feature.get(selectedField.value.value)){
                    let add = false;
                    const featureValue = feature.get(selectedField.value.value);
                    if(selectedOperator.value.value === 'equals' && featureValue.toString().toLowerCase() === singleValue.value.toString().toLowerCase()){
                        add = true;
                    }
                    else if(selectedOperator.value.value === 'contains' && featureValue.toString().toLowerCase().includes(singleValue.value.toString().toLowerCase())){
                        add = true;
                    }
                    else if(selectedOperator.value.value === 'greaterThan' && !isNaN(featureValue) && Number(featureValue) > Number(singleValue.value)){
                        add = true;
                    }
                    else if(selectedOperator.value.value === 'lessThan' && !isNaN(featureValue) && Number(featureValue) < Number(singleValue.value)){
                        add = true;
                    }
                    else if(selectedOperator.value.value === 'between' && !isNaN(featureValue) && Number(featureValue) >= Number(dualValueLow.value) && Number(featureValue) <= Number(dualValueHigh.value)){
                        add = true;
                    }
                    if(add){
                        const featureClone = feature.clone();
                        addFeatures.push(featureClone);
                    }
                }
            });
            mapSettings.selectSource.addFeatures(addFeatures);
            mapSettings.showLayerQuerySelector = false;
            hideWorking();
        }
        
        return {
            dualValueHigh,
            dualValueLow,
            fieldArr,
            fieldOptions,
            mapSettings,
            operatorSelectorOptions,
            selectedField,
            selectedOperator,
            singleValue,
            processQuerySelectorQuery,
            updateMapSettings
        }
    }
};
