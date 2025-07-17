<?php
include_once(__DIR__ . '/services/SanitizerService.php');
?>
<div id="footer" class="footer-container">
    <div class="q-pa-md full-width row justify-between">
        <div class="col-grow column">
            <div class="full-width q-pt-xs q-pb-sm q-pl-sm q-pr-lg">
                <taxa-quick-search></taxa-quick-search>
            </div>
            <div class="row justify-center q-col-gutter-sm">
                <div class="col-6">
                    <q-card flat bordered class="full-width black-border bg-white">
                        <q-card-section class="q-pa-xs">
                            <a href="https://naturalhistory.si.edu/research/smithsonian-marine-station" target="_blank">
                                <q-img height="100px" fit="contain" :src="(clientRoot + '/content/imglib/layout/Smithsonian-Logo.png')" alt="Smithsonian home"></q-img>
                            </a>
                        </q-card-section>
                    </q-card>
                </div>
                <div class="col-6">
                    <q-card flat bordered class="full-width black-border bg-white">
                        <q-card-section class="q-pa-xs">
                            <a href="https://onelagoon.org/" target="_blank">
                                <q-img height="100px" fit="contain" :src="(clientRoot + '/content/imglib/layout/one_lagoon_logo.png')" alt="One Lagoon home"></q-img>
                            </a>
                        </q-card-section>
                    </q-card>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 row justify-end q-col-gutter-md">
            <div class="col-12 col-sm-4 column q-gutter-md">
                <div>
                    <a class="text-white login-link text-h6" :href="(clientRoot + '/misc/Maps.php')">
                        The Indian River Lagoon
                    </a>
                </div>
                <div>
                    <a class="text-white login-link text-h6" :href="(clientRoot + '/misc/Whatsa_Habitat.php')">
                        Habitats
                    </a>
                </div>
                <div>
                    <a class="text-white login-link text-h6" :href="(clientRoot + '/misc/Habitat_Threats.php')">
                        Threats
                    </a>
                </div>
                <div>
                    <a class="text-white login-link text-h6" :href="(clientRoot + '/misc/Total_Biodiv.php')">
                        Biodiversity
                    </a>
                </div>
                <div>
                    <a class="text-white login-link text-h6" :href="(clientRoot + '/misc/Protect-IRL.php')">
                        Stewardship
                    </a>
                </div>
                <div>
                    <a class="text-white login-link text-h6" :href="(clientRoot + '/misc/tour.php')">
                        Take a Tour
                    </a>
                </div>
            </div>
            <div class="col-12 col-sm-4 column q-gutter-md">
                <div>
                    <a class="text-white login-link text-h6" :href="(clientRoot + '/index.php')">
                        Home
                    </a>
                </div>
                <div>
                    <a class="text-white login-link text-h6" :href="(clientRoot + '/misc/contact.php')">
                        Contact Us
                    </a>
                </div>
                <div v-if="userDisplayName">
                    <a class="text-white login-link text-h6" :href="(clientRoot + '/profile/viewprofile.php')">
                        My Profile
                    </a>
                </div>
                <div>
                    <a class="text-white login-link text-h6" :href="(clientRoot + '/sitemap.php')">
                        Sitemap
                    </a>
                </div>
                <div v-if="userDisplayName" class="cursor-pointer">
                    <a class="text-white login-link text-h6" @click="logout();">
                        Logout
                    </a>
                </div>
                <div v-else>
                    <a class="text-white login-link text-h6" href="<?php echo $GLOBALS['CLIENT_ROOT']. '/profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true); ?>">
                        Login
                    </a>
                </div>
                <div v-else>
                    <a class="text-white login-link text-h6" href="https://www.si.edu/termsofuse">
                        Terms of Use
                    </a>
                </div>
                <div v-else>
                    <a class="text-white login-link text-h6" href="https://www.si.edu/privacy">
                        Privacy Statement
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
    const footerElement = Vue.createApp({
        components: {
            'taxa-quick-search': taxaQuickSearch
        },
        setup() {
            const store = useBaseStore();

            const clientRoot = store.getClientRoot;
            const donateConfirm = Vue.ref(false);
            const storeRefs = Pinia.storeToRefs(store);
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

            return {
                clientRoot,
                donateConfirm,
                userDisplayName,
                logout,
                openDonatePage
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


