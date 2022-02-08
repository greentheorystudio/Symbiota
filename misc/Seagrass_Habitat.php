<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Seagrass Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/t_testudinum_wikimedia.jpg");
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
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
