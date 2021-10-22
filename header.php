<?php
include_once(__DIR__ . '/classes/Sanitizer.php');
?>
<div id="mainContainer">
    <div id="bannerContainer" style="clear:both;background-color:#D9E636;height:175px;border-bottom:1px solid black;">
        <div style="float:left;font-family: Chalkboard,Comic Sans MS,Comic Sans,cursive;color:black;font-size:40px;">
            <div style="height:175px;width:400px;display:flex;flex-direction:column;justify-content:center;align-items:center;">
                <div>Lomatium & Friends</div>
                <div>Online Monographs</div>
            </div>
        </div>
        <div style="float:right;">
            <img style="height:175px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/Banner3.JPG" />
        </div>
    </div>
    <div id="topNavigation">
        <ul id="horizontalDropDown">
            <li>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" >Home</a>
            </li>
            <li>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/project.php" >The Project</a>
            </li>
            <li>
                <a href="" >Trees</a>
            </li>
            <li>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/index.php" >Specimen Search</a>
            </li>
            <li>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/search.php" >Image Search</a>
            </li>
            <li>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" target="_blank" >Map Search</a>
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
