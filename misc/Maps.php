<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
	<head>
		<title>The Indian River Lagoon Estuary</title>
		<link href="<?php echo $CLIENT_ROOT; ?>/css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $CLIENT_ROOT; ?>/css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $CLIENT_ROOT; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
	</head>
	<body>
		<?php
        include(__DIR__ . '/../header.php');
		?>
		<div id="innertext">
            <h2>The Indian River Lagoon Estuary</h2>
            <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
                <tr>
                    <td><table width="280" class="image_left">
                            <tr>
                                <td><img border="0" src="../content/imglib/IRLZonesMap.gif" hspace="2" vspace="2" width="271" height="140"></td>
                            </tr>
                        </table><p class="body">The
                            Indian River Lagoon (IRL) is part of the&nbsp;
                            longest barrier island complex in the
                            United States, occupying more than 30% of Florida's east coast.&nbsp;
                            The extent of the IRL system spans approximately 156 miles
                            from Ponce de Leon Inlet
                            in the Mosquito Lagoon to Jupiter Inlet near West Palm
                            Beach.</p>
                        <p class="body"><span class="medium">The IRL is a complex association of terrestrial, wetland and
estuarine ecosystems which combine to create a complex ecosystem mosaic with
high habitat diversity. But the feature which helps
      distinguish the IRL
system from other estuarine systems, and also accounts for  much of the high
biological diversity in the Indian River Lagoon, is its unique geographical
location, which straddles the transition zone between colder temperate, and
warmer sub-tropical biological provinces. Here, as perhaps no where else
in the continental United States, tropical and temperate species coexist and
      thrive.</p></td>
                </tr>
                <tr>
                    <td width="500">
                        <table class="image_left">
                            <tr>
                                <td><img src="../content/imglib/IRLMap.gif" align="right" style="margin: 5px" width="267" height="539"></td>
                            </tr>
                        </table>
                        <p class="body">The Indian River Lagoon System actually consists of 3 lagoons: the
                            Mosquito Lagoon which originates in Volusia County, the Banana River in
                            Brevard County, and the Indian River Lagoon which spans nearly the entire
                            coastal extent of Brevard, Indian River, St. Lucie and Martin Counties.
                        </p>
                        <p class="body">The 5 county area bordering
                            the IRL receives tremendous economic benefit from its presence.&nbsp;
                            Commercial and recreational activities around the IRL support
                            approximately 19,000 jobs and generate over $250 million dollars in annual
                            income. Citrus
                            agriculture in the vicinity of the IRL accounts for over 2 billion dollars
                            per year, while recreational
                            activities such as boating, fishing, water sports, hunting and ecotourism
                            generate approximately $465 million dollars annually.
                            Commercial fishing enterprises in the IRL and along the Florida coast
                            generate approximately $140 million dollars
                            in revenues, and account for nearly 15% of the national fish and shellfish
                            harvest. And real estate
                            leasing and sales along the lagoon account for over $825 million dollars
                            in annual revenue.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="footer_note">
                            Submit additional information, photos or comments to: <br />
                            <a href="mailto:irl_webmaster@si.edu">irl_webmaster@si.edu </a>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
		<?php
        include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
