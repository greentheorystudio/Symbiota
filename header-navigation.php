<div id="topNavigation">
    <template v-if="windowWidth < 1440">
        <q-toolbar class="q-pa-md justify-start horizontalDropDown">
            <q-btn class="horizontalDropDownIconButton q-ml-md" flat round dense icon="menu">
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
                        <q-item clickable v-close-popup @click="donateConfirm = true">
                            <q-item-section>DONATE</q-item-section>
                        </q-item>
                    </q-list>
                </q-menu>
            </q-btn>
        </q-toolbar>
    </template>
    <template v-if="windowWidth >= 1440">
        <q-toolbar class="q-pa-md justify-center horizontalDropDown">
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
            <q-btn class="horizontalDropDownButton horizontalDropDownDonateButton text-capitalize" label="Donate" stretch flat no-wrap @click="donateConfirm = true"></q-btn>
        </q-toolbar>
    </template>
    <q-dialog v-model="donateConfirm" persistent>
        <q-card class="text-h6">
            <q-card-section>
                You are about to be directed to the donation page for the Smithsonian National Musuem of Natural History, the curator
                of the IRL Species Inventory. To make sure your online donation is applied to this project, please type "IRL Species
                Inventory" in the "Apply my donation to" box. Thank you for your support!
            </q-card-section>
            <q-card-actions align="right">
                <q-btn flat label="OK" color="primary" v-close-popup @click="openDonatePage();"></q-btn>
                <q-btn flat label="Cancel" color="primary" v-close-popup></q-btn>
            </q-card-actions>
        </q-card>
    </q-dialog>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const dropDownNavBar = Vue.createApp({
            setup() {
                const navBarData = Vue.ref([
                    {url: CLIENT_ROOT + '/index.php', label: 'Home'},
                    {url: CLIENT_ROOT + '/misc/Maps.php', label: 'The Indian River Lagoon'},
                    {
                        url: CLIENT_ROOT + '/misc/Whatsa_Habitat.php',
                        label: 'Habitats',
                        subItems: [
                            {url: CLIENT_ROOT + '/misc/Barrierislnd.php', label: 'Barrier Islands'},
                            {url: CLIENT_ROOT + '/misc/Beaches.php', label: 'Beaches'},
                            {url: CLIENT_ROOT + '/misc/Dunes.php', label: 'Dunes'},
                            {url: CLIENT_ROOT + '/misc/Mangroves.php', label: 'Mangroves'},
                            {url: CLIENT_ROOT + '/misc/Hammock_Habitat.php', label: 'Maritime Hammocks'},
                            {url: CLIENT_ROOT + '/misc/Oyster_reef.php', label: 'Oyster Reefs'},
                            {url: CLIENT_ROOT + '/misc/Saltmarsh.php', label: 'Salt Marshes'},
                            {url: CLIENT_ROOT + '/misc/Scrub.php', label: 'Scrub'},
                            {url: CLIENT_ROOT + '/misc/Seagrass_Habitat.php', label: 'Seagrass Beds'},
                            {url: CLIENT_ROOT + '/misc/Tidal_Flats.php', label: 'Tidal Flats'}
                        ]
                    },
                    {
                        url: CLIENT_ROOT + '/misc/Habitat_Threats.php',
                        label: 'Threats',
                        subItems: [
                            {url: CLIENT_ROOT + '/misc/impoundments.php', label: 'Mosquito Impoundments'},
                            {url: CLIENT_ROOT + '/misc/development.php', label: 'Shoreline Development'},
                            {url: CLIENT_ROOT + '/misc/muck-nutrients.php', label: 'Muck & Nutrients'},
                            {url: CLIENT_ROOT + '/misc/invasives.php', label: 'Invasive Species'},
                            {url: CLIENT_ROOT + '/misc/weather.php', label: 'Extreme Weather'},
                            {url: CLIENT_ROOT + '/misc/climate-change.php', label: 'Climate Change'}
                        ]
                    },
                    {
                        url: CLIENT_ROOT + '/misc/Total_Biodiv.php',
                        label: 'Biodiversity',
                        subItems: [
                            {url: CLIENT_ROOT + '/misc/benthic_story.php', label: 'Benthic Monitoring in the IRL'}
                        ]
                    },
                    {
                        label: 'Data Explorer',
                        subItems: [
                            {url: CLIENT_ROOT + '/checklists/index.php', label: 'Checklists'},
                            {url: CLIENT_ROOT + '/checklists/dynamicmap.php?interface=checklist', label: 'Dynamic Checklist'},
                            {url: CLIENT_ROOT + '/spatial/index.php', label: 'Map Occurrence Search', newTab: true},
                            {url: CLIENT_ROOT + '/collections/index.php', label: 'Text Occurrence Search'}
                        ]
                    },
                    {
                        label: 'Tutorials',
                        subItems: [
                            {url: CLIENT_ROOT + '/tutorial/spatial/index.php', label: 'Map Search Tutorial', newTab: true},
                            {url: 'https://youtu.be/YwBC-52j6Ps?si=TAGXz8qev1nvyFCZ', label: 'Checklists Overview', newTab: true},
                            {url: 'https://youtu.be/jm2_mn2nClo?si=kDnHt7EE1ERlSYRB', label: 'Creating a Checklist', newTab: true},
                            {url: 'https://youtu.be/L45s7c19kNw?si=rpNhc_r2OdTOSdk_', label: 'Checklists from Occurrence Records', newTab: true}
                        ]
                    },
                    {url: CLIENT_ROOT + '/misc/Protect-IRL.php', label: 'Stewardship'},
                    {url: CLIENT_ROOT + '/misc/tour.php', label: 'Take a Tour'}
                ]);
                const donateConfirm = Vue.ref(false);
                let navBarTimeout = null;
                const navBarToggle = Vue.ref({});
                const userDisplayName = USER_DISPLAY_NAME;
                const windowWidth = Vue.ref(0);

                function  handleResize() {
                    windowWidth.value = window.innerWidth;
                }

                function logout() {
                    const url = profileApiUrl + '?action=logout';
                    fetch(url)
                        .then(() => {
                            window.location.href = CLIENT_ROOT + '/index.php';
                        })
                }

                function setNavBarData() {
                    navBarData.value.forEach((dataObj, index) => {
                        if(dataObj.hasOwnProperty('subItems')){
                            dataObj['id'] = index;
                            navBarToggle[index] = false;
                        }
                    });
                }

                function openDonatePage() {
                    window.open('https://support.si.edu/site/Donation2;jsessionid=00000000.app30030a?idb=172924536&df_id=19745&mfc_pref=T&19745.donation=form1&NONCE_TOKEN=B8237A09ED48545AB4117EA7BD9F20EF&s_subsrc=top-btn&s_src=main-web&autologin=true&19745_donation=form1', '_blank');
                }

                Vue.onMounted(() => {
                    setNavBarData();
                    window.addEventListener('resize', handleResize);
                    handleResize();
                });

                return {
                    donateConfirm,
                    navBarData,
                    navBarToggle,
                    navBarTimeout,
                    userDisplayName,
                    windowWidth,
                    setNavBarData,
                    handleResize,
                    logout,
                    openDonatePage
                };
            },
            methods: {
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
                }
            }
        });
        dropDownNavBar.use(Quasar, { config: {} });
        dropDownNavBar.mount('#topNavigation');
    });
</script>
