<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/OccurrenceEditorManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$occId = (int)$_GET['occid'];
$occIndex = (int)$_GET['occindex'];
$crowdSourceMode = (int)$_GET['csmode'];

$occManager = new OccurrenceEditorMedia();

$occManager->setOccId($occId); 
$occMediaArr = $occManager->getMediaMap();
?>
<script type="text/javascript">
    function processMediaFileChange(f){
        const videoTypes = ['video/mp4', 'video/webm', 'video/ogg'];
        const audioTypes = ['audio/mpeg', 'audio/ogg', 'audio/wav'];
        const fileBox = f.medfile;
        const file = fileBox.files[0];
        if(file){
            f.accessuri.value = '';
            const fileType = file.type;
            const fileName = file.name;
            if(videoTypes.includes(fileType) || audioTypes.includes(fileType)){
                f.format.value = fileType;
                if(videoTypes.includes(fileType)){
                    f.type.value = 'MovingImage';
                }
                if(audioTypes.includes(fileType)){
                    f.type.value = 'Sound';
                }
            }
            else if(fileName.endsWith(".zc")){
                f.type.value = 'Sound';
            }
            else{
                f.type.value = '';
                f.format.value = '';
                alert("The file you are trying to upload is not a supported media filetype.");
                fileBox.value = '';
            }
        }
    }

    function processAccessURIChange(f){
        const url = f.accessuri.value.toLowerCase();
        if(
            url.endsWith(".mp4") ||
            url.endsWith(".webm") ||
            url.endsWith(".ogg") ||
            url.endsWith(".mp3") ||
            url.endsWith(".zc") ||
            url.endsWith(".wav")
        ){
            if(url.endsWith(".mp4")){
                f.type.value = 'MovingImage';
                f.format.value = 'video/mp4';
            }
            if(url.endsWith(".webm")){
                f.type.value = 'MovingImage';
                f.format.value = 'video/webm';
            }
            if(url.endsWith(".ogg")){
                f.type.value = 'MovingImage';
                f.format.value = 'video/ogg';
            }
            if(url.endsWith(".mp3")){
                f.type.value = 'Sound';
                f.format.value = 'audio/mpeg';
            }
            if(url.endsWith(".wav")){
                f.type.value = 'Sound';
                f.format.value = 'audio/wav';
            }
            if(url.endsWith(".zc")){
                f.type.value = 'Sound';
            }
        }
        else{
            alert("The url you entered is not for a supported media filetype.");
            f.accessuri.value.value = '';
        }
    }
