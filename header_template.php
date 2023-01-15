<?php
include_once(__DIR__ . '/classes/Sanitizer.php');
?>
<div id="mainContainer">
    <div id="bannerContainer">
        <h1 class="title">Your New Portal</h1>
    </div>
    <div id="topNavigation">
        <q-toolbar class="q-pa-md horizontalDropDown">
            <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" label="Home" stretch flat></q-btn>
            <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/index.php" label="Search Collections" stretch flat></q-btn>
            <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" target="_blank" label="Spatial Module" stretch flat></q-btn>
            <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/search.php" label="Image Search" stretch flat></q-btn>
            <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/index.php" label="Browse Images" stretch flat></q-btn>
            <q-btn-dropdown stretch flat label="Inventories" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?" class="horizontalDropDownButton text-capitalize" v-model="inventories" @mouseover="inventories = true" @mouseleave="inventories = false">
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
            </q-btn-dropdown>
            <q-btn-dropdown stretch flat label="Interactive Tools" class="horizontalDropDownButton text-capitalize" v-model="tools" @mouseover="tools = true" @mouseleave="tools = false">
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
            </q-btn-dropdown>
            <q-space></q-space>
            <?php
            if($GLOBALS['USER_DISPLAY_NAME']){
                ?>
                <q-breadcrumbs-el class="header-username-text">Welcome <?php echo $GLOBALS['USER_DISPLAY_NAME']; ?>!</q-breadcrumbs-el>
                <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/viewprofile.php" label="My Profile" stretch flat></q-btn>
                <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/index.php?submit=logout" label="Logout" stretch flat></q-btn>
                <?php
            }
            else{
                ?>
                <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']. '/profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true); ?>" label="Log In" stretch flat></q-btn>
                <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/newprofile.php" label="New Account" stretch flat></q-btn>
                <?php
            }
            ?>
            <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitemap.php" label="Sitemap" stretch flat></q-btn>
        </q-toolbar>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dropDownNavBar = Vue.createApp({
                data() {
                    return {
                        inventories: false,
                        tools: false
                    }
                }
            });
            dropDownNavBar.use(Quasar, { config: {} });
            dropDownNavBar.mount('#topNavigation');
        });
    </script>
