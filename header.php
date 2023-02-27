<?php
include_once(__DIR__ . '/classes/Sanitizer.php');
?>
<div id="mainContainer">
    <div style="background-image:url(<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/background.jpg);background-repeat:repeat-x;background-position:top;width:100%;clear:both;height:150px;border-bottom:1px solid #333333;">
        <div>
            <img style="" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/BioMNA.jpg"  alt=""/>
        </div>
    </div>
    <div id="topNavigation">
        <q-toolbar class="q-pa-md horizontalDropDown">
            <template v-if="windowWidth < 1440">
                <q-btn class="horizontalDropDownIconButton q-ml-md" flat round dense icon="menu">
                    <q-menu>
                        <q-list dense>
                            <template v-for="item in navBarData">
                                <template v-if="item.subItems && item.subItems.length">
                                    <q-item clickable v-close-popup :href="item.url" :target="(item.newTab?'_blank':'_self')" v-model="navBarToggle[item.id]" @mouseover="navBarToggle[item.id] = true" @mouseleave="navBarToggle[item.id] = false">
                                        <q-item-section>{{ item.label }}</q-item-section>
                                        <q-menu v-model="navBarToggle[item.id]" transition-duration="750" anchor="top end" self="top start">
                                            <q-list dense @mouseover="navBarToggle[item.id] = true" @mouseleave="navBarToggle[item.id] = false">
                                                <template v-for="subitem in item.subItems">
                                                    <q-item clickable v-close-popup :href="subitem.url" :target="(subitem.newTab?'_blank':'_self')">
                                                        <q-item-section>{{ subitem.label }}</q-item-section>
                                                    </q-item>
                                                </template>
                                            </q-list>
                                        </q-menu>
                                    </q-item>
                                </template>
                                <template v-else>
                                    <q-item clickable v-close-popup :href="item.url" :target="(item.newTab?'_blank':'_self')">
                                        <q-item-section>{{ item.label }}</q-item-section>
                                    </q-item>
                                </template>
                            </template>
                        </q-list>
                    </q-menu>
                </q-btn>
            </template>
            <template v-if="windowWidth >= 1440">
                <template v-for="item in navBarData">
                    <template v-if="item.subItems && item.subItems.length">
                        <q-btn class="horizontalDropDownButton text-capitalize" :href="item.url" :target="(item.newTab?'_blank':'_self')" :label="item.label" v-model="navBarToggle[item.id]" @mouseover="navBarToggle[item.id] = true" @mouseleave="navBarToggle[item.id] = false" stretch flat no-wrap>
                            <q-menu v-model="navBarToggle[item.id]" transition-duration="750" anchor="bottom start" self="top start" square>
                                <q-list dense @mouseover="navBarToggle[item.id] = true" @mouseleave="navBarToggle[item.id] = false">
                                    <template v-for="subitem in item.subItems">
                                        <q-item class="horizontalDropDownButton text-capitalize" :href="subitem.url" :target="(subitem.newTab?'_blank':'_self')" clickable v-close-popup>
                                            <q-item-section>
                                                <q-item-label>{{ subitem.label }}</q-item-label>
                                            </q-item-section>
                                        </q-item>
                                    </template>
                                </q-list>
                            </q-menu>
                        </q-btn>
                    </template>
                    <template v-else>
                        <q-btn class="horizontalDropDownButton text-capitalize" :href="item.url" :target="(item.newTab?'_blank':'_self')" :label="item.label" stretch flat no-wrap></q-btn>
                    </template>
                </template>
            </template>
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
        <?php
        if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array(8, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)) || (array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array(8, $GLOBALS['USER_RIGHTS']['CollEditor'], true))){
            ?>
            const navBarData = [
                {url: CLIENT_ROOT + '/index.php', label: 'Home'},
                {
                    label: 'Search',
                    subItems: [
                        {url: CLIENT_ROOT + '/collections/index.php', label: 'Search Collections'},
                        {url: CLIENT_ROOT + '/spatial/index.php', label: 'Spatial Module', newTab: true}
                    ]
                },
                {
                    label: 'Images',
                    subItems: [
                        {url: CLIENT_ROOT + '/imagelib/search.php', label: 'Image Search'},
                        {url: CLIENT_ROOT + '/imagelib/index.php', label: 'Browse Images'}
                    ]
                },
                {url: CLIENT_ROOT + '/collections/misc/collprofiles.php?collid=8&emode=1', label: 'Collection Management'},
            ];
            <?php
        }
        else{
            ?>
            const navBarData = [
                {url: CLIENT_ROOT + '/index.php', label: 'Home'},
                {
                    label: 'Search',
                    subItems: [
                        {url: CLIENT_ROOT + '/collections/index.php', label: 'Search Collections'},
                        {url: CLIENT_ROOT + '/spatial/index.php', label: 'Spatial Module', newTab: true}
                    ]
                },
                {
                    label: 'Images',
                    subItems: [
                        {url: CLIENT_ROOT + '/imagelib/search.php', label: 'Image Search'},
                        {url: CLIENT_ROOT + '/imagelib/index.php', label: 'Browse Images'}
                    ]
                }
            ];
            <?php
        }
        ?>

        document.addEventListener("DOMContentLoaded", function() {
            const dropDownNavBar = Vue.createApp({
                data() {
                    return {
                        windowWidth: Vue.ref(0),
                        userDisplayName: USER_DISPLAY_NAME,
                        navBarData: navBarData,
                        navBarToggle: Vue.ref({})
                    }
                },
                mounted() {
                    this.setNavBarData();
                    window.addEventListener('resize', this.handleResize);
                    this.handleResize();
                },
                methods: {
                    setNavBarData() {
                        let indexId = 1;
                        this.navBarData.forEach((dataObj) => {
                            if(dataObj.hasOwnProperty('subItems')){
                                dataObj['id'] = indexId;
                                this.navBarToggle[indexId] = false;
                                indexId++;
                            }
                        });
                    },
                    handleResize() {
                        this.windowWidth = window.innerWidth;
                    }
                }
            });
            dropDownNavBar.use(Quasar, { config: {} });
            dropDownNavBar.mount('#topNavigation');
        });
    </script>
