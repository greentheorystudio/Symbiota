const recordInfoWindowPopup = {
    props: {
        recordId: {
            type: Number,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-map-popup overflow-hidden">
                <div class="row justify-end items-start map-popup-header">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentContainerRef" class="fit">
                    <q-card flat bordered :style="tabCardStyle">
                        <q-tabs v-model="selectedTab" active-bg-color="grey-4" align="left">
                            <q-tab class="bg-grey-3" label="Details" name="details" no-caps />
                            <template v-if="siteInfo && siteInfo['LatitudeDD'] && siteInfo['LongitudeDD']">
                                <q-tab class="bg-grey-3" label="Map" name="map" no-caps />
                            </template>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="selectedTab" animated>
                            <q-tab-panel name="details" :style="tabPanelStyle">
                                <template v-if="siteInfo && Number(siteInfo.isEditor) === 1">
                                    <div class="row full-width justify-end">
                                        <q-btn color="grey-4" size="sm" text-color="black" class="black-border" icon="far fa-edit" @click="openSiteEditor();" dense>
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Edit site
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </template>
                                <div><span class="text-bold">Site ID: </span>{{ recordId }}</div>
                                <template v-if="siteInfo && siteInfo.SiteName">
                                    <div><span class="text-bold">Site Name: </span>{{ siteInfo.SiteName }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.ShortName">
                                    <div><span class="text-bold">Short Name: </span>{{ siteInfo.ShortName }}</div>
                                </template>
                                <template v-if="localityStr">
                                    <div><span class="text-bold">Locality: </span>{{ localityStr }}</div>
                                </template>
                                <template v-if="landUnitStr">
                                    <div><span class="text-bold">Land Unit: </span>{{ landUnitStr }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.USGS_Quad">
                                    <div><span class="text-bold">USGS Quad: </span>{{ siteInfo.USGS_Quad }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.HUC">
                                    <div><span class="text-bold">HUC: </span>{{ siteInfo.HUC }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.AKA">
                                    <div><span class="text-bold">AKA: </span>{{ siteInfo.AKA }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.FeatureID">
                                    <div><span class="text-bold">GNIS Feature: </span>{{ siteInfo.FeatureID }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.LatitudeDD && siteInfo.LongitudeDD">
                                    <div><span class="text-bold">Decimal Coordinates: </span>{{ siteInfo.LatitudeDD }}, {{ siteInfo.LongitudeDD }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.SiteDescription">
                                    <div><span class="text-bold">Site Description: </span>{{ siteInfo.SiteDescription }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.SpringType1">
                                    <div><span class="text-bold">Spring Type: </span>{{ siteInfo.SpringType1 }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.SpringType2">
                                    <div><span class="text-bold">Secondary Spring Type: </span>{{ siteInfo.SpringType2 }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.EmergenceEnvironment">
                                    <div><span class="text-bold">Emergence Environment: </span>{{ siteInfo.EmergenceEnvironment }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.SourceGeo">
                                    <div><span class="text-bold">Source Geomorphology: </span>{{ siteInfo.SourceGeo }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.SourceGeo2">
                                    <div><span class="text-bold">Secondary Geomorphology: </span>{{ siteInfo.SourceGeo2 }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.FlowForceMech">
                                    <div><span class="text-bold">Flow Force Mechanism: </span>{{ siteInfo.FlowForceMech }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.LithoPrimary">
                                    <div><span class="text-bold">Primary Lithology: </span>{{ siteInfo.LithoPrimary }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.LithoSecondary">
                                    <div><span class="text-bold">Secondary Lithology: </span>{{ siteInfo.LithoSecondary }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.GeoLayer">
                                    <div><span class="text-bold">Geologic Layer: </span>{{ siteInfo.GeoLayer }}</div>
                                </template>
                                <template v-if="siteInfo && siteInfo.ChannelDynamics">
                                    <div><span class="text-bold">Channel Dynamics: </span>{{ siteInfo.ChannelDynamics }}</div>
                                </template>
                                <template v-if="siteSurveys && siteSurveys.surveys.length">
                                    <div><span class="text-bold"># of Surveys: </span>{{ siteSurveys.surveys.length }}</div>
                                </template>
                                <template v-if="siteSurveys && siteSurveys.highestEOD">
                                    <div><span class="text-bold">High EOD: </span>{{ siteSurveys.highestEOD }}</div>
                                </template>
                                <template v-if="siteSurveys && siteSurveys.avgEOD">
                                    <div><span class="text-bold">Avg EOD: </span>{{ siteSurveys.avgEOD }}</div>
                                </template>
                                <template v-if="siteRepImages.length || siteSketchImages.length">
                                    <q-card flat bordered class="q-mt-xs">
                                        <q-card-section class="row q-gutter-sm">
                                            <div class="text-bold full-width q-mb-sm q-pl-md">Images</div>
                                            <template v-if="siteRepImages.length">
                                                <q-card v-for="image in siteRepImages" style="width: 200px;">
                                                    <a :href="image.ImageHyperlink" target="_blank">
                                                        <img :src="image.ImageHyperlink" style="width: 200px;">
                                                    </a>
                                                    <template v-if="image.Comments">
                                                        <q-card-section>
                                                            {{ image.Comments }}
                                                        </q-card-section>
                                                    </template>
                                                </q-card>
                                            </template>
                                            <template v-if="siteSketchImages.length">
                                                <q-card v-for="image in siteSketchImages" style="width: 200px;">
                                                    <a :href="image.ImageHyperlink" target="_blank">
                                                        <img :src="image.ImageHyperlink" style="width: 200px;">
                                                    </a>
                                                    <template v-if="image.Comments">
                                                        <q-card-section>
                                                            {{ image.Comments }}
                                                        </q-card-section>
                                                    </template>
                                                </q-card>
                                            </template>
                                        </q-card-section>
                                    </q-card>
                                </template>
                                <template v-if="siteReports.length">
                                    <q-card flat bordered class="q-mt-xs">
                                        <q-card-section class="row q-gutter-sm">
                                            <div class="text-bold full-width q-mb-sm q-pl-md">Reports</div>
                                            <q-list dense padding>
                                                <q-item v-for="rep in siteReports" :href="rep.ReportHyperlink" target="_blank" clickable v-ripple>
                                                    <q-item-section>
                                                        {{ rep.Description ? rep.Description : rep.ReportHyperlink }}
                                                    </q-item-section>
                                                </q-item>
                                            </q-list>
                                        </q-card-section>
                                    </q-card>
                                </template>
                            </q-tab-panel>
                            <template v-if="siteInfo && siteInfo['LatitudeDD'] && siteInfo['LongitudeDD']">
                                <q-tab-panel name="map" class="q-pa-none" :style="tabPanelStyle">
                                    <spatial-viewer-element :coordinate-set="siteCoordArr"></spatial-viewer-element>
                                </q-tab-panel>
                            </template>
                        </q-tab-panels>
                    </q-card>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'spatial-viewer-element': spatialViewerElement
    },
    setup(props, context) {
        const store = useBaseStore();
        const clientRoot = store.getClientRoot;
        const contentContainerRef = Vue.ref(null);
        const landUnitStr = Vue.ref(null);
        const localityStr = Vue.ref(null);
        const propsRefs = Vue.toRefs(props);
        const selectedTab = Vue.ref('details');
        const siteCoordArr = Vue.shallowReactive([]);
        const siteInfo = Vue.ref(null);
        const siteImages = Vue.ref(null);
        const siteRepImages = Vue.ref([]);
        const siteReports = Vue.ref([]);
        const siteSketchImages = Vue.ref([]);
        const siteSurveys = Vue.ref(null);
        const tabCardStyle = Vue.ref('');
        const tabPanelStyle = Vue.ref('');

        Vue.watch(propsRefs.recordId, () => {
            if(props.recordId){
                getSiteInfo();
                getSiteImages();
                getSiteReports();
                getSiteSurveys();
            }
        });

        Vue.watch(contentContainerRef, () => {
            if(contentContainerRef.value){
                setTabPanelHeights();
            }
        });

        function closePopup() {
            context.emit('close:popup');
            siteInfo.value = null;
            siteImages.value = null;
            siteRepImages.value = [];
            siteReports.value = [];
            siteSketchImages.value = [];
            siteCoordArr.length = 0;
            siteSurveys.value = null;
            landUnitStr.value = null;
            localityStr.value = null;
            tabCardStyle.value = '';
            selectedTab.value = 'details';
        }

        function getSiteImages() {
            const formData = new FormData();
            formData.append('siteid', props.recordId);
            formData.append('action', 'getSiteImages');
            fetch(siteApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                siteImages.value = data;
                siteRepImages.value = siteImages.value['representative'];
                siteSketchImages.value = siteImages.value['sketch'];
            });
        }

        function getSiteInfo() {
            const formData = new FormData();
            formData.append('siteid', props.recordId);
            formData.append('action', 'getSiteArr');
            fetch(siteApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                siteInfo.value = data;
                localityStr.value = siteInfo.value['Country'] ? siteInfo.value['Country'] : 'Country Not Recorded';
                localityStr.value += siteInfo.value['StateProvince'] ? (', ' + siteInfo.value['StateProvince']) : ', State/Province Not Recorded';
                if(siteInfo.value['County']){
                    localityStr.value += (', ' + siteInfo.value['County']);
                }
                if(siteInfo.value['AccessDescript']){
                    localityStr.value += (', ' + siteInfo.value['AccessDescript']);
                }
                if(siteInfo.value['LandUnit']){
                    landUnitStr.value = siteInfo.value['LandUnit'];
                    landUnitStr.value += (', ' + siteInfo.value['LandUnitDetail']);
                }
                if(siteInfo.value['LatitudeDD'] && siteInfo.value['LongitudeDD']){
                    const coordArr = [];
                    coordArr.push(Number(siteInfo.value['LongitudeDD']));
                    coordArr.push(Number(siteInfo.value['LatitudeDD']));
                    siteCoordArr.push(coordArr);
                }
            });
        }

        function getSiteReports() {
            const formData = new FormData();
            formData.append('siteid', props.recordId);
            formData.append('action', 'getSiteReports');
            fetch(siteApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                siteReports.value = data;
            });
        }

        function getSiteSurveys() {
            const formData = new FormData();
            formData.append('siteid', props.recordId);
            formData.append('action', 'getSiteSurveys');
            fetch(siteApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                siteSurveys.value = data;
            });
        }

        function openSiteEditor() {
            closePopup();
            window.open((clientRoot + '/management/site/index.php?siteid=' + props.recordId), '_blank');
        }

        function setTabPanelHeights() {
            if(props.showPopup){
                const clientHeight = contentContainerRef.value.clientHeight;
                tabCardStyle.value = 'height: ' + clientHeight + 'px;';
                tabPanelStyle.value = 'height: ' + (clientHeight - 82) + 'px;';
            }
        }

        Vue.onMounted(() => {
            window.addEventListener('resize', setTabPanelHeights);
        });

        return {
            contentContainerRef,
            landUnitStr,
            localityStr,
            selectedTab,
            siteCoordArr,
            siteInfo,
            siteRepImages,
            siteReports,
            siteSketchImages,
            siteSurveys,
            tabCardStyle,
            tabPanelStyle,
            closePopup,
            openSiteEditor
        }
    }
};
