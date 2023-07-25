<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileEditButton.js?ver=20230715" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileScinameHeader.js?ver=20230715" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileNotFound.js?ver=20230715" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonNotes.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonFamily.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonVernaculars.js?ver=20230630" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonSynonyms.js?ver=20230630" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileCentralmage.js?ver=20230715" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileDescriptionTabs.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonMap.js?ver=20230718" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonImageLink.js?ver=20230715" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonOccurrenceLink.js?ver=20230720" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileSubtaxaPanel.js?ver=20230718" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileMediaPanel.js?ver=20230718" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileImageCarousel.js?ver=20230720" type="text/javascript"></script>
<script>
    const taxonProfilePage = Vue.createApp({
        template: `
            <template v-if="loading">
                <div class="fill-viewport"></div>
            </template>
            <template v-if="!loading">
                <template v-if="taxon">
                    <div class="profile-split-row">
                        <div class="profile-column">
                            <taxa-profile-sciname-header :taxon="taxon" :style-class="styleClass"></taxa-profile-sciname-header>
                            <taxa-profile-taxon-family :taxon="taxon"></taxa-profile-taxon-family>
                            <taxa-profile-taxon-notes :taxon="taxon"></taxa-profile-taxon-notes>
                            <taxa-profile-taxon-vernaculars :vernaculars="taxon.vernaculars"></taxa-profile-taxon-vernaculars>
                            <taxa-profile-taxon-synonyms :synonyms="taxon.synonyms"></taxa-profile-taxon-synonyms>
                        </div>
                        <template v-if="isEditor">
                            <taxa-profile-edit-button :tid="taxon.tid"></taxa-profile-edit-button>
                        </template>
                    </div>
                    <div class="profile-split-row">
                        <div class="left-column profile-column">
                            <taxa-profile-central-image :taxon="taxon" :central-image="centralImage" :is-editor="isEditor"></taxa-profile-central-image>
                        </div>
                        <div class="right-column profile-column">
                            <taxa-profile-description-tabs :description-arr="descriptionArr" :glossary-arr="glossaryArr"></taxa-profile-description-tabs>
                        </div>
                    </div>
                    <div class="row justify-between items-center q-mt-md">
                        <div>
                            <taxa-profile-taxon-occurrence-link :taxon="taxon"></taxa-profile-taxon-occurrence-link>
                        </div>
                        <template v-if="taxon.imageCnt > 100">
                            <div>
                                <taxa-profile-taxon-image-link :taxon="taxon"></taxa-profile-taxon-image-link>
                            </div>
                        </template>
                        <div>
                            <taxa-profile-taxon-map :taxon="taxon"></taxa-profile-taxon-map>
                        </div>
                    </div>
                    <template v-if="taxon.rankId > 180 && fieldImageArr.length > 0">
                        <div class="profile-center-row">
                            <div class="expansion-container">
                                <q-card>
                                    <div class="q-pt-sm q-pl-md q-mb-md text-h6 text-weight-bold image-sideways-scroller-label">
                                        Field Images
                                    </div>
                                    <q-scroll-area class="q-px-md img-sideways-scroller">
                                        <div class="row no-wrap q-gutter-md">
                                            <div v-for="image in fieldImageArr" :key="image" class="img-thumb q-mb-sm">
                                                <a @click="showFieldImageCarousel(image.url);" class="cursor-pointer">
                                                    <q-img :src="image.url" fit="contain" height="240px" :title="image.caption" :alt="image.sciname"></q-img>
                                                </a>
                                                <div class="photographer">
                                                    <a :href="(clientRoot + '/taxa/index.php?taxon=' + image.tid)">
                                                        <span class="text-italic">{{ image.sciname }}</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </q-scroll-area>
                                </q-card>
                            </div>
                        </div>
                    </template>
                    <template v-if="taxon.rankId > 180 && specimenImageArr.length > 0">
                        <div class="profile-center-row">
                            <div class="expansion-container">
                                <q-card>
                                    <div class="q-pt-sm q-pl-md q-mb-md text-h6 text-weight-bold image-sideways-scroller-label">
                                        Specimen Images
                                    </div>
                                    <q-scroll-area class="q-px-md img-sideways-scroller">
                                        <div class="row no-wrap q-gutter-md">
                                            <div v-for="image in specimenImageArr" :key="image" class="img-thumb q-mb-sm">
                                                <a @click="showSpecimenImageCarousel(image.url);" class="cursor-pointer">
                                                    <q-img :src="image.url" fit="contain" height="240px" :title="image.caption" :alt="image.sciname"></q-img>
                                                </a>
                                                <div class="photographer">
                                                    <a :href="(clientRoot + '/taxa/index.php?taxon=' + image.tid)">
                                                        <span class="text-italic">{{ image.sciname }}</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </q-scroll-area>
                                </q-card>
                            </div>
                        </div>
                    </template>
                    <div class="profile-center-row">
                        <taxa-profile-subtaxa-panel :subtaxa-arr="subtaxaArr" :subtaxa-label="subtaxaLabel" :subtaxa-expansion-label="subtaxaExpansionLabel" :is-editor="isEditor"></taxa-profile-subtaxa-panel>
                    </div>
                </template>
                <template v-else>
                    <taxa-profile-not-found :taxon-value="taxonValue" :fuzzy-matches="fuzzyMatches"></taxa-profile-not-found>
                </template>
                <template v-if="taxon.rankId > 180">
                    <q-dialog v-model="fieldImageCarousel" persistent full-width full-height>
                        <taxa-profile-image-carousel :image-arr="fieldImageArr" :image-index="fieldImageCarouselSlide" @update:show-image-carousel="toggleFieldImageCarousel" @update:current-image="updateFieldImageCarousel"></taxa-profile-image-carousel>
                    </q-dialog>
                    <q-dialog v-model="specimenImageCarousel" persistent full-width full-height>
                        <taxa-profile-image-carousel :image-arr="specimenImageArr" :image-index="specimenImageCarouselSlide" @update:show-image-carousel="toggleSpecimenImageCarousel" @update:current-image="updateSpecimenImageCarousel"></taxa-profile-image-carousel>
                    </q-dialog>
                </template>
            </template>
        `,
        data() {
            return {
                audioArr: Vue.ref({}),
                centralImage: Vue.ref(null),
                clientRoot: Vue.ref(CLIENT_ROOT),
                clValue: clVal,
                descriptionArr: Vue.ref([]),
                fieldImageArr: Vue.ref([]),
                fieldImageCarousel: Vue.ref(false),
                fieldImageCarouselSlide: Vue.ref(null),
                glossaryArr: Vue.ref([]),
                isEditor: isEditor,
                fuzzyMatches: Vue.ref([]),
                loading: Vue.ref(true),
                specimenImageArr: Vue.ref([]),
                specimenImageCarousel: Vue.ref(false),
                specimenImageCarouselSlide: Vue.ref(null),
                subtaxaArr: Vue.ref([]),
                subtaxaExpansionLabel: Vue.ref(''),
                subtaxaLabel: Vue.ref(''),
                styleClass: Vue.ref(null),
                taxon: Vue.ref(null),
                taxonValue: taxonVal
            }
        },
        components: {
            'taxa-profile-edit-button': taxaProfileEditButton,
            'taxa-profile-sciname-header': taxaProfileScinameHeader,
            'taxa-profile-not-found': taxaProfileNotFound,
            'taxa-profile-taxon-notes': taxaProfileTaxonNotes,
            'taxa-profile-taxon-family': taxaProfileTaxonFamily,
            'taxa-profile-taxon-vernaculars': taxaProfileTaxonVernaculars,
            'taxa-profile-taxon-synonyms': taxaProfileTaxonSynonyms,
            'taxa-profile-central-image': taxaProfileCentralImage,
            'taxa-profile-description-tabs': taxaProfileDescriptionTabs,
            'taxa-profile-taxon-map': taxaProfileTaxonMap,
            'taxa-profile-taxon-image-link': taxaProfileTaxonImageLink,
            'taxa-profile-taxon-occurrence-link': taxaProfileTaxonOccurrenceLink,
            'taxa-profile-subtaxa-panel': taxaProfileSubtaxaPanel,
            'taxa-profile-media-panel': taxaProfileMediaPanel,
            'taxa-profile-image-carousel': taxaProfileImageCarousel
        },
        setup () {
            const $q = useQuasar();
            return {
                showLoading(){
                    $q.loading.show({
                        spinner: QSpinnerHourglass,
                        spinnerColor: 'primary',
                        spinnerSize: 140,
                        backgroundColor: 'grey',
                        message: 'Loading...',
                        messageColor: 'primary',
                        customClass: 'text-h4'
                    })
                },
                hideLoading(){
                    $q.loading.hide();
                }
            }
        },
        mounted() {
            this.showLoading();
            this.setTaxon();
        },
        methods: {
            processDescriptions(descArr){
                if(descArr.length > 0){
                    descArr.forEach((desc) => {
                        if((!desc['source'] || desc['source'] === '') && (desc['sourceurl'] && desc['sourceurl'] !== '')){
                            desc['source'] = desc['sourceurl'];
                        }
                        desc['stmts'].forEach((stmt) => {
                            if(stmt['statement'] && stmt['statement'] !== ''){
                                if(stmt['statement'].startsWith('<p>')){
                                    stmt['statement'] = stmt['statement'].slice(3);
                                }
                                if(stmt['statement'].endsWith('</p>')){
                                    stmt['statement'] = stmt['statement'].substring(0, stmt['statement'].length - 4);
                                }
                                if(Number(stmt['displayheader']) === 1 && stmt['heading'] && stmt['heading'] !== ''){
                                    const headingText = '<span class="desc-statement-heading">' + stmt['heading'] + '</span>: ';
                                    stmt['statement'] = headingText + stmt['statement'];
                                }
                            }
                        });
                    });
                }
                this.descriptionArr = descArr;
            },
            processImages(){
                this.taxon['images'].forEach((image) => {
                    if(Number(image['occid']) > 0){
                        image['anchorUrl'] = CLIENT_ROOT + '/collections/individual/index.php?occid=' + image['occid'];
                        if(image['basisofrecord'].includes('Observation')){
                            this.fieldImageArr.push(image);
                        }
                        else{
                            this.specimenImageArr.push(image);
                        }
                    }
                    else{
                        image['anchorUrl'] = CLIENT_ROOT + '/imagelib/imgdetails.php?imgid=' + image['id'];
                        this.fieldImageArr.push(image);
                    }
                });
                if(this.fieldImageArr.length > 0){
                    this.centralImage = this.fieldImageArr.shift();
                }
                else{
                    this.centralImage = this.specimenImageArr.shift();
                }
                this.loading = false;
                this.hideLoading();
            },
            processSubtaxa(){
                if(this.taxon['clName']){
                    this.subtaxaLabel = 'Subtaxa within ' + this.taxon['clName'];
                }
                else{
                    this.subtaxaLabel = 'Subtaxa';
                }
                this.subtaxaExpansionLabel = 'View All ' + this.subtaxaLabel;
                for(let i in this.taxon['sppArr']){
                    if(this.taxon['sppArr'].hasOwnProperty(i)){
                        const subTaxon = this.taxon['sppArr'][i];
                        this.subtaxaArr.push(subTaxon);
                    }
                }
            },
            setGlossary(){
                const formData = new FormData();
                formData.append('tid', this.taxon['tid']);
                formData.append('action', 'getTaxonGlossary');
                fetch(glossaryApiUrl, {
                    method: 'POST',
                    body: formData
                })
                    .then((response) => {
                        if(response.status === 200){
                            response.json().then((resObj) => {
                                this.glossaryArr = resObj;
                            });
                        }
                    });
            },
            setStyleClass(){
                if(Number(this.taxon['rankId']) > 180){
                    this.styleClass = 'species';
                }
                else if(Number(this.taxon['rankId']) === 180){
                    this.styleClass = 'genus';
                }
                else{
                    this.styleClass = 'higher';
                }
            },
            setTaxon(){
                const formData = new FormData();
                formData.append('taxonStr', this.taxonValue);
                formData.append('clid', this.clValue);
                formData.append('action', 'setTaxon');
                fetch(taxaProfileApiUrl, {
                    method: 'POST',
                    body: formData
                })
                    .then((response) => {
                        if(response.status === 200){
                            response.json().then((resObj) => {
                                if(resObj.hasOwnProperty('submittedTid')){
                                    this.taxon = resObj;
                                    this.setStyleClass();
                                    this.setTaxonDescriptions();
                                    this.setGlossary();
                                    this.processSubtaxa();
                                    if(Number(this.taxon['rankId']) > 180){
                                        this.setTaxonFieldImages(100);
                                    }
                                    else{
                                        this.setTaxonFieldImages(1);
                                    }
                                }
                                else if(this.taxonValue !== ''){
                                    const formData = new FormData();
                                    formData.append('sciname', this.taxonValue);
                                    formData.append('lev', '2');
                                    formData.append('action', 'getSciNameFuzzyMatches');
                                    fetch(taxonomyApiUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                        .then((response) => {
                                            if(response.status === 200){
                                                response.json().then((matches) => {
                                                    this.fuzzyMatches = matches;
                                                });
                                            }
                                        });
                                }
                                else{
                                    window.location.href = CLIENT_ROOT + '/index.php';
                                }
                            });
                        }
                    });
            },
            setTaxonDescriptions(){
                const formData = new FormData();
                formData.append('tid', this.taxon['tid']);
                formData.append('action', 'getTaxonDescriptions');
                fetch(taxonomyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                    .then((response) => {
                        if(response.status === 200){
                            response.json().then((resObj) => {
                                this.processDescriptions(resObj);
                            });
                        }
                    });
            },
            setTaxonFieldImages(limit){
                const formData = new FormData();
                formData.append('tid', this.taxon['tid']);
                formData.append('mediatypa', 'taxon');
                formData.append('limit', limit);
                formData.append('action', 'getTaxonMedia');
                fetch(taxaProfileApiUrl, {
                    method: 'POST',
                    body: formData
                })
                    .then((response) => {
                        if(response.status === 200){
                            response.json().then((resObj) => {
                                this.taxon['images'] = resObj['images'];
                                if(Number(this.taxon['rankId']) > 180){
                                    this.setTaxonSpecimenImages();
                                }
                                else{
                                    this.processImages();
                                }
                            });
                        }
                    });
            },
            setTaxonSpecimenImages(){
                const formData = new FormData();
                formData.append('tid', this.taxon['tid']);
                formData.append('mediatypa', 'occurrence');
                formData.append('limit', '100');
                formData.append('action', 'getTaxonMedia');
                fetch(taxaProfileApiUrl, {
                    method: 'POST',
                    body: formData
                })
                    .then((response) => {
                        if(response.status === 200){
                            response.json().then((resObj) => {
                                this.taxon['images'] = this.taxon['images'].concat(resObj['images']);;
                                this.processImages();
                            });
                        }
                    });
            },
            showFieldImageCarousel(index){
                this.fieldImageCarouselSlide = index;
                this.fieldImageCarousel = true;
            },
            showSpecimenImageCarousel(index){
                this.specimenImageCarouselSlide = index;
                this.specimenImageCarousel = true;
            },
            toggleFieldImageCarousel(val){
                this.fieldImageCarousel = val;
            },
            toggleSpecimenImageCarousel(val){
                this.specimenImageCarousel = val;
            },
            updateFieldImageCarousel(val){
                this.fieldImageCarouselSlide = val;
            },
            updateSpecimenImageCarousel(val){
                this.specimenImageCarouselSlide = val;
            }
        }
    });
    taxonProfilePage.use(Quasar, { config: {} });
    taxonProfilePage.mount('#inner-table');
</script>
