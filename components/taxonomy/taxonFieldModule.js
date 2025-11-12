const taxonFieldModule = {
    props: {
        data: {
            type: Object,
            default: null
        }
    },
    template: `
        <div class="row">
            <div class="col-grow">
                <text-field-input-element label="Taxon Name" :value="data.sciname" @update:value="processTaxonNameChange"></text-field-input-element>
            </div>
        </div>
        <div class="row">
            <div class="col-grow">
                <text-field-input-element label="Author" :value="data.author" @update:value="(value) => updateData('author', value)"></text-field-input-element>
            </div>
        </div>
        <div class="row">
            <div class="col-grow">
                <taxon-rank-selector label="Taxon Rank" :value="data.rankid" @update:value="processTaxonRankChange"></taxon-rank-selector>
            </div>
        </div>
        <div v-if="Number(data.tid) === 0" class="row">
            <div class="col-grow">
                <single-scientific-common-name-auto-complete :sciname="parentTaxonVal" label="Parent Taxon" :hide-author="false" :accepted-taxa-only="true" :limit-to-options="true" @update:sciname="processParentTaxonChange"></single-scientific-common-name-auto-complete>
            </div>
        </div>
        <div class="row q-col-gutter-sm no-wrap">
            <div class="col-2">
                <text-field-input-element :value="data.unitind1" @update:value="(value) => processUnitNameChange('unitind1', value)"></text-field-input-element>
            </div>
            <div class="col-10">
                <text-field-input-element label="Unit Name 1" :value="data.unitname1" @update:value="(value) => processUnitNameChange('unitname1', value)"></text-field-input-element>
            </div>
        </div>
        <div class="row q-col-gutter-sm no-wrap">
            <div class="col-2">
                <text-field-input-element :value="data.unitind2" @update:value="(value) => processUnitNameChange('unitind2', value)"></text-field-input-element>
            </div>
            <div class="col-10">
                <text-field-input-element label="Unit Name 2" :value="data.unitname2" @update:value="(value) => processUnitNameChange('unitname2', value)"></text-field-input-element>
            </div>
        </div>
        <div class="row q-col-gutter-sm no-wrap">
            <div class="col-3">
                <text-field-input-element :value="data.unitind3" @update:value="(value) => processUnitNameChange('unitind3', value)"></text-field-input-element>
            </div>
            <div class="col-9">
                <text-field-input-element label="Unit Name 3" :value="data.unitname3" @update:value="(value) => processUnitNameChange('unitname3', value)"></text-field-input-element>
            </div>
        </div>
        <div class="row">
            <div class="col-grow">
                <text-field-input-element label="Notes" :value="data.notes" @update:value="(value) => updateData('notes', value)"></text-field-input-element>
            </div>
        </div>
        <div class="row">
            <div class="col-grow">
                <text-field-input-element label="Source" :value="data.source" @update:value="(value) => updateData('source', value)"></text-field-input-element>
            </div>
        </div>
        <div v-if="Number(data.tid) === 0" class="row">
            <div class="col-grow">
                <checkbox-input-element label="Protect Taxon Locations" :value="data.securitystatus" @update:value="(value) => updateData('securitystatus', (Number(value) === 1 ? 1 : null))"></checkbox-input-element>
            </div>
        </div>
        <q-card v-if="Number(data.tid) === 0" flat bordered class="q-mx-md q-mt-sm">
            <q-card-section class="q-pt-xs q-px-sm q-pb-sm column">
                <div class="text-subtitle1 text-bold">Acceptance Status</div>
                <div class="q-mt-xs q-pl-sm column">
                    <div class="row">
                        <div class="col-grow">
                            <q-option-group v-model="selectedAcceptanceOption" :options="acceptanceOptions" color="primary" dense inline aria-label="Acceptance options" tabindex="0"></q-option-group>
                        </div>
                    </div>
                    <div v-if="selectedAcceptanceOption === 'notaccepted'" class="q-mt-sm row">
                        <div class="col-6">
                            <single-scientific-common-name-auto-complete :sciname="acceptedTaxonVal" label="Accepted Taxon" :hide-author="false" :accepted-taxa-only="true" :limit-to-options="true" @update:sciname="processAcceptedTaxonChange"></single-scientific-common-name-auto-complete>
                        </div>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'selector-input-element': selectorInputElement,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
        'taxon-rank-selector': taxonRankSelector,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { parseScientificName } = useCore();

        const acceptanceOptions = Vue.ref([
            {value: 'accepted', label: 'Accepted'},
            {value: 'notaccepted', label: 'Not Accepted'}
        ]);
        const acceptedTaxonVal = Vue.ref(null);
        const parentTaxonVal = Vue.ref(null);
        const selectedAcceptanceOption = Vue.ref('accepted');

        function processAcceptedTaxonChange(taxonData) {
            if(taxonData && Number(taxonData['tid']) > 0) {
                updateData('tidaccepted', taxonData['tid']);
                acceptedTaxonVal.value = taxonData['sciname'];
            }
            else{
                updateData('tidaccepted', null);
                acceptedTaxonVal.value = null;
            }
        }

        function processParentTaxonChange(taxonData) {
            if(taxonData && Number(taxonData['tid']) > 0) {
                updateData('kingdomid', taxonData['kingdomid']);
                updateData('parenttid', taxonData['tid']);
                parentTaxonVal.value = taxonData['sciname'];
            }
            else{
                updateData('kingdomid', null);
                updateData('parenttid', null);
                parentTaxonVal.value = null;
            }
        }

        function processTaxonNameChange(value) {
            if(value){
                const scinameData = parseScientificName(value);
                updateData('sciname', scinameData['sciname']);
                updateData('unitind1', scinameData['unitind1']);
                updateData('unitname1', scinameData['unitname1']);
                updateData('unitind2', scinameData['unitind2']);
                updateData('unitname2', scinameData['unitname2']);
                updateData('unitind3', scinameData['unitind3']);
                updateData('unitname3', scinameData['unitname3']);
                if(Number(props.data.tid) === 0){
                    updateData('rankid', scinameData['rankid']);
                    if((!parentTaxonVal.value && scinameData['parentname']) || (parentTaxonVal.value && scinameData['parentname'] && parentTaxonVal.value !== scinameData['parentname'])){
                        validateParentName(scinameData['parentname']);
                    }
                }
            }
            else{
                updateData('sciname', null);
                updateData('unitind1', null);
                updateData('unitname1', null);
                updateData('unitind2', null);
                updateData('unitname2', null);
                updateData('unitind3', null);
                updateData('unitname3', null);
                if(Number(props.data.tid) === 0){
                    updateData('rankid', null);
                }
            }
        }

        function processTaxonRankChange(rankObj) {
            updateData('rankid', (rankObj ? rankObj['rankid'] : null));
        }

        function processUnitNameChange(key, value) {
            updateData(key, value);
            setScinameFromUnitNames();
            if(Number(props.data.tid) === 0 && (key === 'unitname1' || key === 'unitname2')) {
                if(key === 'unitname1' && Number(props.data.rankid) > 180){
                    validateParentName(value);
                }
                else if(key === 'unitname2' && Number(props.data.rankid) > 220){
                    validateParentName((props.data.unitname1 + ' ' + value));
                }
            }
        }

        function setScinameFromUnitNames() {
            const unitArr = [];
            if(props.data.unitind1){
                unitArr.push(props.data.unitind1);
            }
            if(props.data.unitname1){
                unitArr.push(props.data.unitname1);
            }
            if(props.data.unitind2){
                unitArr.push(props.data.unitind2);
            }
            if(props.data.unitname2){
                unitArr.push(props.data.unitname2);
            }
            if(props.data.unitind3){
                unitArr.push(props.data.unitind3);
            }
            if(props.data.unitname3){
                unitArr.push(props.data.unitname3);
            }
            updateData('sciname', unitArr.join(' '));
        }

        function updateData(key, value) {
            context.emit('update:taxon-data', {key: key, value: value});
        }

        function validateParentName(parentname) {
            const formData = new FormData();
            formData.append('action', 'getTaxonFromSciname');
            formData.append('sciname', parentname);
            fetch(taxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('tid') && Number(data['tid']) > 0){
                    parentTaxonVal.value = data['sciname'];
                    updateData('kingdomid', data['kingdomid']);
                    updateData('parenttid', data['tid']);
                }
                else{
                    parentTaxonVal.value = null;
                    updateData('kingdomid', null);
                    updateData('parenttid', null);
                }
            });
        }

        return {
            acceptanceOptions,
            acceptedTaxonVal,
            parentTaxonVal,
            selectedAcceptanceOption,
            processAcceptedTaxonChange,
            processParentTaxonChange,
            processTaxonNameChange,
            processTaxonRankChange,
            processUnitNameChange,
            updateData
        }
    }
};
