$(document).ready(function() {
    $('#fieldguideexport').popup({
        transition: 'all 0.3s',
        scrolllock: true
    });
});
let pdfDocGenerator = '';
const lazyLoadCnt = 100;
let loadIndex = 0;
let zipIndex = 1;
let procIndex = 0;
let processed = 0;
let dataArr = [];
let imagesExist = false;
let tempImgArr = [];
let imgDataArr = [];
let contentArr = [];
let titlePageContent = [];
let leftColContent = [];
let rightColContent = [];
let priDescSource = '';
let secDescSource = '';
let anyDescSource = 0;
let photog = [];
let photoNum = 0;
let zipFile = '';
let zipFolder = '';
let pdfFileNum = 0;
let pdfFileTot = 0;
let projFileName = '';
let savedPDFs = 0;
const t0 = 0;
const t1 = 0;

function hideWorking(){
    $('#loadingOverlay').popup('hide');
    $("#fieldguideexport").popup("show");
    //t1 = performance.now();
    //console.log("Total process took " + ((t1 - t0)/1000) + " seconds.");
}

function showWorking(){
    $("#fieldguideexport").popup("hide");
    $('#loadingOverlay').popup('show');
    //t0 = performance.now();
}

function openFieldGuideExporter(){
    $("#fieldguideexport").popup("show");
}

function prepareFieldGuideExport(taxCnt){
    showWorking();
    processSettings();
    zipIndex = document.getElementById("zipindex").value;
    if(taxCnt > 300){
        if(zipIndex > 1){
            taxCnt = taxCnt - ((zipIndex - 1) * 300);
            loadIndex = ((zipIndex - 1) * 3);
        }
        else{
            taxCnt = 300;
        }
    }
    pdfFileTot = Math.ceil(taxCnt/lazyLoadCnt);
    projFileName = checklistName.replace(/ /g,"_");
    pdfDocGenerator = '';
    dataArr = [];
    tempImgArr = [];
    imgDataArr = [];
    contentArr = [];
    titlePageContent = [];
    leftColContent = [];
    rightColContent = [];
    processed = 0;
    savedPDFs = 0;
    zipFile = '';
    zipFolder = '';
    callDataLoader(taxCnt);
}

function callDataLoader(taxCnt){
    lazyLoadData(loadIndex,function(res){
        processDataResponse(res);
        processed = processed + lazyLoadCnt;
        loadIndex++;
        if(processed < taxCnt){
            callDataLoader(taxCnt);
        }
        else{
            prepImageResponse();
        }
    });
}

function processSettings(){
    priDescSource = document.getElementById("fgPriDescSource").value;
    secDescSource = document.getElementById("fgSecDescSource").value;
    anyDescSource = document.getElementById("fgUseAltDesc").checked;
    if(document.getElementById("fgUseAllPhotog").checked === true){
        photog = 'all';
    }
    else{
        photog = [];
        const dbElements = document.getElementsByName("photog[]");
        for(let i = 0; i < dbElements.length; i++){
            if(dbElements[i].checked){
                photog.push(dbElements[i].value);
            }
        }
    }
    photoNum = $("input[name=fgMaxImages]:checked").val();
}

function processDataResponse(res){
    const tempArr = JSON.parse(res);
    for(let i in tempArr) {
        if(tempArr.hasOwnProperty(i)){
            let family = tempArr[i]['family'];
            if(!family) {
                family = "Family Undefined";
            }
            const sciname = tempArr[i]['sciname'];
            if(!dataArr[family]) {
                dataArr[family] = [];
            }
            if(!dataArr[family][sciname]) {
                dataArr[family][sciname] = [];
            }
            dataArr[family][sciname]['author'] = tempArr[i]['author'];
            dataArr[family][sciname]['order'] = tempArr[i]['order'];
            if(tempArr[i]['vern']) {
                dataArr[family][sciname]['common'] = tempArr[i]['vern'][0];
            }
            if(tempArr[i]['desc']){
                if(tempArr[i]['desc'][priDescSource]){
                    dataArr[family][sciname]['desc'] = tempArr[i]['desc'][priDescSource];
                }
                else if(tempArr[i]['desc'][secDescSource]){
                    dataArr[family][sciname]['desc'] = tempArr[i]['desc'][secDescSource];
                }
                else if(anyDescSource){
                    let x = 0;
                    do{
                        for(let de in tempArr[i]['desc']){
                            if(tempArr[i]['desc'].hasOwnProperty(de)){
                                dataArr[family][sciname]['desc'] = tempArr[i]['desc'][de];
                                x++;
                            }
                        }
                    }
                    while(x < 1);
                }
            }
            dataArr[family][sciname]['images'] = [];
            if(tempArr[i]['img']){
                imagesExist = true;
                for(let im in tempArr[i]['img']){
                    if(tempArr[i]['img'].hasOwnProperty(im)){
                        const imgId = tempArr[i]['img'][im]['id'];
                        const imgUrl = tempArr[i]['img'][im]['url'];
                        if(imgId && imgUrl){
                            dataArr[family][sciname]['images'].push(tempArr[i]['img'][im]);
                            tempImgArr.push(imgId);
                        }
                    }
                }
            }
        }
    }
    procIndex++;
}

