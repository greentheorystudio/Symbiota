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
    <title>Habitat Threats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/20MandevilleJ2_S.jpg");
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
</head>
<body>
<div class="hero-container">
    <span class="screen-reader-only" role="img" aria-label="Storm clouds over water with a docked boat and distant red lighthouse."> </span>
    <div class="top-shade-container"></div>
    <div class="logo-container">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" class="header-home-link" >
            <img class="logo-image" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/janky_mangrove_logo_med.png" alt="Mangrove logo" />
        </a>
    </div>
    <div class="title-container">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" class="header-home-link" >
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
            <a href="Maps.php">The Indian River Lagoon</a> &gt;
            <b>Threats</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Threats</h1>
    </div>
    <div class="top-text-container">
        <h3>
            Humans are drawn to the Indian River Lagoon estuary for many of the same reasons its abundance of wildlife is:
            a favorable climate, a coastal location, a wealth of natural resources. However, the increasing numbers of humans
            in the lagoon watershed over the last century has disrupted its fragile ecological balance.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: J. Mandeville
    </div>
</div>
<div id="innertext">
    <p>
        The problems facing the lagoon are complex, ranging from changes in its water flow patterns, loss of habitat, degraded
        water and sediment quality, and losses of native species. At the same time, shifts in global climate patterns also
        threaten the estuary. Scientists and citizens across the region are engaged in a wide range of efforts to better understand,
        restore and protect the Indian River Lagoon’s unique ecology.
    </p>
    <div style="display:flex;justify-content: space-around;align-content: center;">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/impoundments.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/12_PowellD2_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Aerial view of coastal islands and water channels under a partly cloudy sky."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Mosquito Impoundments</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/development.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/stuart_shore_KRoark_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Beach with waves, high-rises, palm trees, and a pool under a partly cloudy sky."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Shoreline Development</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/muck-nutrients.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/muck_LHS_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Close-up of two hands holding Indian River Lagoon muck."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Muck & Nutrients</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div style="display:flex;justify-content: space-around;align-content: center;margin-top: 10px;">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/invasives.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/lionfish_FWS_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Lionfish with striped pattern and long spines swimming above gravel bottom."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Invasive Species</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/weather.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/17AdamsN1_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Lightning storm over water with city lights in the distance."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Extreme Weather</div>
                    </div>
                </div>
            </div>
        </a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/climate-change.php">
            <div class="nav-item-container">
                <div style='width:100%;height:250px;background-image: url("../content/imglib/static/dorian_NOAA_crop.jpg");background-position: center;background-repeat: no-repeat;background-size: cover;'></div>
                <span class="screen-reader-only" role="img" aria-label="Satellite view of a hurricane with a visible eye approaching Florida."> </span>
                <div style="background-color: rgba(226, 232, 236, 0.64);width:100%;height:50px;">
                    <div style="padding:10px;width:300px;">
                        <div style="font-family: 'Fira Sans';font-size: 16pt;font-weight: bold;">Climate Change</div>
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
