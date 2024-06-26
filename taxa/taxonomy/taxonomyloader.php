<?php
/** @var int $isEditor */
/** @var string $status */
/** @var array $tRankArr */
?>
<script src="../../js/taxa.taxonomyloader.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>"></script>
<div>
    <?php
    if($status){
        echo "<div style='color:red;'>".$status. '</div>';
    }
    ?>
    <form id="loaderform" name="loaderform" action="index.php" method="post">
        <fieldset>
            <legend><b>Add New Taxon</b></legend>
            <div>
                <div style="float:left;width:170px;">Taxon Name:</div>
                <input type="text" id="sciname" name="sciname" style="width:300px;border:inset;" value="" onchange="parseName(this.form);clearValidations();"/>
            </div>
            <div>
                <div style="float:left;width:170px;">Author:</div>
                <input type='text' id='author' name='author' style='width:300px;border:inset;' />
            </div>
            <div style="clear:both;">
                <div style="float:left;width:170px;">Taxon Rank:</div>
                <select id="rankid" name="rankid" title="Rank ID" style="border:inset;" onchange="clearValidations();">
                    <option>Select Taxon Rank</option>
                    <option value="0">Non-Ranked Node</option>
                    <option>--------------------------------</option>
                    <?php
                    foreach($tRankArr as $rankId => $rankName){
                        echo "<option value='".$rankId."' ".((int)$rankId === 220? ' SELECTED' : ''). '>' .$rankName."</option>\n";
                    }
                    ?>
                </select>
            </div>
            <div style="clear:both;">
                <div style="float:left;width:170px;">Unit Name 1:</div>
                <input type='text' id='unitind1' name='unitind1' style='width:20px;border:inset;' title='Genus hybrid indicator'/>
                <input type='text' id='unitname1' name='unitname1' style='width:200px;border:inset;' title='Genus or Base Name' onchange="clearValidations();"/>
            </div>
            <div style="clear:both;">
                <div style="float:left;width:170px;">Unit Name 2:</div>
                <input type='text' id='unitind2' name='unitind2' style='width:20px;border:inset;' title='Species hybrid indicator'/>
                <input type='text' id='unitname2' name='unitname2' style='width:200px;border:inset;' title='epithet'/>
            </div>
            <div style="clear:both;">
                <div style="float:left;width:170px;">Unit Name 3:</div>
                <input type='text' id='unitind3' name='unitind3' style='width:50px;border:inset;' title='Rank: e.g. subsp., var., f.'/>
                <input type='text' id='unitname3' name='unitname3' style='width:200px;border:inset;' title='infrasp. epithet'/>
            </div>
            <div style="clear:both;">
                <div style="float:left;width:170px;">Parent Taxon:</div>
                <input type="text" id="parentname" name="parentname" style="width:300px;border:inset;" onchange="clearValidations();" />
                <span id="addparentspan" style="display:none;">
                    <a id="addparentanchor" href="index.php?target=" target="_blank">Add Parent</a>
                </span>
                <input type="hidden" id="parenttid" name="parenttid" value="" />
            </div>
            <div style="clear:both;">
                <div style="float:left;width:170px;">Notes:</div>
                <input type='text' id='notes' name='notes' style='width:400px;border:inset;' title=''/>
            </div>
            <div style="clear:both;">
                <div style="float:left;width:170px;">Source:</div>
                <input type='text' id='source' name='source' style='width:400px;border:inset;' title=''/>
            </div>
            <div style="clear:both;">
                <div style="float:left;width:170px;">Locality Security Status:</div>
                <select id="securitystatus" name="securitystatus" style='border:inset;'>
                    <option value="0">No Security</option>
                    <option value="1">Hide Locality Details</option>
                </select>
            </div>
            <div style="clear:both;">
                <fieldset>
                    <legend><b>Acceptance Status</b></legend>
                    <div>
                        <input type="radio" id="isaccepted" name="acceptstatus" value="1" onchange="acceptanceChanged(this.form);clearValidations();" checked> Accepted
                        <input type="radio" id="isnotaccepted" name="acceptstatus" value="0" onchange="acceptanceChanged(this.form);clearValidations();"> Not Accepted
                    </div>
                    <div id="accdiv" style="display:none;margin-top:3px;">
                        Accepted Taxon:
                        <input id="acceptedstr" name="acceptedstr" type="text" style="width:400px;border:inset;" onchange="clearValidations();" />
                        <input type="hidden" name="tidaccepted" id="tidaccepted" />
                        <div style="margin-top:3px;">
                            Unacceptability Reason:
                            <input type='text' id='unacceptabilityreason' name='unacceptabilityreason' style='width:350px;border:inset;' />
                        </div>
                    </div>
                </fieldset>
            </div>
            <div style="clear:both;margin:10px;width:100%;">
                <div style="float:left;">
                    <input type="button" value="Validate New Taxon" onclick="validateLoadForm(this.form);" />
                </div>
                <div style="margin-left:10px;float:left;">
                    <input type="submit" id="submitButton" name="submitaction" value="Submit New Name" disabled />
                </div>
            </div>
        </fieldset>
    </form>
</div>

