<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Maritime Hammock Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/Rasmussen-KP-Woods_4638.jpg");
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
            <b>Maritime Hammocks</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Maritime Hammocks</h1>
    </div>
    <div class="top-text-container">
        <h3>
            Maritime hammocks, also known as maritime forests, tropical hammocks or coastal hammocks, are narrow bands of
            hardwood forest that develop almost exclusively on stabilized backdunes of barrier islands, inland of primary
            dunes and scrub. Generally dominated by species of broad-leaved evergreen trees and shrubs, maritime hammocks
            are climax communities influenced heavily by salt spray.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: R. Rasmussen
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
                        <a href="#hammock-dynamics-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Hammock Dynamics</span>
                        </a>
                    </li>
                    <li>
                        <a href="#hammock-plants-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Maritime Hammock Plants</span>
                        </a>
                    </li>
                    <li>
                        <a href="#hammock-animals-section" data-number="4">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Maritime Hammock Animals</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                Maritime forests occur along the entire Atlantic coast of the United States, interrupted by natural features
                such as inlets and bays, and human activities including coastal development and agriculture. Adjacent maritime
                forests tend to be vegetatively similar to one another, but overall vegetation profiles change with latitude.
            </p>
            <p>
                Florida, which has the longest coastline in the contiguous United States, has approximately 468,000 acres of
                barrier islands. Maritime forests occupy the highest, most stable areas of these islands, atop stable, vegetated
                dunes. The present location and extent of today's maritime forests were established approximately 5,000 years
                ago.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/2_hammock_landscape_LHS.jpg" />
                    <figcaption>
                        <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="hammock-dynamics-section" class="cd-section">
            <h4>Hammock Dynamics</h4>
            <p>
                Many factors influence whether particular species will be successful colonizers of the maritime forest. Soil
                composition, salt spray, fire, hydrology and human activity are all major influencers of hammock plant community
                composition.
            </p>

            <h5>Soils</h5>
            <p>
                Soils are predominantly composed of either sand or peat. Sandy soils are more common along forested dune ridges,
                while peat is more common among interdune swales and wetlands.
            </p>
            <p>
                Mature vegetated dunes feature distinct soil profiles. The upper horizon consists of leaf litter and twigs.
                A deeper, ashy white horizon results from leeching of organic materials deeper into the soil. Beneath this
                is a tan or orange horizon which receives substances leeched from above.
            </p>

            <h5>Salt Spray</h5>
            <p>
                Tolerance to salt spray is considered the principal factor that controls vegetative cover in maritime forests.
                Trees closest to the ocean are subject to onshore winds carrying sand and salt spray, which acts not only
                to prune terminal buds in the canopy top, but also encourages growth of lateral buds, producing over time,
                the familiar windswept shape of maritime forest canopies.
            </p>
            <p>
                Streamlining of the canopy profile assists growth of maritime forests in several ways.
            </p>
            <p>
                First, the windswept profile of the maritime forest canopy helps to deflect winds up and over the forest,
                preventing trees from being uprooted during intense storms. Second, dense canopies provide shelter to understory
                plants and protect the understory from large temperature fluctuations, reducing warming of the soil during
                the day, and preventing heat loss at night. Third, because trees on the windward edges of the forest show
                increased growth in their lateral buds, they are somewhat denser overall than more interior trees. As winds
                blow across the dense canopy, salt spray is deposited.
            </p>
            <p>
                Interior trees are thus protected from the effects of salt spray by the windward trees. This feature allows
                trees in the interior forest to assume characteristic heights and growth patterns resembling those of mainland
                forests.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/3_FSP_prescribed_burn.jpg" />
                    <figcaption>
                        A prescribed burn underway in St. Sebastian River State Park. <i>Credit: Florida State Parks</i>
                    </figcaption>
                </figure>
            </div>

            <h5>Fire</h5>
            <p>
                Fire is considered an "organizer" of forest cover patterns on barrier islands in Florida. It has long been
                a traditional agricultural tool for maintaining open areas, improving grazing lands, and eliminating pest
                species.
            </p>
            <p>
                Fire characteristics differ depending on canopy species composition.
            </p>
            <p>
                In oak forests, a dense evergreen canopy is usually coupled with a sparse, shade-tolerant understory and a
                somewhat moist litter layer. With less fuel at ground level, when fire does occur, it tends to smolder close
                to the ground, making crown fires infrequent.
            </p>
            <p>
                By contrast, in pine forests, dense understory vegetation is coupled with a tall, sparse canopy, and significantly
                drier soils. Fires in pine forests are likely to have a large fuel source close to the ground, resulting in
                the increased likelihood of intense crown fires.
            </p>
            <p>
                Though pines are considered to be inferior long-term competitors to oaks, maximum fire temperatures in mixed
                forests are high enough to eliminate oaks from an area entirely. Fire may allow pines to gain a competitive
                advantage to oaks in areas where fires occur.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/4_view_from_St_S_River_MZD.jpg" />
                    <figcaption>
                        <i>Credit: M. Donahue</i>
                    </figcaption>
                </figure>
            </div>

            <h5>Hydrology</h5>
            <p>
                Maritime forests have distinctive hydrological features that affect a barrier island's natural communities,
                and help determine whether human development can be sustained.
            </p>
            <p>
                Rainfall is generally the only source of fresh water on barrier islands, and the maritime forest community
                acts as the primary watershed. Precipitation entering the watershed is rapidly drawn deep into a freshwater
                “lens” which floats above the denser salt water in the permeable sediments beneath barrier islands. The volume
                of water in these lenses can be substantial: hydrological models have shown that the freshwater lens on a
                barrier island can contain approximately 40 meters of freshwater for each meter of free water table above
                mean sea level.
            </p>
            <p>
                At the area of contact between fresh and salt water, freshwater at the edges of the lens seeps upward to
                the surface, and into the overlying ocean or lagoon. Water in the lens is generally fairly low in salts, in
                spite of the fact that salt spray is a major ecological influence.
            </p>
            <p>
                However, excessive pumping of freshwater from the lens reservoir for residential and commercial purposes can
                lead to loss of the hydrostatic head in the freshwater lens. In turn, this could increase the rate of salt
                water intrusion into surface waters on the island.
            </p>

            <h5>Other Human Impacts</h5>
            <p>
                In addition to water management, habitat fragmentation and road construction are two other major impacts of
                human activities on maritime hammock barrier islands in Florida.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/5_bulldozer_LHS.jpg" />
                    <figcaption>
                        <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Because maritime forests occur on the most stable areas of barrier islands, they are highly desirable building
                sites. Clearing lots for houses involves disturbing or destroying most, if not all, the natural vegetative
                cover to make space for buildings, parking areas, drainage fields, and septic systems. Following construction,
                dominant landscape plants include grass lawns and ornamental shrubs, many of which are exotic.
            </p>
            <p>
                The fragmentation of forest hammocks allows non-native and weedy species to expand more rapidly, and increases
                competitive pressure on native plant and animal species by compressing them into smaller and smaller disconnected
                islands of habitat.
            </p>
            <p>
                Road construction has several surprising impacts as well. To permit easy access to beaches, usually at least
                one main road is constructed along the entire length of a barrier island, above the dune ridge at the perimeter
                of maritime forests, with other roads built laterally for access to developments and residences. While the
                roads themselves minimally impact existing forests, the openings they create in the forest canopy exposes
                the forest interior to increased salt penetration, threatening growth patterns and species composition.
            </p>
        </div>
        <div id="hammock-plants-section" class="cd-section">
            <h4>Maritime Hammock Plants</h4>
            <p>
                Florida’s maritime hammocks fall into three major types: temperate broad-leaved forest, also known as evergreen
                forest; southern mixed hardwood forest; and tropical forest.
            </p>
            <p>
                Temperate broad-leaved forests are dominated by live oak and sabal palms. Southern mixed hardwood forests
                are dominated by Southern magnolia, American holly, flowering dogwood, pignut hickory and American beech.
                Tropical forests are characterized by both evergreen and deciduous species such as mastic, <i>Eugenia</i>, wild
                tamarind and gumbo limbo.
            </p>
        </div>
        <div id="hammock-animals-section" class="cd-section">
            <h4>Maritime Hammock Animals</h4>
            <p>
                Many different animal species inhabit Florida's barrier island communities. In maritime hammocks, insects,
                small mammals, reptiles and birds dominate the fauna.
            </p>
            <div style="display:flex;justify-content: center;align-content: center;">
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/6_21ShirahD2.jpg" />
                    <figcaption>
                        <i>Credit: D. Shirah</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/7_16_CorteseM1.jpg" />
                    <figcaption>
                        <i>Credit: M. Cortese</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/8_Odocoileus_virginianus_B_Cozza.jpg" />
                    <figcaption>
                        <i>Credit: B. Cozza</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Common inhabitants include wading birds, birds of prey, small mammals and larger mammals such as river otters
                and wild boar. Reptiles include soft-shelled turtles, gopher tortoises, a variety of snakes, as well skinks
                and lizards which prey on the abundant insect, frog, and small mammal population.
            </p>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
