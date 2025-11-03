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
    <link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <style>
        .text-underline {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
    <?php
    include(__DIR__ . '/header.php');
    ?>
    <div id="mainContainer">
        <div class="q-pa-md">
            <h1>Welcome to the Online Virtual Flora of Wisconsin</h1>
            <div class="q-my-md row q-col-gutter-md">
                <div class="col-12 col-sm-6 col-md-7">
                    <p>
                        This site is a collaborative effort between the herbaria of the UW-Madison (WIS) and the UW-Steven's Point (UWSP),
                        along with most of the other herbaria located in the state of Wisconsin. It contains information on each of
                        the more than 2600 vascular plant species that occurs in Wisconsin, including photos, distribution maps, specimen
                        records, and more.
                    </p>
                    <div>
                        <taxa-quick-search default-taxon-type="scientific"></taxa-quick-search>
                    </div>
                    <ul>
                        <li><span class="text-bold">Enter a genus, species, or common name to view the species description pages.</span></li>
                        <li>View detailed species descriptions, photos, interactive maps, and links to specimen records and additional information.</li>
                    </ul>
                    <p class="text-bold">Advanced Searches</p>
                    <ul>
                        <li>
                            See <span class="text-bold">Advanced Searches</span> tab above to <span class="text-bold">Search for Specimen Records</span> and to <span class="text-bold">Browse the Image Library</span>. <br />
                        </li>
                        <li>
                            Search, view, and download nearly 400,000 in-state herbarium specimen records and thousands of images.<br>
                        </li>
                    </ul>
                    <p>
                        <span class="text-bold">Checklists</span> (e.g., County Floras, Wildflowers by Color) are under development.  Take a look or create your own!<br />
                    </p>
                    <p class="text-italic">NOTE: 'Interactive Maps' will plot only collections with known GPS localities.</p>
                </div>
                <div class="col-12 col-sm-6 col-md-5 row justify-center">
                    <organism-of-the-day checklist-id="19" title="Plant of the Day" type="plant"></organism-of-the-day>
                </div>
            </div>
            <q-card class="update-card q-mb-md bg-green-1">
                <q-card-section>
                    <div class="text-h5 text-bold q-mb-sm">We’ve been making some changes!</div>
                    <div>
                        You may have noticed some changes on the Online Flora of Wisconsin website lately!
                    </div>
                    <div>
                        Here are some of the changes we have made and/or are working on:
                        <ul>
                            <li>New layouts for the Taxon Profile Pages.</li>
                            <li>Changes to the way images are displayed.</li>
                            <li>Updates to the spatial module with new map layers and functionality.</li>
                            <li>Updating the photos with new higher resolution images.</li>
                            <li class="text-bold">Adding dichotomous keys from the ongoing work on the <span class="text-underline">Flora of Wisconsin</span>.
                                <br /><span class="text-red">(See the “Keys” tab above for a list of families and genera with completed keys.)</span>
                            </li>
                            <li>Updating the county level maps.</li>
                        </ul>
                    </div>
                    <div>
                        This is all currently a work in progress, and we welcome your comments and constructive criticisms. Check
                        out the site and let us know what you like, don’t like, or would like to see in the future! Also, we would
                        like to add more photographs of the plants of Wisconsin to the website and replace some of the old low-resolution
                        images. If you have photographs that would like to donate to the website, please let us know. Contact me at
                        the email at the bottom of the page. Mary Ann Feist, Curator, Wisconsin State Herbarium.
                    </div>
                </q-card-section>
            </q-card>
        </div>
    </div>
    <?php
    include(__DIR__ . '/footer.php');
    include_once(__DIR__ . '/config/footer-includes.php');
    ?>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa-vernacular.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/project.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist-taxa.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageCarousel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/games/organismOfTheDay.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script>
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
