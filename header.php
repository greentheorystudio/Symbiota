<?php
include_once(__DIR__ . '/services/SanitizerService.php');
?>
<div id="appContainer">
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
                <q-btn class="horizontalDropDownIconButton q-ml-md" flat round dense icon="menu" aria-label="Menu">
                    <q-menu>
                        <q-list dense>
                            <template v-for="item in navBarData">
                                <template v-if="item.subItems && item.subItems.length">
                                    <q-item clickable>
                                        <q-item-section>{{ item.label }}</q-item-section>
                                        <q-menu v-model="navBarToggle[item.id]" transition-duration="100" anchor="top end" self="top start">
                                            <q-list dense>
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
                <q-btn class="horizontalDropDownButton text-capitalize" :href="clientRoot + '/profile/viewprofile.php'" label="My Profile" stretch flat no-wrap></q-btn>
                <q-btn class="horizontalDropDownButton text-capitalize" @click="logout();" label="Logout" stretch flat no-wrap></q-btn>
            </template>
            <template v-else>
                <q-btn class="horizontalDropDownButton text-capitalize" :href="clientRoot + '/profile/index.php?refurl=' + requestPath" label="Log In" stretch flat no-wrap></q-btn>
                <q-btn class="horizontalDropDownButton text-capitalize" :href="clientRoot + '/profile/newprofile.php'" label="New Account" stretch flat no-wrap></q-btn>
            </template>
            <q-btn class="horizontalDropDownButton text-capitalize" :href="clientRoot + '/sitemap.php'" label="Sitemap" stretch flat no-wrap></q-btn>
        </q-toolbar>
    </div>
    <script type="text/javascript">
        const REQUEST_PATH = "<?php echo SanitizerService::getCleanedRequestPath(true); ?>";
        document.addEventListener("DOMContentLoaded", () => {
            const dropDownNavBar = Vue.createApp({
                setup() {
                    const store = useBaseStore();
                    const storeRefs = Pinia.storeToRefs(store);
                    const clientRoot = store.getClientRoot;
                    const navBarData = Vue.ref([
                        {url: CLIENT_ROOT + '/index.php', label: 'Home'},
                        {url: CLIENT_ROOT + '/misc/project.php', label: 'The Project'},
                        {label: 'Trees'},
                        {url: CLIENT_ROOT + '/collections/list.php', label: 'Specimen Search'},
                        {url: CLIENT_ROOT + '/media/search.php', label: 'Image Search'},
                        {url: CLIENT_ROOT + '/spatial/index.php', label: 'Map Search', newTab: true},
                        {
                            label: 'Interactive Tools',
                            subItems: [
                                {url: CLIENT_ROOT + '/checklists/checklist.php', label: 'Dynamic Checklist'},
                                {url: CLIENT_ROOT + '/ident/key.php', label: 'Dynamic Key'}
                            ]
                        }
                    ]);
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
