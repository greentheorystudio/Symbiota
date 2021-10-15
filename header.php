<?php
include_once(__DIR__ . '/classes/Sanitizer.php');
?>
<div id="mainContainer">
    <div style="background-image:url(<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/background.jpg);background-repeat:repeat-x;background-position:top;width:100%;clear:both;height:150px;border-bottom:1px solid #333333;">
        <div style="float:left;">
            <img style="" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/BioMNA.jpg"  alt=""/>
        </div>
    </div>
    <div id="topNavigation">
        <ul id="horizontalDropDown">
            <li>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" >Home</a>
            </li>
            <li>
                <a>Search</a>
                <ul>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/index.php" >Search Collections</a>
                    </li>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" target="_blank">Spatial Module</a>
                    </li>
                </ul>
            </li>
            <li>
                <a>Images</a>
                <ul>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/search.php" >Image Search</a>
                    </li>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/index.php" >Browse Images</a>
                    </li>
                </ul>
            </li>
            <?php
            if($GLOBALS['SYMB_UID']){
                if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array(8, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)) || (array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array(8, $GLOBALS['USER_RIGHTS']['CollEditor'], true))){
                    ?>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collprofiles.php?collid=8&emode=1" >Collection Management</a>
                    </li>
                    <?php
                }
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
                <span><a href="<?php echo $GLOBALS['CLIENT_ROOT']. '/profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true); ?>">Log In</a></span>
                <span><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/newprofile.php">New Account</a></span>
                <?php
            }
            ?>
            <span><a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitemap.php'>Sitemap</a></span>
        </div>
    </div>
