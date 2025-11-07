const useTaxaDescriptionStatementStore = Pinia.defineStore('taxa-description-statement', {
    state: () => ({
        blankTaxaDescriptionStatementRecord: {
            tdsid: 0,
            tdbid: null,
            heading: null,
            statement: null,
            displayheader: null,
            notes: null,
            sortsequence: null
        },
        taxaDescriptionStatementArr: {},
        taxaDescriptionStatementData: {},
        taxaDescriptionStatementEditData: {},
        taxaDescriptionStatementId: 0,
        taxaDescriptionStatementUpdateData: {}
    }),
    getters: {
        getTaxaDescriptionStatementArr(state) {
            return state.taxaDescriptionStatementArr;
        },
        getTaxaDescriptionStatementData(state) {
            return state.taxaDescriptionStatementEditData;
        },
        getTaxaDescriptionStatementEditsExist(state) {
            let exist = false;
            state.taxaDescriptionStatementUpdateData = Object.assign({}, {});
            for(let key in state.taxaDescriptionStatementEditData) {
                if(state.taxaDescriptionStatementEditData.hasOwnProperty(key) && state.taxaDescriptionStatementEditData[key] !== state.taxaDescriptionStatementData[key]) {
                    exist = true;
                    state.taxaDescriptionStatementUpdateData[key] = state.taxaDescriptionStatementEditData[key];
                }
            }
            return exist;
        },
        getTaxaDescriptionStatementValid(state) {
            return (state.taxaDescriptionStatementEditData['tdbid'] && state.taxaDescriptionStatementEditData['statement']);
        }
    },
    actions: {
        clearTaxaDescriptionStatementArr() {
            this.taxaDescriptionStatementArr = Object.assign({}, {});
        },
        createTaxaDescriptionStatementRecord(callback) {
            const formData = new FormData();
            formData.append('statement', JSON.stringify(this.taxaDescriptionStatementEditData));
            formData.append('action', 'createTaxonDescriptionStatementRecord');
            fetch(taxonDescriptionStatementApiUrl, {
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
        deleteTaxaDescriptionStatementRecord(callback) {
            const formData = new FormData();
            formData.append('tdsid', this.taxaDescriptionStatementId.toString());
            formData.append('action', 'deleteTaxonDescriptionStatementRecord');
            fetch(taxonDescriptionStatementApiUrl, {
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
        getCurrentTaxaDescriptionStatementData(tdbid) {
            return this.taxaDescriptionStatementArr[tdbid].find(statement => Number(statement.tdsid) === this.taxaDescriptionStatementId);
        },
        setCurrentTaxaDescriptionStatementRecord(tdbid, tdsid) {
            this.taxaDescriptionStatementId = Number(tdsid);
            if(this.taxaDescriptionStatementId > 0){
                this.taxaDescriptionStatementData = Object.assign({}, this.getCurrentTaxaDescriptionStatementData(tdbid));
            }
            else{
                this.taxaDescriptionStatementData = Object.assign({}, this.blankTaxaDescriptionStatementRecord);
            }
            this.taxaDescriptionStatementEditData = Object.assign({}, this.taxaDescriptionStatementData);
        },
        setTaxonDescriptionStatementArr(tid) {
            const formData = new FormData();
            formData.append('tid', tid.toString());
            formData.append('action', 'getTaxonDescriptionStatements');
            fetch(taxonDescriptionStatementApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.taxaDescriptionStatementArr = Object.assign({}, data);
            });
        },
        updateTaxaDescriptionStatementEditData(key, value) {
            this.taxaDescriptionStatementEditData[key] = value;
        },
        updateTaxaDescriptionStatementRecord(callback) {
            const formData = new FormData();
            formData.append('tdsid', this.taxaDescriptionStatementId.toString());
            formData.append('statementData', JSON.stringify(this.taxaDescriptionStatementUpdateData));
            formData.append('action', 'updateTaxonDescriptionStatementRecord');
            fetch(taxonDescriptionStatementApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.taxaDescriptionStatementData = Object.assign({}, this.taxaDescriptionStatementEditData);
                }
            });
        }
    }
});
