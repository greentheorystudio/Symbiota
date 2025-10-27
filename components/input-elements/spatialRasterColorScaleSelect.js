const spatialRasterColorScaleSelect = {
    props: {
        selectedColorScale: {
            type: String,
            default: ''
        },
        tabindex: {
            type: Number,
            default: 1
        }
    },
    template: `
        <q-select bg-color="white" outlined v-model="selectedOption" popup-content-class="z-max" :options="rasterColorScales" option-value="value" option-label="label" label="Color scale" @update:model-value="changeRasterColorScale" behavior="menu" :tabindex="tabindex" dense options-dense />
    `,
    setup(props, context) {
        const spatialStore = useSpatialStore();

        const propsRefs = Vue.toRefs(props);
        const rasterColorScales = spatialStore.getRasterColorScales;
        const selectedOption = Vue.ref(null);

        Vue.watch(propsRefs.selectedColorScale, () => {
            setSelectedOption();
        });

        function changeRasterColorScale(val) {
            context.emit('raster-color-scale-change', val.value);
        }

        function setSelectedOption() {
            selectedOption.value = rasterColorScales.find(opt => opt['value'] === props.selectedColorScale);
        }

        Vue.onMounted(() => {
            setSelectedOption();
        });
        
        return {
            rasterColorScales,
            selectedOption,
            changeRasterColorScale
        }
    }
};
