<?php
include_once(__DIR__ . '/config/symbini.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Home</title>
        <link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
        <link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
        <link type="text/css" href="css/jquery-ui.css" rel="stylesheet" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <meta name='keywords' content='' />
        <script type="text/javascript">
            <?php include_once('config/googleanalytics.php'); ?>
        </script>

        <script type="text/javascript">
            $(document).ready(function() {
                slideShow();
            });

            var imgArray = [
                "url(images/layout/Image01.JPG)",
                "url(images/layout/Image02.JPG)",
                "url(images/layout/Image03.jpg)",
                "url(images/layout/Image05.JPG)",
                "url(images/layout/Image07.jpg)",
                "url(images/layout/Image08.jpg)",
                "url(images/layout/Image09.jpg)",
                "url(images/layout/Image10.jpg)",
                "url(images/layout/Image11.jpg)",
                "url(images/layout/Image12.jpg)"];
            var photographerArray = [
                "Denise Knapp",
                "Denise Knapp",
                "",
                "",
                "Morgan Ball",
                "Morgan Ball",
                "Morgan Ball",
                "Morgan Ball",
                "Morgan Ball",
                "Morgan Ball"];
            var curIndex = 0;
            var imgDuration = 4000;

            function slideShow() {
                setTimeout(function() {
                    document.getElementById('bannerDiv').style.backgroundImage = imgArray[curIndex];
                    if(photographerArray[curIndex] !== ""){
                        document.getElementById('imageCredit').innerHTML = '<div style="background-color:white;opacity:60%;color:black;padding:5px;font-size: 12px;">(photographer: ' + photographerArray[curIndex] + ')</div>';
                    }
                    else{
                        document.getElementById('imageCredit').innerHTML = '';
                    }
                },1000);
                curIndex++;
                if(curIndex === imgArray.length) {
                    curIndex = 0;
                }
                setTimeout(slideShow, imgDuration);
            }
        </script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/header.php');
        ?>
        <div  id="innertext">
            <h1>Welcome to the California Islands Biodiversity Information System</h1>

            <div style="">
                The Channel Islands of California are world-renowned for their scenic beauty and rich natural
                resources, including a large proportion of plants and animals found nowhere else on earth.
                The islands off the Pacific coast of the Mexican state of Baja California, immediately to the south,
                are likewise renowned for their beauty and valued for their remarkable flora and fauna, including many
                species that they share with the Channel Islands plus others found nowhere else.  This archipelago of
                over twenty islands and islets, the Islands of the Californias, spans two nations and its individual
                islands are managed by a variety of agencies and organizations with differing missions. Nonetheless,
                all of these groups seek to protect the islands’ biota, and carry out many of the same land management
                activities including invasive species detection, eradication and control, rare plant recovery and
                reintroduction, and habitat restoration. With these different institutions all working to protect unique and
                valuable island biota from multiple threats, information sharing among island land and water managers and
                researchers has never been more critical.
            </div>

            <div style="margin-top:10px; margin-bottom: 10px;">
                We launched the California Islands Biodiversity Information System, or Cal-IBIS, to facilitate archipelago-wide
                data sharing, with the ultimate goals of protecting the archipelago’s biota and enhancing scientific
                understanding of it by better informing conservation management and research. This Symbiota Cal-IBIS portal is
                the first and central node of what will ultimately be a larger information system on multiple platforms. It
                includes information on the distribution of animals (from birds, to beetles to snails), plants, and other organisms,
                including macrofungi and lichens. In future nodes of the information system, rare and invasive species’ distributions
                and management history will tracked, habitat restoration projects will be documented, and more. This Symbiota portal
                compiles information on biological specimens and observations of island species from multiple sources, providing
                island stakeholders with a single portal that allows them to track and analyze occurrences, distributions, and
                changes for plants, animals and other organisms across the entire archipelago. Onward island conservation!
            </div>
        </div>

        <?php
        include(__DIR__ . '/footer.php');
        ?>
    </body>
</html>
