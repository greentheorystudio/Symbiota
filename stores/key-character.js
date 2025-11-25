const useKeyCharacterStore = Pinia.defineStore('key-character', {
    state: () => ({
        baseStore: useBaseStore(),
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
        keyCharacterArrData: {},
        keyCharacterData: {},
        keyCharacterDependenceArr: [],
        keyCharacterEditData: {},
        keyCharacterId: 0,
        keyCharacterStateStore: useKeyCharacterStateStore(),
        keyCharacterUpdateData: {}
    }),
    getters: {
        getKeyCharacterArrData(state) {
            return state.keyCharacterArrData;
        },
        getKeyCharacterData(state) {
            return state.keyCharacterEditData;
        },
        getKeyCharacterDependenceArr(state) {
            return state.keyCharacterDependenceArr;
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
        addCharacterDependencyRecord(dcid, dcsid, callback) {
            const formData = new FormData();
            formData.append('cid', this.keyCharacterId.toString());
            formData.append('dcid', dcid.toString());
            formData.append('dcsid', dcsid.toString());
            formData.append('action', 'addCharacterDependencyRecord');
            fetch(keyCharacterApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) > 0){
                    this.setKeyCharacterDependenceArr();
                }
                callback(Number(res));
            });
        },
        clearKeyCharacterArr() {
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
                if(Number(res) > 0){
                    this.keyCharacterId = Number(res);
                    this.keyCharacterData['cid'] = Number(res);
                    this.keyCharacterEditData['cid'] = Number(res);
                }
                callback(Number(res));
            });
        },
        deleteKeyCharacterDependencyRecord(cdid, callback) {
            const formData = new FormData();
            formData.append('cdid', cdid.toString());
            formData.append('action', 'deleteKeyCharacterDependencyRecord');
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
        setCurrentKeyCharacterRecord(chid, cid) {
            this.keyCharacterDependenceArr.length = 0;
            this.keyCharacterStateStore.clearKeyCharacterStateArr();
            this.keyCharacterId = Number(cid);
            if(this.keyCharacterId > 0){
                this.keyCharacterData = Object.assign({}, this.getCurrentKeyCharacterData(chid));
                this.keyCharacterStateStore.setKeyCharacterStateArr(this.keyCharacterId);
                this.setKeyCharacterDependenceArr();
            }
            else{
                this.keyCharacterData = Object.assign({}, this.blankKeyCharacterRecord);
                this.keyCharacterData['language'] = this.baseStore.getDefaultLanguageName;
                this.keyCharacterData['langid'] = this.baseStore.getDefaultLanguageId;
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
        setKeyCharacterDependenceArr() {
            const formData = new FormData();
            formData.append('cid', this.keyCharacterId.toString());
            formData.append('action', 'getKeyCharacterDependenceArr');
            fetch(keyCharacterApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.keyCharacterDependenceArr = data[this.keyCharacterId.toString()];
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
