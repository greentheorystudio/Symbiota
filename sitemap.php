<?php
include_once(__DIR__ . '/config/symbbase.php');
include_once(__DIR__ . '/classes/SiteMapManager.php');
header('Content-Type: text/html; charset=UTF-8' );
$submitAction = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$smManager = new SiteMapManager();
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Site Map</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
</head>
<body>
	<?php
	include(__DIR__ . '/header.php');
	?>
	<div id="innertext">
		<h2>Site Map</h2>
		<div class="pmargin">
			<h3>Collections</h3>
			<ul>
				<li><a href="collections/list.php">Search Collections</a></li>
                <li><a href="spatial/index.php">Spatial Module</a></li>
				<li><a href="collections/misc/collprofiles.php">Collections</a></li>
				<li><a href="collections/misc/collstats.php">Collection Statistics</a></li>
				<?php
                if(isset($GLOBALS['ACTIVATE_EXSICCATI']) && $GLOBALS['ACTIVATE_EXSICCATI']){
                    echo '<li><a href="collections/exsiccati/index.php">Exsiccati Index</a></li>';
                }
                ?>
				<li>Data Publishing</li>
                <li style="margin-left:15px"><a href="collections/datasets/rsshandler.php" target="_blank">Collection RSS Feed</a></li>
                <li style="margin-left:15px"><a href="collections/datasets/datapublisher.php">Darwin Core Archives (DwC-A)</a></li>
                <?php
                if(file_exists('webservices/dwc/rss.xml')){
                    echo '<li style="margin-left:15px"><a href="webservices/dwc/rss.xml" target="_blank">DwC-A RSS Feed</a></li>';
                }
                ?>
                <li><a href="taxa/protectedspecies.php">Protected Species</a></li>

			</ul>

			<h3>Image Library</h3>
			<ul>
				<li><a href="imagelib/index.php">Image Library</a></li>
				<li><a href="imagelib/search.php">Image Search</a></li>
				<li><a href="imagelib/contributors.php">Image Contributors</a></li>
				<li><a href="misc/usagepolicy.php">Usage Policy</a></li>
			</ul>

            <h3>Additional Resources</h3>
			<ul>
                <li><a href="projects/index.php">Biotic Inventory Projects</a></li>
                <li><a href="checklists/index.php">Checklists</a></li>
                <li><a href="checklists/dynamicmap.php?interface=checklist">Dynamic Checklist</a></li>
                <?php
                if(isset($GLOBALS['KEY_MOD_IS_ACTIVE']) && $GLOBALS['KEY_MOD_IS_ACTIVE']){
                    echo '<li><a href="checklists/dynamicmap.php?interface=key">Dynamic Key</a></li>';
                }
                ?>
                <li><a href="taxa/dynamictaxalist.php">Dynamic Taxonomy List</a></li>
                <?php
                if($smManager->hasGlossary()){
                    ?>
                    <li><a href="glossary/index.php">Glossary</a></li>
                    <?php
                }
                ?>
                <li><a href="taxa/taxonomydynamicdisplay.php">Taxonomy Explorer</a></li>
                <li><a href="taxa/dynamictreeviewer.php">Interactive Taxonomic Tree</a></li>
			</ul>

			<?php
            if($GLOBALS['SYMB_UID']){
                ?>
                <fieldset class="sitemapDataManagementContainer">
                    <legend><b>Management Tools</b></legend>
                    <?php
                    if($GLOBALS['IS_ADMIN']){
                        ?>
                        <h3>Administrative Tools</h3>
                        <ul>
                            <li>
                                <a href="admin/portal/index.php">Portal Configurations</a>
                            </li>
                            <li>
                                <a href="admin/mapping/index.php">Mapping Configurations</a>
                            </li>
                            <li>
                                <a href="profile/usermanagement.php">User Management</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collmetadata.php">
                                    Create New Collection
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/management/thumbnailbuilder.php">
                                    Build Image Thumbnails
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/management/guidmapper.php">
                                    Generate GUIDs/UUIDs
                                </a>
                            </li>
                        </ul>
                        <?php
                    }

                    if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS']) || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
                        ?>
                        <h3>Taxonomy</h3>
                        <ul>
                            <?php
                            if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
                                ?>
                                <li><a href="taxa/thesaurus/index.php">Taxonomic Thesaurus Manager</a></li>
                                <li><a href="taxa/thesaurus/identifiermanager.php">Taxonomic Identifier Manager</a></li>
                                <li><a href="taxa/taxonomy/index.php">Taxonomy Editor</a></li>
                                <?php
                            }
                            if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
                                ?>
                                <li><a href="taxa/profile/tpeditor.php">Taxon Profile Manager</a></li>
                                <li><a href="taxa/media/batchimageloader.php">Taxa Media Batch Uploader</a></li>
                                <li><a href="taxa/media/eolimporter.php">Encyclopedia of Life Media Importer</a></li>
                                <?php
                            }
                            ?>
                        </ul>
                        <?php
                    }

                    if((isset($GLOBALS['KEY_MOD_IS_ACTIVE']) && $GLOBALS['KEY_MOD_IS_ACTIVE']) || array_key_exists('KeyAdmin',$GLOBALS['USER_RIGHTS'])){
                        ?>
                        <h3>Identification Keys</h3>
                        <ul>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/ident/admin/index.php">Characters and Character States Editor</a>
                            </li>
                        </ul>
                        <?php
                    }
                    ?>

                    <h3>Glossary</h3>
                    <ul>
                        <li><a href="glossary/index.php">Manage Glossary</a></li>
                    </ul>

                    <h3>References</h3>
                    <ul>
                        <li><a href="references/index.php">Manage References</a></li>
                    </ul>

                    <h3>Datasets</h3>
                    <ul>
                        <li><a href="collections/datasets/index.php">Manage Datasets</a></li>
                    </ul>

                    <?php
                    if(isset($GLOBALS['ACTIVATE_EXSICCATI']) && $GLOBALS['ACTIVATE_EXSICCATI']){
                        ?>
                        <h3>Exsiccati</h3>
                        <ul>
                            <li><a href="collections/exsiccati/index.php">Exsiccati Index</a></li>
                        </ul>
                        <?php
                    }
                    ?>
                </fieldset>
                <?php
            }
            ?>
        </div>
	</div>
	<?php
    include_once(__DIR__ . '/config/footer-includes.php');
    include(__DIR__ . '/footer.php');
	?>
</body>
</html>
