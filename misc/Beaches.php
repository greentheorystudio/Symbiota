<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Beach Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/vero_beach_aerial.jpg");
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
            <b>Beaches</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Beaches</h1>
    </div>
    <div class="top-text-container">
        <h3>
            Beaches lie at the boundary between land and ocean. Florida’s Atlantic beaches, especially those in the Indian
            River Lagoon area, are dynamic, high-energy zones. Though harsh and seemingly barren, beaches are complex habitats
            that support many species of animals and plants.
        </h3>
    </div>
    <div class="photo-credit-container">
        Vero Beach, Florida. Photo credit: D. Piraino, Flickr
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
                        <a href="#beach-dynamics-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Beach Dynamics</span>
                        </a>
                    </li>
                    <li>
                        <a href="#beach-plants-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Beach Plants</span>
                        </a>
                    </li>
                    <li>
                        <a href="#animals-section" data-number="4">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Animals</span>
                        </a>
                    </li>
                    <li>
                        <a href="#invertebrates-section" data-number="5">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Invertebrates</span>
                        </a>
                    </li>
                    <li>
                        <a href="#birds-section" data-number="6">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Birds</span>
                        </a>
                    </li>
                    <li>
                        <a href="#reptiles-section" data-number="7">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Reptiles</span>
                        </a>
                    </li>
                    <li>
                        <a href="#mammals-section" data-number="8">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Mammals</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                Of the Sunshine State’s 8,426 miles (13,560 km) of tidal shoreline, approximately 825 miles (1,328 km)
                consist of sandy beaches, primarily along Florida's east coast. Though the state’s Gulf beaches are renowned
                for their sugar-white sands, the northeastern beaches of Florida, including those in the northern Indian River
                Lagoon area, are composed principally of quartz originating in the Appalachian Mountains. Further south, the
                amount of quartz in sand decreases steadily, and sand composition becomes primarily calcium carbonate from
                rock and shell deposits.
            </p>
        </div>
        <div id="beach-dynamics-section" class="cd-section">
            <h4>Beach Dynamics</h4>
            <p>
                <b><i>Abiotic factors</i></b> such as wave action, erosion, sand accretion by winds, overwash, and the deposition of salt
                spray are physical processes that contribute to beach and dune formation.
            </p>
            <p>
                The slope of a beach and the shape of its dunes are heavily influenced by tides, wind patterns, storm events
                and the movement of sand that often accompanies these events. Waves deposit sand on beaches as they break,
                lose energy and retreat. Hurricanes can erode sands and carry them offshore, or overwash dunes and deposit
                sand inland.
            </p>
            <p>
                In the IRL, beach profiles change seasonally. In summer, waves tend to occur as swells that move sediments
                up the beach, building berms and providing sands for dunes. But during fall and winter, the steep waves
                that accompany storms erode beaches and flatten the profile, depositing eroded sands seaward on longshore
                bars.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/2_beach_from_dune_walk_LHS.jpg" />
                    <figcaption>
                        <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                <b><i>Biotic factors</i></b> generally center around the ability of plants to colonize and grow, which
                stabilizes the beach and its dunes. Colonizing species of plants must be able to tolerate the xeric conditions
                that result from sand being generally well-drained and low in nutrients, as well as frequently being buried
                in sand or inundated by sea water.
            </p>
        </div>
        <div id="beach-plants-section" class="cd-section">
            <h4>Beach Plants</h4>
            <p>
                Intense wave action, strong winds, and the presence of sea water make it difficult for many species of plants
                to colonize beach areas directly along the shoreline. However, several species are able to become established
                in the upper beach zone, thus enabling sand stabilization and subsequent development of dune systems.
            </p>
            <p>
                Most beach plants occupy the <i>pioneering zone</i>, which extends landward from the wrack line on the upper beach
                through the dune area. Pioneering species are highly specialized to tolerate the punishing environmental
                challenges they face: very dry (xeric) conditions, heavy winds, low nutrient availability, high soil
                temperatures and burial in sand.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/3_railroad_vine_LHS.jpg" />
                    <figcaption>
                        Railroad vine (<i>Ipomoea pes-caprae</i>). <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                The most successful pioneering species in coastal zones are halophytic, or able to thrive in highly saline
                conditions. Many of these same plants also have high growth rates; some plants actually grow faster as they
                become buried in sand.
            </p>
            <p>
                Pioneering species are also generally vine-like or succulent, with waxy or hairy coverings on their stems
                and leaves. They produce many seeds that are widely dispersed, helping them to become quickly established
                or recolonized on beach areas. They tend to spread widely as they grow, creating a network of creeping
                stems so if one part of the plant is uprooted or buried in shifting sand, the remaining segments can continue
                to grow.
            </p>
            <p>
                Pioneering plants’ roots also help to anchor sand, and thus assist in subsequent dune building and stabilization.
            </p>
        </div>
        <div id="animals-section" class="cd-section">
            <h4>Animals</h4>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/4_Black_Skimmer_Beach1_WaltersL.jpg" />
                    <figcaption>
                        Black skimmer. <i>Credit: L. Walters</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                At first glance, beaches may appear to support comparatively few animal species. But beyond birds and reptiles,
                there are a great many other species – they’re often just too small or furtive to chance upon casually.
            </p>
            <p>
                Many species utilize beaches as feeding areas. Sandpipers and other shorebirds, wading birds, and even some
                fish such as the Florida pompano (<a href="../taxa/index.php?taxon=7640"><i>Trachinotus carolinus</i></a>) employ the surf zone to prey on animals that either
                wash out of the sand due to wave action, or come close enough to the shore to be captured.
            </p>
        </div>
        <div id="invertebrates-section" class="cd-section">
            <h4>Invertebrates</h4>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/5_Ocypode_quadrata_Maureen_McNally.jpg" />
                    <figcaption>
                        Atlantic ghost crab, <i>Ocypode quadrata</i>. <i>Credit: M. McNally</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                These include the often overlooked but highly abundant meiofauna that live between sand grains, and the
                more familiar species of annelid worms that burrow into the substratum. Various bivalve and snail species,
                as well as many species of small crustaceans such as isopods and amphipods inhabit the wrack line along
                the shore.
            </p>
            <p>
                Surf clams (variable coquina) and mole crabs are two species commonly seen in the surf zone. Both animals
                are extremely fast burrowers, able to rebury themselves almost as fast as they become exposed in shifting
                sands. Further up the beach, somewhat removed from intense wave action, the ghost crab makes its home by
                burrowing into the sand.
            </p>
        </div>
        <div id="birds-section" class="cd-section">
            <h4>Birds</h4>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/6_I_Brought_Lunch_Mary_White.jpg" />
                    <figcaption>
                        <i>Credit: M. White</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Although many species of birds are often observed on beaches, most visit for feeding. Only five species
                of shorebirds prefer nesting sites on bare sands in the upper beach zone, including the snowy plover,
                black skimmer, least tern, royal tern and sandwich tern. Snowy plovers nest only along the Gulf coast of
                Florida.
            </p>
        </div>
        <div id="reptiles-section" class="cd-section">
            <h4>Reptiles</h4>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/6_11_DubrickU2.jpg" />
                    <figcaption>
                        <i>Credit: U. Dubrick</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                The Florida coastline is the most important nesting area for sea turtles in the western Atlantic. Six of
                the world’s seven species of sea turtles are dependent on Florida beaches for nesting during the summer.
            </p>
            <p>
                In Florida, loggerhead turtles and green turtles are by far the most common. Loggerheads lay an average of
                3,000 to 4,000 nests per year; green turtles lay approximately 300 nests per year.
            </p>
            <p>
                The highest sea turtle nest densities occur in southern Brevard County, from south of Cape Canaveral to
                Sebastian Inlet; however, sea turtles nest even along the highly developed beaches of Broward and Dade counties.
            </p>
            <p>
                In human-impacted areas, it is often necessary to dig up turtle nests and relocate the eggs to other areas
                to ensure successful hatching.
            </p>
        </div>
        <div id="mammals-section" class="cd-section">
            <h4>Mammals</h4>
            <p>
                Some mammals also exploit beaches for feeding. Raccoons, feral cats and foxes patrol the wrack line at the
                high-water mark for morsels, and scavenge eggs from sea turtle nests.
            </p>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
