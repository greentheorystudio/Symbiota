const taxonomyConfigurationsTab = {
    template: `
        <div class="q-pa-sm column q-col-gutter-sm">
            <div class="text-grey-8 text-h6 text-weight-bolder">
                Recognized Taxonomic Ranks
            </div>
            <template v-for="rankName in taxonRankNameArr">
                <div class="q-pl-lg text-body1 text-bold no-wrap">
                    <checkbox-input-element :label="capitalizeFirstLetter(rankName.toString())" :value="recognizedTaxonRankArr.includes(taxonRankData[rankName])" @update:value="(value) => processTaxonomyRankCheckboxChange(taxonRankData[rankName], value)"></checkbox-input-element>
                </div>
            </template>
        </div>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement
    },
    setup() {
        const { capitalizeFirstLetter, showNotification } = useCore();
        const configurationStore = useConfigurationStore();

        const coreData = Vue.computed(() => configurationStore.getCoreConfigurationData);
        const recognizedTaxonRankArr = Vue.computed(() => {
            return coreData.value.hasOwnProperty('TAXONOMIC_RANKS') ? JSON.parse(coreData.value['TAXONOMIC_RANKS']) : [];
        });
        const taxonRankData = Vue.ref({});
        const taxonRankNameArr = Vue.computed(() => {
            return Object.keys(taxonRankData.value).length > 0 ? Object.keys(taxonRankData.value) : [];
        });

        function processCallbackResponse(res){
            if(res === 1){
                showNotification('positive','Saved and activated');
            }
            else{
                showNotification('negative', 'There was an error saving and activating the change');
            }
        }

        function processTaxonomyRankCheckboxChange(rankid, value){
            const newRankArr = recognizedTaxonRankArr.value.slice();
            if(value){
                newRankArr.push(rankid);
            }
            else{
                const index = newRankArr.indexOf(rankid);
                newRankArr.splice(index, 1);
            }
            if(coreData.value.hasOwnProperty('TAXONOMIC_RANKS')){
                configurationStore.updateConfigurationValue('TAXONOMIC_RANKS', JSON.stringify(newRankArr), (res) => {
                    processCallbackResponse(res);
                });
            }
            else{
                configurationStore.addConfigurationValue('TAXONOMIC_RANKS', JSON.stringify(newRankArr), (res) => {
                    processCallbackResponse(res);
                });
            }
        }

        function setTaxonRanks() {
            const formData = new FormData();
            formData.append('action', 'getRankNameArr');
            fetch(taxonRankApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => response.json())
            .then((result) => {
                taxonRankData.value = Object.assign({}, result);
            });
        }

        Vue.onMounted(() => {
            setTaxonRanks();
        });

        return {
            recognizedTaxonRankArr,
            taxonRankData,
            taxonRankNameArr,
            capitalizeFirstLetter,
            processTaxonomyRankCheckboxChange
        }
    }
};
