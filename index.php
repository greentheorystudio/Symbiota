<?php
include_once(__DIR__ . '/config/symbini.php');
header('Content-Type: text/html; charset=' .$CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title><?php echo $DEFAULT_TITLE; ?> Home</title>
    <link href="css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
    <link href="css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
    <link href="css/jquery-ui.css" type="text/css" rel="Stylesheet" />
    <style>
        #cycler, #cycler2{position:relative;height:180px;}
        #cycler img, #cycler2 img{position:absolute;z-index:1;background-color:#ffffff;}
        #cycler img.active, #cycler2 img.active{z-index:3}
        .bolditalic {
            font-weight: bold;
            font-style: italic;
        }
    </style>
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/jquery-ui.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/config/googleanalytics.php'); ?>
    <script>
        function cycleImages(){
            var $active = $('#cycler .active');
            var $next = ($active.next().length > 0) ? $active.next() : $('#cycler img:first');
            $next.css('z-index',2);//move the next image up the pile
            $active.fadeOut(1000,function(){//fade out the top image
                $active.css('z-index',1).show().removeClass('active');//reset the z-index and unhide the image
                $next.css('z-index',3).addClass('active');//make the next image the top one
            });

            var $active = $('#cycler2 .active');
            var $next = ($active.next().length > 0) ? $active.next() : $('#cycler2 img:first');
            $next.css('z-index',2);//move the next image up the pile
            $active.fadeOut(1000,function(){//fade out the top image
                $active.css('z-index',1).show().removeClass('active');//reset the z-index and unhide the image
                $next.css('z-index',3).addClass('active');//make the next image the top one
            });
        }

        $(document).ready(function(){
            setInterval('cycleImages()', 3000);
        })
    </script>
</head>
<body>
<?php
include(__DIR__ . '/header.php');
?>
<div  id="innertext">
    <div style="margin-top:15px;padding: 0 10px;">
        Sweeping along 156 miles of Florida’s eastern coast, the Indian River Lagoon (IRL) is home to a wealth of habitats
        and spectacular biodiversity. Its seagrass beds, mangroves, oyster reefs, salt marshes, tidal flats, scrubland,
        beaches and dunes nurture more than 3,500 species of plants, animals and other organisms.
    </div>
    <div style="margin-top:15px;padding: 0 10px;">
        The IRL’s rich biodiversity is largely due to its unique geographic location at the transition between cool, temperate
        and warm, subtropical climate zones. Designated as an “estuary of national significance” by the U.S. Environmental
        Protection Agency, the IRL also provides enormous human benefits, supporting thousands of jobs and generating hundreds
        of millions of dollars in income and revenue annually.
    </div>
    <div style="margin-top:15px;padding: 0 10px;">
        With a dynamic and growing taxonomic species database, ecological and life history information, and extensive documentation
        of the IRL’s many habitats, the Indian River Lagoon Species Inventory portal is intended to be a multi-purpose tool
        to enhance scientific knowledge of the IRL ecosystem; support sound policy-making by decisionmakers and natural resources
        managers; and advance public awareness of the need for steadfast stewardship of the lagoon’s health.
    </div>
    <div style="margin-top:15px;padding: 0 10px;">
        As you explore the portal and its resources, please reach out with comments, questions and concerns to irlwebmaster@si.edu
    </div>
    <div class="searcharea">
        <div class="searchtop">
            <?php
            $searchText = '';
            $buttonText = 'Search';
            $placeholderText = 'Scientific Name';
            include_once(__DIR__ . '/classes/PluginsManager.php');
            $pluginManager = new PluginsManager();
            $pluginManager->setQuickSearchShowSelector(true);
            $pluginManager->setQuickSearchDefaultSetting('common');
            $quicksearch = $pluginManager->createQuickSearch($buttonText,$searchText);
            echo $quicksearch;
            ?>
            <div class="as"> <a href="<?php echo $CLIENT_ROOT; ?>/taxa/dynamictaxalist.php"> Advanced Search</a></div>
        </div>
    </div>
</div>

<?php
include(__DIR__ . '/footer.php');
?>
</body>
</html>
