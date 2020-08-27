<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Algae vs Seagrass</title>
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
    <table cellpadding="0" cellspacing="0"
           style="border-collapse:collapse;border:0;border-color:#111111;width:550px;margin-left:auto;margin-right:auto;">
        <tr>
            <td height="44">
                <p align="left"><font color="#036" size="5" style="text-align:center"><b>Algae vs
                            Seagrass</b></font><br/>
                    <br/>
                    <span class="medium"><a href="Seagrass_Habitat.php">Back to Seagrass Habitats</a></span>
                </p>
            </td>
        </tr>
    </table>
    <div class="clear"></div>
    <table width="465" align="center">
        <tr>
            <td><img border="0" src="../content/imglib/Algae_SeaGrass1.jpg" width="465" height="244">&nbsp;</td>
        </tr>
    </table>
    <table width="552" align="center">
        <tr>
            <td><span class="medium"><b><font
                                color="#000080">A comparison of algal morphology with seagrass morphology:</font>&nbsp;</b>&nbsp;
      <font color="#000080">Note that algae are relatively simple and unspecialized in
structure, possessing only a tough holdfast that assists in anchoring it to a
hard substratum. Seagrasses are more closely
related to terrestrial plants and, like terrestrial plants, possess specialized
tissues that perform specific tasks within each plant. </font>Se<font color="#000080">agrasses
possess true roots that not only hold plants in place, but also are specialized
for extracting minerals and other nutrients from the sediment. All algal cells
possess photosynthetic structures capable of utilizing sunlight to produce
chemical energy. In seagrasses, however, chloroplasts occur only in leaves, thus
confining photosynthesis to leaves. Further, algae are able to take up minerals
and other nutrients directly from the water column via diffusion. Finally, while
most algae lack specialized reproductive structures, most seagrasses have
separate sexes and produce flowers and seeds, with embryos developing inside
ovaries.&nbsp;&nbsp;<br>
</font></span><font color="#000080"><br>
                    <br>
                    <br>
                </font>
            </td>
        </tr>
        <tr>
            <td width="552">
                <p align="center"><font size="2" color="#000080">Report by: K. Hill,
                        Smithsonian Marine Station<br>
                        Submit additional information, photos or comments to:<br>
                        <a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu</a>
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
