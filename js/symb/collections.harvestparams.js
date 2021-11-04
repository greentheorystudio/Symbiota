$(document).ready(function() {
    $( "#taxa" )
        .bind( "keydown", function( event ) {
            if ( event.keyCode == $.ui.keyCode.TAB &&
                $( this ).data( "autocomplete" ).menu.active ) {
                event.preventDefault();
            }
        })
        .autocomplete({
            source: function( request, response ) {
                $.getJSON( "rpc/taxalist.php", {
                    term: extractLast( request.term ), t: function() { return document.harvestparams.taxontype.value; }
                }, response );
            },
            search: function() {
                const term = extractLast(this.value);
                if ( term.length < 4 ) {
                    return false;
                }
            },
            focus: function() {
                return false;
            },
            select: function( event, ui ) {
                const terms = split(this.value);
                terms.pop();
                terms.push( ui.item.value );
                this.value = terms.join( ", " );
                processTaxaParamChange();
                return false;
            }
        },{});
});

function changeTableDisplay(){
    if(document.getElementById("showtable").checked === true){
        document.harvestparams.action = "listtabledisplay.php";
        sessionStorage.collsearchtableview = true;
    }
    else{
        document.harvestparams.action = "list.php";
        sessionStorage.removeItem('collsearchtableview');
    }
}

function setHarvestParamsForm(){
    const stArr = getSearchTermsArr();
    if(stArr['usethes']){
        document.harvestparams.thes.checked = true;
    }
    if(stArr['taxontype']){
        document.harvestparams.type.value = stArr['taxontype'];
    }
    if(stArr['taxa']){
        document.harvestparams.taxa.value = stArr['taxa'];
    }
    let countryStr = '';
    if (stArr['country']) {
        countryStr = stArr['country'];
        countryArr = countryStr.split(";");
        if (countryArr.indexOf('USA') > -1 || countryArr.indexOf('usa') > -1) {
            countryStr = countryArr[0];
        }
        document.harvestparams.country.value = countryStr;
    }
    if(stArr['state']){
        document.harvestparams.state.value = stArr['state'];
    }
    if(stArr['county']){
        document.harvestparams.county.value = stArr['county'];
    }
    if(stArr['local']){
        document.harvestparams.local.value = stArr['local'];
    }
    if(stArr['elevlow']){
        document.harvestparams.elevlow.value = stArr['elevlow'];
    }
    if(stArr['elevhigh']){
        document.harvestparams.elevhigh.value = stArr['elevhigh'];
    }
    if(stArr['assochost']){
        document.harvestparams.assochost.value = stArr['assochost'];
    }
    if(stArr['upperlat']){
        document.harvestparams.upperlat.value = stArr['upperlat'];
        document.harvestparams.bottomlat.value = stArr['bottomlat'];
        document.harvestparams.leftlong.value = stArr['leftlong'];
        document.harvestparams.rightlong.value = stArr['rightlong'];
    }
    if(stArr['pointlat']){
        document.harvestparams.pointlat.value = stArr['pointlat'];
        document.harvestparams.pointlong.value = stArr['pointlong'];
        document.harvestparams.radius.value = stArr['radius'];
        document.harvestparams.groundradius.value = stArr['groundradius'];
        document.harvestparams.radiustemp.value = stArr['radiustemp'];
        document.harvestparams.radiusunits.value = stArr['radiusunits'];
    }
    if(stArr['polyArr']){
        document.harvestparams.polyArr.value = stArr['polyArr'];
        document.getElementById("spatialParamasNoCriteria").style.display = "none";
        document.getElementById("spatialParamasCriteria").style.display = "block";
    }
    if(stArr['circleArr']){
        document.harvestparams.circleArr.value = stArr['circleArr'];
        document.getElementById("spatialParamasNoCriteria").style.display = "none";
        document.getElementById("spatialParamasCriteria").style.display = "block";
    }
    if(stArr['collector']){
        document.harvestparams.collector.value = stArr['collector'];
    }
    if(stArr['collnum']){
        document.harvestparams.collnum.value = stArr['collnum'];
    }
    if(stArr['eventdate1']){
        document.harvestparams.eventdate1.value = stArr['eventdate1'];
    }
    if(stArr['eventdate2']){
        document.harvestparams.eventdate2.value = stArr['eventdate2'];
    }
    if(stArr['occurrenceRemarks']){
        document.harvestparams.occurrenceRemarks.value = stArr['occurrenceRemarks'];
    }
    if(stArr['catnum']){
        document.harvestparams.catnum.value = stArr['catnum'];
    }
    document.harvestparams.othercatnum.checked = !!stArr['othercatnum'];
    if(stArr['typestatus']){
        document.harvestparams.typestatus.checked = true;
    }
    if(stArr['hasaudio']){
        document.harvestparams.hasaudio.checked = true;
    }
    if(stArr['hasimages']){
        document.harvestparams.hasimages.checked = true;
    }
    if(stArr['hasvideo']){
        document.harvestparams.hasvideo.checked = true;
    }
    if(stArr['hasmedia']){
        document.harvestparams.hasmedia.checked = true;
    }
    if(stArr['hasgenetic']){
        document.harvestparams.hasgenetic.checked = true;
    }
    if(sessionStorage.collsearchtableview){
        document.getElementById('showtable').checked = true;
        changeTableDisplay();
    }
}

function resetHarvestParamsForm(f){
    f.thes.checked = true;
    f.type.value = 1;
    f.taxa.value = '';
    f.country.value = '';
    f.state.value = '';
    f.county.value = '';
    f.local.value = '';
    f.elevlow.value = '';
    f.elevhigh.value = '';
    if(f.assochost){
        f.assochost.value = '';
    }
    f.upperlat.value = '';
    f.bottomlat.value = '';
    f.leftlong.value = '';
    f.rightlong.value = '';
    f.pointlat.value = '';
    f.pointlong.value = '';
    f.radiustemp.value = '';
    f.radiusunits.value = 'km';
    f.radius.value = '';
    f.polyArr.value = '';
    f.circleArr.value = '';
    f.collector.value = '';
    f.collnum.value = '';
    f.eventdate1.value = '';
    f.eventdate2.value = '';
    f.occurrenceRemarks.value = '';
    f.catnum.value = '';
    f.othercatnum.checked = true;
    f.typestatus.checked = false;
    f.hasaudio.checked = false;
    f.hasimages.checked = false;
    f.hasvideo.checked = false;
    f.hasmedia.checked = false;
    f.hasgenetic.checked = false;
    document.getElementById('showtable').checked = false;
    document.getElementById("spatialParamasNoCriteria").style.display = "block";
    document.getElementById("spatialParamasCriteria").style.display = "none";
    changeTableDisplay();
    processTaxaParamChange();
    processTextParamChange();
    setSpatialSearchTerms();
}
