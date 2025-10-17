const useSearchStore = Pinia.defineStore('search', {
    state: () => ({
        baseStore: useBaseStore(),
        blankSearchTerms: {
            collid: 0,
            db: [],
            taxontype: '1',
            usethes: true,
            othercatnum: true,
            typestatus: false,
            hasaudio: false,
            hasimages: false,
            hasvideo: false,
            hasmedia: false,
            hasgenetic: false,
            withoutimages: false,
            radiusval: null,
            radiusunit: 'km',
            advanced: [],
            mofextension: []
        },
        blankSpatialInputValues: {
            bottomLatitude: null,
            circleArr: null,
            leftLongitude: null,
            pointLatitude: null,
            pointLongitude: null,
            polyArr: null,
            radius: null,
            radiusUnit: null,
            rightLongitude: null,
            upperLatitude: null
        },
        dateId: null,
        hiddenFieldArr: ['collid', 'institutionid', 'collectionid', 'datasetid', 'tid', 'genus', 'specificepithet', 'taxonrank', 'infraspecificepithet', 'recordedbyid', 'latestdatecollected', 'eventid', 'locationid', 'associatedoccurrences', 'collectionname', 'icon', 'tidaccepted'],
        occidLoadingIndex: 0,
        occurrenceFieldLabels: {
            occid: 'ID',
            dbpk: 'dbpk',
            basisofrecord: 'Basis Of Record',
            occurrenceid: 'Occurrence ID',
            catalognumber: 'Catalog Number',
            othercatalognumbers: 'Other Catalog Numbers',
            ownerinstitutioncode: 'Owner Code',
            institutioncode: 'Institution Code',
            collectioncode: 'Collection Code',
            family: 'Family',
            verbatimscientificname: 'Verbatim Scientific Name',
            sciname: 'Scientific Name',
            scientificnameauthorship: 'Author',
            taxonremarks: 'Taxon Remarks',
            identifiedby: 'Identified By',
            dateidentified: 'Date Identified',
            identificationreferences: 'Identification References',
            identificationremarks: 'Identification Remarks',
            identificationqualifier: 'Identification Qualifier',
            typestatus: 'Type Status',
            recordedby: 'Collector',
            recordnumber: 'Collection Number',
            associatedcollectors: 'Associated Collectors',
            eventdate: 'Date',
            eventtime: 'Time',
            year: 'Year',
            month: 'Month',
            day: 'Day',
            startdayofyear: 'Start Day Of Year',
            enddayofyear: 'End Day Of Year',
            verbatimeventdate: 'Verbatim Date',
            habitat: 'Habitat',
            substrate: 'Substrate',
            fieldnotes: 'Field Notes',
            fieldnumber: 'Field Number',
            eventremarks: 'Event Remarks',
            occurrenceremarks: 'Occurrence Remarks',
            informationwithheld: 'Information Withheld',
            datageneralizations: 'Data Generalizations',
            associatedtaxa: 'Associated Taxa',
            dynamicproperties: 'Dynamic Properties',
            verbatimattributes: 'Description',
            behavior: 'Behavior',
            reproductivecondition: 'Reproductive Condition',
            cultivationstatus: 'Cultivation Status',
            establishmentmeans: 'Establishment Means',
            lifestage: 'Life Stage',
            sex: 'Sex',
            individualcount: 'Individual Count',
            samplingprotocol: 'Sampling Protocol',
            samplingeffort: 'Sampling Effort',
            rep: 'Rep Number',
            preparations: 'Preparations',
            island: 'Island',
            islandgroup: 'Island Group',
            waterbody: 'Water Body',
            continent: 'Continent',
            country: 'Country',
            stateprovince: 'State/Province',
            county: 'County',
            municipality: 'Municipality',
            locality: 'Locality',
            localitysecurity: 'Locality Security',
            localitysecurityreason: 'Locality Security Reason',
            decimallatitude: 'Decimal Latitude',
            decimallongitude: 'Decimal Longitude',
            geodeticdatum: 'Geodetic Datum',
            coordinateuncertaintyinmeters: 'Coordinate Uncertainty (m)',
            footprintwkt: 'Footprint WKT',
            coordinateprecision: 'Coordinate Precision',
            locationremarks: 'Location Remarks',
            verbatimcoordinates: 'Verbatim Coordinates',
            verbatimcoordinatesystem: 'Verbatim Coordinate System',
            georeferencedby: 'Georeferenced By',
            georeferenceprotocol: 'Georeference Protocol',
            georeferencesources: 'Georeference Sources',
            georeferenceverificationstatus: 'Georeference Verification Status',
            georeferenceremarks: 'Georeference Remarks',
            minimumelevationinmeters: 'Elevation Minimum (m)',
            maximumelevationinmeters: 'Elevation Maximum (m)',
            verbatimelevation: 'Verbatim Elevation',
            minimumdepthinmeters: 'Depth Minimum (m)',
            maximumdepthinmeters: 'Depth Maximum (m)',
            verbatimdepth: 'Verbatim Depth',
            disposition: 'Disposition',
            storagelocation: 'Storage Location',
            language: 'Language',
            processingstatus: 'Processing Status',
            duplicatequantity: 'Duplicate Quantity',
            labelproject: 'Label Project',
            recordenteredby: 'Entered By',
            dateentered: 'Date Entered',
            datelastmodified: 'Date Last Modified'
        },
        queryBuilderFieldOptions: [
            {field: 'associatedcollectors', label: 'Associated Collectors'},
            {field: 'associatedtaxa', label: 'Associated Taxa'},
            {field: 'attributes', label: 'Attributes'},
            {field: 'scientificnameauthorship', label: 'Author'},
            {field: 'basisofrecord', label: 'Basis Of Record'},
            {field: 'behavior', label: 'Behavior'},
            {field: 'catalognumber', label: 'Catalog Number'},
            {field: 'collectioncode', label: 'Collection Code'},
            {field: 'recordnumber', label: 'Collection Number'},
            {field: 'recordedby', label: 'Collector/Observer'},
            {field: 'continent', label: 'Continent'},
            {field: 'coordinateuncertaintyinmeters', label: 'Coordinate Uncertainty (m)'},
            {field: 'country', label: 'Country'},
            {field: 'county', label: 'County'},
            {field: 'cultivationstatus', label: 'Cultivation Status'},
            {field: 'datageneralizations', label: 'Data Generalizations'},
            {field: 'eventdate', label: 'Date'},
            {field: 'dateentered', label: 'Date Entered'},
            {field: 'datelastmodified', label: 'Date Last Modified'},
            {field: 'day', label: 'Day'},
            {field: 'dbpk', label: 'dbpk'},
            {field: 'decimallatitude', label: 'Decimal Latitude'},
            {field: 'decimallongitude', label: 'Decimal Longitude'},
            {field: 'maximumdepthinmeters', label: 'Depth Maximum (m)'},
            {field: 'minimumdepthinmeters', label: 'Depth Minimum (m)'},
            {field: 'verbatimattributes', label: 'Description'},
            {field: 'disposition', label: 'Disposition'},
            {field: 'dynamicproperties', label: 'Dynamic Properties'},
            {field: 'maximumelevationinmeters', label: 'Elevation Maximum (m)'},
            {field: 'minimumelevationinmeters', label: 'Elevation Minimum (m)'},
            {field: 'establishmentmeans', label: 'Establishment Means'},
            {field: 'family', label: 'Family'},
            {field: 'fieldnotes', label: 'Field Notes'},
            {field: 'fieldnumber', label: 'Field Number'},
            {field: 'genus', label: 'Genus'},
            {field: 'geodeticdatum', label: 'Geodetic Datum'},
            {field: 'georeferenceprotocol', label: 'Georeference Protocol'},
            {field: 'georeferenceremarks', label: 'Georeference Remarks'},
            {field: 'georeferencesources', label: 'Georeference Sources'},
            {field: 'georeferenceverificationstatus', label: 'Georeference Verification Status'},
            {field: 'georeferencedby', label: 'Georeferenced By'},
            {field: 'habitat', label: 'Habitat'},
            {field: 'identificationqualifier', label: 'Identification Qualifier'},
            {field: 'identificationreferences', label: 'Identification References'},
            {field: 'identificationremarks', label: 'Identification Remarks'},
            {field: 'identifiedby', label: 'Identified By'},
            {field: 'individualcount', label: 'Individual Count'},
            {field: 'informationwithheld', label: 'Information Withheld'},
            {field: 'institutioncode', label: 'Institution Code'},
            {field: 'island', label: 'Island'},
            {field: 'islandgroup', label: 'Island Group'},
            {field: 'labelproject', label: 'Label Project'},
            {field: 'lifestage', label: 'Life Stage'},
            {field: 'locality', label: 'Locality'},
            {field: 'localitysecurity', label: 'Locality Security'},
            {field: 'localitysecurityreason', label: 'Locality Security Reason'},
            {field: 'locationremarks', label: 'Location Remarks'},
            {field: 'username', label: 'Modified By'},
            {field: 'month', label: 'Month'},
            {field: 'municipality', label: 'Municipality'},
            {field: 'occurrenceremarks', label: 'Occurrence Remarks'},
            {field: 'othercatalognumbers', label: 'Other Catalog Numbers'},
            {field: 'ownerinstitutioncode', label: 'Owner Code'},
            {field: 'preparations', label: 'Preparations'},
            {field: 'processingstatus', label: 'Processing Status'},
            {field: 'reproductivecondition', label: 'Reproductive Condition'},
            {field: 'samplingeffort', label: 'Sampling Effort'},
            {field: 'samplingprotocol', label: 'Sampling Protocol'},
            {field: 'sciname', label: 'Scientific Name'},
            {field: 'sex', label: 'Sex'},
            {field: 'specificepithet', label: 'Specific Epithet'},
            {field: 'stateprovince', label: 'State/Province'},
            {field: 'substrate', label: 'Substrate'},
            {field: 'tid', label: 'Taxon ID'},
            {field: 'taxonremarks', label: 'Taxon Remarks'},
            {field: 'typestatus', label: 'Type Status'},
            {field: 'verbatimcoordinates', label: 'Verbatim Coordinates'},
            {field: 'verbatimeventdate', label: 'Verbatim Date'},
            {field: 'verbatimdepth', label: 'Verbatim Depth'},
            {field: 'verbatimelevation', label: 'Verbatim Elevation'},
            {field: 'year', label: 'Year'}
        ],
        queryId: 0,
        queryOccidArr: [],
        queryTaxaArr: [],
        radiusUnitOptions: [
            {value: 'km', label: 'Kilometers'},
            {value: 'mi', label: 'Miles'}
        ],
        searchRecordData: [],
        searchTerms: {},
        searchTermsCollId: 0,
        searchTermsPageNumber: 0,
        searchTermsRecordSortDirection: 'ASC',
        searchTermsRecordSortField: null,
        selections: [],
        selectionsIds: [],
        solrFields: 'occid,collid,catalogNumber,otherCatalogNumbers,family,sciname,tid,scientificNameAuthorship,identifiedBy,' +
            'dateIdentified,typeStatus,recordedBy,recordNumber,eventDate,displayDate,coll_year,coll_month,coll_day,habitat,associatedTaxa,' +
            'cultivationStatus,country,StateProvince,county,municipality,locality,localitySecurity,localitySecurityReason,geo,minimumElevationInMeters,' +
            'maximumElevationInMeters,labelProject,InstitutionCode,CollectionCode,CollectionName,CollType,thumbnailurl,accFamily',
        spatialInputValues: {},
        tidLoadingIndex: 0,
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
        getHiddenFieldArr(state) {
            return state.hiddenFieldArr;
        },
        getOccurrenceFieldLabels(state) {
            return state.occurrenceFieldLabels;
        },
        getQueryBuilderFieldOptions(state) {
            return state.queryBuilderFieldOptions;
        },
        getQueryId(state) {
            return state.queryId;
        },
        getRadiusDisplayValue(state) {
            return state.radiusUnitOptions;
        },
        getRadiusUnitOptions(state) {
            return state.radiusUnitOptions;
        },
        getSearchOccidArr(state) {
            return state.queryOccidArr;
        },
        getSearchRecordCount(state) {
            return state.queryOccidArr.length;
        },
        getSearchRecordData(state) {
            return state.searchRecordData;
        },
        getSearchRecordDataFieldArr(state) {
            const returnArr = [];
            state.searchRecordData.forEach((record) => {
                Object.keys(state.occurrenceFieldLabels).forEach((field) => {
                    if(state.searchTermsRecordSortField === field || (record[field] && !state.hiddenFieldArr.includes(field) && !returnArr.includes(field))){
                        returnArr.push(field);
                    }
                });
            });
            return returnArr;
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
        getSearchTaxaArr(state) {
            return state.queryTaxaArr;
        },
        getSearchTerms(state) {
            return state.searchTerms;
        },
        getSearchTermsCollId(state) {
            return state.searchTermsCollId;
        },
        getSearchTermsJson(state) {
            return JSON.stringify(state.searchTerms);
        },
        getSearchTermsPageNumber(state) {
            return state.searchTermsPageNumber;
        },
        getSearchTermsRecordSortDirection(state) {
            return state.searchTermsRecordSortDirection;
        },
        getSearchTermsRecordSortField(state) {
            return state.searchTermsRecordSortField;
        },
        getSearchTermsValid(state) {
            let populated = false;
            if(
                (state.searchTerms.hasOwnProperty('db') && state.searchTerms['db'].length > 0) ||
                (state.searchTerms.hasOwnProperty('clid') && state.searchTerms['clid']) ||
                (state.searchTerms.hasOwnProperty('taxa') && state.searchTerms['taxa']) ||
                (state.searchTerms.hasOwnProperty('country') && state.searchTerms['country']) ||
                (state.searchTerms.hasOwnProperty('state') && state.searchTerms['state']) ||
                (state.searchTerms.hasOwnProperty('county') && state.searchTerms['county']) ||
                (state.searchTerms.hasOwnProperty('local') && state.searchTerms['local']) ||
                (state.searchTerms.hasOwnProperty('elevlow') && state.searchTerms['elevlow']) ||
                (state.searchTerms.hasOwnProperty('elevhigh') && state.searchTerms['elevhigh']) ||
                (state.searchTerms.hasOwnProperty('collector') && state.searchTerms['collector']) ||
                (state.searchTerms.hasOwnProperty('collnum') && state.searchTerms['collnum']) ||
                (state.searchTerms.hasOwnProperty('eventdate1') && state.searchTerms['eventdate1']) ||
                (state.searchTerms.hasOwnProperty('eventdate2') && state.searchTerms['eventdate2']) ||
                (state.searchTerms.hasOwnProperty('occurrenceRemarks') && state.searchTerms['occurrenceRemarks']) ||
                (state.searchTerms.hasOwnProperty('catnum') && state.searchTerms['catnum']) ||
                (state.searchTerms.hasOwnProperty('upperlat') && state.searchTerms['upperlat']) ||
                (state.searchTerms.hasOwnProperty('pointlat') && state.searchTerms['pointlat']) ||
                (state.searchTerms.hasOwnProperty('circleArr') && state.searchTerms['circleArr'].length > 0) ||
                (state.searchTerms.hasOwnProperty('phuid') && state.searchTerms['phuid']) ||
                (state.searchTerms.hasOwnProperty('imagetag') && state.searchTerms['imagetag']) ||
                (state.searchTerms.hasOwnProperty('imagekeyword') && state.searchTerms['imagekeyword']) ||
                (state.searchTerms.hasOwnProperty('uploaddate1') && state.searchTerms['uploaddate1']) ||
                (state.searchTerms.hasOwnProperty('uploaddate2') && state.searchTerms['uploaddate2']) ||
                (state.searchTerms.hasOwnProperty('polyArr') && state.searchTerms['polyArr'].length > 0) ||
                (state.searchTerms.hasOwnProperty('enteredby') && state.searchTerms['enteredby']) ||
                (state.searchTerms.hasOwnProperty('dateentered') && state.searchTerms['dateentered']) ||
                (state.searchTerms.hasOwnProperty('datemodified') && state.searchTerms['datemodified']) ||
                (state.searchTerms.hasOwnProperty('processingstatus') && state.searchTerms['processingstatus']) ||
                (state.searchTerms.hasOwnProperty('typestatus') && state.searchTerms['typestatus']) ||
                (state.searchTerms.hasOwnProperty('hasaudio') && state.searchTerms['hasaudio']) ||
                (state.searchTerms.hasOwnProperty('hasimages') && state.searchTerms['hasimages']) ||
                (state.searchTerms.hasOwnProperty('hasvideo') && state.searchTerms['hasvideo']) ||
                (state.searchTerms.hasOwnProperty('hasmedia') && state.searchTerms['hasmedia']) ||
                (state.searchTerms.hasOwnProperty('hasgenetic') && state.searchTerms['hasgenetic']) ||
                (state.searchTerms.hasOwnProperty('withoutimages') && state.searchTerms['withoutimages']) ||
                (state.searchTerms.hasOwnProperty('advanced') && state.searchTerms['advanced'].length > 0) ||
                (state.searchTerms.hasOwnProperty('mofextension') && state.searchTerms['mofextension'].length > 0)
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
        getSpatialInputValues(state) {
            if(Object.keys(state.spatialInputValues).length > 0){
                return state.spatialInputValues;
            }
            else{
                return state.blankSpatialInputValues;
            }
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
        clearQueryOccidArr() {
            this.queryOccidArr.length = 0;
            this.queryTaxaArr.length = 0;
            this.occidLoadingIndex = 0;
            this.tidLoadingIndex = 0;
        },
        clearSearchTerms() {
            this.searchTerms = Object.assign({}, this.blankSearchTerms);
            if(Number(this.searchTermsCollId) > 0){
                this.searchTerms['collid'] = this.searchTermsCollId;
                this.searchTerms['db'] = [this.searchTermsCollId];
            }
            this.updateLocalStorageSearchTerms();
        },
        clearSelections() {
            this.selections.length = 0;
            this.selectionsIds.length = 0;
            this.searchRecordData.forEach((record) => {
                record.selected = false;
            });
        },
        clearSpatialInputValues() {
            this.spatialInputValues = Object.assign({}, this.blankSpatialInputValues);
        },
        deselectAllCurrentRecords() {
            this.searchRecordData.forEach((record) => {
                if(this.selectionsIds.indexOf(Number(record.occid)) > -1){
                    this.removeRecordFromSelections(Number(record.occid));
                }
            });
        },
        getSearchOccidSubArr(options) {
            const numRows = options.hasOwnProperty('numRows') ? Number(options['numRows']) : 0;
            const index = options.hasOwnProperty('index') ? Number(options['index']) : 0;
            const bottomLimit = numRows > 0 ? (index * numRows) : 0;
            return this.queryOccidArr.slice(bottomLimit, (bottomLimit + (numRows - 1)));
        },
        getSearchTidArr(options, callback){
            const formData = new FormData();
            formData.append('starr', this.getSearchTermsJson);
            formData.append('options', JSON.stringify(options));
            formData.append('action', 'getSearchTidArr');
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
            if(newSearchTerms.hasOwnProperty('sortField')){
                this.searchTermsRecordSortField = newSearchTerms.sortField;
                this.searchTermsRecordSortDirection = newSearchTerms.sortDirection;
                delete newSearchTerms['sortField'];
                delete newSearchTerms['sortDirection'];
            }
            if(newSearchTerms.hasOwnProperty('collId')){
                this.setSearchCollId(newSearchTerms.collId);
                delete newSearchTerms['collId'];
            }
            this.searchTerms = Object.assign({}, newSearchTerms);
            searchTermsArr[this.dateId.toString()][this.queryId.toString()] = Object.assign({}, newSearchTerms);
            localStorage.setItem('searchTermsArr', JSON.stringify(searchTermsArr));
        },
        processDownloadRequest(options, callback){
            options.filename = 'occurrence_data_' + (options.type === 'zip' ? 'DwCA_' : '') + this.getDateTimeString;
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
            formData.append('action', 'processSearchDownload');
            fetch(searchServiceApiUrl, {
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
            const occidArr = this.getSearchOccidSubArr(options);
            const formData = new FormData();
            formData.append('starr', JSON.stringify({occidArr: occidArr}));
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
                    callback(data, options.index);
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
        processSpatialPopupData(windowType, data) {
            if(windowType.includes('box') && data.hasOwnProperty('boundingBoxArr')){
                this.updateSearchTerms('upperlat', data['boundingBoxArr']['upperlat']);
                this.updateSearchTerms('bottomlat', data['boundingBoxArr']['bottomlat']);
                this.updateSearchTerms('leftlong', data['boundingBoxArr']['leftlong']);
                this.updateSearchTerms('rightlong', data['boundingBoxArr']['rightlong']);
            }
            else if(windowType.includes('circle') && data.hasOwnProperty('circleArr') && data['circleArr'].length === 1){
                this.updateSearchTerms('pointlat', data['circleArr'][0]['pointlat']);
                this.updateSearchTerms('pointlong', data['circleArr'][0]['pointlong']);
                this.updateSearchTerms('radius', data['circleArr'][0]['radius']);
                this.updateSearchTerms('groundradius', data['circleArr'][0]['groundradius']);
                this.updateSearchTerms('radiusval', data['circleArr'][0]['radiusval']);
                this.updateSearchTerms('radiusunit', data['circleArr'][0]['radiusunits']);
            }
            else if(windowType === 'input' && (data.hasOwnProperty('circleArr') || data.hasOwnProperty('polyArr'))){
                if(data.hasOwnProperty('circleArr')){
                    this.updateSearchTerms('circleArr', data['circleArr']);
                }
                if(data.hasOwnProperty('polyArr')){
                    this.updateSearchTerms('polyArr', data['polyArr']);
                }
            }
        },
        redirectWithQueryId(url, addlProp = null, newTab = false) {
            const baseStore = useBaseStore();
            if(newTab){
                window.open((baseStore.getClientRoot + url + '?queryId=' + this.queryId + (addlProp ? ('&' + addlProp['prop'] + '=' + addlProp['propValue']) : '')), '_blank');
            }
            else{
                window.location.href = baseStore.getClientRoot + url + '?queryId=' + this.queryId + (addlProp ? ('&' + addlProp['prop'] + '=' + addlProp['propValue']) : '');
            }
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
        setSearchOccidArr(options, callback){
            const loadingCnt = 250000;
            const formData = new FormData();
            formData.append('starr', this.getSearchTermsJson);
            formData.append('options', JSON.stringify(options));
            formData.append('index', this.occidLoadingIndex.toString());
            formData.append('reccnt', loadingCnt.toString());
            formData.append('action', 'getSearchOccidArr');
            fetch(searchServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.queryOccidArr = this.queryOccidArr.concat(data);
                if(data.length < loadingCnt){
                    if(callback){
                        callback();
                    }
                }
                else{
                    this.occidLoadingIndex++;
                    this.setSearchOccidArr(options, callback);
                }
            });
        },
        setSearchCollId(collid) {
            this.searchTerms['collid'] = collid;
            this.searchTerms['db'] = [collid];
            this.searchTermsCollId = collid;
        },
        setSearchRecordData(options, callback = null) {
            this.processSearch(options, (res) => {
                this.searchRecordData = this.setSelectedRecords(res);
                if(callback){
                    callback(res.length);
                }
            });
        },
        setSearchTaxaArr(callback){
            const loadingCnt = 5000;
            const options = {
                schema: 'taxa',
                spatial: 0,
                index: this.tidLoadingIndex.toString(),
                reccnt: loadingCnt.toString(),
                output: 'json'
            };
            this.processSimpleSearch(this.getSearchTerms, options, (data) => {
                this.queryTaxaArr = this.queryTaxaArr.concat(data);
                if(data.length < loadingCnt){
                    if(callback){
                        callback()
                    }
                }
                else{
                    this.tidLoadingIndex++;
                    this.setSearchTaxaArr(callback);
                }
            });
        },
        setSearchTermsRecordSortDirection(value) {
            this.searchTermsRecordSortDirection = value;
        },
        setSearchTermsRecordSortField(value) {
            this.searchTermsRecordSortField = value;
        },
        setSelectedRecords(recordArr) {
            recordArr.forEach((record) => {
                record.selected = (this.selectionsIds.indexOf(Number(record.occid)) > -1);
            });
            return recordArr;
        },
        setSpatialInputValues() {
            this.spatialInputValues['bottomLatitude'] = this.searchTerms.hasOwnProperty('bottomlat') ? this.searchTerms['bottomlat'] : null;
            this.spatialInputValues['circleArr'] = this.searchTerms.hasOwnProperty('circleArr') ? this.searchTerms['circleArr'] : null;
            this.spatialInputValues['leftLongitude'] = this.searchTerms.hasOwnProperty('leftlong') ? this.searchTerms['leftlong'] : null;
            this.spatialInputValues['pointLatitude'] = this.searchTerms.hasOwnProperty('pointlat') ? this.searchTerms['pointlat'] : null;
            this.spatialInputValues['pointLongitude'] = this.searchTerms.hasOwnProperty('pointlong') ? this.searchTerms['pointlong'] : null;
            this.spatialInputValues['polyArr'] = this.searchTerms.hasOwnProperty('polyArr') ? this.searchTerms['polyArr'] : null;
            this.spatialInputValues['radius'] = this.searchTerms.hasOwnProperty('radiusval') ? this.searchTerms['radiusval'] : null;
            this.spatialInputValues['radiusUnit'] = this.searchTerms.hasOwnProperty('radiusunit') ? this.searchTerms['radiusunit'] : null;
            this.spatialInputValues['rightLongitude'] = this.searchTerms.hasOwnProperty('rightlong') ? this.searchTerms['rightlong'] : null;
            this.spatialInputValues['upperLatitude'] = this.searchTerms.hasOwnProperty('upperlat') ? this.searchTerms['upperlat'] : null;
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
