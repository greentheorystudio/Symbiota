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
        getTaxaDescriptionBlockData(state) {
            return state.taxaDescriptionBlockStore.getTaxaDescriptionBlockData;
        },
        getTaxaDescriptionBlockEditsExist(state) {
            return state.taxaDescriptionBlockStore.getTaxaDescriptionBlockEditsExist;
        },
        getTaxaDescriptionBlockValid(state) {
            return state.taxaDescriptionBlockStore.getTaxaDescriptionBlockValid;
        },
        getTaxaDescriptionStatementArr(state) {
            return state.taxaDescriptionStatementStore.getTaxaDescriptionStatementArr;
        },
        getTaxaDescriptionStatementData(state) {
            return state.taxaDescriptionStatementStore.getTaxaDescriptionStatementData;
        },
        getTaxaDescriptionStatementEditsExist(state) {
            return state.taxaDescriptionStatementStore.getTaxaDescriptionStatementEditsExist;
        },
        getTaxaDescriptionStatementValid(state) {
            return state.taxaDescriptionStatementStore.getTaxaDescriptionStatementValid;
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
        },
        getTaxaVernacularData(state) {
            return state.taxaVernacularStore.getTaxaVernacularData;
        },
        getTaxaVernacularEditsExist(state) {
            return state.taxaVernacularStore.getTaxaVernacularEditsExist;
        },
        getTaxaVernacularValid(state) {
            return state.taxaVernacularStore.getTaxaVernacularValid;
        }
    },
    actions: {
        clearTaxonData() {
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
            this.taxaDescriptionBlockStore.clearTaxaDescriptionBlockArr();
            this.taxaDescriptionStatementStore.clearTaxaDescriptionStatementArr();
            this.taxaMapStore.clearTaxaMapArr();
            this.taxaVernacularStore.clearTaxaVernacularArr();
        },
        createTaxaDescriptionBlockRecord(callback) {
            this.updateTaxaDescriptionBlockEditData('tid', this.taxaId);
            this.taxaDescriptionBlockStore.createTaxaDescriptionBlockRecord((newBlockId) => {
                callback(Number(newBlockId));
                this.setTaxonDescriptionData(this.taxaId);
            });
        },
        createTaxaDescriptionStatementRecord(tdbid, callback) {
            this.updateTaxaDescriptionStatementEditData('tdbid', tdbid);
            this.taxaDescriptionStatementStore.createTaxaDescriptionStatementRecord((newStatementId) => {
                callback(Number(newStatementId));
                this.setTaxonDescriptionData(this.taxaId);
            });
        },
        createTaxaVernacularRecord(callback) {
            this.updateTaxaVernacularEditData('tid', this.taxaId);
            this.taxaVernacularStore.createTaxaVernacularRecord((newVernacularId) => {
                callback(Number(newVernacularId));
                this.setTaxonVernacularArr(this.taxaId);
            });
        },
        createTaxonRecord(callback) {
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
                    this.setTaxon(Number(res));
                }
            });
        },
        deleteTaxaDescriptionBlockRecord(callback = null) {
            this.taxaDescriptionBlockStore.deleteTaxaDescriptionBlockRecord((res) => {
                if(callback){
                    callback(Number(res));
                }
                if(Number(res) === 1){
                    this.setTaxonDescriptionData(this.taxaId);
                }
            });
        },
        deleteTaxaDescriptionStatementRecord(callback = null) {
            this.taxaDescriptionStatementStore.deleteTaxaDescriptionStatementRecord((res) => {
                if(callback){
                    callback(Number(res));
                }
                if(Number(res) === 1){
                    this.setTaxonDescriptionData(this.taxaId);
                }
            });
        },
        deleteTaxaVernacularRecord(callback = null) {
            this.taxaVernacularStore.deleteTaxaVernacularRecord((res) => {
                if(callback){
                    callback(Number(res));
                }
                if(Number(res) === 1){
                    this.setTaxonVernacularArr(this.taxaId);
                }
            });
        },
        deleteTaxonRecord(tid, callback) {
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
                this.setTaxon(0);
                callback(Number(res));
            });
        },
        setCurrentTaxaDescriptionBlockRecord(tdbid) {
            this.taxaDescriptionBlockStore.setCurrentTaxaDescriptionBlockRecord(tdbid);
        },
        setCurrentTaxaDescriptionStatementRecord(tdbid, tdsid) {
            this.taxaDescriptionStatementStore.setCurrentTaxaDescriptionStatementRecord(tdbid, tdsid);
        },
        setCurrentTaxaVernacularRecord(vid) {
            this.taxaVernacularStore.setCurrentTaxaVernacularRecord(vid);
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
        setTaxaImageArr(tid, includeOccurrence = true) {
            const formData = new FormData();
            formData.append('tidArr', JSON.stringify([Number(tid)]));
            formData.append('includeoccurrence', (includeOccurrence ? '1' : '0'));
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
        setTaxaMediaArr(tid, includeOccurrence = true) {
            const formData = new FormData();
            formData.append('tidArr', JSON.stringify([Number(tid)]));
            formData.append('includeoccurrence', (includeOccurrence ? '1' : '0'));
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
        setTaxon(str, callback = null) {
            this.clearTaxonData();
            if(str.toString().length > 0 && str.toString() !== '0'){
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
        setTaxonDescriptionData(tid) {
            this.taxaDescriptionBlockStore.setTaxonDescriptionBlockArr(Number(tid));
            this.taxaDescriptionStatementStore.setTaxonDescriptionStatementArr(Number(tid));
        },
        setTaxonMapArr(tid) {
            this.taxaMapStore.setTaxonMapArr(Number(tid), true);
        },
        setTaxonVernacularArr(tid) {
            this.taxaVernacularStore.setTaxonVernacularArr(Number(tid));
        },
        updateTaxaDescriptionBlockEditData(key, value) {
            this.taxaDescriptionBlockStore.updateTaxaDescriptionBlockEditData(key, value);
        },
        updateTaxaDescriptionStatementEditData(key, value) {
            this.taxaDescriptionStatementStore.updateTaxaDescriptionStatementEditData(key, value);
        },
        updateTaxaVernacularEditData(key, value) {
            this.taxaVernacularStore.updateTaxaVernacularEditData(key, value);
        },
        updateTaxaDescriptionBlockRecord(callback) {
            this.taxaDescriptionBlockStore.updateTaxaDescriptionBlockRecord((res) => {
                callback(Number(res));
                this.setTaxonDescriptionData(this.taxaId);
            });
        },
        updateTaxaDescriptionStatementRecord(callback) {
            this.taxaDescriptionStatementStore.updateTaxaDescriptionStatementRecord((res) => {
                callback(Number(res));
                this.setTaxonDescriptionData(this.taxaId);
            });
        },
        updateTaxaVernacularRecord(callback) {
            this.taxaVernacularStore.updateTaxaVernacularRecord((res) => {
                callback(Number(res));
                this.setTaxonVernacularArr(this.taxaId);
            });
        },
        updateTaxonEditData(key, value) {
            this.taxaEditData[key] = value;
        },
        updateTaxonRecord(callback) {
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
