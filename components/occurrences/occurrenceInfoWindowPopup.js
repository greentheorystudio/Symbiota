const occurrenceInfoWindowPopup = {
    props: {
        occurrenceId: {
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
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentContainerRef" class="fit">
                    <q-card flat bordered :style="tabCardStyle">
                        <q-tabs v-model="selectedTab" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                            <q-tab class="bg-grey-3" label="Details" name="details" no-caps />
                            <template v-if="occurrenceData['decimallatitude'] && occurrenceData['decimallongitude']">
                                <q-tab class="bg-grey-3" label="Map" name="map" no-caps />
                            </template>
                            <template v-if="determinationArr.length > 0">
                                <q-tab class="bg-grey-3" label="Determination History" name="determination" no-caps />
                            </template>
                            <template v-if="imageArr.length > 0 || mediaArr.length > 0">
                                <q-tab class="bg-grey-3" label="Media" name="media" no-caps />
                            </template>
                            <template v-if="checklistArr.length > 0 || geneticLinkArr.length > 0">
                                <q-tab class="bg-grey-3" label="Linked Resources" name="resources" no-caps />
                            </template>
                            <template v-if="eventMofDataExists">
                                <q-tab class="bg-grey-3" :label="(eventMofDataLabel ? eventMofDataLabel : 'Event Measurements or Facts')" name="eventmof" no-caps />
                            </template>
                            <template v-if="occurrenceMofDataExists">
                                <q-tab class="bg-grey-3" :label="(occurrenceMofDataLabel ? occurrenceMofDataLabel : 'Occurrence Measurements or Facts')" name="occurrencemof" no-caps />
                            </template>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="selectedTab" animated>
                            <q-tab-panel name="details" :style="tabPanelStyle">
                                <div class="row justify-start q-gutter-md">
                                    <div v-if="collectionData.icon">
                                        <q-img :src="collectionData.icon" class="coll-icon-occurrence-info-popup" :fit="contain"></q-img>
                                    </div>
                                    <div class="text-h6 text-bold">
                                        {{ collectionNameStr  }}
                                    </div>
                                </div>
                                <div class="q-mt-sm row justify-between q-pb-md">
                                    <div class="col-12 col-md-6 q-pl-md column">
                                        <div v-if="occurrenceData['catalognumber']">
                                            <span class="text-bold">Catalog Number:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['catalognumber'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['othercatalognumbers']">
                                            <span class="text-bold">Other Catalog Numbers:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['othercatalognumbers'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['occurrenceid']">
                                            <span class="text-bold">Occurrence ID:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['occurrenceid'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['sciname']">
                                            <span class="text-bold">Taxon:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['identificationqualifier'] ? occurrenceData['identificationqualifier'] + ' ' : '' }}<span class="text-italic">{{ occurrenceData['sciname'] }}</span></span>
                                        </div>
                                        <div v-if="occurrenceData['family']">
                                            <span class="text-bold">Family:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['family'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['identifiedby']">
                                            <span class="text-bold">Identified By:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['identifiedby'] + (occurrenceData['dateidentified'] ? ' ' + occurrenceData['dateidentified'] : '') }}</span>
                                        </div>
                                        <div v-if="occurrenceData['taxonremarks']">
                                            <span class="text-bold">Taxon Remarks:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['taxonremarks'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['identificationreferences']">
                                            <span class="text-bold">Identification References:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['identificationreferences'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['identificationremarks']">
                                            <span class="text-bold">Identification Remarks:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['identificationremarks'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['typestatus']">
                                            <span class="text-bold">Type Status:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['typestatus'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['recordedby']">
                                            <span class="text-bold">Collector/Observer:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['recordedby'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['recordnumber']">
                                            <span class="text-bold">Collection Number:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['recordnumber'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['eventdate']">
                                            <span class="text-bold">Date:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['eventdate'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['verbatimeventdate']">
                                            <span class="text-bold">Verbatim Date:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['verbatimeventdate'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['eventtime']">
                                            <span class="text-bold">Time:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['eventtime'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['associatedcollectors']">
                                            <span class="text-bold">Associated Collectors:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['associatedcollectors'] }}</span>
                                        </div>
                                        <div v-if="occurrenceLocalityStr">
                                            <span class="text-bold">Locality:</span>
                                            <span class="q-ml-sm">{{ occurrenceLocalityStr }}</span>
                                        </div>
                                        <div v-if="occurrenceCoordinateStr">
                                            <span class="text-bold">Latitude/Longitude:</span>
                                            <span class="q-ml-sm">{{ occurrenceCoordinateStr }}</span>
                                        </div>
                                        <div v-if="occurrenceData['verbatimcoordinates']">
                                            <span class="text-bold">Verbatim Coordinates:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['verbatimcoordinates'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['locationremarks']">
                                            <span class="text-bold">Location Remarks:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['locationremarks'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['georeferenceremarks']">
                                            <span class="text-bold">Georeference Remarks:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['georeferenceremarks'] }}</span>
                                        </div>
                                        <div v-if="occurrenceElevationStr">
                                            <span class="text-bold">Elevation:</span>
                                            <span class="q-ml-sm">{{ occurrenceElevationStr }}</span>
                                        </div>
                                        <div v-if="occurrenceData['verbatimelevation']">
                                            <span class="text-bold">Verbatim Elevation:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['verbatimelevation'] }}</span>
                                        </div>
                                        <div v-if="occurrenceDepthStr">
                                            <span class="text-bold">Depth:</span>
                                            <span class="q-ml-sm">{{ occurrenceDepthStr }}</span>
                                        </div>
                                        <div v-if="occurrenceData['verbatimdepth']">
                                            <span class="text-bold">Verbatim Depth:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['verbatimdepth'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['habitat']">
                                            <span class="text-bold">Habitat:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['habitat'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['substrate']">
                                            <span class="text-bold">Substrate:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['substrate'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['associatedtaxa']">
                                            <span class="text-bold">Associated Taxa:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['associatedtaxa'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['verbatimattributes']">
                                            <span class="text-bold">Verbatim Attributes:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['verbatimattributes'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['dynamicproperties']">
                                            <span class="text-bold">Dynamic Properties:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['dynamicproperties'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['reproductivecondition']">
                                            <span class="text-bold">Reproductive Condition:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['reproductivecondition'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['lifestage']">
                                            <span class="text-bold">Lifestage:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['lifestage'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['sex']">
                                            <span class="text-bold">Sex:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['sex'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['individualcount']">
                                            <span class="text-bold">Individual Count:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['individualcount'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['samplingprotocol']">
                                            <span class="text-bold">Sampling Protocol:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['samplingprotocol'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['occurrenceremarks']">
                                            <span class="text-bold">Occurrence Remarks:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['occurrenceremarks'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['establishmentmeans']">
                                            <span class="text-bold">Establishment Means:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['establishmentmeans'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['preparations']">
                                            <span class="text-bold">Preparations:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['preparations'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['disposition']">
                                            <span class="text-bold">Disposition:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['disposition'] }}</span>
                                        </div>
                                        <div v-if="occurrenceData['informationwithheld']">
                                            <span class="text-bold">Information Withheld:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['informationwithheld'] }}</span>
                                        </div>
                                        <div v-if="collectionData['rights']">
                                            <span class="text-bold">Usage Rights:</span>
                                            <span class="q-ml-sm"><a :href="collectionData['rights']" target="_blank">{{ collectionData['rights'] }}</a></span>
                                        </div>
                                        <div v-if="occurrenceData['guid']">
                                            <span class="text-bold">Record ID:</span>
                                            <span class="q-ml-sm">{{ occurrenceData['guid'] }}</span>
                                        </div>
                                    </div>
                                    <div v-if="imageArr.length > 0" class="col-12 col-md-6">
                                        <image-carousel :image-arr="imageArr"></image-carousel>
                                    </div>
                                </div>
                                <div v-if="(collectionData['contact'] && collectionData['email']) || isEditor" class="column q-gutter-xs text-body1 q-pb-md">
                                    <div v-if="collectionData['contact'] && collectionData['email']">
                                        For additional information about this specimen, please contact: {{ collectionData['contact'] }} (<a :href="('mailto:' + collectionData['email'])">{{ collectionData['email'] }}</a>)
                                    </div>
                                    <div v-if="isEditor">
                                        You can edit this record using the <a :href="(clientRoot + '/collections/editor/occurrenceeditor.php?occid=' + occurrenceId)" target="_blank">Occurrence Editor</a>.
                                    </div>
                                </div>
                            </q-tab-panel>
                            <template v-if="occurrenceData['decimallatitude'] && occurrenceData['decimallongitude']">
                                <q-tab-panel name="map" class="q-pa-none" :style="tabPanelStyle">
                                    <spatial-viewer-element :coordinate-set="coordinateArr"></spatial-viewer-element>
                                </q-tab-panel>
                            </template>
                            <template v-if="determinationArr.length > 0">
                                <q-tab-panel name="determination" :style="tabPanelStyle">
                                    <div class="text-h6 text-bold">Determination History</div>
                                    <div class="q-mt-sm column q-gutter-sm">
                                        <template v-for="determination in determinationArr">
                                            <determination-record-info-block :determination-data="determination"></determination-record-info-block>
                                        </template>
                                    </div>
                                </q-tab-panel>
                            </template>
                            <template v-if="imageArr.length > 0 || mediaArr.length > 0">
                                <q-tab-panel name="media" :style="tabPanelStyle">
                                    <template v-if="imageArr.length > 0">
                                        <div class="q-mt-sm column q-gutter-sm">
                                            <div class="text-h6 text-bold">Images</div>
                                            <template v-for="image in imageArr">
                                                <image-record-info-block :image-data="image"></image-record-info-block>
                                            </template>
                                        </div>
                                    </template>
                                    <template v-if="mediaArr.length > 0">
                                        <div class="q-mt-sm column q-gutter-sm">
                                            <div class="text-h6 text-bold">Media</div>
                                            <template v-for="media in mediaArr">
                                                <media-record-info-block :media-data="media"></media-record-info-block>
                                            </template>
                                        </div>
                                    </template>
                                </q-tab-panel>
                            </template>
                            <template v-if="checklistArr.length > 0 || geneticLinkArr.length > 0">
                                <q-tab-panel name="resources" :style="tabPanelStyle">
                                    <template v-if="checklistArr.length > 0">
                                        <div class="text-h6 text-bold">Checklist Voucher Linkages</div>
                                        <div class="q-pl-sm column q-gutter-sm">
                                            <q-list dense>
                                                <template v-for="checklist in checklistArr">
                                                    <q-item>
                                                        <q-item-section>
                                                            <div class="row justify-start q-gutter-md items-center">
                                                                <div class="text-body1">
                                                                    <a :href="(clientRoot + '/checklists/checklist.php?cl=' + checklist.clid)" target="_blank">
                                                                        {{ checklist.name }}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </q-item-section>
                                                    </q-item>
                                                </template>
                                            </q-list>
                                        </div>
                                    </template>
                                    <template v-if="geneticLinkArr.length > 0">
                                        <div class="text-h6 text-bold">Genetic Record Linkages</div>
                                        <div class="q-mt-sm column q-gutter-sm">
                                            <template v-for="linkage in geneticLinkArr">
                                                <genetic-link-record-info-block :genetic-linkage-data="linkage"></genetic-link-record-info-block>
                                            </template>
                                        </div>
                                    </template>
                                </q-tab-panel>
                            </template>
                            <template v-if="eventMofDataExists">
                                <q-tab-panel name="eventmof" :style="tabPanelStyle">
                                    <div class="column q-gutter-xs">
                                        <template v-for="key in Object.keys(eventMofDataFields)">
                                            <template v-if="eventMofData.hasOwnProperty(key) && eventMofData[key]">
                                                <div class="row justify-start q-gutter-sm">
                                                    <div class="text-bold">
                                                        {{ (eventMofDataFields[key]['label'] ? eventMofDataFields[key]['label'] : key) + ':' }}
                                                    </div>
                                                    <div>
                                                        {{ eventMofData[key] }}
                                                    </div>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </q-tab-panel>
                            </template>
                            <template v-if="occurrenceMofDataExists">
                                <q-tab-panel name="occurrencemof" :style="tabPanelStyle">
                                    <div class="column q-gutter-xs">
                                        <template v-for="key in Object.keys(occurrenceMofDataFields)">
                                            <template v-if="occurrenceMofData.hasOwnProperty(key) && occurrenceMofData[key]">
                                                <div class="row justify-start q-gutter-sm">
                                                    <div class="text-bold">
                                                        {{ (occurrenceMofDataFields[key]['label'] ? occurrenceMofDataFields[key]['label'] : key) + ':' }}
                                                    </div>
                                                    <div>
                                                        {{ occurrenceMofData[key] }}
                                                    </div>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </q-tab-panel>
                            </template>
                        </q-tab-panels>
                    </q-card>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'determination-record-info-block': determinationRecordInfoBlock,
        'genetic-link-record-info-block': geneticLinkRecordInfoBlock,
        'image-carousel': imageCarousel,
        'image-record-info-block': imageRecordInfoBlock,
        'media-record-info-block': mediaRecordInfoBlock,
        'spatial-viewer-element': spatialViewerElement
    },
    setup(props, context) {
        const baseStore = useBaseStore();

        const checklistArr = Vue.ref([]);
        const clientRoot = baseStore.getClientRoot;
        const collectionData = Vue.ref({});
        const collectionNameStr = Vue.computed(() => {
            let nameStr = '';
            if(collectionData.value.hasOwnProperty('collectionname')){
                nameStr += collectionData.value['collectionname'];
                if(collectionData.value['institutioncode'] || collectionData.value['collectioncode']){
                    nameStr += ' (';
                }
                if(collectionData.value['institutioncode']){
                    nameStr += collectionData.value['institutioncode'];
                }
                if(collectionData.value['institutioncode'] && collectionData.value['collectioncode']){
                    nameStr += ':';
                }
                if(collectionData.value['collectioncode']){
                    nameStr += collectionData.value['collectioncode'];
                }
                if(collectionData.value['institutioncode'] || collectionData.value['collectioncode']){
                    nameStr += ')';
                }
            }
            return nameStr;
        });
        const collectionPermissions = Vue.ref([]);
        const contentContainerRef = Vue.ref(null);
        const coordinateArr = Vue.computed(() => {
            if(occurrenceData.value.hasOwnProperty('decimallatitude') && occurrenceData.value.hasOwnProperty('decimallongitude')){
                return [[Number(occurrenceData.value['decimallongitude']), Number(occurrenceData.value['decimallatitude'])]];
            }
            return[];
        });
        const determinationArr = Vue.ref([]);
        const eventMofData = Vue.ref({});
        const eventMofDataExists = Vue.computed(() => {
            let exist = false;
            Object.keys(eventMofDataFields.value).forEach(key => {
                if(eventMofData.value.hasOwnProperty(key) && eventMofData.value[key]){
                    exist = true;
                }
            });
            return exist;
        });
        const eventMofDataFields = Vue.ref({});
        const eventMofDataLabel = Vue.ref(null);
        const geneticLinkArr = Vue.ref([]);
        const imageArr = Vue.ref([]);
        const isEditor = Vue.computed(() => {
            return (collectionPermissions.value.includes('CollAdmin') || collectionPermissions.value.includes('CollEditor'));
        });
        const mediaArr = Vue.ref([]);
        const occurrenceCoordinateStr = Vue.computed(() => {
            let returnStr = '';
            if(occurrenceData.value.hasOwnProperty('occid')){
                if(occurrenceData.value['decimallatitude']){
                    returnStr += occurrenceData.value['decimallatitude'];
                }
                if(occurrenceData.value['decimallongitude']){
                    returnStr += (returnStr ? ' ' : '') + occurrenceData.value['decimallongitude'];
                }
                if(occurrenceData.value['coordinateuncertaintyinmeters']){
                    returnStr += (returnStr ? ' ' : '') + '+-' + occurrenceData.value['coordinateuncertaintyinmeters'];
                }
                if(occurrenceData.value['geodeticdatum']){
                    returnStr += (returnStr ? ' ' : '') + occurrenceData.value['geodeticdatum'];
                }
            }
            return returnStr;
        });
        const occurrenceData = Vue.ref({});
        const occurrenceDepthStr = Vue.computed(() => {
            let returnStr = '';
            if(occurrenceData.value.hasOwnProperty('occid') && occurrenceData.value['minimumdepthinmeters']){
                returnStr += occurrenceData.value['minimumdepthinmeters'];
                if(occurrenceData.value['maximumdepthinmeters']){
                    returnStr += '-' + occurrenceData.value['maximumdepthinmeters'];
                }
                returnStr += ' m (';
                returnStr += (Number(occurrenceData.value['minimumdepthinmeters']) * 3.28).toString();
                if(occurrenceData.value['maximumdepthinmeters']){
                    returnStr += '-' + (Number(occurrenceData.value['maximumdepthinmeters']) * 3.28).toString();
                }
                returnStr += ' ft)';
            }
            return returnStr;
        });
        const occurrenceElevationStr = Vue.computed(() => {
            let returnStr = '';
            if(occurrenceData.value.hasOwnProperty('occid') && occurrenceData.value['minimumelevationinmeters']){
                returnStr += occurrenceData.value['minimumelevationinmeters'];
                if(occurrenceData.value['maximumelevationinmeters']){
                    returnStr += '-' + occurrenceData.value['maximumelevationinmeters'];
                }
                returnStr += ' m (';
                returnStr += (Number(occurrenceData.value['minimumelevationinmeters']) * 3.28).toString();
                if(occurrenceData.value['maximumelevationinmeters']){
                    returnStr += '-' + (Number(occurrenceData.value['maximumelevationinmeters']) * 3.28).toString();
                }
                returnStr += ' ft)';
            }
            return returnStr;
        });
        const occurrenceLocalityStr = Vue.computed(() => {
            let returnStr = '';
            if(occurrenceData.value.hasOwnProperty('occid')){
                const partArr = [];
                if(occurrenceData.value['country']){
                    partArr.push(occurrenceData.value['country']);
                }
                if(occurrenceData.value['stateprovince']){
                    partArr.push(occurrenceData.value['stateprovince']);
                }
                if(occurrenceData.value['county']){
                    partArr.push(occurrenceData.value['county']);
                }
                if(occurrenceData.value['municipality']){
                    partArr.push(occurrenceData.value['municipality']);
                }
                if(occurrenceData.value['waterbody']){
                    partArr.push(occurrenceData.value['waterbody']);
                }
                if(occurrenceData.value['island']){
                    partArr.push(occurrenceData.value['island']);
                }
                if(occurrenceData.value['islandgroup']){
                    partArr.push(occurrenceData.value['islandgroup']);
                }
                if(partArr.length > 0){
                    returnStr += partArr.join(', ');
                }
                if(occurrenceData.value['locality']){
                    returnStr += (returnStr ? '; ' : '') + occurrenceData.value['locality'];
                }
            }
            return returnStr;
        });
        const occurrenceMofData = Vue.ref({});
        const occurrenceMofDataExists = Vue.computed(() => {
            let exist = false;
            Object.keys(occurrenceMofDataFields.value).forEach(key => {
                if(occurrenceMofData.value.hasOwnProperty(key) && occurrenceMofData.value[key]){
                    exist = true;
                }
            });
            return exist;
        });
        const occurrenceMofDataFields = Vue.ref({});
        const occurrenceMofDataLabel = Vue.ref(null);
        const selectedTab = Vue.ref('details');
        const tabCardStyle = Vue.ref('');
        const tabPanelStyle = Vue.ref('');

        Vue.watch(contentContainerRef, () => {
            if(contentContainerRef.value){
                setTabPanelHeights();
            }
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function setChecklistArr() {
            const formData = new FormData();
            formData.append('occid', props.occurrenceId.toString());
            formData.append('action', 'getChecklistListByOccurrenceVoucher');
            fetch(checklistVoucherApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                checklistArr.value = data;
            });
        }

        function setCollectionData() {
            const formData = new FormData();
            formData.append('collid', occurrenceData.value['collid'].toString());
            formData.append('action', 'getCollectionInfoArr');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((resObj) => {
                    collectionData.value = Object.assign({}, resObj);
                    if(collectionData.value['configuredData']){
                        if(collectionData.value['configuredData'].hasOwnProperty('eventMofExtension')){
                            if(Object.keys(collectionData.value['configuredData']['eventMofExtension']['dataFields']).length > 0){
                                eventMofDataFields.value = collectionData.value['configuredData']['eventMofExtension']['dataFields'];
                                if(collectionData.value['configuredData']['eventMofExtension'].hasOwnProperty('dataLabel') && collectionData.value['configuredData']['eventMofExtension']['dataLabel']){
                                    eventMofDataLabel.value = collectionData.value['configuredData']['eventMofExtension']['dataLabel'].toString();
                                }
                                if(Number(occurrenceData.value['eventid']) > 0){
                                    setEventMofData();
                                }
                            }
                        }
                        if(collectionData.value['configuredData'].hasOwnProperty('occurrenceMofExtension')){
                            if(Object.keys(collectionData.value['configuredData']['occurrenceMofExtension']['dataFields']).length > 0){
                                occurrenceMofDataFields.value = collectionData.value['configuredData']['occurrenceMofExtension']['dataFields'];
                                if(collectionData.value['configuredData']['occurrenceMofExtension'].hasOwnProperty('dataLabel') && collectionData.value['configuredData']['occurrenceMofExtension']['dataLabel']){
                                    occurrenceMofDataLabel.value = collectionData.value['configuredData']['occurrenceMofExtension']['dataLabel'].toString();
                                }
                                setOccurrenceMofData();
                            }
                        }
                    }
                });
            });
        }

        function setCollectionPermissions() {
            const formData = new FormData();
            formData.append('permissionJson', JSON.stringify(["CollAdmin", "CollEditor"]));
            formData.append('key', occurrenceData.value['collid'].toString());
            formData.append('action', 'validatePermission');
            fetch(permissionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((resData) => {
                    collectionPermissions.value = resData;
                });
            });
        }

        function setDeterminationArr() {
            const formData = new FormData();
            formData.append('occid', props.occurrenceId.toString());
            formData.append('action', 'getOccurrenceDeterminationArr');
            fetch(occurrenceDeterminationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                determinationArr.value = data;
            });
        }

        function setEventMofData() {
            const formData = new FormData();
            formData.append('type', 'event');
            formData.append('id', occurrenceData.value['eventid'].toString());
            formData.append('action', 'getMofDataByTypeAndId');
            fetch(occurrenceMeasurementOrFactApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                Object.keys(eventMofDataFields.value).forEach(field => {
                    eventMofData.value[field] = (data && data.hasOwnProperty(field)) ? data[field] : null;
                });
            });
        }

        function setGeneticLinkArr() {
            const formData = new FormData();
            formData.append('occid', props.occurrenceId.toString());
            formData.append('action', 'getOccurrenceGeneticLinkArr');
            fetch(occurrenceGeneticLinkApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                geneticLinkArr.value = data;
            });
        }

        function setImageArr() {
            const formData = new FormData();
            formData.append('property', 'occid');
            formData.append('value', props.occurrenceId.toString());
            formData.append('action', 'getImageArrByProperty');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                imageArr.value = data;
            });
        }

        function setMediaArr() {
            const formData = new FormData();
            formData.append('property', 'occid');
            formData.append('value', props.occurrenceId.toString());
            formData.append('action', 'getMediaArrByProperty');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                mediaArr.value = data;
            });
        }

        function setOccurrenceData() {
            const formData = new FormData();
            formData.append('occid', props.occurrenceId.toString());
            formData.append('action', 'getOccurrenceDataArr');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('occid') && Number(data.occid) > 0){
                    occurrenceData.value = Object.assign({}, data);
                    setCollectionData();
                    setCollectionPermissions();
                    setDeterminationArr();
                    setImageArr();
                    setMediaArr();
                    setChecklistArr();
                    setGeneticLinkArr();
                }
            });
        }

        function setOccurrenceMofData() {
            const formData = new FormData();
            formData.append('type', 'occurrence');
            formData.append('id', occurrenceData.value['occid'].toString());
            formData.append('action', 'getMofDataByTypeAndId');
            fetch(occurrenceMeasurementOrFactApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                Object.keys(occurrenceMofDataFields.value).forEach(field => {
                    occurrenceMofData.value[field] = (data && data.hasOwnProperty(field)) ? data[field] : null;
                });
            });
        }

        function setTabPanelHeights() {
            if(contentContainerRef.value){
                const clientHeight = contentContainerRef.value.clientHeight;
                tabCardStyle.value = 'height: ' + clientHeight + 'px;';
                tabPanelStyle.value = 'height: ' + (clientHeight - 82) + 'px;';
            }
        }

        Vue.onMounted(() => {
            window.addEventListener('resize', setTabPanelHeights);
            if(Number(props.occurrenceId) > 0){
                setOccurrenceData();
            }
        });

        return {
            checklistArr,
            clientRoot,
            collectionData,
            collectionNameStr,
            contentContainerRef,
            coordinateArr,
            determinationArr,
            eventMofData,
            eventMofDataExists,
            eventMofDataFields,
            eventMofDataLabel,
            geneticLinkArr,
            imageArr,
            isEditor,
            mediaArr,
            occurrenceCoordinateStr,
            occurrenceData,
            occurrenceDepthStr,
            occurrenceElevationStr,
            occurrenceLocalityStr,
            occurrenceMofData,
            occurrenceMofDataExists,
            occurrenceMofDataFields,
            occurrenceMofDataLabel,
            selectedTab,
            tabCardStyle,
            tabPanelStyle,
            closePopup
        }
    }
};
