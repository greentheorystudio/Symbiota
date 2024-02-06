function useCore() {
    const $q = useQuasar();

    function checkObjectNotEmpty(obj) {
        for(const i in obj){
            if(obj.hasOwnProperty(i) && obj[i]){
                return true;
            }
        }
        return false;
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

    function getRgbaStrFromHexOpacity(hex, opacity) {
        const rgbArr = hexToRgb(hex);
        let retStr = '';
        if(rgbArr){
            retStr = 'rgba(' + rgbArr['r'] + ',' + rgbArr['g'] + ',' + rgbArr['b'] + ',' + opacity + ')';
        }
        return retStr;
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

    function openTutorialWindow(url) {
        window.open(url, '_blank');
    }

    function showNotification(type, text) {
        $q.notify({
            type: type,
            icon: null,
            message: text,
            multiLine: true,
            position: 'top',
            timeout: 5000
        });
    }

    function showWorking() {
        $q.loading.show({
            spinner: QSpinnerHourglass,
            spinnerColor: 'primary',
            spinnerSize: 140,
            backgroundColor: 'grey',
            message: 'Loading...',
            messageColor: 'primary',
            customClass: 'text-h4'
        })
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
        checkObjectNotEmpty,
        convertMysqlWKT,
        generateRandHexColor,
        getArrayBuffer,
        getErrorResponseText,
        getRgbaStrFromHexOpacity,
        hexToRgb,
        hideWorking,
        openTutorialWindow,
        showNotification,
        showWorking,
        writeMySQLWktString
    }
}
