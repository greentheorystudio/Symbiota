<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileEditButton.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileScinameHeader.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileNotFound.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonNotes.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonFamily.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonVernaculars.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonSynonyms.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileCentralmage.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileDescriptionTabs.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonMap.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonImageLink.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileImagePanel.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileSubtaxaPanel.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileMediaPanel.js?ver=20230313" type="text/javascript"></script>
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
                        <taxa-profile-image-panel :taxon="taxon" :image-expansion-label="imageExpansionLabel"></taxa-profile-image-panel>
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
                    image['taxonUrl'] = CLIENT_ROOT + '/taxa/index.php?taxon=' + image['tid'];
                });
                this.centralImage = this.taxon['images'].shift();
                if(Number(this.taxon['imageCnt']) > 100){
                    this.imageExpansionLabel = 'View First 100 Images';
                }
                else{
                    this.imageExpansionLabel = 'View All ' + this.taxon['imageCnt'] + ' Images';
                }
            },
            processMedia(){
                this.taxon['media'].forEach((media) => {
                    media['taxonUrl'] = CLIENT_ROOT + '/taxa/index.php?taxon=' + media['tid'];
                });
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
                        subTaxon['taxaurl'] = CLIENT_ROOT + '/taxa/index.php?taxon=' + subTaxon['tid'] + '&cl=' + (this.taxon.hasOwnProperty('clid')?this.taxon['clid']:'');
                        subTaxon['editurl'] = CLIENT_ROOT + '/taxa/profile/tpeditor.php?tid=' + subTaxon['tid'];
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
            setLinks(){
                this.editLink = CLIENT_ROOT + '/taxa/profile/tpeditor.php?tid=' + this.taxon['tid'];
                this.parentLink = CLIENT_ROOT + '/taxa/index.php?taxon=' + this.taxon['parentTid'] + '&cl=' + (this.taxon.hasOwnProperty('clid')?this.taxon['clid']:'');
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
                            this.loading = false;
                            if(resObj.hasOwnProperty('submittedTid')){
                                this.taxon = resObj;
                                this.setLinks();
                                this.setStyleClass();
                                this.processImages();
                                this.setTaxonDescriptions();
                                this.setGlossary();
                                this.processSubtaxa();
                                this.processMedia();
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
                                            matches.forEach((match) => {
                                                match['url'] = CLIENT_ROOT + '/taxa/index.php?taxon=' + match['tid'];
                                            });
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
            }
        }
    });
    taxonProfilePage.use(Quasar, { config: {} });
    taxonProfilePage.mount('#inner-table');
</script>