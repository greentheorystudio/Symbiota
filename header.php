<table id="maintable" style="border-spacing: 0;">
    <tr>
        <td class="header">
            <div style="background-image:url(<?php echo $CLIENT_ROOT; ?>/images/layout/banner-tile-new.png);background-repeat:repeat-x;background-position:top;width:100%;clear:both;height:200px;border-bottom:1px solid #333333;">
                <div style="float:left;">
                    <img style="border:0px;" src="<?php echo $CLIENT_ROOT; ?>/images/layout/banner-left-new.png" alt="Banner image with logo" />
                </div>
                <div style="float:right;">
                    <img style="" src="<?php echo $CLIENT_ROOT; ?>/images/layout/banner-right-new.png" alt="Banner Egret image" />
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
                        <a href="<?php echo $CLIENT_ROOT; ?>/taxa/dynamictaxalist.php">Search</a>
                        <ul>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/taxa/dynamictaxalist.php" >Dynamic Species List</a>
							</li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/ListedSpec.php" >Special Status Species</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/Nonnatives.php" >Non-Native Species</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/NamSpecies.php" >Species Names and Taxonomy</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?php echo $CLIENT_ROOT; ?>/misc/Maps.php" >The IRL</a>
                        <ul>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/Maps.php" >The IRL</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/Whatsa_lagoon.php" >What is a Lagoon</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/Whats_biodiv.php" >What is Biodiversity</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/Total_Biodiv.php" >Documented IRL Biodiversity</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/Animal_Biodiv.php" >IRL Animal Biodiversity</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/Plant_Biodiv.php" >IRL Plant Biodiversity</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/Protis_Biodiv.php" >IRL Protist Biodiversity</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/Whatsa_Habitat.php" >IRL Habitats</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/Habitat_Threats.php" >Threats to IRL Habitats</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/Climate_Change.php" >Climate Change and the IRL</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?php echo $CLIENT_ROOT; ?>/misc/Protect-IRL.php">Stewardship</a>
                    </li>
                    <li>
                        <a href="<?php echo $CLIENT_ROOT; ?>/misc/PhotoGallery_Intro.php" >Photo Gallery</a>
                        <ul>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/PhotoGallery_Intro.php" >Photo Gallery</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/Sp_Image_Collection.php" >Species Image Collection</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?php echo $CLIENT_ROOT; ?>/misc/IRL_Links.php">Links and Events</a>
                    </li>
                    <li>
                        <a href="<?php echo $CLIENT_ROOT; ?>/misc/Proj_Bkgnd.php" >About Us</a>
                        <ul>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/Proj_Bkgnd.php" >Project Background</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/acknowledge.php" >Acknowledgements</a>
                            </li>
                            <li>
                                <a href="<?php echo $CLIENT_ROOT; ?>/misc/contact.php" >Contact</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="https://naturalhistory.si.edu/research/smithsonian-marine-station">SMS Home</a>
                    </li>
                    <li>
                        <a href="https://onelagoon.org/">One Lagoon Home</a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
    <tr>
        <td class="middlecenter">

