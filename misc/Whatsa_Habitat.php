<?php
include_once(__DIR__ . '/../config/symbbase.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>The Indian River Lagoon: A Mosaic of Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/1_11_LawrenceL2.jpg");
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
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
            <b>Habitats</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Habitats</h1>
    </div>
    <div class="top-text-container">
        <h3>
            Simply put, a habitat is the specific place in an environment where an organism lives. The Indian River Lagoon
            is a diverse mosaic of habitats that span barrier islands, interior uplands and the waters of the lagoon itself.
        </h3>
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
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Barrierislnd.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/2_09_RichardsR1_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Barrier Islands</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Beaches.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/3_vero_beach_aerial_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Beaches</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div style="display:flex;justify-content: space-around;align-content: center;margin-top: 10px;">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Dunes.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/4_18PastorR1_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Dunes</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Mangroves.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/5_09_WhiticarJ2_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Mangroves</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div style="display:flex;justify-content: space-around;align-content: center;margin-top: 10px;">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Hammock_Habitat.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/6_Rasmussen-KP-Woods_4638_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Maritime Hammocks</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Oyster_reef.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/7_20SacksP1_N_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Oyster Reefs</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div style="display:flex;justify-content: space-around;align-content: center;margin-top: 10px;">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Saltmarsh.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/8_11_SmithA1_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Salt Marshes</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Scrub.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/9_Dune_Westward_Canaveral_National_Seashore_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Scrub</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div style="display:flex;justify-content: space-around;align-content: center;margin-top: 10px;">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Seagrass_Habitat.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/10_t_testudinum_wikimedia_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Seagrass Beds</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/Tidal_Flats.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/11_13_FischerD1_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
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
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
