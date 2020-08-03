<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Fresh Water Lens on a Barrier Island</title>
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
    <h2>Fresh Water Lens on a Barrier Island</h2>
    <p align="left">
        <img border="0" src="../content/imglib/FWLens.png" hspace="10" vspace="10" align="center" width="344"
             height="375"><br>
        <br>
        <font color="#000080">
            Diagram of an idealized cross section of water flow patterns in the fresh water
            lens beneath a barrier island.&nbsp; Note that for each meter of free water
            table above mean sea level, there is approximately 40 meters of freshwater in
            the lens floating above the sea water aquifer.&nbsp; Redrawn from Art et
            al.&nbsp; 1974.</font></p>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
