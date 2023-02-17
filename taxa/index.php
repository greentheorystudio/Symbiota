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
    <div id="inner-table"></div>
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
