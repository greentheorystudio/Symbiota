const useGlossaryStore = Pinia.defineStore('glossary', {
    state: () => ({
        blankGlossaryRecord: {
            glossid: 0,
            term: null,
            definition: null,
            language: null,
            source: null,
            translator: null,
            author: null,
            notes: null,
            resourceurl: null,
            uid: null
        },
        glossaryArr: [],
        glossaryData: {},
        glossaryEditData: {},
        glossaryId: 0,
        glossaryImageStore: useGlossaryImageStore(),
        glossaryLanguageArr: [],
        glossaryLoadingIndex: 0,
        glossaryRelatedTermData: {},
        glossarySourceStore: useGlossarySourceStore(),
        glossaryTaxaArr: [],
        glossaryUpdateData: {},
        glossGroupIdValue: 0
    }),
    getters: {
        getGlossaryArr(state) {
            return state.glossaryArr;
        },
        getGlossaryData(state) {
            return state.glossaryEditData;
        },
        getGlossaryEditsExist(state) {
            let exist = false;
            state.glossaryUpdateData = Object.assign({}, {});
            for(let key in state.glossaryEditData) {
                if(state.glossaryEditData.hasOwnProperty(key) && state.glossaryEditData[key] !== state.glossaryData[key]) {
                    exist = true;
                    state.glossaryUpdateData[key] = state.glossaryEditData[key];
                }
            }
            return exist;
        },
        getGlossaryID(state) {
            return state.glossaryId;
        },
        getGlossaryImageArr(state) {
            return state.glossaryImageStore.getGlossaryImageArr;
        },
        getGlossaryImageData(state) {
            return state.glossaryImageStore.getGlossaryImageData;
        },
        getGlossaryImageEditsExist(state) {
            return state.glossaryImageStore.getGlossaryImageEditsExist;
        },
        getGlossaryImageID(state) {
            return state.glossaryImageStore.getGlossaryImageID;
        },
        getGlossaryImageValid(state) {
            return state.glossaryImageStore.getGlossaryImageValid;
        },
        getGlossaryLanguageArr(state) {
            return state.glossaryLanguageArr;
        },
        getGlossaryRelatedTermData(state) {
            return state.glossaryRelatedTermData;
        },
        getGlossarySourceData(state) {
            return state.glossarySourceStore.getGlossarySourceData;
        },
        getGlossarySourceEditsExist(state) {
            return state.glossarySourceStore.getGlossarySourceEditsExist;
        },
        getGlossarySourceID(state) {
            return state.glossarySourceStore.getGlossarySourceID;
        },
        getGlossarySourceValid(state) {
            return state.glossarySourceStore.getGlossarySourceValid;
        },
        getGlossaryTaxaArr(state) {
            return state.glossaryTaxaArr;
        },
        getGlossaryValid(state) {
            return state.glossaryEditData['term'] && state.glossaryEditData['language'];
        },
        getGlossGroupIdStartIndex(state) {
            return state.glossGroupIdValue;
        }
    },
    actions: {
        addGlossaryTermRelationship(glossIdArr, groupId, relationType, callback) {
            const formData = new FormData();
            formData.append('glossIdArr', JSON.stringify(glossIdArr));
            formData.append('groupId', groupId.toString());
            formData.append('relationType', relationType);
            formData.append('action', 'addGlossaryTermRelationships');
            fetch(glossaryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) > 0){
                    this.setGlossaryRelatedTermData(true);
                }
                callback(Number(res));
            });
        },
        clearGlossaryData() {
            this.glossaryId = 0;
            this.glossaryData = Object.assign({}, this.blankGlossaryRecord);
            this.glossaryRelatedTermData = Object.assign({}, {});
            this.glossaryImageStore.clearGlossaryImageData();
        },
        createGlossaryRecord(callback) {
            const formData = new FormData();
            formData.append('glossary', JSON.stringify(this.glossaryEditData));
            formData.append('action', 'createGlossaryRecord');
            fetch(glossaryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) > 0){
                    this.glossaryEditData['glossid'] = res;
                    this.glossaryData = Object.assign({}, this.glossaryEditData);
                    this.glossaryArr.push(this.glossaryData);
                    this.glossaryArr.sort((a, b) => {
                        return a['term'].localeCompare(b['term']);
                    });
                }
                callback(Number(res));
            });
        },
        createGlossaryImageRecord(file, url, callback) {
            this.updateGlossaryImageEditData('glossid', this.glossaryId);
            this.glossaryImageStore.createGlossaryImageRecord(file, url, (newImageId) => {
                callback(Number(newImageId));
                if(Number(newImageId) > 0){
                    this.glossaryImageStore.clearGlossaryImageData();
                    this.glossaryImageStore.setGlossaryImageArr(this.glossaryId);
                }
            });
        },
        createGlossarySourceRecord(callback) {
            this.glossarySourceStore.createGlossarySourceRecord((newGlossarySourceId) => {
                callback(Number(newGlossarySourceId));
            });
        },
        deleteGlossaryImageRecord(callback) {
            this.glossaryImageStore.deleteGlossaryImageRecord((res) => {
                if(Number(res) === 1){
                    this.glossaryImageStore.clearGlossaryImageData();
                    this.glossaryImageStore.setGlossaryImageArr(this.glossaryId);
                }
                callback(Number(res));
            });
        },
        deleteGlossaryRecord(callback) {
            const formData = new FormData();
            formData.append('glossid', this.glossaryId.toString());
            formData.append('action', 'deleteGlossaryRecord');
            fetch(glossaryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    const glossaryArrObj = this.glossaryArr.find(term => Number(term.glossid) === Number(this.glossaryId));
                    const index = this.glossaryArr.indexOf(glossaryArrObj);
                    this.glossaryArr.splice(index, 1);
                }
                callback(Number(res));
            });
        },
        deleteGlossaryRelatedTermRecord(gltlinkid, callback) {
            const formData = new FormData();
            formData.append('gltlinkid', gltlinkid.toString());
            formData.append('action', 'deleteGlossaryRelatedTermRecord');
            fetch(glossaryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    this.setGlossaryRelatedTermData(true);
                }
                callback(Number(res));
            });
        },
        deleteGlossarySourceRecord(callback = null) {
            this.glossarySourceStore.deleteGlossarySourceRecord((res) => {
                if(callback){
                    callback(Number(res));
                }
            });
        },
        getCurrentGlossaryData() {
            return this.glossaryArr.find(glossary => Number(glossary.glossid) === this.glossaryId);
        },
        getGlossGroupIdArrFromRelatedTermData() {
            const returnArr = [];
            const groupIdArr = [];
            Object.keys(this.glossaryRelatedTermData).forEach(glossgrpid => {
                if(this.glossaryRelatedTermData.hasOwnProperty(glossgrpid) && this.glossaryRelatedTermData[glossgrpid].length > 0 && !groupIdArr.includes(glossgrpid)) {
                    groupIdArr.push(glossgrpid);
                    returnArr.push({
                        glossgrpid: Number(glossgrpid),
                        relationshiptype: this.glossaryRelatedTermData[glossgrpid][0]['relationshiptype']
                    });
                }
            });
            return returnArr;
        },
        getNextGlossGroupIdValue() {
            const returnVal = this.glossGroupIdValue;
            this.glossGroupIdValue++;
            return returnVal;
        },
        setCurrentGlossaryImageRecord(glimgid) {
            this.glossaryImageStore.setCurrentGlossaryImageRecord(glimgid);
        },
        setCurrentGlossaryRecord(glossid) {
            this.clearGlossaryData();
            this.glossaryId = Number(glossid);
            if(this.glossaryId > 0){
                this.glossaryData = Object.assign({}, this.getCurrentGlossaryData());
                this.setGlossaryRelatedTermData();
                this.glossaryImageStore.setGlossaryImageArr(this.glossaryId);
            }
            else{
                this.glossaryData = Object.assign({}, this.blankGlossaryRecord);
            }
            this.glossaryEditData = Object.assign({}, this.glossaryData);
        },
        setGlossaryArr(callback){
            const loadingCnt = 10000;
            const formData = new FormData();
            formData.append('numRows', loadingCnt.toString());
            formData.append('index', this.glossaryLoadingIndex.toString());
            formData.append('action', 'getGlossaryArr');
            fetch(glossaryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                const newGlossaryArr = this.glossaryArr.concat(data);
                this.glossaryArr = newGlossaryArr.slice();
                if(data.length === loadingCnt){
                    this.glossaryLoadingIndex++;
                    this.setGlossaryArr(callback);
                }
                else{
                    if(callback){
                        callback();
                    }
                }
            });
        },
        setGlossaryData(callback = null) {
            this.glossaryLoadingIndex = 0;
            this.glossaryArr.length = 0;
            this.glossaryLanguageArr.length = 0;
            this.glossaryTaxaArr.length = 0;
            this.setGlossaryLanguageArr();
            this.setGlossaryTaxaArr();
            this.setGlossaryArr(callback);
        },
        setGlossaryLanguageArr() {
            const formData = new FormData();
            formData.append('action', 'getGlossaryLanguageArr');
            fetch(glossaryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.glossaryLanguageArr = data;
            });
        },
        setGlossaryRelatedTermData(updateGlossaryArr = false) {
            const formData = new FormData();
            formData.append('glossIdArr', JSON.stringify([this.glossaryId]));
            formData.append('action', 'getGlossaryRelatedTermsDataFromGlossidArr');
            fetch(glossaryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.glossaryRelatedTermData = Object.assign({}, data);
                if(updateGlossaryArr){
                    const glossGrpIdArr = this.getGlossGroupIdArrFromRelatedTermData();
                    const glossaryArrObj = this.glossaryArr.find(term => Number(term.glossid) === Number(this.glossaryId));
                    glossaryArrObj['groupIdArr'] = glossGrpIdArr.slice();
                }
            });
        },
        setGlossarySourceData(id) {
            this.glossarySourceStore.setGlossarySourceData(id);
        },
        setGlossaryTaxaArr() {
            const formData = new FormData();
            formData.append('action', 'getGlossaryTaxaArr');
            fetch(glossaryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.glossaryTaxaArr = data;
            });
        },
        setGlossGroupIdStartIndex() {
            const formData = new FormData();
            formData.append('action', 'getGlossGroupIdStartIndex');
            fetch(glossaryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                this.glossGroupIdValue = Number(res);
            });
        },
        updateGlossaryEditData(key, value) {
            this.glossaryEditData[key] = value;
        },
        updateGlossaryImageEditData(key, value) {
            this.glossaryImageStore.updateGlossaryImageEditData(key, value);
        },
        updateGlossaryImageRecord(callback) {
            this.glossaryImageStore.updateGlossaryImageRecord((res) => {
                callback(Number(res));
            });
        },
        updateGlossaryRecord(callback) {
            const formData = new FormData();
            formData.append('glossid', this.glossaryId.toString());
            formData.append('glossaryData', JSON.stringify(this.glossaryUpdateData));
            formData.append('action', 'updateGlossaryRecord');
            fetch(glossaryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.glossaryData = Object.assign({}, this.glossaryEditData);
                    this.glossaryArr = this.glossaryArr.map(term => Number(term.glossid) === Number(this.glossaryId) ? this.glossaryData : term);
                    this.glossaryArr.sort((a, b) => {
                        return a['term'].localeCompare(b['term']);
                    });
                }
            });
        },
        updateGlossarySourceEditData(key, value) {
            this.glossarySourceStore.updateGlossarySourceEditData(key, value);
        },
        updateGlossarySourceRecord(callback) {
            this.glossarySourceStore.updateGlossarySourceRecord((res) => {
                callback(Number(res));
            });
        }
    }
});
