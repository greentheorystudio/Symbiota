<?php
include_once(__DIR__ . '/services/SanitizerService.php');
?>
<div id="appContainer">
    <div id="bannerContainer">
        <div style="float:right;margin-top:20px;">
            <img src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/calIBIS-logo.png" />
        </div>
        <div id="imageCredit" style="position:absolute;bottom:5px;right:5px;">
            <div style="background-color:white;opacity:60%;color:black;padding:5px;font-size: 12px;">
                (photographer: Denise Knapp)
            </div>
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
                                        <q-item-section>{{ item.label }}</q-item-section>
                                        <q-menu v-model="navBarToggle[item.id]" transition-duration="100" anchor="top end" self="top start">
                                            <q-list dense @mouseover="navbarToggleOn(item.id)" @mouseleave="navbarToggleOff(item.id)">
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
                        <q-btn class="horizontalDropDownButton text-capitalize" :href="item.url" :target="(item.newTab?'_blank':'_self')" :label="item.label" v-model="navBarToggle[item.id]" @mouseover="navbarToggleOn(item.id)" @mouseleave="navbarToggleOff(item.id)" stretch flat no-wrap>
                            <q-menu v-model="navBarToggle[item.id]" transition-duration="100" anchor="bottom start" self="top start" square>
                                <q-list dense @mouseover="navbarToggleOn(item.id)" @mouseleave="navbarToggleOff(item.id)">
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
                <q-btn class="horizontalDropDownButton text-capitalize" @click="logout();" label="Logout" stretch flat no-wrap></q-btn>
            </template>
            <template v-else>
                <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT'] . '/profile/index.php?refurl=' . SanitizerService::getCleanedRequestPath(true); ?>" label="Log In" stretch flat no-wrap></q-btn>
                <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/newprofile.php" label="New Account" stretch flat no-wrap></q-btn>
            </template>
            <q-btn class="horizontalDropDownButton text-capitalize" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitemap.php" label="Sitemap" stretch flat no-wrap></q-btn>
        </q-toolbar>
    </div>
    <script>
        const navBarData = [
            {url: CLIENT_ROOT + '/index.php', label: 'Home'},
            {url: CLIENT_ROOT + '/collections/list.php', label: 'Search Collections'},
            {url: CLIENT_ROOT + '/spatial/index.php', label: 'Spatial Module', newTab: true},
            {url: CLIENT_ROOT + '/imagelib/search.php', label: 'Image Search'},
            {url: CLIENT_ROOT + '/imagelib/index.php', label: 'Browse Images'},
            {
                label: 'Interactive Tools',
                subItems: [
                    {url: CLIENT_ROOT + '/checklists/checklist.php', label: 'Dynamic Checklist'},
                    {url: CLIENT_ROOT + '/ident/key.php', label: 'Dynamic Key'}
                ]
            }
        ];

        document.addEventListener("DOMContentLoaded", function() {
            const dropDownNavBar = Vue.createApp({
                data() {
                    return {
                        curIndex: Vue.ref(0),
                        imgArray: Vue.ref([
                            "url(/content/imglib/layout/Image01.JPG)",
                            "url(/content/imglib/layout/Image02.JPG)",
                            "url(/content/imglib/layout/Image03.jpg)",
                            "url(/content/imglib/layout/Image05.JPG)",
                            "url(/content/imglib/layout/Image07.jpg)",
                            "url(/content/imglib/layout/Image08.jpg)",
                            "url(/content/imglib/layout/Image09.jpg)",
                            "url(/content/imglib/layout/Image10.jpg)",
                            "url(/content/imglib/layout/Image11.jpg)",
                            "url(/content/imglib/layout/Image12.jpg)"
                        ]),
                        imgDuration: Vue.ref(4000),
                        navBarData: navBarData,
                        navBarToggle: Vue.ref({}),
                        photographerArray: Vue.ref([
                            "Denise Knapp",
                            "Denise Knapp",
                            "",
                            "",
                            "Morgan Ball",
                            "Morgan Ball",
                            "Morgan Ball",
                            "Morgan Ball",
                            "Morgan Ball",
                            "Morgan Ball"
                        ]),
                        userDisplayName: USER_DISPLAY_NAME,
                        windowWidth: Vue.ref(0)
                    }
                },
                mounted() {
                    this.setNavBarData();
                    window.addEventListener('resize', this.handleResize);
                    this.handleResize();
                    this.slideShow();
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
                    },
                    slideShow() {
                        setTimeout(() => {
                            document.getElementById('bannerContainer').style.backgroundImage = this.imgArray[this.curIndex];
                            if(this.photographerArray[this.curIndex] !== ""){
                                document.getElementById('imageCredit').innerHTML = '<div style="background-color:white;opacity:60%;color:black;padding:5px;font-size: 12px;">(photographer: ' + this.photographerArray[this.curIndex] + ')</div>';
                            }
                            else{
                                document.getElementById('imageCredit').innerHTML = '';
                            }
                        },1000);
                        this.curIndex++;
                        if(this.curIndex === this.imgArray.length) {
                            this.curIndex = 0;
                        }
                        setTimeout(this.slideShow, this.imgDuration);
                    }
                }
            });
            dropDownNavBar.use(Quasar, { config: {} });
            dropDownNavBar.mount('#topNavigation');
        });
    </script>
