const spatialSidePanel = {
    props: {
        expandedElement: {
            type: String,
            default: 'vector'
        },
        showPanel: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <div class="z-max side-panel-container row animate__animated animate__slow" :class="showPanel ? 'animate__slideInLeft' : 'animate__slideOutLeft'">
            <div class="map-side-panel-inner-container">
                <div ref="contentContainerRef" class="map-side-panel-content">
                    <template v-if="inputWindowMode">
                        <div :style="expansionCardStyle">
                            <spatial-vector-tools-tab></spatial-vector-tools-tab>
                        </div>
                    </template>
                    <template v-else>
                        <q-list bordered>
                            <template v-if="searchRecordCnt > 0">
                                <q-separator></q-separator>
                                <q-expansion-item v-model="recordsExpanded" group="sidepanelexpansiongroup" label="Records and Symbology" header-class="bg-grey-3 text-body1 text-bold" @before-show="() => processExpand('records')">
                                    <q-card class="scroll" :style="expansionTabCardStyle">
                                        <q-card-section class="q-pa-none">
                                            <spatial-records-symbology-expansion></spatial-records-symbology-expansion>
                                        </q-card-section>
                                    </q-card>
                                </q-expansion-item>
                            </template>
                            <q-separator></q-separator>
                            <q-expansion-item v-model="vectorExpanded" group="sidepanelexpansiongroup" label="Vector Tools" header-class="bg-grey-3 text-body1 text-bold" @before-show="() => processExpand('vector')">
                                <q-card class="scroll" :style="expansionTabCardStyle">
                                    <q-card-section class="q-pa-none">
                                        <spatial-vector-tools-expansion></spatial-vector-tools-expansion>
                                    </q-card-section>
                                </q-card>
                            </q-expansion-item>
                            <q-separator></q-separator>
                            <q-expansion-item v-model="rasterExpanded" group="sidepanelexpansiongroup" label="Raster Tools" header-class="bg-grey-3 text-body1 text-bold" @before-show="() => processExpand('raster')">
                                <q-card class="scroll" :style="expansionCardStyle">
                                    <q-card-section>
                                        <spatial-raster-tools-expansion :selected-target-raster="mapSettings.selectedTargetRaster"></spatial-raster-tools-expansion>
                                    </q-card-section>
                                </q-card>
                            </q-expansion-item>
                        </q-list>
                    </template>
                </div>
            </div>
            <div role="button" class="col-grow column justify-center items-center cursor-pointer map-side-panel-close-bar" @click="updateMapSettings('showSidePanel', false);" @keyup.enter="updateMapSettings('showSidePanel', false);" aria-role="Toggle side panel" tabindex="0">
                <q-icon color="white" size="sm" name="fas fa-caret-left"></q-icon>
            </div>
        </div>
    `,
    components: {
        'spatial-raster-tools-expansion': spatialRasterToolsExpansion,
        'spatial-records-symbology-expansion': spatialRecordsSymbologyExpansion,
        'spatial-vector-tools-expansion': spatialVectorToolsExpansion,
        'spatial-vector-tools-tab': spatialVectorToolsTab
    },
    setup(props) {
        const { height } = Quasar.dom;
        const searchStore = useSearchStore();
        const contentContainerRef = Vue.ref(null);
        const expansionCardStyle = Vue.ref('');
        const expansionTabCardStyle = Vue.ref('');
        const inputWindowMode = Vue.inject('inputWindowMode');
        const mapSettings = Vue.inject('mapSettings');
        const propsRefs = Vue.toRefs(props);
        const rasterExpanded = Vue.ref(false);
        const recordsExpanded = Vue.ref(false);
        const searchRecordCnt = Vue.computed(() => searchStore.getSearchRecordCount);
        const vectorExpanded = Vue.ref(false);

        const updateMapSettings = Vue.inject('updateMapSettings');

        Vue.watch(propsRefs.expandedElement, () => {
            setExpandedElement();
        });

        Vue.watch(propsRefs.showPanel, () => {
            setExpansionHeight();
        });

        function processExpand(target) {
            updateMapSettings('sidePanelExpandedElement', target);
            setExpandedElement();
        }

        function setExpandedElement() {
            recordsExpanded.value = (mapSettings.sidePanelExpandedElement === 'records');
            vectorExpanded.value = (mapSettings.sidePanelExpandedElement === 'vector');
            rasterExpanded.value = (mapSettings.sidePanelExpandedElement === 'raster');
        }

        function setExpansionHeight() {
            if(!inputWindowMode.value){
                let expansionCnt = 0;
                let expansionHeight = 0;
                const listElementChildren = contentContainerRef.value.childNodes[0].childNodes;
                listElementChildren.forEach((element) => {
                    if(element.localName === 'div'){
                        if(element.classList.contains('q-expansion-item')){
                            expansionCnt++;
                        }
                        if(expansionHeight === 0 && element.classList.contains('q-expansion-item--collapsed')){
                            expansionHeight = height(element);
                        }
                    }
                });
                const cardHeight = contentContainerRef.value.parentNode.parentNode.clientHeight - ((expansionHeight * expansionCnt) + (2 * expansionCnt) + 6);
                const tabCardHeight = contentContainerRef.value.parentNode.parentNode.clientHeight - ((expansionHeight * expansionCnt) + 12);
                expansionCardStyle.value = 'height: ' + cardHeight + 'px;';
                expansionTabCardStyle.value = 'height: ' + tabCardHeight + 'px;';
            }
            else{
                if(contentContainerRef.value){
                    const cardHeight = contentContainerRef.value.parentNode.parentNode.clientHeight - 8;
                    expansionCardStyle.value = 'height: ' + cardHeight + 'px;overflow-y: scroll;';
                }
            }
        }

        Vue.onMounted(() => {
            window.addEventListener('resize', setExpansionHeight);
            setExpandedElement();
        });

        return {
            contentContainerRef,
            expansionCardStyle,
            expansionTabCardStyle,
            inputWindowMode,
            mapSettings,
            rasterExpanded,
            recordsExpanded,
            searchRecordCnt,
            vectorExpanded,
            height,
            processExpand,
            updateMapSettings
        }
    }
};
