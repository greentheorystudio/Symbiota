<?php
include_once(__DIR__ . '/../../classes/TaxonomyDisplayManager.php');

$target = array_key_exists('target',$_REQUEST)?$_REQUEST['target']: '';
$displayAuthor = array_key_exists('displayauthor',$_REQUEST)?(int)$_REQUEST['displayauthor']:0;
$displayFullTree = array_key_exists('displayfulltree',$_REQUEST)?(int)$_REQUEST['displayfulltree']:0;
$displaySubGenera = array_key_exists('displaysubgenera',$_REQUEST)?(int)$_REQUEST['displaysubgenera']:0;

$taxonDisplayObj = new TaxonomyDisplayManager();
$taxonDisplayObj->setTargetStr($target);
$taxonDisplayObj->setDisplayAuthor($displayAuthor);
$taxonDisplayObj->setDisplayFullTree($displayFullTree);
$taxonDisplayObj->setDisplaySubGenera($displaySubGenera);
?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#taxontarget").autocomplete({
                source: function( request, response ) {
                    $.getJSON( "../../api/taxa/autofillsciname.php", { term: request.term }, response );
                }
            },{ minLength: 3 }
        );

    });
</script>
<div>
    <div>
        <form id="tdform" name="tdform" action="index.php" method='POST'>
            <fieldset style="padding:10px;width:550px;">
                <legend><b>Enter Taxon to Edit</b></legend>
                <div>
                    <b>Taxon:</b>
                    <input id="taxontarget" name="target" type="text" style="width:400px;" value="<?php echo $taxonDisplayObj->getTargetStr(); ?>" />
                </div>
                <div style="float:right;margin:15px 80px 15px 15px;">
                    <input type="hidden" name="tabindex" value="1" />
                    <input name="tdsubmit" type="submit" value="Find Taxon"/>
                </div>
                <div style="margin:15px 15px 0 60px;">
                    <input name="displayauthor" type="checkbox" value="1" <?php echo ($displayAuthor?'checked':''); ?> /> Display authors
                </div>
                <div style="margin:3px 15px 0 60px;">
                    <input name="displayfulltree" type="checkbox" value="1" <?php echo ($displayFullTree?'checked':''); ?> /> Display full tree below family
                </div>
                <div style="margin:3px 15px 15px 60px;">
                    <input name="displaysubgenera" type="checkbox" value="1" <?php echo ($displaySubGenera?'checked':''); ?> /> Display species with subgenera
                </div>
            </fieldset>
        </form>
    </div>
    <?php
    if($target){
        $taxonDisplayObj->displayTaxonomyHierarchy();
    }
    ?>
</div>
