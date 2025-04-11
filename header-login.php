<?php
include_once(__DIR__ . '/services/SanitizerService.php');
?>
<div id="loginBar" class="login-bar-wrapper">
    <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/contact.php" class="text-white login-link">Contact Us</a>
    <template v-if="userDisplayName">
        <span class="text-white login-link">Welcome {{ userDisplayName }}!</span>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/viewprofile.php" class="text-white login-link">My Profile</a>
        <a class="text-white cursor-pointer login-link" @click="logout();">Logout</a>
    </template>
    <template v-else>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']. '/profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true); ?>" class="text-white login-link">Login</a>
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/newprofile.php" class="text-white login-link">New Account</a>
    </template>
    <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitemap.php" class="text-white login-link">Sitemap</a>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const loginBar = Vue.createApp({
            data() {
                return {
                    userDisplayName: USER_DISPLAY_NAME
                }
            },
            methods: {
                logout() {
                    const url = profileApiUrl + '?action=logout';
                    fetch(url)
                    .then(() => {
                        window.location.href = CLIENT_ROOT + '/index.php';
                    })
                }
            }
        });
        loginBar.use(Quasar, { config: {} });
        loginBar.mount('#loginBar');
    });
</script>
