<?php
include_once(__DIR__ . '/config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
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
    </head>
    <body>
        <?php
        include(__DIR__ . '/header.php');
        ?>
        <div id="mainContainer">
            <div class="q-pa-md">
                <div class="q-mb-md row q-col-gutter-md">
                    <div class="col-12 col-sm-6 col-md-7">
                        <div style="padding: 0 15px;margin-top:15px;">
                            The genus <i>Lomatium</i> is the largest of 18 genera of Apiaceae from primarily Western North America that
                            form a large clade of over 200 species.  This website is the result of an NSF funded project to create a
                            phylogenetic hypothesis and a new classification for this previously poorly understood group.  This electronic
                            monograph contains descriptions, synonymy, protologues, distribution maps, specimen records, and photographs
                            for all taxa in this group.  Phylogenetic trees and GenBank records of DNA accessions used in our study are
                            also included.
                        </div>
                        <div style="margin-top:15px;padding: 0 10px;">
                            The 18 genera covered in this treatment are:
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Aletes" ><i>Aletes</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Cymopterus" ><i>Cymopterus</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Eurytaenia" ><i>Eurytaenia</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Harbouria" ><i>Harbouria</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Lomatium" ><i>Lomatium</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Musineon" ><i>Musineon</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Neoparrya" ><i>Neoparrya</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Oreonana" ><i>Oreonana</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Oreoxis" ><i>Oreoxis</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Podistera" ><i>Podistera</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Polytaenia" ><i>Polytaenia</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Pseudocymopterus" ><i>Pseudocymopterus</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Shoshonea" ><i>Shoshonea</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Taenidia" ><i>Taenidia</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Tauschia" ><i>Tauschia</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Thaspium" ><i>Thaspium</i></a>,
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Vesper" ><i>Vesper</i></a>, and
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php?taxon=Zizia" ><i>Zizia</i></a>.
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-5 row justify-center">
                        <organism-of-the-day checklist-id="1" title="Plant of the Day" type="plant"></organism-of-the-day>
                    </div>
                </div>
                <div style="margin-top:25px;display:flex;justify-content:center;">
                    <div>
                        <taxa-quick-search default-taxon-type="scientific"></taxa-quick-search>
                    </div>
                </div>
            </div>
        </div>
        <div style="clear:both;"></div>
        <div class="footer">
            <p style="text-align:center;">© <a href="http://herbarium.wisc.edu/" title="Wisconsin State Herbarium home">Wisconsin State Herbarium, UW-Madison</a>
                <br>
                Department of Botany <span><span>•<span style="display:inline-block"></span></span></span> 430 Lincoln Drive
                <span><span>•<span style="display:inline-block"></span></span></span>
                Madison, WI 53706 • Phone: 608-262-2792
                <br>Direct comments to&nbsp;<a href="#"></a><a href="mailto:wisconsin.state.herbarium@gmail.com">wisconsin.state.herbarium@gmail.com</a>
            </p>
        </div>
        <?php
        include_once(__DIR__ . '/config/footer-includes.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa-vernacular.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/project.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist-taxa.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageCarousel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/games/organismOfTheDay.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const homePageModule = Vue.createApp({
                components: {
                    'organism-of-the-day': organismOfTheDay,
                    'taxa-quick-search': taxaQuickSearch
                }
            });
            homePageModule.use(Quasar, { config: {} });
            homePageModule.use(Pinia.createPinia());
            homePageModule.mount('#mainContainer');
        </script>
    </body>
</html>
