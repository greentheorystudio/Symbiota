<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ProfileManager.php');
include_once(__DIR__ . '/../classes/Person.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']: '';
$userId = array_key_exists('userid',$_REQUEST)?(int)$_REQUEST['userid']:0;
$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;

if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) {
    $action = '';
}

$isSelf = 0;
$isEditor = 0;
if(isset($GLOBALS['SYMB_UID']) && $GLOBALS['SYMB_UID']){
    if(!$userId){
        $userId = $GLOBALS['SYMB_UID'];
    }
    if($userId === $GLOBALS['SYMB_UID']){
        $isSelf = 1;
    }
    if($isSelf || $GLOBALS['IS_ADMIN']){
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
    }
    elseif($action === 'Change Login'){
        $pwd = '';
        if($isSelf && isset($_POST['newloginpwd'])) {
            $pwd = $_POST['newloginpwd'];
        }
        if(!$pHandler->changeLogin($_POST['newlogin'], $pwd)){
            $statusStr = $pHandler->getErrorStr();
        }
    }
    elseif($action === 'Clear Tokens'){
        $statusStr = $pHandler->clearAccessTokens();
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
    }
    elseif($action === 'Add Taxonomic Relationship'){
        $statusStr = $pHandler->addUserTaxonomy($_POST['taxon'], $_POST['editorstatus'], $_POST['geographicscope'], $_POST['notes']);
    }
    elseif($action === 'resendconfirmationemail'){
        $pHandler->sendConfirmationEmail($userId);
        $statusStr = 'Resent confirmation email!';
    }
    $person = $pHandler->getPerson();
    if($action){
        if($GLOBALS['VALID_USER']){
            $tabIndex = 2;
        }
        else{
            $tabIndex = 0;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> - View User Profile</title>
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link type="text/css" href="../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" />
    <script src="../js/external/all.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../js/external/jquery.js"></script>
    <script type="text/javascript" src="../js/external/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/external/tiny_mce/tiny_mce.js"></script>
    <script type="text/javascript">
        let tabIndex = <?php echo $tabIndex; ?>;
    </script>
    <script type="text/javascript" src="../js/profile.viewprofile.js?ver=20221025"></script>
    <script type="text/javascript" src="../js/shared.js?ver=20221207"></script>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <?php
    if($isEditor){
        if($statusStr){
            echo "<div style='color:#FF0000;margin:10px 0 10px 10px;'>".$statusStr.'</div>';
        }
        ?>
        <div id="tabs" style="margin:10px;">
            <ul>
                <?php
                if($GLOBALS['VALID_USER']){
                    ?>
                    <li><a href="../checklists/checklistadminmeta.php?userid=<?php echo $userId; ?>">Checklists and Projects</a></li>
                    <li><a href="occurrencemenu.php">Occurrence Management</a></li>
                    <?php
                }
                ?>
                <li><a href="userprofile.php?userid=<?php echo $userId; ?>">User Profile</a></li>
            </ul>
        </div>
        <?php
    }
    ?>
</div>
<?php
include(__DIR__ . '/../footer.php');
include_once(__DIR__ . '/../config/footer-includes.php');
?>
</body>
</html>
