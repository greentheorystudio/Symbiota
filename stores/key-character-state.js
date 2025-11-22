const useKeyCharacterStateStore = Pinia.defineStore('key-character-state', {
    state: () => ({
        blankKeyCharacterStateRecord: {
            csid: 0,
            cid: null,
            characterstatename: null,
            description: null,
            infourl: null,
            language: null,
            langid: null,
            sortsequence: null
        },
        keyCharacterStateArr: [],
        keyCharacterStateData: {},
        keyCharacterStateEditData: {},
        keyCharacterStateId: 0,
        keyCharacterStateUpdateData: {}
    }),
    getters: {
        getKeyCharacterStateArr(state) {
            return state.keyCharacterStateArr;
        },
        getKeyCharacterStateData(state) {
            return state.keyCharacterStateEditData;
        },
        getKeyCharacterStateEditsExist(state) {
            let exist = false;
            state.keyCharacterStateUpdateData = Object.assign({}, {});
            for(let key in state.keyCharacterStateEditData) {
                if(state.keyCharacterStateEditData.hasOwnProperty(key) && state.keyCharacterStateEditData[key] !== state.keyCharacterStateData[key]) {
                    exist = true;
                    state.keyCharacterStateUpdateData[key] = state.keyCharacterStateEditData[key];
                }
            }
            return exist;
        },
        getKeyCharacterStateID(state) {
            return state.keyCharacterStateId;
        },
        getKeyCharacterStateValid(state) {
            return (state.keyCharacterStateEditData['characterstatename'] && state.keyCharacterStateEditData['language']);
        }
    },
    actions: {
        clearKeyCharacterStateArr() {
            this.keyCharacterStateArr.length = 0;
        },
        createKeyCharacterStateRecord(callback) {
            const formData = new FormData();
            formData.append('characterState', JSON.stringify(this.keyCharacterStateEditData));
            formData.append('action', 'createKeyCharacterStateRecord');
            fetch(keyCharacterStateApiUrl, {
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
        deleteKeyCharacterStateRecord(callback) {
            const formData = new FormData();
            formData.append('csid', this.keyCharacterStateId.toString());
            formData.append('action', 'deleteKeyCharacterStateRecord');
            fetch(keyCharacterStateApiUrl, {
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
        getCurrentKeyCharacterStateData() {
            return this.keyCharacterStateArr.find(cs => Number(cs.csid) === this.keyCharacterStateId);
        },
        setCurrentKeyCharacterStateRecord(csid) {
            this.keyCharacterStateId = Number(csid);
            if(this.keyCharacterStateId > 0){
                this.keyCharacterStateData = Object.assign({}, this.getCurrentKeyCharacterStateData());
            }
            else{
                this.keyCharacterStateData = Object.assign({}, this.blankKeyCharacterStateRecord);
            }
            this.keyCharacterStateEditData = Object.assign({}, this.keyCharacterStateData);
        },
        setKeyCharacterStateArr(cid) {
            const formData = new FormData();
            formData.append('cid', cid.toString());
            formData.append('action', 'getKeyCharacterStatesArrFromCid');
            fetch(keyCharacterStateApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.keyCharacterStateArr = data;
            });
        },
        updateKeyCharacterStateEditData(key, value) {
            this.keyCharacterStateEditData[key] = value;
        },
        updateKeyCharacterStateRecord(callback) {
            const formData = new FormData();
            formData.append('csid', this.keyCharacterStateId.toString());
            formData.append('characterStateData', JSON.stringify(this.keyCharacterStateUpdateData));
            formData.append('action', 'updateKeyCharacterStateRecord');
            fetch(keyCharacterStateApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.keyCharacterStateData = Object.assign({}, this.keyCharacterStateEditData);
                }
            });
        }
    }
});
