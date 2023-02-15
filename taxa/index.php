<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/TaxonProfileManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$taxonValue = array_key_exists('taxon',$_REQUEST)?htmlspecialchars($_REQUEST['taxon']): '';
$clValue = array_key_exists('cl',$_REQUEST)?(int)$_REQUEST['cl']:0;

if(!$taxonValue && array_key_exists('quicksearchtaxon',$_REQUEST)){
    $taxonValue = htmlspecialchars($_REQUEST['quicksearchtaxon']);
}

$isEditor = false;
if($GLOBALS['SYMB_UID']){
    if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
        $isEditor = true;
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxon Profile</title>
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../css/speciesprofilebase.css?ver=20221204" rel="stylesheet" type="text/css" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css" />
    <style>
        #innertable{
            width: 90%;
            margin: 15px auto;
        }
        .profile-row{
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .profile-column{
            display: flex;
            flex-direction: column;
        }
        #sciname{
            font-weight: bold;
            font-size: 1.2rem;
        }
        #sciname.species{
            color: #990000;
            font-style: italic;
        }
        #sciname.genus{
            font-style: italic;
        }
        .redirectedfrom{
            font-size: 90%;
            margin-left: 25px;
        }
        .leftcolumn{
            width: 35%;
        }
        .rightcolumn{
            width: 60%;
        }
        #centralimage{
            text-align: center;
            margin: 15px;
        }
        #centralimage img{
            width: 325px;
            border: 2px solid black;
        }
        #centralimage a:hover img{
            border: 2px solid #026B6E;
        }
        #centralimage div.photographer{
            text-align: right;
            margin: 2px 20px 2px 2px;
            font-size: 75%;
        }
        #nocentralimage{
            width: 260px;
            height: 260px;
            border-style: solid;
            margin-top: 5px;
            margin-left: 20px;
            text-align: center;
        }
        .desctabs{
            height: 450px;
        }
        .desctabpanels{
            height: 350px;
        }
        #descsource{
            text-align:	right;
            width: 400px;
            padding: 10px 20px 10px 10px;
        }
        #nodesc{
            margin: 20px;
        }
        .desc-statement-heading{
            font-weight: bold;
        }
    </style>
    <script src="../js/external/all.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../js/external/jquery.js"></script>
    <script type="text/javascript" src="../js/external/jquery-ui.js"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
</head>
<body>
    <?php
    include(__DIR__ . '/../header.php');
    ?>
    <div id="innertable">
        <template v-if="!loading">
            <template v-if="taxon">
                <template v-if="taxon.rankId > 180">
                    <div class="profile-row">
                        <div class="leftcolumn profile-column">
                            <taxa-profile-sciname-header :taxon="taxon" :style-class="styleClass" :parent-link="parentLink"></taxa-profile-sciname-header>
                            <taxa-profile-taxon-family :taxon="taxon"></taxa-profile-taxon-family>
                            <taxa-profile-taxon-notes :taxon="taxon"></taxa-profile-taxon-notes>
                            <taxa-profile-taxon-vernaculars :vernaculars="taxon.vernaculars"></taxa-profile-taxon-vernaculars>
                            <taxa-profile-taxon-synonyms :synonyms="taxon.synonyms"></taxa-profile-taxon-synonyms>
                        </div>
                        <template v-if="isEditor">
                            <taxa-profile-edit-button :edit-link="editLink"></taxa-profile-edit-button>
                        </template>
                    </div>
                    <div class="profile-row">
                        <div class="leftcolumn profile-column">
                            <taxa-profile-central-image :central-image="centralImage" :is-editor="isEditor" :edit-link="editLink"></taxa-profile-central-image>
                        </div>
                        <div class="rightcolumn profile-column">
                            <taxa-profile-description-tabs :description-arr="descriptionArr"></taxa-profile-description-tabs>
                        </div>
                    </div>
                    <div class="profile-row">

                    </div>
                </template>
                <template v-else>
                    <div class="profile-row">
                        <div class="leftcolumn profile-column">
                            <taxa-profile-sciname-header :taxon="taxon" :style-class="styleClass" :parent-link="parentLink"></taxa-profile-sciname-header>
                            <taxa-profile-taxon-family :taxon="taxon"></taxa-profile-taxon-family>
                            <taxa-profile-taxon-notes :taxon="taxon"></taxa-profile-taxon-notes>
                            <taxa-profile-taxon-vernaculars :vernaculars="taxon.vernaculars"></taxa-profile-taxon-vernaculars>
                            <taxa-profile-taxon-synonyms :synonyms="taxon.synonyms"></taxa-profile-taxon-synonyms>
                        </div>
                        <template v-if="isEditor">
                            <taxa-profile-edit-button :edit-link="editLink"></taxa-profile-edit-button>
                        </template>
                    </div>
                    <div class="profile-row">
                        <div class="leftcolumn profile-column">
                            <taxa-profile-central-image :central-image="centralImage" :is-editor="isEditor" :edit-link="editLink"></taxa-profile-central-image>
                        </div>
                        <div class="rightcolumn profile-column">
                            <taxa-profile-description-tabs :description-arr="descriptionArr"></taxa-profile-description-tabs>
                        </div>
                    </div>
                    <div class="profile-row">

                    </div>
                </template>
            </template>
            <template v-else>
                <taxa-profile-not-found :taxon-value="taxonValue" :fuzzy-matches="fuzzyMatches"></taxa-profile-not-found>
            </template>
        </template>
    </div>
    <?php
    include(__DIR__ . '/../footer.php');
    include_once(__DIR__ . '/../config/footer-includes.php');
    ?>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileEditButton.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileScinameHeader.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileNotFound.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonNotes.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonFamily.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonVernaculars.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileTaxonSynonyms.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileCentralmage.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaProfileDescriptionTabs.js" type="text/javascript"></script>
    <script>
        const taxonVal = Vue.ref('<?php echo $taxonValue; ?>');
        const clVal = Vue.ref(<?php echo $clValue; ?>);
        const isEditor = Vue.ref(<?php echo ($isEditor?'true':'false'); ?>);

        const taxonProfilePage = Vue.createApp({
            data() {
                return {
                    centralImage: Vue.ref(null),
                    clValue: clVal,
                    descriptionArr: Vue.ref([]),
                    editLink: Vue.ref(null),
                    isEditor: isEditor,
                    fuzzyMatches: Vue.ref([]),
                    loading: Vue.ref(true),
                    parentLink: Vue.ref(null),
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
                'taxa-profile-description-tabs': taxaProfileDescriptionTabs
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
                        if(image['sciname'] !== this.taxon['sciName']){
                            image['caption'] += ' (linked from ' + image['sciname'] + ')';
                        }
                    });
                    this.centralImage = this.taxon['images'][0];
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
                },
            }
        });
        taxonProfilePage.use(Quasar, { config: {} });
        taxonProfilePage.mount('#innertable');
    </script>
</body>
</html>
