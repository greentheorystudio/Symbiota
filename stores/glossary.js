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
        glossaryData: {},
        glossaryEditData: {},
        glossaryId: 0,
        glossaryUpdateData: {}
    }),
    getters: {
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
        getGlossaryValid(state) {
            return state.glossaryEditData['term'] && state.glossaryEditData['language'];
        }
    },
    actions: {
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
        }
    }
});
