<?php
include_once(__DIR__ . '/../config/symbbase.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>The Indian River Lagoon Estuary</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/1_21LaMartinaM1.jpg");
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
    <div class="page-title-container">
        <h1>The Indian River Lagoon</h1>
    </div>
    <div class="top-text-container">
        <h3>
            Occupying more than 40 percent of Florida’s eastern coast, the 156-mile-long Indian River Lagoon (IRL) is part
            of the longest barrier island complex in the United States.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: M. La Martina
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
                        <a href="#what-is-lagoon-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">What Is a Lagoon?</span>
                        </a>
                    </li>
                    <li>
                        <a href="#irl-dynamics-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Dynamics of the IRL</span>
                        </a>
                    </li>
                    <li>
                        <a href="#wind-section" data-number="4">
                            <span class="cd-dot"></span>
                            <span class="cd-label">The Role of Wind</span>
                        </a>
                    </li>
                    <li>
                        <a href="#inlets-section" data-number="5">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Inlets and Tides</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                The IRL’s seagrass beds, mangroves, oyster reefs, salt marshes, tidal flats, scrubland, beaches and dunes
                nurture more than 3,500 species of plants, animals and other organisms. This rich biodiversity is largely
                due to the lagoon’s unique geographic location, at the transition between cool, temperate and warm, subtropical
                climate zones.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/IRLZonesMap.gif" />
            </div>
            <p>
                Designated as an “estuary of national significance” by the U.S. Environmental Protection Agency, the IRL
                also provides enormous human benefits, supporting thousands of jobs and generating $7.6 billion annually
                to Florida’s economy.
            </p>
        </div>
        <div id="what-is-lagoon-section" class="cd-section">
            <h3>What Is a Lagoon?</h3>
            <div style="display:flex;justify-content: space-between">
                <div style="width: 100%;">
                    <figure style="float: left;margin-right: 30px;">
                        <img style="border:0;width:475px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/2_16_MassaungJ1.jpg" />
                        <figcaption>
                            Aerial view of the Indian River Lagoon. <i>(Credit: J. Massaung)</i>
                        </figcaption>
                    </figure>
                    <p>
                        Lagoons are shallow coastal bodies of water, separated from the ocean by a series of barrier islands
                        which lie parallel to the shoreline.
                    </p>
                    <p>
                        Inlets, either natural or man-made, cut through the barrier islands, and permit tidal currents to
                        transport water into and out of the lagoons. Because lagoons tend to be shallow, water temperature
                        and salinity can fluctuate drastically due to precipitation and evaporation.
                    </p>
                    <p>
                        Lagoons are classified into three main types:
                    </p>
                    <ul class="statictext">
                        <li>
                            <b>Leaky lagoons</b> have wide tidal channels, fast currents and unimpaired exchange of water
                            with the ocean.
                        </li>
                        <li>
                            <b>Choked lagoons</b> occur along high-energy coastlines, and have one or more long, narrow
                            channels which restrict water exchange with the ocean. Water circulation within this type
                            of lagoon is dominated by wind patterns.
                        </li>
                        <li>
                            <b>Restricted lagoons</b> have multiple channels, well-defined exchange with the ocean,
                            and tend to show a net-seaward transport of water. Wind patterns in restricted lagoons
                            can also cause surface currents to develop, thus helping to transport large volumes of water
                            downwind.
                        </li>
                    </ul>

                    <p>
                        The Indian River Lagoon is a <b>restricted-type lagoon</b>.
                    </p>
                    <div style="margin: 15px 0;display:flex;justify-content: center;">
                        <figure style="margin: 15px;">
                            <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/3_07_OwenP1.jpg" />
                            <figcaption>
                                Jupiter Inlet <i>(Credit: P. Owen)</i>
                            </figcaption>
                        </figure>
                    </div>
                </div>
            </div>
        </div>
        <div id="irl-dynamics-section" class="cd-section">
            <h3>Dynamics of the IRL</h3>
            <p>
                Florida’s Indian River Lagoon (IRL) system actually consists of three lagoons: the Mosquito Lagoon, which
                originates in Volusia County; the Banana River, in Brevard County; and the Indian River Lagoon, which spans
                nearly the entire coastal extent of Brevard, Indian River, St. Lucie and Martin counties.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/IRL_map_UMCES.png" />
                    <figcaption>
                        <i>Map courtesy Jane Thomas, University of Maryland Center for Environmental Science</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Other properties of lagoons may change depending on its size and physical characteristics. The Indian River
                Lagoon, for example, is significantly longer than it is wide.
            </p>
        </div>
        <div id="wind-section" class="cd-section">
            <h4>The Role of Wind</h4>
            <p>
                Lagoons like the IRL tend to be well-mixed because they are heavily influenced by wind patterns.
            </p>
            <p>
                Winds enhance vertical mixing in the water column, and also influence surface currents that ensure lateral
                mixing of estuarine water. This results in virtually no change in observed salinity from the surface to the
                bottom. In contrast, salinity in well-mixed estuaries decreases horizontally with distance from the ocean.
            </p>
            <p>
                Winds also influence the direction of the lagoon’s water flow. Currents can switch from north-flowing to
                south-flowing, or be completely stagnant, depending on the prevailing winds.
            </p>
        </div>
        <div id="inlets-section" class="cd-section">
            <h4>Inlets and Tides</h4>
            <p>
                The southern portion of the IRL exchanges water with the ocean through three jettied, human-made inlets,
                all of which differ in size. These and other features divide the IRL into three sub-basins: the southern
                sub-basin, between St. Lucie Inlet and Fort Pierce Inlet; the central sub-basin, between Fort Pierce Inlet
                and Sebastian Inlet; and the northern sub-basin, the remaining area of the lagoon north of Sebastian Inlet.
            </p>
            <p>
                Each of the sub-basins in the IRL experiences somewhat different tidal amplitudes, current speeds, and
                tidal excursion (the horizontal transport distance associated with either ebb or flood tide). Tidal amplitude,
                current speed and tidal excursion are all lowest in the northern sub-basin, and increase to the south.
            </p>
            <p>
                One important exception to this pattern occurs around inlets. In these areas, current speeds during maximum
                ebb or flood tides can exceed 1 meter per second due to the constricting effects of narrow inlet channels.
            </p>
            <p>
                However, tidal transport decreases as distance from the inlet increases. Wind takes over as the primary
                water transport process in the interior of the Lagoon.
            </p>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
