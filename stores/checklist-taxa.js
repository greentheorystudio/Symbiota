const useChecklistTaxaStore = Pinia.defineStore('checklist-taxa', {
    state: () => ({
        blankChecklistTaxaRecord: {
            cltlid: 0,
            tid: null,
            clid: null,
            habitat: null,
            abundance: null,
            notes: null,
            source: null,
            nativity: null,
            endemic: null,
            invasive: null
        },
        checklistTaxaArr: [],
        checklistTaxaData: {},
        checklistTaxaEditData: {},
        checklistTaxaId: 0,
        checklistTaxaUpdateData: {}
    }),
    getters: {
        getChecklistTaxaArr(state) {
            return state.checklistTaxaArr;
        },
        getChecklistTaxaData(state) {
            return state.checklistTaxaEditData;
        },
        getChecklistTaxaEditsExist(state) {
            let exist = false;
            state.checklistTaxaUpdateData = Object.assign({}, {});
            for(let key in state.checklistTaxaEditData) {
                if(state.checklistTaxaEditData.hasOwnProperty(key) && state.checklistTaxaEditData[key] !== state.checklistTaxaData[key]) {
                    exist = true;
                    state.checklistTaxaUpdateData[key] = state.checklistTaxaEditData[key];
                }
            }
            return exist;
        },
        getChecklistTaxaID(state) {
            return state.checklistTaxaId;
        },
        getChecklistTaxaValid(state) {
            return (
                state.checklistTaxaEditData['tid'] && state.checklistTaxaEditData['clid']
            );
        }
    },
    actions: {
        clearChecklistTaxaArr() {
            this.checklistTaxaArr.length = 0;
        },
        createChecklistTaxaRecord(clid, callback) {
            const formData = new FormData();
            formData.append('clid', clid.toString());
            formData.append('checklistTaxon', JSON.stringify(this.checklistTaxaEditData));
            formData.append('action', 'createChecklistTaxonRecord');
            fetch(checklistTaxaApiUrl, {
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
        deleteChecklistTaxaRecord(clid, callback) {
            const formData = new FormData();
            formData.append('clid', clid.toString());
            formData.append('cltlid', this.checklistTaxaId.toString());
            formData.append('action', 'deleteChecklistTaxonRecord');
            fetch(checklistTaxaApiUrl, {
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
        getCurrentChecklistTaxaData() {
            return this.checklistTaxaArr.find(taxon => Number(taxon.cltlid) === this.checklistTaxaId);
        },
        setCurrentChecklistTaxaRecord(cltlid) {
            this.checklistTaxaId = Number(cltlid);
            if(this.checklistTaxaId > 0){
                this.checklistTaxaData = Object.assign({}, this.getCurrentChecklistTaxaData());
            }
            else{
                this.checklistTaxaData = Object.assign({}, this.blankChecklistTaxaRecord);
            }
            this.checklistTaxaEditData = Object.assign({}, this.checklistTaxaData);
        },
        setChecklistTaxaArr(clid, includeKeyData, callback) {
            let clidArr;
            if(Array.isArray(clid)){
                clidArr = clid.slice();
            }
            else{
                clidArr = [clid];
            }
            const formData = new FormData();
            formData.append('clidArr', JSON.stringify(clidArr));
            formData.append('includeKeyData', (includeKeyData ? '1' : '0'));
            formData.append('action', 'getChecklistTaxa');
            fetch(checklistTaxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.checklistTaxaArr = data;
                if(callback){
                    callback();
                }
            });
        },
        updateChecklistTaxaEditData(key, value) {
            this.checklistTaxaEditData[key] = value;
        },
        updateChecklistTaxaRecord(clid, callback) {
            const formData = new FormData();
            formData.append('clid', clid.toString());
            formData.append('cltlid', this.checklistTaxaId.toString());
            formData.append('checklistTaxonData', JSON.stringify(this.checklistTaxaUpdateData));
            formData.append('action', 'updateChecklistTaxonRecord');
            fetch(checklistTaxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.checklistTaxaData = Object.assign({}, this.checklistTaxaEditData);
                }
            });
        }
    }
});
