<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title>Muck & Nutrients</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/muck_LHS.jpg");
            background-position: center bottom;
        }

        #innertext{
            position: sticky;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/static-page.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
</head>
<body>
<div class="hero-container">
    <span class="screen-reader-only" role="img" aria-label="Person holding a large clump of muck with both hands near a boat."> </span>
    <div class="top-shade-container"></div>
    <div class="logo-container">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" class="header-home-link" aria-label="Go to homepage" tabindex="0">
            <img class="logo-image" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/janky_mangrove_logo_med.png" alt="Mangrove logo" />
        </a>
    </div>
    <div class="title-container">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" class="header-home-link" aria-label="Go to homepage" tabindex="0">
            <span class="titlefont">Indian River Lagoon<br />
            Species Inventory</span>
        </a>
    </div>
    <div class="login-bar">
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
            <a href="Maps.php" tabindex="0">The Indian River Lagoon</a> &gt;
            <a href="Habitat_Threats.php" tabindex="0">Threats</a> &gt;
            <b>Muck & Nutrients</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Muck & Nutrients</h1>
    </div>
    <div class="top-text-container">
        <h2>
            Black, sticky and smelly, and coating more and more of the bottom of the Indian River Lagoon, the tarry sediments
            known as muck pose a major threat to the estuary’s function and health.
        </h2>
    </div>
    <div class="photo-credit-container">
        Photo credit: H. Sweat
    </div>
</div>
<div id="bodyContainer">
    <div class="sideNavMover">
        <div class="sideNavContainer">
            <nav id="cd-vertical-nav">
                <ul class="vertical-nav-list">
                    <li>
                        <a href="#intro-section" data-number="1" aria-label="Skip to Intro" tabindex="0">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Intro</span>
                        </a>
                    </li>
                    <li>
                        <a href="#nutrients-section" data-number="2" aria-label="Skip to Nutrients and Harmful Wastes" tabindex="0">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Nutrients and Harmful Wastes</span>
                        </a>
                    </li>
                    <li>
                        <a href="#remediation-section" data-number="3" aria-label="Skip to Remediation" tabindex="0">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Remediation</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                While the lagoon’s sediments are mostly made up of sands, silts and shell fragments, an increasing area of
                the lagoon bottom is becoming covered with a fine-grained, organic-rich mud called muck. Muck accrual in the
                lagoon has been ongoing for the past 40 to 60 years, mostly as a result of terrestrial and industrial runoff.
                Although less than 10 percent of the Indian River Lagoon bottom was covered in muck in 1990, coverage continues
                to grow.
            </p>
            <p>
                Muck generally settles into depressions in the sediment; in the lagoon, most muck occurs in deeper and dredged
                areas of the lagoon such as the Intracoastal Waterway as well as at the mouths of most of the lagoon’s major
                tributaries. Deposits can reach up to 15 feet deep in some areas.
            </p>
            <p>
                Disturbance from boat traffic, wind and waves can stir up and suspend muck in the water column, clouding the
                water and reducing available sunlight necessary for healthy aquatic plant growth. Once displaced, muck particles
                can also be carried with currents and deposited in shallower, nearshore areas such as tidal flats, where it
                interferes with feeding processes of benthic infaunal and filter feeding organisms. Muck can also settle in
                salt marshes and mangrove forests, smothering young vegetation.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/14_BolonM2.jpg" alt="Marina with docked boats under a dark, stormy sky." />
                    <figcaption style="width:500px;">
                        <i>Credit: M. Bolon</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="nutrients-section" class="cd-section">
            <div class="text-h5 text-bold">Nutrients and Harmful Wastes</div>
            <p>
                In addition to suspended sediments, storm water runoff (non-point pollution) from urban and agricultural areas
                can contain high levels of industrial, automotive and household chemicals, fertilizers, pesticides, and animal
                wastes.
            </p>
            <p>
                Periodic releases of freshwater from nearby Lake Okeechobee, along with any accumulated wastes and algae, also
                contribute to water quality issues throughout the Indian River Lagoon estuary.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/2algae_on_lake_O_Jackie_Thurlow_Lippisch.jpg" alt="Aerial view of dark blue water with green algae streaks and part of an airplane wing." />
                    <figcaption style="width:500px;">
                        Streaks of algae bloom on Lake Okeechobee in 2021. <i>Credit: E. Lippisch, J. Thurlow-Lippisch</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Although the high bacterial biomass associated with some areas of the lagoon, such as tidal mudflats, can
                break down these pollutants somewhat, excessive volumes of contaminants can accumulate in the lagoon’s bottom
                sediments, creating health problems or death for a variety of aquatic organisms.
            </p>
            <p>
                Excess nitrogen and phosphorous can alter the dominant plant communities of marshes and mangroves. These nutrients
                can also increase the proliferation of cyanobacterial mats and blooms throughout the lagoon, as well as promote
                excessive phytoplankton growth that interferes with normal filter feeding processes of many organisms.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/cyanobacteria_Paul_Gray.jpg" alt="Boats docked at a marina with cyanobacteria and debris in the water." />
                    <figcaption style="width:500px;">
                        An algae bloom proliferates in a marina in Fort Pierce, Florida. <i>Credit: P. Gray</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="remediation-section" class="cd-section">
            <div class="text-h5 text-bold">Remediation</div>
            <p>
                Muck removal dredging projects are a major avenue to address harmful sediments in the Indian River Lagoon.
                In Brevard County alone, remediation projects aim to remove roughly 6 million cubic yards of sediments that
                have accumulated over decades. Muck is de-watered and transported elsewhere for storage. A single project in
                2019 in the Eau Gallie River removed 600,000 cubic yards of muck, eliminating 1,200 tons of nitrogen and 260
                tons of phosphorous from the lagoon.
            </p>
            <p>
                Reducing new sources of muck is critical to the success of maintaining long-term water quality in the lagoon.
                To that end, the counties surrounding the Indian River Lagoon, together with the Florida Department of Environmental
                Protection, the St. Johns River Water Management District and the South Florida Water Management District,
                are working with communities to:
            </p>
            <ul class="statictext">
                <li>
                    reduce excess fertilizer applications
                </li>
                <li>
                    curb stormwater runoff
                </li>
                <li>
                    identify and upgrade failing septic systems
                </li>
                <li>
                    eliminate wastewater treatment facility discharges
                </li>
                <li>
                    restore acres of submerged aquatic vegetation (seagrasses) and oyster reefs
                </li>
            </ul>
        </div>
    </div>
</div>
<?php
include_once(__DIR__ . '/../config/footer-includes.php');
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
