<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/ProfileManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$login = array_key_exists('login',$_POST)?$_POST['login']:'';
$emailAddr = array_key_exists('emailaddr',$_POST)?$_POST['emailaddr']:'';
$action = array_key_exists('submit',$_REQUEST)?$_REQUEST['submit']:'';

$pHandler = new ProfileManager();
$middle = $pHandler->checkFieldExists('users','middleinitial');
$displayStr = '';

if($login && !$pHandler->setUserName($login)) {
    $login = '';
    $displayStr = 'Invalid login name';
}
if($emailAddr && !$pHandler->validateEmailAddress($emailAddr)) {
    $emailAddr = '';
    $displayStr = 'Invalid login name';
}
if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) {
    $action = '';
}

if($action === 'Create Login'){
    if($pHandler->checkLogin($emailAddr)){
        if($pHandler->register($_POST)){
            header('Location: ../index.php');
        }
        else{
            $displayStr = 'FAILED: Unable to create user.<div style="margin-left:55px;">Please contact system administrator for assistance.</div>';
        }
    }
    else{
        $displayStr = $pHandler->getErrorStr();
    }
}

?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - New User Profile</title>
	<link href="../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
    <style type="text/css">
        canvas {
            border: 1px solid #000;
            height: 50px;
            width: 400px;
        }
    </style>
    <script src="../js/jquery.js" type="text/javascript"></script>
	<script type="text/javascript">
        let randNumber = 0;

        $(document).ready(function() {
            randNumber = Math.floor(1000000 + Math.random() * 900000);
            const canvas = document.getElementById("captchaCanvas");
            const ctx = canvas.getContext("2d");
            ctx.font = "80px Times New Roman";
            ctx.fillText(randNumber.toString(), 10, 100);
        });

        function validateform(f){
			const pwd1 = f.pwd.value;
			const pwd2 = f.pwd2.value;
            const enteredValue = document.getElementById("human-entry").value;
			if(pwd1 === "" || pwd2 === ""){
				alert("Both password fields must contain a value");
				return false;
			}
			if(pwd1.charAt(0) === " " || pwd1.slice(-1) === " "){
				alert("Password cannot start or end with a space, but they can include spaces within the password");
				return false;
			}
			if(pwd1.length < 7){
				alert("Password must be greater than 6 characters");
				return false;
			}
			if(pwd1 !== pwd2){
				alert("Password do not match, please enter again");
				f.pwd.value = "";
				f.pwd2.value = "";
				f.pwd2.focus();
				return false;
			}
			if(f.login.value.replace(/\s/g, "") === ""){
				window.alert("User Name must contain a value");
				return false;
			}
			if( /[^0-9A-Za-z_!@#$-+]/.test( f.login.value ) ) {
		        alert("Login name should only contain 0-9A-Za-z_!@ (spaces are not allowed)");
		        return false;
		    }
			if(f.emailaddr.value.replace(/\s/g, "") === "" ){
				window.alert("Email address is required");
				return false;
			}
			if(f.firstname.value.replace(/\s/g, "") === ""){
				window.alert("First Name must contain a value");
				return false;
			}
			if(f.lastname.value.replace(/\s/g, "") === ""){
				window.alert("Last Name must contain a value");
				return false;
			}
            if(enteredValue.toString() !== randNumber.toString()){
                window.alert("Enter the number displayed in the box to prove you're human");
                return false;
            }

			return true;
		}

        function verifyUserInput() {
            const enteredValue = document.getElementById("human-entry").value;
            if(enteredValue.toString() === randNumber.toString()){
                setTimeout(function() {
                    const enteredValue2 = document.getElementById("human-entry").value;
                    document.getElementById("submitButton").disabled = enteredValue2.toString() !== randNumber.toString();
                }, 500 );
            }
            else{
                document.getElementById("submitButton").disabled = true;
            }
        }
	</script>
</head>
<body>
	<?php
	include(__DIR__ . '/../header.php');
	?>
	<div id="innertext">
	<h1>Create New Profile</h1>

	<?php
	if($displayStr){
		echo '<div style="margin:10px;font-size:110%;font-weight:bold;color:red;">';
		if($displayStr === 'login_exists'){
			echo 'This login ('.$login.') is already being used.<br> '.
				'Please choose a different login name or visit the <a href="index.php?login='.$login.'">login page</a> if you believe this might be you';
		}
		elseif($displayStr === 'email_registered'){
			?>
			<div>
				A different login is already registered to this email address.<br/>
				Use button below to have login emailed to <?php echo $emailAddr; ?>
				<div style="margin:15px">
					<form name="retrieveLoginForm" method="post" action="index.php">
						<input name="emailaddr" type="hidden" value="<?php echo $emailAddr; ?>" />
						<input name="action" type="submit" value="Retrieve Login" />
					</form>
				</div>
			</div>
			<?php
		}
		elseif($displayStr === 'email_invalid'){
			echo 'Email address not valid';
		}
		else{
			echo $displayStr;
		}
		echo '</div>';
	}
	?>
	<fieldset style='margin:10px;width:95%;'>
		<legend><b>Login Details</b></legend>
		<form action="newprofile.php" method="post" onsubmit="return validateform(this);">
			<div style="margin:15px;">
				<table style="border-spacing:3px;">
					<tr>
						<td style="width:120px;">
							<b>Login:</b>
						</td>
						<td>
							<input name="login" value="<?php echo $login; ?>" size="20" />
							<span style="color:red;">*</span>
							<br/>&nbsp;
						</td>
					</tr>
					<tr>
						<td>
							<b>Password:</b>
						</td>
						<td>
							<input name="pwd" id="pwd" value="" size="20" type="password" autocomplete="off" />
							<span style="color:red;">*</span>
						</td>
					</tr>
					<tr>
						<td>
							<b>Password Again:</b>
						</td>
						<td>
							<input id="pwd2" name="pwd2" value="" size="20" type="password" autocomplete="off" />
							<span style="color:red;">*</span>
							<br/>&nbsp;
						</td>
					</tr>
					<tr>
						<td><span style="font-weight:bold;">First Name:</span></td>
						<td>
							<input id="firstname" name="firstname" size="40" value="<?php echo (isset($_POST['firstname'])?htmlspecialchars($_POST['firstname']):''); ?>">
							<span style="color:red;">*</span>
						</td>
					</tr>
                    <?php
                    if($middle){
                        ?>
                        <tr>
                            <td><span style="font-weight:bold;">Middle Initial:</span></td>
                            <td>
                                <input id="middleinitial" name="middleinitial" size="3" value="<?php echo (isset($_POST['middleinitial'])?htmlspecialchars($_POST['middleinitial']):''); ?>">
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
					<tr>
						<td><span style="font-weight:bold;">Last Name:</span></td>
						<td>
							<input id="lastname" name="lastname" size="40" value="<?php echo (isset($_POST['lastname'])?htmlspecialchars($_POST['lastname']):''); ?>">
							<span style="color:red;">*</span>
						</td>
					</tr>
					<tr>
						<td><span style="font-weight:bold;">Email Address:</span></td>
						<td>
							<span class="profile"><input name="emailaddr"  size="40" value="<?php echo $emailAddr; ?>"></span>
							<span style="color:red;">*</span>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><span style="color:red;">* required fields</span></td>
					</tr>
				</table>
				<div style="margin:15px 0 10px 0;"><b><u>Information below is optional, but encouraged</u></b></div>
				<table style="border-spacing:3px;">
					<tr>
						<td><b>Title:</b></td>
						<td>
							<span class="profile"><input name="title"  size="40" value="<?php echo (isset($_POST['title'])?htmlspecialchars($_POST['title']):''); ?>"></span>
						</td>
					</tr>
					<tr>
						<td><b>Institution:</b></td>
						<td>
							<span class="profile"><input name="institution"  size="40" value="<?php echo (isset($_POST['institution'])?htmlspecialchars($_POST['institution']):'') ?>"></span>
						</td>
					</tr>
                    <tr>
                        <td><b>Department:</b></td>
                        <td>
                            <span class="profile"><input name="department"  size="40" value="<?php echo (isset($_POST['department'])?htmlspecialchars($_POST['department']):'') ?>"></span>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Street Address:</b></td>
                        <td>
                            <span class="profile"><input name="address"  size="40" value="<?php echo (isset($_POST['address'])?htmlspecialchars($_POST['address']):'') ?>"></span>
                        </td>
                    </tr>
					<tr>
						<td><span style="font-weight:bold;">City:</span></td>
						<td>
							<span class="profile"><input id="city" name="city" size="40" value="<?php echo ($_POST['city'] ?? ''); ?>"></span>
						</td>
					</tr>
					<tr>
						<td><span style="font-weight:bold;">State:</span></td>
						<td>
							<span class="profile"><input id="state" name="state"  size="40" value="<?php echo (isset($_POST['state'])?htmlspecialchars($_POST['state']):''); ?>"></span>
						</td>
					</tr>
					<tr>
						<td><b>Zip Code:</b></td>
						<td>
							<span class="profile"><input name="zip"  size="40" value="<?php echo (isset($_POST['zip'])?htmlspecialchars($_POST['zip']):''); ?>"></span>
						</td>
					</tr>
					<tr>
						<td><span style="font-weight:bold;">Country:</span></td>
						<td>
							<span class="profile"><input id="country" name="country"  size="40" value="<?php echo (isset($_POST['country'])?htmlspecialchars($_POST['country']):''); ?>"></span>
						</td>
					</tr>
					<tr>
						<td><b>Url:</b></td>
						<td>
							<span class="profile"><input name="url"  size="40" value="<?php echo (isset($_POST['url'])?htmlspecialchars($_POST['url']):''); ?>"></span>
						</td>
					</tr>
					<tr>
						<td><b>Biography:</b></td>
						<td>
							<span class="profile">
								<textarea name="biography" rows="4" cols="40"><?php echo (isset($_POST['biography'])?htmlspecialchars($_POST['biography']):''); ?></textarea>
							</span>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span class="profile">
								<input type="checkbox" name="ispublic" value="1" <?php echo ((isset($_POST['ispublic']))?'CHECKED':''); ?> /> Public can view email and bio within website (e.g. photographer listing)
							</span>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div style="margin:10px;">
                                <canvas id="captchaCanvas"></canvas>
                            </div>
						</td>
					</tr>
                    <tr>
                        <td><span style="font-weight:bold;">Enter number in box above:</span></td>
                        <td>
                            <span class="profile"><input type="text" onkeyup="verifyUserInput()" id="human-entry" maxlength="45"></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="float:right;margin:20px;">
                                <input type="submit" id="submitButton" value="Create Login" name="submit" disabled />
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
		</form>
	</fieldset>
	</div>
	<?php
	include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
