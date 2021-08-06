<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Climate Change</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/dorian_NOAA.jpg");
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
            <b>Climate Change</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Climate Change</h1>
    </div>
    <div class="top-text-container">
        <h3>
            The Indian River Lagoon’s abundance of natural fauna and flora is a result of its geographical overlap of both
            temperate and subtropical biomes. Impacts from sea level rise, the most immediate and emergent threat due to climate
            change, directly affects the ecology, hydrodynamics, circulation patterns, depth and salinity of the many interconnected
            habitats of this shallow, marine-connected ecosystem.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: NOAA/Wikimedia Commons
    </div>
</div>
<div id="bodyContainer">
    <div class="sideNavMover">
        <div class="sideNavContainer">
            <nav id="cd-vertical-nav">
                <ul class="vertical-nav-list">
                    <li>
                        <a href="#sea-level-rise-section" data-number="1">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Sea Level Rise</span>
                        </a>
                    </li>
                    <li>
                        <a href="#impacts-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Impacts</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="sea-level-rise-section" class="cd-section">
            <h4>Sea Level Rise</h4>
            <p>
                Sea level rise in response to global warming is a major concern to all coastal wetlands and tidal flats. Current
                estimates predict sea levels to rise between 12 inches to 8 feet by 2100. Though estuaries are thought of
                as ephemeral, most present-day estuaries have been relatively stable for approximately 6,000 years. Past changes
                in sea level have greatly affected estuarine outlines and could have rapid and significant present-day effects.
                Rising sea levels could render intertidal flats into subtidal habitat and inundate adjoining mangrove and
                salt marsh areas, diminishing their protective and ecological benefits.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/florida_flooding_susceptibility_NASA.jpg" />
                    <figcaption style="width:500px;">
                        Left, a standard topographical view of Florida from space. At right, areas of the state most susceptible
                        to inundation during storm surge and other flooding events show the extent to which much of the Florida
                        peninsula is subject to effects of rising seas. <i>Credit: NASA/CalTech/Wikimedia</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Much attention has been given to the effects of rising sea level on coastal ecosystems throughout the world.
                As intertidal communities, salt marshes and mangroves are at risk from both the amplitude and rate of this rise.
                For the ecosystems to thrive, they must occur at the appropriate elevation and slope. In fact, one of the
                most common reasons for restoration failure in salt marshes is choosing an improper site based on these parameters
                (Crewz & Lewis 1991). As sea level rises, it is possible for marshes and mangroves to shift in a landward direction
                if the rate of rise is slow enough for sediment accretion to occur (Montague & Wiegert 1990). However, coastal
                development and steep terrain may inhibit plant migration, changing zonation in these habitats or flooding
                them completely. In addition, compression of the intertidal zone can lead to increased interspecific competition
                and loss of biodiversity.
            </p>
            <p>
                Surrounded by the Atlantic Ocean and Gulf of Mexico and also inundated by waters from the Everglades, low-lying
                Florida is particularly prone to climate change and rising sea levels. Further, most of Florida sits on porous
                bedrock, which allows for the infiltration of saltwater.
            </p>
            <p>
                Rising sea levels can also erode beaches, submerge estuaries and low-lying wetlands, enhance coastal flooding
                and increase the salinity of estuaries.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Miami_tidal_flooding_16_wikimedia.jpg" />
                    <figcaption style="width:500px;">
                        A flooded street during high tide in Miami in 2016. <i>Credit: Wikimedia Commons</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="impacts-section" class="cd-section">
            <h4>Impacts</h4>
            <p>
                <b>Mangrove communities</b>, occurring along the fringes of intertidal regions throughout most of the IRL, stabilize
                shorelines and also provide habitat and nursery area for IRL's many ecologically and recreationally important
                finfish and invertebrates. These mangrove communities have adapted to occupy and maintain their position along
                the fringes of the lagoon by accreting sediment at a rate in tune with sea level rise when it was occurring
                at a relatively slow pace. Accelerated sea level rise could pose threat to these vital communities by outpacing
                their ability to accumulate sediments at appropriate rates.
            </p>
            <p>
                <b>Seagrass beds</b> are indispensible to the overall health and water quality of the IRL. They also provide sediment
                stabilization and complex habitat. In addition, seagrasses oxygenate the water column, provide substratum
                for epiphytes and are a food source utilized by manatees, urchins, conchs, some fish and sea turtles. Deeper
                waters associated with accelerated sea level rise could diminish sunlight levels and adversely impact the
                photosynthetic capacities of seagrasses leading to substantial decreases in seagrass acreage.
            </p>
            <p>
                <b>Larval stages</b> of some estuarine invertebrate organisms have a narrow range of salinity tolerance. Increased
                salinity in the lagoon accompanying sea level rise could negatively impact their life histories with consequences
                affecting their abundance and diversity.
            </p>
            <p>
                <b>Threats to biodiversity</b>: Sea level rise and warmer water temperatures could decrease the number of temperate
                species that co-occur in the IRL alongside subtropical species; invasive and other opportunistic organisms could
                more easily establish themselves under stressful conditions resulting from sea level rise, displacing native
                flora and fauna.
            </p>
            <p>
                <b>Wetlands and maritime hammocks</b> are at risk of drowning and being inundated by salt water. These habitats
                have nowhere to retreat due to development along much of the lagoon’s shoreline. In the northern sections of
                the lagoon, losses of mud flats and salt marshes would impact the many bird, mammal and invertebrate species
                that live and feed there.
            </p>
            <p>
                The ability of <b>barrier islands and dune communities</b> to buffer storm effects of wind and water would be compromised,
                directly affecting the region’s flora and fauna, but also its many humans and their infrastructure systems.
            </p>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
