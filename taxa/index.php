<?php
/** @var array $topRowElements */
/** @var array $leftColumnElements */
/** @var array $rightColumnElements */
/** @var array $bottomRowElements */
/** @var array $footerRowElements */
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/TaxonProfileManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$taxonValue = array_key_exists('taxon',$_REQUEST)?$_REQUEST['taxon']: '';
$taxAuthId = array_key_exists('taxauthid',$_REQUEST)?(int)$_REQUEST['taxauthid']:1;
$clValue = array_key_exists('cl',$_REQUEST)?(int)$_REQUEST['cl']:0;
$projValue = array_key_exists('proj',$_REQUEST)?(int)$_REQUEST['proj']:0;
$lang = array_key_exists('lang',$_REQUEST)?$_REQUEST['lang']:$GLOBALS['DEFAULT_LANG'];
$descrDisplayLevel = array_key_exists('displaylevel',$_REQUEST)?$_REQUEST['displaylevel']: '';
$showAllImages = array_key_exists('allimages',$_REQUEST);

if(!$taxonValue && array_key_exists('quicksearchtaxon',$_REQUEST)){
    $taxonValue = htmlspecialchars($_REQUEST['quicksearchtaxon']);
}

$taxonManager = new TaxonProfileManager();
if($taxAuthId || $taxAuthId === 0) {
    $taxonManager->setTaxAuthId($taxAuthId);
}
if($clValue) {
    $taxonManager->setClName($clValue);
}
if($projValue) {
    $taxonManager->setProj($projValue);
}
if($lang) {
    $taxonManager->setLanguage($lang);
}
if($taxonValue) {
    $taxonManager->setTaxon($taxonValue);
    $taxonManager->setAttributes();
}
$ambiguous = $taxonManager->getAmbSyn();
$acceptedName = $taxonManager->getAcceptance();
$synonymArr = $taxonManager->getSynonymArr();
$spDisplay = $taxonManager->getDisplayName();
$taxonRank = (int)$taxonManager->getRankId();
$links = $taxonManager->getTaxaLinks();
$vernStr = $taxonManager->getVernacularStr();
$vernArr = $taxonManager->getVernacularArr();
$synStr = $taxonManager->getSynonymStr();
if($links){
    foreach($links as $linkKey => $linkUrl){
        if($linkUrl['title'] === 'REDIRECT'){
            $locUrl = str_replace('--SCINAME--',rawurlencode($taxonManager->getSciName()),$linkUrl['url']);
            header('Location: '.$locUrl);
            exit;
        }
    }
}

$styleClass = '';
if($taxonRank > 180) {
    $styleClass = 'species';
}
elseif($taxonRank === 180) {
    $styleClass = 'genus';
}
else {
    $styleClass = 'higher';
}

$displayLocality = 0;
$isEditor = false;
if($GLOBALS['SYMB_UID']){
    if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
        $isEditor = true;
    }
    if($GLOBALS['IS_ADMIN'] || array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppAdmin',$GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppReadAll',$GLOBALS['USER_RIGHTS'])){
        $displayLocality = 1;
    }
}
if((int)$taxonManager->getSecurityStatus() === 0){
    $displayLocality = 1;
}
$taxonManager->setDisplayLocality($displayLocality);
$descr = array();

if(file_exists('includes/config/taxaProfileTemplateCustom.php')){
    include(__DIR__ . '/includes/config/taxaProfileTemplateCustom.php');
}
else{
    include(__DIR__ . '/includes/config/taxaProfileTemplateDefault.php');
}
?>

<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']. ' - ' .$spDisplay; ?></title>
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/speciesprofilebase.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <script src="../js/all.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/jquery-ui.js"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
    <script type="text/javascript">
        let currentLevel = <?php echo ($descrDisplayLevel?: '1'); ?>;
        const levelArr = [<?php echo ($descr?"'".implode("','",array_keys($descr))."'":''); ?>];
        const allImages = <?php echo ($showAllImages?'true':'false'); ?>;
        let tid = <?php echo $taxonManager->getTid(); ?>;
    </script>
    <script src="../js/symb/taxa.index.js?ver=20210512" type="text/javascript"></script>
    <?php
    if(isset($CSSARR)){
        foreach($CSSARR as $cssVal){
            echo '<link href="includes/config/'.$cssVal.'?ver=150106" type="text/css" rel="stylesheet" id="editorCssLink" />';
        }
    }
    if(isset($JSARR)){
        foreach($JSARR as $jsVal){
            echo '<script src="includes/config/'.$jsVal.'?ver=150106" type="text/javascript"></script>';
        }
    }
    ?>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertable">
    <div id="toprow">
        <?php
        foreach($topRowElements as $e){
            echo $e;
        }
        ?>
    </div>

    <div id="middlerow">
        <div id="leftcolumn" class="<?php echo $styleClass; ?>">
            <?php
            foreach($leftColumnElements as $e){
                echo $e;
            }
            ?>
        </div>


        <div id="rightcolumn" class="<?php echo $styleClass; ?>">
            <?php
            foreach($rightColumnElements as $e){
                echo $e;
            }
            ?>
        </div>
    </div>

    <div id="bottomrow">
        <?php
        foreach($bottomRowElements as $e){
            echo $e;
        }
        ?>
    </div>

    <div id="footerrow">
        <?php
        foreach($footerRowElements as $e){
            echo $e;
        }
        ?>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
