<table id="maintable" style="border-spacing: 0;">
    <tr>
        <td class="header">
            <div style="clear:both;background-color:#D9E636;height:175px;border-bottom:1px solid black;">
                <div style="float:left;font-family: Chalkboard,Comic Sans MS,Comic Sans,cursive;color:black;font-size:40px;">
                    <div style="height:175px;width:400px;display:flex;flex-direction:column;justify-content:center;align-items:center;">
                        <div>Lomatium & Friends</div>
                        <div>Online Monographs</div>
                    </div>
                </div>
                <div style="float:right;">
                    <img style="height:175px;" src="<?php echo $CLIENT_ROOT; ?>/images/layout/Banner3.JPG" />
                </div>
            </div>
            <div id="top_navbar">
                <div id="right_navbarlinks">
                    <?php
                    if($USER_DISPLAY_NAME){
                        ?>
                        <span>
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
							<a href="<?php echo $CLIENT_ROOT. '/profile/index.php?refurl=' .$_SERVER['PHP_SELF']. '?' .$_SERVER['QUERY_STRING']; ?>">
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
                        <a href="" >The Project</a>
                    </li>
                    <li>
                        <a href="" >Trees</a>
                    </li>
                    <li>
                        <a href="" >Genera</a>
                    </li>
                    <li>
                        <a href="" >Species</a>
                    </li>
                    <li>
                        <a href="" >Specimens</a>
                    </li>
                    <li>
                        <a href="" >Images</a>
                    </li>
                    <li>
                        <a href="" >Resources</a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
    <tr>
        <td class="middlecenter">

