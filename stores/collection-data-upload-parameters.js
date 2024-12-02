const useCollectionDataUploadParametersStore = Pinia.defineStore('collection-data-upload-parameters', {
    state: () => ({
        blankCollectionDataUploadParameterRecord: {
            uspid: 0,
            collid: null,
            uploadtype: null,
            title: null,
            dwcpath: null,
            queryparamjson: null,
            cleansql: null,
            configjson: null
        },
        collectionDataUploadParametersArr: [],
        collectionDataUploadParametersData: {},
        collectionDataUploadParametersEditData: {},
        collectionDataUploadParametersId: 0,
        collectionDataUploadParametersUpdateData: {}
    }),
    getters: {
        getCollectionDataUploadParametersArr(state) {
            return state.collectionDataUploadParametersArr;
        },
        getCollectionDataUploadParametersData(state) {
            return state.collectionDataUploadParametersEditData;
        },
        getCollectionDataUploadParametersEditsExist(state) {
            let exist = false;
            state.collectionDataUploadParametersUpdateData = Object.assign({}, {});
            for(let key in state.collectionDataUploadParametersEditData) {
                if(state.collectionDataUploadParametersEditData.hasOwnProperty(key) && state.collectionDataUploadParametersEditData[key] !== state.collectionDataUploadParametersData[key]) {
                    exist = true;
                    state.collectionDataUploadParametersUpdateData[key] = state.collectionDataUploadParametersEditData[key];
                }
            }
            return exist;
        },
        getCollectionDataUploadParametersID(state) {
            return state.collectionDataUploadParametersId;
        },
        getCollectionDataUploadParametersValid(state) {
            return (
                state.collectionDataUploadParametersEditData['uploadtype'] && state.collectionDataUploadParametersEditData['title']
            );
        }
    },
    actions: {
        clearCollectionDataUploadParametersArr() {
            this.collectionDataUploadParametersArr.length = 0;
        },
        createCollectionDataUploadParametersRecord(collid, callback) {
            this.collectionDataUploadParametersEditData['collid'] = collid.toString();
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('uploadParams', JSON.stringify(this.collectionDataUploadParametersEditData));
            formData.append('action', 'createCollectionDataUploadParameterRecord');
            fetch(collectionDataUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                });
            });
        },
        deleteCollectionDataUploadParametersRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('uspid', this.collectionDataUploadParametersId.toString());
            formData.append('action', 'deleteCollectionDataUploadParameterRecord');
            fetch(collectionDataUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((val) => {
                    callback(Number(val));
                });
            });
        },
        getCurrentCollectionDataUploadParametersData() {
            return this.collectionDataUploadParametersArr.find(params => Number(params.uspid) === this.collectionDataUploadParametersId);
        },
        setCurrentCollectionDataUploadParametersRecord(uspid) {
            this.collectionDataUploadParametersId = Number(uspid);
            if(this.collectionDataUploadParametersId > 0){
                this.collectionDataUploadParametersData = Object.assign({}, this.getCurrentCollectionDataUploadParametersData());
            }
            else{
                this.collectionDataUploadParametersData = Object.assign({}, this.blankCollectionDataUploadParameterRecord);
            }
            this.collectionDataUploadParametersEditData = Object.assign({}, this.collectionDataUploadParametersData);
        },
        setCollectionDataUploadParametersArr(collid) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('action', 'getCollectionDataUploadParametersByCollection');
            fetch(collectionDataUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.collectionDataUploadParametersArr = data;
            });
        },
        updateCollectionDataUploadParametersEditData(key, value) {
            this.collectionDataUploadParametersEditData[key] = value;
        },
        updateCollectionDataUploadParametersRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('uspid', this.collectionDataUploadParametersId.toString());
            formData.append('paramsData', JSON.stringify(this.collectionDataUploadParametersUpdateData));
            formData.append('action', 'updateCollectionDataUploadParameterRecord');
            fetch(collectionDataUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                    if(res && Number(res) === 1){
                        this.collectionDataUploadParametersData = Object.assign({}, this.collectionDataUploadParametersEditData);
                    }
                });
            });
        }
    }
});
