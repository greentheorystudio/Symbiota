<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header('Content-Type: text/html; charset=UTF-8' );
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title>Salt Marsh Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/11SmithA1.jpg");
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
    <div class="top-shade-container"></div>
    <div class="logo-container">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" class="header-home-link" >
            <img class="logo-image" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/janky_mangrove_logo_med.png" alt="Mangrove logo" />
        </a>
    </div>
    <div class="title-container">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" class="header-home-link" >
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
                    <li>
                        <a href="#further-reading-section" data-number="6">
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
                Salt marshes form where sediments accumulate and allow growth of flowering plants and trees, which anchor
                this ecosystem. Abundant incoming nutrients from freshwater runoff and tidal flushing enable rapid growth
                of marsh vegetation – each square foot of marsh can yield up to .5 pound (2 kilograms per square meter) of
                plant matter annually. In addition to providing habitat and food sources for many organisms, salt marshes
                shelter coasts from erosion and filter nutrients and sediments from the water column.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/2_05LawrenceL3.jpg" alt="Credit: L. Lawrence" />
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
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/3_20WestM2_N.jpg" alt="Credit: M. West" />
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
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/4_2011_Calendar_Lola_Lawrence.jpg" alt="Credit: L. Lawrence" />
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
                    <img style="border:0;width:200px;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/5_mangrove_salt_marsh_transition.jpg" alt="Credit: Florida Fish and Wildlife Service Research Institute" />
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
                    <img style="border:0;width:200px;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/6_salt_marsh_creek.jpg" alt="Salt Marsh Creek" />
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
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/7_06CohenG2.jpg" alt="Credit: G. Cohen" />
                    <figcaption>
                        <i>Credit: G. Cohen</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/8_06LilienthalP1.jpg" alt="Credit: P. Lilienthal" />
                    <figcaption>
                        <i>Credit: P. Lilienthal</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/9_SpoonbillMarsh_WaltersLinda.jpg" alt="Credit: L. Walters" />
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
        <div id="further-reading-section" class="cd-section">
            <h4>Further Reading</h4>
            <ul class="further-reading-list">
                <li>
                    Adam, P. 1990. <i>Saltmarsh ecology.</i> Cambridge University Press. Cambridge, UK. 461 pp.
                </li>
                <li>
                    Anderson, CE. 1974. A review of structures of several North Carolina salt marsh plants. <i>In</i>: Reimold, RJ & WH Queen,
                    eds. <i>Ecology of halophytes.</i> 307-344. Academic Press. New York. USA.
                </li>
                <li>
                    Armstrong, W. 1979. Aeration in higher plants. <i>Adv. Bot. Res.</i> 7: 225-332.
                </li>
                <li>
                    Brockmeyer JR., RE, Rey, JR, Virnstein, RW, Gilmore, RG & L Earnest. 1997. Rehabilitation of impounded estuarine wetlands
                    by hydrologic reconnection to the Indian River Lagoon, Florida (USA). <i>Wetlands Ecol. Manag.</i> 4: 93-109.
                </li>
                <li>
                    Chapman, VJ. 1960. <i>Salt marshes and salt deserts of the world.</i> Leonard Hill Limited. London, UK.
                </li>
                <li>
                    Coles, SM. 1979. Benthic microalgal populations on intertidal sediments and their role as precursors to salt marsh development.
                    <i>In</i>: Jefferies, RL & AJ Davy, eds. <i>Ecological processes in coastal environments.</i> 25-42. Blackwell Scientific Publications.
                    Oxford, UK.
                </li>
                <li>
                    Costa, CSB & AJ Davy. 1992. Coastal salt marsh communities of Latin America. <i>In</i>: Seeliger, U, ed. <i>Evolutionary ecology
                    in tropical and temperate regions: coastal plant communities of Latin America.</i> 179-199. Academic Press. San Diego, CA. USA.
                </li>
                <li>
                    Crewz, DW & RR Lewis III. 1991. <i>An evaluation of historical attempts to establish vegetation in marine wetlands in Florida.</i>
                    Florida Sea Grant technical paper TP-60. Sea Grant College, University of Florida. Gainesville, FL. USA.
                </li>
                <li>
                    Dame, RF. 1989. The importance of Spartina alterniflora to Atlantic coast estuaries. <i>Rev. Aquat. Sci.</i> 1: 639-660.
                </li>
                <li>
                    David, JR. 1992. <i>The Saint Lucie County Mosquito Control District summary workplan for mosquito impoundment restoration for
                    the salt marshes of Saint Lucie County.</i> Saint Lucie County Mosquito Control District. Saint Lucie, FL. USA.
                </li>
                <li>
                    Dawes, CJ. 1998. <i>Marine botany, 2nd ed.</i> John Wiley & Sons. New York. USA. 480 pp.
                </li>
                <li>
                    Drake, BG. 1989. Photosynthesis of salt marsh species. <i>Aquat. Bot.</i> 34: 167-180.
                </li>
                <li>
                    Dybas, CL. 2002. Florida's Indian River Lagoon: an estuary in transition. <i>BioScience.</i> 52: 554-559.
                </li>
                <li>
                    Eleuterius, LN & CK Eleuterius. 1979. Tide levels and salt marsh zonation. <i>Bull. Mar. Sci.</i> 29: 394-400.
                </li>
                <li>
                    Field, DW, Reyer, AJ, Genovese, PV & BD Shearer. 1991. <i>Coastal wetlands of the United States.</i> National Oceanic and
                    Atmospheric Administration and US Fish and Wildlife Service. Washington, DC.
                </li>
                <li>
                    FNAI. 1997. <i>County distribution and habitats of rare and endangered species in Florida.</i> Florida Natural Areas Inventory. Tallahassee, FL. USA.
                </li>
                <li>
                    Flowers, TJ, Troke, PF & AR Yeo. 1977. The mechanism of salt tolerance in halophytes. <i>Ann. Rev. Plant Physiol.</i> 28: 89-121.
                </li>
                <li>
                    Flowers, TJ, Hajibagheri, MA & NWJ Clipson. 1986. Halophytes. <i>Q. Rev. Biol.</i> 61: 313-337.
                </li>
                <li>
                    Ford, MA & JB Grace. 1998. Effects of vertebrate herbivores on soil processes, plant biomass, litter accumulation and
                    soil elevation changes in a coastal marsh. <i>J. Ecol.</i> 86: 974-982.
                </li>
                <li>
                    FWS. 1999. Coastal Salt Marsh. <i>In</i>: <i>Multi-species recovery plan for South Florida.</i> US Fish & Wildlife Service. 553-595.
                </li>
                <li>
                    Hacker, SD & MD Bertness. 1995. A herbivore paradox: why salt marsh aphids live on poor-quality plants. <i>Amer. Nat.</i> 145: 192-210.
                </li>
                <li>
                    Kale II, HW. 1996. Recently extinct: dusky seaside sparrow, <i>Ammodramas maritimus nigrescens</i>. <i>In</i>: Rodgers, JA, Kale II, HW & HT
                    Smith, eds. <i>Rare and endangered biota of Florida.</i> Volume V. Birds. 7-12. University Presses of Florida. Gainesville, FL. USA.
                </li>
                <li>
                    Klassen, CA. 1998. <i>The utilization of a Florida salt marsh mosquito impoundment by transient fish species.</i> Master's Thesis.
                    Florida Inst. of Technology. 87 pp.
                </li>
                <li>
                    Larson, VL. 1995. Fragmentation of the land-water margin within the northern and central Indian River Lagoon watershed. <i>Bull. Mar. Sci.</i> 57: 267-277.
                </li>
                <li>
                    Leenhouts, WP. 1983. <i>Marsh and water management plan, Merritt Island National Wildlife Refuge.</i> US Fish and Wildlife Service.
                    Merritt Island National Wildlife Refuge. Titusville, FL. USA.
                </li>
                <li>
                    Levine, JM, Brewer, JS & MD Bertness. 1998. Nutrients, competition and plant zonation in a New England salt marsh. <i>J. Ecol.</i> 86: 285-292.
                </li>
                <li>
                    Marinucci, AC. 1982. Trophic importance of Spartina alterniflora production and decomposition to the marsh-estuarine
                    system. <i>Biol. Conserv.</i> 22: 35-58.
                </li>
                <li>
                    Mitsch, WJ & JG Gosselink. 1993. <i>Wetlands, 2nd ed.</i> Van Nosterand Reinhold. New York. USA. 722 pp.
                </li>
                <li>
                    Montague, CL. 1982. The influence of fiddler crab burrows on metabolic processes in salt marsh sediments. <i>In</i>: Kennedy, VS, ed.
                    <i>Estuarine comparisons.</i> 283-301. Academic Press. New York. USA.
                </li>
                <li>
                    Montague, CL & RG Wiegert. 1990. Salt marshes. In: Myers, RL & JJ Ewel, eds. <i>Ecosystems of Florida.</i> UCF Press. Orlando, FL. USA. 765 pp.
                </li>
                <li>
                    Odum, WE. 1988. Comparative ecology of tidal freshwater and salt marshes. <i>Ann. Rev. Ecol. Syst.</i> 19: 147-176.
                </li>
                <li>
                    Odum, WE & JK Hoover. 1988. A comparison of vascular plant communities in tidal freshwater and saltwater marshes. <i>In</i>: Hook, DD,
                    McKee Jr., WH, Smith, HK, Gregory, J, Burrell Jr., VG, DeVoe, MR, Sojka, RE, Gilbert, S, Banks, R, Stozy, LH, Brooks, C,
                    Matthews, TD & TH Shear, eds. <i>The ecology and management of wetlands, volume 1: ecology of wetlands.</i> 526-534. Croom Helm. London, UK.
                </li>
                <li>
                    Pennings, SC & MD Bertness. 2001. Salt marsh communities. In: Bertness, MD, Gaines, SD & ME Hay. <i>Marine community ecology.</i>
                    Sinauer Associates, Inc. Sunderland, MA. USA. 550 pp.
                </li>
                <li>
                    Pezeshki, SR. 1997. Photosynthesis and root growth of Spartina alterniflora in relation to root zone aeration. <i>Photosynthetica</i> 34: 107-114.
                </li>
                <li>
                    Poljakoff-Mayber, A. 1975. Morphological and anatomical changes in plants as a response to salinity stress. <i>In</i>: Poljakoff-Mayber,
                    A & J Gale, eds. <i>Plants in saline environments.</i> 97-117. Springer-Verlag. New York. USA. Ponnamperuma, FN. 1972. The chemistry of submerged soils.
                    Adv. Agron. 24: 29-95.
                </li>
                <li>
                    Poulakis, GR, Shenker, JM & DS Taylor. 2002. Habitat use by fishes after tidal reconnection of an impounded estuarine wetland
                    in the Indian River Lagoon (USA). <i>Wetlands Ecol. Manag.</i> 10: 51-69.
                </li>
                <li>
                    Provost, MW. 1949. Mosquito control and mosquito problems in Florida. <i>Proc. Annu. Meet. Calif. Mosq. Control Assoc.</i> 17th. 32-35.
                </li>
                <li>
                    Rejmanek, M, Sasser, C & GW Peterson. 1988. Hurricane-induced sediment deposition in a Gulf Coast marsh. <i>Est. Coast. Shelf Sci.</i> 27: 217-222.
                </li>
                <li>
                    Rey, JR & T Kain. 1989. <i>A guide to the salt marsh impoundments of Florida.</i> University of Florida, Florida Medical
                    Entomology Laboratory. Vero Beach, FL. USA.
                </li>
                <li>
                    Rey, JR & T Kain. 1993. <i>Coastal marsh enhancement project. Indian River National Estuary Program.</i> Final report contract CE004963-91.
                    University of Florida IFAS. Vero Beach, Florida. USA. 29 pp.
                </li>
                <li>
                    Rozema, J, Gude, H & G Pollak. 1981. An ecophysiological study of the salt secretion of four halophytes. <i>New Phytol.</i> 89: 201-217.
                </li>
                <li>
                    Rozema, J, Bijwaard, P, Prast, G & R Broekman. 1985. Ecophysiological adaptations of coastal halophytes from foredunes
                    and salt marshes. <i>Vegetatio.</i> 62: 499-521.
                </li>
                <li>
                    Schmalzer, PA. 1995. Biodiversity of saline and brackish marshes of the Indian River Lagoon: historic and current patterns. <i>Bull. Mar. Sci.</i> 57: 37-48.
                </li>
                <li>
                    Schomer, NS & RD Drew. 1982. <i>An ecological characterization of the Lower Everglades, Florida Bay and the Florida Keys.</i>
                    US Fish and Wildlife Service, Office of Biological Services; Washington, DC. FWS/OBS 82/58.1.
                </li>
                <li>
                    Schubel, JR & DJ Hirschberg. 1978. Estuarine graveyards, climatic change, and the importance of the estuarine environment. <i>In</i>: Wiley,
                    ML, ed. <i>Estuarine Interactions.</i> 285-303. Academic Press. New York. USA.
                </li>
                <li>
                    Trefry, JH, Metz, S, Trocine, RP, Iricanin, N, Burnside, D, Chen, NC & B Webb. 1990. <i>Design and operation of a muck sediment survey.</i>
                    Final report to the St. Johns River Water Management District. Available from the St. Johns River Water Management District.
                    Palatka, FL. USA.
                </li>
                <li>
                    Trefry, JH & RP Trocine. 2002. <i>Pre-dredging and post-dredging surveys of trace metals and organic substances in Turkey Creek, Florida.</i>
                    Final report to the St. Johns River Water Management District. Available from the St. Johns River Water Management District.
                    Palatka, FL. USA.
                </li>
                <li>
                    Trefry, JH, Trocine, RP & DW Woodall. 2007. Composition and sources of suspended matter in the Indian River Lagoon, Florida.
                    <i>Florida Sci.</i> 70: 363-382.
                </li>
                <li>
                    Wiegert, RG & BJ Freeman. 1990. <i>Tidal marshes of the southeast Atlantic coast: a community profile.</i> US Department of Interior,
                    Fish and Wildlife Service, Biological Report 85 (7.29). Washington, DC.
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
