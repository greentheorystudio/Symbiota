const useChecklistStore = Pinia.defineStore('checklist', {
    state: () => ({
        blankChecklistRecord: {
            clid: 0,
            name: null,
            title: null,
            locality: null,
            publication: null,
            abstract: null,
            authors: null,
            type: null,
            politicaldivision: null,
            searchterms: null,
            parent: null,
            parentclid: null,
            notes: null,
            latcentroid: null,
            longcentroid: null,
            pointradiusmeters: null,
            footprintwkt: null,
            percenteffort: null,
            access: null,
            defaultsettings: null,
            iconurl: null,
            headerurl: null,
            uid: null,
            sortsequence: null,
            childChecklistArr: []
        },
        checklistData: {},
        checklistEditData: {},
        checklistId: 0,
        checklistTaxaStore: useChecklistTaxaStore(),
        checklistUpdateData: {},
        checklistVoucherArr: []
    }),
    getters: {
        getBlankChecklistRecord(state) {
            return state.blankChecklistRecord;
        },
        getChecklistData(state) {
            return state.checklistEditData;
        },
        getChecklistEditsExist(state) {
            let exist = false;
            state.checklistUpdateData = Object.assign({}, {});
            for(let key in state.checklistEditData) {
                if(state.checklistEditData.hasOwnProperty(key) && state.checklistEditData[key] !== state.checklistData[key]) {
                    exist = true;
                    state.checklistUpdateData[key] = state.checklistEditData[key];
                }
            }
            return exist;
        },
        getChecklistID(state) {
            return state.checklistId;
        },
        getChecklistTaxaArr(state) {
            return state.checklistTaxaStore.getChecklistTaxaArr;
        },
        getChecklistValid(state) {
            return !!state.checklistEditData['name'];
        },
        getChecklistVoucherArr(state) {
            return state.checklistVoucherArr;
        }
    },
    actions: {
        clearChecklistData() {
            this.checklistData = Object.assign({}, this.blankChecklistRecord);
            this.checklistTaxaStore.clearChecklistTaxaArr();
            this.checklistVoucherArr.length = 0;
        },
        createChecklistRecord(callback) {
            const formData = new FormData();
            formData.append('checklist', JSON.stringify(this.checklistEditData));
            formData.append('action', 'createChecklistRecord');
            fetch(checklistApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) > 0){
                    this.setChecklist(Number(res));
                }
            });
        },
        createTemporaryChecklistFromTidArr(tidArr, callback) {
            const formData = new FormData();
            formData.append('tidArr', JSON.stringify(tidArr));
            formData.append('action', 'createTemporaryChecklistFromTidArr');
            fetch(checklistApiUrl, {
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
        deleteChecklistRecord(clid, callback) {
            const formData = new FormData();
            formData.append('clid', clid.toString());
            formData.append('action', 'deleteChecklistRecord');
            fetch(checklistApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                this.setChecklist(0);
                callback(Number(res));
            });
        },
        getChecklistListByUid(uid, callback) {
            const formData = new FormData();
            formData.append('uid', uid.toString());
            formData.append('action', 'getChecklistListByUid');
            fetch(checklistApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                callback(resObj);
            });
        },
        saveTemporaryChecklist(searchTermsJson, callback) {
            const formData = new FormData();
            formData.append('clid', this.checklistId.toString());
            formData.append('searchTermsJson', searchTermsJson);
            formData.append('action', 'saveTemporaryChecklist');
            fetch(checklistApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.setChecklist(this.checklistId);
                }
            });
        },
        setChecklist(clid, callback = null) {
            this.clearChecklistData();
            if(Number(clid) > 0){
                this.checklistEditData = Object.assign({}, {});
                const formData = new FormData();
                formData.append('clid', clid.toString());
                formData.append('action', 'getChecklistData');
                fetch(checklistApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((resObj) => {
                    if(resObj.hasOwnProperty('clid') && Number(resObj['clid']) === Number(clid)){
                        this.checklistId = Number(clid);
                        this.checklistData = Object.assign({}, resObj);
                        this.checklistEditData = Object.assign({}, this.checklistData);
                    }
                    if(callback){
                        callback(this.checklistId);
                    }
                });
            }
            else{
                this.checklistEditData = Object.assign({}, this.checklistData);
                if(callback){
                    callback();
                }
            }
        },
        setChecklistTaxaArr(clid, includeKeyData, callback = null) {
            this.checklistTaxaStore.setChecklistTaxaArr(clid, includeKeyData, callback);
        },
        updateChecklistEditData(key, value) {
            this.checklistEditData[key] = value;
        },
        updateChecklistRecord(callback) {
            const formData = new FormData();
            formData.append('clid', this.checklistId.toString());
            formData.append('checklistData', JSON.stringify(this.checklistUpdateData));
            formData.append('action', 'updateChecklistRecord');
            fetch(checklistApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.checklistData = Object.assign({}, this.checklistEditData);
                }
            });
        }
    }
});
