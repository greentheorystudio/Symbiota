const useKeyCharacterHeadingStore = Pinia.defineStore('key-character-heading', {
    state: () => ({
        baseStore: useBaseStore(),
        blankKeyCharacterHeadingRecord: {
            chid: 0,
            headingname: null,
            language: null,
            langid: null,
            sortsequence: null
        },
        keyCharacterHeadingArr: [],
        keyCharacterHeadingData: {},
        keyCharacterHeadingEditData: {},
        keyCharacterHeadingId: 0,
        keyCharacterHeadingIdArr: [],
        keyCharacterHeadingUpdateData: {},
        keyCharacterStore: useKeyCharacterStore()
    }),
    getters: {
        getKeyCharacterHeadingArr(state) {
            return state.keyCharacterHeadingArr;
        },
        getKeyCharacterHeadingData(state) {
            return state.keyCharacterHeadingEditData;
        },
        getKeyCharacterHeadingEditsExist(state) {
            let exist = false;
            state.keyCharacterHeadingUpdateData = Object.assign({}, {});
            for(let key in state.keyCharacterHeadingEditData) {
                if(state.keyCharacterHeadingEditData.hasOwnProperty(key) && state.keyCharacterHeadingEditData[key] !== state.keyCharacterHeadingData[key]) {
                    exist = true;
                    state.keyCharacterHeadingUpdateData[key] = state.keyCharacterHeadingEditData[key];
                }
            }
            return exist;
        },
        getKeyCharacterHeadingIDArr(state) {
            const returnArr = [];
            state.keyCharacterHeadingArr.forEach(heading => {
                returnArr.push(Number(heading['chid']));
            });
            return returnArr;
        },
        getKeyCharacterHeadingValid(state) {
            return (state.keyCharacterHeadingEditData['headingname'] && state.keyCharacterHeadingEditData['language']);
        }
    },
    actions: {
        clearKeyCharacterHeadingArr() {
            this.keyCharacterHeadingArr.length = 0;
            this.keyCharacterStore.clearKeyCharacterArr();
        },
        createKeyCharacterHeadingRecord(callback) {
            const formData = new FormData();
            formData.append('heading', JSON.stringify(this.keyCharacterHeadingEditData));
            formData.append('action', 'createKeyCharacterHeadingRecord');
            fetch(keyCharacterHeadingApiUrl, {
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
        deleteKeyCharacterHeadingRecord(callback) {
            const formData = new FormData();
            formData.append('chid', this.keyCharacterHeadingId.toString());
            formData.append('action', 'deleteKeyCharacterHeadingRecord');
            fetch(keyCharacterHeadingApiUrl, {
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
        getCurrentKeyCharacterHeadingData() {
            return this.keyCharacterHeadingArr.find(heading => Number(heading.chid) === this.keyCharacterHeadingId);
        },
        setCurrentKeyCharacterHeadingRecord(chid) {
            this.keyCharacterHeadingId = Number(chid);
            if(this.keyCharacterHeadingId > 0){
                this.keyCharacterHeadingData = Object.assign({}, this.getCurrentKeyCharacterHeadingData());
            }
            else{
                this.keyCharacterHeadingData = Object.assign({}, this.blankKeyCharacterHeadingRecord);
                this.keyCharacterHeadingData['language'] = this.baseStore.getDefaultLanguageName;
                this.keyCharacterHeadingData['langid'] = this.baseStore.getDefaultLanguageId;
            }
            this.keyCharacterHeadingEditData = Object.assign({}, this.keyCharacterHeadingData);
        },
        setKeyCharacterHeadingArr(language = null) {
            this.clearKeyCharacterHeadingArr();
            const formData = new FormData();
            formData.append('language', (language ? language : ''));
            formData.append('action', 'getKeyCharacterHeadingsArr');
            fetch(keyCharacterHeadingApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.keyCharacterHeadingArr = data;
                if(this.keyCharacterHeadingArr.length > 0){
                    this.keyCharacterStore.setKeyCharacterArrData(this.getKeyCharacterHeadingIDArr);
                }
            });
        },
        updateKeyCharacterHeadingEditData(key, value) {
            this.keyCharacterHeadingEditData[key] = value;
        },
        updateKeyCharacterHeadingRecord(callback) {
            const formData = new FormData();
            formData.append('chid', this.keyCharacterHeadingId.toString());
            formData.append('headingData', JSON.stringify(this.keyCharacterHeadingUpdateData));
            formData.append('action', 'updateKeyCharacterHeadingRecord');
            fetch(keyCharacterHeadingApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.keyCharacterHeadingData = Object.assign({}, this.keyCharacterHeadingEditData);
                }
            });
        }
    }
});
