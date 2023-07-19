<?php
include_once(__DIR__ . '/classes/Sanitizer.php');
?>
<div id="mainContainer">
    <div id="bannerContainer">
        <div class="bannerTitleContainer">
            <div class="bannerTitle">
                <div>Lomatium & Friends</div>
                <div>Online Monographs</div>
            </div>
        </div>
        <div>
            <img style="height:175px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/Banner3.JPG" />
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
                                    <q-item clickable v-close-popup :href="item.url" :target="(item.newTab?'_blank':'_self')" v-model="navBarToggle[item.id]" @mouseover="navbarToggleOn(item.id)" @mouseleave="navbarToggleOff(item.id)">
                                        <q-item-section class="top-nav-text">{{ item.label }}</q-item-section>
                                        <q-menu v-model="navBarToggle[item.id]" transition-duration="100" anchor="top end" self="top start">
                                            <q-list dense @mouseover="navbarToggleOn(item.id)" @mouseleave="navbarToggleOff(item.id)">
                                                <template v-for="subitem in item.subItems">
                                                    <q-item clickable v-close-popup :href="subitem.url" :target="(subitem.newTab?'_blank':'_self')">
                                                        <q-item-section class="top-nav-text">{{ subitem.label }}</q-item-section>
                                                    </q-item>
                                                </template>
                                            </q-list>
                                        </q-menu>
                                    </q-item>
                                </template>
                                <template v-else>
                                    <q-item clickable v-close-popup :href="item.url" :target="(item.newTab?'_blank':'_self')">
                                        <q-item-section class="top-nav-text">{{ item.label }}</q-item-section>
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
                        <q-btn class="horizontalDropDownButton text-capitalize top-nav-border-right top-nav-text" :href="item.url" :target="(item.newTab?'_blank':'_self')" :label="item.label" v-model="navBarToggle[item.id]" @mouseover="navbarToggleOn(item.id)" @mouseleave="navbarToggleOff(item.id)" stretch flat no-wrap>
                            <q-menu v-model="navBarToggle[item.id]" transition-duration="100" anchor="bottom start" self="top start" square>
                                <q-list class="top-nav-border-top" dense @mouseover="navbarToggleOn(item.id)" @mouseleave="navbarToggleOff(item.id)">
                                    <template v-for="subitem in item.subItems">
                                        <q-item class="horizontalDropDownButton text-capitalize top-nav-border-left top-nav-border-right top-nav-border-bottom" :href="subitem.url" :target="(subitem.newTab?'_blank':'_self')" clickable v-close-popup>
                                            <q-item-section>
                                                <q-item-label class="top-nav-text">{{ subitem.label }}</q-item-label>
                                            </q-item-section>
                                        </q-item>
                                    </template>
                                </q-list>
                            </q-menu>
                        </q-btn>
                    </template>
                    <template v-else>
                        <q-btn class="horizontalDropDownButton text-capitalize top-nav-border-right top-nav-text" :href="item.url" :target="(item.newTab?'_blank':'_self')" :label="item.label" stretch flat no-wrap></q-btn>
                    </template>
                </template>
            </template>
            <q-space></q-space>
            <template v-if="userDisplayName">
                <q-breadcrumbs-el class="header-username-text top-nav-text">Welcome {{ userDisplayName }}!</q-breadcrumbs-el>
                <q-btn class="horizontalDropDownButton text-capitalize top-nav-border-left top-nav-text" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/viewprofile.php" label="My Profile" stretch flat no-wrap></q-btn>
                <q-btn class="horizontalDropDownButton text-capitalize top-nav-border-left top-nav-text" @click="logout();" label="Logout" stretch flat no-wrap></q-btn>
            </template>
            <template v-else>
                <q-btn class="horizontalDropDownButton text-capitalize top-nav-border-left top-nav-text" href="<?php echo $GLOBALS['CLIENT_ROOT']. '/profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true); ?>" label="Log In" stretch flat no-wrap></q-btn>
                <q-btn class="horizontalDropDownButton text-capitalize top-nav-border-left top-nav-text" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/newprofile.php" label="New Account" stretch flat no-wrap></q-btn>
            </template>
            <q-btn class="horizontalDropDownButton text-capitalize top-nav-border-left top-nav-text" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitemap.php" label="Sitemap" stretch flat no-wrap></q-btn>
        </q-toolbar>
    </div>
    <script>
        const navBarData = [
            {url: CLIENT_ROOT + '/index.php', label: 'Home'},
            {url: CLIENT_ROOT + '/misc/project.php', label: 'The Project'},
            {label: 'Trees'},
            {url: CLIENT_ROOT + '/collections/index.php', label: 'Specimen Search'},
            {url: CLIENT_ROOT + '/imagelib/search.php', label: 'Image Search'},
            {url: CLIENT_ROOT + '/spatial/index.php', label: 'Map Search', newTab: true},
            {
                label: 'Interactive Tools',
                subItems: [
                    {url: CLIENT_ROOT + '/checklists/dynamicmap.php?interface=checklist', label: 'Dynamic Checklist'},
                    {url: CLIENT_ROOT + '/checklists/dynamicmap.php?interface=key', label: 'Dynamic Key'}
                ]
            }
        ];

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
                    handleResize() {
                        this.windowWidth = window.innerWidth;
                    },
                    logout() {
                        const url = profileApiUrl + '?action=logout';
                        fetch(url)
                            .then(() => {
                                window.location.href = CLIENT_ROOT + '/index.php';
                            })
                    },
                    navbarToggleOff(id) {
                        this.navBarTimeout = setTimeout(() => {
                            this.navBarToggle[Number(id)] = false;
                        }, 400);
                    },
                    navbarToggleOn(id) {
                        clearTimeout(this.navBarTimeout);
                        for(let i in this.navBarToggle){
                            if(this.navBarToggle.hasOwnProperty(i) && Number(i) !== Number(id)){
                                this.navBarToggle[Number(i)] = false;
                            }
                        }
                        this.navBarToggle[Number(id)] = true;
                    },
                    setNavBarData() {
                        let indexId = 1;
                        this.navBarData.forEach((dataObj) => {
                            if(dataObj.hasOwnProperty('subItems')){
                                dataObj['id'] = indexId;
                                this.navBarToggle[indexId] = false;
                                indexId++;
                            }
                        });
                    }
                }
            });
            dropDownNavBar.use(Quasar, { config: {} });
            dropDownNavBar.mount('#topNavigation');
        });
    </script>
