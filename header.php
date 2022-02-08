<?php
include_once(__DIR__ . '/classes/Sanitizer.php');
?>
<table id="maintable" style="border-spacing:0;">
	<tr>
		<td class="header" colspan="3">
			<div style="clear:both;background-color:#000000;height:150px;">
				<div style="float:left;">
					<img style="" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/pla_logo.png" />
				</div>
				<div style="float:right;margin-right:8px;">
					<div style="float:left;margin: 23px 8px 23px 8px;border: 2px solid white;">
						<img style="width:100px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/IMG_6681.JPG" />
					</div>
					<div style="float:left;margin: 23px 8px 23px 8px;border: 2px solid white;">
						<img style="width:100px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/IMG_6580.JPG" />
					</div>
					<div style="float:left;margin: 23px 8px 23px 8px;border: 2px solid white;">
						<img style="width:100px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/IMG_6672.jpg" />
					</div>
					<div style="float:left;margin: 23px 8px 23px 8px;border: 2px solid white;">
						<img style="width:100px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/IMG_6826.JPG" />
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
							<a href="<?php echo $GLOBALS['CLIENT_ROOT']. '/profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true); ?>">
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
						<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/index.php" >Search</a>
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
						<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/index.php" >Browse Images</a>
					</li>
					<li>
						<a href="#" >Agency Floras</a>
						<ul>
							<li>
								<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/blmstates.php" >BLM</a>
							</li>
							<li>
								<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/fwsregions.php" >FWS</a>
							</li>
							<li>
								<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/npsregions.php" >NPS</a>
							</li>
							<li>
								<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/usfsregions.php" >USFS</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php" >Flora Projects</a>
					</li>
					<li>
						<a href="#" >Dynamic Floras</a>
						<ul>
							<li>
								<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=checklist" >Dynamic Checklist</a>
							</li>
							<li>
								<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=key" >Dynamic Key</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#" >Other Networks</a>
						<ul>
							<li>
								<a href="#" >Animals ></a>
								<ul>
									<li>
										<a href="http://symbiota4.acis.ufl.edu/scan/portal/index.php" target="_blank" >SCAN</a>
									</li>
									<li>
										<a href="http://invertebase.org/portal/index.php/" target="_blank" >InvertEBase</a>
									</li>
									<li>
										<a href="http://symbiota.org/neotrop/entomology/index.php" target="_blank" >Neotropical Entomology</a>
									</li>
									<li>
										<a href="http://madrean.org/symbfauna/projects/index.php" target="_blank" >Madrean Archipelago Biodiversity Assessment (MABA) - Fauna</a>
									</li>
								</ul>
							</li>
							<li>
								<a href="#" >Fungi & Lichens ></a>
								<ul>
									<li>
										<a href="http://mycoportal.org/portal/index.php" target="_blank" >MyCoPortal</a>
									</li>
									<li>
										<a href="http://lichenportal.org/portal/index.php" target="_blank" >Consortium of North American Lichen Herbaria</a>
									</li>
									<li>
										<a href="http://lichenportal.org/arctic/index.php" target="_blank" >Arctic Lichen Flora</a>
									</li>
								</ul>
							</li>
							<li>
								<a href="#" >Plants & Algae ></a>
								<ul>
									<li>
										<a href="http://swbiodiversity.org/seinet/index.php" target="_blank" >SEINet</a>
									</li>
									<li>
										<a href="http://sernecportal.org/portal/" target="_blank" >SouthEast Regional Network of Expertise and Collections (SERNEC)</a>
									</li>
									<li>
										<a href="http://midwestherbaria.org/portal/index.php" target="_blank" >Consortium of Midwest Herbaria</a>
									</li>
									<li>
										<a href="http://intermountainbiota.org/portal/index.php" target="_blank" >Intermountain Region Herbaria Network (IRHN)</a>
									</li>
									<li>
										<a href="http://nansh.org/portal/index.php" target="_blank" >North American Network of Small Herbaria</a>
									</li>
									<li>
										<a href="http://ngpherbaria.org/portal/index.php" target="_blank" >Northern Great Plains Herbaria</a>
									</li>
									<li>
										<a href="http://portal.neherbaria.org/portal/" target="_blank" >Consortium of Northeastern Herbaria (CNH)</a>
									</li>
									<li>
										<a href="http://swbiodiversity.unm.edu/" target="_blank" >New Mexico Biodiversity Portal</a>
									</li>
									<li>
										<a href="http://madrean.org/symbflora/projects/index.php?proj=74" target="_blank" >Madrean Archipelago Biodiversity Assessment (MABA) - Flora</a>
									</li>
									<li>
										<a href="http://herbariovaa.org/" target="_blank" >Herbario Virtual Austral Americano</a>
									</li>
									<li>
										<a href="http://cotram.org/" target="_blank" >CoTRAM – Cooperative Taxonomic Resource for Amer. Myrtaceae</a>
									</li>
									<li>
										<a href="http://symbiota.org/neotrop/plantae/index.php" target="_blank" >Neotropical Flora</a>
									</li>
									<li>
										<a href="http://www.pacificherbaria.org/" target="_blank" >Consortium of Pacific Herbaria</a>
									</li>
									<li>
										<a href="http://bryophyteportal.org/portal/" target="_blank" >Consortium of North American Bryophyte Herbaria</a>
									</li>
									<li>
										<a href="http://bryophyteportal.org/frullania/" target="_blank" >Frullania Collaborative Research Network</a>
									</li>
									<li>
										<a href="http://macroalgae.org/portal/index.php" target="_blank" >Macroalgal Consortium Herbarium Portal</a>
									</li>
								</ul>
							</li>
							<li>
								<a href="#" >Multi-Phyla ></a>
								<ul>
									<li>
										<a href="http://stricollections.org/portal/" target="_blank" >Smithsonian Tropical Research Institute Portal (STRI)</a>
									</li>
									<li>
										<a href="http://greatlakesinvasives.org/portal/index.php" target="_blank" >Aquatic Invasives</a>
									</li>
								</ul>
							</li>
							<li>
								<a href="http://collections.ala.org.au/" target="_blank" >Atlas of Living Australia</a>
							</li>
							<li>
								<a href="http://www.gbif.org/" target="_blank" >GBIF</a>
							</li>
							<li>
								<a href="https://www.idigbio.org/" target="_blank" >iDigBio</a>
							</li>
							<li>
								<a href="http://splink.cria.org.br/" target="_blank" >Species Link</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</td>
	</tr>
    <tr>
		<td class='middlecenter'  colspan="3">
