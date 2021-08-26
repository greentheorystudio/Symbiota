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
            <li>
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?" >Inventories</a>
                <!-- <ul>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?pid=1" >Project 1</a>
                    </li>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?pid=2" >Project 2</a>
                    </li>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?pid=3" >Project 3</a>
                    </li>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?pid=4" >Project 4</a>
                    </li>
                </ul> -->
            </li>
            <li>
                <a href="#" >Interactive Tools</a>
                <!-- <ul>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=checklist&tid=1" >Dynamic Checklist 1</a>
                    </li>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=checklist&tid=2" >Dynamic Checklist 2</a>
                    </li>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=checklist&tid=3" >Dynamic Checklist 3</a>
                    </li>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=checklist&tid=4" >Dynamic Checklist 4</a>
                    </li>
                </ul> -->
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
                <span><a href="<?php echo $GLOBALS['CLIENT_ROOT']. '/profile/index.php?refurl=' .$_SERVER['PHP_SELF']. '?' .str_replace('&amp;', '&',htmlspecialchars($_SERVER['QUERY_STRING'], ENT_NOQUOTES)); ?>">Log In</a></span>
                <span><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/newprofile.php">New Account</a></span>
                <?php
            }
            ?>
            <span><a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitemap.php'>Sitemap</a></span>
        </div>
    </div>
