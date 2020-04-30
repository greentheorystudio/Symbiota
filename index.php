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
    </style>
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">
        <?php include_once('config/googleanalytics.php'); ?>
    </script>
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
    <div style="float:right;width:380px;">
        <div style="clear:both;float:right;width:320px;margin-top:8px;margin-right:8px;padding:5px;-moz-border-radius:5px;-webkit-border-radius:5px;border:1px solid black;" >
            <div style="float:left;width:350px;">
                <?php
                $searchText = 'Taxon Search';
                $buttonText = 'Search';
                include_once(__DIR__ . '/classes/PluginsManager.php');
                $pluginManager = new PluginsManager();
                $quicksearch = $pluginManager->createQuickSearch($buttonText,$searchText);
                echo $quicksearch;
                ?>
            </div>
        </div>
    </div>
    <div style="float:left;width:200px;">
        <div style="width:100%;display:flex;justify-content:center;">
            <img src="images/layout/IRL_GRAPHIC.jpg" style="width:175px;height:399px;" />
        </div>
    </div>
    <h1 style="margin-bottom:0;">Welcome to the Indian River Lagoon Species Inventory</h1>
    <div style="display:flex;justify-content:center;flex-wrap:wrap;margin-top:8px;margin-bottom:8px;">
        <img src="images/layout/oliveline2.jpg" style="width:90%;height:15px;" />
    </div>
    <div style="padding: 0 15px;">
        The Indian River Lagoon (IRL) Species Inventory is an online database that provides comprehensive information on all
        aspects of IRL biodiversity. It is utilized by a wide variety of audiences including scientists, resource managers,
        educational groups, and IRL citizenry. The website is continually updated as new discoveries and information become
        available. We invite you to increase your knowledge and appreciation of this invaluable natural resource.
    </div>
    <div style="display:flex;justify-content:center;flex-wrap:wrap;margin-top:8px;margin-bottom:8px;">
        <img src="images/layout/oliveline2.jpg" style="width:90%;height:15px;" />
    </div>
    <div style="display:flex;justify-content:center;flex-wrap:wrap;margin-top:8px;margin-bottom:8px;" id="cycler">
        <img src="images/layout/homepage_slides/New_Pic1.gif" style="width:700px;height:178px;" class="active" />
        <img src="images/layout/homepage_slides/New_Pic2.gif" style="width:700px;height:178px;" />
        <img src="images/layout/homepage_slides/New_Pic3.gif" style="width:700px;height:178px;" />
        <img src="images/layout/homepage_slides/New_Pic4.gif" style="width:700px;height:178px;" />
        <img src="images/layout/homepage_slides/New_Pic5.gif" style="width:700px;height:178px;" />
        <img src="images/layout/homepage_slides/New_Pic6.gif" style="width:700px;height:178px;" />
    </div>
    <div style="display:flex;justify-content:center;flex-wrap:wrap;margin-top:8px;margin-bottom:8px;">
        <img src="images/layout/oliveline2.jpg" style="width:90%;height:15px;" />
    </div>
    <div style="margin-top:15px;padding: 0 10px;">
        <h3 style="margin-bottom:8px;">Discover:</h3>
        <ul>
            <li>over 3,500 species that live in one of the most biologically diverse estuaries in the continental United States</li>
            <li>commercially and recreationally important species</li>
            <li>Special Status species</li>
            <li>invasive species</li>
            <li>why taxonomy is important</li>
            <li>special adaptations of marine invertebrates</li>
            <li>what's new on the website</li>
        </ul>
        <h3 style="margin-bottom:8px;">Learn About:</h3>
        <ul>
            <li>what makes the IRL so unique</li>
            <li>climate change and other threats to the IRL</li>
            <li>scientific research and discovery on IRL organisms and habitats</li>
        </ul>
        <h3 style="margin-bottom:8px;">Explore:</h3>
        <ul>
            <li>IRL's many habitats including seagrass beds, mangroves, oyster reefs, salt marshes, tidal flats, scrub, beaches and dunes</li>
        </ul>
        <h3 style="margin-bottom:8px;">Find Out:</h3>
        <ul>
            <li>how you can make a difference in IRL water quality</li>
        </ul>
        <h3 style="margin-bottom:8px;">Check Out:</h3>
        <ul>
            <li>the IRL Photo Gallery</li>
            <li>the IRL Species Image Collection</li>
        </ul>
    </div>
    <div style="display:flex;justify-content:center;flex-wrap:wrap;margin-top:8px;margin-bottom:8px;">
        <img src="images/layout/oliveline2.jpg" style="width:90%;height:15px;" />
    </div>
    <div style="display:flex;justify-content:center;flex-wrap:wrap;margin-top:8px;margin-bottom:8px;" id="cycler2">
        <img src="images/layout/homepage_slides/New_Pic7.gif" style="width:700px;height:178px;" class="active" />
        <img src="images/layout/homepage_slides/New_Pic8.gif" style="width:700px;height:178px;" />
        <img src="images/layout/homepage_slides/New_Pic9.gif" style="width:700px;height:178px;" />
        <img src="images/layout/homepage_slides/New_Pic10.gif" style="width:700px;height:178px;" />
        <img src="images/layout/homepage_slides/New_Pic11.gif" style="width:700px;height:178px;" />
        <img src="images/layout/homepage_slides/New_Pic12.gif" style="width:700px;height:178px;" />
    </div>
    <div style="display:flex;justify-content:center;flex-wrap:wrap;margin-top:8px;margin-bottom:8px;">
        <img src="images/layout/oliveline2.jpg" style="width:90%;height:15px;" />
    </div>
    <div style="margin-top:15px;padding: 0 10px;">
        <div style="display:flex;justify-content:space-evenly;flex-wrap:wrap;align-items:center;margin-top:8px;margin-bottom:8px;">
            <img src="images/layout/eol_logo.jpg" style="width:150px;height:95px;" alt="EOL Logo" />
            <p style="width:300px;">Did you know that the IRL Species Inventory contributes species information to the Encyclopedia of Life? Visit <a href="http://eol.org">EOL</a> to find out more.</p>
            <img src="images/layout/IRLPlate3.gif" style="width:190px;height:156px;" alt="Plate" />
        </div>
    </div>
    <div style="display:flex;justify-content:center;flex-wrap:wrap;margin-top:8px;margin-bottom:8px;">
        <img src="images/layout/oliveline2.jpg" style="width:90%;height:15px;" />
    </div>
    <div style="display:flex;justify-content:center;flex-wrap:wrap;margin-top:15px;padding: 0 10px;">
        Submit additional information, photos or comments to: <br />
        <a href="mailto:irl_webmaster@si.edu">irl_webmaster@si.edu </a>
    </div>
    <div style="display:flex;justify-content:center;flex-wrap:wrap;margin-top:8px;margin-bottom:8px;">
        <img src="images/layout/oliveline2.jpg" style="width:90%;height:15px;" />
    </div>
</div>

<?php
include(__DIR__ . '/footer.php');
?>
</body>
</html>
