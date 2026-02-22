const collectionRecordDistributionDisplay = {
    props: {
        collectionId: {
            type: Number,
            default: null
        },
        displayType: {
            type: String,
            default: null
        }
    },
    template: `
        <template v-if="displayType">
            <q-card flat bordered>
                <q-card-section>
                    <div class="text-h6 text-bold">{{ displayLabel }}</div>
                    <div class="q-mt-xs q-pl-sm column">
                        <template v-for="key in distributionDataKeys">
                            <div>
                                <template v-if="selectedState || displayType === 'taxonomic'">
                                    {{ key }}
                                </template>
                                <template v-else-if="selectedCountry">
                                    <a role="button" @click="setSelectedState(key);" @keyup.enter="setSelectedState(key);" class="cursor-pointer" aria-label="See distribution" tabindex="0">
                                        {{ key }}
                                    </a>
                                </template>
                                <template v-else>
                                    <a role="button" @click="setSelectedCountry(key);" @keyup.enter="setSelectedCountry(key);" class="cursor-pointer" aria-label="See distribution" tabindex="0">
                                        {{ key }}
                                    </a>
                                </template>
                                <span>
                                    (<a :href="getSearchUrl(key)" target="_blank" aria-label="See occurrence records - Opens in separate tab" tabindex="0">
                                        {{ distributionData[key] }}
                                    </a>)
                                </span>
                            </div>
                        </template>
                    </div>
                </q-card-section>
            </q-card>
        </template>
    `,
    setup(props) {
        const baseStore = useBaseStore();

        const clientRoot = baseStore.getClientRoot;
        const displayLabel = Vue.computed(() => {
            let label = '';
            if(props.displayType === 'geographic'){
                label = 'Geographic Distribution';
                if(selectedState.value){
                    label += (' - ' + selectedState.value);
                }
                else if(selectedCountry.value){
                    label += (' - ' + selectedCountry.value);
                }
            }
            else if(props.displayType === 'taxonomic'){
                label = 'Family Distribution';
            }
            return label;
        });
        const distributionData = Vue.ref({});
        const distributionDataKeys = Vue.computed(() => {
            return Object.keys(distributionData.value);
        });
        const propsRefs = Vue.toRefs(props);
        const selectedCountry = Vue.ref(null);
        const selectedState = Vue.ref(null);

        Vue.watch(propsRefs.displayType, () => {
            setDistributionData();
        });

        function getSearchUrl(key) {
            let url = clientRoot + '/collections/list.php?starr={"db":"' + props.collectionId + '",';
            if(props.displayType === 'geographic'){
                if(selectedState.value){
                    url += '"country":"' + selectedCountry.value + '","state":"' + selectedState.value + '","county":"' + key + '"}';
                }
                else if(selectedCountry.value){
                    url += '"country":"' + selectedCountry.value + '","state":"' + key + '"}';
                }
                else{
                    url += '"country":"' + key + '"}';
                }
            }
            else if(props.displayType === 'taxonomic'){
                url += '"usethes":true,"taxa":"' + key + '"}';
            }
            return url;
        }

        function setDistributionData() {
            const formData = new FormData();
            formData.append('collid', props.collectionId.toString());
            if(props.displayType === 'geographic'){
                formData.append('country', (selectedCountry.value ? selectedCountry.value : ''));
                formData.append('state', (selectedState.value ? selectedState.value : ''));
                formData.append('action', 'getGeographicDistributionData');
            }
            else if(props.displayType === 'taxonomic'){
                formData.append('action', 'getTaxonomicDistributionData');
            }
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((resData) => {
                    distributionData.value = Object.assign({}, resData);
                });
            });
        }

        function setSelectedCountry(value) {
            selectedCountry.value = value;
            setDistributionData();
        }

        function setSelectedState(value) {
            selectedState.value = value;
            setDistributionData();
        }

        Vue.onMounted(() => {
            setDistributionData();
        });

        return {
            clientRoot,
            displayLabel,
            distributionData,
            distributionDataKeys,
            selectedCountry,
            selectedState,
            getSearchUrl,
            setSelectedCountry,
            setSelectedState
        }
    }
};
