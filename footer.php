<?php
include_once(__DIR__ . '/services/SanitizerService.php');
?>
<div id="footer" class="footer-container">
    <div class="q-pa-md full-width row justify-between">
        <div class="col-grow column">
            <div class="full-width q-pt-xs q-pb-sm q-pl-sm q-pr-lg">
                <q-card flat bordered class="black-border bg-grey-3">
                    <q-card-section class="q-pa-sm column">
                        <div class="q-mb-xs row justify-between">
                            <div>
                                <q-btn-toggle v-model="selectedTaxonType" :options="taxonTypeOptions" class="black-border" size="sm" rounded unelevated toggle-color="primary" color="white" text-color="primary" @update:model-value="processTaxonTypeChange"></q-btn-toggle>
                            </div>
                            <div class="row justify-end">
                                <div>
                                    <a class="text-body1 text-bold" :href="(clientRoot + '/taxa/dynamictaxalist.php')">
                                        Advanced Search
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-grow">
                                <taxa-search-auto-complete :label="autoCompleteLabel" :taxon-type="taxonType"></taxa-search-auto-complete>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="row justify-center q-col-gutter-sm">
                <div class="col-6">
                    <q-card flat bordered class="full-width black-border bg-white">
                        <q-card-section class="q-pa-xs">
                            <a href="https://naturalhistory.si.edu/research/smithsonian-marine-station" target="_blank">
                                <q-img height="100px" fit="contain" :src="(clientRoot + '/content/imglib/layout/Smithsonian-Logo.png')"></q-img>
                            </a>
                        </q-card-section>
                    </q-card>
                </div>
                <div class="col-6">
                    <q-card flat bordered class="full-width black-border bg-white">
                        <q-card-section class="q-pa-xs">
                            <a href="https://onelagoon.org/" target="_blank">
                                <q-img height="100px" fit="contain" :src="(clientRoot + '/content/imglib/layout/one_lagoon_logo.png')"></q-img>
                            </a>
                        </q-card-section>
                    </q-card>
                </div>
            </div>
        </div>
        <div class="col-6 row justify-end q-col-gutter-sm">
            <div class="col-3 column">
                <div>
                    <a class="text-white text-h6 text-bold" :href="(clientRoot + '/misc/Maps.php')">
                        The Indian River Lagoon
                    </a>
                </div>
                <div>
                    <a class="text-white text-h6 text-bold" :href="(clientRoot + '/misc/Whatsa_Habitat.php')">
                        Habitats
                    </a>
                </div>
                <div>
                    <a class="text-white text-h6 text-bold" :href="(clientRoot + '/misc/Habitat_Threats.php')">
                        Threats
                    </a>
                </div>
                <div>
                    <a class="text-white text-h6 text-bold" :href="(clientRoot + '/misc/Total_Biodiv.php')">
                        Biodiversity
                    </a>
                </div>
                <div>
                    <a class="text-white text-h6 text-bold" :href="(clientRoot + '/misc/Protect-IRL.php')">
                        Stewardship
                    </a>
                </div>
                <div>
                    <a class="text-white text-h6 text-bold" :href="(clientRoot + '/misc/tour.php')">
                        Take a Tour
                    </a>
                </div>
            </div>
            <div class="col-3 column">
                <div>
                    <a class="text-white text-h6 text-bold" :href="(clientRoot + '/misc/contact.php')">
                        Contact Us
                    </a>
                </div>
                <div v-if="userDisplayName">
                    <a class="text-white text-h6 text-bold" :href="(clientRoot + '/profile/viewprofile.php')">
                        My Profile
                    </a>
                </div>
                <div>
                    <a class="text-white text-h6 text-bold" :href="(clientRoot + '/sitemap.php')">
                        Sitemap
                    </a>
                </div>
                <div v-if="userDisplayName" class="cursor-pointer">
                    <a class="text-white text-h6 text-bold" @click="logout();">
                        Logout
                    </a>
                </div>
                <div v-else>
                    <a class="text-white text-h6 text-bold" href="<?php echo $GLOBALS['CLIENT_ROOT']. '/profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true); ?>">
                        Login
                    </a>
                </div>
            </div>
            <div class="col-2">
                <div>
                    <q-btn class="horizontalDropDownDonateButton" text-color="white" label="Donate" @click="donateConfirm = true" glossy></q-btn>
                </div>
            </div>
        </div>
    </div>
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
    const taxaSearchAutoComplete = {
        props: {
            hideAuthor: {
                type: Boolean,
                default: true
            },
            hideProtected: {
                type: Boolean,
                default: false
            },
            label: {
                type: String,
                default: 'Scientific Name'
            },
            limitToThesaurus: {
                type: Boolean,
                default: false
            },
            optionLimit: {
                type: Number,
                default: 10
            },
            rankHigh: {
                type: Number,
                default: null
            },
            rankLimit: {
                type: Number,
                default: null
            },
            rankLow: {
                type: Number,
                default: null
            },
            taxonType: {
                type: Number,
                default: null
            }
        },
        template: `
            <q-select v-model="selectedTaxon.label" use-input hide-selected fill-input outlined dense options-dense hide-dropdown-icon popup-content-class="z-max" input-debounce="0" bg-color="white" @new-value="createValue" :options="autocompleteOptions" @filter="getOptions" @blur="blurAction" @update:model-value="processChange" :label="label">
                <template v-slot:append>
                    <q-icon name="search" class="cursor-pointer" @click="processSearch();">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Clear value
                        </q-tooltip>
                    </q-icon>
                </template>
            </q-select>
        `,
        setup(props) {
            const { showNotification } = useCore();
            const store = useBaseStore();

            const autocompleteOptions = Vue.ref([]);
            const clientRoot = store.getClientRoot;
            const propsRefs = Vue.toRefs(props);
            const selectedTaxon = Vue.ref({});

            Vue.watch(propsRefs.taxonType, () => {
                selectedTaxon.value = Object.assign({}, {});
            });

            function blurAction(val) {
                if(val && selectedTaxon.value && val.target.value !== selectedTaxon.value.label){
                    const optionObj = autocompleteOptions.value.find(option => option['sciname'] === val.target.value);
                    if(optionObj){
                        processChange(optionObj);
                    }
                    else if(!props.limitToThesaurus){
                        processChange({
                            label: val.target.value,
                            sciname: val.target.value,
                            tid: null,
                            family: null,
                            author: null
                        });
                    }
                    else{
                        showNotification('negative', 'That name was not found in the Taxonomic Thesaurus.');
                    }
                }
            }

            function createValue(val, done) {
                if(val.length > 0) {
                    const optionObj = autocompleteOptions.value.find(option => option['sciname'] === val);
                    if(optionObj){
                        done(optionObj, 'add');
                    }
                    else if(!props.limitToThesaurus){
                        done({
                            label: val,
                            sciname: val,
                            tid: null,
                            family: null,
                            author: null
                        }, 'add');
                    }
                    else{
                        showNotification('negative', 'That name was not found in the Taxonomic Thesaurus.');
                    }
                }
            }

            function getOptions(val, update) {
                update(() => {
                    if(val.length > 2) {
                        let action = 'getAutocompleteSciNameList';
                        let rankLimit, rankLow, rankHigh;
                        let dataSource = taxaApiUrl;
                        if(props.taxonType){
                            if(Number(props.taxonType) === 1){
                                rankLow = 140;
                            }
                            else if(Number(props.taxonType) === 2){
                                rankLimit = 140;
                            }
                            else if(Number(props.taxonType) === 3){
                                rankLow = 180;
                            }
                            else if(Number(props.taxonType) === 4){
                                rankLow = 10;
                                rankHigh = 130;
                            }
                            else if(Number(props.taxonType) === 6){
                                action = 'getAutocompleteVernacularList';
                                dataSource = taxonVernacularApiUrl;
                            }
                        }
                        else{
                            rankLimit = props.rankLimit;
                            rankLow = props.rankLow;
                            rankHigh = props.rankHigh;
                        }
                        const formData = new FormData();
                        formData.append('action', action);
                        formData.append('term', val);
                        formData.append('hideauth', props.hideAuthor);
                        formData.append('hideprotected', props.hideProtected);
                        formData.append('rlimit', rankLimit);
                        formData.append('rlow', rankLow);
                        formData.append('rhigh', rankHigh);
                        formData.append('limit', props.optionLimit);
                        fetch(dataSource, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => response.json())
                        .then((result) => {
                            autocompleteOptions.value = result;
                        });
                    }
                    else{
                        autocompleteOptions.value = [];
                    }
                });
            }

            function processChange(taxonObj) {
                selectedTaxon.value = Object.assign({}, taxonObj);
            }

            function processSearch() {
                if(Number(props.taxonType) === 6){
                    const formData = new FormData();
                    formData.append('vernacular', selectedTaxon.value['label']);
                    formData.append('action', 'getHighestRankingTidByVernacular');
                    fetch(taxonVernacularApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        if(response.status === 200){
                            response.text().then((res) => {
                                if(Number(res) > 0){
                                    window.location.href = (clientRoot + '/taxa/index.php?taxon=' + res);
                                }
                                else{
                                    showNotification('negative', 'That common name was not found in the database');
                                }
                            });
                        }
                    });
                }
                else{
                    if(selectedTaxon.value.hasOwnProperty('tid') && Number(selectedTaxon.value['tid']) > 0){
                        window.location.href = (clientRoot + '/taxa/index.php?taxon=' + selectedTaxon.value['tid']);
                    }
                    else{
                        showNotification('negative', 'That scientific name was not found in the database');
                    }
                }
            }

            return {
                autocompleteOptions,
                selectedTaxon,
                blurAction,
                createValue,
                getOptions,
                processChange,
                processSearch
            }
        }
    };

    const footerElement = Vue.createApp({
        components: {
            'taxa-search-auto-complete': taxaSearchAutoComplete
        },
        setup() {
            const store = useBaseStore();

            const autoCompleteLabel = Vue.ref('Common Name');
            const clientRoot = store.getClientRoot;
            const donateConfirm = Vue.ref(false);
            const selectedTaxonType = Vue.ref('common');
            const storeRefs = Pinia.storeToRefs(store);
            const taxonTypeOptions = [
                {label: 'Common Name', value: 'common'},
                {label: 'Scientific Name', value: 'scientific'}
            ];
            const taxonType = Vue.ref(6);
            const userDisplayName = storeRefs.getUserDisplayName;

            function logout() {
                const url = profileApiUrl + '?action=logout';
                fetch(url)
                .then(() => {
                    window.location.href = clientRoot + '/index.php';
                })
            }

            function openDonatePage() {
                window.open('https://support.si.edu/site/Donation2;jsessionid=00000000.app30030a?idb=172924536&df_id=19745&mfc_pref=T&19745.donation=form1&NONCE_TOKEN=B8237A09ED48545AB4117EA7BD9F20EF&s_subsrc=top-btn&s_src=main-web&autologin=true&19745_donation=form1', '_blank');
            }

            function processTaxonTypeChange(value) {
                if(value === 'common'){
                    autoCompleteLabel.value = 'Common Name';
                    taxonType.value = 6;
                }
                else{
                    autoCompleteLabel.value = 'Scientific Name';
                    taxonType.value = null;
                }
            }

            return {
                autoCompleteLabel,
                clientRoot,
                donateConfirm,
                selectedTaxonType,
                taxonTypeOptions,
                userDisplayName,
                taxonType,
                logout,
                openDonatePage,
                processTaxonTypeChange
            };
        }
    });
    footerElement.use(Quasar, { config: {} });
    footerElement.use(Pinia.createPinia());
    footerElement.mount('#footer');
