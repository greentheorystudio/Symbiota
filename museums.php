<?php
include_once(__DIR__ . '/config/symbini.php');
header('Content-Type: text/html; charset=' .$CHARSET);
?>

<!-- Template begins -->
<?php include($SERVER_ROOT.'/template-begins.php'); ?>

<!-- Page-Specific Styles -->

<!-- Page-Specific JavaScript -->

<!-- Google Analytics -->
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
<title><?php echo $DEFAULT_TITLE; ?> Museums &amp; Collections</title>
<link href="css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
<link href="css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	<?php include_once(__DIR__ . '/config/googleanalytics.php'); ?>
</script>
</head>
<body>
	<!-- Header -->
	<?php include(__DIR__ . '/header.php'); ?>

		<!-- Main Content -->
		<main id="innertext">

			
			<!-- Include existing gallery with image, brief description and links to museum and collections -->
			<div class="uw-text-center">
				<h1 class="uw-mini-bar uw-mini-bar-center"><?php echo $DEFAULT_TITLE; ?></h1>
				<p>The UW-Madison Natural History Museums Council represents two differente collectes (College of Letters & Science, College of Agricultural and Life Sciences) in three different divisions (Biological, Physical, Social Sciences), and among five different departments.</p>				
			</div>

			<div class="uw-pe uw-pe-alternating_content_boxes">
				<div class="alternating-content">
					<div class="alternating-content-box">
						<div class="column">
							<h3>ANTHROPOLOGY</h3>
							<p>The Department of Anthropology curates more than 1,000,000 archaeological artifacts, biological anthropological specimens, ethnographic objects, and related archives such as photographs, slides, paper documents, maps, and film.</p>
							<h4>Official website</h4>
							<a href="https://www.anthropology.wisc.edu/collections/">https://www.anthropology.wisc.edu/collections/</a>
						</div>
					</div>
					<div class="alternating-content-box">
					<a href="https://www.anthropology.wisc.edu/collections/"><img src="<?php echo $CLIENT_ROOT.'/images/Bio_Anthro_Lab15_0535.jpg' ?>" alt="Anthropology Collection"></a>
					</div>
				</div>


				<div class="alternating-content">
					<div class="alternating-content-box">
						<div class="column">
							<h3>BOTANY</h3>
							<p>The Wisconsin State Herbarium (officially designated as such in 1995 by Gov. T. Thompson and the WI Legislature) holds &gt;1.35 million specimens of plants, fungi, lichens, and algae.</p>
							<h4>Official website</h4>
							<a href="https://herbarium.wisc.edu/">https://herbarium.wisc.edu/</a>
						</div>
					</div>
					<div class="alternating-content-box">
						<a href="https://herbarium.wisc.edu/"><img src="<?php echo $CLIENT_ROOT.'/images/Cameron_Ken14_8950.jpg' ?>" alt="Botany Collection"></a>
					</div>
				</div>


				<div class="alternating-content">
					<div class="alternating-content-box">
						<div class="column">
							<h3>ENTOMOLOGY</h3>
							<p>The Wisconsin Insect Research Collection (WIRC) housed in CALS’ Russell Labs, contains ca. 3 million curated specimens with an additional 3-5 million specimens in the backlog.  In recent years 20,000-50,000 specimens have been added each year.</p>
							<h4>Official website</h4>
							<a href="http://labs.russell.wisc.edu/wirc/">http://labs.russell.wisc.edu/wirc/</a>
						</div>
					</div>
					<div class="alternating-content-box">
						<a href="http://labs.russell.wisc.edu/wirc/"><img src="<?php echo $CLIENT_ROOT.'/images/Science_Expeditions11_3841.jpg' ?>" alt="Anthropology Collection"></a>
					</div>
				</div>


				<div class="alternating-content">
					<div class="alternating-content-box">
						<div class="column">
							<h3>GEOSCIENCE</h3>
							<p>The UW Geology Museum (UWGM) is the most popular science outreach venue on campus. Each year it attracts &gt; 50,000 visitors including, and contains about 120,000 geological and paleontological specimens.</p>
							<h4>Official website</h4>
							<a href="http://geoscience.wisc.edu/museum/">http://geoscience.wisc.edu/museum/</a>
						</div>
					</div>
					<div class="alternating-content-box">
						<a href="http://geoscience.wisc.edu/museum/"><img src="<?php echo $CLIENT_ROOT.'/images/Geology_Museum15_3794.jpg' ?>" alt="Anthropology Collection"></a>
					</div>
				</div>


				<div class="alternating-content">
					<div class="alternating-content-box">
						<div class="column">
							<h3>ZOOLOGY</h3>
							<p>The UW Zoological Museum is dedicated to the preservation, study, and understanding of the vertebrate and aquatic fauna of Wisconsin, the Midwest, and world. The collections consist of nearly 750,000 specimens including vertebrates and invertebrates.</p>
							<h4>Official website</h4>
							<a href="https://uwzm.integrativebiology.wisc.edu/">https://uwzm.integrativebiology.wisc.edu/</a>
						</div>
					</div>
					<div class="alternating-content-box">
						<img src="<?php echo $CLIENT_ROOT.'/images/Zoological_Museum09_8758.jpg' ?>" alt="Zoological Museum Herpetology Collection">
					</div>
				</div>

			</div>


			<!-- Add description and links to collection from php (Symbiota about museum/collection pages -> collection/index.php?) -->

		</main>
		<!-- Footer -->
		<?php
		include(__DIR__ . '/footer.php');
		?>
	</body>
</html>
