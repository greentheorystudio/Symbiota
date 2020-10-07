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
            <div style="padding: 0 15px;">
                The genus <i>Lomatium</i> is the largest of 19 genera of Apiaceae from primarily Western North America that
                form a large clade of over 200 species.  This website is the result of an NSF funded project to create a
                phylogenetic hypothesis and a new classification for this previously poorly understood group.  This electronic
                monograph contains descriptions, synonymy, protologues, distribution maps, specimen records, and photographs
                for all taxa in this group.  Phylogenetic trees showing the evolutionary relationships within the group are
                also included.
            </div>
            <div style="margin-top:15px;padding: 0 10px;">
                The 19 genera covered in this treatment are: <i>Aletes</i>, <i>Cymopterus</i>, <i>Eurytaenia</i>, <i>Harbouria</i>,
                <i>Lomatium</i>, <i>Musineon</i>, <i>Neoparrya</i>, <i>Oreonana</i>, <i>Oreoxis</i>, <i>Podistera</i>, <i>Polytaenia</i>,
                <i>Pseudocymopterus</i>, <i>Pteryxia</i>, <i>Shoshonea</i>, <i>Taenidia</i>, <i>Tauschia</i>, <i>Thaspium</i>,
                <i>Vesper</i>, and <i>Zizia</i>.
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
