const useProjectStore = Pinia.defineStore('project', {
    state: () => ({
        blankProjectRecord: {
            pid: 0,
            projname: null,
            displayname: null,
            managers: null,
            briefdescription: null,
            fulldescription: null,
            notes: null,
            iconurl: null,
            headerurl: null,
            occurrencesearch: null,
            ispublic: null,
            dynamicproperties: null,
            parentpid: null,
            sortsequence: null
        },
        projectChecklistArr: [],
        projectData: {},
        projectEditData: {},
        projectId: 0,
        projectUpdateData: {}
    }),
    getters: {
        getProjectChecklistArr(state) {
            return state.projectChecklistArr;
        },
        getProjectChecklistCoordArr(state) {
            const returnArr = [];
            state.projectChecklistArr.forEach(checklist => {
                if(checklist['latcentroid'] && checklist['longcentroid']){
                    returnArr.push([Number(checklist['longcentroid']), Number(checklist['latcentroid'])]);
                }
            });
            return returnArr;
        },
        getProjectData(state) {
            return state.projectEditData;
        },
        getProjectEditsExist(state) {
            let exist = false;
            state.projectUpdateData = Object.assign({}, {});
            for(let key in state.projectEditData) {
                if(state.projectEditData.hasOwnProperty(key) && state.projectEditData[key] !== state.projectData[key]) {
                    exist = true;
                    state.projectUpdateData[key] = state.projectEditData[key];
                }
            }
            return exist;
        },
        getProjectID(state) {
            return state.projectId;
        },
        getProjectValid(state) {
            return !!state.projectEditData['projname'];
        }
    },
    actions: {
        clearProjectData() {
            this.projectId = 0;
            this.projectData = Object.assign({}, this.blankProjectRecord);
        },
        createProjectRecord(callback) {
            const formData = new FormData();
            formData.append('project', JSON.stringify(this.projectEditData));
            formData.append('action', 'createProjectRecord');
            fetch(projectApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) > 0){
                    this.setProject(Number(res));
                }
            });
        },
        deleteProjectRecord(pid, callback) {
            const formData = new FormData();
            formData.append('pid', pid.toString());
            formData.append('action', 'deleteProjectRecord');
            fetch(projectApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                this.setProject(0);
                callback(Number(res));
            });
        },
        getProjectListByUid(uid, callback) {
            const formData = new FormData();
            formData.append('uid', uid.toString());
            formData.append('action', 'getProjectListByUid');
            fetch(projectApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                callback(resObj);
            });
        },
        setProject(pid, callback = null) {
            this.clearProjectData();
            if(Number(pid) > 0){
                this.projectEditData = Object.assign({}, {});
                const formData = new FormData();
                formData.append('pid', pid.toString());
                formData.append('action', 'getProjectData');
                fetch(projectApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((resObj) => {
                    if(resObj.hasOwnProperty('pid') && Number(resObj['pid']) === Number(pid)){
                        this.projectId = Number(pid);
                        this.projectData = Object.assign({}, resObj);
                        this.projectEditData = Object.assign({}, this.projectData);
                        this.setProjectChecklistArr();
                    }
                    if(callback){
                        callback(this.projectId);
                    }
                });
            }
            else{
                this.projectEditData = Object.assign({}, this.projectData);
                if(callback){
                    callback();
                }
            }
        },
        setProjectChecklistArr() {
            const formData = new FormData();
            formData.append('pid', this.projectId.toString());
            formData.append('action', 'getProjectChecklists');
            fetch(projectApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.projectChecklistArr = data;
            });
        },
        updateProjectEditData(key, value) {
            this.projectEditData[key] = value;
        },
        updateProjectRecord(callback) {
            const formData = new FormData();
            formData.append('pid', this.projectId.toString());
            formData.append('projectData', JSON.stringify(this.projectUpdateData));
            formData.append('action', 'updateProjectRecord');
            fetch(projectApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.projectData = Object.assign({}, this.projectEditData);
                }
            });
        }
    }
});
