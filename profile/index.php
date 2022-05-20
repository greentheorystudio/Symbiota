<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ProfileManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

$login = array_key_exists('login',$_REQUEST)?$_REQUEST['login']:'';
$remMe = array_key_exists('remember',$_POST)?$_POST['remember']:'';
$emailAddr = array_key_exists('emailaddr',$_POST)?$_POST['emailaddr']:'';
$resetPwd = array_key_exists('resetpwd',$_REQUEST)?(int)$_REQUEST['resetpwd']:0;
$uid = array_key_exists('uid',$_REQUEST)?(int)$_REQUEST['uid']:0;
$confirmationCode = array_key_exists('confirmationcode',$_REQUEST)?htmlspecialchars($_REQUEST['confirmationcode']):'';
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']: '';
if(!$action && array_key_exists('submit',$_REQUEST)) {
    $action = $_REQUEST['submit'];
}

$refUrl = '';
if(array_key_exists('refurl',$_REQUEST)){
	$refGetStr = '';
	foreach($_GET as $k => $v){
		if($k !== 'refurl'){
			if($k === 'attr' && is_array($v)){
				foreach($v as $v2){
					$refGetStr .= '&attr[]=' .$v2;
				}
			}
			else{
				$refGetStr .= '&' .$k. '=' .$v;
			}
		}
	}
	$refUrl = str_replace('&amp;', '&',htmlspecialchars($_REQUEST['refurl'], ENT_NOQUOTES));
	if(substr($refUrl,-4) === '.php'){
		$refUrl .= '?' .substr($refGetStr,1);
	}
	else{
		$refUrl .= $refGetStr;
	}
}

$pHandler = new ProfileManager();

$statusStr = '';

if($login && !$pHandler->setUserName($login)) {
    $login = '';
    $statusStr = 'Invalid login name';
}
if($emailAddr && !$pHandler->validateEmailAddress($emailAddr)) {
    $emailAddr = '';
    $statusStr = 'Invalid email';
}
if(!is_numeric($resetPwd)) {
    $resetPwd = 0;
}
if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) {
    $action = '';
}
if($remMe) {
    $pHandler->setRememberMe(true);
}

if($action === 'confirm' && $uid && $confirmationCode){
    $pHandler->reset();
    $statusStr = $pHandler->validateFromConfirmationEmail($uid,$confirmationCode);
}
elseif($action === 'logout'){
	$pHandler->reset();
	header('Location: ../index.php');
}
elseif($action === 'Login'){
	if($pHandler->authenticate($_POST['password'])){
		if(!$refUrl || (strncasecmp($refUrl, 'http', 4) === 0) || strpos($refUrl,'newprofile.php')){
			header('Location: ../index.php');
		}
		else{
			header('Location: ' .$refUrl);
		}
	}
	else{
		$statusStr = 'Your username or password was incorrect. Please try again.<br/> If you are unable to remember your login credentials,<br/> use the controls below to retrieve your login or reset your password.';
	}
}
elseif($action === 'Retrieve Login'){
	if($emailAddr){
		if($pHandler->lookupUserName($emailAddr)){
			$statusStr = 'Your login name has been emailed to you. Please check your junk folder if no email appears in your inbox.';
		}
		else{
			$statusStr = $pHandler->getErrorStr();
		}
	}
}
elseif($resetPwd){
	$statusStr = $pHandler->resetPassword($pHandler->getUid($login),false);
}
else{
	$statusStr = $pHandler->getErrorStr();
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Login</title>
	<link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<script type="text/javascript">
		function resetPassword(){
			if(document.getElementById("login").value === ""){
				alert("Enter your login name in the Login field and leave the password blank");
				return false;
			}
			document.getElementById("resetpwd").value = "1";
			document.forms["loginform"].submit();
		}

		function checkCreds(){
			if(document.getElementById("login").value === "" || document.getElementById("password").value === ""){
				alert("Please enter your login and password.");
				return false;
			}
			return true;
		}
	</script>
	<script src="../js/symb/shared.js?ver=20220310" type="text/javascript"></script>
</head>
<body>

<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext" style="padding-left:0;margin-left:0;">

	<?php
	if($statusStr){
		?>
		<div style='color:#FF0000;margin: 1em 1em 0 1em;'>
			<?php
			echo $statusStr;
			?>
		</div>
		<?php
	}
	?>

	<div style="width:300px;margin-right:auto;margin-left:auto;">
		<fieldset style='padding:25px;margin:20px;width:300px;background-color:#FFFFCC;border:2px outset #E8EEFA;'>
			<form id="loginform" name="loginform" action="index.php" onsubmit="return checkCreds();" method="post">
				<div style="margin: 10px;font-weight:bold;">
					Login:&nbsp;&nbsp;&nbsp;<input id="login" name="login" value="<?php echo $login; ?>" style="border-style:inset;" />
				</div>
				<div style="margin:10px;font-weight:bold;">
					Password:&nbsp;&nbsp;<input type="password" id="password" name="password"  style="border-style:inset;" autocomplete="off" />
				</div>
				<div style="margin:10px">
					<input type="checkbox" value='1' name="remember" >
					Remember me on this computer
				</div>
				<div style="margin-right:10px;float:right;">
					<input type="hidden" name="refurl" value="<?php echo $refUrl; ?>" />
					<input type="hidden" id="resetpwd" name="resetpwd" value="">
					<input type="submit" value="Login" name="action">
				</div>
			</form>
		</fieldset>
		<div style="width:300px;text-align:center;margin:20px;">
			<div style="font-weight:bold;">
				Don't have an Account?
			</div>
			<div style="">
				<a href="newprofile.php?refurl=<?php echo $refUrl; ?>">Create an account here</a>
			</div>
			<?php
            if($GLOBALS['EMAIL_CONFIGURED']){
                ?>
                <div style="font-weight:bold;margin-top:5px">
                    Can't remember your password?
                </div>
                <div style="color:blue;cursor:pointer;" onclick="resetPassword();">Reset Password</div>
                <div style="font-weight:bold;margin-top:5px">
                    Can't Remember Login Name?
                </div>
                <div>
                    <div style="color:blue;cursor:pointer;" onclick="toggle('emaildiv');">Retrieve Login</div>
                    <div id="emaildiv" style="display:none;margin:10px 0 10px 40px;">
                        <fieldset style="padding:10px;">
                            <form id="retrieveloginform" name="retrieveloginform" action="index.php" method="post">
                                <div>Your Email: <input type="text" name="emailaddr" /></div>
                                <div><input type="submit" name="action" value="Retrieve Login"/></div>
                            </form>
                        </fieldset>
                    </div>
                </div>
                <?php
            }
            elseif(isset($GLOBALS['ADMIN_EMAIL']) && $GLOBALS['ADMIN_EMAIL']){
                ?>
                <div style="font-weight:bold;margin-top:5px">
                    Can't remember your password?
                </div>
                <div style="font-weight:bold;margin-top:5px">
                    Can't Remember Login Name?
                </div>
                <div style="color:red;">
                    Contact the portal administrator at<br>
                    <?php echo $GLOBALS['ADMIN_EMAIL']; ?><br>
                    for assistance.
                </div>
                <?php
            }
            ?>
		</div>
	</div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>	
