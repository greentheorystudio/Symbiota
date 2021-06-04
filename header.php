<table id="maintable" style="border-spacing: 0;">
    <tr>
        <td class="header">
            <div id="bannerDiv" style="position:relative;">
                <div style="float:right;margin-top:20px;">
                    <img style="" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/calIBIS-logo.png" />
                </div>
                <div id="imageCredit" style="position:absolute;bottom:5px;right:5px;">
                    <div style="background-color:white;opacity:60%;color:black;padding:5px;font-size: 12px;">
                        (photographer: Denise Knapp)
                    </div>
                </div>
            </div>
            <div id="top_navbar">
                <div id="right_navbarlinks">
                    <?php
                    if($GLOBALS['USER_DISPLAY_NAME']){
                        ?>
                        <span style="">
							Welcome <?php echo $GLOBALS['USER_DISPLAY_NAME']; ?>!
						</span>
                        <span style="margin-left:5px;">
							<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/viewprofile.php">My Profile</a>
						</span>
                        <span style="margin-left:5px;">
							<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/index.php?submit=logout">Logout</a>
						</span>
                        <?php
                    }
                    else{
                        ?>
                        <span style="">
							<a href="<?php echo $GLOBALS['CLIENT_ROOT']. '/profile/index.php?refurl=' .$_SERVER['PHP_SELF']. '?' .$_SERVER['QUERY_STRING']; ?>">
								Log In
							</a>
						</span>
                        <span style="margin-left:5px;">
							<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/newprofile.php">
								New Account
							</a>
						</span>
                        <?php
                    }
                    ?>
                    <span style="margin-left:5px;margin-right:5px;">
						<a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitemap.php'>Sitemap</a>
					</span>

                </div>
                <ul id="hor_dropdown">
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
            </div>
        </td>
    </tr>
    <tr>
        <td class="middlecenter">
