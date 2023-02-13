<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/TaxonomyDisplayManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$target = array_key_exists('target',$_REQUEST)?$_REQUEST['target']: '';
$displayAuthor = array_key_exists('displayauthor',$_REQUEST)?(int)$_REQUEST['displayauthor']:0;
$statusStr = array_key_exists('statusstr',$_REQUEST)?$_REQUEST['statusstr']:'';

$taxonDisplayObj = new TaxonomyDisplayManager();
$taxonDisplayObj->setTargetStr($target);

$editable = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $editable = true;
}

$treePath = array();
$targetId = 0;
if($target){
    $treePath = $taxonDisplayObj->getDynamicTreePath();
    $targetId = end($treePath);
    reset($treePath);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']. ' Taxonomy Explorer' .($target ? ': ' . $taxonDisplayObj->getTargetStr() : ''); ?></title>
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link type="text/css" href="../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" />
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/dojo/1.10.4/dijit/themes/claro/claro.css" media="screen">
    <style>
        .dijitLeaf,
        .dijitIconLeaf,
        .dijitFolderClosed,
        .dijitIconFolderClosed,
        .dijitFolderOpened,
        .dijitIconFolderOpen {
            background-image: none;
            width: 0;
            height: 0;
        }
    </style>
    <script src="../js/external/all.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../js/external/jquery.js"></script>
    <script type="text/javascript" src="../js/external/jquery-ui.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/dojo/1.10.4/dojo/dojo.js" data-dojo-config="async: true"></script>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            $("#taxontarget").autocomplete({
                    source: function( request, response ) {
                        $.getJSON( "../api/taxa/autofillsciname.php", {
                            term: request.term,
                            limit: 10,
                            hideauth: false
                        }, response );
                    }
                },{ minLength: 3 }
            );
        });
    </script>
</head>
<body class="claro">
<?php
include(__DIR__ . '/../header.php');
?>
<div class="navpath">
    <a href="../index.php">Home</a> &gt;&gt;
    <a href="taxonomydynamicdisplay.php"><b>Taxonomy Explorer</b></a>
</div>
<div id="innertext">
    <?php
    if($statusStr){
        ?>
        <hr/>
        <div style="color:<?php echo (strpos($statusStr,'SUCCESS') !== false?'green':'red'); ?>;margin:15px;">
            <?php echo $statusStr; ?>
        </div>
        <hr/>
        <?php
    }
    if($editable){
        ?>
        <div style="float:right;" title="Add a New Taxon">
            <a href="taxonomy/index.php">
                <i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i>
            </a>
        </div>
        <?php
    }
    ?>
    <div>
        <form id="tdform" name="tdform" action="taxonomydynamicdisplay.php" method='POST'>
            <fieldset style="padding:10px;width:500px;">
                <legend><b>Enter a taxon</b></legend>
                <div>
                    <b>Taxon:</b>
                    <input id="taxontarget" name="target" type="text" style="width:400px;" value="<?php echo $taxonDisplayObj->getTargetStr(); ?>" />
                </div>
                <div style="float:right;margin:15px 80px 15px 15px;">
                    <input name="tdsubmit" type="submit" value="Display Taxon Tree"/>
                </div>
                <div style="margin:15px 15px 0 60px;">
                    <input name="displayauthor" type="checkbox" value="1" <?php echo ($displayAuthor?'checked':''); ?> /> Display authors
                </div>
            </fieldset>
        </form>
    </div>
    <?php
    if($targetId){
        ?>
        <div id="tree"></div>
        <script type="text/javascript">
            require([
                "dojo/window",
                "dojo/_base/declare",
                "dojo/dom",
                "dojo/on",
                "dijit/Tree",
                "dijit/tree/ObjectStoreModel",
                "dijit/tree/dndSource",
                "dojo/store/JsonRest",
                "dojo/domReady!"
            ], function(win, declare, dom, on, Tree, ObjectStoreModel, dndSource, JsonRest){
                const taxonTreeStore = new JsonRest({
                    target: "../api/taxa/getdynamicchildren.php",
                    labelAttribute: "label",
                    getChildren: function(object){
                        return this.query({
                            id:object.id,
                            authors:<?php echo $displayAuthor; ?>,
                            targetid:<?php echo $targetId; ?>
                        }).then(function(fullObject){
                            return fullObject.children;
                        });
                    },
                    mayHaveChildren: function(object){
                        return "children" in object;
                    }
                });

                const taxonTreeModel = new ObjectStoreModel({
                    store: taxonTreeStore,
                    deferItemLoadingUntilExpand: true,
                    getRoot: function(onItem){
                        this.store.query({
                            id:"root",
                            authors:<?php echo $displayAuthor; ?>,
                            targetid:<?php echo $targetId; ?>
                        }).then(onItem);
                    },
                    mayHaveChildren: function(object){
                        return "children" in object;
                    }
                });

                const TaxonTreeNode = declare(Tree._TreeNode, {
                    _setLabelAttr: {
                        node: "labelNode",
                        type: "innerHTML"
                    }
                });

                const taxonTree = new Tree({
                    model: taxonTreeModel,
                    showRoot: false,
                    label: "Taxa Tree",
                    persist: false,
                    _createTreeNode: function (args) {
                        return new TaxonTreeNode(args);
                    },
                    onClick: function (item) {
                        location.href = item.url;
                    }
                }, "tree");

                taxonTree.set("path", <?php echo json_encode($treePath); ?>).then(
                    function(){
                        if(taxonTree.selectedNode){
                            win.scrollIntoView(taxonTree.selectedNode.id);
                        }
                    }
                );
                taxonTree.startup();

            });
        </script>
        <?php
    }
    elseif($target){
        echo '<div><h3>The taxon you entered is not found in the Taxonomic Thesaurus</h3></div>';
    }
    ?>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>

