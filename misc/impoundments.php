<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Mosquito Impoundments</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/12_PowellD2.jpg");
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
        <img class="logo-image" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/janky_mangrove_logo_med.png" />
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
            <b>Mosquito Impoundments</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Mosquito Impoundments</h1>
    </div>
    <div class="top-text-container">
        <h3>
            For humans, Florida's mangroves and salt marshes have historically been problem areas in one important respect:
            they are preferred breeding habitat for salt marsh mosquitoes. Impoundments were a solution—but one with several
            important ecological side effects.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: D. Powell
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
                        <a href="#history-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">History</span>
                        </a>
                    </li>
                    <li>
                        <a href="#improved-impoundments-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Improved Impoundments</span>
                        </a>
                    </li>
                    <li>
                        <a href="#negative-impacts-section" data-number="4">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Negative Impacts</span>
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
                Salt marsh mosquitoes (<i>Aedes taeniorhynchus</i> and <i>A. sollicitans</i>) are nuisance species that affect the health
                of both humans and domestic animals. They do not reproduce by laying their eggs in standing water. Rather,
                they deposit eggs in the moist soils of high marsh above the water line in tidal wetlands. Eggs will remain
                dormant, often for long periods of time, until water levels rise in response to rains or tides.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/aedes_taeniorhynchus_sean_mccann_flickr.jpg" />
                    <figcaption>
                        <i>Credit: S. McCann, Flickr</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Mosquito control impoundments are areas of salt marsh or mangrove forest that have been diked to allow control
                of water levels for mosquito mitigation. Within the dikes, perimeter ditches are flooded artificially in order
                to control breeding and reproduction of salt marsh mosquitoes without the use of pesticides.
            </p>
            <p>
                Today, 192 impoundments are active on the east coast of Florida. Many of these areas are closed systems with
                wide variations in salinity. This, along with the flooding process, can cause dieback of natural vegetation
                and establishment of species that thrive in lower-salinity conditions. Breeding and spawning behaviors of
                fishes and invertebrates can also be restricted in closed systems.
            </p>
            <div style="clear:both;">
                <figure style="float: left;margin-right: 30px;">
                    <img style="border:0;width:300px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Dusky_Seaside_Sparrow.jpg" />
                    <figcaption style="width:300px;">
                        Dusky seaside sparrow. <i>Credit: P.W. Sykes, Wikimedia</i>
                    </figcaption>
                </figure>
                <p>
                    One of the most devastating effects of impounding was to the dusky seaside sparrow (<i>Ammodramas maritimus nigrescens</i>),
                    which was declared extinct in 1987.
                </p>
            </div>
        </div>
        <div id="history-section" class="cd-section">
            <h4>History</h4>
            <p>
                Before mosquito control methods like impoundments were in place, mosquito landings were among the highest
                densities ever recorded in the continental United States, reaching 500 per person per minute in some areas
                of Florida.
            </p>
            <p>
                Concerted efforts aimed at controlling salt marsh mosquitoes in the Indian River Lagoon began in the mid-1920s
                with construction of miles of hand-dug, parallel ditches. But as the ditches required much maintenance and
                the tides were of low amplitude, little mosquito control was achieved.
            </p>
            <p>
                In the 1930s, field experiments demonstrated that controlling water levels through impoundment could reduce
                mosquito populations by controlling reproduction. However, problematic water losses due to seepage and evaporation
                led to the abandonment of the impoundment strategy in favor of pesticides such as DDT. By the 1950s, concerns
                over pesticide resistance in insects began to emerge, and the focus of mosquito control again shifted back
                to source reduction.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;clear:both;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/mosquito-ditch-digging.jpg" />
                    <figcaption style="width:500px;">
                        Workers prepare a drainage ditch for mosquito control in the 1920s. <i>Credit: J. Thurlow-Lippisch</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                The first impoundments in Florida were built in Brevard County in 1954, with other counties soon following.
                By the 1970s, more than 40,000 acres of Florida's coastal wetlands had been impounded—an area roughly equivalent
                to the land area of Liechtenstein. The majority of impoundments were constructed at the mean high-water level
                and then flooded year-round, which closed them off from adjacent estuarine waters. Others were allowed to
                drain during the winter months, but were flooded again as mosquito breeding season approached.
            </p>
        </div>
        <div id="improved-impoundments-section" class="cd-section">
            <h4>Improved Impoundments</h4>
            <p>
                In the 1960s, in an effort to reduce impoundment impacts, natural resources managers experimented with seasonal
                flooding during peak mosquito breeding season. The rest of the year, dike culverts remained open to allow
                natural tidal fluctuation and flushing.
            </p>
            <figure style="float: left;margin-right: 30px;">
                <img style="border:0;width:300px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/impound.jpg" />
            </figure>
            <p>
                In 1974, seasonal impoundment was combined with active water management. Allowing tides to flush impoundments
                had several positive effects: continued control of salt marsh mosquitoes, retention of black mangroves and
                other vegetation, and the return of juvenile fishes to nursery areas unavailable to them in closed impoundments.
                This management strategy is currently referred to as Rotational Impoundment Management (RIM).
            </p>
            <p>
                RIM has proven to be an effective strategy for controlling mosquitoes while minimizing serious environmental
                impacts to estuaries. Estuaries retain many of their natural functions, and their primary productivity can
                rival that of unaltered wetlands.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/impoundment_flooding_LHS.jpg" />
                    <figcaption style="width:500px;">
                        An IRL impoundment area is flooded via a culvert connecting it to the larger estuary. <i>Credit: L. Sweat</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Culverts remain open between the impoundment and the estuary from October to May, and allow water exchange
                and use of impoundments by transient fish species and invertebrates. In summer, culverts are closed and impoundments
                flooded to the minimum levels needed to prevent egg laying by salt marsh mosquitoes. Low areas of the surrounding
                dike, called spillways, ensure that water levels do not exceed prescribed levels, thus preventing injury
                to vegetation.
            </p>
            <p>
                RIM is currently the most commonly employed management strategy in three of the five counties adjacent to
                the Indian River Lagoon. Combined, St. Lucie, Brevard and Indian River counties manage nearly 6,400 acres
                of impoundments under this strategy.
            </p>
        </div>
        <div id="negative-impacts-section" class="cd-section">
            <h4>Negative Impacts</h4>

            <h5>Water Levels</h5>
            <p>
                While only a thin film of water is enough to prevent salt marsh mosquitoes from laying eggs, impoundments
                are typically flooded to depths of 6 to 20 inches (15 to 50 cm) above the ground surface to compensate for
                evaporation effects. In closed impoundments, this practice eliminated some species such as saltwort (<i>Batis maritima</i>),
                and glasswort (<i>Salicornia bigelovii</i>, and <i>S. virginica</i>). And though black mangroves’ pneumatophores enable
                aeration of roots during short periods of flooding, the short structures cannot withstand prolonged and
                deep flooding.
            </p>

            <h5>Water Quality</h5>
            <p>
                Closed impoundment significantly impacts soil chemistry and water quality. In soils, oxygen concentrations
                can decrease, while nitrogen and sulfide concentrations rise. Water effects are myriad. Some impoundments
                were subject to hypersaline conditions when estuarine waters were pumped in to flood them during warm summer
                months. Because these impoundments were closed to adjacent waters, lack of flushing and evaporation resulted
                in extremely high salinities, which caused local extinctions of some species. In other impoundments flooded
                with artesian well water, ecological turnover resulted, shifting from halophytic to freshwater communities.
            </p>

            <h5>Salinity Fluctuations</h5>
            <p>
                Excessive freshwater flows from storms, as well as runoff from agricultural and developed areas, can cause
                extreme salinity fluctuations in the Indian River Lagoon estuary. Continuous exposure to lower salinity can
                deplete populations of shallow burrowing organisms, resulting in damaging effects on food web dynamics.
            </p>

            <h5>Effects on Fish and Invertebrates</h5>
            <p>
                Several fish species have been greatly impacted by closed impoundments, particularly those that rely on mangrove
                or salt marsh for nursery grounds. Important commercial and recreational fisheries have also experienced
                declines, including tarpon, ladyfish, common snook and mullet. Marine invertebrates were also impacted by
                isolation of impounded wetlands, with biodiversity and species abundance becoming more characteristic of
                freshwater wetlands than marine or estuarine wetlands in some areas.
            </p>

            <h5>Nutrient Flow</h5>
            <p>
                In unaltered systems, nutrients from mangrove leaf fall, which are decomposed into particulate and dissolved
                forms, are utilized in a variety of ways by many different organisms as mangroves are flushed by tides. In
                closed impoundments, natural patterns of nutrient flow between mangrove areas and adjacent waters are interrupted.
                Lacking the connection to estuarine waters, nutrients are never flushed from mangrove areas and remain confined
                within impoundments.
            </p>
        </div>
        <div id="further-reading-section" class="cd-section">
            <h4>Further Reading</h4>
            <ul class="further-reading-list">
                <li>
                    Brockmeyer, R.E., J.R. Rey, R.W. Virnstein, R.G. Gilmore, Jr., and L. Earnest. 1997. Rehabilitation of impounded
                    estuarine wetlands by hydrologic reconnection to the Indian River Lagoon, Florida. Journal of Wetlands Ecology
                    and Management. 4:93-109.
                </li>
                <li>
                    Carlton, J.M. 1975. A guide to common salt marsh and mangrove vegetation. Florida Marine Resources Publications,
                    No. 6. Carlton, 1977. A survey of selected coastal vegetation communities of Florida. Florida Marine Research
                    Publications, No. 30.
                </li>
                <li>
                    Feller, I. C., Ed. 1996. Mangrove Ecology Workshop Manual. A Field Manual for the Mangrove Education and Training
                    Programme for Belize. Marine Research Center, University College of Belize, Calabash Cay, Turneffe Islands. Smithsonian
                    Institution, Washington DC.
                </li>
                <li>
                    Gilmore, R.G. Jr., D.W. Cooke, and C.J. Donahue. 1982. A comparison of the fish populations and habitat in open and
                    closed salt marsh impoundments in east central Florida. Northeast Gulf Science, 5:25-37.
                </li>
                <li>
                    Gilmore, R.G. Jr. and S.C. Snedaker. 1993. Chapter 5: Mangrove Forests. In: W.H. Martin, S.G. Boyce and A.C. Echternacht,
                    eds. Biodiversity of the Southeastern United States: Lowland Terrestrial Communities. John Wiley and Sons, Inc. Publishers.
                    New York, NY. 502 pps.
                </li>
                <li>
                    Harrington, R.W. and E.S. Harrington. 1961. Food selection among fishes invading a high subtropical salt marsh; from onset
                    of flooding through the progress of a mosquito brood. Ecology, 42:646-666.
                </li>
                <li>
                    Heald, E.J. and W.E. Odum. 1970. The contribution of mangrove swamps to Florida fisheries. Proceedings Gulf and
                    Caribbean Fisheries Institute, 22:130-135.
                </li>
                <li>
                    Heald, E.J., M.A. Roessler, and G.L. Beardsley. 1979. Litter production in a southwest Florida black mangrove community. Proceedings
                    of the Florida Anti-Mosquito Association 50th Meeting. Pp. 24-33.
                </li>
                <li>
                    Hull, J.B. and W.E. Dove. 1939. Experimental diking for control of sand fly and mosquito breeding in Florida saltwater marshes.
                    Journal of Economic Entomology, 32:309-312.
                </li>
                <li>
                    Lahmann, E. 1988. Effects of different hydrologic regimes on the productivity of Rhizophora mangle L. A case study of
                    mosquito control impoundments in Hutchinson Island, St. Lucie County, Florida. Ph.D. dissertation, University of Miami,
                    Coral Gables, Florida.
                </li>
                <li>
                    Lewis, R.R., III, R.G. Gilmore, Jr., D.W. Crewz, and W.E. Odum. 1985. Mangrove habitat and fishery resources of
                    Florida. In: W. Seaman, Jr. (ed.). Florida Aquatic Habitat and Fishery Resources. American Fisheries Society, Florida
                    Chapter, Kissimmee, FL.
                </li>
                <li>
                    Lugo, A.E. and S.C. Snedaker. 1974. The ecology of mangroves. Annual Review of Ecology and Systematics 5:39-64.
                </li>
                <li>
                    Lugo, A.E., M. Sell, and S.C. Snedaker. 1976. Mangrove ecosystem analysis. In: Systems Analysis and Simulation in
                    Ecology, B.C. Patten, ed. Pp. 113-145. Academic Press, New York, NY.
                </li>
                <li>
                    Odum, W.E. and C.C. McIvor. 1990. Mangroves. In: Ecosystems of Florida, RL. Myers and J.J. Ewel, eds. Pp. 517 - 548.
                    University of Central Florida Press, Orlando, FL.
                </li>
                <li>
                    Odum, W.E., C.C. McIvor, and T.J. Smith III. 1982. The ecology of the mangroves of south Florida: a community profile. U.S.
                    Fish and Wildlife Service, Office of Biological Services, FWS/OBS-81-24.
                </li>
                <li>
                    Odum, W.E. and E.J. Heald. 1972. Trophic analyses of an estuarine mangrove community. Bulletin of Marine Science, 22(3):671-738.
                </li>
                <li>
                    Onuf, C.P., J.M. Teal, and I. Valiela. 1977. Interactions of nutrients, plant growth and herbivory in a mangrove
                    ecosystem. Ecology, 58:514-526.
                </li>
                <li>
                    Platts, N.G., S.E. Shields, and J.B. Hull. 1943. Diking and pumping for control of sand flies and mosquitoes in
                    Florida salt marshes. Journal of Economic Entomology, 36:409-412.
                </li>
                <li>
                    Pool, D.J., A.E. Lugo, and S.C. Snedaker.1975. Litter production in mangrove forests of southern Florida and Puerto Rico.
                    Proceeding of the International Symposium on Biological Management of Mangroves, G. Walsh, S. Snedaker and H. Teas, eds.
                    Pp. 213-237. University of Florida Press, Gainesville, FL.
                </li>
                <li>
                    Provost, M.W. 1976. Tidal datum planes circumscribing salt marshes. Bulletin of Marine Science, 26:558-563.
                </li>
                <li>
                    Rey, J.R. and T. Kain. 1990. Guide to the salt marsh impoundments of Florida. Florida Medical Entomology Laboratory
                    Publications, Vero Beach, FL.
                </li>
                <li>
                    Rey, J.R., J. Schaffer, D. Tremain, R.A. Crossman, and T. Kain. 1990. Effects of reestablishing tidal connections in
                    two impounded tropical marshes on fishes and physical conditions. Wetlands. 10:27-47.
                </li>
                <li>
                    Rey, J.R. M.S. Peterson, T. Kain, F.E. Vose, and R.A. Crossman. 1990. Fish populations and physical conditions in
                    ditched and impounded marshes in east-central Florida. N.E. Gulf Science, 11:163-170.
                </li>
                <li>
                    Rey, J.R., R.A. Crossman, M. Peterson, J. Shaffer and F. Vose. 1991. Zooplankton of impounded marshes and shallow areas
                    of a subtropical lagoon. Florida Scientist, 54:191-203.
                </li>
                <li>
                    Rey, J.R., R.A. Crossman, T. Kain, and J. Schaffer. 1991. Surface water chemistry of wetlands and the Indian River Lagoon,
                    Florida, USA. Journal of the Florida Mosquito Control Association, 62:25-36.
                </li>
                <li>
                    Rey, J.R., T. Kain and R. Stahl. 1991. Wetland impoundments of east-central Florida. Florida Scientist, 54:33-40.
                </li>
                <li>
                    Rey, J.R. and C.R. Rutledge, 2001. Mosquito Control Impoundments. Document # ENY-648, Entomology and Nematology Department,
                    Florida Cooperative Extension Service, Institute of Food and Agricultural Sciences, University of Florida. Available on the
                    Internet at :  <a href="https://edis.ifas.ufl.edu" target="_blank">https://edis.ifas.ufl.edu</a>.
                </li>
                <li>
                    Simberloff, D.S. 1983. Mangroves. In: Costa Rican Natural History. D.H. Janzen, ed. Pp. 273-276. University of Chicago
                    Press, Chicago, IL.
                </li>
                <li>
                    Snedaker, S.C. 1989. Overview of mangroves and information needs for Florida Bay. Bulletin of Marine Science, 44(1):341-347.
                </li>
                <li>
                    Snedaker, S. C., and A.E. Lugo. 1973. The role of mangrove ecosystems in the maintenance of environmental quality and a
                    high productivity of desirable fisheries. Final report to the Bureau of Sport Fisheries and Wildlife in fulfillment of
                    Contract no. 14-16-008-606. Center for Aquatic Sciences, Gainesville, FL.
                </li>
                <li>
                    Snelson, F.F. 1976. A study of a diverse coastal ecosystem on the Atlantic coast of Florida, Vol. 1., Ichthyological
                    Studies. NGR-10-019-004 NASA, Kennedy Space Center, Florida.
                </li>
                <li>
                    Thayer, G.W., D.R. Colby, and W.F. Hettler Jr. 1987. Utilization of the red mangrove prop roots habitat by fishes in South
                    Florida. Marine Ecology progress Series, 35:25-38.
                </li>
                <li>
                    Tomlinson, P.B. 1986. The botany of mangroves. Cambridge University Press, London.
                </li>
                <li>
                    Waisel, Y. 1972. The biology of halophytes. Academic Press, New York, NY.
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
