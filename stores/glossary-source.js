const useGlossarySourceStore = Pinia.defineStore('glossary-source', {
    state: () => ({
        blankGlossarySourceRecord: {
            tid: 0,
            contributorterm: null,
            contributorimage: null,
            translator: null,
            additionalsources: null
        },
        glossarySourceData: {},
        glossarySourceEditData: {},
        glossarySourceId: 0,
        glossarySourceUpdateData: {}
    }),
    getters: {
        getGlossarySourceData(state) {
            return state.glossarySourceEditData;
        },
        getGlossarySourceEditsExist(state) {
            let exist = false;
            state.glossarySourceUpdateData = Object.assign({}, {});
            for(let key in state.glossarySourceEditData) {
                if(state.glossarySourceEditData.hasOwnProperty(key) && state.glossarySourceEditData[key] !== state.glossarySourceData[key]) {
                    exist = true;
                    state.glossarySourceUpdateData[key] = state.glossarySourceEditData[key];
                }
            }
            return exist;
        },
        getGlossarySourceID(state) {
            return state.glossarySourceId;
        }
    },
    actions: {
        clearGlossarySourceData() {
            this.glossarySourceId = 0;
            this.glossarySourceData = Object.assign({}, this.blankGlossarySourceRecord);
        },
        createGlossarySourceRecord(callback) {
            const formData = new FormData();
            formData.append('glossarySource', JSON.stringify(this.glossarySourceEditData));
            formData.append('action', 'createGlossarySourceRecord');
            fetch(glossarySourceApiUrl, {
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
        deleteGlossarySourceRecord(callback) {
            const formData = new FormData();
            formData.append('tid', this.glossarySourceId.toString());
            formData.append('action', 'deleteGlossarySourceRecord');
            fetch(glossarySourceApiUrl, {
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
        setGlossarySourceData(id) {
            this.clearGlossarySourceData();
            this.glossarySourceId = Number(id);
            if(this.glossarySourceId > 0){
                const formData = new FormData();
                formData.append('tid', this.glossarySourceId.toString());
                formData.append('action', 'getGlossarySourceRecord');
                fetch(glossarySourceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    if(data.hasOwnProperty('tid') && Number(data['tid']) > 0) {
                        this.glossarySourceData = Object.assign({}, data);
                        this.glossarySourceEditData = Object.assign({}, this.glossarySourceData);
                    }
                });
            }
            else{
                this.glossarySourceEditData = Object.assign({}, this.glossarySourceData);
            }
        },
        updateGlossarySourceEditData(key, value) {
            this.glossarySourceEditData[key] = value;
        },
        updateGlossarySourceRecord(callback) {
            const formData = new FormData();
            formData.append('tid', this.glossarySourceId.toString());
            formData.append('glossarySourceData', JSON.stringify(this.glossarySourceUpdateData));
            formData.append('action', 'updateGlossarySourceRecord');
            fetch(glossarySourceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.glossarySourceData = Object.assign({}, this.glossarySourceEditData);
                }
            });
        }
    }
});
