<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mangrove Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../images/layout/Mangrove_top_photo.jpg");
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
        <h1>Indian River Lagoon<br />
            Species Inventory</h1>
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
            <a href="Whatsa_Habitat.php">Habitats</a> &gt;
            <b>Mangroves</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Mangroves</h1>
    </div>
    <div class="top-text-container">
        <h3>
            One of Florida's true natives, mangroves are uniquely evolved to leverage the state's many miles of low-lying
            coastline for their benefit.
        </h3>
        <h3>
            Flooded twice daily by ocean tides, or fringing brackish waters like the Indian River Lagoon, mangroves are bristling
            with adaptations that allow them to thrive in water up to 100 times saltier than most plants can tolerate.
        </h3>
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
                        <a href="#species-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Mangrove Species</span>
                        </a>
                    </li>
                    <li>
                        <a href="#environmental-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Environmental Benefits</span>
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
                acres of mangroves, which play critical ecological roles as fish nurseries, shoreline stabilizers, and
                pollution mitigators.
            </p>
            <p>
                However, an estimated 76 to 85 percent of this acreage has become inaccessible as nursery habitat for
                local fisheries through construction of mosquito ditches and impoundments. Though mangroves are
                protected in Florida by state law, mangrove habitat loss is a problem globally as these edge ecosystems
                are removed to make way for commercial and agricultural development.
            </p>
        </div>
        <div id="species-section" class="cd-section">
            <h4>Mangrove Species</h4>
            <div style="display:flex;justify-content: space-between">
                <div style="display:flex;flex-direction:column;">
                    <img style="border:0;width:400px;margin: 20px 25px 10px 0;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/red_mangrove%20.jpg" />
                    <img style="border:0;width:400px;margin: 4px 25px 10px 4px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/black_mangrove.jpg" />
                </div>
                <div>
                    <p>
                        In the IRL, the most recognizable and common species is the <b>red mangrove</b>. Dominating the shoreline from
                        the upper subtidal to the lower intertidal zones, red mangroves are easily identified by their tangled,
                        reddish “prop roots” that grow outward and down from the trunk into the water and underlying sediments.
                        The tree often appears to be standing or walking on the surface of the water.
                    </p>
                    <p>
                        In the tropics, trees may grow to more than 80 feet (24 meters) in height; however, in Florida, trees
                        typically average around 20 feet (6 meters) in height. Leaves have glossy, bright green upper surfaces
                        and pale undersides. Trees flower throughout the year, peaking in spring and summer. The seed-like
                        propagules of the red mangrove are pencil-shaped, and may reach nearly a foot in length (30 cm) as they
                        mature on the parent tree.
                    </p>
                    <p>
                        <b>Black mangroves</b> typically grow immediately inland of red mangroves. Though they may reach 65 feet
                        (20 meters) in some locations, Florida populations typically grow to 50 feet (15 meters.) Instead of
                        prop roots, black mangroves feature thick stands of pneumatophores, stick-like branches which aid in
                        aeration that grow upward from the ground. Leaves are narrower than those of red mangroves, and are often
                        encrusted with salt. Black mangroves flower throughout spring and early summer, producing lima
                        bean-shaped propagules.
                    </p>
                </div>
            </div>
        </div>
        <div id="environmental-section" class="cd-section">
            <h4>Environmental Benefits</h4>
            <p>
                Long viewed as inhospitable, marginal environments, mangroves provide numerous environmental benefits.
            </p>
            <p>
                Often referred to as the bridge between land and sea, mangroves provide their coastlines a critical
                first line of defense against erosion and storms. Aerial roots trap sediments in and assist in preventing
                coastal erosion. As storms approach and pass by, branches in the canopies and roots in the water reduce
                the force of winds and waves, blunting the edge of strong storm impacts.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: space-between;">
                <img style="border:0;width:320px;margin: 0 0 10px 0;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/wave_action.jpg" />
                <img style="border:0;width:320px;margin: 0 0 10px 0;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/leaf_litter.JPG" />
                <img style="border:0;width:320px;margin: 0 0 10px 0;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/nursery%20.jpg" />
            </div>
            <p>
                Mangroves are extremely absorbent carbon sinks—but managed improperly, they stand to be supercharged
                sources of carbon that could exacerbate global warming processes. The 34 million acres (14 million
                hectares) of global mangrove forest occupies the equivalent of only 2.5 percent of the Amazon rain
                forest. As of 2000, that land area was found to hold an estimated 6.4 billion metric tons of carbon,
                or 8 percent of the Amazon basin’s approximately 76 billion tons of sequestered soil carbon.
            </p>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