function splitArray(arr,size){
    let index;
    const arrLength = arr.length;
    const tempArr = [];
    for (index = 0; index < arrLength; index += size) {
        const subArr = arr.slice(index, (index + size));
        tempArr.push(subArr);
    }

    return tempArr;
}

function prepImageResponse(){
    if(imagesExist){
        tempImgArr = splitArray(tempImgArr,200);
        processImageResponse();
    }
    else{
        createPDFGuides();
    }
}

function processImageResponse(){
    //console.log(tempImgArr.length);
    const reqArrStr = JSON.stringify(tempImgArr[0]);
    loadImageDataUri(reqArrStr,function(res){
        if(res){
            const tempDataArr = res.split("-****-");
            for(let d in tempDataArr){
                if(tempDataArr.hasOwnProperty(d) && tempDataArr[d]){
                    const imgArr = tempDataArr[d].toString().split("-||-");
                    const resId = imgArr[0];
                    const resData = imgArr[1];
                    if(resData) imgDataArr[resId] = resData;
                    //t1 = performance.now();
                    //console.log(resId+" processed at " + ((t1 - t0)/1000) + " seconds.");
                }
            }
        }
        tempImgArr.splice(0,1);
        if(tempImgArr.length > 0){
            processImageResponse();
        }
        else{
            createPDFGuides();
        }
    });
}

function createPDFGuides(){
    pdfFileNum = 1;
    let taxonNum = 0;
    zipFile = new JSZip();
    zipFolder = zipFile.folder("files");
    contentArr = [];
    createTitlePage(pdfFileNum);
    contentArr.push({
        toc: {
            title: {text: 'INDEX', alignment: 'left', style: 'TOCHeader'}
        },
        pageBreak: 'after'
    });
    const familyKeys = Object.keys(dataArr);
    familyKeys.sort();
    for(let i in familyKeys){
        const familyName = familyKeys[i];
        if(typeof familyName === "string"){
            const scinameKeys = Object.keys(dataArr[familyName]);
            scinameKeys.sort();
            for(let s in scinameKeys){
                if(taxonNum === 100){
                    savePDFFile(contentArr);
                    taxonNum = 0;
                    contentArr = [];
                    createTitlePage();
                    contentArr.push({
                        toc: {
                            title: {text: 'INDEX', alignment: 'left', style: 'TOCHeader'}
                        },
                        pageBreak: 'after'
                    });
                }
                const sciname = scinameKeys[s];
                if(typeof sciname === "string"){
                    createPDFPage(familyName,sciname);
                    taxonNum++;
                }
            }
        }
    }
    savePDFFile(contentArr);
}

