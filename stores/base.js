const useBaseStore = Pinia.defineStore('base', {
    state: () => ({
        adminEmail: ADMIN_EMAIL,
        clientRoot: CLIENT_ROOT,
        defaultLanguage: DEFAULT_LANG,
        emailConfigured: EMAIL_CONFIGURED,
        maxUploadFilesize: MAX_UPLOAD_FILESIZE,
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
        getEmailConfigured(state) {
            return state.emailConfigured;
        },
        getMaxUploadFilesize(state) {
            return state.maxUploadFilesize;
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