</script>

<!-- START OF SmartSource Data Collector TAG v10.4.23 -->
<!-- Copyright (c) 2018 Webtrends Inc.  All rights reserved. -->
<script>
    window.webtrendsAsyncInit=function(){
        var dcs=new Webtrends.dcs().init({
            dcsid:"<?php echo $GLOBALS['DCS_ID']; ?>",
            domain:"<?php echo $GLOBALS['DCS_DOMAIN']; ?>",
            timezone:-5,
            i18n:true,
            fpcdom:".irlspecies.org",
            plugins:{
            }
        }).track();
    };
    (function(){
        var s=document.createElement("script"); s.async=true; s.src="https://www.si.edu/assets/webtrends/webtrends.min.js";
        var s2=document.getElementsByTagName("script")[0]; s2.parentNode.insertBefore(s,s2);
    }());
</script>
<noscript><img alt="dcsimg" id="dcsimg" width="1" height="1" src="//logs1.smithsonian.museum/dcsp2e2pf00000sh88n34e5xp_6s5o/njs.gif?dcsuri=/nojavascript&amp;WT.js=No&amp;WT.tv=10.4.23&amp;dcssip=irlspecies.org"/></noscript>
<!-- END OF SmartSource Data Collector TAG v10.4.23 -->
<!-- SITE-SPECIFIC CPP VALUE - PLACE ABOVE EMBED CODE -->
<script type="text/javascript">cpp_value="IRLS";</script>

<script type="text/javascript">
    // ForeSee Production Embed Script v2.00
    // DO NOT MODIFY BELOW THIS LINE *****************************************
    ;(function (g) {
        var d = document, am = d.createElement('script'), h = d.head || d.getElementsByTagName("head")[0], fsr = 'fsReady',
            aex = {
                "src": "//gateway.foresee.com/sites/smithsonian/production/gateway.min.js",
                "type": "text/javascript",
                "async": "true",
                "data-vendor": "fs",
                "data-role": "gateway"
            };
        for (var attr in aex){am.setAttribute(attr, aex[attr]);}h.appendChild(am);g[fsr] = function () {var aT = '__' + fsr + '_stk__';g[aT] = g[aT] || [];g[aT].push(arguments);};
    })(window);
    // DO NOT MODIFY ABOVE THIS LINE *****************************************
</script>


