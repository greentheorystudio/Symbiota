<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title>Seagrass Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/t_testudinum_wikimedia.jpg");
            background-position: center bottom;
        }

        #innertext{
            position: sticky;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
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
            <a href="Whatsa_Habitat.php">Habitats</a> &gt;
            <b>Seagrass Beds</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Seagrass Beds</h1>
    </div>
    <div class="top-text-container">
        <h3>
            Often likened to the rich, grassy meadows of bucolic landscapes, seagrass beds are common features of coastal
            shallows and estuaries from the tropics to the Arctic Circle. Of the 72 species identified worldwide, seven are
            found in Florida—all of which grow in the Indian River Lagoon.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: J. St. John, Wikimedia Commons
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
                        <a href="#value-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">The Value of Seagrasses</span>
                        </a>
                    </li>
                    <li>
                        <a href="#threats-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Threats to Seagrass Communities</span>
                        </a>
                    </li>
                    <li>
                        <a href="#management-section" data-number="4">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Management and Restoration</span>
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
                One of these, Johnson’s seagrass (<i>Halophila johnsonii</i>) is found nowhere in the world except in the Indian
                River Lagoon and was the first threatened seagrass to be listed under the Endangered Species Act.
            </p>
            <p>
                Though often mistaken for seaweeds, seagrasses are evolved from terrestrial plants, and are the only class
                of flowering plant that is completely aquatic. Though they have leaves, roots, flowers and seeds, seagrasses
                lack strong supportive stems and trunks, an adaptation of terrestrial plants to overcome the force of gravity.
                Their natural buoyancy and flexibility allow them bend easily along with waves and currents.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/11_CorapiP2.jpg" />
                    <figcaption>
                        <i>Credit: P. Corapi</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="value-section" class="cd-section">
            <h4>The Value of Seagrasses</h4>
            <p>
                A single acre of seagrass can produce over 10 tons of leaves per year. By comparison, an acre of most grass
                forage crops like alfalfa and switchgrasses produce from 3 to 8 tons of vegetation per year. This high level
                of productivity has inspired comparisons of seagrass communities as the marine equivalent of tropical rainforests.
                In Florida, turtle grass (<i>Thalassia testudinum</i>) can grow .07 to .19 inches (2 to 5 millimeters) per day;
                manatee grass (<i>Syringodium filiforme</i>) can put on .33 inches (8.5 mm) of growth in a day; and shoal grass
                (<i>Halodule wrightii</i>) can produce one new leaf every nine days during spring.
            </p>
            <p>
                This vast biomass provides food, habitat, and nursery areas for myriad adult and juvenile vertebrates and
                invertebrates. A single acre of seagrass may support as many as 40,000 individual fish and 50 million small
                invertebrates. The dense foliage shelters numerous species of juvenile fish, smaller finfish, and benthic
                invertebrates such as crustaceans, bivalves, echinoderms. These leaves also provide abundant attachment sites
                for small macroalgae and epiphytic organisms such as sponges, bryozoans and foraminifera (forams), which
                account for up to 30 percent of the total above-ground biomass in some systems. Finally, seagrasses’ dense
                networks of underground rhizomes also serve as protection, hindering predators’ attempts to dig up the abundant
                invertebrate prey living in the sediments where seagrasses grow.
            </p>
            <div style="display:flex;justify-content: center;align-content: center;">
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/11_PriceN1.jpg" />
                    <figcaption>
                        <i>Credit: N. Price</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Bursatella_leachii_plei_L_Holly_Sweat.jpg" />
                    <figcaption>
                        <i>Credit: L. Sweat</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/06DineenJ1.jpg" />
                    <figcaption>
                        <i>Credit: J. Dineen</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Outside of their importance to biodiversity, seagrasses also offer several other valuable ecological benefits.
                Their sensitivity to changes in water quality makes them important indicator species that reflect the overall
                health of coastal ecosystems. And seagrass meadows also help dampen the effects of strong currents, providing
                protection to fish and invertebrates while also preventing the scouring of bottom areas.
            </p>
            <p>
                Economically, Florida's 2.2 million acres of seagrass support both commercial and recreational fisheries that
                provide a wealth of benefits to the state's economy. Florida's Department of Environmental Protection (DEP)
                reported that in 2000, Florida's seagrass communities supported commercial harvests of fish and shellfish
                valued at over $124 billion. Including the nutrient cycling worth of seagrasses as well as recreational fisheries,
                DEP has estimated that each acre of seagrass in Florida has an economic value of approximately $20,255 per
                year, which translates into a statewide economic benefit of $44.6 billion annually.
            </p>
            <p>
                Seagrass coverage throughout the Indian River Lagoon has been on the decline since mapping surveys began.
                In 1943, the IRL held over 71,000 acres (29,000 hectares); as of 2019, that figure had declined to just over
                32,000 acres (13,000 hectares.)
            </p>
        </div>
        <div id="threats-section" class="cd-section">
            <h4>Threats to Seagrass Communities</h4>
            <p>
                Seagrasses are subject to a number of naturally occurring stresses including storms, excessive grazing by
                herbivores and disease. Human-made threats include point and non-point sources of pollution, reduced water
                clarity, excessive nutrients in runoff, sedimentation and propeller scarring.
            </p>
            <p>
                The effect of these stresses on seagrasses is dependent on both the nature and severity of the impact. Generally,
                if only leaves and above-ground vegetation are affected, seagrasses can recover from damage within a few weeks.
                However, when roots and rhizomes are damaged, a plant’s ability to produce new growth is severely impacted;
                plants may never be able to recover.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/syringodium_filiforme_rhizome_FWSRI.jpg" />
                    <figcaption style="width:500px;">
                        <i>S. filiforme</i> rhizome creeping into an area denuded by propeller scarring. <i>Credit: Florida
                        Fish and Wildlife Research Institute</i>
                    </figcaption>
                </figure>
            </div>

            <h5>Storms, Grazing and Disease</h5>
            <p>
                Several natural processes can negatively affect seagrasses. Wind-driven waves from heavy storms can break
                or uproot seagrasses. Additionally, a number of small and large marine animals disturb seagrasses while foraging,
                including sea urchins and the endangered West Indian Manatee (<i>Trichechus manatus</i>). Other species, such as
                crabs, fishes, skates, and rays disturb rhizomes and roots, and can tear apart seagrass leaves as they forage
                for concealed or buried prey.
            </p>
            <div style="display:flex;justify-content: center;align-content: center;">
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/turtle_in_seagrass_clifton_beard_flickr.jpg" />
                    <figcaption>
                        <i>Credit: C. Beard (Flickr)</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Mr_Ray.jpg" />
                    <figcaption>
                        <i>Credit: L. Hall</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/18SprattR3.jpg" />
                    <figcaption>
                        <i>Credit: R. Spratt</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                A wasting disease, thought to be caused by a marine slime mold, caused extensive damage to <i>Zostera</i> eelgrass
                beds in temperate coastal areas during the 1930s, causing a die-off of up to 90 percent of all eelgrass in
                North America. Introduced seaweed species can also compete with and displace native seagrasses.
            </p>

            <h5>Human Impacts</h5>
            <p>
                By far, human activities have the greatest impact on Indian River Lagoon seagrasses.
            </p>
            <p>
                <b>Reduced water clarity</b>. Like land plants, seagrasses rely on sunlight, and water clarity determines how much
                light reaches their foliage. However, stormwater runoff from urban and agricultural areas carries household
                chemicals, oils, automotive chemicals, pesticides, animal wastes, and other debris, which are suspended as
                tiny particles in the water column. This clouding, or turbidity, reduces sunlight that reaches the seagrasses.
                In extreme cases, excessive sediments in runoff can even physically smother seagrasses.
            </p>
            <p>
                <b>Nutrient loading</b>. As on land, nitrogen and phosphorous are important nutrients in freshwater, brackish and
                marine environments, allowing for the growth of microalgae and phytoplankton that serve as food for a great
                many organisms. But in overabundance, as when heavy volumes of stormwater carry excess fertilizers and animal
                wastes from land, nutrient loading causes massive blooms of algae that block sunlight into the deeper reaches
                of the lagoon.
            </p>
            <p>
                Seasonal freshwater discharges from nearby Lake Okeechobee to manage lake levels also negatively impact lagoon
                water quality. In addition to reducing salinity levels, the freshwater releases also contain high levels of
                nutrients.
            </p>
            <p>
                <b>Dredging</b> to maintain boating channels churns up sediments in the water column. While most impacts are short-term,
                if dredging is of a large enough scale and affects hydrodynamic properties of the area, such as the depth profile,
                water current direction, or current velocity, seagrasses may be severely threatened by sustained reductions
                of water clarity.
            </p>
            <p>
                <b>Prop scarring</b> is caused by accidental or intentional boat groundings in shallow areas. When boaters enter water
                that is shallower than the draft of their vessel, propeller blades dig into seagrass beds as they pass over.
                This cuts not only seagrass blades but also frequently slashes underground rhizomes and roots. The resulting
                catastrophic furrow fragments the larger habitat, especially in areas where seagrass coverage is already
                sparse. Fragmented patches are susceptible to effects of erosion, and are vulnerable to increased damage as
                boaters continue to scar the meadow.
            </p>
            <div style="display:flex;justify-content: center;align-content: center;">
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Prop_Scars_on_Shallows_in_Florida_Bay_NPSPhoto.jpg" />
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/prop_scar_FL_DEP.png" />
                    <figcaption>
                        Propeller scarring in seagrass beds in Florida Bay. <i>Credit: National Park Service, Florida DEP</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="management-section" class="cd-section">
            <h4>Management and Restoration</h4>
            <p>
                As of 2015, the Indian River Lagoon had approximately 59,000 acres of seagrass coverage, down from 80,000
                in 2007, a decline of approximately 26 percent. Since the 1940s, some areas of the lagoon have completely
                lost their seagrass meadows. Some studies estimate the lagoon has lost 95 percent of its seagrasses since
                1981.
            </p>
            <p>
                As a general rule, seagrass coverage remains steady or increases in areas with relatively pristine environmental
                conditions. In the area encompassing the NASA protected zones, Merritt Island Wildlife Refuge, and Canaveral
                National Seashore, seagrass coverage has remained unchanged over the last 50 years; in the central Indian
                River Lagoon, near Sebastian Inlet, seagrass coverage has increased markedly from historic levels, though
                much of this increase is due to the opening of the inlet at its present location.
            </p>
            <p>
                By contrast, seagrass coverage in areas heavily impacted by overdevelopment of shoreline areas and wetlands
                tend to experience significant declines. In the 50-mile stretch of the IRL between the NASA Causeway and Grant,
                Florida, seagrass coverage has decreased by over 70 percent in the last 50 years.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/18BrevardZoo2.jpg" />
                    <figcaption>
                        <i>Credit: Brevard Zoo</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Managing water quality for seagrass health has improved overall water quality within the lagoon, increased
                overall amount and quality of available habitat, and is expected to increase biodiversity within seagrass
                meadows. St. Johns River Water Management District (SJRWMD) and South Florida Water Management District (SFWMD)
                are two of the organizations charged with managing water quality within the Indian River Lagoon. These organizations
                have actively pursued the goal of managing the lagoon in order to preserve and restore seagrass coverage
                to historic levels.
            </p>
            <p>
                Management efforts include large-scale projects to remove muck from the lagoon bottom, construction of stormwater
                parks to intercept thousands of pounds of nutrients before they can enter the lagoon, and a 30-year monitoring
                program to document seasonal fluctuations in seagrass populations.
            </p>
            <p>
                Seagrass restoration efforts are also widespread throughout the lagoon. Often, these projects also include
                clam planting and oyster reef rebuilding. In addition to nursery-grown material, some replanting efforts
                utilize seagrass fragments, dislodged by storms or boating activities, as starting material.
            </p>
        </div>
        <div id="further-reading-section" class="cd-section">
            <h4>Further Reading</h4>
            <ul class="further-reading-list">
                <li>
                    Almasi, M. N., C. M. Hoskin, J. K. Reed and J. Milo. 1987. Effects of natural and artificial Thalassia on rates of
                    sedimentation. J. Sedimentary Petrology 57 (5): 901-906.
                </li>
                <li>
                    Applied Biology, Inc. and Ray L. Lyerly & Associates. 1980. Biological and environmental studies at the Florida Power &
                    Light Company Cape Canaveral Plant and the Orlando Utilities Commission Indian River Plant, Volume II, Part I: Biological
                    studies. Applied Biology, Inc., Atlanta, GA and Ray L. Lyerly & Assoc., Dunedin, FL. 272 pp.
                </li>
                <li>
                    Aspden, William Clarkson. 1980. Aspects of photosynthetic carbon metabolism in seagrasses. Master's Thesis, Fla. Inst. of
                    Tech., Melbourne, FL. 75 pp.
                </li>
                <li>
                    Barile, Diane D. 1986. The Indian River Lagoon - seventy years of cumulative impacts. In: Proceedings Of The Conference:
                    Managing Cumulative Effects In Florida Wetlands, Oct 17-19, 1985, New College of Univ. S. Fla., Sarasota, FL, E.D. Esteves,
                    J. Miller, J. Morris and R. Hamman, eds., E.S.P. Publ. #38, Omnipress, Madison, WI, pp. 193-218.
                </li>
                <li>
                    Barile, Diane D., Christine A. Panico, Mary Beth Corrigan and Michael Dombrowski. 1987. Estuarine management - the Indian
                    River Lagoon. In: Coastal Zone '87: Proceedings Of The Fifth Symposium On Coastal And Ocean Management, Volume 3, Orville T.
                    Magoon, et al, eds., WW Div/ASCE, Seattle, WA/ May 26-29, 1987, Amer. Soc. of Civil Engineers, New York, NY. Pp. 237-250.
                </li>
                <li>
                    Brevard County, Florida. Office of Natural Resources Management. 1986. Seagrass maps of the Indian and Banana Rivers.
                    Brevard County Office Natural Resources Management, Merritt Island, FL. 20 pp., maps, charts.
                </li>
                <li>
                    Brevard County, Florida. Water Resources Department. 1981. Review and update of the wasteload allocations for the
                    Indian and Banana Rivers in Brevard County, Florida. Unpubl. Rep., Brevard County Water Resources Dep., Merritt Island, FL.
                </li>
                <li>
                    Carroll, Joseph D., Jr. 1983. Letter to District Engineer, U. S. Army Corps of Engineers, Jacksonville, Florida. Re:
                    Seagrass mapping of central Indian River Lagoon region, Sebastian area. Letter Correspondence, U.S. Fish & Wildlife Serv.,
                    Vero Beach, FL. 7 pp., maps.
                </li>
                <li>
                    Clark, K. B. 1975. Benthic community structure and function. In: an ecological study of the lagoons surrounding the
                    John F. Kennedy Space Center, Brevard County Florida, April 1972 to September 1975. Volume 1, experimental results and
                    conclusions, NGR 10-015-008, Fla. Inst. of Tech., Melbourne, FL.
                </li>
                <li>
                    Darovec, J. E. Jr., J. M. Carlton, T. R. Pulver, M. D. Moffler, G. B. Smith, W. K. Whitfield, Jr., C. A. Willis, K. A.
                    Steidinger and E. A. Joyce, Jr. 1975. Techniques for coastal restoration and fishery enhancement in Florida. Fla. Marine Res.
                    Publ. No. 15, Fla. Dep. of Natural Resources, Marine Res. Laboratory, St. Petersburg, FL. 27 pp.
                </li>
                <li>
                    Dawes, Clinton J. 1987. The dynamic seagrasses of the Gulf of Mexico and Florida coasts. Fla. Marine Research Publ.
                    No. 42, Proc. of Symp. on Subtropical Seagrasses of the S.E. U. S., Aug 12 1985, Michael J. Durako, Ronald C. Phillips &
                    Roy R. Lewis, III, eds., Fla. Dep. Natural Resources, Bur. Marine Research, St. Petersburg, FL.
                </li>
                <li>
                    Down, C. and R. Withrow. 1978. Vegetation and other parameters in the Brevard County bar-built estuaries. NASA-CR-158242,
                    REPT-06-73, Brevard County Health Dep., Titusville, FL. 90 pp.
                </li>
                <li>
                    Down, Cherie. 1978. Vegetation and other parameters in the Brevard County bar-built estuaries. Rep. No. 06-73, Brevard
                    County Health Dep., Environ. Eng. Dep., Brevard County, FL. 85 pp.
                </li>
                <li>
                    Down, C. 1983. Use of Aerial Imagery in Determining Submerged Features in Three East-Coast Florida Lagoons. Florida Sci. 46(3/4), 355-362.
                </li>
                <li>
                    Durako, Michael J. 1988. The seagrass bed a community under assault. Fla. Naturalist, Fall 1988, pp. 6-8.
                </li>
                <li>
                    Durako, Michael J., Ronald C. Phillips and Roy R. Lewis III, eds. 1987. Proceedings of the symposium on
                    subtropical-tropical seagrasses of the southeastern United States. Fla. Marine Res. Publ. No. 42, Fla. Dep. of
                    Natural Resources, Bur. Marine Res., St. Petersburg, FL. 209 pp.
                </li>
                <li>
                    Eiseman, N. J. 1980. An illustrated guide to the seagrasses of the Indian River region of Florida. Tech. Rep. No. 31,
                    Harbor Branch Found., Inc., Fort Pierce, FL.
                </li>
                <li>
                    Eiseman, N. J. and Calvin McMillan. 1980. A new species of seagrass, <i>Halophila johnsonii</i>, from the Atlantic coast of
                    Florida. Aquatic Botany 9: 15-19.
                </li>
                <li>
                    Eiseman, N. J. and M. C. Benz. 1975. Studies of the benthic plants of the Indian River region. In: Indian River
                    Coastal Zone Study, Second Annual Report, 1974-1975, Volume I, David K. Young, ed., Harbor Branch Consortium, Fort Pierce,
                    FL, pp. 89-103.
                </li>
                <li>
                    Eiseman, N. J., M. C. Benz, and D. E. Serbousek. 1976. Studies of the benthic plants of the Indian River region. In:
                    Indian River Coastal Zone Study, Third Annual Report, 1975-1976, Volume I, David K. Young, ed., Harbor Branch Consortium,
                    Fort Pierce, FL. Pp. 72-86.
                </li>
                <li>
                    Eiseman, N. J., M. C. Benz, and D. E. Serbousek. 1976. Studies on the benthic plants of the Indian River region. In:
                    Indian River Coastal Zone Study, Third Annual Report, 1975-1976, Volume 1, David K. Young, ed., Harbor Branch Consortium, Ft.
                    Pierce, FL. Pp. 71-86.
                </li>
                <li>
                    Eiseman, N. J., Martha Meagher, Reubin Richards and Gregg Stanton. 1974. Chapter 8. Studies on the benthic and shoreline
                    plants of the Indian River region. In: Indian River Study, First Annual Report, 1973- 1974, Volume II, David R. Young, ed.,
                    Harbor Branch Consortium, Fort Pierce, FL. Pp. 256-289.
                </li>
                <li>
                    Eiseman, N.J. 1980. An Illustrated Guide to the Sea Grasses of the Indian River Region of Florida. Harbor Branch Foundation, Inc.
                    Technical Report No. 31.  24 pages.
                </li>
                <li>
                    Fenchel, T. 1970. Studies on the decomposition of organic matter derived from turtle grass, Thalassia testudinum. Limnol. Oceanogr. 15: 14-20
                </li>
                <li>
                    Fletcher, S.W. and W.W. Fletcher. 1995. Factors Affecting Changes in Seagrass Distribution and Diversity Patterns in the
                    Indian River Lagoon Complex Between 1940 and 1992. Bulletin of Marine Science 57(1), 49-58.
                </li>
                <li>
                    Florida (State of). Department of Natural Resources. 1985. Banana River Aquatic Preserve management plan. Fla. Dep. of
                    Natural Resources, Bur. of Environ. Land Management, Division of Recreation and Parks, Tallahassee, FL. 129 pp.
                </li>
                <li>
                    Fonseca, M.S., W.J. Kenworthy, and G.W. Thayer. 1998. Guidelines for the conservation and restoration of seagrasses in
                    the United States and adjacent waters. NOAA Coastal Ocean Program Decision Analysis Series No. 12. NOAA Coastal Ocean Office.
                    Silver Spring, MD.
                </li>
                <li>
                    French, Thomas D. and John R. Montgomery. 1983. Temporal dynamics of copper chemistry in the shoal grass, <i>Halodule wrightii</i>
                    Aschers. Fla. Sci. 46 (3/4): 135-145.
                </li>
                <li>
                    French, Thomas Daniel. 1980. Temporal dynamics of copper chemistry in the shoal grass, Halodule wrightii Aschers. Master's Thesis,
                    Fla. Inst. of Tech., Melbourne, FL. 58 pp.
                </li>
                <li>
                    Fry, B. and P.L Parker. 1979. Animal diet in Texas seagrass meadows: evidence for the importance of benthic plants. Est.
                    Coast. Mar. Sci. 8: 499-509
                </li>
                <li>
                    Fuss, C.M. Jr, and J.A. Kelly, Jr. 1969. Survival and Growth of Sea Grasses Transplanted Under Artificial Conditions.
                    Bulletin of Marine Science 19(2), 351-365.
                </li>
                <li>
                    Fry, Brian and Robert W. Virnstein. 1988. Leaf production and export of the seagrass Syringodium filiforme Kutz. in
                    Indian River Lagoon, Florida. Aquatic Botany 30:261-266.
                </li>
                <li>
                    Fry, Brian. 1983. Leaf growth in the seagrass <i>Syringodium filiforme</i> Kutz. Aquatic Botany 16 (4): 361-368.
                </li>
                <li>
                    Gilbert, Steve and Kerry B. Clark. 1981. Seasonal variation in standing crop of the seagrass <i>Syringodium filiforme</i> and
                    associated macrophytes in the northern Indian River, Florida. Estuaries 4 (3): 223-225.
                </li>
                <li>
                    Gilmore, R. G. 1987. Tropical-subtropical seagrasses of the southeastern United States: Fishes and fish communities. Fla.
                    Marine Research Publ. 42: 117-137.
                </li>
                <li>
                    Gilmore, R. Grant, George R. Kulczycki, Philip A. Hastings and Wayne C. Magley. 1976. Studies of fishes of the Indian
                    River Lagoon and vicinity. In: Indian River Coastal Zone Study, Third Annual Report, 1975-1976, Volume 1, David K.
                    Young, ed., Harbor Branch Consortium, Fort Pierce, FL. Pp. 133-147.
                </li>
                <li>
                    Gilmore, R. Grant, John K. Holt, Robert S. Jones, George R. Kulczycki, Louis G. MacDowell III and Wayne C. Magley. 1978.
                    Portable tripod drop net for estuarine fish studies. Fishery Bulletin 76 (1):285-289.
                </li>
                <li>
                    Gore, Robert H., Edward E. Gallaher, Liberta E. Scotto and Kim A. Wilson. 1981. Studies on decapod Crustacea from the
                    Indian River region of Florida. XI. Community composition, structure, biomass and species-areal relationships of
                    seagrass and drift algae-associated macrocrustaceans. Estuarine, Coastal and Shelf Sci. 12 (4): 485-508.
                </li>
                <li>
                    Gore, Robert H., Linda J. Becker, Nina Blum and Liberta E. Scotto. 1976. Studies of decapod Crustacea in the Indian
                    River region of Florida. In: Indian River Coastal Zone Study, Third Annual Report, 1975-1976, Volume 1, David K. Young, ed.,
                    Harbor Branch Consortium, Fort Pierce, FL. Pp. 148-161.
                </li>
                <li>
                    Haddad, Kenneth D. 1985. Habitats of the Indian River. In: The Indian River Lagoon: Proceedings Of The Indian River
                    Resources Symposium, Diane D. Barile, ed., Marine Resources Council of E. Central Fla., Fla. Inst. of Tech.,
                    Melbourne, FL. Pp. 23-26.
                </li>
                <li>
                    Hall, M.O., and N.J. Eiseman. 1981. The Seagrass Epiphytes of the Indian River, Florida I. Species List with Descriptions
                    and Seasonal Occurrences. Botanica Marina 24, 139-146.
                </li>
                <li>
                    Hall, M.O. and S.S. Bell. 1988. Response of small motile epifauna to a complexity of epiphytic algae on seagrass blades.
                    J. Marine Research 46 (3): 613-630.
                </li>
                <li>
                    Hanlon, Roger and Gilbert Voss. 1975. Guide to the sea grasses of Florida, the Gulf of Mexico and the Caribbean region.
                    Sea Grant Field Guide Ser. No. 4, Univ. of Miami Sea Grant, Univ. of Miami, Miami, FL. 30 pp.
                </li>
                <li>
                    Harbor Cities Watershed Action Committee. 1991. Seagrass restoration in the Harbor Cities Watershed. Final rep., Harbor
                    Cities Watershed Action Committee, Conrad White, ed., Melbourne, FL. 7 pp.
                </li>
                <li>
                    Harrison, P.G. 1989. Detrital processing in seagrass systems: A review of factors affecting decay rates, remineralization, and
                    detritivory. Aquat. Bot. 35: 263-288
                </li>
                <li>
                    Heffernan, J. J., R. A. Gibson, S. F. Treat, J. L. Simon, R. R. Lewis III, R. L. Whitman, eds. 1985. Seagrass productivity
                    in Tampa Bay: A comparison with other subtropical communities. Proc. Tampa Bay Area Sci. Info. Symp. p. 247.
                </li>
                <li>
                    Heffernan, John J. and Robert A. Gibson. 1983. A comparison of primary production rates in Indian River, Florida seagrass systems.
                    Fla. Sci. 46 (3/4): 295-306.
                </li>
                <li>
                    Heijs, F.M.L. 1984. Annual biomass and production of epiphytes in three monospecific seagrass communities of
                    <i>Thalassia hemprichii</i> (Ehrenb.) Aschers. Aquat. Bot. 20: 195-218
                </li>
                <li>
                    Howard, R. K. 1983. Short term turnover of epifauna in small patches of seagrass beds within the Indian River, Florida.
                    Rep. presented at Benthic Ecology Meeting, Fla. Inst. of Tech., Melbourne, FL.
                </li>
                <li>
                    Howard, R. K. 1987. Diel variation in the abundance of epifauna associated with seagrasses of the Indian River Florida.
                    Marine Biol. 96 (1): 137-142.
                </li>
                <li>
                    Howard, Robert K. 1985. Measurements of short-term turnover of epifauna within seagrass beds using an in situ staining
                    method. Marine Ecology-Progress Ser. 22: 163-168.
                </li>
                <li>
                    Howard, Robert K. and Frederick T. Short. 1986. Seagrass growth and survivorship under the influence of epiphyte grazers.
                    Aquatic Botany 24: 287-302.
                </li>
                <li>
                    Humm, Harold J. 1964. Epiphytes of the seagrass Thalassia testudinum, in Florida. Bulletin Marine Sci. Gulf and
                    Caribbean 14 (2): 306-341.
                </li>
                <li>
                    Jensen, Paul R. and Robert A. Gibson. 1986. Primary production in three subtropical seagrass communities: A comparison
                    of four autotrophic components. Fla. Sci. 49 (3): 129-141.
                </li>
                <li>
                    Kenworthy, W. J., M. S. Fonseca, D. E. McIvor and G. W. Thayer. 1989. The submarine light regime and ecological status of
                    seagrasses in Hobe Sound, Florida. Annual Rep. National Marine Fisheries Serv., NOAA, S.E. Fisheries Cent., Beaufort
                    Laboratory, Beaufort, NC.
                </li>
                <li>
                    Kulczycki, George R., Robert W. Virnstein and Walter G. Nelson. 1981. The relationship between fish abundance and algal
                    biomass in a seagrass-drift algae community. Estuarine, Coastal and Shelf Sci. 12 (3): 341-347.
                </li>
                <li>
                    Lewis, R.R. III. 1987. The Restoration and Creation of Seagrass Meadows in the Southeast United States. Florida
                    Marine Research Publications 42, 153-173.
                </li>
                <li>
                    Livingston, R.J. 1987. Historic Trends of Human Impacts on Seagrass Meadows in Florida. Florida Marine Research
                    Publications 42, 139-151.
                </li>
                <li>
                    Marine Resources Council of East Florida. 1987. Marine Resources Council, third annual meeting, land and water
                    planning. Symposium abstr., Marine Resources Council, Fla. Inst. Tech., Melbourne, FL. 17 pp.
                </li>
                <li>
                    Martin County Conservation Alliance. 1992. The environmental health of the estuarine waters of Martin County. Martin
                    County Conserv. Alliance, Stuart, FL. 53 pp.
                </li>
                <li>
                    McMillan, C. 1982. Reproductive Physiology of Tropical Seagrasses. Aquatic Botany 14, 245-258.
                </li>
                <li>
                    McMillan, C. and F.N. Moseley. 1967. Salinity Tolerances of Five Marine Spermatophytes of Redfish Bay, Texas. Ecology 48(3), 503-506.
                </li>
                <li>
                    McRoy, C.P. and S. Williams-Cowper. 1978. Seagrasses of the United States: an ecological review in relation to
                    human activities. US Fish and Wildlife Service FWS/OBS.
                </li>
                <li>
                    Mendonca, M.T. 1983. Movements and feeding ecology of immature green turtles Chelonia mydas in a Florida lagoon. <i>Copeia</i> 4: 1013-1023.
                </li>
                <li>
                    Moffler, M.D. and M.J. Durako. 1987. Reproductive Biology of the Tropical-Subtropical Seagrasses of the Southeastern
                    United States. Florida Marine Research Publications 42, 77-88.
                </li>
                <li>
                    Moore, Donald R. 1963. Distribution of the sea grass, <i>Thalassia</i>, in the United States. Bulletin Marine Sci. Gulf and
                    Caribbean 13(2): 329-342.
                </li>
                <li>
                    Morgan, M.D. and C.L. Kitting. 1984. Productivity and utilization of the seagrass Halodule wrightii and its attached
                    epiphytes. Limnol. Oceanogr. 29: 1099-1176
                </li>
                <li>
                    Nelson, Walter G. 1980. A comparative study of amphipods in seagrasses from Florida to Nova Scotia. Bulletin Marine
                    Sci. 30 (1): 80-89.
                </li>
                <li>
                    Nelson, Walter G. 1981. Experimental studies of decapod and fish predation on seagrass macrobenthos. Marine Ecology-Progress
                    Ser. 5 (2): 141-149.
                </li>
                <li>
                    Odum, E.P. and A.A. de la Cruz. 1963. Detritus as a major component of ecosystems. Bull. Am. Inst. Biol. Sci. 13: 39-40
                </li>
                <li>
                    Packard, J. M. 1984. Impact of manatees <i>Trichecus manatus</i> on seagrass communities in eastern Florida. Acta Zoological
                    Fennica No. 172, pp. 21-22.
                </li>
                <li>
                    Penhale, P.A. 1977. Macrophyte-epiphyte biomass and productivity in an eelgrass (<i>Zostera marina</i>) L. community.
                    J. Exp. Mar. Biol. Ecol. 26: 211-224
                </li>
                <li>
                    Phillips, R. C. 1960. Observations on the ecology and distribution of the Florida seagrasses. Prof. Paper Ser. No. 2,
                    Fla. State Board of Conserv., Marine Laboratory, St. Petersburg, FL. 72 pp.
                </li>
                <li>
                    Phillips, R.C. 1967. On species of the seagrass, <i>Halodule</i>, in Florida. Bulletin of Marine Sci. 17 (3): 672-676.
                </li>
                <li>
                    Phillips, R.C. 1976. Preliminary Observations on Transplanting and A Phenological Index of Seagrasses. Aquatic Botany 2, 93-101.
                </li>
                <li>
                    Phillips R.C. and E.G. Menez. 1988. Seagrasses. Smithsonian Institution Press. Washington, D.C.
                </li>
                <li>
                    Post, Buckley, Schuh and Jernigan, Inc. 1982. Environmental and cost-benefit analyses of discharge alternatives for
                    Harris Corporation facilities in Palm Bay, Florida. Unpubl. Rep., Post, Buckley, Schuh and Jernigan, Inc., Orlando,
                    FL. 122 pp. Maps, figures, refs.
                </li>
                <li>
                    Rice, John D., Robert P. Trocine and Gary N. Wells. 1983. Factors influencing seagrass ecology in the Indian River
                    Lagoon. Fla. Sci. 46 (3/4): 276-286.
                </li>
                <li>
                    Salituri, Jeff Robert. 1975. A study of thermal effects on the growth of manatee grass, Cymodoceum manatorum. Master's
                    Thesis, Fla. Inst. of Tech., Melbourne, FL. 67 pp.
                </li>
                <li>
                    Sargent, F.J., T.J. Leary, D.W. Crewz, and C.R. Kruer. 1995. Scarring of Florida's seagrasses: assessment and
                    management options. Florida Marine Research Institute Technical report TR-1. St. Petersburg, Florida.
                </li>
                <li>
                    Short, F. T. and C. Zimmermann. 1983. The daylight cycle of a seagrass environment. Unpubl. Rep., presented at
                    Benthic Ecology Meeting, Fla. Inst. Tech., Melbourne, FL.
                </li>
                <li>
                    Short, Frederick T. 1985. A method for the culture of tropical seagrasses. Aquatic Botany 22 (2): 187-193.
                </li>
                <li>
                    Snodgrass, Joel W. 1990. Comparison of fishes occurring in monospecific stands of algae and seagrass. Master's Thesis,
                    Univ. of Central Fla., Orlando, FL. 51 pp.
                </li>
                <li>
                    Stephens, F. Carol and Robert A. Gibson. 1976. Studies of epiphytic diatoms in the Indian River, Florida. In: Indian River
                    Coastal Zone Study, Third Annual Report, 1975-1976, Volume 1, David K. Young, ed., Harbor Branch Consortium, Ft.
                    Pierce, FL. Pp. 61-70.
                </li>
                <li>
                    Stoner, A. W. 1980. Perception and choice of substratum by epifaunal amphipods associated with seagrasses. Marine
                    Ecology-Progress Ser. 3: 105-111.
                </li>
                <li>
                    Stoner, Allan W. 1982. The influence of benthic macrophytes on the foraging behavior of pinfish, <i>Lagodon rhomboides</i>
                    (Linnaeus). J. of Experimental Marine Biol. and Ecology 58: 271-284.
                </li>
                <li>
                    Stoner, Allan W. 1983. Distribution of fishes in sea grass meadows: Role of macrophyte biomass and species composition.
                    Fishery Bulletin 81 (4): 837-846.
                </li>
                <li>
                    Stoner, Allan W. 1983. Distributional ecology of amphipods and tanaidaceans associated with three sea grass species.
                    J. Crustacean Biol. 3 (4): 505-518.
                </li>
                <li>
                    Thompson, M. John. 1976. Photomapping and species composition of the seagrass beds in Florida's Indian River estuary. Tech.
                    Rep. No. 10, Harbor Branch Found., Inc., Fort Pierce, FL. 34 pp, maps.
                </li>
                <li>
                    Thompson, M. John. 1978. Species composition and distribution of seagrass beds in the Indian River lagoon, Florida. Fla.
                    Sci. 41 (2): 90-96.
                </li>
                <li>
                    Thorhaug, A. 1990. Restoration of mangroves and seagrasses: economic benefits for fisheries and mariculture. In:
                    Environmental restoration: science and strategies for restoring the Earth. Island Press. Washington, D.C. Volume 265.
                </li>
                <li>
                    Tomasko, D.A. and B.E. Lapointe. 1991. Productivity and biomass of <i>Thalassia testudinum</i> as related to water
                    column nutrient availability and epiphyte levels: field observations and experimental studies. Mar. Ecol. Prog.
                    Ser. 75: 9-16
                </li>
                <li>
                    van Breedveld, J. F. 1975. Transplanting of seagrass with emphasis on the importance of substrate. Fla. Marine Res.
                    Publ. No. 17, Fla. Dep. of Natural Resources, Marine Res. Laboratory, St. Petersburg, FL. 26 pp.
                </li>
                <li>
                    Virnstein, R.W., P.S. Mikkelsen, K.D. Cairns, and M.A. Capone. 1983. Seagrass Beds Versus Sand Bottoms: The Trophic
                    Importance of their Associated Benthic Invertebrates. Florida Sci. 46(3/4), 363-381.
                </li>
                <li>
                    Virnstein, Robert W. and Patricia A. Carbonara. 1985. Seasonal abundance and distribution of drift algae and
                    seagrasses in the mid-Indian River lagoon, Florida. Aquatic Botany 23 (1): 67-82.
                </li>
                <li>
                    Virnstein, R.W. and K.D. Cairns. 1986. Seagrass Maps of the Indian River Lagoon. Final Report to DER, September 1986.
                    Seagrass Ecosystems Analysts, 805 E. 46th Place, Vero Beach, Florida. 27 Pages.
                </li>
                <li>
                    Virnstein, R.W. 1987. Seagrass-associated Invertebrate Communities of the Southeastern U.S.A.: A Review. Florida
                    Marine Research Publications 42, 89-116.
                </li>
                <li>
                    Virnstein, R.W. 1995a. Seagrass Landscape Diversity in the Indian River Lagoon, Florida: The Importance of Geographic
                    Scale and Pattern. Bulletin of Marine Science 57(1): 67-74.
                </li>
                <li>
                    Virnstein, R.W. 1995b. Anomalous Diversity of Some Seagrass-Associated Fauna in the Indian River Lagoon, Florida.
                    Bulletin of Marine Science 57(1): 75-78.
                </li>
                <li>
                    Virnstein, R.W. and C. Curran. 1983. Epifauna of artificial seagrass: Colonization patterns in time and space. Unpubl.
                    Rep. presented at Benthic Ecology Meeting, Fla. Inst. Tech., Melbourne, FL.
                </li>
                <li>
                    Virnstein, R.W., K.D. Cairns, M.A. Capone and P.S. Mikkelsen. 1985. Harbortown Marina seagrass study - a report to
                    Old Park Investments, Inc. Unpubl. Tech. Rep. No. 55, Harbor Branch Found., Inc., Fort Pierce, FL. 5 pp., 8 tables.
                </li>
                <li>
                    Virnstein, Robert W. 1978. Why there are so many animals in seagrass beds, and does abundance imply importance?
                    Fla. Sci. 41 (Suppl. 1): 24. (abstract)
                </li>
                <li>
                    Virnstein, Robert W. 1982. Leaf growth rate of the seagrass <i>Halodule wrightii</i> photographically measured in situ.
                    Aquatic Botany 12 (3): 209-218.
                </li>
                <li>
                    Virnstein, Robert W. 1990. Seagrasses as a barometer of ecosystem health. Abstract, Eighth Annual Coastal Management
                    Seminar, Dec 1990, Univ. Fla., Inst. Food & Agricultural Sci., Cooperative Extension Serv., Ft. Pierce, FL.
                </li>
                <li>
                    Virnstein, Robert W. and Mary Carla Curran. 1986. Colonization of artificial seagrass versus time and distance from
                    source. Marine Ecology-Progress Ser. 29: 279-288.
                </li>
                <li>
                    Virnstein, Robert W. and Robert K. Howard. 1987. Motile epifauna of marine macrophytes in the Indian River Lagoon,
                    Florida. I. Comparisons among three species of seagrasses from adjacent beds. Bulletin of Marine Sci. 41 (1): 1-12.
                </li>
                <li>
                    Virnstein, Robert W. and Robert K. Howard. 1987. Motile epifauna of marine macrophytes in the Indian River lagoon,
                    Florida. II. Comparisons between drift algae and three species of seagrasses. Bulletin Marine Sci. 41 (1): 13-26.
                </li>
                <li>
                    Virnstein, Robert W., John R. Montgomery and Wendy A. Lowery. 1987. Effects of nutrients on seagrass. In: CM167
                    Final Report, Impoundment Management, Indian River County Mosquito Control Dist., Vero Beach, FL, Sep 30 1987, pp. 56-71.
                </li>
                <li>
                    White, C.B. 1986. Seagrass Maps of the Indian & Banana Rivers. Brevard County Office of Natural Resources Management,
                    Merritt Island, Florida.
                </li>
                <li>
                    Young, D.K. and M.W. Young. 1977. Community structure of the macrobenthos associated with seagrass of the Indian
                    River estuary, Florida. In: Ecology of Marine Benthos, B.C. Coull, ed., Univ. of S. Carolina Press, Columbia, SC. Pp. 359-381.
                </li>
                <li>
                    Young, D.K., K.D. Cairns, MA. Middleton, J. E. Miller and M.W. Young. 1976. Studies of seagrass-associated macrobenthos
                    of the Indian River. In: Indian River Coastal Zone Study, Third Annual Report, 1975-1976, Volume 1, David K. Young, ed.,
                    Harbor Branch Consortium, Fort Pierce, FL. Pp. 93-108.
                </li>
                <li>
                    Young, David K., Martin A. Buzas and Martha W. Young. 1976. Species densities of macrobenthos associated with
                    seagrass: A field experimental study of predation. J. Marine Res. 34 (4): 577-592.
                </li>
                <li>
                    Young, David K., ed. 1976. Indian River coastal zone study. Third annual report. 1975-1976. A report on research
                    progress October 1975-October 1976. Harbor Branch Consortium, Fort Pierce, FL. 187 pp.
                </li>
                <li>
                    Zieman, J.C. 1982. The Ecology of the Seagrasses of South Florida: A Community Profile. U.S. Fish and Wildlife Services,
                    Office of Biological Services, Washington, D.C. FWS/OBS-82/25. 158 Pages.
                </li>
                <li>
                    Zieman, J.C., R. Orth, R. Phillips, G. Thayer, and A. Thorhaug. 1984. The effects of oil on seagrass ecosystems. In:
                    Recovery and Resoration of Marine Ecosystems, edited by J. Cairns and A. Buikema. Butterworth Publications, Stoneham,
                    MA. Pps. 37 - 64. Seagrasses of the Southeastern United States 1960-1985. Florida Marine Research Publications 42, pp. 53-76.
                </li>
                <li>
                    Zimmerman, R.J., R.A. Gibson and J.B. Harrington. 1976. The food and feeding of seagrass-associated Gammaridean
                    amphipods in the Indian River. In: Indian River Coastal Zone Study, Third Annual Report, 1975-1976, Volume 1, David K.
                    Young, ed., Harbor Branch Consortium, Fort Pierce, FL. Pp. 87-92.
                </li>
                <li>
                    Zimmermann, Carl F. and John R. Montgomery. 1984. Effects of a decomposing drift algal mat on sediment pore water
                    nutrient concentrations in a Florida seagrass bed. Marine Ecology Progress Ser. 19 (3): 299-302.
                </li>
                <li>
                    Zimmermann, Carl F., John R. Montgomery and Paul R. Carlson. 1985. Variability of dissolved reactive phosphate flux
                    rates in nearshore estuarine sediments: Effects of groundwater flow. Estuaries 8 (2B): 228-236.
                </li>
                <li>
                    Zimmermann, Carl F., Thomas D. French and John R. Montgomery. 1981. Transplanting and survival of the seagrass
                    <i>Halodule wrightii</i> under controlled conditions. N.E. Gulf Sci. 4 (2): 131-136.
                </li>
            </ul>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
include_once(__DIR__ . '/../config/footer-includes.php');
?>
</body>
</html>
