<?php
/** @var boolean $inputWindowMode */
?>
<div id="spatialpanel">
    <div id="sidepanel-accordion">
        <?php
        if($inputWindowMode) {
            include_once(__DIR__ . '/vectortoolstab.php');
        }
        else {
            include_once(__DIR__ . '/queryrecordstabs.php');
            ?>

            <h3 class="tabtitle">Vector Tools</h3>
            <div id="vectortoolstab" style="width:379px;padding:0;">
                <ul>
                    <li><a class="tabtitle" href="#polycalculatortab">Shapes</a></li>
                    <li><a class="tabtitle" href="#pointscalculatortab">Points</a></li>
                </ul>
                <?php include_once(__DIR__ . '/vectortoolstab.php'); ?>
                <?php include_once(__DIR__ . '/pointvectortoolstab.php'); ?>
            </div>

            <h3 class="tabtitle">Raster Tools</h3>
            <div id="rastertoolstab" style="width:379px;padding:0;">
                <?php include_once(__DIR__ . '/rastertoolstab.php'); ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>
