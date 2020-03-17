<?php
include_once(__DIR__ . '/config/symbini.php');
include_once(__DIR__ . '/classes/SiteMapManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$submitAction = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:''; 

$smManager = new SiteMapManager();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
    <head>
    <title><?php echo $DEFAULT_TITLE; ?> Site Tools</title>
    <link href="css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
    <link href="css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
    <script type="text/javascript">
    function submitTaxaNoImgForm(f){
        if(f.clid.value !== ""){
            f.submit();
        }
        return false;
    }
	</script>
	<script type="text/javascript" src="js/symb/shared.js"></script>
	</head>
	<body>
		<?php
		include(__DIR__ . '/header.php');
		?>
		<main id="innertext">
			<h1>Site Tools</h1>
			<?php 
				if($IS_ADMIN){
					?>
				<div>
					<p>IS ADMIN</p>

					<h2>Users</h2>
					<ul>
						<li>
							<a href="profile/usermanagement.php">User Permissions</a>
						</li>
						<li>
							<a href="profile/usertaxonomymanager.php">Taxonomic Interest User Permissions</a>
						</li>
					</ul>				

					<h2>Collections</h2>
						<ul>
							<li><a href="<?php echo $CLIENT_ROOT; ?>/collections/misc/collmetadata.php">Create a New Collection or Observation Profile</a></li>
							<li><a href="<?php echo $CLIENT_ROOT; ?>/collections/admin/guidmapper.php">Collection GUID Mapper</a></li>
							<li><a href="collections/misc/collstats.php">Collection Statistics</a></li>
							<li><a href="collections/misc/rarespecies.php">List of Rare Species</a></li>
						</ul>
						<h3>List of Collections you have permissions to edit:</h3>
						<p>Click collection for specific tools.</p>
						<ul>
							<?php 
							$smManager->setCollectionList();
							if($collList = $smManager->getCollArr()){
								foreach($collList as $k => $cArr){
									echo '<li>';
									echo '<a href="'.$CLIENT_ROOT.'/collections/misc/collprofiles.php?collid='.$k.'&emode=1">';
									echo $cArr['name'];
									echo '</a>';
									echo '</li>';
								}
							}
							else{
                                echo '<li>You have no explicit editing permissions for a particular collections</li>';
							}
							?>
						</ul>

					<h2>Taxonomy</h2>
					<ul>
						<?php 
						if($IS_ADMIN || array_key_exists('Taxonomy',$USER_RIGHTS)){
							?>
							<li><a href="taxa/admin/taxonomydisplay.php">Taxonomic Tree Viewer</a></li>
							<li><a href="taxa/admin/taxonomydynamicdisplay.php">Taxonomy Explorer</a></li>
							<li><a href="collections/cleaning/taxonomycleaner.php">Global Taxonomic Name Cleaner</a></li>
							<li><a href="taxa/admin/taxonomydisplay.php">Edit Taxonomic Placement</a></li>
							<li><a href="taxa/admin/taxonomyloader.php">Add New Taxonomic Name</a></li>
							<li><a href="taxa/admin/batchloader.php">Batch Upload a Taxonomic Data File</a></li>
							<li><a href="taxa/admin/eolmapper.php">Encyclopedia of Life Linkage Manager</a></li>
							<?php 
							}
						else{
                            echo '<li>You are not authorized to edit taxonomy</li>';
						}
						?>
					</ul>				

					<h2>Image Library</h2>
					<ul>
						<li><a href="imagelib/index.php">Image Library</a></li>
						<li><a href="imagelib/search.php">Interactive Search Tool</a></li>
						<li><a href="imagelib/contributors.php">Image Contributors</a></li>
						<li><a href="misc/usagepolicy.php">Usage Policy and Copyright Information</a></li>
						<li><a href="<?php echo $CLIENT_ROOT; ?>/imagelib/admin/thumbnailbuilder.php">Image Thumbnail Builder</a></li>
					</ul>

					<h2>Checklists (curated lists of specimens)</h2>
					<?php
						$clList = $smManager->getChecklistList((array_key_exists('ClAdmin',$USER_RIGHTS)?$USER_RIGHTS['ClAdmin']:0));
						$clAdmin = array();
						if($clList && isset($USER_RIGHTS['ClAdmin'])){
							$clAdmin = array_intersect_key($clList,array_flip($USER_RIGHTS['ClAdmin']));
						}
						$projList = $smManager->getProjectList();
						if($projList){
							echo '<div style="margin-top:10px;"><h2>Biotic Inventory Projects</h2></div><ul>';
							foreach($projList as $pid => $pArr){
								echo "<li><a href='projects/index.php?pid=".$pid."'>".$pArr['name']."</a></li>\n";
								echo '<ul><li>Manager: ' .$pArr['managers']."</li></ul>\n";
							}
							echo '</ul>';
						}
					?>				
					<div style="margin:10px;">
                        Tools for managing Checklists are available from each checklist display page.
                        Editing symbols located in the upper right of the page will display
                        editing options for that checklist.
                        Below is a list of the checklists you are authorized to edit.
					</div>
					<ul>
						<?php
						if($clAdmin){
							foreach($clAdmin as $k => $v){
								echo "<li><a href='".$CLIENT_ROOT. '/checklists/checklist.php?clid=' .$k."&emode=1'>$v</a></li>";
							}
						}
						else{
                            echo '<li>You are not authorized to edit any of the Checklists</li>';
						}
						?>
					</ul>

					<h2>Data Publishing</h2>
					<li style="margin-left:15px"><a href="collections/datasets/rsshandler.php" target="_blank">RSS Feed for Natural History Collections and Observation Projects</a></li>
					<li style="margin-left:15px"><a href="collections/datasets/datapublisher.php">Darwin Core Archives (DwC-A)</a> - published datasets of selected collections</li>
					<?php
						if(file_exists('webservices/dwc/rss.xml')){
							echo '<li style="margin-left:15px;"><a href="webservices/dwc/rss.xml" target="_blank">DwC-A RSS Feed</a></li>';
						}
					?>
				</div>

				<?php
				}	
				else {
					?>
					<div>
						<p>You do not have permission to access this page.</p>
						<p>Please <a href="<?php echo $CLIENT_ROOT. '/profile/index.php?refurl=' .$_SERVER['PHP_SELF']. '?' .$_SERVER['QUERY_STRING']; ?>">log in</a> to see the Site Tools.</p>
					</div>
				<?php
				} 
				?>
					
		</main>
		<?php
		include(__DIR__ . '/footer.php');
		?> 
	</body>
</html>
