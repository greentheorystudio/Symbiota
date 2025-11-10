const spatialBaseLayerSelector = {
    props: {
        tabindex: {
            type: Number,
            default: 1
        }
    },
    template: `
        <q-select bg-color="white" outlined v-model="selectedOption" :options="baseLayerSelectorOptions" option-value="value" option-label="label" label="Base Layer" popup-content-class="z-max" @update:model-value="changeBaseLayer" behavior="menu" :tabindex="tabindex" dense options-dense />
    `,
    setup(_, context) {
        const baseLayerSelectorOptions = [
            {value: 'googleterrain', label: 'Google Terrain'},
            {value: 'googleroadmap', label: 'Google Terrain-Roadmap'},
            {value: 'googlealteredroadmap', label: 'Google Roadmap'},
            {value: 'googlehybrid', label: 'Google Satellite-Roadmap'},
            {value: 'googlesatellite', label: 'Google Satellite'},
            {value: 'worldtopo', label: 'ESRI World Topo'},
            {value: 'worldimagery', label: 'ESRI World Imagery'},
            {value: 'esristreet', label: 'ESRI StreetMap'},
            {value: 'ngstopo', label: 'National Geographic Topo'},
            {value: 'natgeoworld', label: 'National Geographic World'},
            {value: 'openstreet', label: 'OpenStreetMap'},
            {value: 'opentopo', label: 'OpenTopo'}
        ];
        const mapSettings = Vue.inject('mapSettings');
        const selectedOption = Vue.ref({});

        function changeBaseLayer(val) {
            selectedOption.value = val;
            context.emit('change-base-layer', val.value);
        }

        function setSelectedOption() {
            selectedOption.value = baseLayerSelectorOptions.find(opt => opt['value'] === mapSettings.selectedBaseLayer);
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
