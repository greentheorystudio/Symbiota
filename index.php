<?php
include_once(__DIR__ . '/config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );

$editor = false;
if($GLOBALS['SYMB_UID']){
    if($GLOBALS['IS_ADMIN']){
        $editor = true;
    }
    elseif(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array(1, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
        $editor = true;
    }
    elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array(1, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
        $editor = true;
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Home</title>
        <meta name="description" content="Welcome to the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <meta name='keywords' content='' />
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer">Skip to main content</a>
        <?php
        include(__DIR__ . '/header.php');
        ?>
        <div id="mainContainer">
            <div class="q-pa-md">
                <?php
                if($editor){
                    ?>
                    <collection-catalog-number-quick-search collection-id="1"></collection-catalog-number-quick-search>
                    <?php
                }
                elseif(!$GLOBALS['SYMB_UID']){
                    ?>
                    <a href="profile/index.php">Please login</a>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
        include(__DIR__ . '/footer.php');
        include_once(__DIR__ . '/config/footer-includes.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/collectionCatalogNumberQuickSearch.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const homePageModule = Vue.createApp({
                components: {
                    'collection-catalog-number-quick-search': collectionCatalogNumberQuickSearch
                }
            });
            homePageModule.use(Quasar, { config: {} });
            homePageModule.use(Pinia.createPinia());
            homePageModule.mount('#mainContainer');
        </script>
    </body>
</html>
