<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Stewardship</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/21SmithA1_full.jpg");
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
        <h1>Stewardship</h1>
    </div>
    <div class="top-text-container">
        <h3>
            Critical to the Indian River Lagoon’s remarkable biodiversity is its water quality—drawing humans, flora and
            fauna alike. Yet many human activities on the lagoon directly affect the estuary’s water quality, diminishing
            quality of life both above and below water.
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
                        <a href="#fertilizer-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Reduce fertilizer use</span>
                        </a>
                    </li>
                    <li>
                        <a href="#rain-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Send only rain down storm drains</span>
                        </a>
                    </li>
                    <li>
                        <a href="#pet-section" data-number="4">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Pick up after your pet</span>
                        </a>
                    </li>
                    <li>
                        <a href="#plant-section" data-number="5">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Right plant, right place</span>
                        </a>
                    </li>
                    <li>
                        <a href="#footprints-section" data-number="6">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Leave only footprints</span>
                        </a>
                    </li>
                    <li>
                        <a href="#volunteer-section" data-number="7">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Volunteer</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                Below are six proactive ways in which lagoon-area residents and visitors can help improve water quality—and
                the overall Indian River Lagoon estuary system.
            </p>
        </div>
        <div id="fertilizer-section" class="cd-section">
            <h4>Reduce fertilizer use.</h4>
            <p>
                Indiscriminate use of fertilizers results in deteriorating water quality in the Indian River Lagoon as well
                as waterways everywhere. Fertilizers are composed primarily of nitrogen, phosphorus and potassium which are
                the major components required for plant growth. These chemicals, when improperly used, can have significant
                adverse effects on the environment.
            </p>
            <p>
                Although fertilizers can temporarily make a lawn look greener, they are far too often applied incorrectly.
                Instead of being incorporated into plant tissue, excess nutrients work their way into the lagoon directly
                through stormwater runoff or indirectly by seeping through the soil into groundwater. When in the lagoon,
                these nutrients stimulate blooms of micro-algae (i.e., phytoplankton) as well as the proliferation of other
                aquatic plants. Blooms of certain species of phytoplankton can create what are known as harmful algal blooms,
                or HABs. HABs can produce potent toxins that can have negative health consequences for both humans as well as
                marine organisms.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/HAB_2014_P_Shindel.jpg" />
                    <figcaption style="width:500px;">
                        A harmful bloom of blue-green algae near Lake Okeechobee in 2014. <i>Credit: P. Shindel</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Eutrophication is a process in aquatic systems that describes the subsequent decay of rapidly growing plant
                material consuming available oxygen, thus creating hypoxic conditions that threaten other plants and animals
                in the ecosystem. Larval and juvenile stages of fish and invertebrates can be particularly sensitive to eutrophication.
            </p>
            <p>
                This decaying organic material eventually settles to the bottom and, along with fine particles of silt and clay,
                creates a low oxygen (hypoxic) layer often called muck.
            </p>
            <p>
                Hypoxic conditions can affect biodiversity by causing the demise of species normally found in an area while
                promoting the growth of opportunistic species that thrive in low oxygen environments. Muck can easily be
                resuspended and further affect water quality and clarity. Dense phytoplankton blooms (harmful or otherwise)
                can also physically decrease the amount of light penetrating the water column and thus pose as an additional
                threat to healthy seagrass growth.
            </p>
            <p>
                Become familiar with the <a href="https://sfyl.ifas.ufl.edu/miami-dade/natural-resources/florida-yards-and-neighborhoods-fyn/" target="_blank">Florida Yards & Neighborhoods program</a>,
                run by the University of Florida Cooperative Extension service and supported by UF/Institute of Food and Agricultural
                Sciences. This program educates homeowners about the design, installation, and maintenance of healthy landscapes
                that use a minimum of water, fertilizer, and pesticides.
            </p>
            <ul class="statictext">
                <li>
                    Fertilize according to UF/IFAS recommended rates and application timings to prevent leaching.
                </li>
                <li>
                    Select fertilizers with slow-release nitrogen and little or no phosphorus. In Florida, phosphorus, occurring
                    as phosphate, is abundantly and naturally available in sedimentary rocks and soil.
                </li>
                <li>
                    Never fertilize within 10 feet of a body of water.
                </li>
                <li>
                    Do not fertilize before a heavy rain. Sarasota and Lee Counties have banned the application of fertilizer
                    during the rainy (summer) season (1 June – 30 September) and Sarasota County goes further by prohibiting
                    the sale of fertilizer during the summer.
                </li>
                <li>
                    Sweep up spilled fertilizer and return to original package.
                </li>
                <li>
                    Use iron supplements (ferrous sulfate or chelated iron) on turf instead of nitrogen fertilizer to achieve
                    a quick summer green-up.
                </li>
                <li>
                    Avoid “weed and feed” products that contain both fertilizers and herbicides, these can damage some plants.
                </li>
            </ul>
            <p>
                If reclaimed water is used for irrigation, be aware that it does contain some nutrients. Adjust the amount of
                fertilizer accordingly.
            </p>
        </div>
        <div id="rain-section" class="cd-section">
            <h4>Send only rain down storm drains.</h4>
            <p>
                An estimated that 75 billion gallons of stormwater runoff in Brevard County makes its way into the IRL annually.
                Most of this is collected from hard, impervious surfaces: streets, parking lots, sidewalks, and rooftops via
                storm drains, which then enters the lagoon via canals and tributaries.
            </p>
            <p>
                Carried with this runoff are pollutants such as heavy metals, fertilizers, sediments, animal wastes, oil and
                grease from roadways ,anti-freeze, pesticides, household chemicals, bacteria, and an array of organic and
                inorganic material. In the IRL, these materials threaten free-swimming and bottom-dwelling organisms, promote
                excessive algal growth, and can smother seagrasses.
            </p>
            <p>
                This “non-point” pollution is the primary source of pollution in the IRL today and poses the biggest threat
                to its water quality. A common misconception hindering individual efforts to lessen the effects of this pollution
                source is that many of us think that water entering storm drains is initially directed to wastewater facilities
                for treatment. This is not the case.
            </p>
            <p>
                To help keep sediment, leaves, yard clippings, and floating litter that would normally reach the IRL via storm
                drains, many local governments as well as the <a href="https://onelagoon.org/" target="_blank">Indian River Lagoon National Estuary Program</a> have been instrumental
                in installing a number of stormwater treatment devices known as baffle boxes throughout the IRL. Although
                baffle boxes do not remove dissolved nutrients, this inexpensive, relatively simple technology has been
                tremendously successful in preventing much solid material from entering the lagoon.
            </p>
            <p>
                Other tips to keep stormwater cleaner:
            </p>
            <ul class="statictext">
                <li>
                    Follow labels on fertilizer and pesticide products. Pesticides that eradicate terrestrial insects
                    often adversely affect aquatic crustaceans like shrimps, lobsters and crabs, particularly during their
                    larval stages.
                </li>
                <li>
                    Dispose of used motor oil, paint, and other chemicals at designated locations. Never let these materials
                    enter storm drains.
                </li>
                <li>
                    Dispose of animal wastes properly.
                </li>
                <li>
                    Dispose of garbage in proper receptacles.
                </li>
                <li>
                    Use native Florida plants in landscaping to minimize irrigation and fertilizer use.
                </li>
                <li>
                    Recycle or properly dispose of yard wastes including leaves, pine needles, and grass cuttings.
                </li>
                <li>
                    Use rain barrels and rain gardens to capture stormwater runoff from roofs.
                </li>
                <li>
                    Wash your car at a commercial car wash or on your lawn – never in the driveway.
                </li>
                <li>
                    Report runoff from construction sites and illegal dumping and discharging to the appropriate stormwater
                    agency in your county.
                </li>
            </ul>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/18WhiticarJ3.jpg" />
                    <figcaption style="width:500px;">
                        <i>Credit: J. Whiticar</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="pet-section" class="cd-section">
            <h4>Pick up after your pet.</h4>
            <p>
                It’s true: animal waste is gross. Understandably, some pet owners are reluctant to pick up after their pets
                for a variety of reasons: inconvenience; unsightliness; odor. Although more than 60 percent of dog owners
                report responsibly picking up after their four-legged friends, it is estimated that in the five-county region
                surrounding the Indian River Lagoon, every day, as much as 84 tons of dog feces are left on the ground in
                backyards, sidewalks and streets.
            </p>
            <p>
                In addition, some people choose to dispose of animal waste directly into waterways and/or storm drains. Since
                most stormwater is not treated before flowing into canals and local waterways, bacteria and other organisms
                in neglected or improperly disposed of pet waste can directly threaten commercial and recreational fishing,
                shellfishing, boating, and swimming in the Indian River Lagoon. Nutrients associated with pet waste can also
                cause the proliferation of phytoplankton and macro-algae with additional, adverse effects on the IRL ecosystem.
            </p>
            <p>
                <b>Please Don’t:</b>
            </p>
            <ul class="statictext">
                <li>
                    Leave pet waste in your yard to become a health problem.
                </li>
                <li>
                    Allow your pet to defecate near waterways that drain into the lagoon.
                </li>
                <li>
                    Compost pet wastes – this can present hazards to human health.
                </li>
            </ul>
            <p>
                <b>Please Do:</b>
            </p>
            <ul class="statictext">
                <li>
                    Pick up dog waste, preferably in a biodegradable bag.
                </li>
                <li>
                    Dispose of waste by throwing in the trash, flushing it down the toilet or burying it in a hole at least
                    six inches deep.
                </li>
                <li>
                    Dispose of cat waste and litter properly: carefully remove solid waste from the litter box and flush
                    down the toilet. Bag used litter and dispose of in the trash.
                </li>
                <li>
                    Clean up any waste near storm drains, ditches, wells and waterways.
                </li>
                <li>
                    Use a commercial sanitation service if you do not want to pick up after your pet.
                </li>
            </ul>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/11_ReynoldsJ1.jpg" />
                    <figcaption style="width:500px;">
                        A swallowtail butterfly visits native milkweed flowers (<i>Asclepias incarnata</i>). <i>Credit: J. Reynolds</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="plant-section" class="cd-section">
            <h4>Right plant, right place.</h4>
            <p>
                Many homeowners take pride in a beautiful landscape. Landscaping increases the aesthetic and curb appeal of
                dwellings, and enhances property value. Florida’s nursery and landscaping industry is big business, valued
                in the tens of billions of dollars annually.
            </p>
            <p>
                But picking the right plant for the right place—selecting landscaping plants that fit well into existing
                site conditions—can not only minimize water consumption and over-application of fertilizers, but still support
                the economic benefits of the state’s horticultural industry.
            </p>
            <p>
                When commonly used “backyard chemicals” are reduced or eliminated, so is the likelihood that excess chemicals
                will reach the estuary’s waters. Agricultural and residential runoff is an important contributor to declining
                lagoon water quality.
            </p>
            <p>
                A few general principles to keep in mind:
            </p>
            <ul class="statictext">
                <li>
                    Choose low maintenance plants.
                </li>
                <li>
                    Choose flowering and fruiting plants that attract wildlife.
                </li>
                <li>
                    Plant for visual impact by grouping plants together.
                </li>
                <li>
                    Avoid and eliminate non-native, invasive plants in your yard.
                </li>
                <li>
                    Choose healthy looking plants.
                </li>
                <li>
                    Consider the size of a plant or tree when fully matured, not when it is purchased. Trees are cute when
                    they’re small, but are quite different when full-grown!
                </li>
                <li>
                    Select a diverse array of plants, shrubs, trees and perennials. This reduces susceptibility to disease and
                    other pests.
                </li>
                <li>
                    Consider groundcovers in lieu of grass on slopes, for easier maintenance.
                </li>
                <li>
                    Take the long view. Slow-growing plants do take longer to fill your landscape, but will last longer and
                    require less maintenance in the long run.
                </li>
                <li>
                    Be mindful of wind. Certain species of trees are more susceptible to toppling in strong winds.
                </li>
            </ul>
            <p>
                When you are ready to choose specific trees and plants for your landscape, the following resources can help
                guide your selections:
            </p>
            <ul class="statictext">
                <li>
                    <a href="https://ffl.ifas.ufl.edu/" target="_blank">Florida Friendly Landscaping</a>, by the University of Florida Extension
                </li>
                <li>
                    <a href="http://publicserver2.sjrwmd.com/waterwise/search.jsp" target="_blank">Waterwise Plants</a>, by the St. Johns River Water Management District
                </li>
                <li>
                    <a href="https://www.fnps.org/plants" target="_blank">Florida Native Plant Society</a>
                </li>
            </ul>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/20WestM2_N.jpg" />
                    <figcaption style="width:500px;">
                        <i>Credit: M. West</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="footprints-section" class="cd-section">
            <h4>Leave only footprints.</h4>
            <p>
                The Indian River Lagoon’s natural beauty is irresistible, drawing thousands of people every year for kayaking,
                boating, canoeing, camping, sightseeing, wildlife viewing and more. Tourism and recreation contributes significantly
                to the regional economy, contributing an estimated $2 billion annually.
            </p>
            <p>
                In order to preserve and maintain the biodiversity and integrity of the IRL for ourselves and future generations
                to enjoy, consider ways to reduce impacts on, and respect the IRL ecosystem during our outdoor activities
                on the lagoon by “leaving only footprints”
            </p>
            <p>
                <a href="https://lnt.org/why/7-principles/" target="_blank">Leave No Trace</a> principles provide a guideline
                for reducing human impact on the natural environment.
            </p>
            <ul class="statictext">
                <li>
                    Plan ahead and prepare.
                </li>
                <li>
                    Respect wildlife. Observe quietly, and refrain from pursuing for the calendar shot – this stresses wildlife
                    and raises chances for disease transmission.
                </li>
                <li>
                    Leave what you find. Leave plants, flowers and other natural features undisturbed, for others to enjoy.
                </li>
                <li>
                    Dispose of wastes properly. Pack it in, pack it out.
                </li>
                <li>
                    Be considerate of other visitors, present and future.
                </li>
                <li>
                    Travel and camp on established trails and campsites.
                </li>
                <li>
                    Minimize campfire impacts. Don’t use a fire when one isn’t needed, and use fire rings when available.
                </li>
            </ul>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Volunteers_Restoration_Mosquito_Lagoon_Linda_Walters.jpg" />
                    <figcaption style="width:500px;">
                        Volunteers help restore oyster reefs in the Mosquito Lagoon. <i>Credit: L. Walters</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="volunteer-section" class="cd-section">
            <h4>Volunteer.</h4>
            <p>
                Many hands make light work – and the Indian River Lagoon needs all hands on deck to help restore and maintain
                its many habitats and natural resources. Shoreline cleanups, mangrove planting, water quality monitoring,
                oyster gardening, living shorelines, helping out at community events, photographing scenes that inspire
                you – all these things and more are readily available throughout the five-county area encompassing the IRL.
            </p>
            <p>
                For more information on volunteer activities, outreach and other opportunities, visit the Indian River
                Lagoon Council’s website at <a href="https://onelagoon.org/find-volunteer-event/" target="_blank">OneLagoon.org</a>.
            </p>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
