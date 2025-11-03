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
    <title>The Indian River Lagoon: A Mosaic of Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/1_11_LawrenceL2.jpg");
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
</head>
<body>
<a class="screen-reader-only" href="#page-title-container" tabindex="0">Skip to main content</a>
<div class="hero-container">
    <span class="screen-reader-only" role="img" aria-label="Flock of birds flying over a wetland with water patches, grassy vegetation, and a tree-lined horizon."> </span>
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
            <b>Habitats</b>
        </div>
    </div>
    <div id="page-title-container" class="page-title-container">
        <h1>Habitats</h1>
    </div>
    <div class="top-text-container">
        <h2>
            Simply put, a habitat is the specific place in an environment where an organism lives. The Indian River Lagoon
            is a diverse mosaic of habitats that span barrier islands, interior uplands and the waters of the lagoon itself.
        </h2>
    </div>
    <div class="photo-credit-container">
        Photo credit: L. Lawrence
    </div>
</div>
<div id="innertext">
    <p>
        Oceanfront dunes and beaches of the barrier islands give way to maritime hammocks and mangrove fringes. In the lagoon,
        seagrass beds, oyster reefs and spoil islands provide critical habitat for numerous species. Past the mangroves
        lie freshwater swamps, hardwood hammocks and upland forests that characterize interior Florida.
    </p>
    <p>
        Learn more about the IRL’s spectrum of habitats.
    </p>
    <div style="display:flex;justify-content: space-around;align-content: center;">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Barrierislnd.php" aria-label="Go to Barrier Islands" tabindex="0">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/2_09_RichardsR1_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Misty landscape with leafless trees, perched birds, dense foliage, and distant water."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Barrier Islands</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Beaches.php" aria-label="Go to Beaches" tabindex="0">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/3_vero_beach_aerial_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Aerial view of a beach with turquoise water, white sand, a pier, and nearby parking lot surrounded by greenery."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Beaches</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div style="display:flex;justify-content: space-around;align-content: center;margin-top: 10px;">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Dunes.php" aria-label="Go to Dunes" tabindex="0">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/4_18PastorR1_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Red lighthouse on a sandy dune with birds flying and resting under a clear blue sky."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Dunes</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Mangroves.php" aria-label="Go to Mangroves" tabindex="0">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/5_09_WhiticarJ2_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Mangrove trees with aerial roots in shallow water under a partly cloudy sky."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Mangroves</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div style="display:flex;justify-content: space-around;align-content: center;margin-top: 10px;">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Hammock_Habitat.php" aria-label="Go to Maritime Hammocks" tabindex="0">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/6_Rasmussen-KP-Woods_4638_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Forest path winding through tall trees draped in Spanish moss and dense green foliage."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Maritime Hammocks</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Oyster_reef.php" aria-label="Go to Oyster Reefs" tabindex="0">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/7_20SacksP1_N_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Coastal scene with oyster beds and mangroves under a calm blue sky."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Oyster Reefs</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div style="display:flex;justify-content: space-around;align-content: center;margin-top: 10px;">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Saltmarsh.php" aria-label="Go to Salt Marshes" tabindex="0">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/8_11_SmithA1_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Cracked earth of a dried riverbed at sunset, with grasses along the edges and a clear sky."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Salt Marshes</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Scrub.php" aria-label="Go to Scrub" tabindex="0">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/9_Dune_Westward_Canaveral_National_Seashore_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Sunset over a landscape with spiky plants, palm trees, and a partly cloudy sky."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Scrub</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div style="display:flex;justify-content: space-around;align-content: center;margin-top: 10px;">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Seagrass_Habitat.php" aria-label="Go to Seagrass Beds" tabindex="0">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/10_t_testudinum_wikimedia_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Close-up of underwater seagrass with overlapping green and brown blades."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Seagrass Beds</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Tidal_Flats.php" aria-label="Go to Tidal Flats" tabindex="0">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/11_13_FischerD1_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Sandy beach with wind-formed patterns, calm water, and a small island under a clear sky."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Tidal Flats</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
<?php
include_once(__DIR__ . '/../config/footer-includes.php');
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
