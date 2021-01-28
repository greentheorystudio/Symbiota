<?php
include_once(__DIR__ . '/config/symbini.php');
header('Content-Type: text/html; charset=' .$CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
    <head>
        <title><?php echo $DEFAULT_TITLE; ?> Home</title>
        <link href="css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
        <link href="css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
        <meta name='keywords' content='' />
        <script type="text/javascript">
            <?php include_once('config/googleanalytics.php'); ?>
        </script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/header.php');
        ?>
        <div  id="innertext">
            <div style="float:right;width:380px;">
                <?php
                $oodID = 1;
                $ootdGameChecklist = 1;
                $ootdGameTitle = 'Plant of the Day ';
                $ootdGameType = 'plant';

                include_once(__DIR__ . '/classes/GamesManager.php');
                $gameManager = new GamesManager();
                $gameInfo = $gameManager->setOOTD($oodID,$ootdGameChecklist);
                ?>
                <div style="float:right;margin-top:15px;margin-right:10px;margin-bottom:15px;width:350px;text-align:center;">
                    <div style="font-size:130%;font-weight:bold;">
                        <?php echo $ootdGameTitle; ?>
                    </div>
                    <a href="<?php echo $CLIENT_ROOT; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
                        <img src="<?php echo $CLIENT_ROOT; ?>/temp/ootd/<?php echo $oodID; ?>_organism300_1.jpg" style="width:350px;border:0;" />
                    </a><br/>
                    <b>What is this <?php echo $ootdGameType; ?>?</b><br/>
                    <a href="<?php echo $CLIENT_ROOT; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
                        Click here to test your knowledge
                    </a>
                </div>
            </div>
            <div style="padding: 0 15px;margin-top:15px;">
                The genus <i>Lomatium</i> is the largest of 18 genera of Apiaceae from primarily Western North America that
                form a large clade of over 200 species.  This website is the result of an NSF funded project to create a
                phylogenetic hypothesis and a new classification for this previously poorly understood group.  This electronic
                monograph contains descriptions, synonymy, protologues, distribution maps, specimen records, and photographs
                for all taxa in this group.  Phylogenetic trees showing the evolutionary relationships within the group are
                also included.
            </div>
            <div style="margin-top:15px;padding: 0 10px;">
                The 19 genera covered in this treatment are:
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Aletes" ><i>Aletes</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Cymopterus" ><i>Cymopterus</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Eurytaenia" ><i>Eurytaenia</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Harbouria" ><i>Harbouria</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Lomatium" ><i>Lomatium</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Musineon" ><i>Musineon</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Neoparrya" ><i>Neoparrya</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Oreonana" ><i>Oreonana</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Oreoxis" ><i>Oreoxis</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Podistera" ><i>Podistera</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Polytaenia" ><i>Polytaenia</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Pseudocymopterus" ><i>Pseudocymopterus</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Shoshonea" ><i>Shoshonea</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Taenidia" ><i>Taenidia</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Tauschia" ><i>Tauschia</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Thaspium" ><i>Thaspium</i></a>,
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Vesper" ><i>Vesper</i></a>, and
                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/index.php?taxon=Zizia" ><i>Zizia</i></a>.
            </div>
            <div style="margin: 25px auto 0;width:475px;">
                <div style="clear:both;padding:5px;" >
                    <div>
                        <?php
                        $searchText = '';
                        $buttonText = 'Search';
                        include_once(__DIR__ . '/classes/PluginsManager.php');
                        $pluginManager = new PluginsManager();
                        $pluginManager->setQuickSearchShowSelector(true);
                        $quicksearch = $pluginManager->createQuickSearch($buttonText,$searchText);
                        echo $quicksearch;
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
        include(__DIR__ . '/footer.php');
        ?>
    </body>
</html>
