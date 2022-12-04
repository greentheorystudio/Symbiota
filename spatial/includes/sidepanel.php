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
            <div id="vectortoolstab">
                <ul>
                    <li><a class="tabtitle" href="#polycalculatortab">Shapes</a></li>
                    <li><a class="tabtitle" href="#pointscalculatortab">Points</a></li>
                </ul>
                <?php include_once(__DIR__ . '/vectortoolstab.php'); ?>
                <?php include_once(__DIR__ . '/pointvectortoolstab.php'); ?>
            </div>

            <h3 class="tabtitle">Raster Tools</h3>
            <div id="rastertoolstab">
                <?php include_once(__DIR__ . '/rastertoolstab.php'); ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>