function createTitlePage(){
    let fileNumber;
    if(zipIndex > 1) {
        fileNumber = ((zipIndex * 3) + pdfFileNum);
    }
    else {
        fileNumber = pdfFileNum;
    }
    const titlePageHead = checklistName + ' Vol. ' + fileNumber;
    const titleTextArr = [];
    titlePageContent = [];
    titlePageContent.push({text: titlePageHead, style: 'titleHeader'});
    if(checklistAuthors){
        titleTextArr.push({text: 'Authors: ', style: 'descheadtext'});
        titleTextArr.push({text: checklistAuthors, style: 'descstattext'});
        titleTextArr.push('\n');
    }
    if(checklistCitation){
        titleTextArr.push({text: 'Citation: ', style: 'descheadtext'});
        titleTextArr.push({text: checklistCitation, style: 'descstattext'});
        titleTextArr.push('\n');
    }
    if(checklistLocality){
        titleTextArr.push({text: 'Locality: ', style: 'descheadtext'});
        titleTextArr.push({text: checklistLocality, style: 'descstattext'});
        titleTextArr.push('\n');
    }
    if(checklistAbstract){
        titleTextArr.push({text: 'Abstract: ', style: 'descheadtext'});
        titleTextArr.push({text: checklistAbstract, style: 'descstattext'});
        titleTextArr.push('\n');
    }
    if(checklistNotes){
        titleTextArr.push({text: 'Notes: ', style: 'descheadtext'});
        titleTextArr.push({text: checklistNotes, style: 'descstattext'});
        titleTextArr.push('\n');
    }
    titlePageContent.push({text: titleTextArr});
    titlePageContent.push({text: fieldguideDisclaimer, style: 'titleDisclaimer'});
    contentArr.push({
        stack: titlePageContent,
        pageBreak: 'after'
    });
}

function createPDFPage(familyName,sciname){
    //console.log(sciname);
    leftColContent = [];
    rightColContent = [];
    const taxonOrder = dataArr[familyName][sciname]['order'];
    const scinameAuthor = dataArr[familyName][sciname]['author'];
    let commonName = '';
    let descArr = [];
    let imgArr = [];
    if(dataArr[familyName][sciname]['common']) commonName = dataArr[familyName][sciname]['common'];
    if(dataArr[familyName][sciname]['desc']) descArr = dataArr[familyName][sciname]['desc'];
    if(dataArr[familyName][sciname]['images']) imgArr = dataArr[familyName][sciname]['images'];
    leftColContent.push({text: taxonOrder, style: 'ordertext'});
    leftColContent.push('\n');
    leftColContent.push({text: familyName, style: 'familytext'});
    leftColContent.push('\n');
    leftColContent.push({text: sciname, style: 'scinametext'});
    leftColContent.push(' ');
    leftColContent.push({text: scinameAuthor, style: 'authortext'});
    if(commonName){
        leftColContent.push('\n');
        leftColContent.push({text: commonName, style: 'commontext'});
    }
    leftColContent.push('\n\n');
    if(Object.keys(descArr).length !== 0){
        const source = descArr.source;
        delete descArr.source;
        for(let d in descArr){
            if(descArr.hasOwnProperty(d)){
                if(descArr[d]['heading']){
                    leftColContent.push({text: descArr[d]['heading']+':', style: 'descheadtext'});
                    leftColContent.push(' ');
                }
                if(descArr[d]['statement']){
                    leftColContent.push({text: descArr[d]['statement'], style: 'descstattext'});
                    leftColContent.push(' ');
                }
            }
        }
        if(source){
            leftColContent.push('\n');
            leftColContent.push({text: source, style: 'descsourcetext', alignment: 'right'});
        }
    }
    else{
        leftColContent.push({text: 'No Description Available', style: 'nodesctext'});
    }
    if(imgArr.length > 0){
        for(let p in imgArr){
            if(imgArr.hasOwnProperty(p)){
                const imgid = imgArr[p]['id'];
                const owner = imgArr[p]['owner'];
                const photographer = imgArr[p]['photographer'];
                if(imgDataArr[imgid]){
                    let tempArr = [];
                    let creditStr = '';
                    tempArr.push({image: imgDataArr[imgid], width: 150, alignment: 'right'});
                    rightColContent.push(tempArr);
                    if(photographer){
                        creditStr = 'Photograph by: '+photographer;
                    }
                    if(owner){
                        creditStr += (photographer?'\n':'')+owner;
                    }
                    if(creditStr){
                        tempArr = [];
                        tempArr.push({text: creditStr, style: 'imageCredit', alignment: 'right'});
                        rightColContent.push(tempArr);
                    }
                }
            }
        }
    }

    const leftColArr = {
        width: 340,
        text: leftColContent
    };
    let rightColArr;
    if(rightColContent.length > 1){
        rightColArr = {
            table: {
                widths: [160],
                body: rightColContent
            },
            layout: 'noBorders'

        };
    }
    else{
        rightColArr = {
            table: {
                widths: [160],
                body: [rightColContent]
            },
            layout: 'noBorders'

        };
    }
    const pageArr = {
        columns: [leftColArr, rightColArr],
        pageBreak: 'after'
    };
    const TOCString = familyName + ': ' + sciname;
    contentArr.push({text: TOCString, tocItem: true, alignment: 'left', margin: [-500, 0, 0, 0]});
    contentArr.push(pageArr);
}

