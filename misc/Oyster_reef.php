<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Oyster Reef Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/20SacksP1_N.JPG");
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
            <a href="Whatsa_Habitat.php">Habitats</a> &gt;
            <b>Oyster Reefs</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Oyster Reefs</h1>
    </div>
    <div class="top-text-container">
        <h3>
            Oysters have been an important part of Florida’s human history for thousands of years. Huge shell middens at
            archaeological sites throughout the state indicate the species’ importance to the indigenous people of Florida;
            in modern times, oysters have continued to serve as a valuable fishery. A better understanding of their ecological
            role has also emerged.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: P. Sacks
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
                        <a href="#reef-formation-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Reef Formation</span>
                        </a>
                    </li>
                    <li>
                        <a href="#environmental-benefits-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Environmental Benefits</span>
                        </a>
                    </li>
                    <li>
                        <a href="#threats-section" data-number="4">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Threats and Restoration</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                Oyster reefs, often referred to as oyster bars, are common submerged habitats in the southern United States.
                Oyster reefs in Florida are found in nearshore areas and estuaries of both coasts, but grow especially vigorously
                near estuarine river mouths where waters are brackish and less than 30 feet (10 meters) deep. For example,
                the Apalachicola River in northern Florida is a particularly productive area for oysters; the region supplies
                over 90 percent of the state's annual oyster catch.
            </p>
            <p>
                Within the Indian River Lagoon, oyster reefs occur in the vicinity of spoil islands and impounded areas. In
                addition to being commercially valuable, oyster reefs serve a number of important ecological roles in coastal
                systems: providing important habitat for a large number of species; improving water quality; stabilizing bottom
                areas, and influencing water circulation patterns within estuaries.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/1_Linda_Walters_Oysters_Mosquito_Lagoon.jpg" />
                    <figcaption>
                        <i>Credit: L. Walters</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="reef-formation-section" class="cd-section">
            <h4>Reef Formation</h4>
            <p>
                Oyster reefs are built primarily by the eastern oyster, Crassostrea virginica, through successive reproduction
                and settlement of larvae onto existing reef structure. Oysters in Florida spawn from late spring through
                the fall. Oysters’ planktonic, free-swimming larvae require a hard surface to settle upon in order to complete
                development to the juvenile stage, with a strong preference for oyster shells over other materials.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/19TweedieD1_S.jpg" />
                    <figcaption>
                        <i>Credit: D. Tweedie</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Successive generations of oysters may form massive reefs with staggering numbers of individuals. An estimated
                5,895 oysters, or the equivalent of 45 bushels, can be found within a single square yard of oyster reef.
            </p>
            <p>
                Over time, reefs develop into highly complex structures, with many nooks and crannies that provide a wealth
                of microhabitats for many different species of animals. In North Carolina, one study found 303 different
                species utilizing oyster reef as habitat.
            </p>
            <div style="display:flex;justify-content: center;align-content: center;">
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/2_BlueCrabOysters_WaltersLinda.jpg" />
                    <figcaption>
                        <i>Credit: L. Walters</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/3_RaccoonInOysters_WaltersLinda.jpg" />
                    <figcaption>
                        <i>Credit: L. Walters</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/4_Ibis_WaltersLinda.jpg" />
                    <figcaption>
                        <i>Credit: L. Walters</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Common Indian River Lagoon species associated with oyster reefs include hard clam and bay scallop; space
                competitors such as the scorched mussel, ribbed mussel, the jingle shell, and <i>Balanus</i> barnacles; and gastropod
                mollusks including conchs and rocksnails. Sponges, crabs, whelks, flatworms and annelid worms are also common.
            </p>
            <p>
                These abundant food sources draw a diversity of fish to oyster beds for feeding. In addition to preying upon
                the reef’s smaller residents, some utilize the oysters themselves for food, including the black drum and
                cow-nosed ray.
            </p>
        </div>
        <div id="environmental-benefits-section" class="cd-section">
            <h4>Environmental Benefits</h4>
            <p>
                Oysters provide numerous benefits to their surrounding ecosystems, as well as to humans.
            </p>
            <p>
                Oyster reefs are renowned for their ability to improve water quality in the areas where they occur. As filter
                feeders, oysters flow water over their gills to feed, straining out microalgae, suspended organic particles,
                and possibly dissolved organic matter from the water column. Under ideal temperature and salinity conditions,
                a single oyster may filter as much as 4 gallons (15 liters) of water per hour, up to 1,500 times its body
                volume. Spread over an entire reef, for an entire day, the potential for oysters to improve water clarity
                is immense.
            </p>
            <p>
                Filter feeding also results in oysters accumulating several toxins and pollutants that may be found in the
                water column into their tissues. This makes them useful indicators of the environmental health of some areas.
            </p>
            <p>
                Finally, the reefs themselves provide valuable shoreline stabilization and protection against erosion from
                wave action and storms. By dissipating incoming waves, reefs protect against sediment dislodging from shore,
                which diminishes water quality.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/5_Oyster_Reef_Monitoring_LW.jpg" />
                    <figcaption>
                        <i>Credit: L. Walters</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="threats-section" class="cd-section">
            <h4>Threats and Restoration</h4>
            <p>
                Oyster reef restoration has been a concern for resource managers all along the East Coast of the United States,
                but especially in areas where oyster harvesting has historically been commercially important. Over-harvesting,
                intensified coastal development, water pollution and persistent diseases such as MSX and Dermo have levied a
                devastating toll on many oyster populations along the east and Gulf coasts.
            </p>
            <p>
                In the late 1800s, annual oyster harvests in the southeastern United States routinely topped 10 million pounds
                per year, and peaked in 1908 when the harvest was nearly 20 million pounds. Today, annual harvests for oysters
                in the Southeast averages approximately 3 million pounds per year.
            </p>
            <p>
                In many areas, efforts are underway to revitalize depleted oyster reefs and encourage growth of new reefs.
                For example, the Florida Department of Agriculture has stockpiled calico scallop shells from processors and
                placed these on depleted oyster reefs from the spring through the fall spawning periods, when larvae are most
                abundant. Oyster larvae, having a preference for settling on shell material, then attach themselves onto the
                newly placed shells and metamorphose to the juvenile stage. These young oysters, under optimal conditions,
                will grow to marketable size in as little as 18 - 24 months.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/6_Biodegradable_Restoration_Materials_LW.jpg" />
                    <figcaption>
                        <i>Credit: L. Walters</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Throughout the Indian River Lagoon, elsewhere in Florida, and along many areas of the Atlantic coastline,
                restoration efforts have also focused on installing artificial reef lines made of natural oyster shells to
                protect shorelines and provide areas for oyster larvae to colonize.
            </p>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
