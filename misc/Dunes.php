<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Dune Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/1_18PastorR1.jpg");
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
            <b>Dunes</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Dunes</h1>
    </div>
    <div class="top-text-container">
        <h3>
            On virtually any barrier island, wind and sand combine to create sand dunes. Dunes play a vital role in protecting
            coastlines and property from storms, high winds and saltwater intrusion.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: R. Pastor
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
                        <a href="#dune-formation-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Dune Formation</span>
                        </a>
                    </li>
                    <li>
                        <a href="#dune-systems-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Dune Systems</span>
                        </a>
                    </li>
                    <li>
                        <a href="#dune-plants-section" data-number="4">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Dune Plants</span>
                        </a>
                    </li>
                    <li>
                        <a href="#dune-animals-section" data-number="5">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Dune Animals</span>
                        </a>
                    </li>
                    <li>
                        <a href="#human-impacts-section" data-number="6">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Human Impacts</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                Dunes also act as sand reservoirs, which are important for replenishing coastlines after tropical storms,
                hurricanes, intense wave action, or other erosional events.
            </p>
        </div>
        <div id="dune-formation-section" class="cd-section">
            <h4>Dune Formation</h4>
            <p>
                The process of dune formation begins with the transport of sand landward. This happens in three ways: saltation,
                surface creep, or suspension.
            </p>
            <p>
                Saltation occurs when winds blow medium-sized sand grains up the slope of a beach. Surface creep occurs when
                larger grains are rolled along the beach as they collide with smaller wind-blown particles during the saltation
                process.
            </p>
            <p>
                The most common transport process is suspension. Wind picks up small sand grains and brings them landward
                in onshore breezes. When plants, driftwood and other obstructions impede wind and causes the airflow to lose
                momentum, suspended grains fall out of the air on the slip face, or lee side of the obstruction, where they
                accumulate.
            </p>
            <p>
                Over time, sand builds up behind obstructions, creating a series of long, elevated spits of sand, called
                wind shadows. These grow at right angles to the shoreline, and as they present an ever-larger barrier to
                the wind, sand accumulates more rapidly. Plants colonize these stabilized areas, and their roots further
                anchor the sand and fortify the dune structure. As plants continue to colonize the upper beach, wind shadows
                join together to form dunes, which lie parallel to the shoreline.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/2_dune_transition_LHS.jpg" />
                    <figcaption>
                        <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="dune-systems-section" class="cd-section">
            <h4>Dune Systems</h4>
            <p>
                Within dune systems, which resemble a series of low peaks and valleys, the first dune above the intertidal
                zone is called the primary dune, or foredune. This is the area of active colonization by plants, and the area
                most affected by waves and heavy winds.
            </p>
            <p>
                Landward over the crest of the foredune lies the swale: a low, somewhat wet area separating primary dunes
                from secondary dunes. In swales, winds can scour the sand nearly down to the water table, and plant communities
                may consist of more freshwater species that show some salinity tolerance. It is in the shelter of swales
                that scrub communities and maritime forests first become established.
            </p>
            <p>
                Many dune systems also feature secondary dunes. These dunes form when severe storms breach primary dunes
                and deposit sand further inland. Deposition of sand onto secondary dunes also occurs as winds blow fine-grained
                sand inland over the primary dune. Due to their relative stability over time, and because they are generally
                protected by primary dunes, secondary dunes support a significantly broader variety of vegetation than primary
                dunes.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/4_Dune_Eastward_Avalon_State_Park_Fort_Pierce.jpg" />
                    <figcaption>
                        <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="dune-plants-section" class="cd-section">
            <h4>Dune Systems</h4>
            <p>
                Vegetation colonizing the upper beach and foredune must be well-adapted to periodic disturbance, and generally
                consists of grassy, salt-adapted species. Growth of these colonizing species must keep pace with the rate
                of sand build-up along the foredune, which can be rapid.
            </p>
            <p>
                Beyond the pioneering zone in the shelter of swales and secondary dunes, plants are generally more protected
                from the effects of salt spray, seawater and sand burial. The resulting communities are more diverse than
                on adjacent beaches.
            </p>
            <p>
                When established dunes remain stable over time, plants’ cycles of growth, reproduction and leaf shedding
                slowly enriches the sandy soil with decaying plant matter. As this humus accumulates, soils become richer
                and hold more water. This allows other types of vegetation to take root, and begins the process of succession,
                where shrubs and trees replace the pioneering vines and herbaceous species.
            </p>

            <h5>Foredune</h5>
            <p>
                On the foredune, beach pioneers include railroad vine and shoreline sea purslane. South of Cape Hatteras,
                sea oats are the principal dune colonizer; this coarse grass grows up to 6 feet tall and spreads laterally
                via rhizomes. Along with sea oats, two other dune-building species, bitter panic grass and beach cordgrass,
                are stimulated to grow upward by burial in sand.
            </p>
            <p>
                Subsequent lateral growth in these plants allows for the construction and stabilization of a continuous dune
                ridge.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/5_foredune_LHS.jpg" />
                    <figcaption>
                        <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
            </div>

            <h5>Dune Crest</h5>
            <p>
                The dune crest is the area where shrubby and woody species begin to replace herbaceous vines and grasses.
                Common herbaceous plants of the dune crest include sea ox-eye daisy, beach sunflower, firewheel, and annual
                phlox. Also common on dune crests are several woody species including sea grape, saw palmetto, and the invasive
                Brazilian pepper.
            </p>
            <p>
                Many of the woody species growing on dune crests are low-growing and shrubby, while inland the same species
                can demonstrate a more robust growth habit. Dry, low-nutrient soils, frequent high winds and salt spray
                conspire to stunt dune-situated individuals. Salt spray kills the tender terminal buds of many trees and
                shrubs on contact, resulting in the salt-pruned, windswept tree canopies of Florida's dune communities.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/6_Seagrape_Georgia_Schroeder.jpg" />
                    <figcaption>
                        Seagrape, <i>Coccoloba uvifera</i>. <i>Credit: G. Schroeder</i>
                    </figcaption>
                </figure>
            </div>

            <h5>Swales</h5>
            <p>
                Swales between dunes gain an increased measure of protection from winds and salt spray as the dune system
                builds over time. Swales can support freshwater plants, though most plants that grow in swales have some
                degrees of salinity tolerance as well. Stands of sea grape, saw palmetto, and the Brazilian pepper are common
                woody species on dune crests and in swales.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/7_backdune_LHS.jpg" />
                    <figcaption>
                        <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
            </div>

            <h5>Backdune</h5>
            <p>
                Backdunes and secondary dunes generally support a wider variety of vegetation than foredunes. The same species
                that grow as low shrubs or stunted trees on dune crests do grow in backdune areas as well, but in these more
                protected locales they are often able to attain full height. Saw palmetto, cabbage palm, live oak, and
                prickly pear cactus are all common inhabitants of backdunes and secondary dunes.
            </p>
        </div>
        <div id="dune-animals-section" class="cd-section">
            <h4>Dune Animals</h4>
            <p>
                A number of rodents, some of which are becoming increasingly rare, utilize dune habitats. The threatened
                southeastern beach mouse can be found in scattered populations from Cape Canaveral to Sebastian Inlet. Other
                rodents that inhabit dunes include the cotton mouse, cotton rat and rice rat, as well as eastern cottontail
                rabbit and the marsh rabbit. Several other mammals such as gray foxes, raccoons, feral pigs and feral cats
                also use dunes for feeding.
            </p>
            <div style="display:flex;justify-content: center;align-content: center;">
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/08_CorapiP1.jpg" />
                    <figcaption>
                        <i>Credit: P. Corapi</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/9_crab_burrow_LHS.jpg" />
                    <figcaption>
                        <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/10_beach_mouse_burrow.jpg" />
                    <figcaption>
                        <i>Credit: NASA</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Many species of shorebirds utilize dunes for feeding; and several species also nest in dune habitats. Among
                the nesting species are the willet, American oystercatcher, and Wilson's plover, which prefer nest sites in
                dune areas with sparse grass or herbaceous cover. The laughing gull, Caspian tern, and the gull-billed tern
                also nest in dunes but prefer areas with somewhat more dense coverage.
            </p>
            <p>
                Reptiles are also common inhabitants of dunes. Several species of anoles and snakes are common, including
                green anole, Eastern coachwhip snakes and Florida rough green snakes. Gopher tortoises, while not plentiful,
                can often be observed in stable backdune areas.
            </p>
        </div>
        <div id="human-impacts-section" class="cd-section">
            <h4>Dune Animals</h4>
            <p>
                In spite of the stabilizing ability of dune plants, dunes are highly susceptible to human impacts. Vehicles
                traversing beaches, as well as heavy foot traffic, damage vegetation by shifting sand and roots, thus
                destabilizing the dune community. Coastal development can also impact the natural process of dune replenishment
                by adversely influencing natural erosion patterns.
            </p>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
