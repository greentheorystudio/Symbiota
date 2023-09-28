<?php
include_once(__DIR__ . '/config/symbbase.php');
include_once(__DIR__ . '/classes/TaxonQuickSearchManager.php');
header('Content-Type: text/html; charset=UTF-8' );

$taxon = array_key_exists('taxon',$_REQUEST)?trim($_REQUEST["taxon"]):'';

$imgLibManager = new TaxonQuickSearchManager();
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
    <?php include_once('config/googleanalytics.php'); ?>
</head>
<body>
    <?php
    include(__DIR__ . '/header.php');
    ?>
    <div id="innertext">
        <h1>Welcome to the Online Virtual Flora of Wisconsin</h1>
        <div style="float:right;width:380px;">
            <?php
            $oodID = 1;
            $ootdGameChecklist = 19;
            $ootdGameTitle = "Plant of the Day ";
            $ootdGameType = "plant";
            include_once(__DIR__ . '/classes/GamesManager.php');
            $gameManager = new GamesManager();
            $gameInfo = $gameManager->setOOTD($oodID,$ootdGameChecklist);
            ?>
            <div style="float:right;margin-top:30px;margin-right:30px;margin-bottom:15px;width:250px;text-align:center;">
                <div style="font-size:130%;font-weight:bold;">
                    <?php echo $ootdGameTitle; ?>
                </div>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
                    <img src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/temp/ootd/<?php echo $oodID; ?>_organism300_1.jpg" style="width:250px;border:0;" />
                </a><br/>
                <b>What is this <?php echo $ootdGameType; ?>?</b><br/>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
                    Click here to test your knowledge
                </a>
            </div>
        </div>

        <div style="margin: 20px; text-align: left;">
            <p>
                This site is a collaborative effort between the herbaria of the UW-Madison (WIS) and the UW-Steven's Point (UWSP),
                along with most of the other herbaria located in the state of Wisconsin. It contains information on each of
                the more than 2600 vascular plant species that occurs in Wisconsin, including photos, distribution maps, specimen
                records, and more.
            </p>
            <form name='searchform1' action='index.php' method='post'>
                <fieldset style="width:410px;">
                    <legend><b>Quick Search</b></legend>
                    <div style="clear:both;">
                        <input type='text' name='taxon' style="width:300px;" title='Enter family, genus, or scientific name'>
                        <input name='submit' value='Search' type='submit'>
                    </div>
                </fieldset>
            </form>
            <?php
            $taxaList = Array();
            if($taxon){
                echo "<div style='margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;'>Select a species to access available images.</div>";
                $taxaList = $imgLibManager->getSpeciesListWVernaculars($taxon);
                if($taxaList){
                    foreach($taxaList as $key => $value){
                        echo "<div style='margin-left:30px;font-style:italic;'>";
                        echo "<a href='taxa/index.php?taxon=".$key."' target='_blank'>".$value."</a>";
                        echo "</div>";
                    }
                    echo "<div style='margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;'></div>";
                }
            }
            ?>
            <ul>
                <li><b>Enter a genus, species, or common name to view the species description pages.</b></li>
                <li>View detailed species descriptions, photos, interactive maps, and links to specimen records and additional information.</li></ul>
            <p><strong>Advanced Searches</strong></p>
            <ul>
                <li>See <strong>Advanced Searches</strong> tab above to <strong>Search for Specimen Records</strong> and to <strong>Browse the Image Library</strong>. <br>
                </li>
                <li>Search, view, and download nearly 400,000 in-state herbarium specimen records and thousands of images.<br>
                </li>
            </ul>
            <p>
                <strong>Checklists</strong> (e.g., County Floras, Wildflowers by Color) are under development.  Take a look or create your own!<br>
            </p>
            <p><em>NOTE: 'Interactive Maps' will plot only collections with known GPS localities.</em></p>
            <div style="width: 100%; clear:both;"></div>
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
                            <li>Adding dichotomous keys from the ongoing work on the <span class="text-underline">Flora of Wisconsin.</span></li>
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
    <script>
        const homePageModule = Vue.createApp({});
        homePageModule.use(Quasar, { config: {} });
        homePageModule.mount('#innertext');
    </script>
</body>
</html>
