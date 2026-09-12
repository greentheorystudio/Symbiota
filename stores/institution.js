const useInstitutionStore = Pinia.defineStore('institution', {
    state: () => ({
        blankInstitutionRecord: {
            iid: 0,
            instituioncode: null,
            countrycode: null,
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
        institutionData: {},
        institutionEditData: {},
        institutionId: 0,
        institutionUpdateData: {}
    }),
    getters: {
        getInstitutionData(state) {
            return state.institutionEditData;
        },
        getInstitutionEditsExist(state) {
            let exist = false;
            state.institutionUpdateData = Object.assign({}, {});
            for(let key in state.institutionEditData) {
                if(state.institutionEditData.hasOwnProperty(key) && state.institutionEditData[key] !== state.institutionData[key]) {
                    exist = true;
                    state.institutionUpdateData[key] = state.institutionEditData[key];
                }
            }
            return exist;
        },
        getInstitutionID(state) {
            return state.institutionId;
        },
        getInstitutionValid(state) {
            return !!state.institutionEditData['institutionname'];
        }
    },
    actions: {
        createInstitutionRecord(callback) {
            const formData = new FormData();
            formData.append('institution', JSON.stringify(this.institutionEditData));
            formData.append('action', 'createInstitutionRecord');
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
        deleteInstitutionRecord(callback) {
            const formData = new FormData();
            formData.append('iid', this.institutionId.toString());
            formData.append('action', 'deleteInstitutionRecord');
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
        setInstitutionData(iid) {
            this.institutionEditData = Object.assign({}, {});
            this.institutionId = Number(iid);
            if(Number(iid) > 0){
                const formData = new FormData();
                formData.append('iid', iid.toString());
                formData.append('action', 'getInstitutionData');
                fetch(institutionsApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    this.institutionData = Object.assign({}, data);
                    this.institutionEditData = Object.assign({}, this.institutionData);
                });
            }
            else{
                this.institutionData = Object.assign({}, this.blankInstitutionRecord);
                this.institutionEditData = Object.assign({}, this.institutionData);
            }
        },
        updateInstitutionEditData(key, value) {
            this.institutionEditData[key] = value;
        },
        updateInstitutionRecord(callback) {
            const formData = new FormData();
            formData.append('iid', this.institutionId.toString());
            formData.append('institutionData', JSON.stringify(this.institutionUpdateData));
            formData.append('action', 'updateInstitutionRecord');
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
                    this.institutionData = Object.assign({}, this.institutionEditData);
                }
            });
        }
    }
});
