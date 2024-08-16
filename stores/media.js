const useMediaStore = Pinia.defineStore('media', {
    state: () => ({
        blankMediaRecord: {
            mediaid: 0,
            tid: null,
            occid: null,
            accessuri: null,
            sourceurl: null,
            title: null,
            creatoruid: null,
            creator: null,
            type: null,
            format: null,
            owner: null,
            furtherinformationurl: null,
            language: null,
            usageterms: null,
            rights: null,
            bibliographiccitation: null,
            publisher: null,
            contributor: null,
            locationcreated: null,
            description: null,
            sortsequence: null
        },
        mediaArr: [],
        mediaData: {},
        mediaEditData: {},
        mediaFields: {},
        mediaId: 0,
        mediaUpdateData: {}
    }),
    getters: {
        getBlankMediaRecord(state) {
            return state.blankMediaRecord;
        },
        getMediaArr(state) {
            return state.mediaArr;
        },
        getMediaData(state) {
            return state.mediaEditData;
        },
        getMediaEditsExist(state) {
            let exist = false;
            state.mediaUpdateData = Object.assign({}, {});
            for(let key in state.mediaEditData) {
                if(state.mediaEditData.hasOwnProperty(key) && state.mediaEditData[key] !== state.mediaData[key]) {
                    exist = true;
                    state.mediaUpdateData[key] = state.mediaEditData[key];
                }
            }
            return exist;
        },
        getMediaFields(state) {
            return state.mediaFields;
        },
        getMediaID(state) {
            return state.mediaId;
        },
        getMediaValid(state) {
            return !!state.mediaEditData['accessuri'];
        }
    },
    actions: {
        clearMediaArr() {
            this.mediaArr.length = 0;
        },
        createOccurrenceDeterminationRecord(collid, occid, callback) {
            this.determinationEditData['occid'] = occid.toString();
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('determination', JSON.stringify(this.determinationEditData));
            formData.append('action', 'createOccurrenceDeterminationRecord');
            fetch(occurrenceDeterminationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                });
            });
        },
        deleteDeterminationRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('detid', this.determinationId.toString());
            formData.append('action', 'deleteDeterminationRecord');
            fetch(occurrenceDeterminationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((val) => {
                    callback(Number(val));
                });
            });
        },
        getCurrentDeterminationData(detid) {
            return this.determinationArr.find(det => Number(det.detid) === Number(detid));
        },
        makeDeterminationCurrent(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('detid', this.determinationId.toString());
            formData.append('action', 'makeDeterminationCurrent');
            fetch(occurrenceDeterminationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((val) => {
                    callback(Number(val));
                });
            });
        },
        setCurrentDeterminationRecord(detid) {
            if(Number(detid) > 0){
                this.determinationData = Object.assign({}, this.getCurrentDeterminationData(detid));
            }
            else{
                this.determinationData = Object.assign({}, this.blankDeterminationRecord);
            }
            this.determinationEditData = Object.assign({}, this.determinationData);
        },
        setMediaArr(property, value) {
            const formData = new FormData();
            formData.append('property', property);
            formData.append('value', value.toString());
            formData.append('action', 'getMediaArrByProperty');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.mediaArr = data;
            });
        },
        updateDeterminationEditData(key, value) {
            this.determinationEditData[key] = value;
        },
        updateDeterminationRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('detid', this.determinationId.toString());
            formData.append('determinationData', JSON.stringify(this.determinationUpdateData));
            formData.append('action', 'updateDeterminationRecord');
            fetch(occurrenceDeterminationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                    if(res && Number(res) === 1){
                        this.determinationData = Object.assign({}, this.determinationEditData);
                    }
                });
            });
        }
    }
});
