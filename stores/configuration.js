const useConfigurationStore = Pinia.defineStore('configuration', {
    state: () => ({
        configurationAddData: {},
        configurationData: {},
        configurationEditData: {},
        configurationUpdateData: {},
        defaultMapSymbology: {
            'SPATIAL_DRAGDROP_BORDER_COLOR': '#000000',
            'SPATIAL_DRAGDROP_BORDER_WIDTH': '2',
            'SPATIAL_DRAGDROP_FILL_COLOR': '#AAAAAA',
            'SPATIAL_DRAGDROP_OPACITY': '0.3',
            'SPATIAL_DRAGDROP_POINT_RADIUS': '5',
            'SPATIAL_DRAGDROP_RASTER_COLOR_SCALE': 'earth',
            'SPATIAL_POINT_BORDER_COLOR': '#000000',
            'SPATIAL_POINT_BORDER_WIDTH': '1',
            'SPATIAL_POINT_CLUSTER': '1',
            'SPATIAL_POINT_CLUSTER_DISTANCE': '50',
            'SPATIAL_POINT_DISPLAY_HEAT_MAP': '0',
            'SPATIAL_POINT_FILL_COLOR': '#E69E67',
            'SPATIAL_POINT_HEAT_MAP_BLUR': '15',
            'SPATIAL_POINT_HEAT_MAP_RADIUS': '5',
            'SPATIAL_POINT_POINT_RADIUS': '7',
            'SPATIAL_POINT_SELECTIONS_BORDER_COLOR': '#10D8E6',
            'SPATIAL_POINT_SELECTIONS_BORDER_WIDTH': '2',
            'SPATIAL_SHAPES_BORDER_COLOR': '#3399CC',
            'SPATIAL_SHAPES_BORDER_WIDTH': '2',
            'SPATIAL_SHAPES_FILL_COLOR': '#FFFFFF',
            'SPATIAL_SHAPES_OPACITY': '0.4',
            'SPATIAL_SHAPES_POINT_RADIUS': '5',
            'SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR': '#0099FF',
            'SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH': '5',
            'SPATIAL_SHAPES_SELECTIONS_FILL_COLOR': '#FFFFFF',
            'SPATIAL_SHAPES_SELECTIONS_OPACITY': '0.5'
        }
    }),
    getters: {
        getAdditionalConfigurationData(state) {
            return state.configurationData.hasOwnProperty('additional') ? state.configurationData['additional'] : {};
        },
        getConfigurationData(state) {
            return state.configurationEditData;
        },
        getConfigurationEditsExist(state) {
            let exist = false;
            state.configurationAddData = Object.assign({}, {});
            state.configurationUpdateData = Object.assign({}, {});
            for(let key in state.configurationEditData) {
                if(state.configurationEditData.hasOwnProperty(key) && state.configurationEditData[key] !== state.configurationData['core'][key]) {
                    exist = true;
                    if(state.configurationData['core'].hasOwnProperty(key)){
                        state.configurationUpdateData[key] = state.configurationEditData[key];
                    }
                    else{
                        state.configurationAddData[key] = state.configurationEditData[key];
                    }
                }
            }
            return exist;
        },
        getCoreConfigurationData(state) {
            return state.configurationData.hasOwnProperty('core') ? state.configurationData['core'] : {};
        },
        getMapSymbologyData(state) {
            const returnObj = {};
            Object.keys(state.defaultMapSymbology).forEach((prop) => {
                returnObj[prop] = state.configurationEditData.hasOwnProperty(prop) ? state.configurationEditData[prop] : state.defaultMapSymbology[prop];
            });
            return returnObj;
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
        addConfigurationValueDataObj(dataObj, callback) {
            const formData = new FormData();
            formData.append('data', JSON.stringify(dataObj));
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
                if(res && Number(res) === 1 && Object.keys(this.configurationAddData).length === 0){
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
                this.configurationEditData = this.configurationData.hasOwnProperty('core') ? Object.assign({}, this.configurationData['core']) : Object.assign({}, this.configurationData);
            });
        },
        setDefaultSymbologyData(callback) {
            Object.keys(this.defaultMapSymbology).forEach((prop) => {
                this.updateConfigurationEditData(prop, this.defaultMapSymbology[prop]);
            });
            this.updateConfigurationData((res) => {
                callback(Number(res));
            });
        },
        updateConfigurationData(callback) {
            if(this.getConfigurationEditsExist){
                if(Object.keys(this.configurationAddData).length > 0){
                    this.addConfigurationValueDataObj(this.configurationAddData, (res) => {
                        if(Object.keys(this.configurationUpdateData).length === 0 || Number(res) === 0){
                            callback(Number(res));
                            if(Number(res) === 1){
                                this.configurationData['core'] = Object.assign({}, this.configurationEditData);
                            }
                        }
                        else{
                            this.updateConfigurationValueDataObj(this.configurationUpdateData, (res) => {
                                callback(Number(res));
                                if(Number(res) === 1){
                                    this.configurationData['core'] = Object.assign({}, this.configurationEditData);
                                }
                            });
                        }
                    });
                }
                else{
                    this.updateConfigurationValueDataObj(this.configurationUpdateData, (res) => {
                        callback(Number(res));
                        if(Number(res) === 1){
                            this.configurationData['core'] = Object.assign({}, this.configurationEditData);
                        }
                    });
                }
            }
        },
        updateConfigurationEditData(key, value) {
            this.configurationEditData[key] = value;
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
