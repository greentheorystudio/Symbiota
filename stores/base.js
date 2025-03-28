const useBaseStore = Pinia.defineStore('base', {
    state: () => ({
        activateExsiccati: ACTIVATE_EXSICCATI,
        adminEmail: ADMIN_EMAIL,
        clientRoot: CLIENT_ROOT,
        defaultCollectionCategoryId: DEFAULT_COLLECTION_CATEGORY_ID,
        defaultLanguage: DEFAULT_LANG,
        defaultTitle: DEFAULT_TITLE,
        emailConfigured: EMAIL_CONFIGURED,
        glossaryModuleIsActive: GLOSSARY_MOD_IS_ACTIVE,
        imageTagOptions: IMAGE_TAG_OPTIONS,
        keyModuleIsActive: KEY_MOD_IS_ACTIVE,
        maxUploadFilesize: MAX_UPLOAD_FILESIZE,
        occurrenceProcessingStatusOptions: PROCESSING_STATUS_OPTIONS,
        rightsTerms: RIGHTS_TERMS,
        rssActive: RSS_ACTIVE,
        showPasswordReset: SHOW_PASSWORD_RESET,
        solrMode: SOLR_MODE,
        symbUid: SYMB_UID,
        taxonomicRanks: TAXONOMIC_RANKS,
        taxonomicTags: {
            cahr: 'CA Heritage Rank',
            carpr: 'CA Rare Plant Rank',
            casls: 'CA State Listing Status',
            cnddb: 'CNDDB Element Code',
            col: 'Catalogue of Life ID',
            itis: 'ITIS TSN',
            iucn: 'IUCN Red List Category',
            usda: 'USDA Symbol',
            usfrs: 'US Federal Regulatory Status',
            worms: 'WoRMS Aphia ID'
        },
        userDisplayName: USER_DISPLAY_NAME,
        validUser: VALID_USER
    }),
    getters: {
        getActivateExsiccati(state) {
            return state.activateExsiccati;
        },
        getAdminEmail(state) {
            return state.adminEmail;
        },
        getClientRoot(state) {
            return state.clientRoot;
        },
        getDefaultCollectionCategoryId(state) {
            return state.defaultCollectionCategoryId;
        },
        getDefaultLanguage(state) {
            return state.defaultLanguage;
        },
        getDefaultTitle(state) {
            return state.defaultTitle;
        },
        getEmailConfigured(state) {
            return state.emailConfigured;
        },
        getGlossaryModuleIsActive(state) {
            return state.glossaryModuleIsActive;
        },
        getImageTagOptions(state) {
            return state.imageTagOptions;
        },
        getKeyModuleIsActive(state) {
            return state.keyModuleIsActive;
        },
        getMaxUploadFilesize(state) {
            return state.maxUploadFilesize;
        },
        getOccurrenceProcessingStatusOptions(state) {
            return state.occurrenceProcessingStatusOptions;
        },
        getRightsTerms(state) {
            return state.rightsTerms;
        },
        getRssActive(state) {
            return state.rssActive;
        },
        getShowPasswordReset(state) {
            return state.showPasswordReset;
        },
        getSolrMode(state) {
            return state.solrMode;
        },
        getSymbUid(state) {
            return state.symbUid;
        },
        getTaxonomicRanks(state) {
            return state.taxonomicRanks;
        },
        getTaxonomicTags(state) {
            return state.taxonomicTags;
        },
        getUserDisplayName(state) {
            return state.userDisplayName;
        },
        getValidUser(state) {
            return state.validUser;
        }
    },
    actions: {
        getGlobalConfigValue(prop, callback) {
            const formData = new FormData();
            formData.append('action', 'getGlobalConfigValue');
            formData.append('prop', prop);
            fetch(configurationsApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => response.text())
            .then((resStr) => {
                callback(resStr);
            });
        },
        async logout() {
            const url = profileApiUrl + '?action=logout';
            fetch(url)
            .then(() => {
                window.location.href = this.clientRoot + '/index.php';
            })
        },
    }
});
