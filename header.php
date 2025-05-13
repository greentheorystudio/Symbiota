<div id="bannerContainer">
    <div style="background-color:#2F507F;width:100%;clear:both;height:200px;border-bottom:1px solid #333333;"></div>
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
        include(__DIR__ . '/header-login.php');
        ?>
    </div>
    <div class="nav-bar-container">
        <?php
        include(__DIR__ . '/header-navigation.php');
        ?>
    </div>
</div>
