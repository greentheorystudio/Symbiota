<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Contact</title>
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
    <h2>Contact Us</h2>
    <table style="width:450px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <p class="body">Please contact us with comments, suggestions and/or photographs:</p>
                <p class="body">Smithsonian Marine Station at Fort Pierce<br/>
                    701 Seaway Drive<br/>
                    Fort Pierce, FL 34949<br/>
                    <br/>
                    Phone: 772.462.6220&nbsp;FAX: 772.461.8154<br/>
                    <br/>
                    Send Comments to: <a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu</a></font></p>
            </td>
        </tr>
    </table>
    <br/>
    <table style="width:500px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <center><img src="../content/imglib/Stone Crab.jpg" width="500"/>&nbsp;
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
