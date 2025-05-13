<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$clid = (int)$_REQUEST['clid'];
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Checklist Voucher Download</title>
    <meta name="description" content="Download checklist voucher data">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            const dialogArr = ["schemanative", "schemadwc"];
            let dialogStr = "";
            for(let i=0; i<dialogArr.length; i++){
				dialogStr = dialogArr[i]+"info";
				$( "#"+dialogStr+"dialog" ).dialog({
					autoOpen: false,
					modal: true,
					position: { my: "left top", at: "center", of: "#"+dialogStr }
				});

				$( "#"+dialogStr ).click(function() {
					$( "#"+this.id+"dialog" ).dialog( "open" );
				});
			}
		});

		function extensionSelected(obj){
			if(obj.checked === true){
				obj.form.zip.checked = true;
			}
		}

		function zipSelected(obj){
			if(obj.checked === false){
				obj.form.images.checked = false;
				obj.form.identifications.checked = false;
			}
		}

		function validateDownloadForm(){
			return true;
		}

		function closePage(timeToClose){
			setTimeout(function () {
				window.close();
			}, timeToClose);
		}
	</script>
</head>
<body>
	<div id="mainContainer" style="padding: 10px 15px 15px;">
		<h2>Data Usage Guidelines</h2>
	 	 <div style="margin:15px;">
	 	 	By downloading data, the user confirms that he/she has read and agrees with the general
	 	 	<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/usagepolicy.php">data usage terms</a>.
	 	 	Note that additional terms of use specific to the individual collections
	 	 	may be distributed with the data download. When present, the terms
	 	 	supplied by the owning institution should take precedence over the
	 	 	general terms posted in the above link.
	 	 </div>
		<div style='margin:30px;'>
			<form name="downloadform" action="downloadhandler.php" method="post" onsubmit="return validateDownloadForm();">
				<fieldset>
					<?php
					echo '<legend><b>Download Checklist Occurrence Vouchers</b></legend>';
					?>
					<table>
						<tr>
							<td style="vertical-align:top">
								<div style="margin:10px;">
									<b>Structure:</b>
								</div>
							</td>
							<td>
								<div style="margin:10px 0;">
									<input type="radio" name="schema" value="native" onclick="georefRadioClicked(this)" CHECKED />
									Native
									<a id="schemanativeinfo" href="#" onclick="return false" title="More Information">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
									</a><br/>
									<div id="schemanativeinfodialog">
										Native is very similar to Darwin Core except with the addtion of a few fields
										such as substrate, associated collectors, verbatim description.
									</div>
									<input type="radio" name="schema" value="dwc" onclick="georefRadioClicked(this)" />
									Darwin Core
									<a id="schemadwcinfo" href="#" target="" title="More Information">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
									</a><br/>
									<div id="schemadwcinfodialog">
										Darwin Core (DwC) is a TDWG endorsed exchange standard specifically for biodiversity datasets.
										For more information on what data fields are included in DwC, visit the
										<a href="http://rs.tdwg.org/dwc/index.htm"target='_blank'>DwC Quick Reference Guide</a>.
									</div>
									*<a href='http://rs.tdwg.org/dwc/index.htm' class='bodylink' target='_blank'>What is Darwin Core?</a>
								</div>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top">
								<div style="margin:10px;">
									<b>Data Extensions:</b>
								</div>
							</td>
							<td>
								<div style="margin:10px 0;">
									<input type="checkbox" name="identifications" value="1" onchange="extensionSelected(this)" checked /> include Determination History<br/>
									<input type="checkbox" name="images" value="1" onchange="extensionSelected(this)" checked /> include Image Records<br/>
									*Output must be a compressed archive
								</div>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top">
								<div style="margin:10px;">
									<b>File Format:</b>
								</div>
							</td>
							<td>
								<div style="margin:10px 0;">
									<input type="radio" name="format" value="csv" CHECKED /> Comma Delimited (CSV)<br/>
									<input type="radio" name="format" value="tab" /> Tab Delimited<br/>
								</div>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top">
								<div style="margin:10px;">
									<b>Compression:</b>
								</div>
							</td>
							<td>
								<div style="margin:10px 0;">
									<input type="checkbox" name="zip" value="1" onchange="zipSelected(this)" checked />Compressed ZIP file<br/>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div style="margin:10px;">
									<input name="clid" type="hidden" value="<?php echo $clid; ?>" />
                                    <input name="cset" type="hidden" value="utf-8" />
									<input type="submit" name="submitaction" value="Download Data" />
								</div>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</div>
    <?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    ?>
</body>
</html>
