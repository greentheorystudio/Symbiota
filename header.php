<?php
include_once(__DIR__ . '/classes/Sanitizer.php');
?>
<div id="mainContainer">
    <div id="bannerDiv">
        <div style="float:right;margin-top:20px;">
            <img style="" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/calIBIS-logo.png" />
        </div>
        <div id="imageCredit" style="position:absolute;bottom:5px;right:5px;">
            <div style="background-color:white;opacity:60%;color:black;padding:5px;font-size: 12px;">
                (photographer: Denise Knapp)
            </div>
        </div>
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
            <li>
                <a href="#" >Interactive Tools</a>
                <ul>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=checklist" >Dynamic Checklist</a>
                    </li>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=key" >Dynamic Key</a>
                    </li>
                </ul>
            </li>
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
