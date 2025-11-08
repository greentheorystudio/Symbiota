<?php
include_once(__DIR__ . '/services/SanitizerService.php');
?>
<div id="appContainer">
    <div style="background-image:url(<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/background.jpg);background-repeat:repeat-x;background-position:top;width:100%;clear:both;height:150px;border-bottom:1px solid #333333;">
        <div>
            <img style="" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/BioMNA.jpg"  alt=""/>
        </div>
    </div>
    <div id="topNavigation">
        <q-toolbar class="q-pa-md horizontalDropDown">
            <template v-if="windowWidth < 1440">
                <q-btn class="horizontalDropDownIconButton q-ml-md" flat round dense icon="menu" aria-label="Open Menu" tabindex="0">
                    <q-menu class="z-max">
                        <q-list dense>
                            <template v-for="item in navBarData">
                                <template v-if="item.subItems && item.subItems.length">
                                    <q-item clickable v-close-popup :href="item.url" :target="(item.newTab?'_blank':'_self')" v-model="navBarToggle[item.id]" @mouseover="navbarToggleOn(item.id)" @mouseleave="navbarToggleOff(item.id)" tabindex="0">
                                        <q-item-section>{{ item.label }}</q-item-section>
                                        <q-menu v-model="navBarToggle[item.id]" transition-duration="100" anchor="top end" self="top start">
                                            <q-list dense @mouseover="navbarToggleOn(item.id)" @mouseleave="navbarToggleOff(item.id)">
                                                <template v-for="subitem in item.subItems">
                                                    <q-item clickable v-close-popup :href="subitem.url" :target="(subitem.newTab ? '_blank' : '_self')" :aria-label="(subitem.newTab ? (subitem.label + ' - opens in separate tab') : null)" tabindex="0">
                                                        <q-item-section>{{ subitem.label }}</q-item-section>
                                                    </q-item>
                                                </template>
                                            </q-list>
                                        </q-menu>
                                    </q-item>
                                </template>
                                <template v-else>
                                    <q-item clickable v-close-popup :href="item.url" :target="(item.newTab ? '_blank' : '_self')" :aria-label="(item.newTab ? (item.label + ' - opens in separate tab') : null)" tabindex="0">
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
                        <q-btn class="horizontalDropDownButton text-capitalize" :href="item.url" :target="(item.newTab ? '_blank' : '_self')" :label="item.label" :aria-label="(item.newTab ? (item.label + ' - opens in separate tab') : null)" v-model="navBarToggle[item.id]" @mouseover="navbarToggleOn(item.id)" @mouseleave="navbarToggleOff(item.id)" stretch flat no-wrap tabindex="0">
                            <q-menu v-model="navBarToggle[item.id]" transition-duration="100" anchor="bottom start" self="top start" square>
                                <q-list dense @mouseover="navbarToggleOn(item.id)" @mouseleave="navbarToggleOff(item.id)">
                                    <template v-for="subitem in item.subItems">
                                        <q-item class="horizontalDropDownButton text-capitalize" :href="subitem.url" :target="(subitem.newTab ? '_blank' : '_self')" :aria-label="(subitem.newTab ? (subitem.label + ' - opens in separate tab') : null)" clickable v-close-popup tabindex="0">
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
                        <q-btn class="horizontalDropDownButton text-capitalize" :href="item.url" :target="(item.newTab ? '_blank' : '_self')" :label="item.label" :aria-label="(item.newTab ? (item.label + ' - opens in separate tab') : null)" stretch flat no-wrap tabindex="0"></q-btn>
                    </template>
                </template>
            </template>
            <q-space></q-space>
            <template v-if="userDisplayName">
                <q-breadcrumbs-el class="header-username-text">Welcome {{ userDisplayName }}!</q-breadcrumbs-el>
                <q-btn class="horizontalDropDownButton text-capitalize" :href="clientRoot + '/profile/viewprofile.php'" label="My Profile" stretch flat no-wrap tabindex="0"></q-btn>
                <q-btn class="horizontalDropDownButton text-capitalize" @click="logout();" label="Logout" stretch flat no-wrap tabindex="0"></q-btn>
            </template>
            <template v-else>
                <q-btn class="horizontalDropDownButton text-capitalize" :href="clientRoot + '/profile/index.php?refurl=' + requestPath" label="Log In" stretch flat no-wrap tabindex="0"></q-btn>
                <q-btn class="horizontalDropDownButton text-capitalize" :href="clientRoot + '/profile/newprofile.php'" label="New Account" stretch flat no-wrap tabindex="0"></q-btn>
            </template>
            <q-btn class="horizontalDropDownButton text-capitalize" :href="clientRoot + '/sitemap.php'" label="Sitemap" aria-label="Site map" stretch flat no-wrap tabindex="0"></q-btn>
        </q-toolbar>
    </div>
    <script>
        <?php
        if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array(8, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)) || (array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array(8, $GLOBALS['USER_RIGHTS']['CollEditor'], true))){
            ?>
            const activeNavBarData = [
                {url: CLIENT_ROOT + '/index.php', label: 'Home'},
                {
                    label: 'Search',
                    subItems: [
                        {url: CLIENT_ROOT + '/collections/list.php', label: 'Search Collections'},
                        {url: CLIENT_ROOT + '/spatial/index.php', label: 'Spatial Module', newTab: true}
                    ]
                },
                {
                    label: 'Images',
                    subItems: [
                        {url: CLIENT_ROOT + '/media/search.php', label: 'Image Search'},
                        {url: CLIENT_ROOT + '/media/index.php', label: 'Browse Images'}
                    ]
                },
                {url: CLIENT_ROOT + '/collections/misc/collprofiles.php?collid=8&emode=1', label: 'Collection Management'},
            ];
            <?php
        }
        else{
            ?>
            const activeNavBarData = [
                {url: CLIENT_ROOT + '/index.php', label: 'Home'},
                {
                    label: 'Search',
                    subItems: [
                        {url: CLIENT_ROOT + '/collections/list.php', label: 'Search Collections'},
                        {url: CLIENT_ROOT + '/spatial/index.php', label: 'Spatial Module', newTab: true}
                    ]
                },
                {
                    label: 'Images',
                    subItems: [
                        {url: CLIENT_ROOT + '/media/search.php', label: 'Image Search'},
                        {url: CLIENT_ROOT + '/media/index.php', label: 'Browse Images'}
                    ]
                }
            ];
            <?php
        }
        ?>
        const REQUEST_PATH = "<?php echo SanitizerService::getCleanedRequestPath(true); ?>";
        document.addEventListener("DOMContentLoaded", function() {
            const dropDownNavBar = Vue.createApp({
                setup() {
                    const store = useBaseStore();
                    const storeRefs = Pinia.storeToRefs(store);
                    const clientRoot = store.getClientRoot;
                    const navBarData = Vue.ref([]);
                    let navBarTimeout = null;
                    const navBarToggle = Vue.ref({});
                    const requestPath = REQUEST_PATH;
                    const userDisplayName = storeRefs.getUserDisplayName;
                    const windowWidth = Vue.ref(0);

                    function  handleResize() {
                        windowWidth.value = window.innerWidth;
                    }

                    function logout() {
                        const url = profileApiUrl + '?action=logout';
                        fetch(url)
                            .then(() => {
                                window.location.href = clientRoot + '/index.php';
                            })
                    }

                    function navbarToggleOff(id) {
                        this.navBarTimeout = setTimeout(() => {
                            this.navBarToggle[Number(id)] = false;
                        }, 400);
                    }

                    function navbarToggleOn(id) {
                        clearTimeout(this.navBarTimeout);
                        for(let i in this.navBarToggle){
                            if(this.navBarToggle.hasOwnProperty(i) && Number(i) !== Number(id)){
                                this.navBarToggle[Number(i)] = false;
                            }
                        }
                        this.navBarToggle[Number(id)] = true;
                    }

                    function setNavBarData() {
                        navBarData.value.forEach((dataObj, index) => {
                            if(dataObj.hasOwnProperty('subItems')){
                                dataObj['id'] = index;
                                navBarToggle[index] = false;
                            }
                        });
                    }

                    Vue.onMounted(() => {
                        navBarData.value = activeNavBarData.slice();
                        setNavBarData();
                        window.addEventListener('resize', handleResize);
                        handleResize();
                    });

                    return {
                        clientRoot,
                        navBarData,
                        navBarToggle,
                        navBarTimeout,
                        requestPath,
                        userDisplayName,
                        windowWidth,
                        navbarToggleOff,
                        navbarToggleOn,
                        setNavBarData,
                        handleResize,
                        logout
                    };
                }
            });
            dropDownNavBar.use(Quasar, { config: {} });
            dropDownNavBar.use(Pinia.createPinia());
            dropDownNavBar.mount('#topNavigation');
        });
    </script>
