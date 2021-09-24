<?php
include_once(__DIR__ . '/classes/Sanitizer.php');
?>
<div>
    <a class="login-link" href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/contact.php'>Contact Us</a>
</div>
<?php
if($GLOBALS['USER_DISPLAY_NAME']){
    ?>
    <div class="login-link">
        Welcome <?php echo $GLOBALS['USER_DISPLAY_NAME']; ?>!
    </div>
    <div>
        <a class="login-link" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/viewprofile.php">My Profile</a>
    </div>
    <div>
        <a class="login-link" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/index.php?submit=logout">Logout</a>
    </div>
    <?php
}
else{
    ?>
    <div>
        <a class="login-link" href="<?php echo $GLOBALS['CLIENT_ROOT']. '/profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true); ?>">
            Log In
        </a>
    </div>
    <div>
        <a class="login-link" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/newprofile.php">
            New Account
        </a>
    </div>
    <?php
}
?>
<div>
    <a class="login-link" href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitemap.php'>Sitemap</a>
</div>
