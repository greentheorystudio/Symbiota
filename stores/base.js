const useBaseStore = Pinia.defineStore('base', {
    state: () => ({
        adminEmail: ADMIN_EMAIL,
        clientRoot: CLIENT_ROOT,
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
        getUserDisplayName(state) {
            return state.userDisplayName;
        },
        getValidUser(state) {
            return state.validUser;
        }
    },
    actions: {
        async logout() {
            const url = profileApiUrl + '?action=logout';
            fetch(url)
            .then(() => {
                window.location.href = this.clientRoot + '/index.php';
                this.userRights = Object.assign({}, {});
            })
        }
    }
});
