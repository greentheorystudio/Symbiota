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
            notes: 1,
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
        glossarySourceStore: useGlossarySourceStore(),
        glossaryTaxaArr: [],
        glossaryUpdateData: {}
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
        getGlossaryLanguageArr(state) {
            return state.glossaryLanguageArr;
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
        }
    },
    actions: {
        clearGlossaryData() {
            this.glossaryId = 0;
            this.glossaryData = Object.assign({}, this.blankGlossaryRecord);
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
                callback(Number(res));
            });
        },
        createGlossarySourceRecord(callback) {
            this.glossarySourceStore.createGlossarySourceRecord((newGlossarySourceId) => {
                callback(Number(newGlossarySourceId));
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
        setCurrentGlossaryRecord(glossid) {
            this.clearGlossaryData();
            this.glossaryId = Number(glossid);
            if(this.glossaryId > 0){
                this.glossaryData = Object.assign({}, this.getCurrentGlossaryData());
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
        updateGlossaryEditData(key, value) {
            this.glossaryEditData[key] = value;
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
