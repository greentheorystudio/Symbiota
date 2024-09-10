const spatialSymbologyTab = {
    template: `
        <div class="column">
            <div class="q-px-sm q-mb-sm row justify-start">
                <q-select bg-color="white" outlined v-model="selectedSymbologyOption" :options="symbologyOptions" option-value="field" option-label="label" label="Symbology" popup-content-class="z-max" behavior="menu" class="spatial-symbology-dropdown" @update:model-value="processSymbologyChange" dense options-dense />
            </div>
            <div class="q-px-sm q-mb-sm row justify-between q-gutter-xs">
                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="processResetSymbology();" label="Reset Symbology" dense />
                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="autoColorSymbologyKeys();" label="Auto Color" dense />
                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="saveSymbologyImage();" label="Save Symbology" dense />
            </div>
            <q-separator ></q-separator>
            <template v-if="selectedSymbologyOption && symbologyArr[selectedSymbologyOption.field]">
                <div ref="symbologyRef" class="q-py-sm">
                    <div class="text-h6">
                        {{ selectedSymbologyOption.label }}
                    </div>
                    <div class="q-mt-sm column q-gutter-xs">
                        <template v-for="key in symbologyArr[selectedSymbologyOption.field]">
                            <div class="row justify-start">
                                <div class="q-mr-lg">
                                    <color-picker :color-value="key.color" @update:color-picker="(value) => processSymbologyKeyColorChange(value, key.value)"></color-picker>
                                </div>
                                <div class="text-bold self-center">
                                    {{ key.value }}
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <q-separator ></q-separator>
            </template>
        </div>
    `,
    components: {
        'color-picker': colorPicker
    },
    setup() {
        const spatialStore = Vue.inject('spatialStore');

        const mapSettings = Vue.inject('mapSettings');
        const selectedSymbologyOption = Vue.ref(null);
        const symbologyArr = Vue.inject('symbologyArr');
        const symbologyOptions = spatialStore.getSymbologyOptions;
        const symbologyRef = Vue.ref(null);

        const autoColorSymbologyKeys = Vue.inject('autoColorSymbologyKeys');
        const changeMapSymbology = Vue.inject('changeMapSymbology');
        const processSymbologyKeyColorChange = Vue.inject('processSymbologyKeyColorChange');
        const resetSymbology = Vue.inject('resetSymbology');
        const updateMapSettings = Vue.inject('updateMapSettings');

        function processResetSymbology() {
            updateMapSettings('mapSymbology', mapSettings.defaultSymbology);
            setSelectedSymbologyOption();
            resetSymbology();
        }

        function processSymbologyChange(option) {
            updateMapSettings('mapSymbology', option.field);
            changeMapSymbology();
        }

        function saveSymbologyImage() {
            const keyClone = symbologyRef.value.cloneNode(true);
            keyClone.style.backgroundColor = 'white';
            const keyNodes = keyClone.childNodes[1].childNodes;
            keyNodes.forEach(node => {
                if(node.childNodes.length > 0){
                    const button = node.childNodes[0].childNodes[0];
                    button.innerHTML = '';
                }
            });
            document.body.appendChild(keyClone);
            html2canvas(keyClone).then((canvas) => {
                canvas.toBlob((blob) => {
                    saveAs(blob,'map-symbology.png');
                    document.body.removeChild(keyClone);
                });
            });
        }

        function setSelectedSymbologyOption() {
            selectedSymbologyOption.value = symbologyOptions.find(opt => opt['field'] === mapSettings.mapSymbology);
        }

        Vue.onMounted(() => {
            setSelectedSymbologyOption();
        });

        return {
            selectedSymbologyOption,
            symbologyArr,
            symbologyOptions,
            symbologyRef,
            autoColorSymbologyKeys,
            processSymbologyChange,
            processResetSymbology,
            processSymbologyKeyColorChange,
            saveSymbologyImage
        }
    }
};
