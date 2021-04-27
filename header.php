<table id="maintable" style="border-spacing: 0;">
    <tr>
        <td class="header">
            <div style="clear:both;border-bottom:1px solid #333333;">
                <div style="width:850px;margin-left:auto;margin-right:auto;">
                    <img style="" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/wiscflora_banner6.png" />
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
                        <a href="#" >Advanced Searches</a>
                        <ul>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/index.php" ><b>SEARCH</b> for Species &amp; Specimen Records</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/index.php" ><b>BROWSE</b> the Flora  &amp; Image Library</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" target="_blank">Map Search</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" >Checklists</a>
                        <ul>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=7" >County Floras</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=8" >Wildflowers by Color</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=9" >Wildflowers by Month</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=11" >Largest Plant Families</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=20" >BCW Botany Blitzes</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=25" >WIS/BCW Botany Forays</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" >Floristic Projects</a>
                        <ul>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=14" >Brule River State Forest</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=15" >Ridgeway Pine Relict SNA</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=23" >Amsterdam Sloughs State Wildlife Area</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=21" >Crex Meadows State Wildlife Area</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=24" >Fish Lake State Wildlife Area</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=19" >Navarino Cedar Swamp State Natural Area</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" >Resources</a>
                        <ul>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/resources/Keys_pdfs/KEYS_Asteraceae_of_Wisconsin.pdf" target="_blank">Asteraceae of Wisconsin</a>
                            </li>
                            <li>
                                <a href="https://herbarium.wisc.edu/research/publications/" target="_blank">Atlas of the Wisconsin Prairie and Savanna Flora</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/resources/Keys_pdfs/KEYS_Fern_Allies_of_Wisconsin.pdf" target="_blank">Fern Allies of Wisconsin</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/resources/Keys_pdfs/KEYS_Ferns_of_Wisconsin.pdf" target="_blank">Ferns of Wisconsin</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/resources/Keys_pdfs/KEYS_Gymnosperms_of_Wisconsin.pdf" target="_blank">Gymnosperms of Wisconsin</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" >For Further Information</a>
                        <ul>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collstats.php" >About the Consortium of Wisconsin Herbaria</a>
                            </li>
                            <li>
                                <a href="https://herbarium.wisc.edu" target="_blank">WI State Herbarium</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/links.php">Links</a>
                            </li>
                        </ul>
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
            </div>
        </td>
    </tr>
    <tr>
        <td class="middlecenter">

