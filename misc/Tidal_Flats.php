<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Tidal Flat Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/13_FischerD1.JPG");
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
            <b>Tidal Flats</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Tidal Flats</h1>
    </div>
    <div class="top-text-container">
        <h3>
            In some areas of the Indian River Lagoon, when the tides recede, an ephemeral landscape reveals itself. At first
            glance, the lagoon’s silty-sandy bottom may seem like a barren mudscape – but just below the surface an abundance
            of life is burrowed in, waiting for the return of the lagoon’s waters.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: D. Fischer
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
                        <a href="#mud-sand-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Mud vs. Sand</span>
                        </a>
                    </li>
                    <li>
                        <a href="#pros-cons-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Ecological Pros and Cons</span>
                        </a>
                    </li>
                    <li>
                        <a href="#threats-section" data-number="4">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Threats to Tidal Flats</span>
                        </a>
                    </li>
                    <li>
                        <a href="#species-section" data-number="5">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Tidal Flat Species</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                Covered at high tide and exposed at low tide, tidal flats are dominated by soft sediments and generally lack
                vegetation. Found worldwide, they are common elements of estuaries, and are the basic landform upon which
                coastal wetlands accumulate. In the Indian River Lagoon, tidal flats are most abundant near inlets, where
                tidal influence is strongest.
            </p>
            <p>
                Tidal flats comprise only about 7 percent of total coastal shelf areas, but are highly productive ecosystems.
                Though overall biological diversity may be relatively low, tidal flats can contain astounding volumes of
                microorganisms and benthic infauna, or tiny animals that live in the top layer of sediment. In addition to
                recycling organic matter and nutrients from terrestrial and marine sources, benthic infauna are also prey
                for many fin and shellfish species, as well as resident and migratory wetland birds.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/foltz_on_flats_MZD.jpg" />
                    <figcaption>
                        <i>Credit: M. Donahue</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="mud-sand-section" class="cd-section">
            <h4>Mud vs. Sand</h4>
            <p>
                Tidal flats are highly dynamic, with sediments continuously on the move. Depending on sediment grain size,
                tidal flats are generally categorized as either mud or sand flats.
            </p>
            <p>
                Mudflats usually occur in the upper portion of the intertidal zone, and in areas with low-energy water movement.
                Here, sediments contain a high proportion of fine silt and clay particles. Mudflats have higher organic content,
                generally from microbial activity or from adjacent sources such as salt marshes, mangroves and seagrass beds.
            </p>
            <p>
                Sandflats occur in areas with stronger currents and moderate wave action that can carry larger, heavier sediment
                particles. Sediments are mostly quartz (silica) derived from erosion. In southern Florida systems, mud-sand
                and combinations of calcium carbonate coral rock soft-bottom types are common.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Fort_Pierce_Inlet_D_Ramey_Logan-2.jpg" />
                    <figcaption>
                        Fort Pierce Inlet. <i>Credit: D. Ramey Logan, Wikimedia Commons</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Both types of tidal flat occur on Coon Island, along the north side of Fort Pierce Inlet in the Indian River
                Lagoon. The eastern edge of the island gives way to a relatively large tidal flat. On the southern end, where
                currents are relatively strong, sediments are sandy; on the northern and western areas, which are more protected
                from inlet currents, sediments are muddier.
            </p>
            <p>
                Mud and sand flats also differ in their oxygen concentrations, which influence microbial activity. This activity
                stabilizes seasonal variation in organic material, ensuring a more consistent food supply for other organisms.
            </p>
            <p>
                In mudflats, the fine sediments trap detritus and prevent water from easily percolating through. The higher
                surface area of the numerous fine grains allows for higher numbers of microbes, which leads to increased
                anaerobic decomposition of organic matter. This activity produces hydrogen sulfide, methane and ammonia in
                an oxygen-poor layer, roughly .4 inches (1 cm) below the surface. Often black in color, this layer is visually
                striking in its contrast to the thin, grayish oxygenated layer above it.
            </p>
            <p>
                In sandflats, the large-grained particles allow water to percolate easily through sediments, which allows
                oxygen to penetrate as deep as 4 to 8 inches (10 to 20 cm) below the surface. Light can also filter deeply,
                allowing for prolonged activity by photosynthetic microorganisms.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/black_necked_stilt_Ursula_Dubrick.jpg" />
                    <figcaption>
                        Black-necked stilt with chick. <i>Credit: U. Dubrick</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="pros-cons-section" class="cd-section">
            <h4>Ecological Pros and Cons</h4>
            <p>
                For benthic organisms, life in the muddy sands of tidal flats affords many advantages. They can retreat into
                deeper sediments or burrows when threatened by predation. Able to move around, infaunal bivalves can survive
                partial predation as well as direct competition with burrowing neighbors. Desiccation is rarely an issue.
                Finally, organic materials accumulating on sediments provide a ready, constant food source.
            </p>
            <p>
                But there are drawbacks: lack of a securing "anchor" in the sediment. In contrast to rocky intertidal habitats,
                where organisms are often securely attached to the rock via cement, byssal threads and muscular feet, tidal
                flat organisms are at the mercy of the sediments. During periods of severe storm erosion, larger infauna in
                soft bottom habitats may become easily dislodged and subsequently displaced.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/luidia_senegalensis_MZD.jpg" />
                    <figcaption>
                        Nine-armed seastar. <i>Credit: M. Donahue</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="threats-section" class="cd-section">
            <h4>Threats to Tidal Flats</h4>
            <p>
                Clean water and sediments are critical for healthy lagoon habitats. Tidal flat areas face a number of human-made
                and natural threats, including sea level rise, loss of habitat, salinity fluctuations, pollution, erosion
                and invasive species. Threats to tidal flats directly mirror threats to the larger Indian River Lagoon.
            </p>
            <p>
                For more information on the challenges facing the tidal flats and other areas of the lagoon, visit the
                <a href="Habitat_Threats.php">Threats resource page</a>.
            </p>
        </div>
        <div id="species-section" class="cd-section">
            <h4>Tidal Flat Species</h4>
            <p>
                Tidal flats host a diverse biotic assemblage, ranging from microscopic organisms to large crabs, fish and
                wading birds.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/man_o_war_MZD.jpg" />
                    <figcaption>
                        Man o’ war jellyfish during low tide on the IRL. <i>Credit: M. Donahue</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                The majority of organisms in tidal flats are considered to be benthic, or living in or on the lagoon bottom.
                Though most are extremely tiny, such as bacteria and diatoms, some, including parchment worms and the nine-armed
                sea star, can grow to be quite large.
            </p>
            <ul class="statictext">
                <li>
                    <i>Microbenthos</i> comprise primarily bacteria and diatoms.
                </li>
                <li>
                    <i>Meiobenthos</i> are usually less than a millimeter in length, which live in the void spaces between
                    relatively large sand grains in sediments.
                </li>
                <li>
                    <i>Hyperbenthos</i> are slightly larger, a few millimeters in length, and live in the water just above
                    the lagoon floor as well as in the very top layers of the sediment.
                </li>
                <li>
                    <i>Macrobenthos</i> are larger and can move freely through soft sediments, and include polychaete worms,
                    bivalves and amphipods.
                </li>
                <li>
                    <i>Epibenthos</i> are large, predatory and grazing species including crabs, mollusks, fish, rays, wading
                    birds and mammals.
                </li>
            </ul>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
