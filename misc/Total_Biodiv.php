<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Biodiversity</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/15_SpradleyM1.jpg");
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
        <h1>Biodiversity</h1>
    </div>
    <div class="top-text-container">
        <h3>
            Home to over 4,200 species of plants, birds, fish and mammals, the Indian River Lagoon is considered to be one
            of the most biodiverse estuaries in North America.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: M. Spradley
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
                        <a href="#location-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">A Unique Location</span>
                        </a>
                    </li>
                    <li>
                        <a href="#what-is-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">What is Biodiversity?</span>
                        </a>
                    </li>
                    <li>
                        <a href="#threats-section" data-number="4">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Threats to Biodiversity</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                An estimated 2,100 species of plants and more than 2,200 animal species live within the watershed, including
                nearly 700 species of fish and 370 species of birds. Ongoing research continues to discover and catalog numerous
                species of invertebrates, crustaceans, microscopic diatoms, sponges and algae.
            </p>
        </div>
        <div id="location-section" class="cd-section">
            <h4>A Unique Location</h4>
            <p>
                The IRL owes its incredible biodiversity to two main factors: its unique geographical position, and its diverse
                montage of habitat types.
            </p>
            <p>
                East-central Florida is located in the transition area between the temperate Carolinian climate zone to the
                north, and the subtropical Caribbean climate zone to the south. Temperate species of plants and animals exist
                in the Indian River Lagoon at the southernmost extent of their ranges, while subtropical and tropical species
                exist at their northernmost extents. Generally, the area around Cape Canaveral in northern Brevard County is
                where vegetation patterns begin to shift from primarily warm-temperate shrubs and trees, to more subtropical
                and tropical varieties.
            </p>
            <p>
                Mangroves and salt marshes provide ample breeding, nursery and feeding grounds for a variety of species, and
                the lagoon’s ocean beaches attract some of the highest numbers of nesting sea turtles in the Western Hemisphere.
                The IRL also lies within the Atlantic Flyway and is an important stopover for many species of seasonally migratory
                birds.
            </p>
            <div style="display:flex;justify-content: center;align-content: center;">
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/13_MillerC1.jpg" />
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/17EbaughT1.jpg" />
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Caretta_caretta_Sabrina_Bethurum.jpg" />
                    <figcaption style="width:800px;">
                        River otters, pileated woodpeckers and loggerhead turtles all rely on the IRL’s diversity of Habitats
                        for their life cycles. <i>Credit: C. Miller, T. Ebaugh, S. Bethurum </i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Iconic residents of the lagoon include the Atlantic bottlenose dolphin (<i>Tursiops truncatus</i>) and manatee
                (<i>Trichechus manatus latirostris</i>). An estimated one-third of the U.S. manatee population uses the lagoon, and
                an estimated 300 dolphins are believed to live permanently in the IRL.
            </p>
            <p>
                The lagoon’s watershed is also home to 53 species of animals that are classified as either threatened or endangered.
                Johnson’s seagrass (<i>Halophila johnsonii</i>) is one IRL resident that is found nowhere else in the world; other rare
                species include the four-petaled pawpaw (<i>Asimina tetramera</i>), smalltooth sawfish (<i>Pristis pectinata</i>), green and
                leatherback turtles (<i>Chelonia mydas</i> and <i>Dermochelys coriacea</i>) and Kirtland’s warbler (<i>Setophaga kirtlandii</i>).
            </p>
        </div>
        <div id="what-is-section" class="cd-section">
            <h4>What is Biodiversity?</h4>
            <p>
                Biodiversity may be defined as the measure of how healthy an ecosystem is. Healthy ecosystems support high
                biological diversity, while stressed or highly disturbed ecosystems do not. Biodiversity encompasses not
                only diversity of species and diverse gene pools, but also diverse habitat and ecosystem types.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/06RaulersonD1.jpg" />
                    <figcaption style="width:500px;">
                        Land crabs on the march. <i>Credit: D. Raulerson</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Populations with greater genetic diversity are far better equipped to cope with environmental change, and
                are able to reproduce more successfully than populations with low genetic diversity. Populations with low
                genetic diversity can become so well adapted to local conditions that any environmental disturbance may be
                enough to reduce their numbers dramatically, or even destroy them entirely.
            </p>
        </div>
        <div id="threats-section" class="cd-section">
            <h4>Threats to Biodiversity</h4>
            <p>
                The factors which threaten biodiversity in estuaries and in the oceans are generally the same as those which
                affect biodiversity in terrestrial systems: overexploitation, physical alteration of habitat areas, alien species
                introductions, and changes in atmospheric composition.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Fort_Pierce_Inlet_Daniel_Piraino_Flickr.jpg" />
                    <figcaption style="width:500px;">
                        Fort Pierce from the air. <i>Credit: D. Piraino, Flickr</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Many threats to the survival of life in the oceans can originate on land. Examples of threats include siltation,
                nutrient loading, and pollution by toxic chemicals. The continuous growth of human development also threatens
                coastal and estuarine ecosystems. Habitat degradation which occurs as the result of these problems inevitably
                leads to loss of species from an ecosystem, and thus, a loss of biodiversity.
            </p>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
