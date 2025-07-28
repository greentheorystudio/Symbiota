const useChecklistTaxaStore = Pinia.defineStore('checklist-taxa', {
    state: () => ({
        blankChecklistTaxaRecord: {
            cltlid: 0,
            tid: null,
            sciname: null,
            clid: null,
            habitat: null,
            abundance: null,
            notes: null,
            source: null
        },
        checklistTaxaArr: [],
        checklistTaxaData: {},
        checklistTaxaEditData: {},
        checklistTaxaId: 0,
        checklistTaxaUpdateData: {},
        taxaFilterOptions: [],
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
                !!state.checklistTaxaEditData['tid']
            );
        },
        getTaxaFilterOptions(state) {
            return state.taxaFilterOptions;
        }
    },
    actions: {
        clearChecklistTaxaArr() {
            this.checklistTaxaArr.length = 0;
            this.taxaFilterOptions.length = 0;
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
        deleteChecklistTaxonRecord(clid, callback) {
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
        setChecklistTaxaArr(clid, includeKeyData, includeSynonymyData, includeVernacularData, callback) {
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
            formData.append('includeSynonymyData', (includeSynonymyData ? '1' : '0'));
            formData.append('includeVernacularData', (includeVernacularData ? '1' : '0'));
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
                if(!includeKeyData && this.checklistTaxaArr.length > 0){
                    this.setTaxaFilterOptions();
                }
                if(callback){
                    callback();
                }
            });
        },
        setTaxaFilterOptions() {
            this.checklistTaxaArr.forEach(taxon => {
                if(!this.taxaFilterOptions.find(taxonObj => taxonObj['sciname'] === taxon['sciname'])){
                    this.taxaFilterOptions.push({sciname: taxon['sciname'], label: taxon['sciname'], rankid: taxon['rankid']});
                }
                if(taxon['family'] && taxon['family'] !== '[Incertae Sedis]'){
                    if(!this.taxaFilterOptions.find(taxonObj => taxonObj['sciname'] === taxon['family'])){
                        this.taxaFilterOptions.push({sciname: taxon['family'], label: taxon['family'], rankid: 140});
                    }
                }
                if(Number(taxon['rankid']) >= 220){
                    const unitNameArr = taxon['sciname'].split(' ');
                    if(!this.taxaFilterOptions.find(taxonObj => taxonObj['sciname'] === unitNameArr[0])){
                        this.taxaFilterOptions.push({sciname: unitNameArr[0], label: unitNameArr[0], rankid: 180});
                    }
                    if(!this.taxaFilterOptions.find(taxonObj => taxonObj['sciname'] === (unitNameArr[0] + ' ' + unitNameArr[1]))){
                        this.taxaFilterOptions.push({sciname: (unitNameArr[0] + ' ' + unitNameArr[1]), label: (unitNameArr[0] + ' ' + unitNameArr[1]), rankid: 220});
                    }
                }
            });
            this.taxaFilterOptions.sort((a, b) => {
                return a['sciname'].toLowerCase().localeCompare(b['sciname'].toLowerCase());
            });
        },
        updateChecklistTaxaEditData(key, value) {
            this.checklistTaxaEditData[key] = value;
        },
        updateChecklistTaxonRecord(clid, callback) {
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
