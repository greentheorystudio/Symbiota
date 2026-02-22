const useCollectionMediaUploadParametersStore = Pinia.defineStore('collection-media-upload-parameters', {
    state: () => ({
        blankCollectionMediaUploadParameterRecord: {
            spprid: 0,
            collid: null,
            title: null,
            filenamepatternmatch: null,
            patternmatchfield: 'catalognumber',
            configjson: {}
        },
        blankConfigurations: {
            createOccurrence: true
        },
        collectionMediaUploadParametersArr: [],
        collectionMediaUploadParametersData: {},
        collectionMediaUploadParametersEditData: {},
        collectionMediaUploadParametersId: null,
        collectionMediaUploadParametersUpdateData: {}
    }),
    getters: {
        getCollectionMediaUploadParametersArr(state) {
            return state.collectionMediaUploadParametersArr;
        },
        getCollectionMediaUploadParametersData(state) {
            return state.collectionMediaUploadParametersEditData;
        },
        getCollectionMediaUploadParametersEditsExist(state) {
            let exist = false;
            state.collectionMediaUploadParametersUpdateData = Object.assign({}, {});
            for(let key in state.collectionMediaUploadParametersEditData) {
                if(state.collectionMediaUploadParametersEditData.hasOwnProperty(key) && state.collectionMediaUploadParametersEditData[key] !== state.collectionMediaUploadParametersData[key]) {
                    exist = true;
                    state.collectionMediaUploadParametersUpdateData[key] = state.collectionMediaUploadParametersEditData[key];
                }
            }
            return exist;
        },
        getCollectionMediaUploadParametersID(state) {
            return state.collectionMediaUploadParametersId;
        },
        getCollectionMediaUploadParametersValid(state) {
            return (
                state.collectionMediaUploadParametersEditData['title']
            );
        },
        getConfigurations(state) {
            if(state.collectionMediaUploadParametersEditData.hasOwnProperty('configjson') && state.collectionMediaUploadParametersEditData.configjson){
                return JSON.parse(state.collectionMediaUploadParametersEditData.configjson);
            }
            else{
                return state.blankConfigurations;
            }
        },
    },
    actions: {
        createCollectionMediaUploadParametersRecord(collid, callback) {
            this.collectionMediaUploadParametersEditData['collid'] = collid.toString();
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('uploadParams', JSON.stringify(this.collectionMediaUploadParametersEditData));
            formData.append('action', 'createCollectionMediaUploadParameterRecord');
            fetch(collectionMediaUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(Number(res) > 0){
                    this.setCollectionMediaUploadParametersArr(collid, Number(res));
                }
            });
        },
        deleteCollectionMediaUploadParametersRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('uspid', this.collectionMediaUploadParametersId.toString());
            formData.append('action', 'deleteCollectionMediaUploadParameterRecord');
            fetch(collectionMediaUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((val) => {
                callback(Number(val));
                if(Number(val) === 1){
                    this.setCollectionMediaUploadParametersArr(collid);
                    this.setCurrentCollectionMediaUploadParametersRecord(0);
                }
            });
        },
        getCurrentCollectionMediaUploadParametersData() {
            return this.collectionMediaUploadParametersArr.find(params => Number(params.spprid) === this.collectionMediaUploadParametersId);
        },
        setCurrentCollectionMediaUploadParametersRecord(spprid) {
            this.collectionMediaUploadParametersId = Number(spprid);
            if(this.collectionMediaUploadParametersId > 0){
                this.collectionMediaUploadParametersData = Object.assign({}, this.getCurrentCollectionMediaUploadParametersData());
            }
            else{
                this.collectionMediaUploadParametersData = Object.assign({}, this.blankCollectionMediaUploadParameterRecord);
                this.collectionMediaUploadParametersData['configjson'] = Object.assign({}, this.blankConfigurations);
            }
            this.collectionMediaUploadParametersEditData = Object.assign({}, this.collectionMediaUploadParametersData);
        },
        setCollectionMediaUploadParametersArr(collid, spprid = null) {
            this.collectionMediaUploadParametersArr.length = 0;
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('action', 'getCollectionMediaUploadParametersByCollection');
            fetch(collectionMediaUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.collectionMediaUploadParametersArr = data;
                if(spprid){
                    this.setCurrentCollectionMediaUploadParametersRecord(spprid);
                }
            });
        },
        updateCollectionMediaUploadParametersEditData(key, value) {
            this.collectionMediaUploadParametersEditData[key] = value;
        },
        updateCollectionMediaUploadParametersRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('uspid', this.collectionMediaUploadParametersId.toString());
            formData.append('paramsData', JSON.stringify(this.collectionMediaUploadParametersUpdateData));
            formData.append('action', 'updateCollectionMediaUploadParameterRecord');
            fetch(collectionMediaUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.setCollectionMediaUploadParametersArr(collid);
                    this.collectionMediaUploadParametersData = Object.assign({}, this.collectionMediaUploadParametersEditData);
                }
            });
        }
    }
});
