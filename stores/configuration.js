const useConfigurationStore = Pinia.defineStore('configuration', {
    state: () => ({
        configurationData: {}
    }),
    getters: {
        getAdditionalConfigurationData(state) {
            return state.configurationData.hasOwnProperty('additional') ? state.configurationData['additional'] : {};
        },
        getConfigurationData(state) {
            return state.configurationData;
        },
        getCoreConfigurationData(state) {
            return state.configurationData.hasOwnProperty('core') ? state.configurationData['core'] : {};
        },
        getServerData(state) {
            return state.configurationData.hasOwnProperty('server') ? state.configurationData['server'] : {};
        }
    },
    actions: {
        addConfigurationValue(configName, value, callback) {
            const newData = {};
            newData[configName] = value;
            const formData = new FormData();
            formData.append('data', JSON.stringify(newData));
            formData.append('action', 'addConfigurationArr');
            fetch(configurationsApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.setConfigurationData();
                }
            });
        },
        deleteConfigurationValue(configName, callback) {
            const newData = {};
            newData[configName] = null;
            const formData = new FormData();
            formData.append('data', JSON.stringify(newData));
            formData.append('action', 'deleteConfigurationArr');
            fetch(configurationsApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.setConfigurationData();
                }
            });
        },
        setConfigurationData() {
            const formData = new FormData();
            formData.append('action', 'getConfigurationData');
            fetch(configurationsApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resData) => {
                this.configurationData = Object.assign({}, resData);
            });
        },
        updateConfigurationValue(configName, value, callback) {
            const newData = {};
            newData[configName] = value;
            const formData = new FormData();
            formData.append('data', JSON.stringify(newData));
            formData.append('action', 'updateConfigurationValueArr');
            fetch(configurationsApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.setConfigurationData();
                }
            });
        },
        updateConfigurationValueDataObj(dataObj, callback) {
            const formData = new FormData();
            formData.append('data', JSON.stringify(dataObj));
            formData.append('action', 'updateConfigurationValueArr');
            fetch(configurationsApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.setConfigurationData();
                }
            });
        },
        updateCssVersion(callback) {
            const formData = new FormData();
            formData.append('action', 'updateCss');
            fetch(configurationsApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.setConfigurationData();
                }
            });
        },
        validateClientPath(value, callback) {
            const formData = new FormData();
            formData.append('value', value);
            formData.append('action', 'validateClientPath');
            fetch(configurationsApiUrl, {
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
        validateNameCore(value, callback) {
            const formData = new FormData();
            formData.append('value', value);
            formData.append('action', 'validateNameCore');
            fetch(configurationsApiUrl, {
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
        validateNameExisting(value, callback) {
            const formData = new FormData();
            formData.append('value', value);
            formData.append('action', 'validateNameExisting');
            fetch(configurationsApiUrl, {
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
        validateServerPath(value, callback) {
            const formData = new FormData();
            formData.append('value', value);
            formData.append('action', 'validateServerPath');
            fetch(configurationsApiUrl, {
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
        validateServerWritePath(value, callback) {
            const formData = new FormData();
            formData.append('value', value);
            formData.append('action', 'validateServerWritePath');
            fetch(configurationsApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
            });
        }
    }
});
