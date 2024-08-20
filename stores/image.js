const useImageStore = Pinia.defineStore('image', {
    state: () => ({
        blankImageRecord: {
            imgid: 0,
            tid: null,
            url: null,
            thumbnailurl: null,
            originalurl: null,
            photographer: null,
            photographeruid: null,
            format: null,
            caption: null,
            owner: null,
            sourceurl: null,
            referenceurl: null,
            copyright: null,
            rights: null,
            locality: null,
            occid: null,
            notes: null,
            anatomy: null,
            username: null,
            sourceidentifier: null,
            mediamd5: null,
            dynamicproperties: null,
            sortsequence: null,
            tagArr: []
        },
        imageArr: [],
        imageData: {},
        imageEditData: {},
        imageFields: {},
        imageId: 0,
        imageUpdateData: {}
    }),
    getters: {
        getBlankImageRecord(state) {
            return state.blankImageRecord;
        },
        getImageArr(state) {
            return state.imageArr;
        },
        getImageCount(state) {
            return state.imageArr.length;
        },
        getImageData(state) {
            return state.imageEditData;
        },
        getImageEditsExist(state) {
            let exist = false;
            state.imageUpdateData = Object.assign({}, {});
            for(let key in state.imageEditData) {
                if(key === 'tagArr'){
                    if(state.imageEditData[key].length !== state.imageData[key].length){
                        exist = true;
                        state.imageUpdateData[key] = state.imageEditData[key];
                    }
                    else if(state.imageData[key].length > 0){
                        state.imageData[key].forEach(tag => {
                            if(!state.imageEditData[key].includes(tag)){
                                exist = true;
                                state.imageUpdateData[key] = state.imageEditData[key];
                            }
                        });
                    }
                }
                else{
                    if(state.imageEditData.hasOwnProperty(key) && state.imageEditData[key] !== state.imageData[key]) {
                        exist = true;
                        state.imageUpdateData[key] = state.imageEditData[key];
                    }
                }
            }
            return exist;
        },
        getImageFields(state) {
            return state.imageFields;
        },
        getImageID(state) {
            return state.imageId;
        },
        getImageValid(state) {
            return !!state.imageEditData['url'];
        }
    },
    actions: {
        clearImageArr() {
            this.imageArr.length = 0;
        },
        clearImageData() {
            this.imageData = Object.assign({}, this.blankImageRecord);
            this.imageEditData = Object.assign({}, {});
        },
        setCurrentImageRecord(imgid) {
            this.imageId = Number(imgid);
            this.clearImageData();
            if(this.imageId > 0){
                this.setImageData();
            }
        },
        setImageArr(property, value) {
            const formData = new FormData();
            formData.append('property', property);
            formData.append('value', value.toString());
            formData.append('action', 'getImageArrByProperty');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.imageArr = data;
            });
        },
        setImageData() {
            const formData = new FormData();
            formData.append('imgid', this.imageId.toString());
            formData.append('action', 'getImageData');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('imgid') && Number(data.imgid) > 0){
                    this.imageData = Object.assign({}, data);
                    this.imageEditData = Object.assign({}, this.imageData);
                }
            });
        },
        updateImageEditData(key, value) {
            this.imageEditData[key] = value;
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
