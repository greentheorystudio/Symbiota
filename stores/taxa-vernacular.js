const useTaxaVernacularStore = Pinia.defineStore('taxa-vernacular', {
    state: () => ({
        blankTaxaVernacularRecord: {
            vid: 0,
            tid: null,
            vernacularname: null,
            language: null,
            langid: null,
            source: null,
            notes: null,
            username: null,
            isupperterm: null,
            sortsequence: null
        },
        taxaVernacularArr: [],
        taxaVernacularData: {},
        taxaVernacularEditData: {},
        taxaVernacularId: 0,
        taxaVernacularUpdateData: {}
    }),
    getters: {
        getTaxaVernacularArr(state) {
            return state.taxaVernacularArr;
        },
        getTaxaVernacularData(state) {
            return state.taxaVernacularEditData;
        },
        getTaxaVernacularEditsExist(state) {
            let exist = false;
            state.taxaVernacularUpdateData = Object.assign({}, {});
            for(let key in state.taxaVernacularEditData) {
                if(state.taxaVernacularEditData.hasOwnProperty(key) && state.taxaVernacularEditData[key] !== state.taxaVernacularData[key]) {
                    exist = true;
                    state.taxaVernacularUpdateData[key] = state.taxaVernacularEditData[key];
                }
            }
            return exist;
        },
        getTaxaVernacularID(state) {
            return state.taxaVernacularId;
        },
        getTaxaVernacularValid(state) {
            return !!state.taxaVernacularEditData['vernacularname'];
        }
    },
    actions: {
        clearTaxaVernacularArr() {
            this.taxaVernacularArr.length = 0;
        },
        createTaxaVernacularRecord(callback) {
            const formData = new FormData();
            formData.append('vernacular', JSON.stringify(this.taxaVernacularEditData));
            formData.append('action', 'createTaxonCommonNameRecord');
            fetch(taxonVernacularApiUrl, {
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
        deleteTaxaVernacularRecord(callback) {
            const formData = new FormData();
            formData.append('vid', this.taxaVernacularId.toString());
            formData.append('action', 'deleteTaxonCommonNameRecord');
            fetch(taxonVernacularApiUrl, {
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
        getCurrentTaxaVernacularData() {
            return this.taxaVernacularArr.find(vern => Number(vern.vid) === this.taxaVernacularId);
        },
        setCurrentTaxaVernacularRecord(vid) {
            this.taxaVernacularId = Number(vid);
            if(this.taxaVernacularId > 0){
                this.taxaVernacularData = Object.assign({}, this.getCurrentTaxaVernacularData());
            }
            else{
                this.taxaVernacularData = Object.assign({}, this.blankTaxaVernacularRecord);
            }
            this.taxaVernacularEditData = Object.assign({}, this.taxaVernacularData);
        },
        setTaxonVernacularArr(tid) {
            const formData = new FormData();
            formData.append('tid', tid.toString());
            formData.append('action', 'getCommonNamesByTid');
            fetch(taxonVernacularApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.taxaVernacularArr = data;
            });
        },
        updateTaxaVernacularEditData(key, value) {
            this.taxaVernacularEditData[key] = value;
        },
        updateTaxaVernacularRecord(callback) {
            const formData = new FormData();
            formData.append('vid', this.taxaVernacularId.toString());
            formData.append('vernacularData', JSON.stringify(this.taxaVernacularUpdateData));
            formData.append('action', 'updateCommonNameRecord');
            fetch(taxonVernacularApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.taxaVernacularData = Object.assign({}, this.taxaVernacularEditData);
                }
            });
        }
    }
});
