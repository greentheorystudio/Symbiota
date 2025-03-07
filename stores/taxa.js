const useTaxaStore = Pinia.defineStore('taxa', {
    state: () => ({
        blankTaxaRecord: {
            tid: 0,
            kingdomid: null,
            rankid: null,
            sciname: null,
            unitind1: null,
            unitname1: null,
            unitind2: null,
            unitname2: null,
            unitind3: null,
            unitname3: null,
            author: null,
            tidaccepted: null,
            parenttid: null,
            family: null,
            source: null,
            notes: null,
            hybrid: null,
            securitystatus: null
        },
        taxaData: {},
        taxaEditData: {},
        taxaId: 0,
        taxaUpdateData: {}
    }),
    getters: {
        getTaxaData(state) {
            return state.taxaEditData;
        },
        getTaxaEditsExist(state) {
            let exist = false;
            state.taxaUpdateData = Object.assign({}, {});
            for(let key in state.taxaEditData) {
                if(state.taxaEditData.hasOwnProperty(key) && state.taxaEditData[key] !== state.taxaData[key]) {
                    exist = true;
                    state.taxaUpdateData[key] = state.taxaEditData[key];
                }
            }
            return exist;
        },
        getTaxaID(state) {
            return state.taxaId;
        },
        getTaxaValid(state) {
            return (
                (state.taxaEditData['kingdomid'] && state.taxaEditData['sciname'])
            );
        }
    },
    actions: {
        clearTaxaData() {
            this.taxaData = Object.assign({}, this.blankTaxaRecord);
        },
        createTaxaRecord(callback) {
            const formData = new FormData();
            formData.append('taxon', JSON.stringify(this.taxaEditData));
            formData.append('action', 'addTaxon');
            fetch(taxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) > 0){
                    this.setTaxa(Number(res));
                }
            });
        },
        deleteTaxaRecord(tid, callback) {
            const formData = new FormData();
            formData.append('tid', tid.toString());
            formData.append('action', 'deleteTaxonByTid');
            fetch(taxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                this.setTaxa(0);
                callback(Number(res));
            });
        },
        setTaxa(tid, callback = null) {
            this.clearTaxaData();
            if(Number(tid) > 0){
                this.taxaEditData = Object.assign({}, {});
                this.taxaId = Number(tid);
                const formData = new FormData();
                formData.append('tid', this.taxaId.toString());
                formData.append('action', 'getTaxonFromTid');
                fetch(taxaApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((resObj) => {
                    this.taxaData = Object.assign({}, resObj);
                    this.taxaEditData = Object.assign({}, this.taxaData);
                    if(callback){
                        callback();
                    }
                });
            }
            else{
                this.taxaEditData = Object.assign({}, this.taxaData);
                if(callback){
                    callback();
                }
            }
        },
        updateTaxaEditData(key, value) {
            this.taxaEditData[key] = value;
        },
        updateTaxaRecord(callback) {
            const formData = new FormData();
            formData.append('tid', this.taxaId.toString());
            formData.append('taxonData', JSON.stringify(this.taxaUpdateData));
            formData.append('action', 'editTaxon');
            fetch(taxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.taxaData = Object.assign({}, this.taxaEditData);
                }
            });
        }
    }
});
