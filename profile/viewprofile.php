<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/ProfileManager.php');
include_once(__DIR__ . '/../classes/Person.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']: '';
$userId = array_key_exists('userid',$_REQUEST)?$_REQUEST['userid']:0;
$tabIndex = array_key_exists('tabindex',$_REQUEST)?$_REQUEST['tabindex']:0;

if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) {
    $action = '';
}
if(!is_numeric($userId)) {
    $userId = 0;
}
if(!is_numeric($tabIndex)) {
    $tabIndex = 0;
}

$isSelf = 0;
$isEditor = 0;
if(isset($SYMB_UID) && $SYMB_UID){
    if(!$userId){
        $userId = $SYMB_UID;
    }
    if($userId === $SYMB_UID){
        $isSelf = 1;
    }
    if($isSelf || $IS_ADMIN){
        $isEditor = 1;
    }
}
if(!$userId) {
    header('Location: index.php?refurl=viewprofile.php');
}

$pHandler = new ProfileManager();
$pHandler->setUid($userId);

$statusStr = '';
$person = null;
if($isEditor){
    if($action === 'Submit Edits'){
        $firstname = $_REQUEST['firstname'];
        $middleinitial = (array_key_exists('middleinitial',$_REQUEST)?$_REQUEST['middleinitial']:'');
        $lastname = $_REQUEST['lastname'];
        $email = $_REQUEST['email'];

        $title = array_key_exists('title',$_REQUEST)?$_REQUEST['title']: '';
        $institution = array_key_exists('institution',$_REQUEST)?$_REQUEST['institution']: '';
        $department = array_key_exists('department',$_REQUEST)?$_REQUEST['department']: '';
        $address = array_key_exists('address',$_REQUEST)?$_REQUEST['address']: '';
        $city = array_key_exists('city',$_REQUEST)?$_REQUEST['city']: '';
        $state = array_key_exists('state',$_REQUEST)?$_REQUEST['state']: '';
        $zip = array_key_exists('zip',$_REQUEST)?$_REQUEST['zip']: '';
        $country = array_key_exists('country',$_REQUEST)?$_REQUEST['country']: '';
        $url = array_key_exists('url',$_REQUEST)?$_REQUEST['url']: '';
        $biography = array_key_exists('biography',$_REQUEST)?$_REQUEST['biography']: '';
        $isPublic = array_key_exists('ispublic',$_REQUEST)?$_REQUEST['ispublic']: '';

        $newPerson = new Person();
        $newPerson->setUid($userId);
        $newPerson->setFirstName($firstname);
        if($middleinitial) {
            $newPerson->setMiddleInitial($middleinitial);
        }
        $newPerson->setLastName($lastname);
        $newPerson->setTitle($title);
        $newPerson->setInstitution($institution);
        $newPerson->setDepartment($department);
        $newPerson->setAddress($address);
        $newPerson->setCity($city);
        $newPerson->setState($state);
        $newPerson->setZip($zip);
        $newPerson->setCountry($country);
        $newPerson->setEmail($email);
        $newPerson->setUrl($url);
        $newPerson->setBiography($biography);
        $newPerson->setIsPublic($isPublic);

        if(!$pHandler->updateProfile($newPerson)){
            $statusStr = 'Profile update failed!';
        }
        $person = $pHandler->getPerson();
        $tabIndex = 2;
    }
    elseif($action === 'Change Password'){
        $newPwd = $_REQUEST['newpwd'];
        $updateStatus = false;
        if($isSelf){
            $oldPwd = $_REQUEST['oldpwd'];
            $updateStatus = $pHandler->changePassword($newPwd, $oldPwd, $isSelf);
        }
        else{
            $updateStatus = $pHandler->changePassword($newPwd);
        }
        if($updateStatus){
            $statusStr = "<span style='color:green;'>Password update successful!</span>";
        }
        else{
            $statusStr = 'Password update failed! Are you sure you typed the old password correctly?';
        }
        $person = $pHandler->getPerson();
        $tabIndex = 2;
    }
    elseif($action === 'Change Login'){
        $pwd = '';
        if($isSelf && isset($_POST['newloginpwd'])) {
            $pwd = $_POST['newloginpwd'];
        }
        if(!$pHandler->changeLogin($_POST['newlogin'], $pwd)){
            $statusStr = $pHandler->getErrorStr();
        }
        $person = $pHandler->getPerson();
        $tabIndex = 2;
    }
    elseif($action === 'Clear Tokens'){
        $statusStr = $pHandler->clearAccessTokens();
        $person = $pHandler->getPerson();
        $tabIndex = 2;
    }
    elseif($action === 'Delete Profile'){
        if($pHandler->deleteProfile(true)){
            header('Location: ../index.php');
        }
        else{
            $statusStr = 'Profile deletion failed! Please contact the system administrator';
        }
    }
    elseif($action === 'delusertaxonomy'){
        $statusStr = $pHandler->deleteUserTaxonomy($_GET['utid']);
        $person = $pHandler->getPerson();
        $tabIndex = 2;
    }
    elseif($action === 'Add Taxonomic Relationship'){
        $statusStr = $pHandler->addUserTaxonomy($_POST['taxon'], $_POST['editorstatus'], $_POST['geographicscope'], $_POST['notes']);
        $person = $pHandler->getPerson();
        $tabIndex = 2;
    }

    if(!$person) {
        $person = $pHandler->getPerson();
    }
}
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title><?php echo $DEFAULT_TITLE; ?> - View User Profile</title>
    <link href="../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
    <link type="text/css" href="../css/jquery-ui.css" rel="stylesheet" />
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/tiny_mce/tiny_mce.js"></script>
    <script type="text/javascript">
        let tabIndex = <?php echo $tabIndex; ?>;
    </script>
    <script type="text/javascript" src="../js/symb/profile.viewprofile.js?ver=20210313"></script>
    <script type="text/javascript" src="../js/symb/shared.js"></script>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <?php
    if($isEditor){
        if($statusStr){
            echo "<div style='color:#FF0000;margin:10px 0 10px 10px;'>".$statusStr. '</div>';
        }
        ?>
        <div id="tabs" style="margin:10px;">
            <ul>
                <?php
                if($FLORA_MOD_IS_ACTIVE){
                    ?>
                    <li><a href="../checklists/checklistadminmeta.php?userid=<?php echo $userId; ?>">Species Checklists</a></li>
                    <?php
                }
                ?>
                <li><a href="occurrencemenu.php">Occurrence Management</a></li>
                <li><a href="userprofile.php?userid=<?php echo $userId; ?>">User Profile</a></li>
            </ul>
        </div>
        <?php
    }
    ?>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
