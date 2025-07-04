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
        displayAuthors: false,
        displayDetails: false,
        displayImages: false,
        displaySortByOptions: [
            {value: 'family', label: 'Family/Scientific Name'},
            {value: 'sciname', label: 'Scientific Name'}
        ],
        displaySortVal: 'family',
        displaySynonyms: false,
        displayTaxonFilterVal: null,
        displayVernaculars: false,
        displayVouchers: false,
        imageStore: useImageStore(),
        taxaVernacularStore: useTaxaVernacularStore()
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
                if(state.checklistEditData.hasOwnProperty(key) && state.checklistEditData[key] !== state.checklistData[key]) {
                    exist = true;
                    state.checklistUpdateData[key] = state.checklistEditData[key];
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
        getChecklistTaxaArr(state) {
            return state.checklistTaxaStore.getChecklistTaxaArr;
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
        clearChecklistData() {
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
        deleteChecklistRecord(clid, callback) {
            const formData = new FormData();
            formData.append('clid', clid.toString());
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
            }
        },
        processDownloadRequest(name, type, clidArr, callback){
            let filename;
            const formData = new FormData();
            formData.append('clidArr', JSON.stringify(clidArr));
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
        setChecklist(clid, callback = null) {
            this.clearChecklistData();
            if(Number(clid) > 0){
                this.checklistEditData = Object.assign({}, {});
                const formData = new FormData();
                formData.append('clid', clid.toString());
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
                        this.checklistEditData = Object.assign({}, this.checklistData);
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
        setChecklistImageData(clid, numberPerTaxon) {
            this.imageStore.setChecklistImageData(clid, numberPerTaxon);
        },
        setChecklistTaxaArr(clid, includeKeyData, includeSynonymyData, includeVernacularData, callback = null) {
            this.checklistTaxaStore.setChecklistTaxaArr(clid, includeKeyData, includeSynonymyData, includeVernacularData, callback);
        },
        setChecklistVoucherData(clid) {
            let clidArr;
            if(Array.isArray(clid)){
                clidArr = clid.slice();
            }
            else{
                clidArr = [clid];
            }
            this.checklistVoucherData = Object.assign({}, {});
            const formData = new FormData();
            formData.append('clidArr', JSON.stringify(clidArr));
            formData.append('action', 'getChecklistVouchers');
            fetch(checklistVoucherApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.checklistVoucherData = Object.assign({}, data);
            });
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
        }
    }
});
