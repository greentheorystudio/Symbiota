const searchCriteriaTab = {
    props: {
        collectionId: {
            type: Number,
            default: null
        },
        showSpatial: {
            type: Boolean,
            default: true
        }
    },
    template: `
        <div class="column q-pa-sm q-col-gutter-sm">
            <div class="row justify-end q-col-gutter-sm">
                <div>
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="resetCriteria();" label="Reset" dense />
                </div>
                <div>
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="loadPoints();" label="Load Records" :disabled="!searchTermsValid" dense />
                </div>
            </div>
            <div class="column q-col-gutter-sm">
                <template v-if="(!showSpatial && (searchTerms.upperlat || searchTerms.pointlat)) || searchTerms.circleArr || searchTerms.polyArr">
                    <div class="text-body1 text-bold">
                        Records will be searched within the spatial criteria
                    </div>
                </template>
                <div>
                    <checkbox-input-element label="Include Synonyms from Taxonomic Thesaurus" :value="searchTerms.usethes" @update:value="(value) => updateSearchTerms('usethes', value)"></checkbox-input-element>
                </div>
                <div class="row q-col-gutter-sm">
                    <div class="col-12 col-sm-4 col-md-3">
                        <selector-input-element label="Taxon Type" :options="taxonTypeOptions" :value="searchTerms.taxontype" @update:value="updateTaxonType"></selector-input-element>
                    </div>
                    <div class="col-12 col-sm-8 col-md-9">
                        <multiple-scientific-common-name-auto-complete :label="scinameFieldLabel" :sciname-arr="scinameArr" :taxon-type="searchTerms.taxontype" @update:sciname="processScientificNameChange"></multiple-scientific-common-name-auto-complete>
                    </div>
                </div>
                <div class="row q-col-gutter-sm">
                    <div class="col-12 col-sm-4 col-md-4">
                        <text-field-input-element label="Country" :value="searchTerms.country" field-hint="Separate multiple terms with semicolons" @update:value="(value) => updateSearchTerms('country', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-4 col-md-4">
                        <text-field-input-element label="State/Province" :value="searchTerms.state" field-hint="Separate multiple terms with semicolons" @update:value="(value) => updateSearchTerms('state', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-4 col-md-4">
                        <text-field-input-element label="County" :value="searchTerms.county" field-hint="Separate multiple terms with semicolons" @update:value="(value) => updateSearchTerms('county', value)"></text-field-input-element>
                    </div>
                </div>
                <div class="row q-col-gutter-sm">
                    <div class="col-12 col-sm-4 col-md-4">
                        <text-field-input-element label="Locality" :value="searchTerms.local" field-hint="Separate multiple terms with semicolons" @update:value="(value) => updateSearchTerms('local', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-4 col-md-4">
                        <text-field-input-element label="Elevation Low" data-type="int" min-value="0" :value="searchTerms.elevlow" @update:value="(value) => updateSearchTerms('elevlow', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-4 col-md-4">
                        <text-field-input-element label="Elevation High" data-type="int" min-value="0" :value="searchTerms.elevhigh" @update:value="(value) => updateSearchTerms('elevhigh', value)"></text-field-input-element>
                    </div>
                </div>
                <template v-if="showSpatial">
                    <div class="q-pa-sm column q-gutter-sm">
                        <q-card flat bordered class="black-border">
                            <q-card-section>
                                <div class="text-body1 text-bold">Define a bounding box in decimal degrees</div>
                                <div class="row q-col-gutter-sm">
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <text-field-input-element label="Northern Latitude" data-type="number" min-value="-90" max-value="90" :value="searchTerms.upperlat" @update:value="(value) => updateSearchTerms('upperlat', value)"></text-field-input-element>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <text-field-input-element label="Southern Latitude" data-type="number" min-value="-90" max-value="90" :value="searchTerms.bottomlat" @update:value="(value) => updateSearchTerms('bottomlat', value)"></text-field-input-element>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <text-field-input-element label="Western Longitude" data-type="number" min-value="-180" max-value="180" :value="searchTerms.leftlong" @update:value="(value) => updateSearchTerms('leftlong', value)"></text-field-input-element>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <text-field-input-element label="Eastern Longitude" data-type="number" min-value="-180" max-value="180" :value="searchTerms.rightlong" @update:value="(value) => updateSearchTerms('rightlong', value)"></text-field-input-element>
                                    </div>
                                </div>
                                <div class="row justify-end">
                                    <div class="self-center">
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openSpatialPopup('input-box');" icon="fas fa-globe" dense>
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Open Mapping Aid
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                        <q-card flat bordered class="black-border">
                            <q-card-section>
                                <div class="text-body1 text-bold">Define a point-radius in decimal degrees</div>
                                <div class="row q-col-gutter-sm">
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <text-field-input-element label="Latitude" data-type="number" min-value="-90" max-value="90" :value="searchTerms.pointlat" @update:value="(value) => updatePointRadiusCriteria('pointlat', value)"></text-field-input-element>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <text-field-input-element label="Longitude" data-type="number" min-value="-180" max-value="180" :value="searchTerms.pointlong" @update:value="(value) => updatePointRadiusCriteria('pointlong', value)"></text-field-input-element>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <text-field-input-element label="Radius" data-type="number" min-value="0" :value="radiusVal" @update:value="(value) => updatePointRadiusCriteria('radiusval', value)"></text-field-input-element>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <selector-input-element :options="radiusUnitOptions" :value="selectedRadiusUnit" @update:value="(value) => updatePointRadiusCriteria('radiusunit', value)"></selector-input-element>
                                    </div>
                                </div>
                                <div class="row justify-end">
                                    <div class="self-center">
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openSpatialPopup('input-circle');" icon="fas fa-globe" dense>
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Open Mapping Aid
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                        <q-card flat bordered class="black-border">
                            <q-card-section class="row justify-between q-gutter-md">
                                <div class="text-body1 text-bold">Open the Spatial Window to define complex spatial criteria</div>
                                <div>
                                    <div class="self-center">
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openSpatialPopup('input');" icon="fas fa-globe" dense>
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Open Mapping Aid
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                    </div>
                </template>
                <div class="row q-col-gutter-sm">
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element label="Collector/Observer's Last Name" :value="searchTerms.collector" field-hint="Separate multiple terms with semicolons" @update:value="(value) => updateSearchTerms('collector', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element label="Collection Number" :value="searchTerms.collnum" field-hint="Separate multiple terms with semicolons" @update:value="(value) => updateSearchTerms('collnum', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <date-input-element label="Date (earliest)" :value="searchTerms.eventdate1" @update:value="(value) => updateDateData('eventdate1', value)"></date-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <date-input-element label="Date (latest)" :value="searchTerms.eventdate2" @update:value="(value) => updateDateData('eventdate2', value)"></date-input-element>
                    </div>
                </div>
                <div class="row q-col-gutter-sm">
                    <div class="col-12 col-sm-4 col-md-4">
                        <text-field-input-element label="Occurrence Remarks" :value="searchTerms.occurrenceRemarks" field-hint="Separate multiple terms with semicolons" @update:value="(value) => updateSearchTerms('occurrenceRemarks', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-4 col-md-4">
                        <text-field-input-element label="Catalog Number" :value="searchTerms.catnum" field-hint="Separate multiple terms with semicolons" @update:value="(value) => updateSearchTerms('catnum', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-4 col-md-4">
                        <checkbox-input-element label="Include other catalog numbers" :value="searchTerms.usethes" @update:value="(value) => updateSearchTerms('othercatnum', value)"></checkbox-input-element>
                    </div>
                </div>
                <template v-if="Number(collectionId) > 0">
                    <div class="row q-col-gutter-sm">
                        <div class="col-12 col-sm-6 col-md-3">
                            <text-field-input-element label="Entered by" :value="searchTerms.enteredby" @update:value="(value) => updateSearchTerms('enteredby', value)"></text-field-input-element>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <date-input-element label="Date entered" :value="searchTerms.dateentered" @update:value="(value) => updateDateData('dateentered', value)"></date-input-element>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <date-input-element label="Date modified" :value="searchTerms.datemodified" @update:value="(value) => updateDateData('datemodified', value)"></date-input-element>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <selector-input-element label="Processing Status" :options="processingStatusOptions" :value="searchTerms.processingstatus" @update:value="(value) => updateSearchTerms('processingstatus', value)"></selector-input-element>
                        </div>
                    </div>
                </template>
                <div class="row q-col-gutter-md">
                    <div class="col-12 col-sm-6 column q-gutter-sm">
                        <div v-if="Number(collectionId) > 0">
                            <checkbox-input-element label="Limit to records without images" :value="searchTerms.withoutimages" @update:value="(value) => updateSearchTerms('withoutimages', value)"></checkbox-input-element>
                        </div>
                        <div>
                            <checkbox-input-element label="Limit to type specimens" :value="searchTerms.typestatus" @update:value="(value) => updateSearchTerms('typestatus', value)"></checkbox-input-element>
                        </div>
                        <div>
                            <checkbox-input-element label="Limit to records with genetic data" :value="searchTerms.hasgenetic" @update:value="(value) => updateSearchTerms('hasgenetic', value)"></checkbox-input-element>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 column q-gutter-sm">
                        <div>
                            <checkbox-input-element label="Limit to records with audio" :value="searchTerms.hasaudio" @update:value="(value) => updateSearchTerms('hasaudio', value)"></checkbox-input-element>
                        </div>
                        <div>
                            <checkbox-input-element label="Limit to records with images" :value="searchTerms.hasimages" @update:value="(value) => updateSearchTerms('hasimages', value)"></checkbox-input-element>
                        </div>
                        <div>
                            <checkbox-input-element label="Limit to records with video" :value="searchTerms.hasvideo" @update:value="(value) => updateSearchTerms('hasvideo', value)"></checkbox-input-element>
                        </div>
                        <div>
                            <checkbox-input-element label="Limit to records with images, audio, or video" :value="searchTerms.hasmedia" @update:value="(value) => updateSearchTerms('hasmedia', value)"></checkbox-input-element>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <template v-if="showSpatialPopup">
            <spatial-analysis-popup
                :bottom-lat="bottomLatitude"
                :circle-arr="circleArr"
                :left-long="leftLongitude"
                :point-lat="pointLatitude"
                :point-long="pointLongitude"
                :poly-arr="polyArr"
                :radius="radius"
                :right-long="rightLongitude"
                :upper-lat="upperLatitude"
                :show-popup="showSpatialPopup"
                :window-type="popupWindowType"
                @update:spatial-data="processSpatialData"
                @close:popup="closeSpatialPopup();"
            ></spatial-analysis-popup>
        </template>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'date-input-element': dateInputElement,
        'multiple-scientific-common-name-auto-complete': multipleScientificCommonNameAutoComplete,
        'selector-input-element': selectorInputElement,
        'spatial-analysis-popup': componentRegistry.hasOwnProperty('spatialAnalysisPopup') ? componentRegistry['spatialAnalysisPopup'] : null,
        'text-field-input-element': textFieldInputElement
    },
    setup(_, context) {
        const baseStore = useBaseStore();
        const searchStore = useSearchStore();

        const bottomLatitude = Vue.ref(null);
        const circleArr = Vue.ref(null);
        const leftLongitude = Vue.ref(null);
        const pointLatitude = Vue.ref(null);
        const pointLongitude = Vue.ref(null);
        const polyArr = Vue.ref(null);
        const popupWindowType = Vue.ref(null);
        const processingStatusOptions = Vue.computed(() => baseStore.getOccurrenceProcessingStatusOptions);
        const radius = Vue.ref(null);
        const radiusUnitOptions = [
            {value: 'km', label: 'Kilometers'},
            {value: 'mi', label: 'Miles'}
        ];
        const radiusVal = Vue.ref(null);
        const rightLongitude = Vue.ref(null);
        const scinameArr = Vue.ref([]);
        const scinameFieldLabel = Vue.ref('Scientific Names');
        const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
        const searchTermsValid = Vue.computed(() => searchStore.getSearchTermsValid);
        const selectedRadiusUnit = Vue.ref('km');
        const showSpatialPopup = Vue.ref(false);
        const taxonTypeOptions = [
            {value: '1', label: 'Family or Scientific Name'},
            {value: '2', label: 'Family only'},
            {value: '3', label: 'Scientific Name only'},
            {value: '4', label: 'Taxonomic group'},
            {value: '5', label: 'Common Name'}
        ];
        const upperLatitude = Vue.ref(null);

        const loadPoints = Vue.inject('loadPoints');

        function clearSpatialInputValues() {
            bottomLatitude.value = null;
            circleArr.value = null;
            leftLongitude.value = null;
            pointLatitude.value = null;
            pointLongitude.value = null;
            polyArr.value = null;
            radius.value = null;
            rightLongitude.value = null;
            upperLatitude.value = null;
        }

        function closeSpatialPopup() {
            popupWindowType.value = null;
            showSpatialPopup.value = false;
            clearSpatialInputValues();
        }

        function openSpatialPopup(type) {
            setSpatialInputValues();
            popupWindowType.value = type;
            showSpatialPopup.value = true;
        }

        function processScientificNameChange(taxonArr) {
            scinameArr.value = taxonArr;
            if(scinameArr.value.length > 0){
                const nameArr = [];
                scinameArr.value.forEach((taxon) => {
                    nameArr.push(taxon.label);
                });
                updateSearchTerms('taxa', nameArr.join(';'));
            }
            else{
                updateSearchTerms('taxa', null);
            }
        }

        function processSpatialData(data) {
            if(popupWindowType.value.includes('box') && data.hasOwnProperty('boundingBoxArr')){
                updateSearchTerms('upperlat', data['boundingBoxArr']['upperlat']);
                updateSearchTerms('bottomlat', data['boundingBoxArr']['bottomlat']);
                updateSearchTerms('leftlong', data['boundingBoxArr']['leftlong']);
                updateSearchTerms('rightlong', data['boundingBoxArr']['rightlong']);
            }
            else if(popupWindowType.value.includes('circle') && data.hasOwnProperty('circleArr') && data['circleArr'].length === 1){
                updateSearchTerms('pointlat', data['circleArr'][0]['pointlat']);
                updateSearchTerms('pointlong', data['circleArr'][0]['pointlong']);
                updateSearchTerms('radius', data['circleArr'][0]['radius']);
                updateSearchTerms('groundradius', data['circleArr'][0]['groundradius']);
                radiusVal.value = data['circleArr'][0]['radius'] / 1000;
                selectedRadiusUnit.value = 'km';
            }
            else if(popupWindowType.value === 'input' && (data.hasOwnProperty('circleArr') || data.hasOwnProperty('polyArr'))){
                if(data.hasOwnProperty('circleArr')){
                    updateSearchTerms('circleArr', data['circleArr']);
                }
                if(data.hasOwnProperty('polyArr')){
                    updateSearchTerms('polyArr', data['polyArr']);
                }
            }
        }

        function setScinameArrFromSearchTerms() {
            const searchTermsScinameArr = searchTerms.value['taxa'].split(';');
            searchTermsScinameArr.forEach((sciname) => {
                scinameArr.value.push({
                    label: sciname.trim(),
                    sciname: sciname.trim()
                });
            });
        }

        function setSpatialInputValues() {
            bottomLatitude.value = searchTerms.value.hasOwnProperty('bottomlat') ? searchTerms.value['bottomlat'] : null;
            circleArr.value = searchTerms.value.hasOwnProperty('circleArr') ? searchTerms.value['circleArr'] : null;
            leftLongitude.value = searchTerms.value.hasOwnProperty('leftlong') ? searchTerms.value['leftlong'] : null;
            pointLatitude.value = searchTerms.value.hasOwnProperty('pointlat') ? searchTerms.value['pointlat'] : null;
            pointLongitude.value = searchTerms.value.hasOwnProperty('pointlong') ? searchTerms.value['pointlong'] : null;
            polyArr.value = searchTerms.value.hasOwnProperty('polyArr') ? searchTerms.value['polyArr'] : null;
            radius.value = searchTerms.value.hasOwnProperty('radius') ? searchTerms.value['radius'] : null;
            rightLongitude.value = searchTerms.value.hasOwnProperty('rightlong') ? searchTerms.value['rightlong'] : null;
            upperLatitude.value = searchTerms.value.hasOwnProperty('upperlat') ? searchTerms.value['upperlat'] : null;
        }

        function resetCriteria() {
            searchStore.clearSearchTerms();
            context.emit('reset:search-criteria');
        }

        function updateDateData(prop, dateData) {
            searchStore.updateSearchTerms(prop, dateData['date']);
        }

        function updatePointRadiusCriteria(prop, value) {
            if(prop === 'pointlat' || prop === 'pointlong'){
                searchStore.updateSearchTerms(prop, value);
            }
            else if(prop === 'radiusval'){
                radiusVal.value = value;
            }
            else if(prop === 'radiusunit'){
                selectedRadiusUnit.value = value;
            }
            updateRadius();
        }

        function updateRadius(){
            if(searchTerms.value['pointlat'] && searchTerms.value['pointlong'] && Number(radiusVal.value) > 0){
                const radius = selectedRadiusUnit.value === 'km' ? (radiusVal.value * 1000) : ((radiusVal.value * 0.621371192) * 1000);
                const centerCoords = ol.proj.fromLonLat([searchTerms.value['pointlong'], searchTerms.value['pointlat']]);
                const edgeCoordinate = [centerCoords[0] + radius, centerCoords[1]];
                const fixedcenter = ol.proj.transform(centerCoords, 'EPSG:3857', 'EPSG:4326');
                const fixededgeCoordinate = ol.proj.transform(edgeCoordinate, 'EPSG:3857', 'EPSG:4326');
                const groundRadius = turf.distance([fixedcenter[0], fixedcenter[1]], [fixededgeCoordinate[0], fixededgeCoordinate[1]]);
                searchStore.updateSearchTerms('radius', radius);
                searchStore.updateSearchTerms('groundradius', groundRadius);
            }
        }

        function updateSearchTerms(prop, value) {
            searchStore.updateSearchTerms(prop, value);
        }

        function updateTaxonType(value) {
            if(Number(value) === 5){
                scinameFieldLabel.value = 'Common Names';
            }
            else{
                scinameFieldLabel.value = 'Scientific Names';
            }
            updateSearchTerms('taxontype', value);
        }

        Vue.onMounted(() => {
            if(searchTerms.value['taxa'] && searchTerms.value['taxa'] !== ''){
                setScinameArrFromSearchTerms();
            }
        });

        return {
            bottomLatitude,
            circleArr,
            leftLongitude,
            pointLatitude,
            pointLongitude,
            polyArr,
            popupWindowType,
            processingStatusOptions,
            radius,
            radiusUnitOptions,
            radiusVal,
            rightLongitude,
            scinameArr,
            scinameFieldLabel,
            searchTerms,
            searchTermsValid,
            selectedRadiusUnit,
            showSpatialPopup,
            taxonTypeOptions,
            upperLatitude,
            closeSpatialPopup,
            loadPoints,
            openSpatialPopup,
            processScientificNameChange,
            processSpatialData,
            resetCriteria,
            updateDateData,
            updatePointRadiusCriteria,
            updateSearchTerms,
            updateTaxonType
        }
    }
};
