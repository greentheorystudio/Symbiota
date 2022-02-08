<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Salt Marsh Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/11SmithA1.jpg");
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
            <b>Salt Marshes</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Salt Marshes</h1>
    </div>
    <div class="top-text-container">
        <h3>
            Occurring in the zone between high and low tide, salt marshes are common in sheltered coastal areas and along
            estuaries. Marshes act as nurseries to a wide variety of organisms, some of which are notably threatened or marketed
            as important fisheries species.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: A. Smith
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
                        <a href="#plant-adaptations-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Plant Adaptations</span>
                        </a>
                    </li>
                    <li>
                        <a href="#distribution-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Distribution & Regional Occurrence</span>
                        </a>
                    </li>
                    <li>
                        <a href="#communities-section" data-number="4">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Types of Communities</span>
                        </a>
                    </li>
                    <li>
                        <a href="#species-section" data-number="5">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Salt Marsh Species</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                Salt marshes form where sediments accumulate and allow growth of flowering plants and trees, which anchor
                this ecosystem. Abundant incoming nutrients from freshwater runoff and tidal flushing enable rapid growth
                of marsh vegetation – each square foot of marsh can yield up to .5 pound (2 kilograms per square meter) of
                plant matter annually. In addition to providing habitat and food sources for many organisms, salt marshes
                shelter coasts from erosion and filter nutrients and sediments from the water column.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/2_05LawrenceL3.jpg" />
                    <figcaption>
                        <i>Credit: L. Lawrence</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="plant-adaptations-section" class="cd-section">
            <h4>Plant Adaptations</h4>
            <p>
                Perched between terrestrial and marine environments, salt marshes are biologically diverse communities adapted
                for harsh environmental conditions including flooding, low oxygen (anoxia), salinity fluctuations and extreme
                temperatures.
            </p>

            <h5>Flooding & Anoxia</h5>
            <p>
                Tidal flooding is a normal process in salt marshes. Low- and mid-marsh areas can be submerged for hours during
                daily tidal cycles, and high marshes can experience storm surge that can affect more upland vegetation. The
                frequency and duration of flooding events, as well as the tolerance of individual species to saltwater submersion,
                is a major determinant of salt marsh zonation. Zonation occurs when various salt marsh plant species thrive
                in specific elevation ranges.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/3_20WestM2_N.jpg" />
                    <figcaption>
                        <i>Credit: M. West</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Lower limits of plant zonation are usually set by environmental tolerances, while upper limits are mainly
                the result of interspecific competition. Some plants, such as smooth cordgrass (<i>Spartina alterniflora</i>),
                can withstand and are even limited to areas that receive substantial flooding. Other vegetation, like black
                needle rush (<i>Juncus roemerianus</i>), prefers less frequent flooding. Submersion in water can create a host of
                problems for vegetation including increased intake or loss of salts through tissues and greater exposure to
                aqueous toxins. Waterlogged soil and high levels of decaying material can deplete oxygen, creating anoxic
                sediments and producing toxic sulfides.
            </p>
            <p>
                Most plants that grow in anoxic soil produce adventitious roots near the sediment surface to facilitate oxygen
                uptake. For example, frequently flooded plants like smooth cordgrass grow roots in the top inch (3 cm) of
                the sediment which help oxygenate deeper roots. Some plants also have a well-developed system of spongy air
                passages, called aerenchyma tissue, which transfer oxygen from the atmosphere to submerged roots.
            </p>

            <h5>Salinity</h5>
            <p>
                Salinity in salt marshes is highly variable because of the influx of both fresh and saltwater into the environment.
                Freshwater enters upland marsh areas from terrestrial streams and rivers, increasing during periods of high
                precipitation. Saltwater inundates marshes during high tides, and dry seasons and high evaporation further
                increases salinity. Salinity gradients caused by these processes contribute to zonation in marsh plants based
                on salt tolerance among species.
            </p>
            <p>
                Most flowering plants (angiosperms) have a limited ability to thrive in saline waters, and diversity of vegetation
                decreases with increasing salinity. Seeds and seedlings are especially vulnerable to salt stress, further
                contributing to zonation in plants. However, many salt marsh plants have evolved to tolerate high salinities.
                Some plants increase succulence by retaining water or excluding salt at the roots, while others excrete salt
                through specialized glands or sequester it into leaves that are shed periodically.
            </p>
            <p>
                Perhaps one of the greatest stresses for salt marsh plants is the difficulty of absorbing water from salty
                soil, which averages 10 to 20 parts per thousand (ppt) but may exceed 100 ppt in areas such as salt pans.
                Many marsh plants adjust to this physiological strain by accumulating sugars and other organic solutes in
                their tissues, which increase the vascular pressure needed to absorb water from the soil.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/4_2011_Calendar_Lola_Lawrence.jpg" />
                    <figcaption>
                        <i>Credit: L. Lawrence</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="distribution-section" class="cd-section">
            <h4>Distribution & Regional Occurrence</h4>
            <p>
                Salt marsh habitats are found at nearly all latitudes, transitioning into mangrove forests in the tropics/subtropics.
                In the United States, the majority of the four million acres of salt marshes are along the east coast from
                Maine to Florida and along the Gulf of Mexico coastline. Few marshes exist on the Pacific coast of the U.S.
                due to high wave energy and mountainous terrain, but extensive marshes can be found in Alaska. Florida is
                home to an estimated 420,000 acres of salt marsh, with 70 percent in the northern part of the state, 20 percent
                in the south, and 10 percent in the Indian River. The majority of marshes in the IRL are concentrated in the
                northern half of the system.
            </p>
        </div>
        <div id="communities-section" class="cd-section">
            <h4>Types of Communities</h4>
            <h5>Salt Marsh-Mangrove Transition</h5>
            <div style="clear:both;">
                <figure style="float: left;margin-right: 30px;">
                    <img style="border:0;width:200px;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/5_mangrove_salt_marsh_transition.jpg" />
                    <figcaption>
                        <i>Credit: Florida Fish and Wildlife Service Research Institute</i>
                    </figcaption>
                </figure>
                <p>
                    Marshes in South Florida, including the Everglades and Ten Thousand Islands, are predominantly transition
                    areas where salt marsh plants grow in peats that have formed around mangroves and buttonwoods. Black
                    mangrove is particularly common, growing alongside <i>Baccharis</i>, <i>Salicornia</i>, <i>Batis</i>, <i>Distichlis</i>, <i>Borrichia</i>
                    and <i>Iva</i> species. Fresh and saltwater influences in these areas create harsh environments with wide salinity
                    fluctuations that help to balance growth between vegetation.
                </p>
            </div>

            <h5>High Marsh</h5>
            <p>
                High marsh occurs in areas above the mean high-water mark and is not commonly flooded by tides. Stands of
                <i>Spartina</i>, <i>Juncus</i>, <i>Salicornia</i> and <i>Distichlis</i> are mixed with various mangrove species. On the east coast of
                South Florida around Biscayne Bay, high marshes are often dominated by <i>Spartina</i>, transitioning to large
                <i>Juncus</i> populations toward the Homestead region. High marsh is the most common salt marsh community in the
                IRL.
            </p>

            <h5>Oligohaline Marsh</h5>
            <div style="clear:both;">
                <figure style="float: left;margin-right: 30px;">
                    <img style="border:0;width:200px;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/6_salt_marsh_creek.jpg" />
                    <figcaption>
                        <a href="https://www.flickr.com/photos/ryanregister/2454522021/sizes/l/" target="_blank"><i>Credit: Ryan Register, Flickr</i></a>
                    </figcaption>
                </figure>
                <p>
                    Oligohaline marsh forms where large influxes of freshwater enter the salt marsh ecosystem. Vegetation in
                    these brackish areas is a mixture of both marine and estuarine plants that tolerate low salinities,
                    including needlegrass rush, golden leather fern, cattail, and Jamaica swamp grass. The creation of mosquito
                    impoundments shifted many of the marshes in the IRL toward this type of community.
                </p>
            </div>

            <h5>Salt Pans</h5>
            <p>
                Salt pans form in low-latitude marshes where high soil salinities of 100 parts per thousand or greater create
                bare patches devoid of vegetation. These barrens are often bordered by highly salt-tolerant plants including
                saltworts, glassworts and <i>Juncus</i> spp. Salt pans are prevalent throughout Florida marshes, but are most common
                along the southwest coast. Salt pans are typically well-drained areas, and should not be confused with salt
                ponds, which hold standing water in higher latitude marshes.
            </p>
        </div>
        <div id="species-section" class="cd-section">
            <h4>Salt Marsh Species</h4>
            <p>
                Salt marsh vegetation can vary between community types. However, the most common genera of foundation plants
                in Florida marshes include <i>Spartina</i>, <i>Juncus</i>, <i>Distichlis</i> and <i>Batis</i>. These vascular plants and associated algae
                provide habitat and food for a variety of fishes, birds, mammals, insects and other invertebrates. According
                to the Florida Natural Areas Inventory of 1997, local salt marshes support at least 10 species of fishes,
                33 birds, 12 mammals and five vascular plants that are considered to be rare or endangered.
            </p>
            <div style="display:flex;justify-content: center;align-content: center;">
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/7_06CohenG2.jpg" />
                    <figcaption>
                        <i>Credit: G. Cohen</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/8_06LilienthalP1.jpg" />
                    <figcaption>
                        <i>Credit: P. Lilienthal</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/9_SpoonbillMarsh_WaltersLinda.jpg" />
                    <figcaption>
                        <i>Credit: L. Walters</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Many salt marsh organisms have a substantial impact of the health of the system. For example, herbivores and
                detritivores break down and consume large amounts of organic material produced by plants and algae, and fiddler
                crabs excavate complex burrows that aerate the soil and promote growth of <i>Spartina</i> grasses.
            </p>
            <p>
                Salt marshes are also home to several hundred species of microalgae and numerous attached or drift macroalgae.
                Algae growth and decay is more rapid than in plants, allowing other organisms to easily use them as an energy
                source. Macroalgae also provide habitat and food for fishes and invertebrates, while mats of phytoplankton,
                benthic diatoms and cyanobacteria stabilize sediments on mud flats, possibly allowing for colonization of
                salt marsh vegetation.
            </p>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
