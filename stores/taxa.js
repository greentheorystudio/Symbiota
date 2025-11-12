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
        taxaDescriptionBlockStore: useTaxaDescriptionBlockStore(),
        taxaDescriptionStatementStore: useTaxaDescriptionStatementStore(),
        taxaEditData: {},
        taxaFuzzyMatches: [],
        taxaId: 0,
        taxaIdentifiers: [],
        taxaImageArr: [],
        taxaImageCount: 0,
        taxaMapStore: useTaxaMapStore(),
        taxaMediaArr: [],
        taxaParentData: {},
        taxaStr: '',
        taxaSynonyms: [],
        taxaTaggedImageArr: [],
        taxaUpdateData: {},
        taxaUseData: {},
        taxaVernacularStore: useTaxaVernacularStore()
    }),
    getters: {
        getAccepted(state) {
            return Number(state.taxaData['tid']) === Number(state.taxaData['tidaccepted']);
        },
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
        getBlankTaxaRecord(state) {
            return state.blankTaxaRecord;
        },
        getHasAcceptedChildren(state) {
            let response = false;
            if(state.taxaChildren.length > 0){
                state.taxaChildren.forEach((child) => {
                    if(Number(child['tid']) === Number(child['tidaccepted'])){
                        response = true;
                    }
                });
            }
            return response;
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
        getTaxaMapData(state) {
            return state.taxaMapStore.getTaxaMapData;
        },
        getTaxaMapEditsExist(state) {
            return state.taxaMapStore.getTaxaMapEditsExist;
        },
        getTaxaMediaArr(state) {
            return state.taxaMediaArr;
        },
        getTaxaStr(state) {
            return state.taxaStr;
        },
        getTaxaSynonyms(state) {
            return state.taxaSynonyms;
        },
        getTaxaTaggedImageArr(state) {
            return state.taxaTaggedImageArr;
        },
        getTaxaUseData(state) {
            return state.taxaUseData;
        },
        getTaxaValid(state) {
            return (
                (Number(state.taxaEditData['kingdomid']) > 0 && state.taxaEditData['sciname'])
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
        addTaxaImageTag(imgid, callback) {
            const tagValue = 'TID-' + this.taxaId.toString();
            const formData = new FormData();
            formData.append('imgid', imgid.toString());
            formData.append('tag', tagValue);
            formData.append('action', 'addImageTag');
            fetch(imageApiUrl, {
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
            this.taxaTaggedImageArr.length = 0;
            this.taxaId = 0;
            this.taxaUseData = Object.assign({}, {});
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
        createTaxaMapRecord(file, path, callback) {
            this.updateTaxaMapEditData('tid', this.taxaId);
            this.taxaMapStore.createTaxaMapRecord(file, path, (newMapId) => {
                callback(Number(newMapId));
                this.setTaxonMapArr(this.taxaId);
            });
        },
        createTaxaVernacularRecord(callback) {
            this.updateTaxaVernacularEditData('tid', this.taxaId);
            this.taxaVernacularStore.createTaxaVernacularRecord((newVernacularId) => {
                callback(Number(newVernacularId));
                this.setTaxonVernacularArr(this.taxaId);
            });
        },
        createTaxonRecord(taxonData, callback) {
            const formData = new FormData();
            formData.append('taxon', JSON.stringify(taxonData));
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
                if(Number(res) > 0){
                    this.populateTaxonHierarchyData(Number(res));
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
        deleteTaxaMapRecord(callback = null) {
            this.taxaMapStore.deleteTaxaMapRecord((res) => {
                if(callback){
                    callback(Number(res));
                }
                if(Number(res) === 1){
                    this.setTaxonMapArr(this.taxaId);
                }
            });
        },
        deleteTaxaImageTag(imgid) {
            const tagValue = 'TID-' + this.taxaId.toString();
            const formData = new FormData();
            formData.append('imgid', imgid.toString());
            formData.append('tag', tagValue);
            formData.append('action', 'deleteImageTag');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
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
        deleteTaxonRecord(callback) {
            const formData = new FormData();
            formData.append('tid', this.taxaId.toString());
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
        populateTaxonHierarchyData(tid) {
            const formData = new FormData();
            formData.append('tid', tid.toString());
            formData.append('action', 'populateTaxonHierarchyData');
            fetch(taxonHierarchyApiUrl, {
                method: 'POST',
                body: formData
            })
        },
        remapTaxonResources(remaptid, callback) {
            const formData = new FormData();
            formData.append('tid', this.taxaId.toString());
            formData.append('targettid', remaptid);
            formData.append('action', 'remapTaxonResources');
            fetch(taxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(Number(res) === 1){
                    this.updateTaxonHierarchyData(this.taxaId, () => {
                        this.updateTaxonHierarchyData(remaptid);
                    });
                }
            });
        },
        removeTaxonFromHierarchyData() {
            const formData = new FormData();
            formData.append('tidarr', JSON.stringify([this.taxaId]));
            formData.append('action', 'clearHierarchyTable');
            fetch(taxonHierarchyApiUrl, {
                method: 'POST',
                body: formData
            })
        },
        revertScinameEdits() {
            this.taxaEditData['sciname'] = this.taxaData['sciname'];
            this.taxaEditData['unitind1'] = this.taxaData['unitind1'];
            this.taxaEditData['unitname1'] = this.taxaData['unitname1'];
            this.taxaEditData['unitind2'] = this.taxaData['unitind2'];
            this.taxaEditData['unitname2'] = this.taxaData['unitname2'];
            this.taxaEditData['unitind3'] = this.taxaData['unitind3'];
            this.taxaEditData['unitname3'] = this.taxaData['unitname3'];
        },
        setCurrentTaxaDescriptionBlockRecord(tdbid) {
            this.taxaDescriptionBlockStore.setCurrentTaxaDescriptionBlockRecord(tdbid);
        },
        setCurrentTaxaDescriptionStatementRecord(tdbid, tdsid) {
            this.taxaDescriptionStatementStore.setCurrentTaxaDescriptionStatementRecord(tdbid, tdsid);
        },
        setCurrentTaxaMapRecord(mid) {
            this.taxaMapStore.setCurrentTaxaMapRecord(this.taxaId, mid);
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
            formData.append('includetagged', '1');
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
        setTaxaTaggedImageArr(tid) {
            const tagValue = 'TID-' + tid.toString();
            const formData = new FormData();
            formData.append('value', tagValue);
            formData.append('action', 'getImageArrByTagValue');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.taxaTaggedImageArr = data;
            });
        },
        setTaxaUseData() {
            const formData = new FormData();
            formData.append('tid', this.taxaId.toString());
            formData.append('action', 'getTaxaUseData');
            fetch(taxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                this.taxaUseData = Object.assign({}, resObj);
            });
        },
        setTaxon(str, actual, callback = null) {
            this.clearTaxonData();
            if(str.toString().length > 0 && str.toString() !== '0'){
                this.taxaStr = str;
                const formData = new FormData();
                formData.append('actual', (actual ? '1' : '0'));
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
        updateTaxaMapEditData(key, value) {
            this.taxaMapStore.updateTaxaMapEditData(key, value);
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
        updateTaxaMapRecord(callback) {
            this.taxaMapStore.updateTaxaMapRecord((res) => {
                callback(Number(res));
                this.setTaxonMapArr(this.taxaId);
            });
        },
        updateTaxaVernacularRecord(callback) {
            this.taxaVernacularStore.updateTaxaVernacularRecord((res) => {
                callback(Number(res));
                this.setTaxonVernacularArr(this.taxaId);
            });
        },
        updateTaxonChildrenKingdomFamily() {
            const formData = new FormData();
            formData.append('tid', this.taxaId.toString());
            formData.append('kingdomid', this.taxaData['kingdomid'].toString());
            formData.append('family', this.taxaData['family']);
            formData.append('action', 'updateTaxonChildrenKingdomFamily');
            fetch(taxaApiUrl, {
                method: 'POST',
                body: formData
            })
        },
        updateTaxonEditData(key, value) {
            this.taxaEditData[key] = value;
        },
        updateTaxonHierarchyData(tid, callback = null) {
            const formData = new FormData();
            formData.append('tid', tid.toString());
            formData.append('action', 'updateTaxonHierarchyData');
            fetch(taxonHierarchyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then(() => {
                if(callback){
                    callback();
                }
            });
        },
        updateTaxonParent(parenttid, kingdomid, family, callback) {
            this.updateTaxonEditData('kingdomid', kingdomid);
            this.updateTaxonEditData('parenttid', parenttid);
            this.updateTaxonEditData('family', family);
            if(this.getTaxaEditsExist){
                this.updateTaxonRecord((res) => {
                    if(res === 1){
                        this.updateTaxonHierarchyData(this.taxaId, (res) => {
                            if(this.taxaChildren.length > 0){
                                this.updateTaxonChildrenKingdomFamily();
                            }
                        });
                    }
                    callback(res);
                });
            }
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
