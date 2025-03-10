const useTaxaMapStore = Pinia.defineStore('taxa-map', {
    state: () => ({
        blankTaxaMapRecord: {
            mid: 0,
            tid: null,
            url: null,
            title: null
        },
        taxaMapArr: {},
        taxaMapData: {},
        taxaMapEditData: {},
        taxaMapId: 0,
        taxaMapUpdateData: {}
    }),
    getters: {
        getTaxaMapArr(state) {
            return state.taxaMapArr;
        },
        getTaxaMapData(state) {
            return state.taxaMapEditData;
        },
        getTaxaMapEditsExist(state) {
            let exist = false;
            state.taxaMapUpdateData = Object.assign({}, {});
            for(let key in state.taxaMapEditData) {
                if(state.taxaMapEditData.hasOwnProperty(key) && state.taxaMapEditData[key] !== state.taxaMapData[key]) {
                    exist = true;
                    state.taxaMapUpdateData[key] = state.taxaMapEditData[key];
                }
            }
            return exist;
        },
        getTaxaMapID(state) {
            return state.taxaMapId;
        },
        getTaxaMapValid(state) {
            return !!state.taxaMapEditData['tid'];
        }
    },
    actions: {
        clearTaxaMapArr() {
            this.taxaMapArr = Object.assign({}, {});
        },
        createTaxaMapRecord(callback) {
            const formData = new FormData();
            formData.append('map', JSON.stringify(this.taxaMapEditData));
            formData.append('action', 'createTaxonMapRecord');
            fetch(taxonMapApiUrl, {
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
        deleteTaxaMapRecord(callback) {
            const formData = new FormData();
            formData.append('mid', this.taxaMapId.toString());
            formData.append('action', 'deleteTaxonMapRecord');
            fetch(taxonMapApiUrl, {
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
        getCurrentTaxaMapData(tid) {
            return this.taxaMapArr.hasOwnProperty(tid) ? this.taxaMapArr[tid] : null;
        },
        setCurrentTaxaMapRecord(tid, mid) {
            this.taxaMapId = Number(mid);
            if(this.taxaMapId > 0){
                this.taxaMapData = Object.assign({}, this.getCurrentTaxaMapData(tid));
            }
            else{
                this.taxaMapData = Object.assign({}, this.blankTaxaMapRecord);
            }
            this.taxaMapEditData = Object.assign({}, this.taxaMapData);
        },
        setTaxaMapArr(tid, includeSubtaxa) {
            const formData = new FormData();
            formData.append('tid', tid.toString());
            formData.append('includeSubtaxa', (includeSubtaxa ? '1' : '0'));
            formData.append('action', 'getTaxonMaps');
            fetch(taxonMapApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.taxaMapArr = Object.assign({}, data);
            });
        },
        updateTaxaMapEditData(key, value) {
            this.taxaMapEditData[key] = value;
        },
        updateTaxaMapRecord(callback) {
            const formData = new FormData();
            formData.append('mid', this.taxaMapId.toString());
            formData.append('mapData', JSON.stringify(this.taxaMapUpdateData));
            formData.append('action', 'updateTaxonMapRecord');
            fetch(taxonMapApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.taxaMapData = Object.assign({}, this.taxaMapEditData);
                }
            });
        }
    }
});
