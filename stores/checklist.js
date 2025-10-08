const useChecklistStore = Pinia.defineStore('checklist', {
    state: () => ({
        blankChecklistRecord: {
            clid: 0,
            name: null,
            title: null,
            locality: null,
            publication: null,
            abstract: null,
            authors: null,
            type: null,
            politicaldivision: null,
            searchterms: null,
            parent: null,
            parentclid: null,
            notes: null,
            latcentroid: null,
            longcentroid: null,
            pointradiusmeters: null,
            footprintwkt: null,
            percenteffort: null,
            access: null,
            defaultsettings: null,
            iconurl: null,
            headerurl: null,
            uid: null,
            sortsequence: null,
            childChecklistArr: []
        },
        checklistData: {},
        checklistEditData: {},
        checklistId: 0,
        checklistTaxaStore: useChecklistTaxaStore(),
        checklistUpdateData: {},
        checklistVoucherData: {},
        clidArr: null,
        displayAuthors: false,
        displayDetails: false,
        displayImages: false,
        displayKey: false,
        displaySortByOptions: [
            {value: 'family', label: 'Family/Scientific Name'},
            {value: 'sciname', label: 'Scientific Name'}
        ],
        displaySortVal: 'family',
        displaySynonyms: false,
        displayTaxonFilterVal: null,
        displayVernaculars: false,
        displayVouchers: false,
        imageCountPerTaxon: null,
        imageStore: useImageStore(),
        loadKeyData: false,
        loadSynonymyData: false,
        loadVernacularData: false,
        taxaVernacularStore: useTaxaVernacularStore(),
        voucherTargetTidArr: []
    }),
    getters: {
        getBlankChecklistRecord(state) {
            return state.blankChecklistRecord;
        },
        getChecklistData(state) {
            return state.checklistEditData;
        },
        getChecklistEditsExist(state) {
            let exist = false;
            state.checklistUpdateData = Object.assign({}, {});
            for(let key in state.checklistEditData) {
                if(state.checklistEditData.hasOwnProperty(key)){
                    if((key === 'searchterms' || key === 'defaultsettings') && JSON.stringify(state.checklistEditData[key]) !== JSON.stringify(state.checklistData[key])) {
                        exist = true;
                        state.checklistUpdateData[key] = Object.assign({}, state.checklistEditData[key]);
                    }
                    else if(state.checklistEditData[key] !== state.checklistData[key]) {
                        exist = true;
                        state.checklistUpdateData[key] = state.checklistEditData[key];
                    }
                }
            }
            return exist;
        },
        getChecklistID(state) {
            return state.checklistId;
        },
        getChecklistImageData(state) {
            return state.imageStore.getChecklistImageData;
        },
        getChecklistFlashcardTaxaArr(state) {
            return state.checklistTaxaStore.getChecklistFlashcardTaxaArr;
        },
        getChecklistTaxaArr(state) {
            return state.checklistTaxaStore.getChecklistTaxaArr;
        },
        getChecklistTaxaData(state) {
            return state.checklistTaxaStore.getChecklistTaxaData;
        },
        getChecklistTaxaEditsExist(state) {
            return state.checklistTaxaStore.getChecklistTaxaEditsExist;
        },
        getChecklistTaxaFlashcardTidAcceptedArr(state) {
            return state.checklistTaxaStore.getChecklistTaxaFlashcardTidAcceptedArr;
        },
        getChecklistTaxaID(state) {
            return state.checklistTaxaStore.getChecklistTaxaID;
        },
        getChecklistTaxaImageOptionArr(state) {
            return state.checklistTaxaStore.getChecklistTaxaImageOptionArr;
        },
        getChecklistTaxaTaggedImageArr(state) {
            return state.checklistTaxaStore.getChecklistTaxaTaggedImageArr;
        },
        getChecklistTaxaTidAcceptedArr(state) {
            return state.checklistTaxaStore.getChecklistTaxaTidAcceptedArr;
        },
        getChecklistTaxaTidArr(state) {
            return state.checklistTaxaStore.getChecklistTaxaTidArr;
        },
        getChecklistTaxaValid(state) {
            return state.checklistTaxaStore.getChecklistTaxaValid;
        },
        getChecklistTaxaVoucherArr(state) {
            return state.checklistTaxaStore.getChecklistTaxaVoucherArr;
        },
        getChecklistTaxaVoucherOccidArr(state) {
            return state.checklistTaxaStore.getChecklistTaxaVoucherOccidArr;
        },
        getChecklistValid(state) {
            return !!state.checklistEditData['name'];
        },
        getChecklistVoucherData(state) {
            return state.checklistVoucherData;
        },
        getDisplayAuthors(state) {
            return state.displayAuthors;
        },
        getDisplayDetails(state) {
            return state.displayDetails;
        },
        getDisplayImages(state) {
            return state.displayImages;
        },
        getDisplayKey(state) {
            return state.displayKey;
        },
        getDisplaySortByOptions(state) {
            return state.displaySortByOptions;
        },
        getDisplaySortVal(state) {
            return state.displaySortVal;
        },
        getDisplaySynonyms(state) {
            return state.displaySynonyms;
        },
        getDisplayTaxonFilterVal(state) {
            return state.displayTaxonFilterVal;
        },
        getDisplayVernaculars(state) {
            return state.displayVernaculars;
        },
        getDisplayVouchers(state) {
            return state.displayVouchers;
        },
        getDownloadOptions(state) {
            return {
                authors: (state.displayAuthors ? '1' : '0'),
                images: (state.displayImages ? '1' : '0'),
                synonyms: (state.displaySynonyms ? '1' : '0'),
                vernaculars: (state.displayVernaculars ? '1' : '0'),
                notes: (state.displayVouchers ? '1' : '0'),
                taxaSort: state.displaySortVal,
                taxonFilter: state.displayTaxonFilterVal
            };
        },
        getTaxaFilterOptions(state) {
            return state.checklistTaxaStore.getTaxaFilterOptions;
        }
    },
    actions: {
        addChecklistVoucherRecords(occidArr, callback) {
            const formData = new FormData();
            formData.append('clid', this.checklistId.toString());
            formData.append('tid', this.getChecklistTaxaData['tid'].toString());
            formData.append('occidArr', JSON.stringify(occidArr));
            formData.append('action', 'createChecklistVoucherRecords');
            fetch(checklistVoucherApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                this.setChecklistVoucherData();
            });
        },
        addCurrentChecklistTaxonImageTag(imgid, callback) {
            this.checklistTaxaStore.addCurrentChecklistTaxonImageTag(imgid, (res) => {
                if(res === 1){
                    this.setChecklistImageData(this.imageCountPerTaxon);
                }
                callback(res);
            });
        },
        clearChecklistData() {
            this.checklistId = 0;
            this.checklistData = Object.assign({}, this.blankChecklistRecord);
            this.checklistTaxaStore.clearChecklistTaxaArr();
            this.imageStore.clearChecklistImageData();
            this.checklistVoucherData = Object.assign({}, {});
        },
        createChecklistRecord(callback) {
            const formData = new FormData();
            formData.append('checklist', JSON.stringify(this.checklistEditData));
            formData.append('action', 'createChecklistRecord');
            fetch(checklistApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) > 0){
                    this.setChecklist(Number(res));
                }
            });
        },
        createChecklistTaxaRecord(callback) {
            this.checklistTaxaStore.createChecklistTaxaRecord(this.checklistId, (newChecklistTaxaId) => {
                callback(Number(newChecklistTaxaId));
                if(newChecklistTaxaId && Number(newChecklistTaxaId) > 0){
                    this.checklistTaxaStore.setCurrentChecklistTaxonRecord(0);
                    this.checklistTaxaStore.clearChecklistTaxaArr();
                    this.checklistTaxaStore.setChecklistTaxaArr(this.clidArr, this.loadKeyData, this.loadSynonymyData, this.loadVernacularData);
                }
            });
        },
        createTemporaryChecklistFromTidArr(tidArr, callback) {
            const formData = new FormData();
            formData.append('tidArr', JSON.stringify(tidArr));
            formData.append('action', 'createTemporaryChecklistFromTidArr');
            fetch(checklistApiUrl, {
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
        deleteChecklistRecord(callback) {
            const formData = new FormData();
            formData.append('clid', this.checklistId.toString());
            formData.append('action', 'deleteChecklistRecord');
            fetch(checklistApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                this.setChecklist(0);
                callback(Number(res));
            });
        },
        deleteChecklistTaxonRecord(callback = null) {
            this.checklistTaxaStore.deleteChecklistTaxonRecord(this.checklistId, (res) => {
                if(callback){
                    callback(Number(res));
                }
                if(Number(res) === 1){
                    this.checklistTaxaStore.clearChecklistTaxaArr();
                    this.checklistTaxaStore.setChecklistTaxaArr(this.clidArr, this.loadKeyData, this.loadSynonymyData, this.loadVernacularData);
                }
            });
        },
        deleteChecklistVoucherRecord(occid, callback) {
            const formData = new FormData();
            formData.append('clid', this.checklistId.toString());
            formData.append('occid', occid.toString());
            formData.append('action', 'deleteChecklistVoucherRecord');
            fetch(checklistVoucherApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                this.setChecklistVoucherData();
            });
        },
        deleteCurrentChecklistTaxonImageTag(imgid, callback) {
            this.checklistTaxaStore.deleteCurrentChecklistTaxonImageTag(imgid, (res) => {
                if(res === 1){
                    this.setChecklistImageData(this.imageCountPerTaxon);
                }
                callback(res);
            });
        },
        getChecklistListByUid(uid, callback) {
            const formData = new FormData();
            formData.append('uid', uid.toString());
            formData.append('action', 'getChecklistListByUid');
            fetch(checklistApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                callback(resObj);
            });
        },
        lazyLoadChecklistVoucherData() {
            const targetArr = this.voucherTargetTidArr.splice(0, 200);
            const formData = new FormData();
            formData.append('clidArr', JSON.stringify(this.clidArr));
            formData.append('tidArr', JSON.stringify(targetArr));
            formData.append('action', 'getChecklistVouchers');
            fetch(checklistVoucherApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                Object.keys(data).forEach((key) => {
                    this.checklistVoucherData[key] = data[key];
                });
                if(this.voucherTargetTidArr.length > 0){
                    this.lazyLoadChecklistVoucherData();
                }
            });
        },
        normalizeSearchTerms() {
            if(this.checklistData['searchterms']){
                if(this.checklistData['searchterms'].hasOwnProperty('locality') && this.checklistData['searchterms']['locality']){
                    this.checklistData['searchterms']['local'] = this.checklistData['searchterms']['locality'];
                    delete this.checklistData['searchterms']['locality'];
                }
                if(this.checklistData['searchterms'].hasOwnProperty('recordedby') && this.checklistData['searchterms']['recordedby']){
                    this.checklistData['searchterms']['collector'] = this.checklistData['searchterms']['recordedby'];
                    delete this.checklistData['searchterms']['recordedby'];
                }
                if(this.checklistData['searchterms'].hasOwnProperty('collid') && Number(this.checklistData['searchterms']['collid']) > 0){
                    this.checklistData['searchterms']['db'] = [Number(this.checklistData['searchterms']['collid'])];
                    delete this.checklistData['searchterms']['collid'];
                }
                if(this.checklistData['searchterms'].hasOwnProperty('taxon') && this.checklistData['searchterms']['taxon']){
                    this.checklistData['searchterms']['taxa'] = this.checklistData['searchterms']['taxon'];
                    delete this.checklistData['searchterms']['taxon'];
                }
                if(this.checklistData['searchterms'].hasOwnProperty('latnorth') && this.checklistData['searchterms']['latnorth'] && this.checklistData['searchterms'].hasOwnProperty('latsouth') && this.checklistData['searchterms']['latsouth'] && this.checklistData['searchterms'].hasOwnProperty('lngeast') && this.checklistData['searchterms']['lngeast'] && this.checklistData['searchterms'].hasOwnProperty('lngwest') && this.checklistData['searchterms']['lngwest']){
                    this.checklistData['searchterms']['upperlat'] = this.checklistData['searchterms']['latnorth'];
                    this.checklistData['searchterms']['bottomlat'] = this.checklistData['searchterms']['latsouth'];
                    this.checklistData['searchterms']['leftlong'] = this.checklistData['searchterms']['lngwest'];
                    this.checklistData['searchterms']['rightlong'] = this.checklistData['searchterms']['lngeast'];
                    delete this.checklistData['searchterms']['latnorth'];
                    delete this.checklistData['searchterms']['latsouth'];
                    delete this.checklistData['searchterms']['lngwest'];
                    delete this.checklistData['searchterms']['lngeast'];
                }
            }
        },
        processChecklistDefaultDisplaySettings() {
            if(this.checklistData.hasOwnProperty('defaultsettings') && this.checklistData['defaultsettings']){
                if(this.checklistData['defaultsettings'].hasOwnProperty('ddetails') && Number(this.checklistData['defaultsettings']['ddetails']) === 1){
                    this.displayDetails = true;
                }
                if(this.checklistData['defaultsettings'].hasOwnProperty('showsynonyms') && Number(this.checklistData['defaultsettings']['showsynonyms']) === 1){
                    this.displaySynonyms = true;
                }
                if(this.checklistData['defaultsettings'].hasOwnProperty('dcommon') && Number(this.checklistData['defaultsettings']['dcommon']) === 1){
                    this.displayVernaculars = true;
                }
                if(this.checklistData['defaultsettings'].hasOwnProperty('dimages') && Number(this.checklistData['defaultsettings']['dimages']) === 1){
                    this.displayImages = true;
                }
                if(this.checklistData['defaultsettings'].hasOwnProperty('dvouchers') && Number(this.checklistData['defaultsettings']['dvouchers']) === 1){
                    this.displayVouchers = true;
                }
                if(this.checklistData['defaultsettings'].hasOwnProperty('dauthors') && Number(this.checklistData['defaultsettings']['dauthors']) === 1){
                    this.displayAuthors = true;
                }
                if(this.checklistData['defaultsettings'].hasOwnProperty('dalpha') && Number(this.checklistData['defaultsettings']['dalpha']) === 1){
                    this.displaySortVal = 'sciname';
                }
                if(this.checklistData['defaultsettings'].hasOwnProperty('keyactive') && Number(this.checklistData['defaultsettings']['keyactive']) === 1){
                    this.displayKey = true;
                }
            }
        },
        processDownloadRequest(name, type, callback){
            let filename;
            const formData = new FormData();
            formData.append('clidArr', JSON.stringify(this.clidArr));
            formData.append('options', JSON.stringify(this.getDownloadOptions));
            if(type === 'csv'){
                filename = (name + '.csv');
                formData.append('action', 'processCsvDownload');
            }
            else if(type === 'docx'){
                filename = (name + '.docx');
                formData.append('clid', this.checklistId.toString());
                formData.append('action', 'processDocxDownload');
            }
            formData.append('filename', filename);
            fetch(checklistPackagingServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.blob() : null;
            })
            .then((blob) => {
                callback(filename, blob);
            });
        },
        saveTemporaryChecklist(searchTermsJson, callback) {
            const formData = new FormData();
            formData.append('clid', this.checklistId.toString());
            formData.append('searchTermsJson', searchTermsJson);
            formData.append('action', 'saveTemporaryChecklist');
            fetch(checklistApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.setChecklist(this.checklistId);
                }
            });
        },
        setChecklist(clid, callback = null, privateOverride = false) {
            this.clearChecklistData();
            if(Number(clid) > 0){
                this.checklistEditData = Object.assign({}, {});
                const formData = new FormData();
                formData.append('clid', clid.toString());
                formData.append('privateOverride', (privateOverride ? '1' : '0'));
                formData.append('action', 'getChecklistData');
                fetch(checklistApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((resObj) => {
                    if(resObj.hasOwnProperty('clid') && Number(resObj['clid']) === Number(clid)){
                        this.checklistId = Number(clid);
                        this.checklistData = Object.assign({}, resObj);
                        this.normalizeSearchTerms();
                        this.checklistEditData = Object.assign({}, this.checklistData);
                        this.clidArr = this.checklistData['clidArr'].slice();
                        this.processChecklistDefaultDisplaySettings();
                    }
                    if(callback){
                        callback(this.checklistId);
                    }
                });
            }
            else{
                this.checklistEditData = Object.assign({}, this.checklistData);
                if(callback){
                    callback();
                }
            }
        },
        setChecklistFlashcardImageData(numberPerTaxon) {
            this.imageStore.clearChecklistImageData();
            this.imageStore.setChecklistTaggedImageData(this.clidArr, numberPerTaxon, () => {
                const targetArr = [];
                this.getChecklistTaxaFlashcardTidAcceptedArr.forEach(tid => {
                    if(!this.getChecklistImageData.hasOwnProperty(tid)){
                        targetArr.push(tid);
                    }
                });
                this.imageStore.setChecklistImageData(targetArr, numberPerTaxon);
            }, this.getChecklistTaxaFlashcardTidAcceptedArr);
        },
        setChecklistImageData(numberPerTaxon) {
            this.imageCountPerTaxon = numberPerTaxon;
            this.imageStore.clearChecklistImageData();
            this.imageStore.setChecklistTaggedImageData(this.clidArr, numberPerTaxon, () => {
                const targetArr = [];
                this.getChecklistTaxaTidAcceptedArr.forEach(tid => {
                    if(!this.getChecklistImageData.hasOwnProperty(tid)){
                        targetArr.push(tid);
                    }
                });
                this.imageStore.setChecklistImageData(targetArr, numberPerTaxon);
            });
        },
        setChecklistTaxaArr(includeKeyData, includeSynonymyData, includeVernacularData, callback = null) {
            this.loadKeyData = includeKeyData;
            this.loadSynonymyData = includeSynonymyData;
            this.loadVernacularData = includeVernacularData;
            this.checklistTaxaStore.clearChecklistTaxaArr();
            this.checklistTaxaStore.setChecklistTaxaArr(this.clidArr, includeKeyData, includeSynonymyData, includeVernacularData, callback);
        },
        setChecklistVoucherData() {
            this.checklistVoucherData = Object.assign({}, {});
            this.voucherTargetTidArr = this.getChecklistTaxaTidArr.slice();
            this.lazyLoadChecklistVoucherData();
        },
        setClidArr(value) {
            this.clidArr = value;
        },
        setCurrentChecklistTaxonRecord(cltid) {
            this.checklistTaxaStore.setCurrentChecklistTaxonRecord(cltid);
        },
        setCurrentChecklistTaxonVoucherArr() {
            this.checklistTaxaStore.setCurrentChecklistTaxonVoucherArr();
        },
        setDisplayAuthors(value) {
            this.displayAuthors = value;
        },
        setDisplayDetails(value) {
            this.displayDetails = value;
        },
        setDisplayImages(value) {
            this.displayImages = value;
        },
        setDisplaySortVal(value) {
            this.displaySortVal = value;
        },
        setDisplaySynonyms(value) {
            this.displaySynonyms = value;
        },
        setDisplayTaxonFilterVal(value) {
            this.displayTaxonFilterVal = value;
        },
        setDisplayVernaculars(value) {
            this.displayVernaculars = value;
        },
        setDisplayVouchers(value) {
            this.displayVouchers = value;
        },
        updateChecklistEditData(key, value) {
            this.checklistEditData[key] = value;
        },
        updateChecklistEditDefaultSettingsData(key, value) {
            let newSettings = {};
            if(this.checklistEditData['defaultsettings']){
                newSettings = Object.assign({}, this.checklistEditData['defaultsettings']);
            }
            newSettings[key] = value;
            this.checklistEditData['defaultsettings'] = Object.assign({}, newSettings);
        },
        updateChecklistRecord(callback) {
            const formData = new FormData();
            formData.append('clid', this.checklistId.toString());
            formData.append('checklistData', JSON.stringify(this.checklistUpdateData));
            formData.append('action', 'updateChecklistRecord');
            fetch(checklistApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.checklistData = Object.assign({}, this.checklistEditData);
                }
            });
        },
        updateChecklistTaxonEditData(key, value) {
            this.checklistTaxaStore.updateChecklistTaxaEditData(key, value);
        },
        updateChecklistTaxonRecord(callback) {
            this.checklistTaxaStore.updateChecklistTaxonRecord(this.checklistId, (res) => {
                callback(Number(res));
                if(Number(res) === 1){
                    this.checklistTaxaStore.clearChecklistTaxaArr();
                    this.checklistTaxaStore.setChecklistTaxaArr(this.clidArr, this.loadKeyData, this.loadSynonymyData, this.loadVernacularData);
                }
            });
        },
    }
});
