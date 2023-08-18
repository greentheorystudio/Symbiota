<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Acknowledgements</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <h2>Acknowledgements</h2>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">The Smithsonian Marine Station at Fort Pierce sincerely acknowledges the many
                    individuals and organizations who have contributed generously to the Indian River Lagoon Species
                    Inventory project.</p></td>
        </tr>
    </table>

    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center"><img src="../content/imglib/irl_sunrise.jpg" width="400"/></td>
        </tr>
    </table>
    <br/>
    <p style="width:700px;margin-left:auto;margin-right:auto;" class="title">Funding:</p>
    <p style="width:700px;margin-left:auto;margin-right:auto;" class="body">
        The Indian River Lagoon National Estuary Program (St. Johns River Water Management District) has provided
        generous financial support for the IRL Species Inventory project over the years and is gratefully acknowledged.
        Other funding sources include The Smithsonian Insitution's Seidell Fund and the St. Lucie County Board of
        Commissioners.
    </p>
    <p style="width:700px;margin-left:auto;margin-right:auto;" class="title">Technical Contributions:</p>
    <p style="width:700px;margin-left:auto;margin-right:auto;" class="body">The following individuals have contributed
        tirelessly to the technical expansion of the IRL Species Inventory since it was deposited with the Smithsonian
        Marine Station at Fort Pierce in 1997: Kathleen Hill, Joseph Dineen, L. Holly Sweat, and Julie Piraino.</p>
    <p>
    <p style="width:700px;margin-left:auto;margin-right:auto;" class="body">Other contributors include: Paul E.
        Hargraves (Phytoplankton), James Masterson, Melany Puglisi Weening, Brian Steves and Marianne Tempken.</p>
    <p style="width:700px;margin-left:auto;margin-right:auto;" class="body">Current Web Design: James Kochert, Joseph
        Dineen, and Julie Piraino</p>
    <p style="width:700px;margin-left:auto;margin-right:auto;" class="body">Initial Web Designs: Karen Davis, Kathleen
        Hill and L. Holly Sweat</p>

    <p style="width:700px;margin-left:auto;margin-right:auto;" class="body">Initial Inventory Compilation: Hilary Swain,
        Susan Hopkins and Clarissa Thornton</p>
    <table style="width:325px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <table width="325" class="table-border">
                    <tr>
                        <td><p class="label">Project Advisor</p></td>
                        <td><span>Valerie Paul</span></td>
                    </tr>
                    <tr>
                        <td><p class="label">Project Supervisor</p></td>
                        <td><span>Joseph Dineen</span></td>
                    </tr>
                    <tr>
                        <td><p class="label">Project Coordinator</p></td>
                        <td><span>L. Holly Sweat</span></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
