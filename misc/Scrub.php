<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title>Scrub Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/1_Dune_Westward_Canaveral_National_Seashore.jpg");
            background-position: center bottom;
        }

        #innertext{
            position: sticky;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/static-page.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
</head>
<body>
<a class="screen-reader-only" href="#page-title-container" tabindex="0">Skip to main content</a>
<div class="hero-container">
    <span class="screen-reader-only" role="img" aria-label="Sunset over a landscape with palm trees and vegetation, with clouds lit by the setting sun."> </span>
    <div class="top-shade-container"></div>
    <div class="logo-container">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" class="header-home-link" aria-label="Go to homepage" tabindex="0">
            <img class="logo-image" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/janky_mangrove_logo_med.png" alt="Mangrove logo" />
        </a>
    </div>
    <div class="title-container">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" class="header-home-link" aria-label="Go to homepage" tabindex="0">
            <span class="titlefont">Indian River Lagoon<br />
            Species Inventory</span>
        </a>
    </div>
    <div class="login-bar">
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
            <a href="Maps.php" tabindex="0">The Indian River Lagoon</a> &gt;
            <a href="Whatsa_Habitat.php" tabindex="0">Habitats</a> &gt;
            <b>Scrub</b>
        </div>
    </div>
    <div id="page-title-container" class="page-title-container">
        <h1>Scrub</h1>
    </div>
    <div class="top-text-container">
        <h2>
            Built upon sandy or well-drained soils on stable backdune areas, scrub communities are dominated by herbaceous
            shrubs, evergreen oaks and pines. Also known as coastal strand, these ecosystems are rapidly vanishing due to
            developmental pressures along the coast.
        </h2>
    </div>
    <div class="photo-credit-container">
        Photo credit: H. Sweat
    </div>
