<?php
include_once(__DIR__ . '/config/symbini.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Home</title>
    <link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <meta name='keywords' content='' />
    <?php include_once(__DIR__ . '/config/googleanalytics.php'); ?>
</head>
<body>
<?php
include(__DIR__ . '/header.php');
?>
<div id="innertext">
    <h1>Welcome to the Green Theory Studio Symbiota demo portal</h1>

    <div class="pmargin">
        This portal serves as a demonstration of Green Theory Studio's version of Symbiota. The data provided here is for
        demonstration purposes only and, while originating from other Symbiota portals, should not be used for research or
        reference in any way. Please visit the <a href="https://github.com/greentheorystudio/Symbiota"><b>GitHub repository
        for this Symbiota version</b></a> for information on installation and system requirements. Further information about
        the Symbiota software project can be found at <a href="https://symbiota.org/docs/" target="_blank"><b>its general
        information site</b></a>.
    </div>
    <div class="pmargin">
        Some of the features that set this version of Symbiota apart from others include:
        <ul>
            <li>Easy installation using Composer</li>
            <li>An updated and improved <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php"><b>spatial
                mapping module</b></a> that offers more tools and better performance</li>
            <li>A complete conversion of all mapping functionality (like the
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=checklist"><b>Dynamic
                Checklist</b></a> and <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/dynamicmap.php?interface=key"><b>Dynamic
                Key</b></a> for example) to use the native spatial mapping module, so Google licenses are no longer needed</li>
            <li>Improved security measures and a built-in captcha system on the
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/newprofile.php"><b>New Account page</b></a> making
                it secure out-of-the-box</li>
            <li>An integrated search system allowing users to jump between viewing results in the
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/index.php"><b>text-based search</b></a> and
                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php"><b>spatial search</b></a> easily</li>
        </ul>
    </div>
    <div class="pmargin">
        If you would like login access to this portal to try out its administrative features, please send us a message
        through our <a href="https://greentheorystudio.com/contact/"><b>contact page</b></a> and we'd be happy to provide
        you with access.
    </div>
</div>

<?php
include(__DIR__ . '/footer.php');
?>
</body>
</html>
