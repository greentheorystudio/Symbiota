const taxonomyConfigurationsTab = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="text-grey-8 text-h6 text-weight-bolder">
                    Default Taxonomic Ranks
                </div>
                <template v-for="rank in taxonRankOptionsArr">
                    <div class="q-pl-lg text-body1 text-bold no-wrap">
                        <checkbox-input-element :label="rank['name']" :value="defaultTaxonRankArr.includes(rank['id'])" @update:value="(value) => processTaxonomyRankCheckboxChange(rank['id'], value)" :disabled="rank['id'] === 10"></checkbox-input-element>
                    </div>
                </template>
            </q-card-section>
        </q-card>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement
    },
    setup() {
        const { capitalizeFirstLetter, showNotification } = useCore();
        const configurationStore = useConfigurationStore();

        const coreData = Vue.computed(() => configurationStore.getCoreConfigurationData);
        const defaultTaxonRankArr = Vue.computed(() => {
            return coreData.value.hasOwnProperty('TAXONOMIC_RANKS') ? JSON.parse(coreData.value['TAXONOMIC_RANKS']) : [10];
        });
        const taxonRankData = Vue.ref({});
        const taxonRankNameArr = Vue.computed(() => {
            return Object.keys(taxonRankData.value).length > 0 ? Object.keys(taxonRankData.value) : [];
        });
        const taxonRankOptionsArr = Vue.computed(() => {
            const returnArr = [];
            taxonRankNameArr.value.forEach((rankName) => {
                const rankId = taxonRankData.value[rankName];
                const optionObj = returnArr.find(rank => rank['id'] === rankId);
                if(optionObj){
                    optionObj['name'] = optionObj['name'] + ', ' + capitalizeFirstLetter(rankName.toString());
                }
                else{
                    returnArr.push({
                        name: capitalizeFirstLetter(rankName.toString()),
                        id: rankId
                    });
                }
            });
            return returnArr;
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
            const newRankArr = defaultTaxonRankArr.value.slice();
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
            defaultTaxonRankArr,
            taxonRankOptionsArr,
            capitalizeFirstLetter,
            processTaxonomyRankCheckboxChange
        }
    }
};
