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
        clearMediaData() {
            this.mediaData = Object.assign({}, this.blankMediaRecord);
            this.mediaEditData = Object.assign({}, {});
        },
        setCurrentMediaRecord(medid) {
            this.mediaId = Number(medid);
            this.clearMediaData();
            if(this.mediaId > 0){
                this.setMediaData();
            }
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
        setMediaData() {
            const formData = new FormData();
            formData.append('mediaid', this.mediaId.toString());
            formData.append('action', 'getMediaData');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('mediaid') && Number(data.mediaid) > 0){
                    this.mediaData = Object.assign({}, data);
                    this.mediaEditData = Object.assign({}, this.mediaData);
                }
            });
        },
        updateMediaEditData(key, value) {
            this.mediaEditData[key] = value;
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
