<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Extreme Weather</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/17AdamsN1_full.jpg");
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
            <b>Extreme Weather</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Extreme Weather</h1>
    </div>
    <div class="top-text-container">
        <h3>
            Salt marshes, mangroves and other coastal ecosystems can usually recover quickly from natural disturbances such
            as fire and hurricanes. However, when disturbance events occur in close succession, they may have lasting effects
            on the ecosystems.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: N. Adams
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
                        <a href="#erosion-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Erosion</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                Hurricanes produce storm surges, wind and waves that can impact mangroves and marshes in several ways. Upper
                marshes and mangrove swamps can experience an influx of seawater at a salinity to which vegetation is not
                accustomed, causing dieback of several plant species. Wind can strip trees and bushes of foliage and damage
                the trunk. The white mangrove, <i>Laguncularia racemosa</i>, is the mangrove species most susceptible to wind damage
                (Doyle et al. 1995).
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/09_WhiticarJ1.jpg" />
                    <figcaption style="width:500px;">
                        <i>Credit: J. Whiticar</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                In addition, lower elevations can experience extreme rates of sedimentation or erosion. Sediment erosion can
                wash away much of the vegetation, reducing habitat acreage. However, sediment accretion could be more harmful,
                essentially covering marsh and mangrove areas (Rejmanek <i>et al</i>. 1998) and smothering sessile benthic invertebrates.
                One example of rapid sedimentation occurred in the upper Chesapeake Bay, when over a 70-year period, 50 percent
                of the sediment accumulation was attributed to one flood event and a single hurricane (Schubel & Hirschberg 1978).
                Regeneration of mangrove forests following substantial storm damage may take decades, and restored swamps may
                have altered biodiversity and plant zonation (Ellison & Farnsworth 1990).
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Dania_Beach_Di_Palma_D_Wikimedia.jpg" />
                    <figcaption style="width:500px;">
                        Eroded shoreline at Dania Beach. <i>Credit: D. Di Palma/Wikimedia Commons</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="erosion-section" class="cd-section">
            <h4>Erosion</h4>
            <p>
                Sources of IRL tidal flat erosion are many. Storms, wind induced waves, hurricanes, epibenthic bioturbation,
                prop scarring, etc., can singly and sometimes synergistically contribute to the erosion of tidal flats. Because
                most of IRL tidal flat areas are located in the vicinity of inlets, they are further subjected to fluctuations
                in tidal current velocities. As mentioned above, since most infaunal organisms burrowing on the tidal flat
                lack an anchoring structure, severe rapid erosion, i.e. that which outpaces the ability of these organisms
                to burrow more deeply, can lead to substantial changes in infaunal abundance.
            </p>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
