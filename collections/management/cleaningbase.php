<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceCleaner.php');

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;

$cleanManager = new OccurrenceCleaner();
if($collid) {
    $cleanManager->setCollId($collid);
}
$collMap = $cleanManager->getCollMap();

$isEditor = 0;
if($GLOBALS['IS_ADMIN'] || ($collMap['colltype'] === 'General Observations') || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = 1;
}

if($collMap['colltype'] === 'General Observations'){
	$cleanManager->setObsUid($GLOBALS['SYMB_UID']);
}
?>
<style>
    table.styledtable {  width: 300px }
    table.styledtable td { white-space: nowrap; }
    h3 { text-decoration:underline }
</style>
<div id="innertext" style="background-color:white;">
    <?php
    if($isEditor){
        echo '<h2>'.$collMap['collectionname'].' ('.$collMap['code'].')</h2>';
        ?>
        <div style="color:red;margin:20px 0;font-weight:bold;">It is strongly recommended to download a backup of your
            collection data before using the Duplicate Merging, Geography Cleaning, or Taxonomic Name Resolution modules.
        </div>
        <?php
        if($collMap['colltype'] !== 'General Observations'){
            ?>
            <h3>Duplicate Records</h3>
            <div style="margin:0 0 40px 15px;">
                <ul>
                    <li>
                        <a href="duplicatesearch.php?collid=<?php echo $collid; ?>&action=listdupscatalog">
                            Catalog Numbers
                        </a>
                    </li>
                    <li>
                        <a href="duplicatesearch.php?collid=<?php echo $collid; ?>&action=listdupsothercatalog">
                            Other Catalog Numbers
                        </a>
                    </li>
                </ul>
            </div>
            <?php
        }
        ?>

        <h3>Political Geography</h3>
        <div style="margin:0 0 40px 15px;">
            <ul>
                <li>
                    <a href="politicalunits.php?collid=<?php echo $collid; ?>">Open Geography Cleaning Module</a>
                </li>
            </ul>
        </div>

        <h3>Occurrence Coordinates</h3>
        <div style="margin:0 0 40px 15px;">
            <fieldset style="margin:10px 0;padding:5px;">
                <legend style="font-weight:bold">Statistics and Action Panel</legend>
                <ul>
                    <?php
                    $statsArr = $cleanManager->getCoordStats();
                    ?>
                    <li>Georeferenced: <?php echo $statsArr['coord']; ?>
                        <?php
                        if($statsArr['coord']){
                            ?>
                            <a href="../editor/occurrencetabledisplay.php?collid=<?php echo $collid; ?>&occindex=0&q_catalognumber=&q_customfield1=decimallatitude&q_customtype1=NOTNULL" style="margin-left:5px;" title="Open Editor" target="_blank">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i>
                            </a>
                            <?php
                        }
                        ?>
                    </li>
                    <li>Lacking coordinates: <?php echo $statsArr['noCoord']; ?>
                        <?php
                        if($statsArr['noCoord']){
                            ?>
                            <a href="../editor/occurrencetabledisplay.php?collid=<?php echo $collid; ?>&occindex=0&q_catalognumber=&q_customfield1=decimallatitude&q_customtype1=NULL" style="margin-left:5px;" title="Open Editor" target="_blank">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i>
                            </a>
                            <a href="../georef/batchgeoreftool.php?collid=<?php echo $collid; ?>" style="margin-left:5px;" title="Open Batch Georeference Tool" target="_blank">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i><span style="font-size:70%;margin-left:-3px;">b-geo</span>
                            </a>
                            <?php
                        }
                        ?>
                    </li>
                    <li style="margin-left:15px">Lacking coordinates with verbatim coordinates: <?php echo $statsArr['noCoord_verbatim']; ?>
                        <?php
                        if($statsArr['noCoord_verbatim']){
                            ?>
                            <a href="../editor/occurrencetabledisplay.php?collid=<?php echo $collid; ?>&occindex=0&q_catalognumber=&q_customfield1=decimallatitude&q_customtype1=NULL&q_customfield2=verbatimcoordinates&q_customtype2=NOTNULL" style="margin-left:5px;" title="Open Editor" target="_blank">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i>
                            </a>
                            <?php
                        }
                        ?>
                    </li>
                    <li style="margin-left:15px">Lacking coordinates without verbatim coordinates: <?php echo $statsArr['noCoord_noVerbatim']; ?>
                        <?php
                        if($statsArr['noCoord_noVerbatim']){
                            ?>
                            <a href="../editor/occurrencetabledisplay.php?collid=<?php echo $collid; ?>&occindex=0&q_catalognumber=&q_customfield1=decimallatitude&q_customtype1=NULL&q_customfield2=verbatimcoordinates&q_customtype2=NULL" style="margin-left:5px;" title="Open Editor" target="_blank">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i>
                            </a>
                            <?php
                        }
                        ?>
                    </li>
                    <li>
                        <a href="coordinatevalidator.php?collid=<?php echo $collid; ?>">Check coordinates against political boundaries</a>
                    </li>
                </ul>
            </fieldset>
        </div>

        <h3>Taxonomy</h3>
        <div style="margin:0 0 40px 15px;">
            <ul>
                <li><a href="taxonomycleaner.php?collid=<?php echo $collid; ?>">Open Taxonomic Name Resolution Module</a></li>
                <?php
                if($cleanManager->hasDuplicateClusters()){
                    echo '<li><a href="index.php?collid='.$collid.'&tabindex=3&dupedepth=3&action=listdupeconflicts">';
                    echo 'View duplicate occurrences with potential identification conflicts...';
                    echo '</a></li>';
                }
                ?>
            </ul>
        </div>
        <?php
    }
    else{
        echo '<h2>You are not authorized to access this page</h2>';
    }
    ?>
</div>
