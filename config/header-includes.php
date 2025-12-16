<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/animate.min.css?ver=4.1.1" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/quasar.prod.css?ver=2.18.5" rel="stylesheet" type="text/css"/>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/vue.global.prod.js?ver=2023-11-30" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/vue-demi.js?ver=2023-11-30" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/pinia.js?ver=2.1.7" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
<?php
if(array_key_exists('GOOGLE_ANALYTICS_KEY', $GLOBALS) && $GLOBALS['GOOGLE_ANALYTICS_KEY']){
 echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $GLOBALS['GOOGLE_ANALYTICS_KEY'] . '"></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag("js", new Date()); gtag("config", "' . $GLOBALS['GOOGLE_ANALYTICS_KEY'] . '"); </script>';
}
include_once(__DIR__ . '/js-globals.php');
?>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/base.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/collection.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/image.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/media.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/occurrence-location.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/occurrence-collecting-event.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/occurrence-determination.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/occurrence-genetic-link.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/occurrence.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/search.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/spatial.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/hooks/core.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/shared.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>

<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaQuickSearch.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/tutorial/tutorialModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
