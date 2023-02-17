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
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <script src="../js/external/all.min.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
    <script type="text/javascript">
        const taxonVal = Vue.ref('<?php echo $taxonValue; ?>');
        const clVal = Vue.ref(<?php echo $clValue; ?>);
        const isEditor = Vue.ref(<?php echo ($isEditor?'true':'false'); ?>);
    </script>
</head>
<body>
    <?php
    include(__DIR__ . '/../header.php');
    ?>
    <div id="inner-table">
        <template v-if="!loading">
            <template v-if="taxon">
                <div class="profile-split-row">
                    <div class="left-column profile-column">
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
    </div>
    <?php
    include(__DIR__ . '/../footer.php');
    include_once(__DIR__ . '/../config/footer-includes.php');
    if(file_exists(__DIR__ . '/profile-custom.php')){
        include_once(__DIR__ . '/profile-custom.php');
    }
    else{
        include_once(__DIR__ . '/profile-default.php');
    }
    ?>
</body>
</html>
