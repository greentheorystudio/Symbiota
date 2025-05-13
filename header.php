<?php
include_once(__DIR__ . '/services/SanitizerService.php');
?>
<div id="appContainer">
    <div id="bannerContainer">
        <div style="width:850px;margin-left:auto;margin-right:auto;">
            <img src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/wiscflora_banner6.png" />
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
    <script>
        const REQUEST_PATH = "<?php echo SanitizerService::getCleanedRequestPath(true); ?>";
        document.addEventListener("DOMContentLoaded", () => {
            const dropDownNavBar = Vue.createApp({
                setup() {
                    const store = useBaseStore();
                    const storeRefs = Pinia.storeToRefs(store);
                    const clientRoot = store.getClientRoot;
                    const navBarData = Vue.ref([
                        {url: clientRoot + '/index.php', label: 'Home'},
                        {
                            label: 'Advanced Searches',
                            subItems: [
                                {url: clientRoot + '/collections/list.php', label: 'SEARCH for Species & Specimen Records'},
                                {url: clientRoot + '/imagelib/index.php', label: 'BROWSE the Flora & Image Library'},
                                {url: clientRoot + '/spatial/index.php', label: 'Map Search', newTab: true}
                            ]
                        },
                        {
                            label: 'Checklists',
                            subItems: [
                                {url: clientRoot + '/projects/index.php?proj=7', label: 'County Floras'},
                                {url: clientRoot + '/projects/index.php?proj=8', label: 'Wildflowers by Color'},
                                {url: clientRoot + '/projects/index.php?proj=9', label: 'Wildflowers by Month'},
                                {url: clientRoot + '/projects/index.php?proj=11', label: 'Largest Plant Families'},
                                {url: clientRoot + '/projects/index.php?proj=20', label: 'BCW Botany Blitzes'},
                                {url: clientRoot + '/projects/index.php?proj=25', label: 'WIS/BCW Botany Forays'}
                            ]
                        },
                        {
                            label: 'Floristic Projects',
                            subItems: [
                                {url: clientRoot + '/projects/index.php?proj=14', label: 'Brule River State Forest'},
                                {url: clientRoot + '/projects/index.php?proj=15', label: 'Ridgeway Pine Relict SNA'},
                                {url: clientRoot + '/projects/index.php?proj=23', label: 'Amsterdam Sloughs State Wildlife Area'},
                                {url: clientRoot + '/projects/index.php?proj=21', label: 'Crex Meadows State Wildlife Area'},
                                {url: clientRoot + '/projects/index.php?proj=24', label: 'Fish Lake State Wildlife Area'},
                                {url: clientRoot + '/projects/index.php?proj=19', label: 'Navarino Cedar Swamp State Natural Area'}
                            ]
                        },
                        {
                            label: 'Resources',
                            subItems: [
                                {url: clientRoot + '/resources/Keys_pdfs/KEYS_Asteraceae_of_Wisconsin.pdf', label: 'Asteraceae of Wisconsin', newTab: true},
                                {url: 'https://herbarium.wisc.edu/research/publications/', label: 'Atlas of the Wisconsin Prairie and Savanna Flora', newTab: true},
                                {url: clientRoot + '/resources/Keys_pdfs/KEYS_Fern_Allies_of_Wisconsin.pdf', label: 'Fern Allies of Wisconsin', newTab: true},
                                {url: clientRoot + '/resources/Keys_pdfs/KEYS_Ferns_of_Wisconsin.pdf', label: 'Ferns of Wisconsin', newTab: true},
                                {url: clientRoot + '/resources/Keys_pdfs/KEYS_Gymnosperms_of_Wisconsin.pdf', label: 'Gymnosperms of Wisconsin', newTab: true}
                            ]
                        },
                        {
                            label: 'For Further Information',
                            subItems: [
                                {url: clientRoot + '/collections/misc/collstats.php', label: 'About the Consortium of Wisconsin Herbaria'},
                                {url: 'https://herbarium.wisc.edu', label: 'WI State Herbarium', newTab: true},
                                {url: clientRoot + '/misc/links.php', label: 'Links'}
                            ]
                        },
                        {url: clientRoot + '/keys.php', label: 'Keys'},
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
