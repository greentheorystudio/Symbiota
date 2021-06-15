<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);

$IRLManager = new IRLManager();

$mangrovePlantArr = $IRLManager->getChecklistTaxa(15);
$mangroveAlgaeArr = $IRLManager->getChecklistTaxa(16);
$mangroveAnimalArr = $IRLManager->getChecklistTaxa(17);
$vernacularArr = $IRLManager->getChecklistVernaculars();
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
            width: 100%;
            height: 1000px;
            background-position: center bottom;
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
        }

        .top-shade-container {
            position: absolute;
            top: 0;
            left: 0;
            height: 150px;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 5000000;
        }

        .logo-image {
            height: 160px;
        }

        .title-container {
            position: absolute;
            top: 30px;
            left: 150px;
            color: white;
            padding-left: 20px;
        }

        .login-container {
            position: absolute;
            top: 15px;
            right: 0;
            background-color: rgba(0, 0, 0, 0.5);
            width: 500px;
            height: 35px;
            padding-left: 10px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .login-container a {
            color: white;
        }

        .login-link {
            font-family: 'Fira Sans';
            font-size: 9pt;
            color: white;
            margin: 10px;
        }

        .nav-bar-container {
            position: absolute;
            top: 150px;
            left: 0;
        }

        .breadcrumb-container {
            position: absolute;
            top: 225px;
            left: 30px;
            width: 100%;
        }

        .page-title-container {
            position: absolute;
            top: 280px;
            left: 35px;
            padding: 0 20px;
            background-color: rgba(226, 232, 236, 0.58);
        }

        .top-text-container {
            position: absolute;
            top: 360px;
            left: 35px;
            width: 85%;
            padding: 0 20px;
            background-color: rgba(226, 232, 236, 0.58);
        }
        #bodyContainer{
            margin-top: 30px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        #innertext{
            position: sticky;
            min-height: 200px;
            width: 60%;
            z-index: 1;
        }
        ol, ul {
            list-style: none;
        }
        .sideNavMover {
            position: sticky;
            top: 50px;
            left: 0;
            z-index: 50000;
        }
        .sideNavContainer {
            position: relative;
        }
        .no-touch #cd-vertical-nav {
            position: absolute;
            left: 0;
            top: 0;
        }
        .no-touch #cd-vertical-nav a {
            display: inline-block;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }
        .no-touch #cd-vertical-nav a:after {
            content: "";
            display: table;
            clear: both;
        }
        .no-touch #cd-vertical-nav a span {
            float: left;
            display: inline-block;
            -webkit-transform: scale(0.6);
            -moz-transform: scale(0.6);
            -ms-transform: scale(0.6);
            -o-transform: scale(0.6);
            transform: scale(0.6);
        }
        .no-touch #cd-vertical-nav a:hover span, .no-touch #cd-vertical-nav a.is-selected span {
            -webkit-transform: scale(1);
            -moz-transform: scale(1);
            -ms-transform: scale(1);
            -o-transform: scale(1);
            transform: scale(1);
        }
        .no-touch #cd-vertical-nav a:hover .cd-label {
            opacity: 1;
        }
        .no-touch #cd-vertical-nav a.is-selected .cd-dot, .no-touch #cd-vertical-nav a:hover .cd-dot {
            background-color: #EF434C;
        }
        .no-touch #cd-vertical-nav .cd-dot {
            position: relative;
            top: 8px;
            height: 12px;
            width: 12px;
            border-radius: 50%;
            background-color: #242038;
            -webkit-transition: -webkit-transform 0.2s, background-color 0.5s;
            -moz-transition: -moz-transform 0.2s, background-color 0.5s;
            transition: transform 0.2s, background-color 0.5s;
            -webkit-transform-origin: 50% 50%;
            -moz-transform-origin: 50% 50%;
            -ms-transform-origin: 50% 50%;
            -o-transform-origin: 50% 50%;
            transform-origin: 50% 50%;
        }
        .no-touch #cd-vertical-nav .cd-label {
            position: relative;
            margin-left: 20px;
            margin-top: -10px;
            padding: .4em .5em;
            width: 300px;
            color: black;
            font-size: 0.875rem;
            -webkit-transition: -webkit-transform 0.2s, opacity 0.2s;
            -moz-transition: -moz-transform 0.2s, opacity 0.2s;
            transition: transform 0.2s, opacity 0.2s;
            opacity: 0;
            -webkit-transform-origin: 100% 50%;
            -moz-transform-origin: 100% 50%;
            -ms-transform-origin: 100% 50%;
            -o-transform-origin: 100% 50%;
            transform-origin: 100% 50%;
        }
        .touch #cd-vertical-nav {
            position: fixed;
            z-index: 1;
            right: 5%;
            bottom: 30px;
            width: 90%;
            max-width: 400px;
            max-height: 90%;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
            -webkit-transform-origin: right bottom;
            -moz-transform-origin: right bottom;
            -ms-transform-origin: right bottom;
            -o-transform-origin: right bottom;
            transform-origin: right bottom;
            -webkit-transform: scale(0);
            -moz-transform: scale(0);
            -ms-transform: scale(0);
            -o-transform: scale(0);
            transform: scale(0);
            -webkit-transition-property: -webkit-transform;
            -moz-transition-property: -moz-transform;
            transition-property: transform;
            -webkit-transition-duration: 0.2s;
            -moz-transition-duration: 0.2s;
            transition-duration: 0.2s;
            border-radius: 0.25em;
            background-color: rgba(255, 255, 255, 0.9);
        }
        .touch #cd-vertical-nav a {
            display: block;
            padding: 1em;
            border-bottom: 1px solid rgba(62, 57, 71, 0.1);
        }
        .touch #cd-vertical-nav a span:first-child {
            display: none;
        }
        .touch #cd-vertical-nav a.is-selected span:last-child {
            color: #d88683;
        }
        .touch #cd-vertical-nav.open {
            -webkit-transform: scale(1);
            -moz-transform: scale(1);
            -ms-transform: scale(1);
            -o-transform: scale(1);
            transform: scale(1);
        }
        .touch #cd-vertical-nav li:last-child a {
            border-bottom: none;
        }

        @media only screen and (min-width: 768px) {
            .touch #cd-vertical-nav {
                bottom: 40px;
            }
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/modernizr.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var contentSections = $('.cd-section');
            var navigationItems = $('#cd-vertical-nav a');

            updateNavigation();

            document.addEventListener('scroll', function (event) {
                updateNavigation();
            }, true);

            navigationItems.on('click', function(event){
                event.preventDefault();
                smoothScroll(this.hash);
            });

            $('.touch #cd-vertical-nav a').on('click', function(){
                $('.touch #cd-vertical-nav').removeClass('open');
            });

            function updateNavigation() {
                contentSections.each(function(){
                    $this = $(this);
                    var activeSection = $('#cd-vertical-nav a[href="#'+$this.attr('id')+'"]').data('number') - 1;
                    if ( ( $this.offset().top - $(window).height()/2 < $(window).scrollTop() ) && ( $this.offset().top + $this.height() - $(window).height()/2 > $(window).scrollTop() ) ) {
                        navigationItems.eq(activeSection).addClass('is-selected');
                    }else {
                        navigationItems.eq(activeSection).removeClass('is-selected');
                    }
                });
            }

            function smoothScroll(hash) {
                var element = document.querySelector(hash);
                element.scrollIntoView({ behavior: 'smooth', block: 'start'});
            }
        });
    </script>
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
        <p class="intro-text">
            One of Florida's true natives, mangroves are uniquely evolved to leverage the state's many miles of low-lying
            coastline for their benefit.
        </p>
        <p class="intro-text">
            Flooded twice daily by ocean tides, or fringing brackish waters like the Indian River Lagoon, mangroves are bristling
            with adaptations that allow them to thrive in water up to 100 times saltier than most plants can tolerate.
        </p>
    </div>
</div>
<div id="bodyContainer">
    <div class="sideNavMover">
        <div class="sideNavContainer">
            <nav id="cd-vertical-nav">
                <ul>
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
                    <img style="border:0;width:300px;margin: 10px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/red_mangrove%20.jpg" />
                    <img style="border:0;width:300px;margin: 10px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/black_mangrove.jpg" />
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
            <div style="margin:15px;display:flex;justify-content: space-around;">
                <img style="border:0;width:300px;margin: 10px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/wave_action.jpg" />
                <img style="border:0;width:300px;margin: 10px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/leaf_litter.JPG" />
                <img style="border:0;width:300px;margin: 10px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/nursery%20.jpg" />
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
