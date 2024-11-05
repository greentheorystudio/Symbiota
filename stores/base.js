const useBaseStore = Pinia.defineStore('base', {
    state: () => ({
        adminEmail: ADMIN_EMAIL,
        clientRoot: CLIENT_ROOT,
        defaultCollectionCategoryId: DEFAULT_COLLECTION_CATEGORY_ID,
        defaultLanguage: DEFAULT_LANG,
        defaultTitle: DEFAULT_TITLE,
        emailConfigured: EMAIL_CONFIGURED,
        imageTagOptions: IMAGE_TAG_OPTIONS,
        maxUploadFilesize: MAX_UPLOAD_FILESIZE,
        occurrenceProcessingStatusOptions: PROCESSING_STATUS_OPTIONS,
        rightsTerms: RIGHTS_TERMS,
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
        getImageTagOptions(state) {
            return state.imageTagOptions;
        },
        getMaxUploadFilesize(state) {
            return state.maxUploadFilesize;
        },
        getOccurrenceProcessingStatusOptions(state) {
            return state.occurrenceProcessingStatusOptions;
        },
        getSolrMode(state) {
            return state.solrMode;
        },
        getRightsTerms(state) {
            return state.rightsTerms;
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
        getGlobalJsonConfigValue(prop, callback) {
            const formData = new FormData();
            formData.append('action', 'getGlobalConfigValue');
            formData.append('prop', prop);
            fetch(configurationsApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => response.json())
            .then((data) => {
                callback(data ? data : null);
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
