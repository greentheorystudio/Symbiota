<?php
include_once(__DIR__ . '/../config/symbbase.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Silent Sentinels</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/SLRiver_sunrise.jpg");
            background-position: center bottom;
        }

        .bottom-hero-container {
            background-image: url("../content/imglib/static/IMG_0425.JPG");
            background-position: center bottom;
            width: 100%;
            height: 1000px;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
            margin-top:100px;
            display: flex;
            justify-content: center;
            align-items: center;
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
        <h1>Silent Sentinels</h1>
    </div>
    <div class="top-text-container">
        <h3>
            As signals of change in Florida’s Indian River Lagoon, tiny bottom-dwelling creatures known as benthic infauna
            punch above their weight.
        </h3>
    </div>
    <div class="photo-credit-container" style="font-size: 17px;font-style: italic;">
        The St. Lucie River at sunrise. The river is a major tributary to the Indian River Lagoon, and its freshwater carries
        nutrients as well as other pollutants from more densely populated areas of its drainage basin.
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
                        <a href="#monitoring-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Monitoring the Invisible</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                FORT PIERCE, FLORIDA -- March on the Indian River Lagoon in south-central Florida is a lovely time of year.
                The sun isn’t yet too hot. Aquamarine water glitters in a ruffling breeze under a sky propped up by tropical
                columns of cloud. Dolphins and turtles pop up regularly in the inlet leading to the sea.
            </p>
            <p>
                On a 70-degree spring morning, three Smithsonian biologists head out on a flat-bottomed boat loaded with buckets,
                sample jars and heavy metal tools. As their colleagues have done quarterly since 2005, they visit 15 sites
                around the central part of the 156-mile-long estuary to collect samples.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/msites_map.jpg" />
                    <figcaption style="width:500px;font-size: 17px;font-style: italic;margin-left:10px;margin-top:-10px;">
                        A map of the Smithsonian’s long-term benthic monitoring sites on the Indian River Lagoon. (Credit: Holly Sweat)
                    </figcaption>
                </figure>
            </div>
            <p>
                The sites were selected to represent a range of conditions in the lagoon, from fairly pristine to heavily impacted.
            </p>
            <div style="display:flex;justify-content: center;align-content: center;">
                <figure style="margin: 15px;">
                    <div style="display:flex;gap:20px;">
                        <img style="border:0;height:250px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/pristine.jpg" />
                        <img style="border:0;height:250px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/impacted.jpg" />
                    </div>
                    <figcaption style="width:800px;font-size: 17px;font-style: italic;">
                        The image on the left depicts an area of the lagoon with relatively pristine, healthy water conditions; at right, an area of the lagoon with highly impacted, degraded water conditions.  (Photos: Holly Sweat)
                    </figcaption>
                </figure>
            </div>
            <p>
                To most eyes, the samples the scientists bring back from these sites resemble a young child’s collection of
                summer flotsam: jars full of bright pink fluid and tiny seashells; some with only jumble of brownish wrack;
                others crammed full of watery, silty mud.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:600px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/jess_with_jars.jpg" />
                    <figcaption style="width:600px;font-size: 17px;font-style: italic;">
                        SMS technician Jess Glanz examines a sample jar from a collection site on the Indian River Lagoon.  (Photo: Holly Sweat)
                    </figcaption>
                </figure>
            </div>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <h2 style="width:80%;">This layer, known as the benthos (Greek: “depth of the sea”), is as important to life in the water as
                    soils are to life on land.</h2>
            </div>
            <p>
                Where these samples come from are the habitats at the bottom of the lagoon. This layer, known as the benthos
                (Greek: “depth of the sea”), is as important to life in the water as soils are to life on land. The animals
                that live there – many of them microscopic – wear many hats.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:600px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/infauna.jpg" />
                    <figcaption style="width:600px;font-size: 17px;font-style: italic;">
                        A sampling of benthic infauna. (Credit: Holly Sweat)
                    </figcaption>
                </figure>
            </div>
            <p>
                They serve as food for many animals, from tiny fish to large, valuable commercial and sport species, including
                mullet and red drum.
            </p>
            <p>
                They act as filters, siphoning vast quantities of water and capturing suspended nutrients and other particles –
                which are then sequestered in the sediments below.
            </p>
            <p>
                They also act as silent sentinels of change, largely invisible indicators of shifts across a body of water
                that serves as the foundation of life and livelihoods for a huge swath of Florida’s Atlantic coast.
            </p>
            <p>
                Finally, they are ubiquitous. Literally, everywhere: crowded and scattered throughout the sediments of the
                bottom of the lagoon.
            </p>
            <p>
                So, the jars and buckets of mud and silt and crustaceans and worms aboard the Smithsonian research boat are
                more important than they might first seem. Like many small things collected over time, their sums and differences
                begin to tell a story – one that others can build on in the efforts to restore a lagoon that has begun to
                falter under the weight of human influence.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure>
                    <img style="border:0;width:100%;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Team_BEL.jpg" />
                    <figcaption style="width:100%;font-size: 17px;font-style: italic;">
                        From left, Program Coordinator Jess Glanz, Principal Investigator Dr. Holly Sweat and Technician Garrett O’Donnell sample for
                        benthic infauna on the Indian River Lagoon. (Photo: Scott Jones)
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="monitoring-section" class="cd-section">
            <h4>Monitoring the Invisible</h4>
            <p>
                The Indian River Lagoon’s range of temperate-to-subtropical climates make for enticing environs for many plants
                and animals. More than 4,000 species of flora and fauna call the lagoon home. Set foot anywhere on the bottom
                of the lagoon, however, and you stand atop an even greater diversity of life. In some areas, a size 10.5
                footprint can contain as many as 8,600 individual creatures.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/odonnell_garret_fabricinuda_trilobata.jpg" />
                    <figcaption style="width:500px;font-size: 17px;font-style: italic;">
                        Thousands of these <i>Fabricinuda trilobata</i> worms were found in a sample of Indian River Lagoon sediment.  (Photo: Holly Sweat)
                    </figcaption>
                </figure>
            </div>
            <p>
                But great weather and even better fishing mean people love it here too. In the 1950s, just 45,000 people lived
                in the five counties around the lagoon. By the 2020s, the region’s population exploded to over a million people,
                and remains one of the fastest growing areas of the United States. Like other coastal areas around the world,
                massive human influxes have had a deep impact on the ecological function of the entire estuary.
            </p>
            <p>
                People dug canals to drain the area’s abundant wetlands. Water that once flowed south from nearby Lake Okeechobee
                was redirected to flow out east through the Indian River Lagoon and west through Fort Myers toward Sanibel Island.
                Huge citrus, sugar and cattle operations flourished.
            </p>
            <div style="display:flex;justify-content: center;align-content: center;">
                <figure style="margin:0;display:flex;flex-direction:column;justify-content: center;align-content: center;">
                    <div style="width:100%;display:flex;justify-content: center;align-content: center;gap:30px;">
                        <img style="border:0;height:375px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/flowmap1-historic-trim.jpg" />
                        <img style="border:0;height:375px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/flowmap2-current-trim.jpg" />
                        <img style="border:0;height:375px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/flowmap3-cerp-trim.jpg" />
                    </div>
                    <figcaption style="width:80%;margin: 0 auto;font-size: 17px;font-style: italic;">
                        These maps show the historic, current and projected future water flow in southern Florida. A decades-long
                        plan to restore water flow south through the Everglades is underway. <i>(Graphics: National Park Service)</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                During exceptionally rainy years, freshwater releases from Lake O carry nutrient-laden waters, which drive harmful
                algal blooms, and dilute the lagoon’s salinity. Septic tanks are common for handling residential wastes, and many
                systems leak. Pollution, erratic salinity and oxygen starvation from algae blooms wreak havoc not only on fish,
                but also on oysters, seagrasses and benthic infauna – the filters, stabilizers and foundation of the lagoon’s
                ecosystem.
            </p>
            <p>
                Though the lagoon has proven to be resilient, effects pile up over time—both negative impacts as well as positive
                gains from restoration efforts. Often, these trends can only be detected by long-term data collection like
                the Smithsonian’s benthic infaunal monitoring program, which was launched in 2005 as a component of the hugely
                ambitious <a href="https://storymaps.arcgis.com/stories/dfd3e4261602415683015a919dfbafec" target="_blank" >Comprehensive Everglades Restoration Program</a>.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <video width="900" controls>
                        <source src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/M09_Grab1_20220105_Trim.mp4" type="video/mp4">
                    </video>
                    <figcaption style="width:900px;font-size: 17px;font-style: italic;">
                        Scientists use tools like the ponar grab, descending here, to collect sediments from the bottom of the lagoon. (Credit: Holly Sweat and Jess Glanz)
                    </figcaption>
                </figure>
            </div>
            <p>
                A goal of the monitoring program is to reveal connections and trends between the lagoon’s health, and what’s
                living in the benthos at a given moment in time. Long-term, benthic infauna could also potentially be used
                to gauge several lagoon health indicators.
            </p>
            <p style="padding-left:50px;">
                <b>Effectiveness of restoration efforts.</b> The proverbial canary in the coal mine is a bioindicator species,
                which forewarns of coming trouble—or signals that all is well. The presence or absence of a particular species
                of clam, worm or other bottom-dwelling invertebrate in an area of the lagoon could be used to help diagnose
                the effectiveness of restoration efforts, for example, or connect the effects of certain water quality trends.
            </p>
            <p style="padding-left:50px;">
                <b>Biodiversity.</b> Nothing in the lagoon could exist without the benthic community, yet this area has the
                largest gaps in scientific knowledge about biodiversity in the lagoon. Benthic monitoring and analysis help
                reveal a more complete picture of biodiversity in the Indian River Lagoon. In the last several years, advanced
                genetic techniques alone have uncovered over 1,000 previously unknown species—but their roles in the ecosystem
                remain unclear.
            </p>
            <p style="padding-left:50px;">
                <b>Lagoon health report card.</b> One goal of the benthic monitoring program is to develop an index with local data
                to show local conditions, graded like a report card.
            </p>
            <p>
                By connecting changes in benthic communities to turning-point events in the lagoon, both natural and human-made,
                the monitoring program is the kind of baseline scientific research necessary for understanding how the lagoon
                ecosystem works—and contributes to the greater roadmap for restoration underway across this jewel of coastal
                Atlantic Florida.
            </p>
        </div>
    </div>
</div>
<div class="bottom-hero-container">
    <div style="width: 70%;padding: 0 20px;background-color: rgba(226, 232, 236, 0.58);">
        <h3 style="display:flex;align-content: center;flex-direction:column;justify-content: center;align-items:center;">
            <i>“It is a wonderful river… immensely deep and very fine sweet water. The beauties of nature are here very manifest, in fact it is a wonderland.”</i><br />
            –Dr. Herman Herold, 1884, <i>Logbook of Trip to Jupiter Inlet, Transcript</i>
        </h3>
    </div>
</div>
<p style="padding-left:20px;">
    <i>Story published September 2022</i>
</p>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
