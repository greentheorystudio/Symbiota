<?php
//error_reporting(E_ALL);
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$charset);
?>
<html>
<head>
    <title><?php echo $defaultTitle; ?> Links</title>
    <link href="../css/base.css?<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" href="../css/main.css?<?php echo $CSS_VERSION; ?>" type="text/css" />
</head>
<body>
	<?php
	$displayLeftMenu = (isset($misc_linksMenu)?$misc_linksMenu:"true");
	include($serverRoot.'/header.php');
	if(isset($misc_linksCrumbs)){
		echo "<div class='navpath'>";
		echo "<a href='../index.php'>Home</a> &gt; ";
		echo $misc_linksCrumbs;
		echo " <b>Links</b>"; 
		echo "</div>";
	}
	?> 
        <!-- This is inner text! --> 
        <div id="innertext">
            <h1>Links</h1>
        	<div style="margin-left:10px;">
				<div>
					<div style="font-weight:bold;">General Resources</div>
					<ul>
						<li><a href='http://www.gbif.org/' target='_blank'>Global Biodiversity Information Facility (GBIF)</a></li>
						<li><a href='https://www.idigbio.org/' target='_blank'>Integrated Digitized Collections (iDigBio)</a></li>
						<li><a href='http://symbiota.org/docs/' target='_blank'>Symbiota Virtual Flora Software Project</a></li>
					</ul>
				</div>
				<div>
					<div style="font-weight:bold;">Regional Floristic Resources</div>
					<ul>
						<li><a href='http://www.uwgb.edu/biodiversity/herbarium/pteridophytes/pteridophytes_of_wisconsin01.htm' target='_blank'>Ferns of Wisconsin (UW-Green Bay)</a></li>
						<li><a href='http://www.uwgb.edu/biodiversity/herbarium/invasive_species/invasive_plants01.htm' target='_blank'>Invasive Plants of Wisconsin (UW-Green Bay)</a></li>
						<li><a href='https://itunes.apple.com/tw/app/id462075116?mt=875116' target='_blank'>Key to Woody Plants of Wisconsin Forests: <i>a Free App for the iPhone</i></a></li>
						<li><a href='http://michiganflora.net/home.aspx' target='_blank'>Michigan Flora</a></li>
						<li><a href='https://www.minnesotawildflowers.info/' target='_blank'>Minnesota Wildflowers</a></li>
						<li><a href='http://www.botany.wisc.edu/orchids/Keys.html' target='_blank'>Orchids of Wisconsin</a></li>
						<li><a href='http://www.uwgb.edu/biodiversity/herbarium/shrubs/Shrub_intro01.htm' target='_blank'>Shrubs of Wisconsin (UW-Green Bay)</a></li>
						<li><a href='http://botany.wisc.edu/jsulman/Sparganium%20identification%20key%20and%20description.htm' target='_blank'>Sparganium of Wisconsin</a></li>
						<li><a href='http://www.uwgb.edu/biodiversity/herbarium/trees/tree_intro01.htm' target='_blank'>Trees of Wisconsin (UW-Green Bay)</a></li>
						<li><a href='http://www.uwgb.edu/biodiversity/herbarium/wetland_plants/wetland_plants01.htm' target='_blank'>Wetland Plants of Wisconsin (UW-Green Bay)</a></li>
						<li><a href='http://dnr.wi.gov/topic/endangeredresources/plants.asp' target='_blank'>Wisconsin's Rare Plants - Wisconsin DNR</a></li>
						<li><a href='http://dnr.wi.gov/topic/EndangeredResources/Lichens.asp' target='_blank'>Wisconsin's Rare Lichens - Wisconsin DNR</a></li>
					</ul>
				</div>
				<div>
					<div style="font-weight:bold;">Other Regional Symbiota Nodes</div>
					<ul>
						<li><a href="http://midwestherbaria.org/portal/" target='_blank'>Consortium of Midwest Herbaria</a></li>
						<li><a href="http://ngpherbaria.org/portal/" target='_blank'>Consortium of North Great Plains Herbaria</a></li>
						<li><a href="http://greatlakesinvasives.org/portal/index.php" target='_blank'>Great Lakes Invasives Network</a></li>
					</ul>
				</div>
				<div>
					<div style="font-weight:bold;">Other Symbiota Nodes</div>
					<ul>
						<li><a href='http://bryophyteportal.org/portal/' target='_blank'>Consortium of North American Bryophyte Herbaria</a></li>
						<li><a href='http://lichenportal.org/portal/index.php' target='_blank'>Consortium of North American Lichen Herbaria</a></li>
						<li><a href='http://mycoportal.org/portal/index.php' target='_blank'>Mycology Collections Data Portal</a></li>
					</ul>
				</div>
			</div>
		</div>

	<?php
	include($serverRoot.'/footer.php');
	?> 

</body>
</html>