</script>
<div id="mediadiv" style="width:795px;">
	<div style="float:right;cursor:pointer;" onclick="toggle('addmediadiv');" title="Add a New Media File">
		<i style="height:20px;width:20px;color:green;" class="fas fa-plus"></i>
	</div>
	<div id="addmediadiv" style="display:<?php echo ($occMediaArr?'none':''); ?>;">
		<form name="medianewform" action="occurrenceeditor.php" method="post" enctype="multipart/form-data" onsubmit="return verifyMediaAddForm(this);">
			<fieldset style="padding:15px">
				<legend><b>Add a New Media File</b></legend>
				<div style='padding:15px;width:90%;border:1px solid yellow;background-color:#FFFF99;'>
					<div class="targetdiv" style="display:block;">
						<div style="font-weight:bold;font-size:110%;margin-bottom:5px;">
							Select a media file located on your computer:
						</div>
				    	<input type='hidden' name='MAX_FILE_SIZE' value='5000000000' />
						<div>
							<input name='medfile' onchange="processMediaFileChange(this.form);" type='file' size='70'/>
						</div>
						<div style="float:right;text-decoration:underline;font-weight:bold;">
							<a href="#" onclick="toggle('targetdiv');return false;">Enter URL</a>
						</div>
					</div>
					<div class="targetdiv" style="display:none;">
						<div style="margin-bottom:10px;">
							Enter a URL to an existing media file already located on a web server.
						</div>
						<div>
							<b>Media File URL:</b><br/>
							<input type='text' name='accessuri' onchange="processAccessURIChange(this.form);" />
						</div>
						<div style="float:right;text-decoration:underline;font-weight:bold;">
							<a href="#" onclick="toggle('targetdiv');return false;">
								Upload Local Media File
							</a>
						</div>
					</div>
				</div>
				<div style="clear:both;margin:20px 0 5px 10px;">
					<b>Title:</b>
					<textarea name="title" rows="2" style="width:300px;resize:vertical;"></textarea>
				</div>
				<div style='margin:0 0 5px 10px;'>
					<b>Creator:</b>
					<select name='creatoruid'>
						<option value="">Select Creator</option>
						<option value="">---------------------------------------</option>
						<?php
							$pArr = $occManager->getPhotographerArr();
							foreach($pArr as $id => $uname){
								echo '<option value="'.$id.'" >';
								echo $uname;
								echo '</option>';
							}
						?>
					</select>
					<a href="#" onclick="toggle('creatoroverride');return false;" title="Display creator override field">
						<i style="height:15px;width:15px;" class="far fa-plus-square"></i>
					</a>
				</div>
				<div id="creatoroverride" style="margin:0 0 5px 10px;display:none;">
					<b>Creator (override):</b>
					<input name='creator' type='text' style="width:300px;">
					*Will override above selection
				</div>
                <div style="margin:0 0 5px 10px;">
                    <b>Description:</b>
                    <textarea name="description" rows="2" style="width:300px;resize:vertical;"></textarea>
                </div>
                <div style="margin:0 0 5px 10px;">
                    <b>Locality:</b>
                    <textarea name="locationcreated" rows="2" style="width:300px;resize:vertical;"></textarea>
                </div>
                <div style="margin:0 0 5px 10px;">
                    <b>Language:</b>
                    <input name='language' type='text' style="width:300px;">
                </div>
				<div style="margin:0 0 5px 10px;">
                    <b>Usage Terms:</b>
                    <textarea name="usageterms" rows="2" style="width:300px;resize:vertical;"></textarea>
                </div>
                <div style="margin:0 0 5px 10px;">
                    <b>Rights:</b>
                    <textarea name="rights" rows="2" style="width:300px;resize:vertical;"></textarea>
                </div>
                <div style="margin:0 0 5px 10px;">
                    <b>Owner:</b>
                    <input name='owner' type='text' style="width:300px;">
                </div>
                <div style="margin:0 0 5px 10px;">
                    <b>Publisher:</b>
                    <input name='publisher' type='text' style="width:300px;">
                </div>
                <div style="margin:0 0 5px 10px;">
                    <b>Contributor:</b>
                    <input name='contributor' type='text' style="width:300px;">
                </div>
                <div style="margin:0 0 5px 10px;">
                    <b>Bibliographic Citation:</b>
                    <textarea name="bibliographiccitation" rows="2" style="width:300px;resize:vertical;"></textarea>
                </div>
                <div style="margin:0 0 5px 10px;">
                    <b>Further Information URL:</b>
                    <textarea name="furtherinformationurl" rows="2" style="width:300px;resize:vertical;"></textarea>
                </div>
				<div style="margin:0 0 5px 10px;">
					<b>Sort Sequence:</b>
					<input name="sortsequence" type="text" size="10" value="" />
				</div>
				<div style="margin:10px 0 10px 20px;">
					<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
					<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
					<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
					<input type="hidden" name="tabindex" value="1" />
                    <input type="hidden" name="type" value="" />
                    <input type="hidden" name="format" value="" />
					<input type="submit" name="submitaction" value="Submit New Media" />
				</div>
			</fieldset>
		</form>
		<hr style="margin:30px 0;" />
	</div>
	<div style="clear:both;margin:15px;">
		<?php
		if($occMediaArr){
			?>
			<table>
				<?php 
				foreach($occMediaArr as $medId => $medArr){
					?>
					<tr>
						<td style="width:300px;text-align:center;padding:20px;">
							<?php
							$medUrl = $medArr['accessuri'];
                            $medFormat = $medArr['format'];
							if($GLOBALS['IMAGE_DOMAIN'] && strncmp($medUrl, '/', 1) === 0) {
                                $medUrl = $GLOBALS['IMAGE_DOMAIN'].$medUrl;
                            }

							if(strncmp($medFormat, 'video/', 6) === 0){
                                echo '<video width="300" controls>';
                                echo '<source src="'.$medUrl.'" type="'.$medFormat.'">';
                                echo '</video>';
                            }
                            elseif(strncmp($medFormat, 'audio/', 6) === 0){
                                echo '<audio style="width:300px;" controls>';
                                echo '<source src="'.$medUrl.'" type="'.$medFormat.'">';
                                echo '</audio>';
                            }
                            elseif(substr($medUrl, -3) === '.zc'){
                                echo '<a href="'.$medUrl.'">Download File</a>';
                            }
							?>
						</td>
						<td style="text-align:left;padding:10px;width:100%;">
							<div style="float:right;cursor:pointer;" onclick="toggle('med<?php echo $medId; ?>editdiv');" title="Edit Media MetaData">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i>
							</div>
							<div style="margin-top:30px">
								<div>
									<b>Title:</b>
									<?php echo wordwrap($medArr['title'],60,'<br />\n',true); ?>
								</div>
								<div>
									<b>Creator:</b>
									<?php
									if($medArr['creator']){
										echo wordwrap($medArr['creator'], 60, '<br />\n', true);
                                    }
									elseif($medArr['creatoruid']){
										$pArr = $occManager->getPhotographerArr();
										echo wordwrap($pArr[$medArr['creatoruid']], 60, '<br />\n', true);
									} 
									?>
								</div>
								<div>
									<b>Description:</b>
									<?php echo wordwrap($medArr['description'],60,'<br />\n',true); ?>
								</div>
								<div>
									<b>Locality:</b>
									<?php echo wordwrap($medArr['locationcreated'],60,'<br />\n',true); ?>
								</div>
                                <div>
                                    <b>Language:</b>
                                    <?php echo wordwrap($medArr['language'],60,'<br />\n',true); ?>
                                </div>
                                <div>
                                    <b>Usage Terms:</b>
                                    <?php echo wordwrap($medArr['usageterms'],60,'<br />\n',true); ?>
                                </div>
                                <div>
                                    <b>Rights:</b>
                                    <?php echo wordwrap($medArr['rights'],60,'<br />\n',true); ?>
                                </div>
                                <div>
                                    <b>Owner:</b>
                                    <?php echo wordwrap($medArr['owner'],60,'<br />\n',true); ?>
                                </div>
                                <div>
                                    <b>Publisher:</b>
                                    <?php echo wordwrap($medArr['publisher'],60,'<br />\n',true); ?>
                                </div>
                                <div>
                                    <b>Contributor:</b>
                                    <?php echo wordwrap($medArr['contributor'],60,'<br />\n',true); ?>
                                </div>
                                <div>
                                    <b>Bibliographic Citation:</b>
                                    <?php echo wordwrap($medArr['bibliographiccitation'],60,'<br />\n',true); ?>
                                </div>
                                <div>
									<b>Further Information URL:</b>
									<a href="<?php echo $medArr['furtherinformationurl']; ?>" target="_blank">
										<?php 
										$furtherInfoUrlDisplay = $medArr['furtherinformationurl'];
										if(strlen($furtherInfoUrlDisplay) > 60) {
                                            $furtherInfoUrlDisplay = '...' . substr($furtherInfoUrlDisplay, -60);
                                        }
										echo $furtherInfoUrlDisplay;
										?>
									</a>
								</div>
								<div>
									<b>Sort Sequence:</b>
									<?php echo $medArr['sortsequence']; ?>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div id="med<?php echo $medId; ?>editdiv" style="display:none;clear:both;">
								<form name="med<?php echo $medId; ?>editform" action="occurrenceeditor.php" method="post" onsubmit="return verifyMedEditForm(this);">
									<fieldset style="padding:15px">
										<legend><b>Edit Media Data</b></legend>
										<div>
											<b>Title:</b><br/>
											<textarea name="title" rows="2" style="width:300px;resize:vertical;"><?php echo $medArr['title']; ?></textarea>
										</div>
										<div>
											<b>Creator:</b><br/> 
											<select name='creatoruid'>
												<option value="">Select Creator</option>
												<option value="">---------------------------------------</option>
												<?php
												$pArr = $occManager->getPhotographerArr();
												foreach($pArr as $id => $uname){
													echo "<option value='".$id."' ".($id === $medArr['creatoruid']? 'SELECTED' : ''). '>';
													echo $uname;
													echo "</option>\n";
												}
												?>
											</select>
											<a href="#" onclick="toggle('mededitoverride<?php echo $medId; ?>');return false;" title="Display creator override field">
												<i style="height:15px;width:15px;" class="far fa-plus-square"></i>
											</a>
										</div>
										<div id="mededitoverride<?php echo $medId; ?>" style="display:<?php echo ($medArr['photographer']?'block':'none'); ?>;">
											<b>Creator (override):</b><br/>
											<input name='creator' type='text' value="<?php echo $medArr['creator']; ?>" style="width:300px;" maxlength='100'>
											*Warning: value will override above selection
										</div>
										<div>
											<b>Description:</b><br/>
											<textarea name="description" rows="2" style="width:300px;resize:vertical;"><?php echo $medArr['description']; ?></textarea>
										</div>
										<div>
											<b>Locality:</b><br/>
											<textarea name="locationcreated" rows="2" style="width:300px;resize:vertical;"><?php echo $medArr['locationcreated']; ?></textarea>
										</div>
										<div>
											<b>Language:</b><br/>
											<input name="language" type="text" value="<?php echo $medArr['language']; ?>" style="width:95%;" />
										</div>
                                        <div>
											<b>URL: </b><br/>
											<input name="accessuri" type="text" value="<?php echo $medArr['accessuri']; ?>" onchange="processAccessURIChange(this.form);" style="width:95%;" />
										</div>
                                        <div>
                                            <b>Usage Terms:</b><br/>
                                            <textarea name="usageterms" rows="2" style="width:300px;resize:vertical;"><?php echo $medArr['usageterms']; ?></textarea>
                                        </div>
                                        <div>
                                            <b>Rights:</b><br/>
                                            <textarea name="rights" rows="2" style="width:300px;resize:vertical;"><?php echo $medArr['rights']; ?></textarea>
                                        </div>
                                        <div>
                                            <b>Owner:</b><br/>
                                            <input name="owner" type="text" value="<?php echo $medArr['owner']; ?>" style="width:95%;" />
                                        </div>
                                        <div>
                                            <b>Publisher:</b><br/>
                                            <input name="publisher" type="text" value="<?php echo $medArr['publisher']; ?>" style="width:95%;" />
                                        </div>
                                        <div>
                                            <b>Contributor:</b><br/>
                                            <input name="contributor" type="text" value="<?php echo $medArr['contributor']; ?>" style="width:95%;" />
                                        </div>
                                        <div>
                                            <b>Bibliographic Citation:</b><br/>
                                            <textarea name="bibliographiccitation" rows="2" style="width:300px;resize:vertical;"><?php echo $medArr['bibliographiccitation']; ?></textarea>
                                        </div>
                                        <div>
                                            <b>Further Information URL:</b><br/>
                                            <textarea name="furtherinformationurl" rows="2" style="width:300px;resize:vertical;"><?php echo $medArr['furtherinformationurl']; ?></textarea>
                                        </div>
										<div>
											<b>Sort Sequence:</b><br/>
											<input name="sortsequence" type="text" value="<?php echo $medArr['sortsequence']; ?>" style="width:10%;" />
										</div>
					                    <div style="margin-top:10px;">
											<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
											<input type="hidden" name="medid" value="<?php echo $medId; ?>" />
											<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
											<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
                                            <input type="hidden" name="type" value="<?php echo $medArr['type']; ?>" />
                                            <input type="hidden" name="format" value="<?php echo $medArr['format']; ?>" />
											<input type="submit" name="submitaction" value="Submit Media Edits" />
										</div>
									</fieldset>
								</form>
								<form name="med<?php echo $medId; ?>delform" action="occurrenceeditor.php" method="post" onsubmit="return verifyMedDelForm();">
									<fieldset style="padding:15px">
										<legend><b>Delete Media</b></legend>
										<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
										<input type="hidden" name="medid" value="<?php echo $medId; ?>" />
										<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
										<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
										<input name="removemed" type="checkbox" value="1" /> Remove media file from server
										<div style="margin-left:20px;">
											(Note: leaving unchecked removes media from database without removing file from server)
										</div>
										<div style="margin:10px 20px;">
											<input type="submit" name="submitaction" value="Delete Media" />
										</div>
									</fieldset>
								</form>
								<form name="med<?php echo $medId; ?>remapform" action="occurrenceeditor.php" method="post" onsubmit="return verifyImgRemapForm(this);">
									<fieldset style="padding:15px">
										<legend><b>Remap to Another Occurrence Record</b></legend>
										<div>
											<b>Occurrence Record #:</b> 
											<input id="imgoccid-<?php echo $medId; ?>" name="targetoccid" type="text" value="" />
											<span style="cursor:pointer;color:blue;"  onclick="openOccurrenceSearch('imgoccid-<?php echo $medId; ?>')">
												Open Occurrence Linking Aid
											</span>
										</div>
										<div style="margin:10px 20px;">
											<input name="occid" type="hidden" value="<?php echo $occId; ?>" />
											<input type="hidden" name="medid" value="<?php echo $medId; ?>" />
											<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
											<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
											<input type="submit" name="submitaction" value="Remap Media" />
										</div>
									</fieldset>
								</form>
								<form action="occurrenceeditor.php" method="post">
									<fieldset style="padding:15px">
										<legend><b>Disassociate Media from all Occurrence Records</b></legend>
										<div style="margin:10px 20px;">
											<input name="occid" type="hidden" value="<?php echo $occId; ?>" />
											<input name="medid" type="hidden" value="<?php echo $medId; ?>" />
											<input name="occindex" type="hidden" value="<?php echo $occIndex; ?>" />
											<input name="csmode" type="hidden" value="<?php echo $crowdSourceMode; ?>" />
											<input name="submitaction" type="submit" value="Disassociate Media" />
										</div>
										<div>
											*Media will only be available from Taxon Profile page
										</div>
									</fieldset>
								</form>
							</div>
							<hr/>
						</td>
					</tr>
					<?php 
				}
				?>
			</table>
			<?php 
		}
		?>
	</div>
</div>
