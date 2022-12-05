<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Mangrove Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/1_09_WhiticarJ2.jpg");
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
            <a href="Whatsa_Habitat.php">Habitats</a> &gt;
            <b>Mangroves</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Mangroves</h1>
    </div>
    <div class="top-text-container">
        <h3>
            One of Florida’s true natives, mangroves are uniquely evolved to leverage the state’s many miles of low-lying
            coastline for their benefit. Flooded twice daily by ocean tides, or fringing brackish waters like the Indian
            River Lagoon, mangroves are bristling with adaptations that allow them to thrive in water up to 100 times saltier
            than most plants can tolerate.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: J. Whiticar
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
                        <a href="#mangrove-species-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Mangrove Species</span>
                        </a>
                    </li>
                    <li>
                        <a href="#mangrove-distribution-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Mangrove Distribution</span>
                        </a>
                    </li>
                    <li>
                        <a href="#adaptations-section" data-number="4">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Adaptations</span>
                        </a>
                    </li>
                    <li>
                        <a href="#forests-section" data-number="5">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Types of Mangrove Forests</span>
                        </a>
                    </li>
                    <li>
                        <a href="#environmental-benefits-section" data-number="6">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Environmental Benefits</span>
                        </a>
                    </li>
                    <li>
                        <a href="#ecological-role-section" data-number="7">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Ecological Role</span>
                        </a>
                    </li>
                    <li>
                        <a href="#nutrients-section" data-number="8">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Nutrients</span>
                        </a>
                    </li>
                    <li>
                        <a href="#associated-section" data-number="9">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Associated Plants and Animals</span>
                        </a>
                    </li>
                    <li>
                        <a href="#further-reading-section" data-number="10">
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
                Of Florida’s estimated 469,000 acres of mangrove forests, the Indian River Lagoon contains roughly 8,000
                acres of mangroves, which play critical ecological roles as fish nurseries, shoreline stabilizers, and pollution
                mitigators.
            </p>
            <p>
                However, an estimated 76 to 85 percent of this acreage has become inaccessible as nursery habitat for local
                fisheries through construction of mosquito ditches and impoundments. Though mangroves are protected in Florida
                by state law, mangrove habitat loss is a problem globally as these edge ecosystems are removed to make way for
                commercial and agricultural development.
            </p>
            <p>
                Worldwide, more than 50 species of mangroves exist. In Florida, the mangrove community consists of three
                main species of true mangroves: the red mangrove, <a href="../taxa/index.php?taxon=Rhizophora mangle"><i>Rhizophora mangle</i></a>,
                the black mangrove, <a href="../taxa/index.php?taxon=Avicennia germinans"><i>Avicennia germinans</i></a>,
                and the white mangrove, <a href="../taxa/index.php?taxon=Laguncularia racemosa"><i>Laguncularia racemosa</i></a>.
            </p>
            <p>
                Buttonwood (<a href="../taxa/index.php?taxon=Conocarpus erectus"><i>Conocarpus erectus</i></a>), while often considered a fourth mangrove species, is technically a mangrove
                associate, as it lacks the specialization of true mangroves and grows in uplands beyond the reach of tides.
            </p>
        </div>
        <div id="mangrove-species-section" class="cd-section">
            <h4>Mangrove Species</h4>
            <figure style="float: left;margin-right: 30px;">
                <img style="border:0;width:475px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/2_Rhizophora_mangle_L_Holly_Sweat.jpg" />
                <figcaption>
                    <i>Credit: H. Sweat</i>
                </figcaption>
            </figure>
            <p>
                In the IRL, the most recognizable and common species is the red mangrove. Dominating the shoreline from the
                upper subtidal to the lower intertidal zones, red mangroves are easily identified by their tangled, reddish
                “prop roots” that grow outward and down from the trunk into the water and underlying sediments. The tree
                often appears to be standing or walking on the surface of the water.
            </p>
            <p>
                In the tropics, trees may grow to more than 80 feet (24 meters) in height; however, in Florida, trees typically
                average around 20 feet (6 meters) in height. Dwarf mangroves are prevalent in Merritt Island and the northern
                reaches of the lagoon, but also occur as far south as the Keys.
            </p>
            <p>
                Leaves have glossy, bright green upper surfaces and pale undersides. Trees flower throughout the year, peaking
                in spring and summer. The seed-like propagules of the red mangrove are pencil-shaped, and may reach nearly
                a foot in length (30 cm) as they mature on the parent tree.
            </p>
            <div style="clear:both;">
                <figure style="float: left;margin-right: 30px;">
                    <img style="border:0;width:200px;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/3_Avicennia_germinans_L_Holly_Sweat.jpg" />
                    <figcaption>
                        <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
                <p>
                    <b>Black mangroves</b> typically grow immediately inland of red mangroves. Though they may reach 65 feet (20 meters)
                    in some locations, Florida populations typically grow to 50 feet (15 meters.) Instead of prop roots, black
                    mangroves feature thick stands of pneumatophores, stick-like branches which aid in aeration that grow upward
                    from the ground. Leaves are narrower than those of red mangroves, and are often encrusted with salt. Black
                    mangroves flower throughout spring and early summer, producing lima bean-shaped propagules.
                </p>
            </div>
            <div style="clear:both;">
                <figure style="float: left;margin-right: 30px;">
                    <img style="border:0;width:200px;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/4_white_mangrove_naturalist.jpg" />
                    <figcaption>
                        <i>Credit: W. Hong, iNaturalist</i>
                    </figcaption>
                </figure>
                <p>
                    <b>White mangroves</b> are more prominent in high marsh areas, typically growing upland of both red and
                    black mangroves. White mangroves are significantly shorter than red or black mangroves, as well as the
                    least cold tolerant, generally reaching a maximum 50 feet (15 meters) in height under optimal conditions.
                    Leaves are oval and flattened, with two glands at the base, known as nectaries, that produce sugar. Trees
                    flower in spring and early summer. Propagules are small, measuring just over a third of an inch (roughly
                    1 cm).
                </p>
            </div>
        </div>
        <div id="mangrove-distribution-section" class="cd-section" style="clear:both;">
            <h4>Mangrove Distribution</h4>
            <figure style="float: left;margin-right: 30px;">
                <img style="border:0;width:200px;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/FL-Mangrove-dist.PNG" />
                <figcaption>
                    <i>Credit: Wikimedia Commons</i>
                </figcaption>
            </figure>
            <p>
                Mangroves occur in dense, brackish swamps along coastal and tidally influenced, low energy shorelines. In
                Florida, mangrove forests extend from the Florida Keys to St. Augustine on the Atlantic coast, and Cedar
                Key on the Gulf coast. Factors such as climate, salt tolerance, water level fluctuation, nutrient runoff,
                and wave energy influence the composition, distribution, and extent of mangrove communities.
            </p>
            <p>
                Temperature also plays a major role in mangrove distribution. Typically, mangroves occur in areas where mean
                annual temperatures do not drop below 66°F (19°C). Mangroves are damaged under conditions where temperatures
                fluctuate more than 50°F (10°C) within short periods of time, or when they are subject to freezing conditions
                for even a few hours. Stress induced by low temperatures leads to decreasing structural complexity in black
                mangroves, with tree height, leaf area, leaf size and tree density within a forest all negatively impacted.
            </p>
        </div>
        <div id="adaptations-section" class="cd-section">
            <h4>Adaptations</h4>
            <p>
                Mangroves have several important traits that allow them to thrive in a harsh saline environment that often
                excludes other species.
            </p>

            <h5>Aerial Roots</h5>
            <p>
                Soils in mangrove areas tend to be fairly oxygen-poor, preventing many types of plants from taking root.
                Mangroves have adapted to these anoxic conditions by evolving broad, shallow root systems instead of deep-reaching
                taproots. However, specialized aerial roots allow mangroves to absorb oxygen despite their waterlogged environments.
            </p>
            <figure style="float: left;margin-right: 30px;">
                <img style="border:0;width:475px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/5_RedMangroveProps_WaltersLinda.jpg" />
                <figcaption>
                    <i>Credit: L. Walters</i>
                </figcaption>
            </figure>
            <p>
                Red mangroves’ prop roots act to both stabilize the tree and aerate the roots. Above-ground portions of prop
                roots have many small pores, called lenticels, that allow oxygen to diffuse first into spongy air spaces
                called aerenchyma near the outer edges of the root, then into underground roots. Lenticels are also hydrophobic,
                or water-repelling, and block water from entering the trees’ roots even during high tide.
            </p>
            <figure style="float: left;margin-right: 30px;">
                <img style="border:0;width:475px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/6_BlackMangrovesBird_WaltersLinda.jpg" />
                <figcaption>
                    <i>Credit: L. Walters</i>
                </figcaption>
            </figure>
            <p>
                Instead of buttressing prop roots, black mangroves’ aerial pneumatophores form dense thickets of finger-like
                protrusions around the base of trees. Sometimes called “dead man’s fingers” in reference to their gnarled,
                spooky appearance, these pneumatophores function similarly to red mangroves’ prop roots, and can reach 8
                inches (20 cm) in height.
            </p>
            <p>
                As an upland species, white mangroves lack pneumatophores and prop roots. However, when submerged for prolonged
                periods of time, the tree may develop “peg” roots to aerate the subsurface cable roots.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;clear:both;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/7_Avicennia_germinans-salt_excretion.jpg" />
                    <figcaption>
                        <i>Credit: Ulf Mehlig, Wikimedia Commons</i>
                    </figcaption>
                </figure>
            </div>

            <h5>Salt Management</h5>
            <p>
                Mangroves can grow in either fresh or salt water, depending on which is available. However, they are largely
                confined to estuaries and upland fringe areas that are at least periodically flooded by brackish or salt water.
                One reason mangroves rarely develop in strictly freshwater zones may be due to more intense competition from
                freshwater plants.
            </p>
            <p>
                That makes salt management critical. Mangroves accomplish this either by preventing salts from entering their
                tissues, or by excreting excess salts that are absorbed. Red mangroves block salts at their root surfaces
                via a reverse osmosis process that allows water to diffuse freely into plant tissues. Black and white mangroves,
                as well as buttonwoods, excrete salt to balance concentrations, which may be up to ten times higher than in
                species which exclude salts. Salt-excreting species can absorb high-salinity water, then excrete excess salts
                using specialized salt glands located in the leaves.
            </p>

            <h5>Reproductive Strategies</h5>
            <p>
                Mangroves employ two unique methods to increase their chances of reproductive success: vivipary, and water-borne
                dispersal of young plants.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/8_red_mangrove_propagule.jpg" />
                    <figcaption>
                        <i>Credit: </i>
                    </figcaption>
                </figure>
            </div>
            <p>
                In plants, vivipary is akin to gestation in animals: seeds or embryos begin to develop before they separate
                from the parent. In red and black mangroves, seeds partially germinate and develop into seedlings (propagules)
                while still attached to the parent tree, rather than dropping off to await germination in favorable conditions.
                White mangroves are not considered to be viviparous; however, germination in this species often occurs during
                the dispersal period.
            </p>
            <p>
                Propagules eventually detach from the parent and float in water for a certain period of time, during which
                germination completes. In red mangroves, this is approximately 40 days; in black mangroves, 14 days; and in
                white mangroves, 8 days.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/20GillE1_S.jpg" />
                    <figcaption>
                        <i>Credit: E. Gill</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="forests-section" class="cd-section">
            <h4>Types of Mangrove Forests</h4>
            <p>
                There are five distinct types of mangrove forests based on water level, wave energy, and pore water salinity.
            </p>

            <h5>Mangrove Fringe</h5>
            <p>
                Tall mangrove fringe forests occur along protected coastlines and the exposed open waters of bays and lagoons.
                Red mangroves are typically the dominant species. Daily tidal cycles inundate fringe, as well as export buoyant
                materials such as leaves, twigs and propagules from mangrove areas to adjacent shallow-water areas. This export
                of organic material provides nutrition to a wide variety of organisms and provides for continued growth of
                the fringing forest.
            </p>

            <h5>Overwash Islands</h5>
            <p>
                Mangrove overwash islands are also subject to tidal inundation, and are dominated by red mangroves. The major
                difference between fringe and overwash islands is that the entire island is typically flooded during each
                tidal cycle. Because overwash islands are unsuitable for human habitation, and because water acts as a barrier
                to predatory animals such as raccoons, rats and feral cats, overwash islands are often the site of bird rookeries.
            </p>

            <h5>Riverine Mangrove Forests</h5>
            <p>
                Riverine mangrove forests occur on seasonal floodplains in areas where natural patterns of freshwater discharge
                remain intact. Salinity drops during the wet season, when rains cause extensive freshwater runoff; during
                the dry season, salinity increases when estuarine waters are able to intrude more deeply into the river system.
                Nutrient availability is also highest during low-salinity periods, supporting optimal mangrove growth.
            </p>

            <h5>Basin Mangrove Forests</h5>
            <p>
                Basin mangrove forests are a very common community type, and the most commonly altered. Basin mangrove forests
                occur in inland depressions which are irregularly flushed by tides. Periodic hypersaline conditions can stunt
                or kill trees. Black mangroves tend to dominate in basin communities, but certain exotic species including
                Brazilian pepper and Australian pine are also successful invaders. Basin mangrove forests contribute large
                amounts of organic debris to adjacent waters, including whole leaves and dissolved organics.
            </p>

            <h5>Dwarf Mangrove Forests</h5>
            <p>
                Dwarf mangrove forests occur in areas where lack of nutrients, freshwater, and inundation by tides limit
                tree growth. Any mangrove species can be dwarfed; trees are generally no taller than three feet (1 meter).
                Dwarf forests are most common in South Florida, in the vicinity of the Everglades, but can occur throughout
                mangroves’ range.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/9_12_VanOsE1.jpg" />
                    <figcaption>
                        <i>Credit: E. Van Os</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="environmental-benefits-section" class="cd-section">
            <h4>Environmental Benefits</h4>
            <p>
                Long viewed as inhospitable, marginal environments, mangroves provide numerous environmental benefits.
            </p>
            <p>
                Often referred to as the bridge between land and sea, mangroves provide their coastlines a critical first
                line of defense against erosion and storms. Aerial roots trap sediments in and assist in preventing coastal
                erosion. As storms approach and pass by, branches in the canopies and roots in the water reduce the force of
                winds and waves, blunting the edge of strong storm impacts.
            </p>
            <p>
                Mangroves are extremely absorbent carbon sinks—but managed improperly, they stand to be supercharged sources
                of carbon that could exacerbate global warming processes. The 34 million acres (14 million hectares) of global
                mangrove forest occupies the equivalent of only 2.5 percent of the Amazon rain forest. As of 2000, that land
                area was found to hold an estimated 6.4 billion metric tons of carbon, or 8 percent of the Amazon basin’s
                approximately 76 billion tons of sequestered soil carbon.
            </p>
            <p>
                But over the next 15 years, deforestation of mangroves for agriculture, aquaculture, timber and human development
                resulted in the loss of up to 122 million metric tons of soil carbon—equivalent to Brazil’s total carbon dioxide
                output in 2015 of 447 million tons of CO<sup>2</sup>.
            </p>
            <p>
                Mangroves also reduce environmental pollutants. Like many other tidal wetlands, mangrove communities accumulate
                nutrients – including harmful toxic metals and trace elements. Mangrove roots, invertebrates, epiphytic algae,
                bacteria and other microorganisms can sequester nutrients in their tissues, often for long periods of time.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/10_Leaf_Litter_Lorae_Simpson.jpg" />
                    <figcaption>
                        Mangrove leaf litter. <i>Credit: L. Simpson</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="ecological-role-section" class="cd-section">
            <h4>Ecological Role</h4>
            <p>
                Mangroves were once believed to create soils. It’s not hard to see why: their complex aerial root structures
                trap the abundant leaf litter that falls from the trees each year, as well as any other passing organic detritus
                carried past by water and wave action. Mangrove forests are among the world’s most highly productive ecosystems.
            </p>
            <p>
                As living material dies and is decomposed, tidal flushing helps distribute this material to areas where other
                organisms can use it—creating the conditions for highly biodiverse communities.
            </p>
        </div>
        <div id="nutrients-section" class="cd-section">
            <h4>Nutrients</h4>
            <p>
                Leaf litter, including twigs, propagules, flowers, small branches and insect refuse, is a major nutrient
                source to consumers in mangrove systems. Generally, leaf litter is composed of approximately 68 to 86 percent
                leaves, 3 to 15 percent twigs, and 8 to 21 percent miscellaneous material.
            </p>
            <p>
                Leaf fall in Florida mangroves has been estimated to be 2.4 dry grams per square meter per day on average,
                with significant variation depending on the site. Once fallen, leaves and twigs decompose fairly rapidly,
                with black mangrove leaves decomposing faster than red mangrove leaves. Tidal and frequently flooded areas
                have faster rates of decomposition and export than other areas. Decomposition of red mangrove litter proceeds
                faster under saline conditions than under freshwater conditions. As the decay process proceeds, nitrogen,
                protein, and caloric content within the leaf all increase.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/11_Littorina_angulifera_L_Holly_Sweat.jpg" />
                    <figcaption>
                        Mangrove periwinkle, <i>Littorina angulifera</i>. <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="associated-section" class="cd-section">
            <h4>Associated Plants and Animals</h4>
            <p>
                Mangroves provide habitat for a wide variety of species. An estimated 220 fish species, 24 reptile species,
                18 mammal species, and 181 bird species that all utilize Florida mangroves as habitat during their life cycles.
                A wide diversity of invertebrates and crustaceans also rely on mangrove prop roots as habitat for at least
                part of their life cycles. Mangrove roots are particularly valuable juvenile fish habitat—even more so than
                any seagrass beds that may be nearby, which are notoriously rich habitats for fish. Fish densities in mangroves
                can be up to 35 times higher than in adjacent seagrass beds.
            </p>
            <p>
                Many other species make use of mangrove areas for foraging, roosting, breeding, and other activities. Prop
                roots host sessile (fixed) organisms such as bryozoans, tunicates, barnacles, and mussels. More mobile organisms
                including crabs, shrimp and snails also use prop roots for feeding and refuge.
            </p>
            <p>
                The canopy provides shelter to organisms that can migrate from the water’s surface, including lagoonal snails,
                mangrove crabs, isopods and insects, and of course, birds. The tiny-to-microscopic organisms that dwell in the
                upper sediments of mangrove forests, or benthic infauna, are also present in surprising abundance given the
                harsh conditions.
            </p>
            <p>
                In upland mangrove communities, plants including bromeliads, orchids, ferns and other epiphytes appear in
                the canopy. Common upland arboreal animals include jays, wrens, woodpeckers, warblers, skinks, anoles, snakes,
                and tree snails.
            </p>
            <p>
                In the understory of upland hardwood communities, animals including snakes, rodents, insects and mammals
                like gray foxes, deer and bobcats reside. Many of these animals enter mangrove forests daily for feeding,
                but return to the upland community at other times.
            </p>
        </div>
        <div id="further-reading-section" class="cd-section">
            <h4>Further Reading</h4>
            <ul class="further-reading-list">
                <li>
                    Atkinson, MR, Findlay, GP, Hope, AB, Pitman, MG, Sadler, HDW & HR West. 1967. Salt regulation in the mangroves
                    <i>Rhizophora mangle</i> Lam. and <i>Aerialitis annulata</i> R. <i>Australian J. Biol. Sci.</i> 20: 589-599.
                </li>
                <li>
                    Brockmeyer, RE, Rey, JR, Virnstein, RW, Gilmore, Jr., RG & L Earnest. 1997. Rehabilitation of impounded estuarine
                    wetlands by hydrologic reconnection to the Indian River Lagoon, Florida. <i>J. Wetlands Ecol. Manag.</i> 4: 93-109.
                </li>
                <li>
                    Carlson, PR & LA Yarbro. 1987. Physical and biological control of mangrove pore water chemistry. In: Hook, DD et al., eds.
                    <i>The Ecology and Management of Wetlands.</i> 112-132. Croom Helm. London, UK.
                </li>
                <li>
                    Carlton, JM. 1974. Land-building and stabilization by mangroves. <i>Env. Conserv.</i> 1: 285-294.
                </li>
                <li>
                    Carlton, JM. 1975. <i>A guide to common salt marsh and mangrove vegetation.</i> Florida Marine Resources Publications 6.
                </li>
                <li>
                    Carlton,JM. 1977. <i>A survey of selected coastal vegetation communities of Florida.</i> Florida Marine Research Publications 30.
                </li>
                <li>
                    Cintron, G, Lugo, AE, Pool, DJ, & G Morris. 1978. Mangroves of arid environments in Puerto Rico and adjacent islands.
                    <i>Biotropica.</i> 10: 110-121.
                </li>
                <li>
                    Feller, IC, ed. 1996. <i>Mangrove Ecology Workshop Manual. A Field Manual for the Mangrove Education and Training Programme
                    for Belize.</i> Marine Research Center, University College of Belize. Calabash Cay, Turneffe Islands. Smithsonian Institution,
                    Washington DC.
                </li>
                <li>
                    Gilmore, Jr., RG, Cooke, DW & CJ Donahue. 1982. A comparison of the fish populations and habitat in open and closed salt marsh
                    impoundments in east central Florida. <i>NE Gulf Sci.</i> 5: 25-37.
                </li>
                <li>
                    Gilmore, Jr., RG & SC Snedaker. 1993. Chapter 5: Mangrove Forests. In: Martin, WH, Boyce, SG & AC Echternacht, eds. <i>Biodiversity
                    of the Southeastern United States: Lowland Terrestrial Communities.</i> John Wiley & Sons, Inc. Publishers. New York, NY. 502 pp.
                </li>
                <li>
                    Harrington, RW & ES Harrington. 1961. Food selection among fishes invading a high subtropical salt marsh; from onset of flooding
                    through the progress of a mosquito brood. <i>Ecology.</i> 42: 646-666.
                </li>
                <li>
                    Heald, EJ. 1969. <i>The production of organic detritus in a south Florida estuary.</i> Ph.D. Thesis, University of Miami. Coral Gables, FL.
                </li>
                <li>
                    Heald, EJ & WE Odum. 1970. The contribution of mangrove swamps to Florida fisheries. <i>Proc. Gulf Caribbean Fish. Inst.</i> 22: 130-135.
                </li>
                <li>
                    Heald, EJ, Roessler, MA & GL Beardsley. 1979. Litter production in a southwest Florida black mangrove community.
                    <i>Proc. FL Anti-Mosquito Assoc. 50th Meeting.</i> 24-33.
                </li>
                <li>
                    Hull, JB & WE Dove. 1939. Experimental diking for control of sand fly and mosquito breeding in Florida saltwater marshes.
                    <i>J. Econ. Entomology.</i> 32: 309-312.
                </li>
                <li>
                    Lahmann, E. 1988. <i>Effects of different hydrologic regimes on the productivity of</i> Rhizophora mangle <i>L. A case study of
                    mosquito control impoundments in Hutchinson Island, St. Lucie County, Florida.</i> Ph.D. dissertation, University of Miami.
                    Coral Gables, FL.
                </li>
                <li>
                    Lewis, III, RR, Gilmore, Jr., RG, Crewz, DW & WE Odum. 1985. Mangrove habitat and fishery resources of Florida. <i>In</i>: Seaman,
                    Jr., W, ed. <i>Florida Aquatic Habitat and Fishery Resources.</i> American Fisheries Society, Florida Chapter. Kissimmee, FL.
                </li>
                <li>
                    Lugo, AE. 1980. Mangrove ecosystems: successional or steady state? <i>Biotropica.</i> 12:65-73.
                </li>
                <li>
                    Lugo, AE & SC Snedaker. 1974. The ecology of mangroves. <i>Ann. Rev. Ecol. Syst.</i> 5: 39-64.
                </li>
                <li>
                    Lugo, AE, Sell, M & SC Snedaker. 1976. Mangrove ecosystem analysis. In: Patten, BC, ed. <i>Systems Analysis
                    and Simulation in Ecology.</i> 113-145. Academic Press. New York, NY. USA
                </li>
                <li>
                    Lugo, AE & Patterson-Zucca, C. 1977. The impact of low temperature stress on mangrove structure and growth. <i>Trop. Ecol.</i> 18: 149-161.
                </li>
                <li>
                    Miller, PC. 1972. Bioclimate, leaf temperature, and primary production in red mangrove canopies in South Florida. <i>Ecology.</i> 53: 22-45.
                </li>
                <li>
                    Odum, WE. 1970. <i>Pathways of energy flow in a south Florida estuary.</i> Ph.D. Thesis, University of Miami. Coral Gables, FL.
                </li>
                <li>
                    Odum, WE & CC McIvor. 1990. Mangroves. In: Myers, RL & JJ Ewel, eds. <i>Ecosystems of Florida.</i> 517 - 548.
                    University of Central Florida Press. Orlando, FL.
                </li>
                <li>
                    Odum, WE, McIvor, CC & TJ Smith III. 1982. <i>The ecology of the mangroves of south Florida: a community profile.</i> U.S. Fish and
                    Wildlife Service, Office of Biological Services. FWS/OBS-81-24.
                </li>
                <li>
                    Odum, WE & EJ Heald. 1972. Trophic analyses of an estuarine mangrove community. <i>Bull. Mar. Sci.</i> 22: 671-738.
                </li>
                <li>
                    Onuf, CP, Teal, JM & I Valiela. 1977. Interactions of nutrients, plant growth and herbivory in a mangrove ecosystem. <i>Ecology.</i> 58: 514-526.
                </li>
                <li>
                    Platts, NG, Shields, SE & JB Hull. 1943. Diking and pumping for control of sand flies and mosquitoes in Florida salt
                    marshes. <i>J. Econ. Entomology.</i> 36: 409-412.
                </li>
                <li>
                    Pool, DJ, Lugo, AE & SC Snedaker.1975. Litter production in mangrove forests of southern Florida and Puerto Rico. <i>Proc. Int.
                    Symp. Biol. Manag. Mangroves.</i> 213-237. University of Florida Press, Gainesville, FL.
                </li>
                <li>
                    Pool, DJ, Snedaker, SC & AE Lugo. 1977. Structure of mangrove forests in Florida, Puerto Rico, Mexico, and Central America. <i>Biotropica.</i> 9: 195-212.
                </li>
                <li>
                    Provost, MW. 1976. Tidal datum planes circumscribing salt marshes. <i>Bull. Mar. Sci.</i> 26: 558-563.
                </li>
                <li>
                    Rabinowitz, D. 1978a. Dispersal properties of mangrove propagules. <i>Biotropica.</i> 10: 47-57.
                </li>
                <li>
                    Rabinowitz, D. 1978b. Early growth of mangrove seedlings in Panama, and a hypothesis concerning the
                    relationship of dispersal and zonation. <i>J. Biogeography.</i> 5: 113-133.
                </li>
                <li>
                    Rey, JR & T Kain. 1990. <i>Guide to the salt marsh impoundments of Florida.</i> Florida Medical Entomology Laboratory Publications. Vero Beach, FL.
                </li>
                <li>
                    Rey, JR, Schaffer, J, Tremain, D, Crossman, RA & T Kain. 1990. Effects of reestablishing tidal connections in two
                    impounded tropical marshes on fishes and physical conditions. <i>Wetlands.</i> 10: 27-47.
                </li>
                <li>
                    Rey, JR, Peterson, MS, Kain, T, Vose, FE & RA Crossman. 1990. Fish populations and physical conditions in ditched and
                    impounded marshes in east-central Florida. <i>N.E. Gulf Science.</i> 11: 163-170.
                </li>
                <li>
                    Rey, JR, Crossman, RA, Peterson, M, Shaffer, J & F Vose. 1991. Zooplankton of impounded marshes and shallow areas
                    of a subtropical lagoon. <i>FL Sci.</i> 54: 191-203.
                </li>
                <li>
                    Rey, JR, Crossman, RA, Kain, T & J Schaffer. 1991. Surface water chemistry of wetlands and the Indian River Lagoon,
                    Florida, USA. <i>J. FL Mosquito Con. Assoc.</i> 62: 25-36.
                </li>
                <li>
                    Rey, JR, Kain, T & R Stahl. 1991. Wetland impoundments of east-central Florida. <i>FL Sci.</i> 54: 33-40.
                </li>
                <li>
                    Rey, JR & CR Rutledge. 2001. <i>Mosquito Control Impoundments.</i> Document # ENY-648, Entomology and Nematology Department, Florida
                    Cooperative Extension Service, Institute of Food and Agricultural Sciences, University of Florida. Available
                    online at: <a href="https://edis.ifas.ufl.edu" target="_blank">https://edis.ifas.ufl.edu</a>.
                </li>
                <li>
                    Savage, T. 1972. <i>Florida mangroves as shoreline stabilizers.</i> Florida Department of Natural Resources Professional Papers 19.
                </li>
                <li>
                    Scholander, PF, van Dam, L & SI Scholander. 1955. Gas exchange in the roots of mangroves. <i>Amer. J. Botany.</i> 42: 92-98.
                </li>
                <li>
                    Simberloff, DS. 1983. Mangroves. In: Janzen, DH., ed. <i>Costa Rican Natural History.</i> 273-276. University of Chicago Press. Chicago, IL.
                </li>
                <li>
                    Snedaker, SC. 1989. Overview of mangroves and information needs for Florida Bay. <i>Bull. Mar. Sci.</i> 44: 341-347.
                </li>
                <li>
                    Snedaker, S C & AE Lugo. 1973. <i>The role of mangrove ecosystems in the maintenance of environmental quality and a high
                    productivity of desirable fisheries.</i> Final report to the Bureau of Sport Fisheries and Wildlife in fulfillment of
                    Contract no. 14-16-008-606. Center for Aquatic Sciences. Gainesville, FL.
                </li>
                <li>
                    Snelson, FF. 1976. <i>A study of a diverse coastal ecosystem on the Atlantic coast of Florida. Vol. 1: Ichthyological Studies.</i>
                    NGR-10-019-004 NASA. Kennedy Space Center, Florida. USA.
                </li>
                <li>
                    Thayer, GW, Colby, DR & WF Hettler Jr. 1987. Utilization of the red mangrove prop roots habitat by fishes in South Florida.
                    <i>Mar. Ecol. Prog. Ser.</i> 35: 25-38.
                </li>
                <li>
                    Tomlinson, PB. 1986. <i>The botany of mangroves.</i> Cambridge University Press. London.
                </li>
                <li>
                    Waisel, Y. 1972. <i>The biology of halophytes.</i> Academic Press. New York, NY.
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
