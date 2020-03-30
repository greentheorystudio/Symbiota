<table id="maintable" style="border-spacing: 0;">
    <tr>
        <td class="header">
            <div style="width:100%;position:relative;height:200px;border-bottom:1px solid #333333;">
                <div style="background-color:#000000;width:100%;height:200px;">
                    <div style="float:left;">
                        <img style="border:0;height:200px;" src="<?php echo $CLIENT_ROOT; ?>/images/layout/Welcome_Image.jpg" />
                    </div>
                    <div style="float:right;height:150px;display:flex;align-items:center;">
                        <img style="height:68px;margin-right:30px;" src="<?php echo $CLIENT_ROOT; ?>/images/layout/headerMastHead_sms.jpg" />
                    </div>
                </div>
                <div style="background-color:#FFFFFF;width:100%;height:50px;z-index:9;opacity:0.7;position:absolute;bottom:0;left:0;"></div>
                <div style="width:100%;height:50px;z-index:10;position:absolute;bottom:0;left:0;">
                    <div style="width:100%;color:black;opacity:1;display:flex;justify-content:space-evenly;align-items:center;">
                        <img style="border:0;height:45px;" src="<?php echo $CLIENT_ROOT; ?>/images/layout/Smithsonian-Logo.png" />
                        in partnership with
                        <img style="border:0;height:45px;padding:2px;background-color:white;" src="<?php echo $CLIENT_ROOT; ?>/images/layout/HorzCoBrandLogo.jpg" />
                    </div>
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
                        <a href="#" >Search</a>
                        <ul>
							<li>
								<a href="#" >Search by Word or Phrase</a>
							</li>
							<li>
								<a href="#" >Go to Species Reports</a>
							</li>
							<li>
								<a href="#" >Alphabetized Species Lists</a>
							</li>
							<li>
								<a href="#" >Expanded Species Reports</a>
							</li>
                            <li>
                                <a href="#" >Special Status Species</a>
                            </li>
                            <li>
                                <a href="#" >Non-Native Species</a>
                            </li>
                            <li>
                                <a href="#" >Species Names and Taxonomy</a>
                            </li>
                            <li>
                                <a href="#" >Marine Invertebrates</a>
                            </li>
                            <li>
                                <a href="#" >What's New</a>
                            </li>
						</ul>
                    </li>
                    <li>
                        <a href="#" >The IRL</a>
                        <ul>
                            <li>
                                <a href="#" >The IRL</a>
                            </li>
                            <li>
                                <a href="#" >What is a Lagoon</a>
                            </li>
                            <li>
                                <a href="#" >What is Biodiversity</a>
                            </li>
                            <li>
                                <a href="#" >Documented IRL Biodiversity</a>
                            </li>
                            <li>
                                <a href="#" >IRL Animal Biodiversity</a>
                            </li>
                            <li>
                                <a href="#" >IRL Plant Biodiversity</a>
                            </li>
                            <li>
                                <a href="#" >IRL Protist Biodiversity</a>
                            </li>
                            <li>
                                <a href="#" >IRL Habitats</a>
                            </li>
                            <li>
                                <a href="#" >Threats to IRL Habitats</a>
                            </li>
                            <li>
                                <a href="#" >Climate Change and the IRL</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">Stewardship</a>
                    </li>
                    <li>
                        <a href="#" >Photo Gallery</a>
                        <ul>
                            <li>
                                <a href="#" >Photo Gallery</a>
                            </li>
                            <li>
                                <a href="#" >Species Image Collection</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">Links and Events</a>
                    </li>
                    <li>
                        <a href="#" >About Us</a>
                        <ul>
                            <li>
                                <a href="#" >Project Background</a>
                            </li>
                            <li>
                                <a href="#" >Acknowledgements</a>
                            </li>
                            <li>
                                <a href="#" >Contact</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="https://naturalhistory.si.edu/research/smithsonian-marine-station">SMS Home</a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
    <tr>
        <td class="middlecenter">

