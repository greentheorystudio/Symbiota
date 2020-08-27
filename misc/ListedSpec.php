<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $CHARSET);

$IRLManager = new IRLManager();

$plantsArr = $IRLManager->getChecklistTaxa(8);
$fishesArr = $IRLManager->getChecklistTaxa(9);
$reptilesArr = $IRLManager->getChecklistTaxa(10);
$birdsArr = $IRLManager->getChecklistTaxa(11);
$mammalsArr = $IRLManager->getChecklistTaxa(12);
$vernacularArr = $IRLManager->getChecklistVernaculars();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Special Status Species of the Indian River Lagoon</title>
    <link href="<?php echo $CLIENT_ROOT; ?>/css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $CLIENT_ROOT; ?>/css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $CLIENT_ROOT; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <h2>Special Status Species of the Indian River Lagoon</h2>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="6" class="table-border">
        <tr>
            <td colspan="2">
                <p class="heading">Key to Listing Status</p></td>
        </tr>
        <tr>
            <td align="center">E</td>
            <td><span>Endangered: According to the Endangered Species Act of 1973, any species which is in danger of extinction throughout all or a significant portion of its range.</span>
            </td>
        </tr>
        <tr>
            <td align="center">T</td>
            <td><span>Threatened: According to the Endangered Species Act of 1973, any species which is likely to become an endangered species within the foreseeable future throughout all or a significant portion of its range.</span>
            </td>
        </tr>
        <tr>
            <td align="center">T1</td>
            <td><span>Threatened due to similarity of appearance</span></td>
        </tr>
        <tr>
            <td align="center">CE</td>
            <td><span>Commercially Exploited</span></td>
        </tr>
        <tr>
            <td align="center">SSC</td>
            <td><span>Florida State Species of Special Concern</span></td>
        </tr>
    </table>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body"><em>Note:</em> These species are listed by the Florida Fish &amp; Wildlife Conservation
                    Commission under one or more of the following criteria:</p>
                <p class="body"><strong>1</strong>: Species has a significant vulnerability to habitat modification,
                    environmental alteration, human disturbance, or human exploitation which, in the foreseeable future,
                    may result in its becoming a threatened species unless appropriate protective or management
                    techniques are initiated or maintained.</p>

                <p class="body"><strong>2</strong>: Species may already meet certain criteria for designation as a
                    threatened species, but for which conclusive data are limited or lacking.</p>
                <p class="body"><strong>3</strong>: Species may occupy such an unusually vital or essential ecological
                    niche that, should it decline significantly in numbers or distribution, other species would be
                    adversely affected to a significant degree.</p>
                <p class="body"><strong>4</strong>: Species has not sufficiently recovered from past population
                    depletion.</p>
                <p class="body"><strong>5</strong>: Species occurs as a population either intentionally introduced or
                    being experimentally managed to attain specific objectives, and the species of special concern
                    prohibitions in Rule 68A-27.002, F.A.C., shall not apply to species so designated, provided that the
                    intentional killing, attempting to kill, posession or sale of such species is prohibited.</p></td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="4"
           class="table-border no-border alternate">
        <tr>
            <th>Species Name</th>
            <th>Common Name</th>
            <th>FL State & US Federal Status</th>
        </tr>
        <?php
        if($plantsArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Plants</p></td>
            </tr>
            <?php
            foreach($plantsArr as $id => $taxArr){
                echo '<tr>';
                echo '<td><span><i><a href="../taxa/index.php?taxon='.$id.'">'.$taxArr['sciname'].'</a></i></span></td>';
                if(array_key_exists($id,$vernacularArr)){
                    $vernacularStr = implode(', ', $vernacularArr[$id]);
                    echo '<td><span>'.wordwrap($vernacularStr,20,"<br />\n",true).'</span></td>'."\n";
                }
                else{
                    echo '<td><span></span></td>'."\n";
                }
                echo '<td><span>'.$taxArr['notes'].'</span></td>';
                echo '</tr>';
            }
        }
        if($fishesArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Fishes</p></td>
            </tr>
            <?php
            foreach($fishesArr as $id => $taxArr){
                echo '<tr>';
                echo '<td><span><i><a href="../taxa/index.php?taxon='.$id.'">'.$taxArr['sciname'].'</a></i></span></td>';
                if(array_key_exists($id,$vernacularArr)){
                    $vernacularStr = implode(', ', $vernacularArr[$id]);
                    echo '<td><span>'.wordwrap($vernacularStr,20,"<br />\n",true).'</span></td>'."\n";
                }
                else{
                    echo '<td><span></span></td>'."\n";
                }
                echo '<td><span>'.$taxArr['notes'].'</span></td>';
                echo '</tr>';
            }
        }
        if($reptilesArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Reptiles & Amphibians</p></td>
            </tr>
            <?php
            foreach($reptilesArr as $id => $taxArr){
                echo '<tr>';
                echo '<td><span><i><a href="../taxa/index.php?taxon='.$id.'">'.$taxArr['sciname'].'</a></i></span></td>';
                if(array_key_exists($id,$vernacularArr)){
                    $vernacularStr = implode(', ', $vernacularArr[$id]);
                    echo '<td><span>'.wordwrap($vernacularStr,20,"<br />\n",true).'</span></td>'."\n";
                }
                else{
                    echo '<td><span></span></td>'."\n";
                }
                echo '<td><span>'.$taxArr['notes'].'</span></td>';
                echo '</tr>';
            }
        }
        if($birdsArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Birds</p></td>
            </tr>
            <?php
            foreach($birdsArr as $id => $taxArr){
                echo '<tr>';
                echo '<td><span><i><a href="../taxa/index.php?taxon='.$id.'">'.$taxArr['sciname'].'</a></i></span></td>';
                if(array_key_exists($id,$vernacularArr)){
                    $vernacularStr = implode(', ', $vernacularArr[$id]);
                    echo '<td><span>'.wordwrap($vernacularStr,20,"<br />\n",true).'</span></td>'."\n";
                }
                else{
                    echo '<td><span></span></td>'."\n";
                }
                echo '<td><span>'.$taxArr['notes'].'</span></td>';
                echo '</tr>';
            }
        }
        if($mammalsArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Mammals</p></td>
            </tr>
            <?php
            foreach($mammalsArr as $id => $taxArr){
                echo '<tr>';
                echo '<td><span><i><a href="../taxa/index.php?taxon='.$id.'">'.$taxArr['sciname'].'</a></i></span></td>';
                if(array_key_exists($id,$vernacularArr)){
                    $vernacularStr = implode(', ', $vernacularArr[$id]);
                    echo '<td><span>'.wordwrap($vernacularStr,20,"<br />\n",true).'</span></td>'."\n";
                }
                else{
                    echo '<td><span></span></td>'."\n";
                }
                echo '<td><span>'.$taxArr['notes'].'</span></td>';
                echo '</tr>';
            }
        }
        ?>
    </table>

    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="title">References</p>
                <p class="body"><a href="http://www.fao.org/fishery/topic/16310/en">CITES</a>. Convention on
                    International Trade in Endangered Species of Wild Fauna and Flora. Food &amp; Agriculture
                    Organization of the United Nations.</p>
                <p class="body"><a href="http://www.epa.gov/lawsregs/laws/esa.html">Endangered Species Act of 1973</a>.
                    Public Law 93-205. United States Environmental Protection Agency.</p>
                <p class="body"><a
                            href="http://ecos.fws.gov/tess_public/pub/stateListingAndOccurrenceIndividual.jsp?state=FL">Endangered
                        &amp; Threatened Species in Florida</a>. U.S. Fish &amp; Wildlife Species Reports. Environmental
                    Conservation Online System.</p>
                <p class="body"><a href="https://www.flrules.org/gateway/ruleno.asp?id=5B-40.0055">Florida Regulated
                        Plant Index</a>. 5B-40.0055. Department of Agriculture &amp; Consumer Services. Division of
                    Plant Industry.</p>
                <p class="body"><a href="http://myfwc.com/wildlifehabitats/imperiled/">Florida's Endangered &amp;
                        Threatened Species List</a>. May 2011. Florida Fish &amp; Wildlife Conservation Commission.</p>
                <p class="body"><a href="http://www.fl-dof.com/forest_management/plant_conserve_list.html">Florida's
                        Federally Listed Plant Species</a>. Florida Department of Agriculture &amp; Consumer Services.
                    Florida Forest Service.</p></td>
        </tr>
    </table>
    <table style="width:700px;margin-left:auto;margin-right:auto;" cellpadding="4">
        <tr>
            <td><p class="footer_note">Compiled and edited by LH Sweat <br/>
                    For questions, comments or contributions, <br/>
                    please contact us at:<br>
                    <a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu</a></p></td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
