<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Configurations.php');
include_once(__DIR__ . '/../../classes/TaxonomyUtilities.php');

if(!$GLOBALS['IS_ADMIN']) {
    header('Location: ../../index.php');
}

$confManager = new Configurations();
$taxaUtilities = new TaxonomyUtilities();

$fullConfArr = $confManager->getConfigurationsArr();
$coreConfArr = $fullConfArr['core'];
$taxonomyRankArr = $taxaUtilities->getRankArr();
$recognizedRanks = isset($GLOBALS['TAXONOMIC_RANKS']) ? json_decode($GLOBALS['TAXONOMIC_RANKS'], true) : array();
?>
<div id="taxonomyconfig">
    <fieldset style="margin: 10px 0;">
        <legend><b>Taxonomy Configurations</b></legend>
        <?php
        if($taxonomyRankArr){
            ?>
            <div>
                <h3>Recognized Taxonomic Ranks</h3>
                <?php
                foreach($taxonomyRankArr as $rankId => $rankArr){
                    $checked = in_array((int)$rankId, $recognizedRanks, true);
                    ?>
                    <div style="margin-top:3px;">
                        <input type="checkbox" class="taxonomy-checkbox" value="<?php echo $rankId; ?>" onchange="processTaxonomyRankCheckChange('<?php echo (isset($coreConfArr['TAXONOMIC_RANKS'])?'update':'add'); ?>');" <?php echo ($checked?'CHECKED':'').((int)$rankId === 10?' DISABLED':''); ?> />
                        <span style="margin-left:20px;"><?php echo $rankArr['rankname']; ?></span> <?php echo ((int)$rankId === 10?'<span style="color:red;">(required)</span>':''); ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        else{
            ?>
            <div class="field-block">
                <span class="field-label">Taxonomic ranks have not been populated in the database</span>
                <span class="field-elem"></span>
            </div>
            <?php
        }
        ?>
    </fieldset>
</div>
