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
        checklistFlashcardTaxaArr: [],
        checklistTaxaArr: [],
        checklistTaxaData: {},
        checklistTaxaEditData: {},
        checklistTaxaFlashcardTidAcceptedArr: [],
        checklistTaxaId: 0,
        checklistTaxaImageOptionArr: [],
        checklistTaxaTaggedImageArr: [],
        checklistTaxaTidAcceptedArr: [],
        checklistTaxaTidArr: [],
        checklistTaxaUpdateData: {},
        checklistTaxaVoucherArr: [],
        loadingIndex: 0,
        taxaFilterOptions: []
    }),
    getters: {
        getChecklistFlashcardTaxaArr(state) {
            return state.checklistFlashcardTaxaArr;
        },
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
        getChecklistTaxaFlashcardTidAcceptedArr(state) {
            return state.checklistTaxaFlashcardTidAcceptedArr;
        },
        getChecklistTaxaID(state) {
            return state.checklistTaxaId;
        },
        getChecklistTaxaImageOptionArr(state) {
            return state.checklistTaxaImageOptionArr;
        },
        getChecklistTaxaTaggedImageArr(state) {
            return state.checklistTaxaTaggedImageArr;
        },
        getChecklistTaxaTidAcceptedArr(state) {
            return state.checklistTaxaTidAcceptedArr;
        },
        getChecklistTaxaTidArr(state) {
            return state.checklistTaxaTidArr;
        },
        getChecklistTaxaValid(state) {
            return (
                !!state.checklistTaxaEditData['tid']
            );
        },
        getChecklistTaxaVoucherArr(state) {
            return state.checklistTaxaVoucherArr;
        },
        getChecklistTaxaVoucherOccidArr(state) {
            const returnArr = [];
            state.checklistTaxaVoucherArr.forEach(voucher => {
                returnArr.push(Number(voucher.occid));
            });
            return returnArr;
        },
        getTaxaFilterOptions(state) {
            return state.taxaFilterOptions;
        }
    },
    actions: {
        addCurrentChecklistTaxonImageTag(imgid, callback) {
            const tagValue = 'CLID-' + this.checklistTaxaData['clid'].toString() + '-' + this.checklistTaxaData['tidaccepted'].toString();
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
                if(Number(res) === 1){
                    this.setCurrentChecklistTaxonTaggedImageArr();
                }
                callback(Number(res));
            });
        },
        clearChecklistTaxaArr() {
            this.checklistFlashcardTaxaArr.length = 0;
            this.checklistTaxaArr.length = 0;
            this.checklistTaxaFlashcardTidAcceptedArr.length = 0;
            this.checklistTaxaTidAcceptedArr.length = 0;
            this.checklistTaxaTidArr.length = 0;
            this.taxaFilterOptions.length = 0;
            this.loadingIndex = 0;
        },
        clearCurrentChecklistTaxon() {
            this.checklistTaxaImageOptionArr.length = 0;
            this.checklistTaxaTaggedImageArr.length = 0;
            this.checklistTaxaVoucherArr.length = 0;
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
        deleteCurrentChecklistTaxonImageTag(imgid, callback) {
            const tagValue = 'CLID-' + this.checklistTaxaData['clid'].toString() + '-' + this.checklistTaxaData['tid'].toString();
            const formData = new FormData();
            formData.append('imgid', imgid.toString());
            formData.append('tag', tagValue);
            formData.append('action', 'deleteImageTag');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    this.setCurrentChecklistTaxonTaggedImageArr();
                }
                callback(Number(res));
            });
        },
        getCurrentChecklistTaxaData() {
            return this.checklistTaxaArr.find(taxon => Number(taxon.cltlid) === this.checklistTaxaId);
        },
        setChecklistTaxaArr(clidArr, includeKeyData, includeSynonymyData, includeVernacularData, useAcceptedNames, callback = null) {
            const loadingCnt = 200;
            const formData = new FormData();
            formData.append('clidArr', JSON.stringify(clidArr));
            formData.append('includeKeyData', (includeKeyData ? '1' : '0'));
            formData.append('includeSynonymyData', (includeSynonymyData ? '1' : '0'));
            formData.append('includeVernacularData', (includeVernacularData ? '1' : '0'));
            formData.append('useAcceptedNames', (useAcceptedNames ? '1' : '0'));
            formData.append('index', this.loadingIndex.toString());
            formData.append('reccnt', loadingCnt.toString());
            formData.append('action', 'getChecklistTaxa');
            fetch(checklistTaxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.checklistTaxaArr = this.checklistTaxaArr.concat(data);
                if(data.length < loadingCnt){
                    this.setChecklistTaxaIdArrs();
                    if(!includeKeyData && this.checklistTaxaArr.length > 0){
                        this.setTaxaFilterOptions();
                    }
                    if(callback){
                        callback();
                    }
                }
                else{
                    this.loadingIndex++;
                    this.setChecklistTaxaArr(clidArr, includeKeyData, includeSynonymyData, includeVernacularData, useAcceptedNames, callback);
                }
            });
        },
        setChecklistTaxaIdArrs() {
            this.checklistTaxaArr.forEach(taxon => {
                if(!this.checklistTaxaTidAcceptedArr.includes(Number(taxon['tidaccepted']))){
                    this.checklistTaxaTidAcceptedArr.push(Number(taxon['tidaccepted']));
                }
                if(!this.checklistTaxaTidArr.includes(Number(taxon['tid']))){
                    this.checklistTaxaTidArr.push(Number(taxon['tid']));
                }
                if(Number(taxon['rankid']) >= 220 && !this.checklistTaxaFlashcardTidAcceptedArr.includes(Number(taxon['tidaccepted']))){
                    this.checklistFlashcardTaxaArr.push(taxon);
                    this.checklistTaxaFlashcardTidAcceptedArr.push(Number(taxon['tidaccepted']));
                }
            });
        },
        setCurrentChecklistTaxonImageOptionArr() {
            const formData = new FormData();
            formData.append('property', 'tid');
            formData.append('limit', '500');
            formData.append('value', this.checklistTaxaData['tidaccepted'].toString());
            formData.append('action', 'getImageArrByProperty');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.checklistTaxaImageOptionArr = data;
            });
        },
        setCurrentChecklistTaxonTaggedImageArr() {
            const tagValue = 'CLID-' + this.checklistTaxaData['clid'].toString() + '-' + this.checklistTaxaData['tid'].toString();
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
                this.checklistTaxaTaggedImageArr = data;
            });
        },
        setCurrentChecklistTaxonRecord(cltlid) {
            this.clearCurrentChecklistTaxon();
            this.checklistTaxaId = Number(cltlid);
            if(this.checklistTaxaId > 0){
                this.checklistTaxaData = Object.assign({}, this.getCurrentChecklistTaxaData());
                this.setCurrentChecklistTaxonTaggedImageArr();
                this.setCurrentChecklistTaxonImageOptionArr();
                this.setCurrentChecklistTaxonVoucherArr();
            }
            else{
                this.checklistTaxaData = Object.assign({}, this.blankChecklistTaxaRecord);
            }
            this.checklistTaxaEditData = Object.assign({}, this.checklistTaxaData);
        },
        setCurrentChecklistTaxonVoucherArr() {
            const formData = new FormData();
            formData.append('clid', this.checklistTaxaData['clid'].toString());
            formData.append('tid', this.checklistTaxaData['tid'].toString());
            formData.append('action', 'getChecklistTaxonVouchers');
            fetch(checklistVoucherApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.checklistTaxaVoucherArr = data;
            });
        },
        setTaxaFilterOptions() {
            this.checklistTaxaArr.forEach(taxon => {
                if(!this.taxaFilterOptions.find(taxonObj => taxonObj['sciname'].toLowerCase() === taxon['sciname'].toLowerCase())){
                    this.taxaFilterOptions.push({sciname: taxon['sciname'], label: taxon['sciname'], rankid: taxon['rankid']});
                }
                if(taxon['family'] && taxon['family'] !== '[Incertae Sedis]'){
                    if(!this.taxaFilterOptions.find(taxonObj => taxonObj['sciname'].toLowerCase() === taxon['family'].toLowerCase())){
                        this.taxaFilterOptions.push({sciname: taxon['family'], label: taxon['family'], rankid: 140});
                    }
                }
                if(taxon.hasOwnProperty('vernacularData') && taxon['vernacularData'] && taxon['vernacularData'].length > 0){
                    taxon['vernacularData'].forEach(vernacular => {
                        if(!this.taxaFilterOptions.find(taxonObj => taxonObj['sciname'].toLowerCase() === vernacular['vernacularname'].toLowerCase())){
                            this.taxaFilterOptions.push({sciname: vernacular['vernacularname'], label: vernacular['vernacularname'], rankid: 0});
                        }
                    });
                }
                if(Number(taxon['rankid']) >= 220){
                    const unitNameArr = taxon['sciname'].split(' ');
                    if(!this.taxaFilterOptions.find(taxonObj => taxonObj['sciname'].toLowerCase() === unitNameArr[0].toLowerCase())){
                        this.taxaFilterOptions.push({sciname: unitNameArr[0], label: unitNameArr[0], rankid: 180});
                    }
                    if(!this.taxaFilterOptions.find(taxonObj => taxonObj['sciname'].toLowerCase() === (unitNameArr[0].toLowerCase() + ' ' + unitNameArr[1].toLowerCase()))){
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
