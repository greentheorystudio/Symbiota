const useTaxaDescriptionBlockStore = Pinia.defineStore('taxa-description-block', {
    state: () => ({
        blankTaxaDescriptionBlockRecord: {
            tdbid: 0,
            tid: null,
            caption: null,
            source: null,
            sourceurl: null,
            language: null,
            langid: null,
            displaylevel: null,
            uid: null,
            notes: null
        },
        taxaDescriptionBlockArr: [],
        taxaDescriptionBlockData: {},
        taxaDescriptionBlockEditData: {},
        taxaDescriptionBlockId: 0,
        taxaDescriptionBlockUpdateData: {}
    }),
    getters: {
        getTaxaDescriptionBlockArr(state) {
            return state.taxaDescriptionBlockArr;
        },
        getTaxaDescriptionBlockData(state) {
            return state.taxaDescriptionBlockEditData;
        },
        getTaxaDescriptionBlockEditsExist(state) {
            let exist = false;
            state.taxaDescriptionBlockUpdateData = Object.assign({}, {});
            for(let key in state.taxaDescriptionBlockEditData) {
                if(state.taxaDescriptionBlockEditData.hasOwnProperty(key) && state.taxaDescriptionBlockEditData[key] !== state.taxaDescriptionBlockData[key]) {
                    exist = true;
                    state.taxaDescriptionBlockUpdateData[key] = state.taxaDescriptionBlockEditData[key];
                }
            }
            return exist;
        },
        getTaxaDescriptionBlockID(state) {
            return state.taxaDescriptionBlockId;
        },
        getTaxaDescriptionBlockValid(state) {
            return !!state.taxaDescriptionBlockEditData['caption'];
        }
    },
    actions: {
        clearTaxaDescriptionBlockArr() {
            this.taxaDescriptionBlockArr.length = 0;
        },
        createTaxaDescriptionBlockRecord(callback) {
            const formData = new FormData();
            formData.append('description', JSON.stringify(this.taxaDescriptionBlockEditData));
            formData.append('action', 'createTaxonDescriptionBlockRecord');
            fetch(taxonDescriptionBlockApiUrl, {
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
        deleteTaxaDescriptionBlockRecord(callback) {
            const formData = new FormData();
            formData.append('tdbid', this.taxaDescriptionBlockId.toString());
            formData.append('action', 'deleteTaxonDescriptionBlockRecord');
            fetch(taxonDescriptionBlockApiUrl, {
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
        getCurrentTaxaDescriptionBlockData() {
            return this.taxaDescriptionBlockArr.find(block => Number(block.tdbid) === this.taxaDescriptionBlockId);
        },
        setCurrentTaxaDescriptionBlockRecord(tdbid) {
            this.taxaDescriptionBlockId = Number(tdbid);
            if(this.taxaDescriptionBlockId > 0){
                this.taxaDescriptionBlockData = Object.assign({}, this.getCurrentTaxaDescriptionBlockData());
            }
            else{
                this.taxaDescriptionBlockData = Object.assign({}, this.blankTaxaDescriptionBlockRecord);
            }
            this.taxaDescriptionBlockEditData = Object.assign({}, this.taxaDescriptionBlockData);
        },
        setTaxaDescriptionBlockArr(tid) {
            const formData = new FormData();
            formData.append('tid', tid.toString());
            formData.append('action', 'getTaxonDescriptions');
            fetch(taxonDescriptionBlockApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.taxaDescriptionBlockArr = data;
            });
        },
        updateTaxaDescriptionBlockEditData(key, value) {
            this.taxaDescriptionBlockEditData[key] = value;
        },
        updateTaxaDescriptionBlockRecord(callback) {
            const formData = new FormData();
            formData.append('tdbid', this.taxaDescriptionBlockId.toString());
            formData.append('descriptionData', JSON.stringify(this.taxaDescriptionBlockUpdateData));
            formData.append('action', 'updateCommonNameRecord');
            fetch(taxonDescriptionBlockApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.taxaDescriptionBlockData = Object.assign({}, this.taxaDescriptionBlockEditData);
                }
            });
        }
    }
});
