const spatialSymbologyTab = {
    template: `
        <div class="column">
            <div class="row justify-between">
                <div class="q-px-sm q-mb-sm column q-gutter-xs">
                    <q-select bg-color="white" outlined v-model="selectedSymbologyOption" :options="symbologyOptions" option-value="field" option-label="label" label="Symbology" popup-content-class="z-max" behavior="menu" class="spatial-symbology-dropdown" @update:model-value="processSymbologyChange" dense options-dense />
                    <div class="q-mt-sm q-ml-md">
                        <svg xmlns="http://www.w3.org/2000/svg" style="height:15px;width:15px;margin-bottom:-2px;">
                            <g>
                                <circle cx="7.5" cy="7.5" r="7" fill="white" stroke="#000000" stroke-width="1px" ></circle>
                            </g>
                        </svg> = Specimen
                    </div>
                    <div class="q-mt-sm q-ml-md">
                        <svg style="height:14px;width:14px;margin-bottom:-2px;" xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <path stroke="#000000" d="m6.70496,0.23296l-6.70496,13.48356l13.88754,0.12255l-7.18258,-13.60611z" stroke-width="1px" fill="white"/>
                            </g>
                        </svg> = Observation
                    </div>
                </div>
                <div class="q-px-sm q-mb-sm column q-gutter-xs">
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="processResetSymbology();" label="Reset Symbology" dense />
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="autoColorSymbologyKeys();" label="Auto Color" dense />
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="saveSymbologyImage();" label="Save Symbology" dense />
                </div>
            </div>
            <q-separator ></q-separator>
            <div v-if="selectedSymbologyOption" class="q-py-sm">
                <template v-if="selectedSymbologyOption.field !== 'sciname'">
                    <div ref="symbologyRef">
                        <div class="text-h6">
                            {{ selectedSymbologyOption.label + '   [' + symbologyArr[selectedSymbologyOption.field].length + ']' }}
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
                </template>
                <template v-else>
                    <div ref="symbologyRef">
                        <div class="text-h6">
                            {{ 'Taxa   [' + symbologyArr['sciname'].length + ']' }}
                        </div>
                        <div class="column q-gutter-xs">
                            <template v-for="key in symbologyArr['taxonomy']">
                                <template v-if="!key.hasOwnProperty('taxa')">
                                    <div class="q-mt-sm row justify-start">
                                        <div class="q-mr-lg">
                                            <color-picker :color-value="symbologyArr['sciname'].find(kObj => kObj['value'] === key.value).color" @update:color-picker="(value) => processSymbologyKeyColorChange(value, key.value)"></color-picker>
                                        </div>
                                        <div class="text-body1 text-bold self-center">
                                            {{ key.value }}
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <template v-for="key in symbologyArr['taxonomy']">
                                <template v-if="key.hasOwnProperty('taxa')">
                                    <div class="q-mt-sm">
                                        <div class="text-body1 text-bold">
                                            {{ key.value }}
                                        </div>
                                        <div class="q-ml-md column q-gutter-xs">
                                            <template v-for="taxon in key['taxa']">
                                                <div class="row justify-start">
                                                    <div class="q-mr-lg">
                                                        <color-picker :color-value="symbologyArr['sciname'].find(key => key['value'] === taxon.value).color" @update:color-picker="(value) => processSymbologyKeyColorChange(value, taxon.value)"></color-picker>
                                                    </div>
                                                    <div class="text-bold self-center">
                                                        {{ taxon.value }}
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
            <q-separator ></q-separator>
        </div>
    `,
    components: {
        'color-picker': colorPicker
    },
    setup() {
        const spatialStore = useSpatialStore();

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
            updateMapSettings('mapSymbology', 'collectionname');
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
                    if(Number(node.childNodes[0].childNodes[0].nodeType) === 1){
                        const button = node.childNodes[0].childNodes[0];
                        button.innerHTML = '';
                    }
                    else{
                        const taxaKeyNodes = node.childNodes[1].childNodes;
                        taxaKeyNodes.forEach(node => {
                            if(node.childNodes.length > 0){
                                if(Number(node.childNodes[0].childNodes[0].nodeType) === 1){
                                    const button = node.childNodes[0].childNodes[0];
                                    button.innerHTML = '';
                                }
                            }
                        });
                    }
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
