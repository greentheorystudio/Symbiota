<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$clid = array_key_exists('clid',$_REQUEST) ? (int)$_REQUEST['clid'] : 0;
$pid = array_key_exists('proj',$_REQUEST) ? (int)$_REQUEST['pid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Interactive Key</title>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <script type="text/javascript">
            const CLID = <?php echo $clid; ?>;
            const PID = <?php echo $pid; ?>;
        </script>
    </head>
    <body>
        <div id="app-container">
            <?php
            include(__DIR__ . '/../header.php');
            echo '<div class="navpath">';
            echo '<a href="../index.php">Home</a> &gt;&gt; ';
            if($dynClid){
                if($dataManager->getClType() === 'Specimen Checklist'){
                    $link = $GLOBALS['CLIENT_ROOT'].'/collections/list.php?starr={"clid":"'.$clid.'"}';
                    echo "<a href='".$link."'>";
                    echo 'Occurrence Checklist';
                    echo '</a> &gt;&gt; ';
                }
            }
            elseif($clid){
                echo '<a href="'.$GLOBALS['CLIENT_ROOT'].'/checklists/checklist.php?cl='.$clid.'&proj='.$projValue.'">';
                echo 'Checklist: '.$dataManager->getClName();
                echo '</a> &gt;&gt; ';
            }
            elseif($pid){
                echo '<a href="'.$GLOBALS['CLIENT_ROOT'].'/projects/index.php?pid='.$pid.'">';
                echo 'Project Checklists';
                echo '</a> &gt;&gt; ';
            }
            echo '<b>Key: '.$dataManager->getClName().'</b>';
            echo '</div>';

            ?>
            <div class="navpath">
                <a :href="(clientRoot + '/index.php')">Home</a> &gt;&gt;
                <b>Search Collections</b>
            </div>
            <div id="innertext">
                <form name="keyform" id="keyform" action="key.php" method="get">
                    <table id="keytable">
                        <tr>
                            <td id="keycharcolumn">
                                <div>
                                    <div style="font-weight:bold;margin-top:0.5em;">Taxon:</div>
                                    <select name="taxon">
                                        <?php
                                        echo "<option value='All Species'>-- Select a Taxonomic Group --</option>\n";
                                        $selectList = $dataManager->getTaxaFilterList();
                                        foreach($selectList as $value){
                                            $selectStr = ($value === $taxonValue? 'SELECTED' : '');
                                            echo "<option $selectStr>$value</option>\n";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div style='font-weight:bold; margin-top:0.5em;'>
                                    <input type="hidden" id="cl" name="cl" value="<?php echo $clid; ?>" />
                                    <input type="hidden" id="dynclid" name="dynclid" value="<?php echo $dynClid; ?>" />
                                    <input type="hidden" id="proj" name="proj" value="<?php echo $projValue; ?>" />
                                    <input type="hidden" id="rv" name="rv" value="<?php echo $dataManager->getRelevanceValue(); ?>" />
                                    <input type="submit" name="submitbutton" id="submitbutton" value="Display/Reset Species List"/>
                                </div>
                                <hr style="height:2px" />

                                <?php
                                if(count($languages) > 1){
                                    echo "<div id='langlist' style='margin:0.5em;'>Languages: <select name='lang' onchange='setLang(this);'>\n";
                                    foreach($languages as $l){
                                        echo "<option value='".$l."' ".($GLOBALS['DEFAULT_LANG'] === $l? 'SELECTED' : '').">$l</option>\n";
                                    }
                                    echo "</select></div>\n";
                                }
                                echo "<div style='margin:5px'>Display as: <select name='displaymode' onchange='document.getElementById(\"keyform\").submit();'><option value='0'>Scientific Name</option><option value='1' ".($displayMode? ' SELECTED' : ''). ' >Common Name</option></select></div>';
                                if($chars){
                                    foreach($chars as $key => $htmlStrings){
                                        echo $htmlStrings."\n";
                                    }
                                }
                                ?>
                            </td>
                            <td id="keymidcolumn"></td>
                            <td id="keytaxacolumn">
                                <?php
                                if(($clid && $taxonValue) || $dynClid){
                                    ?>
                                    <table style="border:0;width:300px;">
                                        <tr><td colspan='2'>
                                                <h2>
                                                    <?php
                                                    echo '<a href="../checklists/checklist.php?cl='.$clid.'&dynclid='.$dynClid.'&proj='.$projValue.'" target="_blank">';
                                                    echo $dataManager->getClName(). ' ';
                                                    echo '</a>';
                                                    ?>
                                                </h2>
                                                <?php
                                                if(!$dynClid) {
                                                    echo '<div>' . $dataManager->getClAuthors() . '</div>';
                                                }
                                                ?>
                                            </td></tr>
                                        <?php
                                        $count = $dataManager->getTaxaCount();
                                        if($count > 0){
                                            echo "<tr><td colspan='2'>Species Count: ".$count."</td></tr>\n";
                                        }
                                        else{
                                            echo "<tr><td colspan='2'>There are no species matching your criteria. Please deselect some characters to make the search less restrictive.</td></tr>\n";
                                        }
                                        ksort($taxa);
                                        foreach($taxa as $family => $species){
                                            echo "<tr><td colspan='2'><h3 style='margin-bottom:0;margin-top:10px;'>$family</h3></td></tr>\n";
                                            natcasesort($species);
                                            foreach($species as $tid => $disName){
                                                $newSpLink = '../taxa/index.php?taxon='.$tid. '&cl=' .($dataManager->getClType() === 'static' ?$dataManager->getClName(): '');
                                                echo "<tr><td><div style='margin:0 5px 0 10px;'><a href='".$newSpLink."' target='_blank'><i>$disName</i></a></div></td>\n";
                                                echo "<td style='text-align: right;'>\n";
                                                if($isEditor){
                                                    echo "<a href='tools/editor.php?tid=$tid&lang=".$GLOBALS['DEFAULT_LANG']."' target='_blank'><i style='height:15px;width:15px;' title='Edit morphology' class='far fa-edit'></i></a>\n";
                                                }
                                                echo "</td></tr>\n";
                                            }
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                else{
                                    echo $dataManager->getIntroHtml();
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                    <?php
                    if(array_key_exists('crumburl',$_REQUEST)) {
                        echo "<input type='hidden' name='crumburl' value='" . $_REQUEST['crumburl'] . "' />";
                    }
                    if(array_key_exists('crumbtitle',$_REQUEST)) {
                        echo "<input type='hidden' name='crumbtitle' value='" . $_REQUEST['crumbtitle'] . "' />";
                    }
                    ?>
                </form>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script>
            const keyIdentificationModule = Vue.createApp({
                setup() {
                    const baseStore = useBaseStore();

                    const clId = CLID;
                    const clientRoot = baseStore.getClientRoot;
                    const pId = PID;

                    Vue.onMounted(() => {

                    });

                    return {
                        clId,
                        clientRoot,
                        pId
                    }
                }
            });
            keyIdentificationModule.use(Quasar, { config: {} });
            keyIdentificationModule.use(Pinia.createPinia());
            keyIdentificationModule.mount('#app-container');
        </script>
    </body>
</html>

