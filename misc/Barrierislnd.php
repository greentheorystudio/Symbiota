<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Barrier Island Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/1_09_RichardsR1.jpg");
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
            <b>Barrier Islands</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Barrier Islands</h1>
    </div>
    <div class="top-text-container">
        <h3>
            The extensive Indian River Lagoon barrier island system along the east coast of Florida is the largest in the
            United States, consisting of approximately 189,300 hectares (467,700 acres). These islands separate the IRL
            from the Atlantic Ocean, and are an important defense against hurricanes, large waves, heavy winds and storm
            surges.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: R. Richards
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
                        <a href="#human-impacts-section" data-number="2">
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
                Barrier islands form in two ways: from longshore drift currents that move sands southward along the coast;
                and from the emergence of underwater shoals. Florida's barrier islands are believed to have originated
                during the Pleistocene epoch (1.75 million to 11,000 years ago) when uprisings of ancient beaches and their
                associated sediments were compressed into coquina (rock). Traces of this coquina system, called the Anastasia
                Formation, can be found from St. Augustine in the north to Boca Raton in the south.
            </p>
            <p>
                Barrier islands’ shorelines are constantly shifting in response to wind, waves, tidal action and sediment
                shifts. In a natural state, the shoreline of a barrier island migrates oceanward or retreats landward as
                the forces of erosion and accretion (deposition of sediments) occur.
            </p>
            <p>
                Whether barrier islands begin as simple sandbars, or as emerged shoals, they gradually accumulate sand due
                to wave action and winds. It is this build-up of sand along the coast that forms the well-developed beaches,
                dunes and maritime forests along Florida's coast. Wave action is constantly at work eroding sand from some
                areas of the barrier island system; simultaneously, waves deposit this eroded sand into different areas via
                longshore drift, storms and hurricanes.
            </p>
            <p>
                Deposits of eroded sediments generally occur either parallel to the coast or at the ends of barrier island
                systems, rather than seaward. In general, sands tend to be carried southward along the coast, and are deposited
                as they encounter the northern ends of barrier islands or other structures such as jetties. Data from Pilkey
                et al. (1984) suggests the Florida coastline has been eroding landward at a rate of 0.3 - 0.6 mm per year.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/3_JTL_Aerial_Image.jpg" />
                    <figcaption>
                        An aerial view of the Indian River Lagoon. <i>Credit: J. Thurlow-Lippisch</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                The substantial system of barrier islands in the area of the Indian River Lagoon encompasses a variety of
                habitat types. In the immediate vicinity of the shoreline are beaches, dunes, and swales. Beyond the beach
                zone are coastal strand, also called scrub, maritime hammocks, spoil islands, and the mangrove fringes that
                border the Indian River Lagoon.
            </p>
        </div>
        <div id="human-impacts-section" class="cd-section">
            <h4>Human Impacts</h4>
            <p>
                Florida's barrier islands have been extensively developed and support a large human population, leaving
                little of the original landscape unaltered.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/4_15_DunkertonT2.jpg" />
                    <figcaption>
                        <i>Credit: T. Dunkerton</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Florida's beaches are rated among the finest in the U.S. and draw tourists from all over the globe to swim,
                surf, bask in the sun, snorkel, fish and sail. As a result, tourism has become the premier industry in Florida,
                supporting more than 1.6 million jobs. In 2019, approximately 131.4 million out-of-state visitors came to Florida,
                spending $98.8 billion dollars.
            </p>
            <p>
                While development and tourism have been an economic boon to Florida, they have also brought and compounded
                associated problems that must be continually addressed.
            </p>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
