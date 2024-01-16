const useBaseStore = Pinia.defineStore('base', {
    state: () => ({
        clientRoot: CLIENT_ROOT,
        springUid: SPRING_UID,
        userDisplayName: USER_DISPLAY_NAME,
        userRights: {}
    }),
    getters: {
        getClientRoot(state) {
            return state.clientRoot
        },
        getSpringUid(state) {
            return state.springUid
        },
        getUserDisplayName(state) {
            return state.userDisplayName
        },
        getUserRights(state) {
            return state.userRights
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
        },
        async setUserRights() {
            const formData = new FormData();
            formData.append('action', 'getCurrentUserRights');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((res) => {
                    this.userRights = Object.assign({}, res);
                });
            });
        }
    }
});