function savePDFFile(content){
    let fileNumber;
    if(zipIndex > 1) {
        fileNumber = ((zipIndex * 3) + pdfFileNum);
    }
    else {
        fileNumber = pdfFileNum;
    }
    const filename = projFileName + '-' + fileNumber + '.pdf';
    pdfFileNum++;
    const docDefinition = {
        content: content,
        footer: function (page) {
            return [
                {canvas: [{type: 'line', x1: 20, y1: 0, x2: 595 - 20, y2: 0, lineWidth: 1}]},
                {
                    columns: [
                        {
                            width: 400,
                            text: checklistName + ' Vol. ' + fileNumber,
                            alignment: 'right',
                            style: 'checkListName',
                            margin: [20, 10, 20, 10]
                        },
                        {
                            width: 200,
                            columns: [
                                {
                                    width: 60,
                                    text: page, alignment: 'left', style: 'pageNumber', margin: [20, 10, 20, 10]
                                },
                                {
                                    width: 140,
                                    text: 'Back to Contents',
                                    alignment: 'right',
                                    style: 'TOCLink',
                                    margin: [0, 10, 40, 10],
                                    linkToPage: 1
                                }


                            ]
                        }
                    ]
                }
            ];
        },
        styles: {
            titleHeader: {
                fontSize: 18,
                bold: true,
                alignment: 'center',
                margin: [0, 190, 0, 150]
            },
            titleDisclaimer: {
                fontSize: 10,
                alignment: 'left',
                margin: [0, 50, 0, 0]
            },
            ordertext: {
                fontSize: 11.5,
                bold: true
            },
            familytext: {
                fontSize: 16,
                bold: true
            },
            scinametext: {
                fontSize: 18,
                italics: true
            },
            authortext: {
                fontSize: 10
            },
            commontext: {
                fontSize: 10.5
            },
            descheadtext: {
                fontSize: 11,
                bold: true
            },
            nodesctext: {
                fontSize: 15,
                bold: true
            },
            descstattext: {
                fontSize: 10.5
            },
            descsourcetext: {
                fontSize: 8
            },
            checkListName: {
                fontSize: 9,
                bold: true
            },
            pageNumber: {
                fontSize: 11,
                bold: true
            },
            TOCLink: {
                fontSize: 10,
                bold: true
            },
            imageCredit: {
                fontSize: 6,
                margin: [0, 0, 0, 13]
            }
        }
    };
    pdfDocGenerator = pdfMake.createPdf(docDefinition);
    pdfDocGenerator.getBase64((data) => {
        zipFolder.file(filename, data.substr(data.indexOf(',')+1), {base64: true});
        savedPDFs++;
        if(savedPDFs === pdfFileTot){
            zipFile.generateAsync({type:"blob"}).then(function(content) {
                const zipfilename = projFileName + zipIndex + '.zip';
                saveAs(content,zipfilename);
                hideWorking();
            });
        }
    });
}

function loadImageDataUri(imgid,callback){
    const http = new XMLHttpRequest();
    const url = "rpc/fieldguideimageprocessor.php";
    const params = 'imgid=' + imgid;
    //console.log(url+'?'+params);
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.onreadystatechange = function() {
        if(http.readyState === 4 && http.status === 200) {
            callback(http.responseText);
        }
    };
    http.send(params);
}

function selectAllPhotog(){
    let boxesChecked = true;
    const selectAll = document.getElementById("fgUseAllPhotog");
    if(!selectAll.checked){
        boxesChecked = false;
    }
    const dbElements = document.getElementsByName("photog[]");
    for(let i = 0; i < dbElements.length; i++){
        dbElements[i].checked = boxesChecked;
    }
}

function checkPhotogSelections(){
    let boxesChecked = true;
    const dbElements = document.getElementsByName("photog[]");
    for(let i = 0; i < dbElements.length; i++){
        if(!dbElements[i].checked) {
            boxesChecked = false;
        }
    }
    document.getElementById("fgUseAllPhotog").checked = boxesChecked;
}
