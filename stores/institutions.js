const useInstitutionsStore = Pinia.defineStore('institutions', {
    state: () => ({
        blankInstitutionRecord: {
            iid: 0,
            instituioncode: null,
            institutionname: null,
            institutionname2: null,
            address1: null,
            address2: null,
            city: null,
            stateprovince: null,
            postalcode: null,
            country: null,
            phone: null,
            contact: null,
            email: null,
            notes: null,
        },
        institutionsArr: [],
        institutionsData: {},
        institutionsEditData: {},
        institutionsId: 0,
        institutionsUpdateData: {}
    }),
    getters: {
        getInstitutionsArr(state) {
            return state.institutionsArr;
        },
        getInstitutionsData(state) {
            return state.institutionsEditData;
        },
        getInstitutionsEditsExist(state) {
            let exist = false;
            state.institutionsUpdateData = Object.assign({}, {});
            for(let key in state.institutionsEditData) {
                if(state.institutionsEditData.hasOwnProperty(key) && state.institutionsEditData[key] !== state.institutionsData[key]) {
                    exist = true;
                    state.institutionsUpdateData[key] = state.institutionsEditData[key];
                }
            }
            return exist;
        },
        getInstitutionsID(state) {
            return state.institutionsId;
        },
        getInstitutionsValid(state) {
            return !!state.institutionsEditData['institutionname'];
        }
    },
    actions: {
        clearInstitutionsArr() {
            this.institutionsArr.length = 0;
        },
        createInstitutionsRecord(callback) {
            const formData = new FormData();
            formData.append('description', JSON.stringify(this.institutionsEditData));
            formData.append('action', 'createInstitutionsRecord');
            fetch(institutionsApiUrl, {
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
        deleteInstitutionsRecord(callback) {
            const formData = new FormData();
            formData.append('iid', this.institutionsId.toString());
            formData.append('action', 'deleteInstitutionsRecord');
            fetch(institutionsApiUrl, {
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
        getCurrentInstitutionsData() {
            return this.institutionsArr.find(block => Number(block.iid) === this.institutionsId);
        },
        setCurrentInstitutionsRecord(iid) {
            this.institutionsId = Number(iid);
            if(this.institutionsId > 0){
                this.institutionsData = Object.assign({}, this.getCurrentInstitutionsData());
            }
            else{
                this.institutionsData = Object.assign({}, this.blankInstitutionRecord);
            }
            this.institutionsEditData = Object.assign({}, this.institutionsData);
        },
        setInstitutionData(iid) {
            this.institutionsEditData = Object.assign({}, {});
            this.institutionsId = Number(iid);
            if (Number(iid) > 0){
                const formData = new FormData();
                formData.append('iid', iid.toString());
                formData.append('action', 'getInstitutionsData');
                fetch(institutionsApiUrl, {
                    method: 'POST',
                    body: formData
                })
                    .then((response) => {
                        return response.ok ? response.json() : null;
                    })
                    .then((data) => {
                        this.institutionsData = Object.assign({}, data);
                    });
            } else {
                this.institutionsData = Object.assign({}, this.blankInstitutionRecord);
            }
            this.institutionsEditData = Object.assign({}, this.institutionsData);
        },
        updateInstitutionsEditData(key, value) {
            this.institutionsEditData[key] = value;
        },
        updateInstitutionsRecord(callback) {
            const formData = new FormData();
            formData.append('iid', this.institutionsId.toString());
            formData.append('descriptionData', JSON.stringify(this.institutionsUpdateData));
            formData.append('action', 'updateInstitutionsRecord');
            fetch(institutionsApiUrl, {
                method: 'POST',
                body: formData
            })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    callback(Number(res));
                    if(res && Number(res) === 1){
                        this.institutionsData = Object.assign({}, this.institutionsEditData);
                    }
                });
        }
    }
});
