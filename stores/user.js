const useUserStore = Pinia.defineStore('user', {
    state: () => ({
        baseStore: useBaseStore(),
        blankUserRecord: {
            uid: 0,
            firstname: null,
            middleinitial: null,
            lastname: null,
            username: null,
            password: null,
            title: null,
            institution: null,
            department: null,
            address: null,
            city: null,
            state: null,
            zip: null,
            country: null,
            email: null,
            url: null,
            biography: null,
            guid: null,
            validated: null,
            lastlogindate: null
        },
        checklistArr: [],
        checklistStore: useChecklistStore(),
        collectionStore: useCollectionStore(),
        collectionArr: [],
        projectStore: useProjectStore(),
        projectArr: [],
        tokenCnt: 0,
        userData: {},
        userEditData: {},
        userId: 0,
        userUpdateData: {}
    }),
    getters: {
        getChecklistArr(state) {
            return state.checklistArr;
        },
        getCollectionArr(state) {
            return state.collectionArr;
        },
        getProjectArr(state) {
            return state.projectArr;
        },
        getTokenCnt(state) {
            return state.tokenCnt;
        },
        getUserData(state) {
            return state.userEditData;
        },
        getUserEditsExist(state) {
            let exist = false;
            state.userUpdateData = Object.assign({}, {});
            for(let key in state.userEditData) {
                if(state.userEditData.hasOwnProperty(key) && state.userEditData[key] !== state.userData[key]) {
                    exist = true;
                    state.userUpdateData[key] = state.userEditData[key];
                }
            }
            return exist;
        },
        getUserID(state) {
            return state.userId;
        },
        getUserValid(state) {
            return (state.userEditData['firstname']  && state.userEditData['lastname'] && state.userEditData['username'] && state.userEditData['email']);
        }
    },
    actions: {
        clearUserAccessTokens(callback) {
            const formData = new FormData();
            formData.append('uid', this.userId.toString());
            formData.append('action', 'clearAccessTokens');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                this.setUserAccessTokenCnt();
            });
        },
        clearUserData() {
            this.userId = 0;
            this.tokenCnt = 0;
            this.checklistArr.length = 0;
            this.collectionArr.length = 0;
            this.projectArr.length = 0;
            this.userData = Object.assign({}, this.blankUserRecord);
        },
        createUserRecord(callback) {
            const formData = new FormData();
            formData.append('user', JSON.stringify(this.userEditData));
            formData.append('action', 'createAccount');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
            });
        },
        deleteUserRecord(uid, callback) {
            const formData = new FormData();
            formData.append('uid', uid.toString());
            formData.append('action', 'deleteAccount');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(Number(res) === 1){
                    this.setUser(0);
                }
            });
        },
        resendConfirmationEmail(callback) {
            const formData = new FormData();
            formData.append('uid', this.userId.toString());
            formData.append('action', 'sendConfirmationEmail');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
            });
        },
        setUser(uid, callback = null) {
            this.clearUserData();
            if(Number(uid) > 0){
                this.userEditData = Object.assign({}, {});
                this.userId = Number(uid);
                const formData = new FormData();
                formData.append('uid', this.userId.toString());
                formData.append('action', 'getUserByUid');
                fetch(profileApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((resObj) => {
                    this.userData = Object.assign({}, resObj);
                    this.userEditData = Object.assign({}, this.userData);
                    if(Number(this.userId) === Number(this.baseStore.getSymbUid)){
                        this.setUserAccessTokenCnt();
                        this.setUserChecklists();
                        this.setUserCollections();
                        this.setUserProjects();
                    }
                    if(callback){
                        callback();
                    }
                });
            }
            else{
                this.userEditData = Object.assign({}, this.userData);
                if(callback){
                    callback();
                }
            }
        },
        setUserAccessTokenCnt() {
            const formData = new FormData();
            formData.append('uid', this.userId.toString());
            formData.append('action', 'getAccessTokenCnt');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                this.tokenCnt = Number(res);
            });
        },
        setUserChecklists() {
            this.checklistStore.getChecklistListByUid(this.userId, (checklistData) => {
                this.checklistArr = checklistData;
            });
        },
        setUserCollections() {
            this.collectionStore.getCollectionListByUid(this.userId, (collectionData) => {
                this.collectionArr = collectionData;
            });
        },
        setUserProjects() {
            this.projectStore.getProjectListByUid(this.userId, (projectData) => {
                this.projectArr = projectData;
            });
        },
        updateUserEditData(key, value) {
            this.userEditData[key] = value;
        },
        updateUserPassword(newPassword, callback) {
            const formData = new FormData();
            formData.append('uid', this.userId.toString());
            formData.append('pwd', newPassword);
            formData.append('action', 'changePassword');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
            });
        },
        updateUserRecord(callback) {
            const formData = new FormData();
            formData.append('uid', this.userId.toString());
            formData.append('user', JSON.stringify(this.userUpdateData));
            formData.append('action', 'editAccount');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.userData = Object.assign({}, this.userEditData);
                }
            });
        }
    }
});
