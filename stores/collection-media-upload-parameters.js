const useCollectionMediaUploadParametersStore = Pinia.defineStore('collection-media-upload-parameters', {
    state: () => ({
        blankCollectionMediaUploadParameterRecord: {
            uspid: 0,
            collid: null,
            uploadtype: null,
            title: null,
            dwcpath: null,
            queryparamjson: null,
            cleansql: null,
            configjson: null
        },
        collectionMediaUploadParametersArr: [],
        collectionMediaUploadParametersData: {},
        collectionMediaUploadParametersEditData: {},
        collectionMediaUploadParametersId: 0,
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
        }
    },
    actions: {
        clearCollectionMediaUploadParametersArr() {
            this.collectionMediaUploadParametersArr.length = 0;
        },
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
                response.text().then((res) => {
                    callback(Number(res));
                });
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
                response.text().then((val) => {
                    callback(Number(val));
                });
            });
        },
        getCurrentCollectionMediaUploadParametersData() {
            return this.collectionMediaUploadParametersArr.find(params => Number(params.uspid) === this.collectionMediaUploadParametersId);
        },
        setCurrentCollectionMediaUploadParametersRecord(uspid) {
            this.collectionMediaUploadParametersId = Number(uspid);
            if(this.collectionMediaUploadParametersId > 0){
                this.collectionMediaUploadParametersData = Object.assign({}, this.getCurrentCollectionMediaUploadParametersData());
            }
            else{
                this.collectionMediaUploadParametersData = Object.assign({}, this.blankCollectionMediaUploadParameterRecord);
            }
            this.collectionMediaUploadParametersEditData = Object.assign({}, this.collectionMediaUploadParametersData);
        },
        setCollectionMediaUploadParametersArr(collid) {
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
                response.text().then((res) => {
                    callback(Number(res));
                    if(res && Number(res) === 1){
                        this.collectionMediaUploadParametersData = Object.assign({}, this.collectionMediaUploadParametersEditData);
                    }
                });
            });
        }
    }
});
