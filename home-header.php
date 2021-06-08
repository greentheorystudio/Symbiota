<div class="hero-container">
    <div class="title-container">
        <h1>Indian River Lagoon<br />
            Species Inventory</h1>
    </div>
    <div class="login-container">
        <?php
        if($GLOBALS['USER_DISPLAY_NAME']){
            ?>
            <div class="login-link">
                Welcome <?php echo $GLOBALS['USER_DISPLAY_NAME']; ?>!
            </div>
            <div>
                <a class="login-link" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/viewprofile.php">My Profile</a>
            </div>
            <div>
                <a class="login-link" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/index.php?submit=logout">Logout</a>
            </div>
            <?php
        }
        else{
            ?>
            <div>
                <a class="login-link" href="<?php echo $GLOBALS['CLIENT_ROOT']."/profile/index.php?refurl=".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>">
                    Log In
                </a>
            </div>
            <div>
                <a class="login-link" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/newprofile.php">
                    New Account
                </a>
            </div>
            <?php
        }
        ?>
        <div>
            <a class="login-link" href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitemap.php'>Sitemap</a>
        </div>
    </div>
    <div class="nav-bar-container">
        <?php
        include(__DIR__ . '/header-navigation.php');
        ?>
    </div>
    <div class="quicksearch-container">
        <div class="searcharea">
            <div class="searchtop">
                <div class="as"> <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/dynamictaxalist.php"> Advanced Search</a></div>
                <?php
                $searchText = '';
                $buttonText = 'Search';
                $placeholderText = 'Scientific Name';
                include_once(__DIR__ . '/classes/PluginsManager.php');
                $pluginManager = new PluginsManager();
                $pluginManager->setQuickSearchShowSelector(true);
                $pluginManager->setQuickSearchDefaultSetting('common');
                $quicksearch = $pluginManager->createQuickSearch($buttonText,$searchText);
                echo $quicksearch;
                ?>
            </div>
        </div>
    </div>
    <div class="heading-container">
        <div class="heading-inner">
            <h3>The <b>Indian River Lagoon Species Inventory</b> is a dynamic and growing research resource and ecological encyclopedia that documents the biodiversity of the 156-mile-long estuary system along Florida’s Atlantic coast.</h3>
        </div>
    </div>
</div>
