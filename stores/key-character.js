const useKeyCharacterStore = Pinia.defineStore('key-character', {
    state: () => ({
        blankKeyCharacterRecord: {
            cid: 0,
            chid: null,
            charactername: null,
            description: null,
            infourl: null,
            language: null,
            langid: null,
            sortsequence: null
        },
        currentKeyCharacterArr: [],
        keyCharacterArrData: {},
        keyCharacterData: {},
        keyCharacterEditData: {},
        keyCharacterId: 0,
        keyCharacterStateStore: useKeyCharacterStateStore(),
        keyCharacterUpdateData: {}
    }),
    getters: {
        getCurrentKeyCharacterArr(state) {
            return state.currentKeyCharacterArr;
        },
        getKeyCharacterArrData(state) {
            return state.keyCharacterArrData;
        },
        getKeyCharacterData(state) {
            return state.keyCharacterEditData;
        },
        getKeyCharacterEditsExist(state) {
            let exist = false;
            state.keyCharacterUpdateData = Object.assign({}, {});
            for(let key in state.keyCharacterEditData) {
                if(state.keyCharacterEditData.hasOwnProperty(key) && state.keyCharacterEditData[key] !== state.keyCharacterData[key]) {
                    exist = true;
                    state.keyCharacterUpdateData[key] = state.keyCharacterEditData[key];
                }
            }
            return exist;
        },
        getKeyCharacterID(state) {
            return state.keyCharacterId;
        },
        getKeyCharacterValid(state) {
            return (state.keyCharacterEditData['charactername'] && state.keyCharacterEditData['language']);
        }
    },
    actions: {
        clearKeyCharacterArr() {
            this.currentKeyCharacterArr.length = 0;
            this.keyCharacterArrData = Object.assign({}, {});
            this.keyCharacterStateStore.clearKeyCharacterStateArr();
        },
        createKeyCharacterRecord(callback) {
            const formData = new FormData();
            formData.append('character', JSON.stringify(this.keyCharacterEditData));
            formData.append('action', 'createKeyCharacterRecord');
            fetch(keyCharacterApiUrl, {
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
        deleteKeyCharacterRecord(callback) {
            const formData = new FormData();
            formData.append('cid', this.keyCharacterId.toString());
            formData.append('action', 'deleteKeyCharacterRecord');
            fetch(keyCharacterApiUrl, {
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
        getCurrentKeyCharacterData(chid) {
            return this.keyCharacterArrData[chid].find(character => Number(character.cid) === this.keyCharacterId);
        },
        setCurrentKeyCharacterArr(chid) {
            this.currentKeyCharacterArr = this.keyCharacterArrData.hasOwnProperty(chid) ? this.keyCharacterArrData[chid] : [];
        },
        setCurrentKeyCharacterRecord(chid, cid) {
            this.keyCharacterStateStore.clearKeyCharacterStateArr();
            this.keyCharacterId = Number(cid);
            if(this.keyCharacterId > 0){
                this.keyCharacterData = Object.assign({}, this.getCurrentKeyCharacterData(chid));
                this.keyCharacterStateStore.setKeyCharacterStateArr(this.keyCharacterId);
            }
            else{
                this.keyCharacterData = Object.assign({}, this.blankKeyCharacterRecord);
            }
            this.keyCharacterEditData = Object.assign({}, this.keyCharacterData);
        },
        setKeyCharacterArrData(chidArr) {
            const formData = new FormData();
            formData.append('chidArr', JSON.stringify(chidArr));
            formData.append('action', 'getKeyCharactersArrByChidArr');
            fetch(keyCharacterApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.keyCharacterArrData = Object.assign({}, data);
            });
        },
        updateKeyCharacterEditData(key, value) {
            this.keyCharacterEditData[key] = value;
        },
        updateKeyCharacterRecord(callback) {
            const formData = new FormData();
            formData.append('cid', this.keyCharacterId.toString());
            formData.append('characterData', JSON.stringify(this.keyCharacterUpdateData));
            formData.append('action', 'updateKeyCharacterRecord');
            fetch(keyCharacterApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.keyCharacterData = Object.assign({}, this.keyCharacterEditData);
                }
            });
        }
    }
});
