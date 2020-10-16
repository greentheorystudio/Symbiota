$(document).ready(function() {
    function split( val ) {
        return val.split( /,\s*/ );
    }
    function extractLast( term ) {
        return split( term ).pop();
    }

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

function updateRadius(){
    const radiusUnits = document.getElementById("radiusunits").value;
    let radiusInMiles = document.getElementById("radiustemp").value;
    if(radiusUnits === "km"){
        radiusInMiles = radiusInMiles * 0.6214;
    }
    document.getElementById("radius").value = radiusInMiles;
}

function setHarvestParamsForm(){
    let coordArr;
    const stArr = JSON.parse(starrJson);
    if(!stArr['usethes']){
        document.harvestparams.thes.checked = false;
    }
    if(stArr['taxontype']){
        document.harvestparams.type.value = stArr['taxontype'];
    }
    if(stArr['taxa']){
        document.harvestparams.taxa.value = stArr['taxa'];
    }
    let countryStr;
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
    if(stArr['boundingBoxArr']){
        coordArr = stArr['boundingBoxArr'].split(';');
        document.harvestparams.upperlat.value = coordArr[0];
        document.harvestparams.bottomlat.value = coordArr[1];
        document.harvestparams.leftlong.value = coordArr[2];
        document.harvestparams.rightlong.value = coordArr[3];
    }
    if(stArr['circleArr']){
        coordArr = stArr['circleArr'].split(';');
        document.harvestparams.pointlat.value = coordArr[0];
        document.harvestparams.pointlong.value = coordArr[1];
        document.harvestparams.radiustemp.value = coordArr[2];
        document.harvestparams.radius.value = coordArr[2]*0.6214;
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
    if(stArr['catnum']){
        document.harvestparams.catnum.value = stArr['catnum'];
    }
    if(stArr['typestatus']){
        document.harvestparams.typestatus.checked = true;
    }
    if(stArr['hasimages']){
        document.harvestparams.hasimages.checked = true;
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
	f.collector.value = '';
	f.collnum.value = '';
	f.eventdate1.value = '';
	f.eventdate2.value = '';
	f.catnum.value = '';
	f.includeothercatnum.checked = true;
	f.typestatus.checked = false;
	f.hasimages.checked = false;
    sessionStorage.removeItem('jsonstarr');
    document.getElementById('showtable').checked = false;
    changeTableDisplay();
}
