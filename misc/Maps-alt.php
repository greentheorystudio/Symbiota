<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>The Indian River Lagoon Estuary</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .nav-item-container {
            width: 315px;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <h1>The Indian River Lagoon Estuary</h1>
    <p class="intro-text">
        Occupying more than 40 percent of Florida’s eastern coast, the 156-mile-long Indian River Lagoon (IRL) is part
        of the longest barrier island complex in the United States.
    </p>
    <p>
        The IRL’s seagrass beds, mangroves, oyster reefs, salt marshes, tidal flats, scrubland, beaches and dunes
        nurture more than 3,500 species of plants, animals and other organisms. This rich biodiversity is largely due
        to the lagoon’s unique geographic location, at the transition between cool, temperate and warm, subtropical
        climate zones.
    </p>
    <p>
        Designated as an “estuary of national significance” by the U.S. Environmental Protection Agency, the IRL also
        provides enormous human benefits, supporting thousands of jobs and generating $7.6 billion annually to Florida’s
        economy.
    </p>
    <div style="width:100%;margin-top:25px;">
        <hr style="border-top: 0.75pt solid #333f48;">
    </div>
    <div style="display:flex;justify-content: space-around;align-content: center;">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Whatsa_lagoon.php">
            <div class="nav-item-container">
                <div style='width:100%;height:225px;background-image: url("../images/layout/What_Is_a_Lagoon.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:100px;">
                    <div style="padding:20px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;margin-bottom:20px;">What is a Lagoon?</div>
                        <div style="font-family: 'Fira Sans';font-size: 10pt;font-style: italic;">
                            Between land and sea, lagoons are defined by flux.
                        </div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Whatsa_Habitat.php">
            <div class="nav-item-container">
                <div style='width:100%;height:225px;background-image: url("../images/layout/Dune_Westward_Canaveral_National_Seashore.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:100px;">
                    <div style="padding:20px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;margin-bottom:20px;">Habitats</div>
                        <div style="font-family: 'Fira Sans';font-size: 10pt;font-style: italic;">
                            The Indian River Lagoon is a mosaic of natural elements.
                        </div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Habitat_Threats.php">
            <div class="nav-item-container">
                <div style='width:100%;height:225px;background-image: url("../images/layout/algae_bloom.jpeg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:100px;">
                    <div style="padding:20px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;margin-bottom:20px;">Threats</div>
                        <div style="font-family: 'Fira Sans';font-size: 10pt;font-style: italic;">
                            A delightful environment for humans comes at a cost.
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
