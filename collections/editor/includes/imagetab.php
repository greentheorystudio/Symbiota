<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/OccurrenceEditorManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$occId = (int)$_GET['occid'];
$occIndex = (int)$_GET['occindex'];
$crowdSourceMode = (int)$_GET['csmode'];

$occManager = new OccurrenceEditorImages();

$occManager->setOccId($occId); 
$specImgArr = $occManager->getImageMap();
?>
<div id="imagediv" style="width:795px;">
	<div style="float:right;cursor:pointer;" onclick="toggle('addimgdiv');" title="Add a New Image">
		<i style="height:20px;width:20px;color:green;" class="fas fa-plus"></i>
	</div>
	<div id="addimgdiv" style="display:<?php echo ($specImgArr?'none':''); ?>;">
		<form name="imgnewform" action="occurrenceeditor.php" method="post" enctype="multipart/form-data" onsubmit="return verifyImgAddForm(this);">
			<fieldset style="padding:15px">
				<legend><b>Add a New Image</b></legend>
				<div style='padding:15px;width:90%;border:1px solid yellow;background-color:#FFFF99;'>
					<div class="targetdiv" style="display:block;">
						<div style="font-weight:bold;margin-bottom:5px;">
							Select an image file located on your computer that you want to upload:
						</div>
				    	<div>
							<input name='imgfile' type='file' size='70'/>
						</div>
						<div style="float:right;text-decoration:underline;font-weight:bold;">
							<a href="#" onclick="toggle('targetdiv');return false;">Enter URL</a>
						</div>
					</div>
					<div class="targetdiv" style="display:none;">
						<div style="margin-bottom:10px;">
							Enter a URL to an image already located on a web server. 
							If the image is larger than a typical web image, the url will be saved as the large version 
							and a basic web derivative will be created. 
						</div>
						<div>
							<b>Image URL:</b><br/> 
							<input type='text' name='imgurl' size='70'/>
						</div>
						<div style="float:right;text-decoration:underline;font-weight:bold;">
							<a href="#" onclick="toggle('targetdiv');return false;">
								Upload Local Image
							</a>
						</div>
						<div>
							<input type="checkbox" name="copytoserver" value="1" /> Copy large image to Server (if left unchecked, source URL will serve as large version)
						</div>
					</div>
					<div>
						<input type="checkbox" name="nolgimage" value="1" /> Do not map large version of image (when applicable) 
					</div>
				</div>
				<div style="clear:both;margin:20px 0 5px 10px;">
					<b>Caption:</b> 
					<textarea name="caption" rows="2" style="width:300px;resize:vertical;"></textarea>
				</div>
				<div style='margin:0 0 5px 10px;'>
					<b>Photographer:</b> 
					<select name='photographeruid' name='photographeruid'>
						<option value="">Select Photographer</option>
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
					<a href="#" onclick="toggle('imgaddoverride');return false;" title="Display photographer override field">
						<i style="height:15px;width:15px;" class="far fa-plus-square"></i>
					</a>
				</div>
				<div id="imgaddoverride" style="margin:0 0 5px 10px;display:none;">
					<b>Photographer (override):</b> 
					<input name='photographer' type='text' style="width:300px;" maxlength='100'>
					*Will override above selection
				</div>
				<div style="margin:0 0 5px 10px;">
					<b>Notes:</b> 
					<input name="notes" type="text" size="40" value="" />
				</div>
				<div style="margin:0 0 5px 10px;">
					<b>Copyright:</b>
					<input name="copyright" type="text" size="40" value="" />
				</div>
				<div style="margin:0 0 5px 10px;">
					<b>Source Webpage:</b>
					<input name="sourceurl" type="text" size="40" value="" />
				</div>
				<div style="margin:0 0 5px 10px;">
					<b>Sort Sequence:</b>
					<input name="sortsequence" type="text" size="10" value="" />
				</div>
				<div style="margin:0 0 5px 10px;">
					<b>Describe this image</b>
				</div>
                    <?php 
                       $kArr = $occManager->getImageTagValues();
                       foreach($kArr as $key => $description) { 
				          echo "<div style='margin-left:10px;'>\n";
					      echo "   <input name='ch_$key' type='checkbox' value='0' />$description</br>\n";
                          echo "</div>\n";
                       }
                    ?>
				<div style="margin:10px 0 10px 20px;">
					<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
					<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
					<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
					<input type="hidden" name="tabindex" value="1" />
					<input type="submit" name="submitaction" value="Submit New Image" />
				</div>
			</fieldset>
		</form>
		<hr style="margin:30px 0;" />
	</div>
	<div style="clear:both;margin:15px;">
		<?php
		if($specImgArr){
			?>
			<table>
				<?php 
				foreach($specImgArr as $imgId => $imgArr){
					?>
					<tr>
						<td style="width:300px;text-align:center;padding:20px;">
							<?php
							$imgUrl = $imgArr['url'];
							$origUrl = $imgArr['origurl'];
							$tnUrl = $imgArr['tnurl'];
							if($imgUrl === 'empty' && $origUrl) {
                                $imgUrl = $origUrl;
                            }
							if(!$tnUrl && $imgUrl) {
                                $tnUrl = $imgUrl;
                            }
							echo '<a href="'.$imgUrl.'" target="_blank">';
							if(array_key_exists('error', $imgArr)){
								echo '<div style="font-weight:bold;">'.$imgArr['error'].'</div>';
							}
							else{
								echo '<img src="'.$imgUrl.'" style="width:250px;" title="'.$imgArr['caption'].'" />';
							}
							echo '</a>';
							if($imgUrl !== $origUrl) {
                                echo '<div><a href="' . $imgUrl . '" target="_blank">Open Medium Image</a></div>';
                            }
							if($origUrl) {
                                echo '<div><a href="' . $origUrl . '" target="_blank">Open Large Image</a></div>';
                            }
							?>
						</td>
						<td style="text-align:left;padding:10px;">
							<div style="float:right;cursor:pointer;" onclick="toggle('img<?php echo $imgId; ?>editdiv');" title="Edit Image MetaData">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i>
							</div>
							<div style="margin-top:30px">
								<div>
									<b>Caption:</b> 
									<?php echo $imgArr['caption']; ?>
								</div>
								<div>
									<b>Photographer:</b> 
									<?php
									if($imgArr['photographer']){
										echo $imgArr['photographer'];
									}
									else if($imgArr['photographeruid']){
										$pArr = $occManager->getPhotographerArr();
										echo $pArr[$imgArr['photographeruid']];
									} 
									?>
								</div>
								<div>
									<b>Notes:</b> 
									<?php echo $imgArr['notes']; ?>
								</div>
								<div>
									<b>Tags:</b> 
                                    <?php
                                    $kArr = $occManager->getImageTagUsage($imgId);
                                    $tempArr = array();
                                    foreach($kArr as $tags => $tagArr) {
                                        if($tagArr['value']  === 1) {
                                            $tempArr[] = $tagArr['shortlabel'];
                                        }
                                    }
                                    echo implode(', ', $tempArr)
                                    ?>
								</div>
								<div>
									<b>Copyright:</b>
									<?php echo $imgArr['copyright']; ?>
								</div>
								<div>
									<b>Source Webpage:</b>
									<a href="<?php echo $imgArr['sourceurl']; ?>" target="_blank">
										<?php 
										echo $imgArr['sourceurl'];
										?>
									</a>
								</div>
								<div>
									<b>Web URL: </b>
									<a href="<?php echo $imgArr['url']; ?>" title="<?php echo $imgArr['url']; ?>" target="_blank">
										<?php 
										echo $imgArr['url'];
										?>
									</a>
								</div>
								<div>
									<b>Large Image URL: </b>
									<a href="<?php echo $imgArr['origurl']; ?>" title="<?php echo $imgArr['origurl']; ?>" target="_blank">
										<?php 
										echo $imgArr['origurl'];
										?>
									</a>
								</div>
								<div>
									<b>Thumbnail URL: </b>
									<a href="<?php echo $imgArr['tnurl']; ?>" title="<?php echo $imgArr['tnurl']; ?>" target="_blank">
										<?php 
										echo $imgArr['tnurl'];
										?>
									</a>
								</div>
								<div>
									<b>Sort Sequence:</b>
									<?php echo $imgArr['sortseq']; ?>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div id="img<?php echo $imgId; ?>editdiv" style="display:none;clear:both;">
								<form name="img<?php echo $imgId; ?>editform" action="occurrenceeditor.php" method="post" onsubmit="return verifyImgEditForm(this);">
									<fieldset style="padding:15px">
										<legend><b>Edit Image Data</b></legend>
										<div>
											<b>Caption:</b><br/> 
											<textarea name="caption" rows="2" style="width:300px;resize:vertical;"><?php echo $imgArr['caption']; ?></textarea>
										</div>
										<div>
											<b>Photographer:</b><br/> 
											<select name='photographeruid' name='photographeruid'>
												<option value="">Select Photographer</option>
												<option value="">---------------------------------------</option>
												<?php
												$pArr = $occManager->getPhotographerArr();
												foreach($pArr as $id => $uname){
													echo "<option value='".$id."' ".($id === $imgArr['photographeruid']? 'SELECTED' : ''). '>';
													echo $uname;
													echo "</option>\n";
												}
												?>
											</select>
											<a href="#" onclick="toggle('imgeditoverride<?php echo $imgId; ?>');return false;" title="Display photographer override field">
												<i style="height:15px;width:15px;" class="far fa-plus-square"></i>
											</a>
										</div>
										<div id="imgeditoverride<?php echo $imgId; ?>" style="display:<?php echo ($imgArr['photographer']?'block':'none'); ?>;">
											<b>Photographer (override):</b><br/> 
											<input name='photographer' type='text' value="<?php echo $imgArr['photographer']; ?>" style="width:300px;" maxlength='100'>
											* Warning: value will override above selection
										</div>
										<div>
											<b>Notes:</b><br/>
											<input name="notes" type="text" value="<?php echo $imgArr['notes']; ?>" style="width:95%;" />
										</div>
										<div>
											<b>Copyright:</b><br/>
											<input name="copyright" type="text" value="<?php echo $imgArr['copyright']; ?>" style="width:95%;" />
										</div>
										<div>
											<b>Source Webpage:</b><br/>
											<input name="sourceurl" type="text" value="<?php echo $imgArr['sourceurl']; ?>" style="width:95%;" />
										</div>
										<div>
											<b>Web URL: </b><br/>
											<input name="url" type="text" value="<?php echo $imgArr['url']; ?>" style="width:95%;" />
											<?php if(stripos($imgArr['url'],$GLOBALS['IMAGE_ROOT_URL']) === 0){ ?>
												<div style="margin-left:10px;">
													<input type="checkbox" name="renameweburl" value="1" />
													Rename web image file on server to match above edit
												</div>
												<input name='oldurl' type='hidden' value='<?php echo $imgArr['url'];?>' />
											<?php } ?>
										</div>
										<div>
											<b>Large Image URL: </b><br/>
											<input name="origurl" type="text" value="<?php echo $imgArr['origurl']; ?>" style="width:95%;" />
											<?php if(stripos($imgArr['origurl'],$GLOBALS['IMAGE_ROOT_URL']) === 0){ ?>
												<div style="margin-left:10px;">
													<input type="checkbox" name="renameorigurl" value="1" />
													Rename large image file on server to match above edit
												</div>
												<input name='oldorigurl' type='hidden' value='<?php echo $imgArr['origurl'];?>' />
											<?php } ?>
										</div>
										<div>
											<b>Thumbnail URL: </b><br/>
											<input name="tnurl" type="text" value="<?php echo $imgArr['tnurl']; ?>" style="width:95%;" />
											<?php if(stripos($imgArr['tnurl'],$GLOBALS['IMAGE_ROOT_URL']) === 0){ ?>
												<div style="margin-left:10px;">
													<input type="checkbox" name="renametnurl" value="1" />
													Rename thumbnail file on server to match above edit
												</div>
												<input name='oldtnurl' type='hidden' value='<?php echo $imgArr['tnurl'];?>' />
											<?php } ?>
										</div>
										<div>
											<b>Sort Sequence:</b><br/>
											<input name="sortsequence" type="text" value="<?php echo $imgArr['sortseq']; ?>" style="width:10%;" />
										</div>
					                    <div>
						                   <b>Tags:</b>
					                    </div>
	                                        <?php 
                                            $kArr = $occManager->getImageTagUsage($imgId);
                                            foreach($kArr as $tags => $tagArr) {
                                                echo "<div style='margin-left:10px;'>\n";
                                                if($tagArr['value'] === 1) {
                                                    $checked = 'CHECKED';
                                                }
                                                else {
                                                    $checked='';
                                                }
                                                echo "   <input name='ch_".$tagArr['tagkey']."' type='checkbox' $checked value='".$tagArr['value']."' />".$tagArr['description']."\n";
                                                echo "   <input name='hidden_".$tagArr['tagkey']."' type='hidden' value='".$tagArr['value']."' />\n";
                                                echo "</div>\n";
                                            }
                                            ?>
										<div style="margin-top:10px;">
											<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
											<input type="hidden" name="imgid" value="<?php echo $imgId; ?>" />
											<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
											<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
											<input type="submit" name="submitaction" value="Submit Image Edits" />
										</div>
									</fieldset>
								</form>
								<form name="img<?php echo $imgId; ?>delform" action="occurrenceeditor.php" method="post" onsubmit="return verifyImgDelForm();">
									<fieldset style="padding:15px">
										<legend><b>Delete Image</b></legend>
										<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
										<input type="hidden" name="imgid" value="<?php echo $imgId; ?>" />
										<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
										<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
										<input name="removeimg" type="checkbox" value="1" /> Remove image from server 
										<div style="margin-left:20px;">
											(Note: leaving unchecked removes image from database without removing from server)
										</div>
										<div style="margin:10px 20px;">
											<input type="submit" name="submitaction" value="Delete Image" />
										</div>
									</fieldset>
								</form>
								<form name="img<?php echo $imgId; ?>remapform" action="occurrenceeditor.php" method="post" onsubmit="return verifyImgRemapForm(this);">
									<fieldset style="padding:15px">
										<legend><b>Remap to Another Occurrence Record</b></legend>
										<div>
											<b>Occurrence Record #:</b> 
											<input id="imgoccid-<?php echo $imgId; ?>" name="targetoccid" type="text" value="" />
											<span style="cursor:pointer;color:blue;"  onclick="openOccurrenceSearch('imgoccid-<?php echo $imgId; ?>')">
												Open Occurrence Linking Aid
											</span>
										</div>
										<div style="margin:10px 20px;">
											<input name="occid" type="hidden" value="<?php echo $occId; ?>" />
											<input type="hidden" name="imgid" value="<?php echo $imgId; ?>" />
											<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
											<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
											<input type="submit" name="submitaction" value="Remap Image" />
										</div>
									</fieldset>
								</form>
								<form action="occurrenceeditor.php" method="post">
									<fieldset style="padding:15px">
										<legend><b>Disassociate Image from all Occurrence Records</b></legend>
										<div style="margin:10px 20px;">
											<input name="occid" type="hidden" value="<?php echo $occId; ?>" />
											<input name="imgid" type="hidden" value="<?php echo $imgId; ?>" />
											<input name="occindex" type="hidden" value="<?php echo $occIndex; ?>" />
											<input name="csmode" type="hidden" value="<?php echo $crowdSourceMode; ?>" />
											<input name="submitaction" type="submit" value="Disassociate Image" />
										</div>
										<div>
											* Image will only be available from Taxon Profile page 
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
