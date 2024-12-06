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
        blankConfigurations: {
            existingRecords: 'update'
        },
        collectionDataUploadParametersArr: [],
        collectionDataUploadParametersData: {},
        collectionDataUploadParametersEditData: {},
        collectionDataUploadParametersId: null,
        collectionDataUploadParametersUpdateData: {},
        eventMofDataFields: {},
        occurrenceMofDataFields: {},
        uploadTypeOptions: [
            {value: 6, label: 'File Upload (DwC-A, csv, geoJSON)'},
            {value: 8, label: 'IPT/DwC-A Provider'},
            {value: 10, label: 'Symbiota Portal'}
        ]
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
        },
        getConfigurations(state) {
            if(state.collectionDataUploadParametersEditData.hasOwnProperty('configjson') && state.collectionDataUploadParametersEditData.configjson){
                return JSON.parse(state.collectionDataUploadParametersEditData.configjson);
            }
            else{
                return state.blankConfigurations;
            }
        },
        getUploadTypeOptions(state) {
            return state.uploadTypeOptions;
        }
    },
    actions: {
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
            this.collectionDataUploadParametersArr.length = 0;
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
