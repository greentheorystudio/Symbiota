<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceDataset.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$action = array_key_exists('submitaction',$_REQUEST)?htmlspecialchars($_REQUEST['submitaction']):'';

if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) {
    $action = '';
}

$datasetManager = new OccurrenceDataset();

$statusStr = '';
if($action === 'createNewDataset'){
    if(!$datasetManager->createDataset($_POST['name'],$_POST['notes'],$GLOBALS['SYMB_UID'])){
        $statusStr = implode(',',$datasetManager->getErrorArr());
    }
}
elseif($action === 'addSelectedToDataset'){
    $datasetID = $_POST['datasetid'];
    if(!$datasetID && $_POST['name']) {
        $datasetManager->createDataset($_POST['name'], '', $GLOBALS['SYMB_UID']);
    }
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Dataset Manager</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <style>
        fieldset{ padding:15px;margin:15px; }
        legend{ font-weight: bold; }
        .dataset-item{ margin-bottom: 10px }
    </style>
    <script src="../../js/all.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../../js/symb/shared.js?ver=20211227"></script>
    <script type="text/javascript">
        function validateAddForm(f){
            if(f.adduser.value === ""){
                alert("Enter a user (login or last name)");
                return false
            }
            if(f.adduser.value.indexOf(" [#") === -1){
                $.ajax({
                    url: "rpc/getuserlist.php",
                    dataType: "json",
                    data: {
                        term: f.adduser.value
                    },
                    success: function(data) {
                        if(data && data !== ""){
                            f.adduser.value = data;
                            alert("Located login: "+data);
                            f.submit();
                        }
                        else{
                            alert("Unable to locate user");
                        }
                    }
                });
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<?php
include(__DIR__ . '/../../header.php');
?>
<div class='navpath'>
    <a href='../../index.php'>Home</a> &gt;&gt;
    <?php
    echo '<a href="../../profile/viewprofile.php?tabindex=1">My Profile</a> &gt;&gt; ';
    ?>
    <a href="index.php">
        <b>Dataset Listing</b>
    </a>
</div>
<div id="innertext">
    <?php
    if($statusStr){
        $color = 'green';
        if(strpos($statusStr,'ERROR') !== false) {
            $color = 'red';
        }
        elseif(strpos($statusStr,'WARNING') !== false) {
            $color = 'orange';
        }
        elseif(strpos($statusStr,'NOTICE') !== false) {
            $color = 'yellow';
        }
        echo '<div style="margin:15px;color:'.$color.';">';
        echo $statusStr;
        echo '</div>';
    }
    $dataSetArr = $datasetManager->getDatasetArr();
    ?>
    <div>
        <div style="float:right;margin:10px;" title="Create New Dataset" onclick="toggle('adddiv')">
            <i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i>
        </div>
        <h2>Occurrence Dataset Management</h2>
        <div>These tools will allow you to define and manage datasets profiles. Once a profile is created, you can link occurrence records via the occurrence search and display pages.</div>
        <div id=adddiv style="display:none">
            <fieldset>
                <legend><b>Create New Dataset</b></legend>
                <form name="adminform" action="index.php" method="post" onsubmit="return validateEditForm(this)">
                    <div>
                        <b>Name</b><br />
                        <input name="name" type="text" style="width:250px" />
                    </div>
                    <div>
                        <b>Notes</b><br />
                        <input name="notes" type="text" style="width:90%;" />
                    </div>
                    <div style="margin:15px">
                        <button name="submitaction" type="submit" value="createNewDataset">Create New Dataset</button>
                    </div>
                </form>
            </fieldset>
        </div>
        <?php
        if($dataSetArr){
            ?>
            <fieldset>
                <legend><b>Owned by You</b></legend>
                <?php
                if(array_key_exists('owner',$dataSetArr)){
                    $ownerArr = $dataSetArr['owner'];
                    unset($dataSetArr['owner']);
                    foreach($ownerArr as $dsid => $dsArr){
                        ?>
                        <div class="dataset-item">
                            <div>
                                <a href="datasetmanager.php?datasetid=<?php echo $dsid; ?>" title="Manage and edit dataset">
                                    <?php
                                    echo '<b>'.$dsArr['name'].' (#'.$dsid.')</b>';
                                    ?>
                                </a>
                            </div>
                            <div style="margin-left:15px;">
                                <?php
                                echo ($dsArr['notes']?'<div>'.$dsArr['notes'].'</div>':'');
                                echo '<div>Created: '.$dsArr['ts'].'</div>';
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                else{
                    echo '<div style="font-weight:bold;">There are no datasets owned by you</div>';
                }
                ?>
            </fieldset>
            <fieldset>
                <legend>Shared with You</legend>
                <?php
                if(array_key_exists('other',$dataSetArr)){
                    $otherArr = $dataSetArr['other'];
                    foreach($otherArr as $dsid => $dsArr){
                        ?>
                        <div>
                            <a href="datasetmanager.php?datasetid=<?php echo $dsid; ?>" title="Access Dataset">
                                <?php
                                $role = 'Dataset reader';
                                if($dsArr['role'] === 'DatasetAdmin') {
                                    $role = 'Dataset Administator';
                                }
                                elseif($dsArr['role'] === 'DatasetEditor') {
                                    $role = 'Dataset Editor';
                                }
                                echo '<b>'.$dsArr['name'].' (#'.$dsid.')</b> - '.$role;
                                ?>
                            </a>
                        </div>
                        <div style="margin-left:15px;">
                            <?php
                            echo ($dsArr['notes']?$dsArr['notes'].'<br/>':'');
                            echo 'Created: '.$dsArr['ts'];
                            ?>
                        </div>
                        <?php
                    }
                }
                else{
                    echo '<div style="font-weight:bold;">There are no datasets shared with you</div>';
                }
                ?>
            </fieldset>
            <?php
        }
        else{
            ?>
            <div style="margin:20px">
                <div style="font-weight:bold">There are no datasets associated to your login</div>
                <div style="margin-top:15px"><a href="#" onclick="toggle('adddiv');">Create a New Dataset</a></div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<?php
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>
