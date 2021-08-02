<div id="mainContainer">
    <div id="bannerContainer">
        <h1 class="title">Your New Symbiota Portal</h1>
    </div>
    <div id="topNavigation">
        <ul id="horizontalDropDown">
            <li>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" >Home</a>
            </li>
            <li>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/index.php" >Search Collections</a>
            </li>
            <li>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" target="_blank">Spatial Module</a>
            </li>
            <li>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/search.php" >Image Search</a>
            </li>
            <li>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/index.php" >Browse Images</a>
            </li>
            <?php
            if($GLOBALS['IS_ADMIN']){
                ?>
                <li>
                    <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/admin/specifyupdater.php" >Specify Updater</a>
                </li>
                <?php
            }
            ?>
        </ul>
        <div id="rightNavigationLinks">
            <?php
            if($GLOBALS['USER_DISPLAY_NAME']){
                ?>
                <span>Welcome <?php echo $GLOBALS['USER_DISPLAY_NAME']; ?>!</span>
                <span><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/viewprofile.php">My Profile</a></span>
                <span><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/index.php?submit=logout">Logout</a></span>
                <?php
            }
            else{
                ?>
                <span><a href="<?php echo $GLOBALS['CLIENT_ROOT']. '/profile/index.php?refurl=' .$_SERVER['PHP_SELF']. '?' .$_SERVER['QUERY_STRING']; ?>">Log In</a></span>
                <span><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/newprofile.php">New Account</a></span>
                <?php
            }
            ?>
            <span><a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitemap.php'>Sitemap</a></span>
        </div>
    </div>
