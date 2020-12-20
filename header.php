<table id="maintable" style="border-spacing: 0;">
    <tr>
        <td class="header">
            <div style="background-image:url(<?php echo $CLIENT_ROOT; ?>/images/layout/background.jpg);background-repeat:repeat-x;background-position:top;width:100%;clear:both;height:150px;border-bottom:1px solid #333333;">
                <div style="float:left;">
                    <img style="" src="<?php echo $CLIENT_ROOT; ?>/images/layout/BioMNA.jpg" />
                </div>
            </div>
            <div id="top_navbar">
                <div id="right_navbarlinks">
                    <?php
                    if($USER_DISPLAY_NAME){
                        ?>
                        <span style="">
							Welcome <?php echo $USER_DISPLAY_NAME; ?>!
						</span>
                        <span style="margin-left:5px;">
							<a href="<?php echo $CLIENT_ROOT; ?>/profile/viewprofile.php">My Profile</a>
						</span>
                        <span style="margin-left:5px;">
							<a href="<?php echo $CLIENT_ROOT; ?>/profile/index.php?submit=logout">Logout</a>
						</span>
                        <?php
                    }
                    else{
                        ?>
                        <span style="">
							<a href="<?php echo $CLIENT_ROOT."/profile/index.php?refurl=".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>">
								Log In
							</a>
						</span>
                        <span style="margin-left:5px;">
							<a href="<?php echo $CLIENT_ROOT; ?>/profile/newprofile.php">
								New Account
							</a>
						</span>
                        <?php
                    }
                    ?>
                    <span style="margin-left:5px;margin-right:5px;">
						<a href='<?php echo $CLIENT_ROOT; ?>/sitemap.php'>Sitemap</a>
					</span>

                </div>
                <ul id="hor_dropdown">
                    <li>
                        <a href="<?php echo $CLIENT_ROOT; ?>/index.php" >Home</a>
                    </li>
                    <li>
                        <a>Search</a>
                        <ul>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/collections/index.php" >Search Collections</a>
							</li>
							<li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/spatial/index.php" target="_blank">Spatial Module</a>
							</li>
						</ul>
                    </li>
                    <li>
                        <a>Images</a>
                        <ul>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/imagelib/search.php" >Image Search</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/imagelib/index.php" >Browse Images</a>
                            </li>
                        </ul>
                    </li>
                    <?php
                    if($SYMB_UID){
                        if((array_key_exists('CollAdmin',$USER_RIGHTS) && in_array(8, $USER_RIGHTS['CollAdmin'])) || (array_key_exists('CollEditor',$USER_RIGHTS) && in_array(8, $USER_RIGHTS['CollEditor']))){
                            ?>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?collid=8&emode=1" >Collection Management</a>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </td>
    </tr>
    <tr>
        <td class="middlecenter">

