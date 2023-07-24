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
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonOccurrenceLink.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileImagePanel.js?ver=20230719" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileSubtaxaPanel.js?ver=20230718" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileMediaPanel.js?ver=20230718" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileImageCarousel.js?ver=20230719" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/custom/taxaProfileTaxonNativeStatus.js" type="text/javascript"></script>
<script>
    const taxonProfilePage = Vue.createApp({
        template: `
            <template v-if="!loading">
                <template v-if="taxon">
                    <div class="profile-split-row">
                        <div class="left-column profile-column">
                            <taxa-profile-sciname-header :taxon="taxon" :style-class="styleClass" :parent-link="parentLink"></taxa-profile-sciname-header>
                            <taxa-profile-taxon-family :taxon="taxon"></taxa-profile-taxon-family>
                            <taxa-profile-taxon-native-status :taxon="taxon"></taxa-profile-taxon-native-status>
                            <taxa-profile-taxon-notes :taxon="taxon"></taxa-profile-taxon-notes>
                            <taxa-profile-taxon-vernaculars :vernaculars="taxon.vernaculars"></taxa-profile-taxon-vernaculars>
                            <taxa-profile-taxon-synonyms :synonyms="taxon.synonyms"></taxa-profile-taxon-synonyms>
                        </div>
                        <template v-if="isEditor">
                            <taxa-profile-edit-button :edit-link="editLink"></taxa-profile-edit-button>
                        </template>
                    </div>
                    <div class="profile-split-row">
                        <div class="left-column profile-column">
                            <taxa-profile-central-image :taxon="taxon" :central-image="centralImage" :is-editor="isEditor" :edit-link="editLink"></taxa-profile-central-image>
                        </div>
                        <div class="right-column profile-column">
                            <taxa-profile-description-tabs :description-arr="descriptionArr" :glossary-arr="glossaryArr"></taxa-profile-description-tabs>
                            <div class="right-inner-row">
                                <taxa-profile-taxon-map :taxon="taxon"></taxa-profile-taxon-map>
                            </div>
                            <div class="right-inner-row">
                                <taxa-profile-taxon-image-link :taxon="taxon"></taxa-profile-taxon-image-link>
                            </div>
                        </div>
                    </div>
                    <div class="profile-center-row">
                        <taxa-profile-image-panel :taxon="taxon" :image-expansion-label="imageExpansionLabel" @update:set-image-carousel="showImageCarousel"></taxa-profile-image-panel>
                    </div>
                    <div class="profile-center-row">
                        <taxa-profile-media-panel :taxon="taxon"></taxa-profile-media-panel>
                    </div>
                    <div class="profile-center-row">
                        <taxa-profile-subtaxa-panel :subtaxa-arr="subtaxaArr" :subtaxa-label="subtaxaLabel" :subtaxa-expansion-label="subtaxaExpansionLabel" :is-editor="isEditor"></taxa-profile-subtaxa-panel>
                    </div>
                </template>
                <template v-else>
                    <taxa-profile-not-found :taxon-value="taxonValue" :fuzzy-matches="fuzzyMatches"></taxa-profile-not-found>
                </template>
                <q-dialog v-model="imageCarousel" persistent full-width full-height>
                    <taxa-profile-image-carousel :image-arr="this.taxon.images" :image-index="imageCarouselSlide" @update:show-image-carousel="toggleImageCarousel" @update:current-image="updateImageCarousel"></taxa-profile-image-carousel>
                </q-dialog>
            </template>
        `,
        data() {
            return {
                audioArr: Vue.ref({}),
                centralImage: Vue.ref(null),
                clValue: clVal,
                descriptionArr: Vue.ref([]),
                editLink: Vue.ref(null),
                glossaryArr: Vue.ref([]),
                imageCarousel: Vue.ref(false),
                imageCarouselSlide: Vue.ref(null),
                imageExpansionLabel: Vue.ref(''),
                isEditor: isEditor,
                fuzzyMatches: Vue.ref([]),
                loading: Vue.ref(true),
                parentLink: Vue.ref(null),
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
            'taxa-profile-image-panel': taxaProfileImagePanel,
            'taxa-profile-subtaxa-panel': taxaProfileSubtaxaPanel,
            'taxa-profile-media-panel': taxaProfileMediaPanel,
            'taxa-profile-image-carousel': taxaProfileImageCarousel,
            'taxa-profile-taxon-native-status': taxaProfileTaxonNativeStatus
        },
        mounted() {
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
                    }
                    else{
                        image['anchorUrl'] = CLIENT_ROOT + '/imagelib/imgdetails.php?imgid=' + image['id'];
                    }
                });
                this.centralImage = this.taxon['images'].shift();
                if(Number(this.taxon['imageCnt']) > 100){
                    this.imageExpansionLabel = 'View First 100 Images';
                }
                else{
                    this.imageExpansionLabel = 'View All ' + this.taxon['images'].length + ' Images';
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
                                this.setTaxonMedia();
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
            setTaxonMedia(){
                const formData = new FormData();
                formData.append('tid', this.taxon['tid']);
                formData.append('limit', '100');
                formData.append('includeav', '1');
                formData.append('action', 'getTaxonMedia');
                fetch(taxaProfileApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.json().then((resObj) => {
                            this.taxon['images'] = resObj['images'];
                            this.taxon['media'] = resObj['media'];
                            this.processImages();
                        });
                    }
                });
            },
            showImageCarousel(index){
                this.imageCarouselSlide = index;
                this.imageCarousel = true;
            },
            toggleImageCarousel(val){
                this.imageCarousel = val;
            },
            updateImageCarousel(val){
                this.imageCarouselSlide = val;
            }
        }
    });
    taxonProfilePage.use(Quasar, { config: {} });
    taxonProfilePage.mount('#inner-table');
</script>
