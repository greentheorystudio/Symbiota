<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title>Associated Species Entry Aid</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link type="text/css" href="../../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" />
	<script type="text/javascript" src="../../js/external/jquery.js"></script>
	<script type="text/javascript" src="../../js/external/jquery-ui.js"></script>
	<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
			$("#taxonname").autocomplete({ source: "../../api/taxa/getassocspp.php" },
			{ minLength: 4, autoFocus: true, delay: 200 });

			$("#taxonname").focus();
		});

		function addName(){
            const nameElem = document.getElementById("taxonname");
            if(nameElem.value){
                let asStr = opener.document.fullform.associatedtaxa.value;
                if(asStr) {
                    asStr = asStr + ", ";
                }
		    	opener.document.fullform.associatedtaxa.value = asStr + nameElem.value;
		    	nameElem.value = "";
		    	nameElem.focus();
		    }
	    }
    </script>
</head>

<body style="background-color:white">
	<div id="innertext" style="background-color:white;">
		<fieldset style="width:450px;">
			<legend><b>Associated Species Entry Aid</b></legend>
			<div style="">
				Taxon: 
				<input id="taxonname" type="text" style="width:350px;" /><br/>
				<input id="transbutton" type="button" value="Add Name" onclick="addName();" />
			</div>
		</fieldset>
	</div>
    <?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    ?>
</body>
</html> 

