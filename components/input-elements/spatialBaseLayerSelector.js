const spatialBaseLayerSelector = {
    props: {
        tabindex: {
            type: Number,
            default: 0
        }
    },
    template: `
        <q-select bg-color="white" outlined v-model="selectedOption" :options="baseLayerSelectorOptions" option-value="value" option-label="label" label="Base Map" popup-content-class="z-top" @update:model-value="changeBaseLayer" behavior="menu" :tabindex="tabindex" dense options-dense />
    `,
    setup(_, context) {
        const baseStore = useBaseStore();

        const stadiaMapLayersActive = baseStore.getStadiaMapLayersActive;

        const baseLayerSelectorOptions = Vue.computed(() => {
            const returnArr = [
                {value: 'googleterrain', label: 'Google Maps Terrain'},
                {value: 'googleroadmap', label: 'Google Maps Terrain-Roadmap'},
                {value: 'googlealteredroadmap', label: 'Google Maps Roadmap'},
                {value: 'googlehybrid', label: 'Google Maps Satellite-Roadmap'},
                {value: 'googlesatellite', label: 'Google Maps Satellite'},
                {value: 'worldtopo', label: 'ESRI World Topo'},
                {value: 'worldimagery', label: 'ESRI World Imagery'},
                {value: 'esristreet', label: 'ESRI StreetMap'},
                {value: 'ngstopo', label: 'National Geographic Topo'},
                {value: 'natgeoworld', label: 'National Geographic World'},
                {value: 'openstreet', label: 'OpenStreetMap'},
                {value: 'opentopo', label: 'OpenTopo'}
            ];
            if(stadiaMapLayersActive){
                returnArr.push({value: 'stamenalidadebright', label: 'Alidade Bright'});
                returnArr.push({value: 'stamenalidadesatellite', label: 'Alidade Satellite'});
                returnArr.push({value: 'stamenalidadesmooth', label: 'Alidade Smooth'});
                returnArr.push({value: 'stamenalidadesmoothdark', label: 'Alidade Smooth Dark'});
                returnArr.push({value: 'stamenoutdoors', label: 'Outdoors'});
                returnArr.push({value: 'stamenterrain', label: 'Stamen Terrain'});
                returnArr.push({value: 'stamentoner', label: 'Stamen Toner'});
                returnArr.push({value: 'stamentonerblacklite', label: 'Stamen Toner Blacklite'});
                returnArr.push({value: 'stamentonerdark', label: 'Stamen Toner Dark'});
                returnArr.push({value: 'stamentonerlite', label: 'Stamen Toner Lite'});
                returnArr.push({value: 'stamenwatercolor', label: 'Stamen Watercolor'});
            }
            return returnArr;
        });
        const mapSettings = Vue.inject('mapSettings');
        const selectedOption = Vue.ref({});

        function changeBaseLayer(val) {
            selectedOption.value = val;
            context.emit('change-base-layer', val.value);
        }

        function setSelectedOption() {
            selectedOption.value = baseLayerSelectorOptions.value.find(opt => opt['value'] === mapSettings.selectedBaseLayer);
        }

        Vue.onMounted(() => {
            setSelectedOption();
        });

        return {
            baseLayerSelectorOptions,
            selectedOption,
            changeBaseLayer
        }
    }
};
