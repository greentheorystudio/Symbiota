const useOccurrenceStore = Pinia.defineStore('occurrence', {
    state: () => ({
        baseStore: useBaseStore(),
        basisOfRecordOptions: [
            {value: 'PreservedSpecimen', label: 'Preserved Specimen'},
            {value: 'HumanObservation', label: 'Observation'},
            {value: 'FossilSpecimen', label: 'Fossil Specimen'},
            {value: 'LivingSpecimen', label: 'Living Specimen'},
            {value: 'MaterialSample', label: 'Material Sample'}
        ],
        blankOccurrenceRecord: {
            occid: 0,
            collid: 0,
            dbpk: null,
            basisofrecord: null,
            occurrenceid: null,
            catalognumber: null,
            othercatalognumbers: null,
            ownerinstitutioncode: null,
            institutionid: null,
            collectionid: null,
            datasetid: null,
            institutioncode: null,
            collectioncode: null,
            family: null,
            verbatimscientificname: null,
            sciname: null,
            tid: null,
            genus: null,
            specificepithet: null,
            taxonrank: null,
            infraspecificepithet: null,
            scientificnameauthorship: null,
            taxonremarks: null,
            identifiedby: null,
            dateidentified: null,
            identificationreferences: null,
            identificationremarks: null,
            identificationqualifier: null,
            typestatus: null,
            recordedby: null,
            recordnumber: null,
            recordedbyid: null,
            associatedcollectors: null,
            eventdate: null,
            latestdatecollected: null,
            eventtime: null,
            year: null,
            month: null,
            day: null,
            startdayofyear: null,
            enddayofyear: null,
            verbatimeventdate: null,
            habitat: null,
            substrate: null,
            fieldnotes: null,
            fieldnumber: null,
            eventid: null,
            eventremarks: null,
            occurrenceremarks: null,
            informationwithheld: null,
            datageneralizations: null,
            associatedoccurrences: null,
            associatedtaxa: null,
            dynamicproperties: null,
            verbatimattributes: null,
            behavior: null,
            reproductivecondition: null,
            cultivationstatus: null,
            establishmentmeans: null,
            lifestage: null,
            sex: null,
            individualcount: null,
            samplingprotocol: null,
            samplingeffort: null,
            rep: null,
            preparations: null,
            locationid: null,
            island: null,
            islandgroup: null,
            waterbody: null,
            continent: null,
            country: null,
            stateprovince: null,
            county: null,
            municipality: null,
            locality: null,
            localitysecurity: null,
            localitysecurityreason: null,
            decimallatitude: null,
            decimallongitude: null,
            geodeticdatum: null,
            coordinateuncertaintyinmeters: null,
            footprintwkt: null,
            coordinateprecision: null,
            locationremarks: null,
            verbatimcoordinates: null,
            verbatimcoordinatesystem: null,
            georeferencedby: null,
            georeferenceprotocol: null,
            georeferencesources: null,
            georeferenceverificationstatus: null,
            georeferenceremarks: null,
            minimumelevationinmeters: null,
            maximumelevationinmeters: null,
            verbatimelevation: null,
            minimumdepthinmeters: null,
            maximumdepthinmeters: null,
            verbatimdepth: null,
            previousidentifications: null,
            disposition: null,
            storagelocation: null,
            language: null,
            observeruid: null,
            processingstatus: null,
            duplicatequantity: null,
            labelproject: null
        },
        checklistArr: [],
        collectingEventAutoSearch: false,
        collectingEventStore: useOccurrenceCollectingEventStore(),
        collectionStore: useCollectionStore(),
        crowdSourceQueryFieldOptions: [
            {field: 'family', label: 'Family'},
            {field: 'sciname', label: 'Scientific Name'},
            {field: 'othercatalognumbers', label: 'Other Catalog Numbers'},
            {field: 'country', label: 'Country'},
            {field: 'stateprovince', label: 'State/Province'},
            {field: 'county', label: 'County'},
            {field: 'municipality', label: 'Municipality'},
            {field: 'recordedby', label: 'Collector'},
            {field: 'recordnumber', label: 'Collector Number'},
            {field: 'eventdate', label: 'Collection Date'}
        ],
        determinationStore: useOccurrenceDeterminationStore(),
        displayMode: 1,
        editArr: [],
        entryFollowUpAction: 'remain',
        geneticLinkStore: useOccurrenceGeneticLinkStore(),
        imageStore: useImageStore(),
        isEditor: false,
        isLocked: false,
        locationStore: useOccurrenceLocationStore(),
        mediaStore: useMediaStore(),
        occId: null,
        occurrenceData: {},
        occurrenceEditData: {},
        occurrenceEntryFormat: 'specimen',
        occurrenceFields: {},
        occurrenceFieldDefinitions: {},
        occurrenceMofData: {},
        occurrenceMofEditData: {},
        occurrenceMofUpdateData: {},
        occurrenceUpdateData: {}
    }),
    getters: {
        getBasisOfRecordOptions(state) {
            return state.basisOfRecordOptions;
        },
        getBlankCollectingEventRecord(state) {
            return state.collectingEventStore.getBlankCollectingEventRecord;
        },
        getBlankLocationRecord(state) {
            return state.locationStore.getBlankLocationRecord;
        },
        getBlankOccurrenceRecord(state) {
            return state.blankOccurrenceRecord;
        },
        getChecklistArr(state) {
            return state.checklistArr;
        },
        getClientRoot() {
            const store = useBaseStore();
            return store.getClientRoot;
        },
        getCollectingEventAutoSearch(state) {
            return state.collectingEventAutoSearch;
        },
        getCollectingEventCollectionArr(state) {
            return state.collectingEventStore.getCollectingEventCollectionArr;
        },
        getCollectingEventData(state) {
            return state.collectingEventStore.getCollectingEventData;
        },
        getCollectingEventEditsExist(state) {
            return state.collectingEventStore.getCollectingEventEditsExist;
        },
        getCollectingEventFields(state) {
            return state.collectingEventStore.getCollectingEventFields;
        },
        getCollectingEventID(state) {
            return state.collectingEventStore.getCollectingEventID;
        },
        getCollectingEventReplicateData(state) {
            return state.collectingEventStore.getCollectingEventReplicateData;
        },
        getCollectingEventReplicateTaxaCnt(state) {
            return state.collectingEventStore.getCollectingEventReplicateTaxaCnt;
        },
        getCollectingEventValid(state) {
            return state.collectingEventStore.getCollectingEventValid;
        },
        getCollectionData(state) {
            return state.collectionStore.getCollectionData;
        },
        getCollId(state) {
            return state.collectionStore.getCollectionId;
        },
        getCrowdSourceQueryFieldOptions(state) {
            return state.crowdSourceQueryFieldOptions;
        },
        getDeterminationArr(state) {
            return state.determinationStore.getDeterminationArr;
        },
        getDeterminationData(state) {
            return state.determinationStore.getDeterminationData;
        },
        getDeterminationEditsExist(state) {
            return state.determinationStore.getDeterminationEditsExist;
        },
        getDeterminationFields(state) {
            return state.determinationStore.getDeterminationFields;
        },
        getDeterminationID(state) {
            return state.determinationStore.getDeterminationID;
        },
        getDeterminationValid(state) {
            return state.determinationStore.getDeterminationValid;
        },
        getDisplayMode(state) {
            return state.displayMode;
        },
        getEditArr(state) {
            return state.editArr;
        },
        getEditorHideFields(state) {
            return state.collectionStore.getEditorHideFields;
        },
        getEmbeddedOccurrenceRecord(state) {
            return (state.occurrenceEntryFormat !== 'replicate' && state.occurrenceEntryFormat !== 'lot');
        },
        getEntryFollowUpAction(state) {
            return state.entryFollowUpAction;
        },
        getEventMofData(state) {
            return state.collectingEventStore.getEventMofData;
        },
        getEventMofDataFields(state) {
            return state.collectionStore.getEventMofDataFields;
        },
        getEventMofDataFieldsLayoutData(state) {
            return state.collectionStore.getEventMofDataFieldsLayoutData;
        },
        getEventMofDataLabel(state) {
            return state.collectionStore.getEventMofDataLabel;
        },
        getEventMofEditData(state) {
            return state.collectingEventStore.getEventMofEditData;
        },
        getEventMofEditsExist(state) {
            return state.collectingEventStore.getEventMofEditsExist;
        },
        getEventRecordFields(state) {
            return state.collectionStore.getEventRecordFields;
        },
        getGeneticLinkArr(state) {
            return state.geneticLinkStore.getGeneticLinkArr;
        },
        getGeneticLinkData(state) {
            return state.geneticLinkStore.getGeneticLinkData;
        },
        getGeneticLinkEditsExist(state) {
            return state.geneticLinkStore.getGeneticLinkEditsExist;
        },
        getGeneticLinkValid(state) {
            return state.geneticLinkStore.getGeneticLinkValid;
        },
        getImageArr(state) {
            return state.imageStore.getImageArr;
        },
        getImageCount(state) {
            return state.imageStore.getImageCount;
        },
        getIsEditor(state) {
            return (state.collectionStore.getCollectionPermissions.includes('CollAdmin') || state.collectionStore.getCollectionPermissions.includes('CollEditor'));
        },
        getIsLocked(state) {
            return state.isLocked;
        },
        getLimitIdsToThesaurus(state) {
            return state.collectionStore.getLimitIdsToThesaurus;
        },
        getLocationCollectingEventArr(state) {
            return state.collectingEventStore.getLocationCollectingEventArr;
        },
        getLocationData(state) {
            return state.locationStore.getLocationData;
        },
        getLocationEditsExist(state) {
            return state.locationStore.getLocationEditsExist;
        },
        getLocationFields(state) {
            return state.locationStore.getLocationFields;
        },
        getLocationID(state) {
            return state.locationStore.getLocationID;
        },
        getLocationValid(state) {
            return state.locationStore.getLocationValid;
        },
        getMediaArr(state) {
            return state.mediaStore.getMediaArr;
        },
        getOccId(state) {
            return state.occId;
        },
        getOccurrenceData(state) {
            return state.occurrenceEditData;
        },
        getOccurrenceEditsExist(state) {
            let exist = false;
            state.occurrenceUpdateData = Object.assign({}, {});
            for(let key in state.occurrenceEditData) {
                if(state.occurrenceEditData.hasOwnProperty(key) && state.occurrenceEditData[key] !== state.occurrenceData[key]) {
                    exist = true;
                    state.occurrenceUpdateData[key] = state.occurrenceEditData[key];
                }
            }
            return exist;
        },
        getOccurrenceEntryFormat(state) {
            return state.occurrenceEntryFormat;
        },
        getOccurrenceFieldControlledVocabularies(state) {
            return state.collectionStore.getOccurrenceFieldControlledVocabularies;
        },
        getOccurrenceFields(state) {
            return state.occurrenceFields;
        },
        getOccurrenceFieldDefinitions(state) {
            return state.occurrenceFieldDefinitions;
        },
        getOccurrenceMofData(state) {
            return state.occurrenceMofEditData;
        },
        getOccurrenceMofDataFields(state) {
            return state.collectionStore.getOccurrenceMofDataFields;
        },
        getOccurrenceMofDataFieldsLayoutData(state) {
            return state.collectionStore.getOccurrenceMofDataFieldsLayoutData;
        },
        getOccurrenceMofDataLabel(state) {
            return state.collectionStore.getOccurrenceMofDataLabel;
        },
        getOccurrenceMofEditData(state) {
            const editData = {
                add: [],
                delete: [],
                update: []
            };
            Object.keys(state.occurrenceMofUpdateData).forEach((key) => {
                if(state.occurrenceMofEditData[key] && !state.occurrenceMofData[key]){
                    editData.add.push({field: key, value: state.occurrenceMofUpdateData[key]});
                }
                else if(!state.occurrenceMofEditData[key] && state.occurrenceMofData[key]){
                    editData.delete.push(key);
                }
                else if(state.occurrenceMofEditData[key] !== state.occurrenceMofData[key]){
                    editData.update.push({field: key, value: state.occurrenceMofUpdateData[key]});
                }
            });
            return editData;
        },
        getOccurrenceMofEditsExist(state) {
            let exist = false;
            state.occurrenceMofUpdateData = Object.assign({}, {});
            for(let key in state.occurrenceMofEditData) {
                if(state.occurrenceMofEditData.hasOwnProperty(key) && state.occurrenceMofEditData[key] !== state.occurrenceMofData[key]) {
                    exist = true;
                    state.occurrenceMofUpdateData[key] = state.occurrenceMofEditData[key];
                }
            }
            return exist;
        },
        getOccurrenceValid(state) {
            return (state.occurrenceEditData['sciname']);
        },
        getTranscriberHideFields(state) {
            return state.collectionStore.getTranscriberHideFields;
        }
    },
    actions: {
        batchUpdateOccurrenceData(starr, field, oldValue, newValue, matchType, callback) {
            const formData = new FormData();
            formData.append('collid', this.getCollId.toString());
            formData.append('starr', JSON.stringify(starr));
            formData.append('field', field);
            formData.append('oldValue', (oldValue ? oldValue.toString() : ''));
            formData.append('newValue', (newValue ? newValue.toString() : ''));
            formData.append('matchType', matchType.toString());
            formData.append('action', 'batchUpdateOccurrenceData');
            fetch(occurrenceApiUrl, {
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
        clearOccurrenceData() {
            this.occurrenceData = Object.assign({}, this.blankOccurrenceRecord);
            this.isLocked = false;
            this.determinationStore.clearDeterminationArr();
            this.editArr.length = 0;
            this.imageStore.clearImageArr();
            this.mediaStore.clearMediaArr();
            this.checklistArr.length = 0;
            this.geneticLinkStore.clearGeneticLinkArr();
            this.occurrenceMofData = Object.assign({}, {});
        },
        createCollectingEventRecord(callback, eventData = null) {
            this.collectingEventStore.createCollectingEventRecord(this.getCollId, this.getLocationID, this.occurrenceEntryFormat, this.getCollectionData['defaultrepcount'], this.getEventMofDataFields, (newEventId) => {
                if(newEventId && Number(newEventId) > 0){
                    if(this.getEventMofEditsExist){
                        this.processMofEditData('event', null, Number(newEventId));
                    }
                    this.mergeEventOccurrenceData();
                }
                callback(Number(newEventId));
            }, eventData);
        },
        createLocationRecord(callback, locationData = null) {
            this.locationStore.createLocationRecord(this.getCollId, (newLocationId) => {
                if(newLocationId && Number(newLocationId) > 0){
                    this.mergeLocationOccurrenceData();
                }
                callback(Number(newLocationId));
            }, locationData);
        },
        createOccurrenceDeterminationRecord(callback) {
            const newIsCurrent = Number(this.determinationStore.getDeterminationData['iscurrent']) === 1;
            this.determinationStore.createOccurrenceDeterminationRecord(this.getCollId, this.occId, (newDetId) => {
                callback(Number(newDetId));
                if(newIsCurrent){
                    this.setCurrentOccurrenceRecord(this.occId);
                }
                else if(newDetId && Number(newDetId) > 0){
                    this.determinationStore.setDeterminationArr(this.occId);
                }
            });
        },
        createOccurrenceGeneticLinkageRecord(callback) {
            this.geneticLinkStore.createOccurrenceGeneticLinkageRecord(this.getCollId, this.occId, (newLinkId) => {
                callback(Number(newLinkId));
                if(newLinkId && Number(newLinkId) > 0){
                    this.geneticLinkStore.setGeneticLinkArr(this.occId);
                }
            });
        },
        createOccurrenceRecord(callback) {
            this.setOccurrenceCollectionData();
            const formData = new FormData();
            formData.append('collid', this.getCollId.toString());
            formData.append('occurrence', JSON.stringify(this.occurrenceEditData));
            formData.append('action', 'createOccurrenceRecord');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) > 0){
                    if(this.getOccurrenceMofEditsExist){
                        this.processMofEditData('occurrence', null, Number(res));
                    }
                    if(this.entryFollowUpAction === 'remain' || this.entryFollowUpAction === 'none'){
                        this.setCurrentOccurrenceRecord(Number(res));
                    }
                    else{
                        this.setCurrentOccurrenceRecord(0);
                    }
                }
            });
        },
        deleteCollectingEventRecord(callback = null) {
            this.collectingEventStore.deleteCollectingEventRecord(this.getCollId, (res) => {
                if(callback){
                    callback(Number(res));
                }
                if(Number(res) === 1){
                    this.setCurrentCollectingEventRecord(0);
                    if(this.getLocationID > 0){
                        this.collectingEventStore.getLocationCollectingEvents(this.getCollId, this.getLocationID);
                    }
                }
            });
        },
        deleteLocationRecord(callback = null) {
            this.locationStore.deleteLocationRecord(this.getCollId, (res) => {
                if(callback){
                    callback(Number(res));
                }
                if(Number(res) === 1){
                    this.setCurrentLocationRecord(0);
                }
            });
        },
        deleteOccurrenceDeterminationRecord(callback = null) {
            this.determinationStore.deleteDeterminationRecord(this.getCollId, (res) => {
                if(callback){
                    callback(Number(res));
                }
                if(Number(res) === 1){
                    this.determinationStore.setDeterminationArr(this.occId);
                }
            });
        },
        deleteGeneticLinkageRecord(callback = null) {
            this.geneticLinkStore.deleteGeneticLinkageRecord(this.getCollId, (res) => {
                if(callback){
                    callback(Number(res));
                }
                if(Number(res) === 1){
                    this.geneticLinkStore.setGeneticLinkArr(this.occId);
                }
            });
        },
        deleteOccurrenceRecord(occid, callback) {
            const formData = new FormData();
            formData.append('collid', this.getCollId.toString());
            formData.append('occid', occid.toString());
            formData.append('action', 'deleteOccurrenceRecord');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((val) => {
                if(this.occId === Number(occid)){
                    this.setCurrentOccurrenceRecord(0);
                }
                callback(Number(val));
            });
        },
        evaluateOccurrenceForDeletion(occid, callback) {
            const formData = new FormData();
            formData.append('collid', this.getCollId.toString());
            formData.append('occid', occid);
            formData.append('action', 'evaluateOccurrenceForDeletion');
            fetch(occurrenceApiUrl, {
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
        getBatchUpdateCount(starr, field, oldValue, matchType, callback) {
            const formData = new FormData();
            formData.append('collid', this.getCollId.toString());
            formData.append('starr', JSON.stringify(starr));
            formData.append('field', field);
            formData.append('oldValue', (oldValue ? oldValue.toString() : ''));
            formData.append('matchType', matchType.toString());
            formData.append('action', 'getBatchUpdateCount');
            fetch(occurrenceApiUrl, {
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
        getCoordinateVerificationData(callback) {
            if(this.occurrenceEditData['decimallatitude'] && this.occurrenceEditData['decimallongitude']){
                const url = 'https://nominatim.openstreetmap.org/reverse?lat=' + this.occurrenceEditData['decimallatitude'].toString() + '&lon=' + this.occurrenceEditData['decimallongitude'].toString() + '&format=json';
                fetch(url)
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    const returnData = {
                        valid: false,
                        address: false,
                        country: null,
                        state: null,
                        county: null
                    };
                    if(data.hasOwnProperty('address')){
                        returnData.address = true;
                        returnData.country = data.address.country;
                        returnData.state = data.address.state;
                        returnData.valid = true;
                        if((!this.occurrenceEditData['country'] || this.occurrenceEditData['country'] === '') && returnData.country && returnData.country !== ''){
                            this.updateOccurrenceEditData('country', returnData.country);
                        }
                        if(this.occurrenceEditData['country'] && returnData.country && this.occurrenceEditData['country'] !== '' && this.occurrenceEditData['country'].toLowerCase() !== returnData.country.toLowerCase()){
                            if(this.occurrenceEditData['country'].toLowerCase() !== 'usa' && this.occurrenceEditData['country'].toLowerCase() !== 'united states of america' && returnData.country.toLowerCase() !== 'united states'){
                                returnData.valid = false;
                            }
                        }
                        if(returnData.state && returnData.state !== ''){
                            if(this.occurrenceEditData['stateprovince'] && this.occurrenceEditData['stateprovince'] !== '' && this.occurrenceEditData['stateprovince'].toLowerCase() !== returnData.state.toLowerCase()){
                                returnData.valid = false;
                            }
                            else{
                                this.updateOccurrenceEditData('stateprovince', returnData.state);
                            }
                        }
                        if(data.address.county && data.address.county !== ''){
                            let coordCountyIn = data.address.county.replace(' County', '');
                            coordCountyIn = coordCountyIn.replace(' Parish', '');
                            returnData.county = coordCountyIn;
                            if(this.occurrenceEditData['county'] && this.occurrenceEditData['county'] !== '' && this.occurrenceEditData['county'].toLowerCase() !== coordCountyIn.toLowerCase()){
                                returnData.valid = false;
                            }
                            else{
                                this.updateOccurrenceEditData('county', coordCountyIn);
                            }
                        }
                    }
                    callback(returnData);
                });
            }
        },
        getNearbyLocations(callback) {
            this.locationStore.getNearbyLocations(this.getCollId, callback);
        },
        getOccurrenceDuplicateIdentifierRecordArr(identifierField, identifier, callback) {
            const formData = new FormData();
            formData.append('collid', this.getCollId.toString());
            formData.append('occid', this.occId.toString());
            formData.append('identifierField', identifierField);
            formData.append('identifier', identifier);
            formData.append('action', 'getOccurrenceDuplicateIdentifierRecordArr');
            fetch(occurrenceApiUrl, {
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
        goToNewOccurrenceRecord(carryLocation = false, carryEvent = false) {
            this.setCurrentOccurrenceRecord(0);
            if(carryLocation){
                this.mergeLocationOccurrenceData();
            }
            else{
                this.locationStore.clearLocationData();
                this.collectingEventStore.clearLocationData();
            }
            if(carryEvent){
                this.mergeEventOccurrenceData();
            }
            else{
                this.setCurrentCollectingEventRecord(0);
            }
        },
        makeDeterminationCurrent(callback = null) {
            this.determinationStore.makeDeterminationCurrent(this.getCollId, (res) => {
                if(callback){
                    callback(Number(res));
                }
                if(Number(res) === 1){
                    this.setCurrentOccurrenceRecord(this.occId);
                }
            });
        },
        mergeEventOccurrenceData() {
            const eventData = this.getCollectingEventData;
            this.occurrenceEditData['eventid'] = this.getCollectingEventID;
            this.occurrenceEditData['fieldnotes'] = eventData['fieldnotes'];
            this.occurrenceEditData['fieldnumber'] = eventData['fieldnumber'];
            this.occurrenceEditData['recordedby'] = eventData['recordedby'];
            this.occurrenceEditData['recordnumber'] = eventData['recordnumber'];
            this.occurrenceEditData['recordedbyid'] = eventData['recordedbyid'];
            this.occurrenceEditData['associatedcollectors'] = eventData['associatedcollectors'];
            this.occurrenceEditData['eventdate'] = eventData['eventdate'];
            this.occurrenceEditData['latestdatecollected'] = eventData['latestdatecollected'];
            this.occurrenceEditData['eventtime'] = eventData['eventtime'];
            this.occurrenceEditData['year'] = eventData['year'];
            this.occurrenceEditData['month'] = eventData['month'];
            this.occurrenceEditData['day'] = eventData['day'];
            this.occurrenceEditData['startdayofyear'] = eventData['startdayofyear'];
            this.occurrenceEditData['enddayofyear'] = eventData['enddayofyear'];
            this.occurrenceEditData['verbatimeventdate'] = eventData['verbatimeventdate'];
            if(this.occurrenceEntryFormat === 'replicate'){
                this.occurrenceEditData['habitat'] = eventData['habitat'];
            }
            this.occurrenceEditData['substrate'] = eventData['substrate'];
            if(Number(this.occurrenceEditData['localitysecurity']) !== 1 && Number(eventData['localitysecurity']) === 1){
                this.occurrenceEditData['localitysecurity'] = eventData['localitysecurity'];
                this.occurrenceEditData['localitysecurityreason'] = eventData['localitysecurityreason'];
            }
            if(eventData['decimallatitude']){
                this.occurrenceEditData['decimallatitude'] = eventData['decimallatitude'];
            }
            if(eventData['decimallongitude']){
                this.occurrenceEditData['decimallongitude'] = eventData['decimallongitude'];
            }
            if(eventData['geodeticdatum']){
                this.occurrenceEditData['geodeticdatum'] = eventData['geodeticdatum'];
            }
            if(eventData['coordinateuncertaintyinmeters']){
                this.occurrenceEditData['coordinateuncertaintyinmeters'] = eventData['coordinateuncertaintyinmeters'];
            }
            if(eventData['footprintwkt']){
                this.occurrenceEditData['footprintwkt'] = eventData['footprintwkt'];
            }
            if(eventData['georeferencedby']){
                this.occurrenceEditData['georeferencedby'] = eventData['georeferencedby'];
            }
            if(eventData['georeferenceprotocol']){
                this.occurrenceEditData['georeferenceprotocol'] = eventData['georeferenceprotocol'];
            }
            if(eventData['georeferencesources']){
                this.occurrenceEditData['georeferencesources'] = eventData['georeferencesources'];
            }
            if(eventData['georeferenceverificationstatus']){
                this.occurrenceEditData['georeferenceverificationstatus'] = eventData['georeferenceverificationstatus'];
            }
            if(eventData['georeferenceremarks']){
                this.occurrenceEditData['georeferenceremarks'] = eventData['georeferenceremarks'];
            }
            this.occurrenceEditData['minimumdepthinmeters'] = eventData['minimumdepthinmeters'];
            this.occurrenceEditData['maximumdepthinmeters'] = eventData['maximumdepthinmeters'];
            this.occurrenceEditData['verbatimdepth'] = eventData['verbatimdepth'];
            this.occurrenceEditData['samplingprotocol'] = eventData['samplingprotocol'];
            this.occurrenceEditData['samplingeffort'] = eventData['samplingeffort'];
            if(this.occurrenceEntryFormat === 'replicate'){
                this.occurrenceEditData['labelproject'] = eventData['labelproject'];
            }
        },
        mergeLocationOccurrenceData() {
            const locationData = this.getLocationData;
            this.occurrenceEditData['locationid'] = this.getLocationID;
            this.occurrenceEditData['waterbody'] = locationData['waterbody'];
            this.occurrenceEditData['country'] = locationData['country'];
            this.occurrenceEditData['stateprovince'] = locationData['stateprovince'];
            this.occurrenceEditData['county'] = locationData['county'];
            this.occurrenceEditData['municipality'] = locationData['municipality'];
            this.occurrenceEditData['locality'] = locationData['locality'];
            this.occurrenceEditData['localitysecurity'] = locationData['localitysecurity'];
            this.occurrenceEditData['localitysecurityreason'] = locationData['localitysecurityreason'];
            this.occurrenceEditData['decimallatitude'] = locationData['decimallatitude'];
            this.occurrenceEditData['decimallongitude'] = locationData['decimallongitude'];
            this.occurrenceEditData['geodeticdatum'] = locationData['geodeticdatum'];
            this.occurrenceEditData['coordinateuncertaintyinmeters'] = locationData['coordinateuncertaintyinmeters'];
            this.occurrenceEditData['footprintwkt'] = locationData['footprintwkt'];
            this.occurrenceEditData['coordinateprecision'] = locationData['coordinateprecision'];
            this.occurrenceEditData['locationremarks'] = locationData['locationremarks'];
            this.occurrenceEditData['verbatimcoordinates'] = locationData['verbatimcoordinates'];
            this.occurrenceEditData['verbatimcoordinatesystem'] = locationData['verbatimcoordinatesystem'];
            this.occurrenceEditData['georeferencedby'] = locationData['georeferencedby'];
            this.occurrenceEditData['georeferenceprotocol'] = locationData['georeferenceprotocol'];
            this.occurrenceEditData['georeferencesources'] = locationData['georeferencesources'];
            this.occurrenceEditData['georeferenceverificationstatus'] = locationData['georeferenceverificationstatus'];
            this.occurrenceEditData['georeferenceremarks'] = locationData['georeferenceremarks'];
            this.occurrenceEditData['minimumelevationinmeters'] = locationData['minimumelevationinmeters'];
            this.occurrenceEditData['maximumelevationinmeters'] = locationData['maximumelevationinmeters'];
            this.occurrenceEditData['verbatimelevation'] = locationData['verbatimelevation'];
        },
        mergeSelectedEventOccurrenceData(data, missingOnly) {
            const skipFields = ['occid','collid','dbpk','occurrenceid','catalognumber','othercatalognumbers','ownerinstitutioncode',
                'institutionid','collectionid','datasetid','institutioncode','collectioncode','disposition','storagelocation',
                'processingstatus','recordenteredby','dateentered','datelastmodified'];
            const dataProps = Object.keys(data);
            dataProps.forEach((prop) => {
                if(data[prop] && this.occurrenceEditData.hasOwnProperty(prop) && !skipFields.includes(prop) && (!this.occurrenceEditData[prop] || !missingOnly)){
                    this.occurrenceEditData[prop] = data[prop];
                }
            });
        },
        parseRecordedByLastName(nameStr) {
            let lastName = null;
            const trimmedName = nameStr ? nameStr.trim() : null;
            if(trimmedName){
                let lastNameArr = [];
                let primaryArr = trimmedName.split(';');
                if(primaryArr.length > 0){
                    primaryArr = primaryArr[0].split('&');
                }
                if(primaryArr.length > 0){
                    primaryArr = primaryArr[0].split(' and ');
                }
                if(primaryArr.length > 0){
                    lastNameArr = primaryArr[0].split(',');
                }
                if(lastNameArr.length > 0 && lastNameArr[0]){
                    if(lastNameArr.length > 1){
                        lastName = lastNameArr[0];
                    }
                    else{
                        let tempArr = lastNameArr[0].split(' ');
                        lastName = tempArr.pop();
                        do{
                            lastName = tempArr.pop();
                        }
                        while(tempArr.length > 0 && (lastName.includes('.') || lastName === 'III' || lastName.length < 3));
                    }
                }
            }
            return lastName;
        },
        processMofEditData(dataType, callback = null, id = null) {
            const formData = new FormData();
            formData.append('type', dataType);
            if(dataType === 'event'){
                formData.append('id', id ? id.toString() : this.getCollectingEventID.toString());
                formData.append('editData', JSON.stringify(this.getEventMofEditData));
            }
            else{
                formData.append('id', id ? id.toString() : this.occId.toString());
                formData.append('editData', JSON.stringify(this.getOccurrenceMofEditData));
            }
            formData.append('collid', this.getCollId.toString());
            formData.append('action', 'processMofEdits');
            fetch(occurrenceMeasurementOrFactApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(dataType === 'event'){
                    this.collectingEventStore.setEventMofData(this.getEventMofDataFields);
                }
                else{
                    this.setOccurrenceMofData();
                }
                if(callback){
                    callback(Number(res));
                }
            });
        },
        revertCollectingEventEditData() {
            this.collectingEventStore.revertCollectingEventEditData();
        },
        revertLocationEditData() {
            this.locationStore.revertLocationEditData();
        },
        searchLocations(criteria, callback) {
            this.locationStore.searchLocations(this.getCollId, criteria, callback);
        },
        setChecklistArr() {
            const formData = new FormData();
            formData.append('occid', this.occId.toString());
            formData.append('action', 'getChecklistListByOccurrenceVoucher');
            fetch(checklistVoucherApiUrl, {
                method: 'POST',
                body: formData
            })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    this.checklistArr = data;
                });
        },
        setCollectingEventAutoSearch(value) {
            this.collectingEventAutoSearch = value;
        },
        setCollectingEventCollectionsArr() {
            this.collectingEventStore.setCollectingEventCollectionsArr();
        },
        setCollectingEventFields() {
            this.collectingEventStore.setCollectingEventFields();
        },
        setCollectingEventReplicateData() {
            this.collectingEventStore.setCollectingEventReplicateData();
        },
        setCollection(collid, forceEditor = true, callback = null) {
            this.collectionStore.setCollection(collid, () => {
                if(this.getIsEditor){
                    this.occurrenceEntryFormat = this.getCollectionData['datarecordingmethod'];
                    if(this.occurrenceData.hasOwnProperty('occid')){
                        this.occurrenceEditData = Object.assign({}, this.occurrenceData);
                        if(this.occurrenceEntryFormat === 'lot' || this.occurrenceEntryFormat === 'replicate'){
                            this.setCurrentLocationRecord(this.occurrenceEditData['locationid'] ? this.occurrenceEditData['locationid'] : 0);
                            this.setCurrentCollectingEventRecord(this.occurrenceEditData['eventid'] ? this.occurrenceEditData['eventid'] : 0);
                        }
                        if(Number(this.occurrenceData.occid) > 0){
                            this.setOccurrenceMofData();
                        }
                    }
                    if(this.getCollectingEventID === 0){
                        this.updateCollectingEventEditData('repcount', (this.getCollectionData['defaultrepcount'] ? Number(this.getCollectionData['defaultrepcount']) : 0))
                    }
                    if(callback){
                        callback();
                    }
                }
                else if(forceEditor){
                    window.location.href = this.getClientRoot + '/index.php';
                }
            });
        },
        setCurrentCollectingEventRecord(eventid) {
            if(Number(eventid) > 0){
                this.collectingEventStore.setCurrentCollectingEventRecord(eventid, this.occurrenceEntryFormat, this.getCollectionData['defaultrepcount'], this.getEventMofDataFields, () => {
                    this.setCurrentOccurrenceRecord(this.occId);
                    this.mergeEventOccurrenceData();
                });
            }
        },
        setCurrentDeterminationRecord(detid) {
            this.determinationStore.setCurrentDeterminationRecord(detid);
        },
        setCurrentGeneticLinkageRecord(linkid) {
            this.geneticLinkStore.setCurrentGeneticLinkageRecord(linkid);
        },
        setCurrentLocationRecord(locationid) {
            this.locationStore.setCurrentLocationRecord(locationid, this.getCollId, () => {
                this.mergeLocationOccurrenceData();
                this.collectingEventStore.getLocationCollectingEvents(this.getCollId, locationid);
            });
        },
        setCurrentOccurrenceRecord(occid, callback = null) {
            this.occId = Number(occid);
            this.clearOccurrenceData();
            if(this.occId > 0){
                this.occurrenceEditData = Object.assign({}, {});
                this.setOccurrenceData(callback);
            }
            else{
                this.occurrenceData['collid'] = this.getCollId;
                if(this.entryFollowUpAction === 'newrecordlocation'){
                    this.transferEditLocationDataToOccurrenceData();
                }
                else if(this.entryFollowUpAction === 'newrecordevent'){
                    this.transferEditCollectingEventDataToOccurrenceData();
                }
                else if(this.entryFollowUpAction === 'newrecordclone'){
                    this.occurrenceData = Object.assign({}, this.occurrenceEditData);
                    this.occurrenceData['collid'] = this.getCollId;
                }
                this.occurrenceData['language'] = this.baseStore.getDefaultLanguageName;
                this.occurrenceEditData = Object.assign({}, this.occurrenceData);
                if(this.getLocationID > 0){
                    this.mergeLocationOccurrenceData();
                }
                if(this.getCollectingEventID > 0){
                    this.mergeEventOccurrenceData();
                }
                if(callback){
                    callback();
                }
            }
        },
        setDisplayMode(value) {
            this.displayMode = Number(value);
        },
        setEditArr() {
            const formData = new FormData();
            formData.append('occid', this.occId.toString());
            formData.append('action', 'getOccurrenceEditArr');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    this.editArr = data;
                });
        },
        setEntryFollowUpAction(value) {
            this.entryFollowUpAction = value;
        },
        setLocationFields() {
            this.locationStore.setLocationFields();
        },
        setNewCollectingEventDataFromCurrentOccurrence() {
            this.setCurrentCollectingEventRecord(0);
            this.collectingEventStore.updateCollectingEventEditData('fieldnotes', this.occurrenceEditData['fieldnotes']);
            this.collectingEventStore.updateCollectingEventEditData('fieldnumber', this.occurrenceEditData['fieldnumber']);
            this.collectingEventStore.updateCollectingEventEditData('recordedby', this.occurrenceEditData['recordedby']);
            this.collectingEventStore.updateCollectingEventEditData('recordnumber', this.occurrenceEditData['recordnumber']);
            this.collectingEventStore.updateCollectingEventEditData('recordedbyid', this.occurrenceEditData['recordedbyid']);
            this.collectingEventStore.updateCollectingEventEditData('associatedcollectors', this.occurrenceEditData['associatedcollectors']);
            this.collectingEventStore.updateCollectingEventEditData('eventdate', this.occurrenceEditData['eventdate']);
            this.collectingEventStore.updateCollectingEventEditData('latestdatecollected', this.occurrenceEditData['latestdatecollected']);
            this.collectingEventStore.updateCollectingEventEditData('eventtime', this.occurrenceEditData['eventtime']);
            this.collectingEventStore.updateCollectingEventEditData('year', this.occurrenceEditData['year']);
            this.collectingEventStore.updateCollectingEventEditData('month', this.occurrenceEditData['month']);
            this.collectingEventStore.updateCollectingEventEditData('day', this.occurrenceEditData['day']);
            this.collectingEventStore.updateCollectingEventEditData('startdayofyear', this.occurrenceEditData['startdayofyear']);
            this.collectingEventStore.updateCollectingEventEditData('enddayofyear', this.occurrenceEditData['enddayofyear']);
            this.collectingEventStore.updateCollectingEventEditData('verbatimeventdate', this.occurrenceEditData['verbatimeventdate']);
            if(this.occurrenceEntryFormat === 'replicate'){
                this.collectingEventStore.updateCollectingEventEditData('habitat', this.occurrenceEditData['habitat']);
            }
            this.collectingEventStore.updateCollectingEventEditData('substrate', this.occurrenceEditData['substrate']);
            this.collectingEventStore.updateCollectingEventEditData('localitysecurity', this.occurrenceEditData['localitysecurity']);
            this.collectingEventStore.updateCollectingEventEditData('localitysecurityreason', this.occurrenceEditData['localitysecurityreason']);
            this.collectingEventStore.updateCollectingEventEditData('decimallatitude', this.occurrenceEditData['decimallatitude']);
            this.collectingEventStore.updateCollectingEventEditData('decimallongitude', this.occurrenceEditData['decimallongitude']);
            this.collectingEventStore.updateCollectingEventEditData('geodeticdatum', this.occurrenceEditData['geodeticdatum']);
            this.collectingEventStore.updateCollectingEventEditData('coordinateuncertaintyinmeters', this.occurrenceEditData['coordinateuncertaintyinmeters']);
            this.collectingEventStore.updateCollectingEventEditData('footprintwkt', this.occurrenceEditData['footprintwkt']);
            this.collectingEventStore.updateCollectingEventEditData('georeferencedby', this.occurrenceEditData['georeferencedby']);
            this.collectingEventStore.updateCollectingEventEditData('georeferenceprotocol', this.occurrenceEditData['georeferenceprotocol']);
            this.collectingEventStore.updateCollectingEventEditData('georeferencesources', this.occurrenceEditData['georeferencesources']);
            this.collectingEventStore.updateCollectingEventEditData('georeferenceverificationstatus', this.occurrenceEditData['georeferenceverificationstatus']);
            this.collectingEventStore.updateCollectingEventEditData('georeferenceremarks', this.occurrenceEditData['georeferenceremarks']);
            this.collectingEventStore.updateCollectingEventEditData('minimumdepthinmeters', this.occurrenceEditData['minimumdepthinmeters']);
            this.collectingEventStore.updateCollectingEventEditData('maximumdepthinmeters', this.occurrenceEditData['maximumdepthinmeters']);
            this.collectingEventStore.updateCollectingEventEditData('verbatimdepth', this.occurrenceEditData['verbatimdepth']);
            this.collectingEventStore.updateCollectingEventEditData('samplingprotocol', this.occurrenceEditData['samplingprotocol']);
            this.collectingEventStore.updateCollectingEventEditData('samplingeffort', this.occurrenceEditData['samplingeffort']);
            if(this.occurrenceEntryFormat === 'replicate'){
                this.collectingEventStore.updateCollectingEventEditData('labelproject', this.occurrenceEditData['labelproject']);
            }
        },
        setOccurrenceCollectionData() {
            this.occurrenceEditData['basisofrecord'] = this.getCollectionData['colltype'];
            this.occurrenceEditData['institutioncode'] = this.getCollectionData['institutioncode'];
            this.occurrenceEditData['collectioncode'] = this.getCollectionData['collectioncode'];
        },
        setOccurrenceData(callback) {
            const formData = new FormData();
            formData.append('occid', this.occId.toString());
            formData.append('collid', this.getCollId.toString());
            formData.append('action', 'getOccurrenceDataLock');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                this.isLocked = Number(res) === 1;
                if(!this.isLocked && Number(this.occId) > 0){
                    const formData = new FormData();
                    formData.append('occid', this.occId.toString());
                    formData.append('action', 'getOccurrenceDataArr');
                    fetch(occurrenceApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        return response.ok ? response.json() : null;
                    })
                    .then((data) => {
                        if(data.hasOwnProperty('occid') && Number(data.occid) > 0){
                            this.occurrenceData = Object.assign({}, data);
                            this.setOccurrenceDeterminationArr();
                            this.setEditArr();
                            this.setOccurrenceImageArr();
                            this.setOccurrenceMediaArr();
                            this.setChecklistArr();
                            this.geneticLinkStore.setGeneticLinkArr(this.occId);
                            if(this.getCollId !== Number(this.occurrenceData.collid)){
                                this.setCollection(this.occurrenceData.collid, true, callback);
                            }
                            else{
                                this.occurrenceEditData = Object.assign({}, this.occurrenceData);
                                if(this.occurrenceEntryFormat === 'lot' || this.occurrenceEntryFormat === 'replicate'){
                                    this.setCurrentLocationRecord(this.occurrenceEditData['locationid'] ? this.occurrenceEditData['locationid'] : 0);
                                    this.setCurrentCollectingEventRecord(this.occurrenceEditData['eventid'] ? this.occurrenceEditData['eventid'] : 0);
                                }
                                this.setOccurrenceMofData();
                                if(callback){
                                    callback();
                                }
                            }
                        }
                        else if(this.getCollId > 0){
                            this.setCurrentOccurrenceRecord(0);
                            if(callback){
                                callback();
                            }
                        }
                        else{
                            const baseStore = useBaseStore();
                            window.location.href = baseStore.getClientRoot + '/index.php';
                        }
                    });
                }
            });
        },
        setOccurrenceDeterminationArr() {
            this.determinationStore.setDeterminationArr(this.occId);
        },
        setOccurrenceEntryFormat(value) {
            this.occurrenceEntryFormat = value;
        },
        setOccurrenceFields() {
            const formData = new FormData();
            formData.append('action', 'getOccurrenceFields');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.occurrenceFields = Object.assign({}, data);
                this.setOccurrenceFieldDefinitions();
            });
        },
        setOccurrenceFieldDefinitions() {
            fetch(fieldDefinitionsUrl)
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('occurrence')){
                    this.occurrenceFieldDefinitions = Object.assign({}, data['occurrence']);
                }
            });
        },
        setOccurrenceImageArr() {
            this.imageStore.setImageArr('occid', this.occId);
        },
        setOccurrenceMediaArr() {
            this.mediaStore.setMediaArr('occid', this.occId);
        },
        setOccurrenceMofData() {
            const formData = new FormData();
            formData.append('type', 'occurrence');
            formData.append('id', this.occId.toString());
            formData.append('action', 'getMofDataByTypeAndId');
            fetch(occurrenceMeasurementOrFactApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                Object.keys(this.getOccurrenceMofDataFields).forEach(field => {
                    this.occurrenceMofData[field] = (data && data.hasOwnProperty(field)) ? data[field] : null;
                });
                this.occurrenceMofEditData = Object.assign({}, this.occurrenceMofData);
            });
        },
        transferEditCollectingEventDataToOccurrenceData() {
            this.transferEditLocationDataToOccurrenceData();
            this.occurrenceData['eventid'] = this.occurrenceEditData['eventid'];
            this.occurrenceData['fieldnotes'] = this.occurrenceEditData['fieldnotes'];
            this.occurrenceData['fieldnumber'] = this.occurrenceEditData['fieldnumber'];
            this.occurrenceData['recordedby'] = this.occurrenceEditData['recordedby'];
            this.occurrenceData['recordnumber'] = this.occurrenceEditData['recordnumber'];
            this.occurrenceData['recordedbyid'] = this.occurrenceEditData['recordedbyid'];
            this.occurrenceData['associatedcollectors'] = this.occurrenceEditData['associatedcollectors'];
            this.occurrenceData['eventdate'] = this.occurrenceEditData['eventdate'];
            this.occurrenceData['latestdatecollected'] = this.occurrenceEditData['latestdatecollected'];
            this.occurrenceData['eventtime'] = this.occurrenceEditData['eventtime'];
            this.occurrenceData['year'] = this.occurrenceEditData['year'];
            this.occurrenceData['month'] = this.occurrenceEditData['month'];
            this.occurrenceData['day'] = this.occurrenceEditData['day'];
            this.occurrenceData['startdayofyear'] = this.occurrenceEditData['startdayofyear'];
            this.occurrenceData['enddayofyear'] = this.occurrenceEditData['enddayofyear'];
            this.occurrenceData['verbatimeventdate'] = this.occurrenceEditData['verbatimeventdate'];
            if(this.occurrenceEntryFormat === 'replicate'){
                this.occurrenceData['habitat'] = this.occurrenceEditData['habitat'];
            }
            this.occurrenceData['substrate'] = this.occurrenceEditData['substrate'];
            this.occurrenceData['minimumdepthinmeters'] = this.occurrenceEditData['minimumdepthinmeters'];
            this.occurrenceData['maximumdepthinmeters'] = this.occurrenceEditData['maximumdepthinmeters'];
            this.occurrenceData['verbatimdepth'] = this.occurrenceEditData['verbatimdepth'];
            this.occurrenceData['samplingprotocol'] = this.occurrenceEditData['samplingprotocol'];
            this.occurrenceData['samplingeffort'] = this.occurrenceEditData['samplingeffort'];
            if(this.occurrenceEntryFormat === 'replicate'){
                this.occurrenceData['labelproject'] = this.occurrenceEditData['labelproject'];
            }
        },
        transferEditLocationDataToOccurrenceData() {
            this.occurrenceData['locationid'] = this.occurrenceEditData['locationid'];
            this.occurrenceData['island'] = this.occurrenceEditData['island'];
            this.occurrenceData['islandgroup'] = this.occurrenceEditData['islandgroup'];
            this.occurrenceData['waterbody'] = this.occurrenceEditData['waterbody'];
            this.occurrenceData['continent'] = this.occurrenceEditData['continent'];
            this.occurrenceData['country'] = this.occurrenceEditData['country'];
            this.occurrenceData['stateprovince'] = this.occurrenceEditData['stateprovince'];
            this.occurrenceData['county'] = this.occurrenceEditData['county'];
            this.occurrenceData['municipality'] = this.occurrenceEditData['municipality'];
            this.occurrenceData['locality'] = this.occurrenceEditData['locality'];
            this.occurrenceData['localitysecurity'] = this.occurrenceEditData['localitysecurity'];
            this.occurrenceData['localitysecurityreason'] = this.occurrenceEditData['localitysecurityreason'];
            this.occurrenceData['decimallatitude'] = this.occurrenceEditData['decimallatitude'];
            this.occurrenceData['decimallongitude'] = this.occurrenceEditData['decimallongitude'];
            this.occurrenceData['geodeticdatum'] = this.occurrenceEditData['geodeticdatum'];
            this.occurrenceData['coordinateuncertaintyinmeters'] = this.occurrenceEditData['coordinateuncertaintyinmeters'];
            this.occurrenceData['footprintwkt'] = this.occurrenceEditData['footprintwkt'];
            this.occurrenceData['coordinateprecision'] = this.occurrenceEditData['coordinateprecision'];
            this.occurrenceData['locationremarks'] = this.occurrenceEditData['locationremarks'];
            this.occurrenceData['verbatimcoordinates'] = this.occurrenceEditData['verbatimcoordinates'];
            this.occurrenceData['verbatimcoordinatesystem'] = this.occurrenceEditData['verbatimcoordinatesystem'];
            this.occurrenceData['georeferencedby'] = this.occurrenceEditData['georeferencedby'];
            this.occurrenceData['georeferenceprotocol'] = this.occurrenceEditData['georeferenceprotocol'];
            this.occurrenceData['georeferencesources'] = this.occurrenceEditData['georeferencesources'];
            this.occurrenceData['georeferenceverificationstatus'] = this.occurrenceEditData['georeferenceverificationstatus'];
            this.occurrenceData['georeferenceremarks'] = this.occurrenceEditData['georeferenceremarks'];
            this.occurrenceData['minimumelevationinmeters'] = this.occurrenceEditData['minimumelevationinmeters'];
            this.occurrenceData['maximumelevationinmeters'] = this.occurrenceEditData['maximumelevationinmeters'];
            this.occurrenceData['verbatimelevation'] = this.occurrenceEditData['verbatimelevation'];
        },
        transferOccurrenceRecord(transferToCollid, callback) {
            const formData = new FormData();
            formData.append('occid', this.occId.toString());
            formData.append('transferToCollid', transferToCollid);
            formData.append('action', 'transferOccurrenceRecord');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(res);
                    this.setCurrentOccurrenceRecord(this.occId);
                });
            });
        },
        updateCollectingEventEditData(key, value) {
            this.collectingEventStore.updateCollectingEventEditData(key, value);
        },
        updateCollectingEventLocation(locationid, callback) {
            this.collectingEventStore.updateCollectingEventLocation(this.getCollId, locationid, (res) => {
                if(Number(res) === 1){
                    this.setCurrentLocationRecord(locationid);
                }
                callback(Number(res));
            });
        },
        updateCollectingEventRecord(callback) {
            this.collectingEventStore.updateCollectingEventRecord(this.getCollId, callback);
        },
        updateDeterminationEditData(key, value) {
            this.determinationStore.updateDeterminationEditData(key, value);
        },
        updateEventMofEditData(key, value) {
            this.collectingEventStore.updateEventMofEditData(key, value);
        },
        updateGeneticLinkageEditData(key, value) {
            this.geneticLinkStore.updateGeneticLinkageEditData(key, value);
        },
        updateLocationEditData(key, value) {
            this.locationStore.updateLocationEditData(key, value);
        },
        updateLocationRecord(callback) {
            this.locationStore.updateLocationRecord(this.getCollId, callback);
        },
        updateOccurrenceDeterminationRecord(callback) {
            const isCurrent = Number(this.determinationStore.getDeterminationData['iscurrent']) === 1;
            this.determinationStore.updateDeterminationRecord(this.getCollId, (res) => {
                callback(Number(res));
                if(isCurrent){
                    this.setCurrentOccurrenceRecord(this.occId);
                }
                else{
                    this.setOccurrenceDeterminationArr();
                }
            });
        },
        updateOccurrenceEditData(key, value) {
            this.occurrenceEditData[key] = value;
            if(key === 'locationid' || (this.getEmbeddedOccurrenceRecord && this.getEventRecordFields && this.getEventRecordFields.includes(key) && this.getCollectingEventID > 0)){
                this.updateCollectingEventEditData(key, value);
            }
        },
        updateOccurrenceEditDataDate(dateData) {
            this.occurrenceEditData['eventdate'] = dateData ? dateData['date'] : null;
            this.occurrenceEditData['year'] = dateData ? dateData['year'] : null;
            this.occurrenceEditData['month'] = dateData ? dateData['month'] : null;
            this.occurrenceEditData['day'] = dateData ? dateData['day'] : null;
            this.occurrenceEditData['startdayofyear'] = dateData ? dateData['startDayOfYear'] : null;
            this.occurrenceEditData['enddayofyear'] = dateData ? dateData['endDayOfYear'] : null;

        },
        updateOccurrenceEditDataTaxon(taxon) {
            this.occurrenceEditData['sciname'] = taxon ? taxon.sciname : null;
            this.occurrenceEditData['tid'] = taxon ? taxon.tid : null;
            this.occurrenceEditData['family'] = taxon ? taxon.family : null;
            this.occurrenceEditData['scientificnameauthorship'] = taxon ? taxon.author : null;
            this.occurrenceEditData['taxonData'] = taxon ? Object.assign({}, taxon) : null;
        },
        updateOccurrenceEvent(eventid, updateData, callback) {
            const formData = new FormData();
            formData.append('collid', this.getCollId.toString());
            formData.append('occid', this.occId.toString());
            formData.append('eventid', eventid.toString());
            formData.append('updateData', (updateData ? '1' : '0'));
            formData.append('action', 'updateOccurrenceEvent');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                });
            });
        },
        updateOccurrenceGeneticLinkageRecord(callback) {
            this.geneticLinkStore.updateGeneticLinkageRecord(this.getCollId, (res) => {
                callback(Number(res));
                if(Number(res) === 1){
                    this.geneticLinkStore.setGeneticLinkArr(this.occId);
                }
            });
        },
        updateOccurrenceLocation(locationid, updateData, callback) {
            const formData = new FormData();
            formData.append('collid', this.getCollId.toString());
            formData.append('occid', this.occId.toString());
            formData.append('locationid', locationid.toString());
            formData.append('updateData', (updateData ? '1' : '0'));
            formData.append('action', 'updateOccurrenceLocation');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                });
            });
        },
        updateOccurrenceMofEditData(key, value) {
            this.occurrenceMofEditData[key] = value;
        },
        updateOccurrenceRecord(callback) {
            const formData = new FormData();
            formData.append('collid', this.getCollId.toString());
            formData.append('occid', this.occId.toString());
            formData.append('occurrenceData', JSON.stringify(this.occurrenceUpdateData));
            formData.append('action', 'updateOccurrenceRecord');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    if(res && Number(res) === 1){
                        if(this.occurrenceUpdateData.hasOwnProperty('sciname') || this.occurrenceUpdateData.hasOwnProperty('tid')){
                            this.determinationStore.setDeterminationArr(this.occId);
                        }
                        this.occurrenceData = Object.assign({}, this.occurrenceEditData);
                        if(this.getCollectingEventID === 0 && Number(this.occurrenceData['eventid']) > 0){
                            this.setCurrentCollectingEventRecord(this.occurrenceData['eventid']);
                        }
                    }
                    if(Number(res) === 1 && this.getCollectingEventID > 0 && (this.occurrenceUpdateData.hasOwnProperty('locationid') || this.getEmbeddedOccurrenceRecord) && this.getCollectingEventEditsExist){
                        this.updateCollectingEventRecord(callback);
                    }
                    else{
                        callback(Number(res));
                    }
                });
            });
        }
    }
});
