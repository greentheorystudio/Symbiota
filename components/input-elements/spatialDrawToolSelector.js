const spatialDrawToolSelector = {
    props: {
        tabindex: {
            type: Number,
            default: 1
        }
    },
    template: `
        <q-select bg-color="white" outlined v-model="selectedOption" :options="drawSelectorOptions" option-value="value" option-label="label" label="Draw" popup-content-class="z-max" class="draw-tool-dropdown" @update:model-value="drawSelectorChange" behavior="menu" :tabindex="tabindex" dense options-dense />
    `,
    setup() {
        const drawSelectorOptions = Vue.ref([
            {value: 'None', label: 'None'}
        ]);
        const inputWindowToolsArr = Vue.inject('inputWindowToolsArr');
        const map = Vue.inject('map');
        const mapSettings = Vue.inject('mapSettings');
        const selectedOption = Vue.ref(null);

        const changeDraw = Vue.inject('changeDraw');
        const updateMapSettings = Vue.inject('updateMapSettings');

        Vue.watch(mapSettings, () => {
            setSelectedOption();
        });

        function drawSelectorChange(val) {
            updateMapSettings('selectedDrawTool', val.value);
            map.value.removeInteraction(mapSettings.draw);
            changeDraw();
        }

        function setDrawSelectorOptions() {
            if(inputWindowToolsArr.length === 0 || inputWindowToolsArr.includes('polygon')){
                const newOption = {value: 'Polygon', label: 'Polygon'};
                drawSelectorOptions.value.push(newOption);
            }
            if(inputWindowToolsArr.length === 0 || inputWindowToolsArr.includes('polygon') || inputWindowToolsArr.includes('box')){
                const newOption = {value: 'Box', label: 'Box   '};
                drawSelectorOptions.value.push(newOption);
            }
            if(inputWindowToolsArr.length === 0 || inputWindowToolsArr.includes('circle')){
                const newOption = {value: 'Circle', label: 'Circle'};
                drawSelectorOptions.value.push(newOption);
            }
            if(inputWindowToolsArr.length === 0 || inputWindowToolsArr.includes('linestring')){
                const newOption = {value: 'LineString', label: 'Line'};
                drawSelectorOptions.value.push(newOption);
            }
            if(inputWindowToolsArr.length === 0 || inputWindowToolsArr.includes('point')){
                const newOption = {value: 'Point', label: 'Point'};
                drawSelectorOptions.value.push(newOption);
            }
        }

        function setSelectedOption() {
            selectedOption.value = drawSelectorOptions.value.find(opt => opt['value'] === mapSettings.selectedDrawTool);
        }

        Vue.onMounted(() => {
            setDrawSelectorOptions();
            setSelectedOption();
        });
        
        return {
            drawSelectorOptions,
            mapSettings,
            selectedOption,
            drawSelectorChange
        }
    }
};
