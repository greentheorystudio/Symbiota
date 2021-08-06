<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Shoreline Development</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/Stuart_shoreline_KRoark_flickr.jpg");
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
            <b>Shoreline Development</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Shoreline Development</h1>
    </div>
    <div class="top-text-container">
        <h3>
            Land use changes from the growing population and urbanization in Florida and throughout the world have altered
            coastal ecosystems. Along the Indian River Lagoon, many habitats have been altered or removed to accommodate
            booming population growth.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: K. Roark, Flickr
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
                        <a href="#habitat-impacts-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Habitat Impacts</span>
                        </a>
                    </li>
                    <li>
                        <a href="#water-impacts-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Water Impacts</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                Among the greatest impacts of this development are habitat loss and changes in water quality from runoff
                and other pollutants.
            </p>
        </div>
        <div id="habitat-impacts-section" class="cd-section">
            <h4>Habitat Impacts</h4>
            <p>
                Since the 1950s, an estimated 75 percent of the Indian River Lagoon’s mangroves and salt marshes have been
                destroyed, altered or functionally isolated. These marginal habitats have been removed and filled with dredged
                material to create roads, residential communities and businesses. Changes in mangrove and salt marsh areas
                have direct repercussions for bordering sand and mudflats.
            </p>
            <p>
                The habitat fragmentation that results from this rapid development has fractured animal communities and left
                ecosystems more vulnerable to other habitat disturbances.
            </p>
            <p>
                Preserved natural areas and various state permitting processes aim to minimize such habitat loss. Substantial
                mangroves and salt marshes lay within protected areas such as the Merritt Island National Wildlife Refuge
                and property managed by the Florida Department of Environmental Protection. Proper management provides conservation
                from further development and encourages restoration programs that work to increase habitat acreage.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Lake_O_release_mixing_with_ocean_JTL.jpg" />
                    <figcaption style="width:500px;">
                        Freshwater released from Lake Okeechobee creates a distinct boundary as it meets salty ocean water
                        at the St. Lucie River inlet. <i>Credit: E. Lippisch</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="water-impacts-section" class="cd-section">
            <h4>Water Impacts</h4>
            <p>
                Development of the built human environment has greatly increased the amount of freshwater that drains to
                the Indian River Lagoon. This change in the natural volume and timing of freshwater inputs to the lagoon has
                greatly altered the health of the lagoon.
            </p>
            <p>
                One major influence is the complex network of agricultural and drainage canals constructed over the 20th century,
                which deliver up to 2.5 times the amount of freshwater than the estuary system can naturally handle. These
                freshwater surges not only impact the salinity of the brackish estuary, but also allow more direct flow of
                sediments and other pollutants into the lagoon.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/drainage_canal_JTL.jpg" />
                    <figcaption>
                        A drainage canal in south Florida. <i>Credit: J. Thurlow-Lippisch</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Unlike pollution coming from a factory or water treatment facility, non-point source pollution cannot be
                traced back to a single point of origin. It includes the diluted discharges of contaminant-laden water from
                residential and agricultural sources, nutrient inputs from septic drainage fields, and pollutants carried
                to the lagoon as stormwater runoff.
            </p>
            <p>
                Urbanization and the accompanying increase in paved, impermeable surfaces reduces the watershed’s ability to
                filter rainfall and other water runoff. In undeveloped, natural areas, rainfall percolates down into porous
                soil, which mechanically and biologically filters out contaminants before they can reach the lagoon. As more
                and more land is paved over, this important natural process is lost.
            </p>
            <p>
                Increased sediments and pollutants from human activity also drives the formation of muck on the lagoon bottom.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/07_OwenP1.jpg" />
                    <figcaption style="width:500px;">
                        Jupiter Inlet from above. Many of the lagoon’s water-adjacent areas are heavily developed, though the
                        region has many parks and natural preserve areas. <i>Credit: P. Owen</i>
                    </figcaption>
                </figure>
            </div>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
