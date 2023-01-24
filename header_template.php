<?php
include_once(__DIR__ . '/classes/Sanitizer.php');
?>
<div id="mainContainer">
    <div id="bannerContainer">
        <h1 class="title">Your New Portal</h1>
    </div>
    <div id="topNavigation">
        <q-toolbar class="q-pa-md horizontalDropDown">
            <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" label="Home" stretch flat no-wrap></q-btn>
            <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/index.php" label="Search Collections" stretch flat no-wrap></q-btn>
            <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" target="_blank" label="Spatial Module" stretch flat no-wrap></q-btn>
            <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/search.php" label="Image Search" stretch flat no-wrap></q-btn>
            <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/index.php" label="Browse Images" stretch flat no-wrap></q-btn>
            <q-btn stretch flat no-wrap label="Inventories" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?" class="horizontalDropDownButton text-capitalize" v-model="inventories" @mouseover="inventories = true" @mouseleave="inventories = false">
                <q-menu v-model="inventories" transition-duration="750" square fit>
                    <q-list @mouseover="inventories = true" @mouseleave="inventories = false">
                        <q-item class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?pid=1" clickable v-close-popup>
                            <q-item-section>
                                <q-item-label>Project 1</q-item-label>
                            </q-item-section>
                        </q-item>
                        <q-item class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?pid=2" clickable v-close-popup>
                            <q-item-section>
                                <q-item-label>Project 2</q-item-label>
                            </q-item-section>
                        </q-item>
                        <q-item class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?pid=3" clickable v-close-popup>
                            <q-item-section>
                                <q-item-label>Project 3</q-item-label>
                            </q-item-section>
                        </q-item>
                        <q-item class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?pid=4" clickable v-close-popup>
                            <q-item-section>
                                <q-item-label>Project 4</q-item-label>
                            </q-item-section>
                        </q-item>
                    </q-list>
                </q-menu>
            </q-btn>
            <q-btn stretch flat no-wrap label="Interactive Tools" class="horizontalDropDownButton text-capitalize" v-model="tools" @mouseover="tools = true" @mouseleave="tools = false">
                <q-menu v-model="tools" transition-duration="750" square fit>
                    <q-list @mouseover="tools = true" @mouseleave="tools = false">
                        <q-item class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=checklist&tid=1" clickable v-close-popup>
                            <q-item-section>
                                <q-item-label>Dynamic Checklist 1</q-item-label>
                            </q-item-section>
                        </q-item>
                        <q-item class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=checklist&tid=2" clickable v-close-popup>
                            <q-item-section>
                                <q-item-label>Dynamic Checklist 2</q-item-label>
                            </q-item-section>
                        </q-item>
                        <q-item class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=checklist&tid=3" clickable v-close-popup>
                            <q-item-section>
                                <q-item-label>Dynamic Checklist 3</q-item-label>
                            </q-item-section>
                        </q-item>
                        <q-item class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=checklist&tid=4" clickable v-close-popup>
                            <q-item-section>
                                <q-item-label>Dynamic Checklist 4</q-item-label>
                            </q-item-section>
                        </q-item>
                    </q-list>
                </q-menu>
            </q-btn>
            <q-space></q-space>
            <template v-if="userDisplayName">
                <q-breadcrumbs-el class="header-username-text">Welcome {{ userDisplayName }}!</q-breadcrumbs-el>
                <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/viewprofile.php" label="My Profile" stretch flat no-wrap></q-btn>
                <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/index.php?submit=logout" label="Logout" stretch flat no-wrap></q-btn>
            </template>
            <template v-else>
                <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']. '/profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true); ?>" label="Log In" stretch flat no-wrap></q-btn>
                <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/newprofile.php" label="New Account" stretch flat no-wrap></q-btn>
            </template>
            <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitemap.php" label="Sitemap" stretch flat no-wrap></q-btn>
        </q-toolbar>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dropDownNavBar = Vue.createApp({
                data() {
                    return {
                        userDisplayName: USER_DISPLAY_NAME,
                        inventories: false,
                        tools: false
                    }
                }
            });
            dropDownNavBar.use(Quasar, { config: {} });
            dropDownNavBar.mount('#topNavigation');
        });
    </script>
