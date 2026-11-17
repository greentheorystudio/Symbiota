<?php
include_once(__DIR__ . '/config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );

$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$model = (array_key_exists('model', $_REQUEST) && $_REQUEST['model']) ? preg_replace('/[^A-Za-z0-9]/', '', (string)$_REQUEST['model']) : '';
$term = (array_key_exists('term', $_REQUEST) && $_REQUEST['term']) ? preg_replace('/[^A-Za-z0-9]/', '', (string)$_REQUEST['term']) : '';

$availableModels = array('mof', 'occurrence');
if($model && !in_array($model, $availableModels, true)){
    $model = '';
    $term = '';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Field Index</title>
        <meta name="description" content="<?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal field index">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <script type="text/javascript">
            const COLLID = <?php echo $collid; ?>;
            const MODEL = '<?php echo $model; ?>';
            const TERM = '<?php echo $term; ?>';
        </script>
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
        <div id="mainContainer" class="fullscreen">

        </div>
        <?php
        include_once(__DIR__ . '/config/footer-includes.php');
        ?>
        <script type="text/javascript">
            const fieldIndexModule = Vue.createApp({
                setup() {
                    const baseStore = useBaseStore();
                    const collectionStore = useCollectionStore();

                    const clientRoot = baseStore.getClientRoot;
                    const collId = COLLID;
                    const model = MODEL;
                    const term = TERM;

                    Vue.onMounted(() => {
                        if(Number(collId) > 0){
                            collectionStore.setCollection(collId, () => {
                                if(!isEditor.value){
                                    window.location.href = clientRoot + '/index.php';
                                }
                            });
                        }
                        else if(model !== ''){
                            setModelFieldData();
                        }
                        else{
                            window.location.href = clientRoot + '/index.php';
                        }
                    });

                    return {

                    }
                }
            });
            fieldIndexModule.use(Quasar, { config: {} });
            fieldIndexModule.use(Pinia.createPinia());
            fieldIndexModule.mount('#mainContainer');
        </script>
    </body>
</html>
