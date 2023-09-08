<?php
/** @var int $queryId */

include_once(__DIR__ . '/datasetmanagement.php');
include_once(__DIR__ . '/../../collections/csvoptions.php');
?>
<!-- Data Download Form -->
<div style="display:none;">
    <form name="datadownloadform" id="datadownloadform" action="../api/search/datadownloader.php" method="post">
        <input id="starrjson" name="starrjson" type="hidden" />
        <input id="dh-q" name="dh-q" type="hidden" />
        <input id="dh-fq" name="dh-fq" type="hidden" />
        <input id="dh-fl" name="dh-fl" type="hidden" />
        <input id="dh-rows" name="dh-rows" type="hidden" />
        <input id="dh-type" name="dh-type" type="hidden" />
        <input id="dh-filename" name="dh-filename" type="hidden" />
        <input id="dh-contentType" name="dh-contentType" type="hidden" />
        <input id="dh-selections" name="dh-selections" type="hidden" />
        <input id="schemacsv" name="schemacsv" type="hidden" />
        <input id="identificationscsv" name="identificationscsv" type="hidden" />
        <input id="imagescsv" name="imagescsv" type="hidden" />
        <input id="formatcsv" name="formatcsv" type="hidden" />
        <input id="zipcsv" name="zipcsv" type="hidden" />
    </form>
</div>

<!-- Dataset Form -->
<div style="display:none;">
    <form name="datasetform" id="datasetform" action="../collections/datasets/datasetHandler.php" method="post" target="_blank">
        <input id="dsstarrjson" name="dsstarrjson" type="hidden" />
        <input id="selectedtargetdatasetid" name="targetdatasetid" type="hidden" />
        <input id="occarrjson" name="occarrjson" type="hidden" />
        <input id="datasetformaction" name="action" type="hidden" />
    </form>
</div>

<input type="hidden" id="queryId" name="queryId" value='<?php echo $queryId; ?>' />
