<?php
include_once(__DIR__ . '/../config/symbbase.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Biodiversity</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/15_SpradleyM1.jpg");
            background-position: center bottom;
        }

        #innertext{
            position: sticky;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/modernizr.js" type="text/javascript"></script>
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
            The Indian River Lagoon’s diverse montage of habitats creates a broad variety of opportunities for life in the estuary.
            Thousands of species of plants, birds, fish and mammals call the lagoon home.
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
                    <li>
                        <a href="#further-reading-section" data-number="5">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Further Reading</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                The IRL Species Inventory is an effort to document organisms that have been present in the Lagoon at some point in time –
                but is certainly an incomplete record of the total diversity in the Indian River Lagoon. However, to date, the Inventory
                contains documentation for 4,460 individual species, including an estimated 2,100 species of plants and more than 2,200
                animal species.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:375px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/old_IRL_bidiversity_pie_chart.jpg" />
                </figure>
            </div>
            <p>
                The original IRL Species Inventory, first compiled in 1995, listed a total of 2,493 different species of plants, animals and
                protists. Of these, animals comprised the greatest proportion of species in the inventory (71.4 percent), with 1,779 species
                grouped into 20 phyla. Plants were grouped into four phyla, consisting of 289 different species. Protista (17 percent) consisted
                of 425 species in four phyla. No data are available for Kingdom Monera (bacteria).
            </p>
            <p>
                Sampling of some taxa are more complete and thoroughly documented than others, including fishes, birds, mollusks, chrysophytes,
                dinoflagellates, rhizopods, ectoprocts, sipunculids, echinoderms, and mammals. Other taxonomic groups, including vascular plants,
                amphibians and reptiles, and marine macroalgae are relatively complete but could benefit from increased sampling over wider areas
                of the lagoon. Other taxa are, at the very best, partial lists, for example sponges and chaetognaths.
            </p>
            <p>
                Ongoing research continues to discover and catalog numerous species of invertebrates, crustaceans, microscopic diatoms, sponges
                and algae.
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
                        River otters, pileated woodpeckers and loggerhead turtles all rely on the IRL’s diversity of habitats
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
                <b>Genetic Diversity:</b> Populations with greater genetic diversity are far better equipped to cope with environmental change, and
                are able to reproduce more successfully than populations with low genetic diversity. Populations with low
                genetic diversity can become so well adapted to local conditions that any environmental disturbance may be
                enough to reduce their numbers dramatically, or even destroy them entirely.
            </p>
            <p>
                <b>Species Richness:</b> Another measure of biodiversity is <i>species diversity</i>. One measure of this is <i>richness</i>, the
                number of species which occur within a particular taxonomic level (species, genus, family) in a geographic area. In marine
                ecosystems, species diversity tends to vary widely depending upon latitudinal and longitudinal location. Tropical areas
                tend to have higher species richness, for example. The lowest species diversity is found in the eastern Atlantic.
            </p>
            <p>
                <b>Ecosystem Diversity:</b> Ecosystems are the collection of all the plants and animals within a particular area, each
                differing in species composition, physical structure and function. Ecosystem diversity refers to the number of ecosystems in
                a geographic area; the Indian River Lagoon is an example of a collection of ecosystems, each of them highly diverse.
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
        <div id="further-reading-section" class="cd-section">
            <h4>Further Reading</h4>
            <ul class="further-reading-list">
                <li>
                    Norse, Elliot A. 1993. Global Marine Biological Diversity: A Strategy for Building Conservation Into Decision Making.
                    Island Press, Washington, D.C. 384 pp.
                </li>
                <li>
                    Swain, H., P. A. Schamlzer, D. R. Breininger, K. Root, S. Boyle, S. Bergen, S. MacCaffree. 1995. Appendix B Biological
                    Consultant's Report. Brevard County Scrub Conservation and Development Plan. Dept. Bio. Sci., Florida Institute of
                    Technology., Melbourne, FL.
                </li>
                <li>
                    Thorne-Miller, Boyce, and J. Catena. 1991. The Living Ocean: Understanding and Protecting Marine Biodiversity.
                    Island press, Washington D.C. 180 pp.
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
