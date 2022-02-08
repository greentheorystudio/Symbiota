<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Invasive Species</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/lionfish_FWS_amanda_nalley.jpg");
            background-position: center bottom;
        }

        #innertext{
            position: sticky;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/modernizr.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/static-page.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
</head>
<body>
<div class="hero-container">
    <div class="top-shade-container"></div>
    <div class="logo-container">
        <img class="logo-image" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/janky_mangrove_logo_med.png" />
    </div>
    <div class="title-container">
        <span class="titlefont">Indian River Lagoon<br />
            Species Inventory</span>
    </div>
    <div class="login-container">
        <?php
        include(__DIR__ . '/../header-login.php');
        ?>
    </div>
    <div class="nav-bar-container">
        <?php
        include(__DIR__ . '/../header-navigation.php');
        ?>
    </div>
    <div class="breadcrumb-container">
        <div class='navpath'>
            <a href="Maps.php">The Indian River Lagoon</a> &gt;
            <a href="Habitat_Threats.php">Threats</a> &gt;
            <b>Invasive Species</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Invasive Species</h1>
    </div>
    <div class="top-text-container">
        <h3>
            Two of the very features that make the Indian River Lagoon so biologically diverse also predispose the system
            to invasion by exotics: its climate, and its geographic location near to the western Atlantic Gulf Stream.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: A. Nalley, FWS
    </div>
</div>
<div id="bodyContainer">
    <div class="sideNavMover">
        <div class="sideNavContainer">
            <nav id="cd-vertical-nav">
                <ul class="vertical-nav-list">
                    <li>
                        <a href="#intro-section" data-number="1">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Intro</span>
                        </a>
                    </li>
                    <li>
                        <a href="#about-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">About Non-Native Species</span>
                        </a>
                    </li>
                    <li>
                        <a href="#changed-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Changed Waters, Changed Species</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                The temperate-to-subtropical climate transition invites a wide variety of species to settle down. The lagoon
                also has many points of entry from both land and sea, with canals that drain upland portions of the watershed
                as well as open inlets to the Atlantic Ocean where the Gulf Stream passes relatively close to the coastline.
            </p>
        </div>
        <div id="about-section" class="cd-section">
            <h4>About Non-Native Species</h4>
            <p>
                Hundreds of non-indigenous plants and animals have been introduced in the five centuries since the arrival of
                the first Europeans in Florida. Many have greatly benefited humans. For example, cultivated plants of the
                genus <i>Citrus</i>, which originated in southeast Asia, India and West Asia, are now a billion-dollar industry in
                the state.
            </p>
            <p>
                However, along with intentional and beneficial introductions have come plenty of accidental and harmful new
                arrivals. Released from predators, competitors, parasites and disease that keep them in check in their native
                ranges, some introduced species may proliferate to the point where they become invasive. Their presence in
                the ecosystems they have invaded has resulted in significant economic or environmental harm.
            </p>
            <p>
                Estuaries and shallow-water muddy sediments have proportionately more invasive species than rocky shores and
                open coast sandy shores. This difference probably results from the fact that most introductions, intentional
                or not, take place within estuaries like the Indian River Lagoon.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Brazilian_pepper_tree_Forest_and_Kim_Starr_Wikimedia.jpg" />
                    <figcaption style="width:500px;">
                        Brazilian pepper tree, <i>Schinus terebinthifolia</i>. <i>Credit: F. and K. Starr, Wikimedia Commons</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Invasive non-natives can cause ecological damage by competing with, displacing or otherwise negatively impacting
                native populations. These species can also cause economic harm by damaging valuable agricultural products as
                well as human-built infrastructure.
            </p>
            <p>
                The impacts can be substantial, but it is difficult to predict which species will be harmful. Very often, the
                invaders go largely unnoticed until populations have grown and spread to a point where eradication is difficult
                and costly, or even impossible.
            </p>
        </div>
        <div id="changed-section" class="cd-section">
            <h4>Changed Waters, Changed Species</h4>
            <p>
                In Florida, the introduced nutria, <a href="../taxa/index.php?taxon=Myocastor coypus"><i>Myocastor coypus</i></a>,
                contributes to the loss of marsh acreage by foraging on vegetation. Changes in water flow around salt marshes and
                mangroves have allowed for expansion of the invading Brazilian pepper, <a href="../taxa/index.php?taxon=Schinus terebinthifolius"><i>Schinus terebinthifolius</i></a>,
                and the Australian pine, <a href="../taxa/index.php?taxon=Casuarina equistifolia"><i>Casuarina equistifolia</i></a>.
                Closing portions of these habitats for mosquito impoundments has reduced the salinity, allowing the invasion of
                more oligohaline vegetation and animals such as the blackchin tilapia, <a href="../taxa/index.php?taxon=Sarotherodon melanotheron"><i>Sarotherodon melanotheron</i></a>.
            </p>
            <p>
                Disturbed or barren areas will often be colonized by invasives before native plants can become established.
                Efforts are ongoing to remove invasive plants from terrestrial areas, but aquatic invasions of fishes and
                invertebrates are often difficult or impossible to reverse, and can only be managed to prevent further range
                expansion.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Agama_picticauda.jpg" />
                    <figcaption style="width:500px;">
                        Peter’s rock agama (<i>Agama picticauda</i>) is an easily sighted invader around the Indian River Lagoon and
                        elsewhere in Florida. <i>Credit: Wikimedia Commons</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                In the Indian River Lagoon region, notably harmful species include:
            </p>
            <ul class="statictext">
                <li>
                    Charru mussel (<i>Mytella charruana</i>), which like the more widely known green and zebra mussels can clog
                    power plant intake pipes;
                </li>
                <li>
                    Lionfish (<i>Pterois volitans</i>), which competes for food and space with overfished native species such
                    as grouper and snapper, along with many other important native fish species;
                </li>
                <li>
                    Brazilian peppertree (<i>Schinus terebinthifolia</i>), which infests over 700,000 acres throughout Florida
                    and prevents many native species from growing in its dense shade.
                </li>
            </ul>
            <p>
                Other factors contribute to the spread of harmful invasive species in Florida and the Indian River Lagoon:
            </p>
            <p>
                <b>Storms.</b> Seasonal tropical storms and hurricanes can also facilitate the spread of exotics throughout
                the lagoon watershed and Florida broadly, by physically dispersing individuals, seeds and propagules.
            </p>
            <p>
                <b>Development.</b> The third most populous state in the nation, Florida’s resident population numbers nearly
                22 million people (as of 2020); and several times that number visiting each year. Much of Florida is dominated
                by habitats created or extensively modified by humans. In the IRL, as in many other parts of Florida, human
                disturbance exposes local ecosystems to invasion by harmful newcomers.
            </p>
            <p>
                <b>Trade and shipping.</b> Florida’s status as an international transportation and shipping hub also contributes
                to the exotic invasive crisis. Hundreds of millions of imported live plants pass through the Port of Miami
                each year, and many species of animals shipped to the U.S. for the exotic pet trade also pass through Florida.
            </p>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
