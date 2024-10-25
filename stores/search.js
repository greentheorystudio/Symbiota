const useSearchStore = Pinia.defineStore('search', {
    state: () => ({
        baseStore: useBaseStore(),
        blankSearchTerms: {
            taxontype: '1',
            usethes: true,
            othercatnum: true,
            typestatus: false,
            hasaudio: false,
            hasimages: false,
            hasvideo: false,
            hasmedia: false,
            hasgenetic: false
        },
        dateId: null,
        queryId: 0,
        queryRecCnt: 0,
        searchRecordData: [],
        searchTerms: {},
        searchTermsPageNumber: 0,
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
        getQueryId(state) {
            return state.queryId;
        },
        getSearchRecCnt(state) {
            return Number(state.queryRecCnt);
        },
        getSearchRecordData(state) {
            return state.searchRecordData;
        },
        getSearchRecordDataIdArr(state) {
            const returnArr = [];
            state.searchRecordData.forEach((record) => {
                returnArr.push(Number(record.occid));
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
        getSearchTermsPageNumber(state) {
            return state.searchTermsPageNumber;
        },
        getSearchTermsValid(state) {
            let populated = false;
            if(state.searchTerms.hasOwnProperty('db') ||
                state.searchTerms.hasOwnProperty('clid') ||
                state.searchTerms.hasOwnProperty('taxa') ||
                state.searchTerms.hasOwnProperty('country') ||
                state.searchTerms.hasOwnProperty('state') ||
                state.searchTerms.hasOwnProperty('county') ||
                state.searchTerms.hasOwnProperty('local') ||
                state.searchTerms.hasOwnProperty('elevlow') ||
                state.searchTerms.hasOwnProperty('elevhigh') ||
                state.searchTerms.hasOwnProperty('collector') ||
                state.searchTerms.hasOwnProperty('collnum') ||
                state.searchTerms.hasOwnProperty('eventdate1') ||
                state.searchTerms.hasOwnProperty('eventdate2') ||
                state.searchTerms.hasOwnProperty('occurrenceRemarks') ||
                state.searchTerms.hasOwnProperty('catnum') ||
                state.searchTerms.hasOwnProperty('typestatus') ||
                state.searchTerms.hasOwnProperty('hasaudio') ||
                state.searchTerms.hasOwnProperty('hasimages') ||
                state.searchTerms.hasOwnProperty('hasvideo') ||
                state.searchTerms.hasOwnProperty('hasmedia') ||
                state.searchTerms.hasOwnProperty('hasgenetic') ||
                state.searchTerms.hasOwnProperty('upperlat') ||
                state.searchTerms.hasOwnProperty('pointlat') ||
                state.searchTerms.hasOwnProperty('circleArr') ||
                state.searchTerms.hasOwnProperty('phuid') ||
                state.searchTerms.hasOwnProperty('imagetag') ||
                state.searchTerms.hasOwnProperty('imagekeyword') ||
                state.searchTerms.hasOwnProperty('uploaddate1') ||
                state.searchTerms.hasOwnProperty('uploaddate2') ||
                state.searchTerms.hasOwnProperty('polyArr')
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
            this.selectionsIds.push(Number(record.occid));
            const currentRecord = this.searchRecordData.find(obj => Number(obj['occid']) === Number(record.occid));
            if(currentRecord){
                currentRecord.selected = true;
            }
        },
        clearLocalStorageSearchTerms() {
            localStorage.removeItem('searchTermsArr');
        },
        clearSearchTerms() {
            this.searchTerms = Object.assign({}, this.blankSearchTerms);
            this.updateLocalStorageSearchTerms();
        },
        clearSelections() {
            this.selections.length = 0;
            this.selectionsIds.length = 0;
            this.searchRecordData.forEach((record) => {
                record.selected = false;
            });
        },
        copySearchUrlToClipboard(index){
            const currentSearchTerms = Object.assign({}, this.getSearchTerms);
            currentSearchTerms.recordPage = index;
            const searchTermsJson = JSON.stringify(currentSearchTerms);
            let copyUrl = window.location.href + '?starr=' + searchTermsJson.replaceAll("'", '%squot;');
            navigator.clipboard.writeText(copyUrl).then();
        },
        deselectAllCurrentRecords() {
            this.searchRecordData.forEach((record) => {
                if(this.selectionsIds.indexOf(Number(record.occid)) > -1){
                    this.removeRecordFromSelections(Number(record.occid));
                }
            });
        },
        initializeSearchStorage(queryId) {
            this.dateId = this.getDateIdValue;
            this.queryId = queryId.toString();
            this.searchTerms = Object.assign({}, this.blankSearchTerms);
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
                this.searchTermsPageNumber = newSearchTerms.recordPage;
                delete newSearchTerms['recordPage'];
            }
            this.searchTerms = Object.assign({}, newSearchTerms);
            searchTermsArr[this.dateId.toString()][this.queryId.toString()] = Object.assign({}, newSearchTerms);
            localStorage.setItem('searchTermsArr', JSON.stringify(searchTermsArr));
        },
        processDownloadRequest(options, callback){
            options.filename = 'occurrence_data_' + (options.type === 'zip' ? 'DwCA_' : '') + this.getDateTimeString + '.' + options.type;
            const formData = new FormData();
            if(options.selections){
                formData.append('starr', JSON.stringify({
                    occid: this.getSelectionsIds
                }));
            }
            else{
                formData.append('starr', this.getSearchTermsJson);
            }
            formData.append('options', JSON.stringify(options));
            fetch(dataDownloadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.blob() : null;
            })
            .then((blob) => {
                callback(options.filename, blob);
            });
        },
        processSearch(options, callback){
            const formData = new FormData();
            formData.append('starr', this.getSearchTermsJson);
            if(this.baseStore.getSolrMode){
                let startindex = 0;
                if(index > 0) {
                    startindex = index * options.numRows;
                }
                formData.append('rows', options.numRows.toString());
                formData.append('start', startindex.toString());
                formData.append('fl', this.getSOLRFields);
                formData.append('wt', 'geojson');
                fetch(solrConnectorUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    callback(data, options.index, options.numRows);
                });
            }
            else{
                formData.append('options', JSON.stringify(options));
                formData.append('action', 'processSearch');
                fetch(searchServiceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    callback(data, options.index, options.numRows);
                });
            }
        },
        processSimpleSearch(starr, options, callback){
            const formData = new FormData();
            formData.append('starr', JSON.stringify(starr));
            formData.append('options', JSON.stringify(options));
            formData.append('action', 'processSearch');
            fetch(searchServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                callback(data);
            });
        },
        redirectWithQueryId(url) {
            const baseStore = useBaseStore();
            window.location.href = baseStore.getClientRoot + url + '?queryId=' + this.queryId;
        },
        removeRecordFromSelections(id) {
            const selObj = this.selections.find(obj => Number(obj['occid']) === Number(id));
            const selObjIndex = this.selections.indexOf(selObj);
            this.selections.splice(selObjIndex, 1);
            const selObjIdIndex = this.selectionsIds.indexOf(Number(id));
            this.selectionsIds.splice(selObjIdIndex, 1);
            const currentRecord = this.searchRecordData.find(obj => Number(obj['occid']) === Number(id));
            if(currentRecord){
                currentRecord.selected = false;
            }
        },
        selectAllCurrentRecords() {
            this.searchRecordData.forEach((record) => {
                if(this.selectionsIds.indexOf(Number(record.occid)) < 0){
                    this.addRecordToSelections(record);
                }
            });
        },
        setLocalStorageSearchTerms() {
            const newBlankSearchTerms = {};
            newBlankSearchTerms[this.dateId.toString()] = {};
            localStorage.setItem('searchTermsArr', JSON.stringify(newBlankSearchTerms));
        },
        setQueryIdInLocalStorageSearchTerms(queryId) {
            const stArr = JSON.parse(localStorage['searchTermsArr']);
            stArr[this.dateId.toString()][queryId.toString()] = {};
            localStorage.setItem('searchTermsArr', JSON.stringify(stArr));
        },
        setSearchRecCnt(options, callback){
            this.queryRecCnt = 0;
            const formData = new FormData();
            formData.append('starr', this.getSearchTermsJson);
            if(this.baseStore.getSolrMode){
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
                formData.append('options', JSON.stringify(options));
                formData.append('action', 'getSearchRecCnt(');
                fetch(searchServiceApiUrl, {
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
        setSearchRecordData(options) {
            this.processSearch(options, (res) => {
                this.searchRecordData = this.setSelectedRecords(res);
            });
        },
        setSelectedRecords(recordArr) {
            recordArr.forEach((record) => {
                record.selected = (this.selectionsIds.indexOf(Number(record.occid)) > -1);
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
