function processGbifOrgKey(){
	const gbifInstOrgKey = document.getElementById("gbifInstOrgKey").value;
	const gbifOrgKey = document.getElementById("gbifOrgKey").value;
	let gbifInstKey = document.getElementById("gbifInstKey").value;
	let gbifDatasetKey = document.getElementById("gbifDataKey").value;
	let gbifEndpointKey = document.getElementById("gbifEndKey").value;
	const dwcUri = document.getElementById("dwcUri").value;
	const portalName = document.getElementById("portalname").value;
	const collName = document.getElementById("collname").value;
	if(!gbifInstKey){
		gbifInstKey = findInstKey();
	}

	if(gbifInstOrgKey && gbifOrgKey){
		if(!gbifInstKey){
			gbifInstKey = createGbifInstallation(gbifInstOrgKey,portalName);
		}
		if(!gbifDatasetKey){
			gbifDatasetKey = createGbifDataset(gbifInstKey,gbifOrgKey,collName);
		}
		if(gbifDatasetKey){
			if(dwcUri){
				gbifEndpointKey = createGbifEndpoint(gbifDatasetKey, dwcUri);
				if(gbifEndpointKey){
					document.getElementById("aggKeysStr").value = JSON.stringify({
						organizationKey: gbifOrgKey,
						installationKey: gbifInstKey,
						datasetKey: gbifDatasetKey,
						endpointKey: gbifEndpointKey
					});
				}
			}
			else{
				alert('Please create/refresh your Darwin Core Archive and try again.');
				return false;
			}
		}
		else{
			alert('Invalid Organization Key or insufficient permissions. Please recheck your Organization Key and verify that this portal can create datasets for your organization with GBIF.');
			return false;
		}
		return true;
	}
	else{
		alert('Please enter an Organization Key.');
		return false;
	}
}

function createGbifInstallation(gbifOrgKey,collName){
	const type = 'POST';
	const url = 'http://api.gbif.org/v1/installation';
	const data = JSON.stringify({
		organizationKey: gbifOrgKey,
		type: "SYMBIOTA_INSTALLATION",
		title: collName
	});

	return callGbifCurl(type,url,data);
}

function createGbifDataset(gbifInstKey,gbifOrgKey,collName){
	const type = 'POST';
	const url = 'http://api.gbif.org/v1/dataset';
	const data = JSON.stringify({
		installationKey: gbifInstKey,
		publishingOrganizationKey: gbifOrgKey,
		title: collName,
		type: "OCCURRENCE"
	});

	return callGbifCurl(type,url,data);
}

function createGbifEndpoint(gbifDatasetKey,dwcUri){
	const type = 'POST';
	const url = 'http://api.gbif.org/v1/dataset/' + gbifDatasetKey + '/endpoint';
	const data = JSON.stringify({
		type: "DWC_ARCHIVE",
		url: dwcUri
	});

	return callGbifCurl(type,url,data);
}

function startGbifCrawl(gbifDatasetKey){
	const type = 'POST';
	const url = 'http://api.gbif.org/v1/dataset/' + gbifDatasetKey + '/crawl';
	const data = '';

	callGbifCurl(type,url,data);
	alert('Your data is being updated in GBIF. Please allow 5-10 minutes for completion.')
}

function findInstKey(){
	let key = '';
	$.ajax({
		type: "POST",
		url: "rpc/checkgbifinstall.php",
		async: false,
		success: function(response) {
			key = response;
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(errorThrown);
		}
	});
	return key;
}

function callGbifCurl(type,url,data){
	let key;
	$.ajax({
		type: "POST",
		url: "rpc/getgbifcurl.php",
		data: {type: type, url: url, data: data},
		async: false,
		success: function(response) {
			key = response;
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(errorThrown);
		}
	});
	return key;
}
