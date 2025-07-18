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
        subtaxaImageData: {},
        taxaAcceptedData: null,
        taxaChildren: [],
        taxaData: {},
        taxaEditData: {},
        taxaFuzzyMatches: [],
        taxaId: 0,
        taxaIdentifiers: [],
        taxaImageArr: [],
        taxaImageCount: 0,
        taxaMediaArr: [],
        taxaParentData: {},
        taxaStr: '',
        taxaSynonyms: [],
        taxaUpdateData: {},
        taxaDescriptionBlockStore: useTaxaDescriptionBlockStore(),
        taxaDescriptionStatementStore: useTaxaDescriptionStatementStore(),
        taxaMapStore: useTaxaMapStore(),
        taxaVernacularStore: useTaxaVernacularStore()
    }),
    getters: {
        getAcceptedTaxonData(state) {
            if(state.taxaAcceptedData){
                return state.taxaAcceptedData;
            }
            else{
                return state.taxaData;
            }
        },
        getAcceptedTaxonTid(state) {
            if(state.taxaAcceptedData){
                return state.taxaAcceptedData['tid'];
            }
            else{
                return state.taxaData['tid'];
            }
        },
        getSubtaxaImageData(state) {
            return state.subtaxaImageData;
        },
        getSubtaxaTidArr(state) {
            const returnArr = [];
            state.taxaChildren.forEach((child) => {
                returnArr.push(child['tid']);
            });
            return returnArr;
        },
        getTaxaAcceptedData(state) {
            return state.taxaAcceptedData;
        },
        getTaxaChildren(state) {
            return state.taxaChildren;
        },
        getTaxaData(state) {
            return state.taxaEditData;
        },
        getTaxaDescriptionBlockArr(state) {
            return state.taxaDescriptionBlockStore.getTaxaDescriptionBlockArr;
        },
        getTaxaDescriptionStatementArr(state) {
            return state.taxaDescriptionStatementStore.getTaxaDescriptionStatementArr;
        },
        getTaxaDescriptionStatementData(state) {
            return state.taxaDescriptionStatementStore.getTaxaDescriptionStatementData;
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
        getTaxaFuzzyMatches(state) {
            return state.taxaFuzzyMatches;
        },
        getTaxaID(state) {
            return state.taxaId;
        },
        getTaxaIdentifiers(state) {
            return state.taxaIdentifiers;
        },
        getTaxaImageArr(state) {
            return state.taxaImageArr;
        },
        getTaxaImageCount(state) {
            return state.taxaImageCount;
        },
        getTaxaMapArr(state) {
            return state.taxaMapStore.getTaxaMapArr;
        },
        getTaxaMediaArr(state) {
            return state.taxaMediaArr;
        },
        getTaxaParentData(state) {
            return state.taxaParentData;
        },
        getTaxaStr(state) {
            return state.taxaStr;
        },
        getTaxaSynonyms(state) {
            return state.taxaSynonyms;
        },
        getTaxaValid(state) {
            return (
                (state.taxaEditData['kingdomid'] && state.taxaEditData['sciname'])
            );
        },
        getTaxaVernacularArr(state) {
            return state.taxaVernacularStore.getTaxaVernacularArr;
        }
    },
    actions: {
        clearTaxaData() {
            this.taxaStr = '';
            this.taxaData = Object.assign({}, this.blankTaxaRecord);
            this.taxaAcceptedData = null;
            this.taxaParentData = Object.assign({}, {});
            this.taxaIdentifiers.length = 0;
            this.taxaSynonyms.length = 0;
            this.taxaChildren.length = 0;
            this.taxaFuzzyMatches.length = 0;
            this.subtaxaImageData = Object.assign({}, {});
            this.taxaImageArr.length = 0;
            this.taxaImageCount = 0;
            this.taxaMediaArr.length = 0;
            this.taxaId = 0;
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
        setTaxa(str, callback = null) {
            this.clearTaxaData();
            if(str.toString().length > 0){
                this.taxaStr = str;
                const formData = new FormData();
                if(Number(this.taxaStr) > 0){
                    formData.append('tid', this.taxaStr.toString());
                    formData.append('action', 'getTaxonFromTid');
                }
                else{
                    formData.append('sciname', this.taxaStr.toString());
                    formData.append('action', 'getTaxonFromSciname');
                }
                fetch(taxaApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((resObj) => {
                    if(resObj.hasOwnProperty('tid') && Number(resObj['tid'] > 0)){
                        this.taxaEditData = Object.assign({}, {});
                        this.taxaId = Number(resObj['tid']);
                        this.taxaData = Object.assign({}, resObj);
                        this.taxaEditData = Object.assign({}, this.taxaData);
                        if(resObj['acceptedTaxon']){
                            this.taxaAcceptedData = Object.assign({}, resObj['acceptedTaxon']);
                        }
                        this.taxaParentData = Object.assign({}, resObj['parentTaxon']);
                        this.taxaIdentifiers = resObj['identifiers'];
                        this.taxaSynonyms = resObj['synonyms'];
                        this.taxaChildren = resObj['children'];
                        this.taxaVernacularStore.setTaxaVernacularArr(this.getAcceptedTaxonTid);
                        this.taxaMapStore.setTaxaMapArr(this.getAcceptedTaxonTid, true);
                    }
                    if(callback){
                        callback(this.taxaId);
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
        setFuzzyMatches() {
            const formData = new FormData();
            formData.append('sciname', this.taxaStr.toString());
            formData.append('lev', '2');
            formData.append('action', 'getSciNameFuzzyMatches');
            fetch(taxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                this.taxaFuzzyMatches = resObj;
            });
        },
        setSubtaxaImageData() {
            const formData = new FormData();
            formData.append('tidArr', JSON.stringify(this.getSubtaxaTidArr));
            formData.append('includeoccurrence', '1');
            formData.append('limitPerTaxon', '1');
            formData.append('sortsequenceLimit', '50');
            formData.append('action', 'getTaxonArrDisplayImageData');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                this.subtaxaImageData = Object.assign({}, resObj);
            });
        },
        setTaxaDescriptionData() {
            this.taxaDescriptionBlockStore.setTaxaDescriptionBlockArr(this.getAcceptedTaxonTid);
            this.taxaDescriptionStatementStore.setTaxaDescriptionStatementArr(this.getAcceptedTaxonTid);
        },
        setTaxaImageArr() {
            const formData = new FormData();
            formData.append('tidArr', JSON.stringify([this.getAcceptedTaxonTid]));
            formData.append('includeoccurrence', '1');
            formData.append('limitPerTaxon', '100');
            formData.append('action', 'getTaxonArrDisplayImageData');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                if(resObj.hasOwnProperty(this.getAcceptedTaxonTid)){
                    this.taxaImageCount = resObj['count'];
                    this.taxaImageArr = resObj[this.getAcceptedTaxonTid];
                }
            });
        },
        setTaxaMediaArr() {
            const formData = new FormData();
            formData.append('tidArr', JSON.stringify([this.getAcceptedTaxonTid]));
            formData.append('includeoccurrence', '1');
            formData.append('limitPerTaxon', '100');
            formData.append('action', 'getTaxonArrDisplayMediaData');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                if(resObj.hasOwnProperty(this.getAcceptedTaxonTid)){
                    this.taxaMediaArr = resObj[this.getAcceptedTaxonTid];
                }
            });
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
