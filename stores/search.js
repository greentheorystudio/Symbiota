const useSearchStore = Pinia.defineStore('search', {
    state: () => ({
        dateId: null,
        queryId: 0,
        queryRecCnt: 0,
        lazyLoadCnt: 20000,
        recordsLazyLoadCnt: 100,
        recordsPageNumber: 1,
        searchRecordData: [],
        searchTerms: {
            surveyonly: false
        },
        selections: [],
        selectionsIds: [],
        solrFields: 'occid,collid,catalogNumber,otherCatalogNumbers,family,sciname,tid,scientificNameAuthorship,identifiedBy,' +
            'dateIdentified,typeStatus,recordedBy,recordNumber,eventDate,displayDate,coll_year,coll_month,coll_day,habitat,associatedTaxa,' +
            'cultivationStatus,country,StateProvince,county,municipality,locality,localitySecurity,localitySecurityReason,geo,minimumElevationInMeters,' +
            'maximumElevationInMeters,labelProject,InstitutionCode,CollectionCode,CollectionName,CollType,thumbnailurl,accFamily'
    }),
    getters: {
        getDateId(state) {
            return state.dateId;
        },
        getDateIdValue() {
            const day = new Date().getDate().toString();
            const month = new Date().getMonth() + 1;
            const year = new Date().getFullYear().toString();
            return day + month + year;
        },
        getDateTimeString() {
            const now = new Date();
            let dateTimeString = now.getFullYear().toString();
            dateTimeString += (((now.getMonth()+1) < 10)?'0':'')+(now.getMonth()+1).toString();
            dateTimeString += ((now.getDate() < 10)?'0':'')+now.getDate().toString();
            dateTimeString += ((now.getHours() < 10)?'0':'')+now.getHours().toString();
            dateTimeString += ((now.getMinutes() < 10)?'0':'')+now.getMinutes().toString();
            dateTimeString += ((now.getSeconds() < 10)?'0':'')+now.getSeconds().toString();
            return dateTimeString;
        },
        getLazyLoadCnt(state) {
            return state.lazyLoadCnt;
        },
        getPaginationFirstRecordNumber(state) {
            let recordNumber = 1;
            if(Number(state.recordsPageNumber) > 1){
                recordNumber = recordNumber + ((Number(state.recordsPageNumber) - 1) * Number(state.recordsLazyLoadCnt));
            }
            return recordNumber;
        },
        getPaginationLastPageNumber(state) {
            let lastPage = 1;
            if(Number(state.queryRecCnt) > Number(state.recordsLazyLoadCnt)){
                lastPage = Math.floor(Number(state.queryRecCnt) / Number(state.recordsLazyLoadCnt));
            }
            if(Number(state.queryRecCnt) % Number(state.recordsLazyLoadCnt)){
                lastPage++;
            }
            return lastPage;
        },
        getPaginationLastRecordNumber(state) {
            let recordNumber = (Number(state.queryRecCnt) > Number(state.recordsLazyLoadCnt)) ? Number(state.recordsLazyLoadCnt) : Number(state.queryRecCnt);
            if(Number(state.queryRecCnt) > Number(state.recordsLazyLoadCnt) && Number(state.recordsPageNumber) > 1){
                if(Number(state.recordsPageNumber) === Number(state.getPaginationLastPageNumber)){
                    recordNumber = (Number(state.queryRecCnt) % Number(state.recordsLazyLoadCnt)) + ((Number(state.recordsPageNumber) - 1) * Number(state.recordsLazyLoadCnt));
                }
                else{
                    recordNumber = Number(state.recordsPageNumber) * Number(state.recordsLazyLoadCnt);
                }
            }
            return recordNumber;
        },
        getPaginationObj(state) {
            return {
                page: state.recordsPageNumber,
                lastPage: state.getPaginationLastPageNumber,
                rowsPerPage: state.recordsLazyLoadCnt,
                firstRowNumber: state.getPaginationFirstRecordNumber,
                lastRowNumber: state.getPaginationLastRecordNumber,
                rowsNumber: Number(state.queryRecCnt)
            };
        },
        getQueryId(state) {
            return state.queryId;
        },
        getQueryRecCnt(state) {
            return Number(state.queryRecCnt);
        },
        getSearchRecordData(state) {
            return state.searchRecordData;
        },
        getSearchRecordDataIdArr(state) {
            const returnArr = [];
            state.searchRecordData.forEach((record) => {
                returnArr.push(Number(record.siteid));
            });
            return returnArr;
        },
        getSearchRecordSelectedCount(state) {
            return state.searchRecordData.filter((record) => {
                return record.selected === true;
            }).length;
        },
        getSearchTerms(state) {
            return state.searchTerms;
        },
        getSearchTermsJson(state) {
            return JSON.stringify(state.searchTerms);
        },
        getSearchTermsValid(state) {
            let populated = false;
            if(state.searchTerms.hasOwnProperty('surveyonly') ||
                state.searchTerms.hasOwnProperty('springid') ||
                state.searchTerms.hasOwnProperty('springname') ||
                state.searchTerms.hasOwnProperty('LandManagerID') ||
                state.searchTerms.hasOwnProperty('LCC') ||
                state.searchTerms.hasOwnProperty('AKA') ||
                state.searchTerms.hasOwnProperty('project') ||
                state.searchTerms.hasOwnProperty('datepicker1') ||
                state.searchTerms.hasOwnProperty('datepicker2') ||
                state.searchTerms.hasOwnProperty('springtype') ||
                state.searchTerms.hasOwnProperty('Country') ||
                state.searchTerms.hasOwnProperty('StateProvince') ||
                state.searchTerms.hasOwnProperty('county') ||
                state.searchTerms.hasOwnProperty('TreatmentAreaID') ||
                state.searchTerms.hasOwnProperty('LandUnit') ||
                state.searchTerms.hasOwnProperty('ProclaimedNF') ||
                state.searchTerms.hasOwnProperty('landunitdetail') ||
                state.searchTerms.hasOwnProperty('quad') ||
                state.searchTerms.hasOwnProperty('huc') ||
                state.searchTerms.hasOwnProperty('distFromMe') ||
                state.searchTerms.hasOwnProperty('upperlat') ||
                state.searchTerms.hasOwnProperty('pointlat') ||
                state.searchTerms.hasOwnProperty('circleArr') ||
                state.searchTerms.hasOwnProperty('polyArr') ||
                state.searchTerms.hasOwnProperty('distFromMeRadiusTemp') ||
                state.searchTerms.hasOwnProperty('distFromMeRadius') ||
                state.searchTerms.hasOwnProperty('distFromMeGroundRadius') ||
                state.searchTerms.hasOwnProperty('distFromMeLat') ||
                state.searchTerms.hasOwnProperty('distFromMeLong')
            ){
                populated = true;
            }
            return populated;
        },
        getSelections(state) {
            return state.selections;
        },
        getSelectionsIds(state) {
            return state.selectionsIds;
        },
        getSOLRFields(state) {
            return state.solrFields;
        },
        getTimestringIdentifier() {
            return Date.now().toString();
        }
    },
    actions: {
        addRecordToSelections(record) {
            this.selections.push(record);
            this.selectionsIds.push(Number(record.siteid));
            const currentRecord = this.searchRecordData.find(obj => Number(obj['siteid']) === Number(record.siteid));
            if(currentRecord){
                currentRecord.selected = true;
            }
        },
        clearLocalStorageSearchTerms() {
            localStorage.removeItem('searchTermsArr');
        },
        clearSearchTerms() {
            for(const key in this.searchTerms){
                delete this.searchTerms[key];
            }
            this.searchTerms['surveyonly'] = false;
            this.updateLocalStorageSearchTerms();
        },
        clearSelections() {
            this.selections.length = 0;
            this.selectionsIds.length = 0;
            this.searchRecordData.forEach((record) => {
                record.selected = false;
            });
        },
        copySearchUrlToClipboard(){
            const currentSearchTerms = Object.assign({}, this.getSearchTerms);
            currentSearchTerms.recordPage = this.recordsPageNumber;
            const searchTermsJson = JSON.stringify(currentSearchTerms);
            let copyUrl = window.location.href + '?starr=' + searchTermsJson.replaceAll("'", '%squot;');
            navigator.clipboard.writeText(copyUrl).then();
        },
        deselectAllCurrentRecords() {
            this.searchRecordData.forEach((record) => {
                if(this.selectionsIds.indexOf(Number(record.siteid)) > -1){
                    this.removeRecordFromSelections(Number(record.siteid));
                }
            });
        },
        initializeSearchStorage(queryId) {
            this.dateId = this.getDateIdValue;
            this.queryId = queryId.toString();
            if(localStorage.hasOwnProperty('searchTermsArr')){
                const stArr = JSON.parse(localStorage['searchTermsArr']);
                if(!stArr.hasOwnProperty(this.dateId.toString())){
                    this.clearLocalStorageSearchTerms();
                    this.setLocalStorageSearchTerms();
                }
            }
            else{
                this.setLocalStorageSearchTerms();
            }
            const stArr = JSON.parse(localStorage['searchTermsArr']);
            if(Number(queryId) === 0 || !stArr.hasOwnProperty(this.dateId.toString())){
                this.queryId = this.getTimestringIdentifier;
                this.setQueryIdInLocalStorageSearchTerms(this.queryId);
            }
            else if(!stArr[this.dateId.toString()].hasOwnProperty(queryId.toString())){
                this.setQueryIdInLocalStorageSearchTerms(queryId);
            }
            else{
                this.searchTerms = Object.assign({}, stArr[this.dateId.toString()][this.queryId.toString()]);
            }
        },
        loadSearchTermsArrFromJson(json) {
            const searchTermsArr = JSON.parse(localStorage['searchTermsArr']);
            const newSearchTerms = JSON.parse(json);
            if(newSearchTerms.hasOwnProperty('recordPage')){
                this.recordsPageNumber = newSearchTerms.recordPage;
                delete newSearchTerms['recordPage'];
            }
            this.searchTerms = Object.assign({}, newSearchTerms);
            searchTermsArr[this.dateId.toString()][this.queryId.toString()] = Object.assign({}, newSearchTerms);
            localStorage.setItem('searchTermsArr', JSON.stringify(searchTermsArr));
        },
        processDownloadRequest(settings){
            if(settings.hasOwnProperty('dlType') && settings.dlType){
                const selection = !!(settings.hasOwnProperty('selection') && settings.selection);
                const filename = 'springdata_' + this.getDateTimeString;
                let contentType = '';
                if(settings.dlType === 'kml') {
                    contentType = 'application/vnd.google-earth.kml+xml';
                }
                else if(settings.dlType === 'geojson') {
                    contentType = 'application/vnd.geo+json';
                }
                else if(settings.dlType === 'gpx') {
                    contentType = 'application/gpx+xml';
                }
                const formData = new FormData();
                formData.append('dh-type', settings.dlType);
                formData.append('dh-filename', filename);
                formData.append('dh-contentType', (settings.dlType === 'csv' ? 'text/csv; charset=utf-8' : contentType));
                formData.append('starrjson', this.getSearchTermsJson);
                if(selection) {
                    formData.append('dh-selections', this.getSelectionsIds.join());
                }
                if(settings.hasOwnProperty('taxaId') && settings.hasOwnProperty('taxaType') && Number(settings.taxaId) > 0 && Number(settings.taxaType) > 0) {
                    formData.append('dh-taxaid', settings.taxaId.toString());
                    formData.append('dh-taxatype', settings.taxaType.toString());
                }
                formData.append('action', 'downloadsearchdata');
                fetch(searchApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.blob() : null;
                })
                .then((blob) => {
                    if(blob !== null){
                        const objectUrl = window.URL.createObjectURL(blob);
                        const anchor = document.createElement('a');
                        anchor.href = objectUrl;
                        anchor.download = filename;
                        document.body.appendChild(anchor);
                        anchor.click();
                        anchor.remove();
                    }
                });
            }
        },
        processGetQueryRecCnt(solrMode, callback){
            this.queryRecCnt = 0;
            const formData = new FormData();
            formData.append('starr', this.getSearchTermsJson);
            if(solrMode){
                formData.append('rows', '0');
                formData.append('start', '0');
                formData.append('wt', 'json');
                fetch(solrConnectorUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    this.queryRecCnt = Number(data['response']['numFound']);
                    callback();
                });
            }
            else{
                formData.append('action', 'getQueryRecCnt');
                fetch(searchApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    this.queryRecCnt = Number(res);
                    callback();
                });
            }
        },
        processGetQueryResultsGeoJson(solrMode, index, finalIndex, callback){
            let startindex = 0;
            if(index > 0) {
                startindex = index * this.lazyLoadCnt;
            }
            const formData = new FormData();
            formData.append('starr', this.getSearchTermsJson);
            formData.append('rows', this.lazyLoadCnt.toString());
            formData.append('start', startindex.toString());
            if(solrMode){
                formData.append('fl', this.getSOLRFields);
                formData.append('wt', 'geojson');
                fetch(solrConnectorUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    callback(res, index, finalIndex);
                });
            }
            else{
                formData.append('action', 'getQueryResultsGeoJson');
                fetch(searchApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    callback(res, index, finalIndex);
                });
            }
        },
        processGetQueryResultsRecordData(index){
            this.searchRecordData = [];
            const formData = new FormData();
            formData.append('starr', this.getSearchTermsJson);
            formData.append('cntperpage', this.recordsLazyLoadCnt.toString());
            formData.append('index', index.toString());
            formData.append('action', 'getQueryResultsRecordData');
            fetch(searchApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.searchRecordData = this.setSelectedRecords(data);
                this.recordsPageNumber = Number(index);
            });
        },
        redirectWithQueryId(url) {
            const baseStore = useBaseStore();
            window.location.href = baseStore.getClientRoot + url + '?queryId=' + this.queryId;
        },
        removeRecordFromSelections(id) {
            const selObj = this.selections.find(obj => Number(obj['siteid']) === Number(id));
            const selObjIndex = this.selections.indexOf(selObj);
            this.selections.splice(selObjIndex, 1);
            const selObjIdIndex = this.selectionsIds.indexOf(Number(id));
            this.selectionsIds.splice(selObjIdIndex, 1);
            const currentRecord = this.searchRecordData.find(obj => Number(obj['siteid']) === Number(id));
            if(currentRecord){
                currentRecord.selected = false;
            }
        },
        selectAllCurrentRecords() {
            this.searchRecordData.forEach((record) => {
                if(this.selectionsIds.indexOf(Number(record.siteid)) < 0){
                    this.addRecordToSelections(record);
                }
            });
        },
        setLocalStorageSearchTerms() {
            const blankSearchTerms = {};
            blankSearchTerms[this.dateId.toString()] = {};
            localStorage.setItem('searchTermsArr', JSON.stringify(blankSearchTerms));
        },
        setQueryIdInLocalStorageSearchTerms(queryId) {
            const stArr = JSON.parse(localStorage['searchTermsArr']);
            stArr[this.dateId.toString()][queryId.toString()] = {};
            localStorage.setItem('searchTermsArr', JSON.stringify(stArr));
        },
        setRecordsLazyLoadCnt(val) {
            this.recordsLazyLoadCnt = Number(val);
        },
        setSelectedRecords(recordArr) {
            recordArr.forEach((record) => {
                record.selected = (this.selectionsIds.indexOf(Number(record.siteid)) > -1);
            });
            return recordArr;
        },
        updateLocalStorageSearchTerms() {
            const stArr = JSON.parse(localStorage['searchTermsArr']);
            stArr[this.dateId.toString()][this.queryId.toString()] = Object.assign({}, this.searchTerms);
            localStorage.setItem('searchTermsArr', JSON.stringify(stArr));
        },
        updateSearchTerms(prop, value) {
            if(value && value !== ''){
                this.searchTerms[prop] = value;
            }
            else{
                delete this.searchTerms[prop];
            }
            this.updateLocalStorageSearchTerms();
        }
    }
});
