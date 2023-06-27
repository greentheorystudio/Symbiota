<div id="topNavigation">
    <template v-if="windowWidth < 1440">
        <q-toolbar class="q-pa-md justify-start horizontalDropDown">
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
        </q-toolbar>
    </template>
</div>
<script>
    const navBarData = [
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
                {url: CLIENT_ROOT + '/tutorial/spatial/index.php', label: 'Map Search Tutorial', newTab: true},
                {url: CLIENT_ROOT + '/collections/index.php', label: 'Text Occurrence Search'}
            ]
        },
        {url: CLIENT_ROOT + '/misc/Protect-IRL.php', label: 'Stewardship'},
        {url: CLIENT_ROOT + '/misc/tour.php', label: 'Take a Tour'}
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