</div>
<div id="bodyContainer">
    <div class="sideNavMover">
        <div class="sideNavContainer">
            <nav id="cd-vertical-nav">
                <ul class="vertical-nav-list">
                    <li>
                        <a href="#intro-section" data-number="1" aria-label="Skip to Intro" tabindex="0">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Intro</span>
                        </a>
                    </li>
                    <li>
                        <a href="#scrub-composition-section" data-number="2" aria-label="Skip to Scrub Composition" tabindex="0">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Scrub Composition</span>
                        </a>
                    </li>
                    <li>
                        <a href="#fire-ecology-section" data-number="3" aria-label="Skip to Fire Ecology" tabindex="0">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Fire Ecology</span>
                        </a>
                    </li>
                    <li>
                        <a href="#scrub-plants-section" data-number="4" aria-label="Skip to Scrub Plants" tabindex="0">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Scrub Plants</span>
                        </a>
                    </li>
                    <li>
                        <a href="#scrub-animals-section" data-number="5" aria-label="Skip to Scrub Animals" tabindex="0">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Scrub Animals</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                Most coastal habitats from Cape Canaveral in Brevard County to Miami in Dade County have been highly fragmented
                due to development. In Brevard County alone, the natural scrub community is estimated to have diminished by
                69 percent during from 1943 to 1991; over roughly the same period, population increased by nearly 3,000 percent.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/2_Jonathan_Dickinson_State_Park_Overlook.jpg" alt="Overlook of Johnathan Dickenson State Park at sunset." />
                    <figcaption>
                        Jonathan Dickinson State Park overlook. <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="scrub-composition-section" class="cd-section">
            <div class="text-h5 text-bold">Scrub Composition</div>
            <p>
                Except for saw palmetto scrub, the term “scrub” refers to well-drained, open pineland with oak or palmetto
                understory that are well-adapted to dry conditions. Scrub habitats are further divided into subcategories
                based on vegetation structure, composition, soil type, geography and fire patterns. Scrub subtypes include
                coastal scrub, oak scrub, sand pine scrub, rosemary scrub, slash pine scrub, and scrubby flatwoods.
            </p>
            <p>
                Leaf fall in scrub areas is minimal, and ground cover generally sparse due to shading by overstory trees.
                Open patches of sand are often present in scrub lands, and where they occur, understory trees and woody shrubs
                benefit from the intense sunlight that reaches the ground.
            </p>
            <p>
                Florida's scrub and pine flatwoods consist of similar shrub layers, with pine flatwoods differing by having
                an open canopy of slash pine intermingled with pond pine. Drier areas tend to be dominated by scrub oaks,
                while less well-drained areas are dominated by saw palmetto. Many Indian River Lagoon sites feature mixed
                oak-palmetto shrub layers.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/3_Merritt_Island_prescribed_burn_USFWS.jpg" alt="Firefighter standing before a prescribed burn on Merritt Island." />
                    <figcaption>
                        Prescribed burn on Merritt Island. <i>Credit: US Fish and Wildlife Service</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="fire-ecology-section" class="cd-section">
            <div class="text-h5 text-bold">Fire Ecology</div>
            <p>
                While strong winds and flooding influence the character and structure of scrub habitats, fire has been the
                historic primary force shaping these ecosystems.
            </p>
            <p>
                Low leaf fall coupled with sparse ground vegetation reduce overall fire risk, but as sand pines mature, their
                crowns build up large fuel reserves that feed hot, fast-moving fires. These events regenerate the scrub
                community and prevents its succession to an oak hammock or scrubby flatwoods community. Fire also disperses
                pine seeds, recycles minerals back into the earth as ash, and diminishes the oak and palmetto understory.
            </p>
            <p>
                Many herbaceous scrub species are gap specialists, vulnerable to competition and eventual exclusion from
                scrub areas. These plants benefit from reduced competition in the burn zone following a fire, as seen by a
                boost in their abundance in an area after a fire than when the same zone is fire-free for a longer period.
            </p>
            <p>
                Frequent fires are more beneficial to oak scrub and scrubby flatwoods communities; less frequent fires are
                more beneficial to sand pine scrub and other pine-dominated scrub types.
            </p>
        </div>
        <div id="scrub-plants-section" class="cd-section">
            <div class="text-h5 text-bold">Scrub Plants</div>
            <p>
                East central Florida's barrier islands scrub is dominated primarily by saw palmetto and other common shrubs
                such as nakedwood, tough buckthorn, rapanea, hercules club, bay, sea grapes and snowberry. Shrubby forms of
                live oak are also common in coastal scrub communities.
            </p>
            <div style="display:flex;justify-content: center;align-content: center;">
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Coccoloba_Seagrape_Sweat.jpg" alt="Cluster of seagrape leaves with red veins and a glossy texture." />
                    <figcaption>
                        <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Zanthoxylum_Hercules_Sweat.jpg" alt="Close-up of Hercules club plant with small green berries and purple thorns." />
                    <figcaption>
                        <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Serenoa_repens_L_Holly_Sweat.jpg" alt="Cluster of green palmetto plants with fan-shaped leaves in a forested area." />
                    <figcaption>
                        <i>Credit: H. Sweat</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Indicator species for other types of scrub communities include sand pine, myrtle oak, scrub live oak, Chapman's oak,
                coastalplain goldenaster, and narrowleaf silkgrass.
            </p>
        </div>
        <div id="scrub-animals-section" class="cd-section">
            <div class="text-h5 text-bold">Scrub Animals</div>
            <p>
                Some of Florida's most threatened and endangered species rely on scrub habitat—and many species are found
                only in Florida. Among them are the gopher tortoise, the eastern indigo snake, the southeastern beach mouse,
                and the Florida scrub jay. Many other animals also utilize scrub areas for feeding and for shelter.
            </p>
            <div style="display:flex;justify-content: center;align-content: center;">
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/11_RogersJ1.jpg" alt="Blue Florida scrub jay perched on a branch with green foliage in the background." />
                    <figcaption>
                        <i>Credit: J. Rogers</i>
                    </figcaption>
                </figure>
                <figure style="margin:0;">
                    <img style="border:0;height:200px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/11_PichonK1.jpg" alt="Close-up of a tortoise with a patterned shell in grassy surroundings." />
                    <figcaption>
                        <i>Credit: K. Pichon</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                The Florida mouse is entirely restricted to the state; its nearest relatives live in southern Mexico. Burrowing
                gopher tortoises create a diversity of microhabitats upon which numerous other species rely.
            </p>
        </div>
    </div>
</div>
<?php
include_once(__DIR__ . '/../config/footer-includes.php');
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
