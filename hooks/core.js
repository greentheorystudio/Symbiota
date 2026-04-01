function useCore() {
    const $q = useQuasar();

    function capitalizeFirstLetter(string) {
        if(typeof string !== 'string' || string.length === 0) {
            return string;
        }
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    function convertMysqlWKT(wkt) {
        let long;
        let lat;
        let wktStr = '';
        let adjustedStr = '';
        let coordStr = '';
        if(wkt.substring(0,7) === 'POLYGON'){
            adjustedStr = wkt.substring(8,wkt.length-1);
            const adjustedStrArr = adjustedStr.split('),');
            for(let ps in adjustedStrArr){
                if(adjustedStrArr.hasOwnProperty(ps)){
                    coordStr += '(';
                    let subStr = adjustedStrArr[ps].substring(1,adjustedStrArr[ps].length);
                    if(adjustedStrArr[ps].substring(adjustedStrArr[ps].length - 1,adjustedStrArr[ps].length) === ')'){
                        subStr = subStr.substring(0,subStr.length - 1);
                    }
                    const subStrArr = subStr.split(',');
                    for(let ss in subStrArr){
                        if(subStrArr.hasOwnProperty(ss)){
                            const geocoords = subStrArr[ss].split(' ');
                            lat = geocoords[0];
                            long = geocoords[1];
                            coordStr += long+' '+lat+',';
                        }
                    }
                    coordStr = coordStr.substring(0,coordStr.length-1);
                    coordStr += '),';
                }
            }
            coordStr = coordStr.substring(0,coordStr.length-1);
            wktStr = 'POLYGON('+coordStr+')';
        }
        else if(wkt.substring(0,12) === 'MULTIPOLYGON'){
            adjustedStr = wkt.substring(13,wkt.length-1);
            const adjustedStrArr = adjustedStr.split(')),');
            for(let ps in adjustedStrArr){
                if(adjustedStrArr.hasOwnProperty(ps)){
                    coordStr += '(';
                    const subStr = adjustedStrArr[ps].substring(2,adjustedStrArr[ps].length);
                    const subStrArr = subStr.split('),');
                    for(let ss in subStrArr){
                        if(subStrArr.hasOwnProperty(ss)){
                            coordStr += '(';
                            if(subStrArr[ss].substring(subStrArr[ss].length - 2,subStrArr[ss].length) === '))'){
                                subStrArr[ss] = subStrArr[ss].substring(0,subStrArr[ss].length - 2);
                            }
                            const subSubStrArr = subStrArr[ss].split(',');
                            for(let sss in subSubStrArr){
                                if(subSubStrArr.hasOwnProperty(sss)){
                                    const geocoords = subSubStrArr[sss].split(' ');
                                    lat = geocoords[0];
                                    long = geocoords[1];
                                    coordStr += long+' '+lat+',';
                                }
                            }
                            coordStr = coordStr.substring(0,coordStr.length-1);
                            coordStr += '),';
                        }
                    }
                    coordStr = coordStr.substring(0,coordStr.length-1);
                    coordStr += '),';
                }
            }
            coordStr = coordStr.substring(0,coordStr.length-1);
            wktStr = 'MULTIPOLYGON('+coordStr+')';
        }
        return wktStr;
    }

    function convertUtmToDecimalDegrees(zone, easting, northing, datum){
        const d = 0.99960000000000004;
        let d1 = 6378137;
        let d2 = 0.00669438;
        if(datum && datum.match(/nad\s?27/i)){
            d1 = 6378206;
            d2 = 0.006768658;
        }
        else if(datum && datum.match(/nad\s?83/i)){
            d1 = 6378137;
            d2 = 0.00669438;
        }
        const d4 = (1 - Math.sqrt(1 - d2)) / (1 + Math.sqrt(1 - d2));
        const d15 = Number(easting) - 500000;
        const d11 = ((zone - 1) * 6 - 180) + 3;
        const d3 = d2 / (1 - d2);
        const d10 = Number(northing) / d;
        const d12 = d10 / (d1 * (1 - d2 / 4 - (3 * d2 * d2) / 64 - (5 * Math.pow(d2, 3)) / 256));
        const d14 = d12 + ((3 * d4) / 2 - (27 * Math.pow(d4, 3)) / 32) * Math.sin(2 * d12) + ((21 * d4 * d4) / 16 - (55 * Math.pow(d4, 4)) / 32) * Math.sin(4 * d12) + ((151 * Math.pow(d4, 3)) / 96) * Math.sin(6 * d12);
        const d5 = d1 / Math.sqrt(1 - d2 * Math.sin(d14) * Math.sin(d14));
        const d6 = Math.tan(d14) * Math.tan(d14);
        const d7 = d3 * Math.cos(d14) * Math.cos(d14);
        const d8 = (d1 * (1 - d2)) / Math.pow(1 - d2 * Math.sin(d14) * Math.sin(d14), 1.5);
        const d9 = d15 / (d5 * d);
        const d17 = d14 - ((d5 * Math.tan(d14)) / d8) * (((d9 * d9) / 2 - (((5 + 3 * d6 + 10 * d7) - 4 * d7 * d7 - 9 * d3) * Math.pow(d9, 4)) / 24) + (((61 + 90 * d6 + 298 * d7 + 45 * d6 * d6) - 252 * d3 - 3 * d7 * d7) * Math.pow(d9, 6)) / 720);
        const latValue = (d17 / Math.PI) * 180;
        const d18 = ((d9 - ((1 + 2 * d6 + d7) * Math.pow(d9, 3)) / 6) + (((((5 - 2 * d7) + 28 * d6) - 3 * d7 * d7) + 8 * d3 + 24 * d6 * d6) * Math.pow(d9, 5)) / 120) / Math.cos(d14);
        const lngValue = d11 + ((d18 / Math.PI) * 180);
        return (Number(latValue) > 0 && Number(lngValue) > 0) ? {lat: latValue, long: lngValue} : null;
    }

    async function csvToArray(str) {
        str = str.replaceAll('\r\r\n', '');
        const PROCESS_SIZE = 1000;
        let lineTermination;
        const cleanedHeaders = [];
        let resultArr = [];
        if(str.endsWith('\r\n')){
            lineTermination = '\r\n';
        }
        else{
            lineTermination = '\n';
        }
        const headers = str.slice(0, str.indexOf(lineTermination)).split(',');
        headers.forEach(header => {
            header = header.replaceAll('\r', '');
            header = header.replaceAll('\n', '');
            header = header.replaceAll('\b', '');
            cleanedHeaders.push(header);
        });
        if(str.endsWith(lineTermination)){
            str = str.substring(0, str.length - 2);
        }
        const rows = str.slice(str.indexOf(lineTermination) + 1).split(lineTermination);
        const processRows = async (batch) => {
            const promises = batch.map((row) => {
                if(row){
                    const dataObjPattern = new RegExp("(,|\\r?\\n|\\r|^)(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|([^\",\\r\\n]*))",'gi');
                    const values = [];
                    if(row.startsWith(',')) {
                        values.push('');
                    }
                    let dataMatch = null;
                    while(dataMatch = dataObjPattern.exec(row)){
                        let dataValue = '';
                        if(dataMatch[2]){
                            dataValue = dataMatch[2].replace(new RegExp('""', 'g'), '"');
                        }
                        else {
                            dataValue = dataMatch[3];
                        }
                        values.push(dataValue);
                    }
                    if(values.length >= cleanedHeaders.length){
                        return cleanedHeaders.reduce((object, header, index) => {
                            let fieldName = header.trim();
                            if(fieldName.indexOf('"') > -1){
                                fieldName = fieldName.replaceAll('"', '');
                            }
                            let fieldValue = values[index] ? values[index].replaceAll('\r', '') : '';
                            fieldValue = fieldValue.replaceAll('\n', '');
                            fieldValue = fieldValue.replaceAll('\b', '');
                            if(fieldValue.indexOf('"') > -1){
                                fieldValue = fieldValue.replaceAll('"','');
                            }
                            object[fieldName.toLowerCase()] = fieldValue;
                            return object;
                        }, {});
                    }
                    else{
                        return null;
                    }
                }
            });
            return Promise.all(promises);
        };
        for(let i = 0; rows.length > 0; i += PROCESS_SIZE) {
            const batch = rows.slice(0, PROCESS_SIZE);
            rows.splice(0, PROCESS_SIZE);
            resultArr = resultArr.concat(await processRows(batch));
        }
        return resultArr;
    }

    function generateRandHexColor() {
        let hexColor = '';
        const x = Math.round(0xffffff * Math.random()).toString(16);
        const y = (6 - x.length);
        const z = '000000';
        const z1 = z.substring(0, y);
        hexColor = '#' + z1 + x;
        return hexColor;
    }

    function getArrayBuffer(file) {
        return new Promise((resolve) => {
            let bytes = null;
            const reader = new FileReader();
            reader.readAsArrayBuffer(file);
            reader.onload = () => {
                const arrayBuffer = reader.result;
                if(typeof arrayBuffer !== 'string'){
                    bytes = new Uint8Array(arrayBuffer);
                }
                resolve(bytes);
            };
        });
    }

    function getCorrectedPolygonCoordArr(coordArr) {
        const returnArr = [];
        if(coordArr.length > 0){
            coordArr.forEach((coords) => {
                if(Array.isArray(coords[0])){
                    returnArr.push(getCorrectedPolygonCoordArr(coords));
                }
                else if(coords.length === 2 && !isNaN(coords[0]) && !isNaN(coords[1])){
                    returnArr.push([Number(coords[1]), Number(coords[0])]);
                }
            });
        }
        return returnArr;
    }

    function getCurrentDateStr() {
        const today = new Date();
        const year = today.getFullYear();
        let month = today.getMonth() + 1;
        let day = today.getDate();
        if(month < 10){
            month = '0' + month;
        }
        if(day < 10){
            day = '0' + day;
        }
        return `${year}-${month}-${day}`;
    }

    function getErrorResponseText(status, statusText){
        let text;
        if(status === 0){
            text = 'Cancelled';
        }
        else{
            text = 'Error: ' + status + ' ' + statusText;
        }
        return text;
    }

    function getPlatformProperty(prop){
        let value = null;
        if(prop === 'userAgent'){
            value = $q.platform.userAgent;
        }
        else{
            let propArr = prop.split('.');
            if(propArr[0] === 'is'){
                value = $q.platform.is[propArr[1]];
            }
            else if(propArr[0] === 'has'){
                value = $q.platform.has[propArr[1]];
            }
            else if(propArr[0] === 'within'){
                value = $q.platform.within[propArr[1]];
            }
        }
        return value;
    }

    function getRgbaStrFromHexOpacity(hex, opacity) {
        const rgbArr = hexToRgb(hex);
        let retStr = '';
        if(rgbArr){
            retStr = 'rgba(' + rgbArr['r'] + ',' + rgbArr['g'] + ',' + rgbArr['b'] + ',' + opacity + ')';
        }
        return retStr;
    }

    function getSubstringByRegEx(regExStr, text) {
        if(regExStr.startsWith('/')){
            regExStr = regExStr.substring(1);
        }
        if(regExStr.endsWith('/')){
            regExStr = regExStr.substring(0, (regExStr.length - 1));
        }
        const regExObj = new RegExp(regExStr);
        const matchArr = text.match(regExObj);
        return (matchArr && matchArr.length > 1) ? matchArr[1] : null;
    }

    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1],16),
            g: parseInt(result[2],16),
            b: parseInt(result[3],16)
        } : null;
    }

    function hideWorking() {
        $q.loading.hide();
    }

    function parseDate(dateStr){
        const monthNames = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        const validformat1 = /^\d{4}-\d{1,2}-\d{1,2}$/;
        const validformat2 = /^\d{1,2}\/\d{1,2}\/\d{2,4}$/;
        const validformat3 = /^\d{1,2} \D+ \d{2,4}$/;
        const returnData = {
            date: null,
            year: null,
            month: null,
            day: null,
            startDayOfYear: null,
            endDayOfYear: null
        };
        if(dateStr){
            const dateObj = new Date(dateStr);
            let dateTokens;
            try {
                if(validformat1.test(dateStr)){
                    dateTokens = dateStr.split('-');
                    if(dateTokens[0].length === 4){
                        returnData['year'] = Number(dateTokens[0]);
                        returnData['month'] = (Number(dateTokens[1]) >= 1 && Number(dateTokens[1]) <= 12) ? Number(dateTokens[1]) : null;
                        returnData['day'] = (returnData['month'] && (Number(dateTokens[2]) >= 1 && Number(dateTokens[2]) <= 31)) ? Number(dateTokens[2]) : null;
                    }
                }
                else if(validformat2.test(dateStr)){
                    dateTokens = dateStr.split('/');
                    if(dateTokens[2].length === 4){
                        returnData['year'] = Number(dateTokens[2]);
                        returnData['month'] = (Number(dateTokens[0]) >= 1 && Number(dateTokens[0]) <= 12) ? Number(dateTokens[0]) : null;
                        returnData['day'] = (returnData['month'] && (Number(dateTokens[1]) >= 1 && Number(dateTokens[1]) <= 31)) ? Number(dateTokens[1]) : null;
                    }
                }
                else if(validformat3.test(dateStr)){
                    dateTokens = dateStr.split(' ');
                    if(dateTokens[2].length === 4){
                        returnData['year'] = Number(dateTokens[2]);
                        let monthStr = dateTokens[1];
                        monthStr = monthStr.substring(0, 3);
                        monthStr = monthStr.toLowerCase();
                        returnData['month'] = (monthNames.indexOf(monthStr) > -1) ? (monthNames.indexOf(monthStr) + 1) : null;
                        returnData['day'] = (returnData['month'] && (Number(dateTokens[0]) >= 1 && Number(dateTokens[0]) <= 31)) ? Number(dateTokens[0]) : null;
                    }
                }
                else if(dateObj instanceof Date){
                    returnData['year'] = Number(dateObj.getFullYear());
                    returnData['month'] = (dateObj.getMonth() + 1);
                    returnData['day'] = Number(dateObj.getDate());
                }
            } catch (ex) {}
        }
        if(returnData['year']){
            let dateMonthStr = returnData['month'] ? returnData['month'].toString() : '00';
            if(dateMonthStr.length === 1){
                dateMonthStr = '0' + dateMonthStr;
            }
            let dateDayStr = returnData['day'] ? returnData['day'].toString() : '00';
            if(dateDayStr.length === 1){
                dateDayStr = '0' + dateDayStr;
            }
            returnData['date'] = returnData['year'].toString() + '-' + dateMonthStr + '-' + dateDayStr;
            if(returnData['month'] && returnData['day']){
                let startTestDate = new Date(returnData['year'], (returnData['month'] - 1), returnData['day']);
                if(startTestDate instanceof Date){
                    const janFirst = new Date(returnData['year'], 0, 1);
                    returnData['startDayOfYear'] = Math.ceil((startTestDate - janFirst) / 86400000) + 1;
                    let endTestDate = new Date(returnData['year'], 11, 31);
                    if(endTestDate instanceof Date){
                        returnData['endDayOfYear'] = Math.ceil((endTestDate - janFirst) / 86400000) + 1;
                    }
                }
            }
        }
        return returnData;
    }

    function parseFile(file, callback) {
        const CHUNK_SIZE = 512;
        const reader = new FileReader();
        let offset = 0;
        let resultStr = '';

        reader.onload = (event) => {
            if(event.target.result.length > 0) {
                resultStr += event.target.result;
                offset += CHUNK_SIZE;
                readNext();
            }
            else {
                callback(resultStr);
            }
        };

        function readNext() {
            let slice = file.slice(offset, offset + CHUNK_SIZE);
            reader.readAsText(slice);
        }

        readNext();
    }

    function parseScientificName(sciname){
        const returnData = {
            sciname: null,
            parentname: null,
            rankid: null,
            unitind1: null,
            unitname1: null,
            unitind2: null,
            unitname2: null,
            unitind3: null,
            unitname3: null
        };
        returnData['sciname'] = sciname.replaceAll(/^\s+|\s+$/g, '');
        let activeIndex = 0;
        const sciNameArr = returnData['sciname'].split(' ');
        if(sciNameArr[activeIndex].length === 1){
            returnData['unitind1'] = sciNameArr[activeIndex];
            returnData['unitname1'] = sciNameArr[activeIndex + 1];
            activeIndex = 2;
        }
        else{
            returnData['unitname1'] = sciNameArr[activeIndex];
            activeIndex = 1;
        }
        if(sciNameArr.length > activeIndex){
            if(sciNameArr[activeIndex].length === 1){
                returnData['unitind2'] = sciNameArr[activeIndex];
                returnData['unitname2'] = sciNameArr[activeIndex + 1];
                activeIndex += 2;
            }
            else{
                returnData['unitname2'] = sciNameArr[activeIndex];
                activeIndex += 1;
            }
            returnData['rankid'] = 220;
        }
        if(sciNameArr.length > activeIndex){
            if(sciNameArr[activeIndex].substring(sciNameArr[activeIndex].length - 1, sciNameArr[activeIndex].length) === '.' || sciNameArr[activeIndex].length === 1){
                returnData['unitind3'] = sciNameArr[activeIndex];
                returnData['unitname3'] = sciNameArr[activeIndex + 1];
                if(sciNameArr[activeIndex] === 'ssp.' || sciNameArr[activeIndex] === 'subsp.'){
                    returnData['rankid'] = 230;
                }
                else if(sciNameArr[activeIndex] === 'var.'){
                    returnData['rankid'] = 240;
                }
                else if(sciNameArr[activeIndex] === 'f.'){
                    returnData['rankid'] = 260;
                }
                else if(sciNameArr[activeIndex] === 'x' || sciNameArr[activeIndex] === 'X'){
                    returnData['rankid'] = 220;
                }
            }
            else{
                returnData['unitname3'] = sciNameArr[activeIndex];
                returnData['rankid'] = 230;
            }
        }
        if(returnData['unitname1'].length > 4 && (returnData['unitname1'].indexOf('aceae') === (returnData['unitname1'].length - 5) || returnData['unitname1'].indexOf('idae') === (returnData['unitname1'].length - 4))){
            returnData['rankid'] = 140;
        }
        if(returnData['rankid'] > 180){
            if(returnData['rankid'] === 220){
                returnData['parentname'] = returnData['unitname1'];
            }
            else if(returnData['rankid'] > 220){
                returnData['parentname'] = returnData['unitname1'] + ' ' + returnData['unitname2'];
            }
        }
        return returnData;
    }

    function processCsvDownload(csvDataArr, filename) {
        if(typeof csvDataArr === 'object' && csvDataArr.length > 0 && typeof filename === 'string' && filename.length > 0){
            let csvContent = '';
            let headersSaved = false;
            const headerArr = [];
            csvDataArr.forEach(row => {
                const fixedRow = [];
                if(Array.isArray(row)){
                    row.forEach(val => {
                        if(val){
                            val = '\"' + (val ? val : '') + '\"';
                        }
                        fixedRow.push(val);
                    });
                }
                else{
                    for(let key in row) {
                        if(!headersSaved){
                            headerArr.push('\"' + key + '\"');
                        }
                        if(row.hasOwnProperty(key)){
                            const val = '\"' + (row[key] ? row[key] : '') + '\"';
                            fixedRow.push(val);
                        }
                    }
                }
                if(!headersSaved && headerArr.length > 0){
                    csvContent += headerArr.join(',') + '\n';
                    headersSaved = true;
                }
                csvContent += fixedRow.join(',') + '\n';
            });
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8,' });
            const fName = filename + '.csv';
            const elem = window.document.createElement('a');
            elem.href = window.URL.createObjectURL(blob);
            elem.download = fName;
            document.body.appendChild(elem);
            elem.click();
            document.body.removeChild(elem);
        }
    }

    function showNotification(type, text, duration = 5000) {
        $q.notify({
            type: type,
            icon: null,
            message: text,
            multiLine: true,
            position: 'top',
            timeout: duration
        });
    }

    function showWorking(text = null) {
        $q.loading.show({
            spinner: QSpinnerHourglass,
            spinnerColor: 'primary',
            spinnerSize: 140,
            backgroundColor: 'grey',
            message: text,
            messageColor: 'primary',
            customClass: 'text-h4'
        })
    }

    function validatePolygonCoordArr(coordArr) {
        let returnVal = false;
        if(coordArr.length > 0){
            if(Array.isArray(coordArr[0])){
                returnVal = validatePolygonCoordArr(coordArr[0]);
            }
            else if(coordArr.length === 2 && !isNaN(coordArr[0]) && !isNaN(coordArr[1])){
                returnVal = true;
            }
        }
        return returnVal;
    }

    function writeMySQLWktString(type, geocoords) {
        let long, lat;
        let wktStr = '';
        let coordStr = '';
        if(type === 'Polygon'){
            geocoords.forEach((coords, i) => {
                coordStr += '(';
                coords.forEach((coord, ci) => {
                    lat = geocoords[i][ci][1];
                    long = geocoords[i][ci][0];
                    coordStr += lat+' '+long+',';
                });
                coordStr = coordStr.substring(0,coordStr.length-1);
                coordStr += '),';
            });
            coordStr = coordStr.substring(0,coordStr.length-1);
            wktStr = 'POLYGON('+coordStr+')';
        }
        else if(type === 'MultiPolygon'){
            geocoords.forEach((poly, i) => {
                coordStr += '(';
                poly.forEach((coords, r) => {
                    coordStr += '(';
                    coords.forEach((coord, c) => {
                        lat = geocoords[i][r][c][1];
                        long = geocoords[i][r][c][0];
                        coordStr += lat+' '+long+',';
                    });
                    coordStr = coordStr.substring(0,coordStr.length-1);
                    coordStr += '),';
                });
                coordStr = coordStr.substring(0,coordStr.length-1);
                coordStr += '),';
            });
            coordStr = coordStr.substring(0,coordStr.length-1);
            wktStr = 'MULTIPOLYGON('+coordStr+')';
        }

        return wktStr;
    }

    return {
        capitalizeFirstLetter,
        csvToArray,
        convertMysqlWKT,
        convertUtmToDecimalDegrees,
        generateRandHexColor,
        getArrayBuffer,
        getCorrectedPolygonCoordArr,
        getCurrentDateStr,
        getErrorResponseText,
        getPlatformProperty,
        getRgbaStrFromHexOpacity,
        getSubstringByRegEx,
        hexToRgb,
        hideWorking,
        parseFile,
        parseDate,
        parseScientificName,
        processCsvDownload,
        showNotification,
        showWorking,
        validatePolygonCoordArr,
        writeMySQLWktString
    }
}
