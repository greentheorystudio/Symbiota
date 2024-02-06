<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileEditButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileScinameHeader.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileNotFound.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonNotes.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonFamily.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonVernaculars.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonSynonyms.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileCentralmage.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileDescriptionTabs.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonMap.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonImageLink.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonOccurrenceLink.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileImagePanel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileSubtaxaPanel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileMediaPanel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileImageCarousel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/custom/taxaProfileTaxonNativeStatus.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script type="text/javascript">
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
                            <taxa-profile-central-image :taxon="taxon" :central-image="centralImage" :is-editor="isEditor" @update:set-image-carousel="showImageCarousel"></taxa-profile-central-image>
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
        setup() {
            const { hideWorking, showWorking } = useCore();
            const store = useBaseStore();
            const centralImage = Vue.ref(null);
            const clientRoot = store.getClientRoot;
            const clValue = CL_VAL;
            const descriptionArr = Vue.ref([]);
            const glossaryArr = Vue.ref([]);
            const imageCarousel = Vue.ref(false);
            const imageCarouselSlide = Vue.ref(null);
            const imageExpansionLabel = Vue.ref('');
            const isEditor = IS_EDITOR;
            const fuzzyMatches = Vue.ref([]);
            const loading = Vue.ref(true);
            const subtaxaArr = Vue.ref([]);
            const subtaxaExpansionLabel = Vue.ref('');
            const subtaxaLabel = Vue.ref('');
            const styleClass = Vue.ref(null);
            const taxon = Vue.ref(null);
            const taxonValue = TAXON_VAL;

            function processDescriptions(descArr) {
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
                descriptionArr.value = descArr;
            }

            function processImages() {
                taxon.value['images'].forEach((image) => {
                    if(Number(image['occid']) > 0){
                        image['anchorUrl'] = clientRoot + '/collections/individual/index.php?occid=' + image['occid'];
                    }
                    else{
                        image['anchorUrl'] = clientRoot + '/imagelib/imgdetails.php?imgid=' + image['id'];
                    }
                });
                centralImage.value = taxon.value['images'].length > 0 ? taxon.value['images'][0] : null;
                if(Number(taxon.value['imageCnt']) > 100){
                    imageExpansionLabel.value = 'View First 100 Images';
                }
                else{
                    imageExpansionLabel.value = 'View All ' + taxon.value['images'].length + ' Images';
                }
                loading.value = false;
                hideWorking();
            }

            function processSubtaxa() {
                if(taxon.value['clName']){
                    subtaxaLabel.value = 'Subtaxa within ' + taxon.value['clName'];
                }
                else{
                    subtaxaLabel.value = 'Subtaxa';
                }
                subtaxaExpansionLabel.value = 'View All ' + subtaxaLabel.value;
                for(let i in taxon.value['sppArr']){
                    if(taxon.value['sppArr'].hasOwnProperty(i)){
                        const subTaxon = taxon.value['sppArr'][i];
                        subtaxaArr.value.push(subTaxon);
                    }
                }
            }

            function setGlossary() {
                const formData = new FormData();
                formData.append('tid', taxon.value['tid']);
                formData.append('action', 'getTaxonGlossary');
                fetch(glossaryApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.json().then((resObj) => {
                            glossaryArr.value = resObj;
                        });
                    }
                });
            }

            function setStyleClass() {
                if(Number(taxon.value['rankId']) > 180){
                    styleClass.value = 'species';
                }
                else if(Number(taxon.value['rankId']) === 180){
                    styleClass.value = 'genus';
                }
                else{
                    styleClass.value = 'higher';
                }
            }

            function setTaxon() {
                const formData = new FormData();
                formData.append('taxonStr', taxonValue);
                formData.append('clid', clValue);
                formData.append('action', 'setTaxon');
                fetch(taxaProfileApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.json().then((resObj) => {
                            if(resObj.hasOwnProperty('submittedTid')){
                                taxon.value = resObj;
                                setStyleClass();
                                setTaxonDescriptions();
                                setGlossary();
                                processSubtaxa();
                                setTaxonMedia();
                            }
                            else if(taxonValue.value !== ''){
                                const formData = new FormData();
                                formData.append('sciname', taxonValue.value);
                                formData.append('lev', '2');
                                formData.append('action', 'getSciNameFuzzyMatches');
                                fetch(taxonomyApiUrl, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then((response) => {
                                    if(response.status === 200){
                                        response.json().then((matches) => {
                                            fuzzyMatches.value = matches;
                                        });
                                    }
                                });
                            }
                            else{
                                window.location.href = clientRoot + '/index.php';
                            }
                        });
                    }
                });
            }

            function setTaxonDescriptions() {
                const formData = new FormData();
                formData.append('tid', taxon.value['tid']);
                formData.append('action', 'getTaxonDescriptions');
                fetch(taxonomyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.json().then((resObj) => {
                            processDescriptions(resObj);
                        });
                    }
                });
            }

            function setTaxonMedia() {
                const formData = new FormData();
                formData.append('tid', taxon.value['tid']);
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
                            taxon.value['images'] = resObj['images'];
                            taxon.value['media'] = resObj['media'];
                            processImages();
                        });
                    }
                });
            }

            function showImageCarousel(index) {
                imageCarouselSlide.value = index;
                imageCarousel.value = true;
            }

            function toggleImageCarousel(val) {
                imageCarousel.value = val;
            }

            function updateImageCarousel(val) {
                imageCarouselSlide.value = val;
            }

            Vue.onMounted(() => {
                showWorking();
                setTaxon();
            });

            return {
                centralImage,
                descriptionArr,
                glossaryArr,
                imageCarousel,
                imageCarouselSlide,
                imageExpansionLabel,
                isEditor,
                fuzzyMatches,
                loading,
                subtaxaArr,
                subtaxaExpansionLabel,
                subtaxaLabel,
                styleClass,
                taxon,
                taxonValue,
                showImageCarousel,
                toggleImageCarousel,
                updateImageCarousel
            }
        }
    });
    taxonProfilePage.use(Quasar, { config: {} });
    taxonProfilePage.use(Pinia.createPinia());
    taxonProfilePage.mount('#inner-table');
</script>
