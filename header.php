<link rel="stylesheet" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.min.css" type="text/css" >
<link rel="stylesheet" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/uw.css" type="text/css" >
<link rel="stylesheet" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/uw-ie.css" type="text/css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/fonts/fonts.0.0.1.css">
<link rel="stylesheet" href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/fonts/foundation-icons.css">
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>

<!-- UW-Madison Top Header, Accessible -->
<header>
    <a class="uw-show-on-focus" href="#main" id="uw-skip-link">Skip to main content</a>
    <div class="uw-global-bar" role="navigation">
        <a class="uw-global-name-link" href="http://www.wisc.edu" aria-label="University home page">
            U<span>niversity <span class="uw-of">of</span> </span>W<span>isconsin</span>-Madison
        </a>
    </div>

    <!-- UW-Madison Brand, Site Main Header -->
    <header id="branding" class="uw-header">
        <div class="uw-header-container">
            <div class="uw-header-crest-title">
                <div class="uw-header-crest">
                    <!-- <a href="http://www.wisc.edu"><img class="uw-crest-svg" src="<?php //echo $GLOBALS['CLIENT_ROOT'].'/images/uw-crest.svg' ?>" alt="Link to University of Wisconsin-Madison home page"></a> -->
                    <a href="http://www.wisc.edu">
                        <img class="uw-crest-svg" src="http://geoscience.wisc.edu/museum/wp-content/themes/madisonwp2015-1/images/hybrid_logo.svg" alt="Link to University of Wisconsin-Madison home page">
                    </a>
                </div>
                <div class="uw-title-tagline">
                    <h1 id="site-title" class="uw-site-title">
                        <a href="" rel="home">UW-Madison Integrated Specimen Portal</a>
                    </h1>
                    <div id="site-description" class="uw-site-tagline">
                        A Gateway to Biodiversity, Human, and Environmental Specimens from our Natural History Museums
                    </div>
                </div>
            </div>
            <!-- Add quicksearch here -->
    </header>

    <!-- Toggle Menu for mobile -->
    <button class="uw-mobile-menu-button-bar uw-mobile-menu-button-bar-reversed uw-is-closed " data-menu="uw-top-menus" aria-label="Open menu" aria-expanded="false" aria-controls="uw-top-menus">Menu
        <svg aria-hidden="true" focusable="false">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#uw-symbol-menu"></use>
        </svg>
        <svg aria-hidden="true" focusable="false">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#uw-symbol-close"></use>
        </svg>
    </button>

    <!-- Main Navigation -->
    <div id="uw-top-menus" class="uw-is-visible uw-horizontal uw-hidden" aria-hidden="false">
        <div class="uw-main-nav">
            <nav class="uw-nav-menu uw-nav-menu-reverse" aria-label="Main Menu">
                <ul id="uw-main-nav">
                    <li class="current-menu-item">
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" aria-current="page">Home</a>
                    </li>
                    <li class="uw-dropdown">
                        <a href="fallback_url_for_no_js" role="button" aria-haspopup="true" aria-expanded="false">
                            Search
                            <svg aria-hidden="true" class="uw-caret" focusable="false">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#uw-symbol-caret-down"></use>
                            </svg>
                            <svg aria-hidden="true" class="uw-caret" focusable="false">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#uw-symbol-caret-up"></use>
                            </svg>
                        </a>
                        <ul aria-hidden="true" aria-label="Search submenu" class="uw-child-menu">
                            <li>
                                <a href="#">Quick Search</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/index.php">Advanced Search</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial-module-info.php">Spatial Module</a>
                            </li>
                            <li>
                                <a href="#">Fun Searches</a>
                            </li>
                        </ul>
                    </li>
                    <li class="uw-dropdown">
                        <a href="fallback_url_for_no_js" role="button" aria-haspopup="true" aria-expanded="false">
                            Taxa
                            <svg aria-hidden="true" class="uw-caret" focusable="false">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#uw-symbol-caret-down"></use>
                            </svg>
                            <svg aria-hidden="true" class="uw-caret" focusable="false">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#uw-symbol-caret-up"></use>
                            </svg>
                        </a>
                        <ul aria-hidden="true" aria-label="Search submenu" class="uw-child-menu">
                            <li>
                                <a href="#">Biological and Non-biological trees</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/taxonomy/taxonomydisplay.php">Taxonomic Tree
                                    Viewer</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/taxonomy/taxonomydynamicdisplay.php">Taxonomy
                                    Explorer</a>
                            </li>
                        </ul>
                    </li>
                    <li class="uw-dropdown">
                        <a href="fallback_url_for_no_js" role="button" aria-haspopup="true" aria-expanded="false">
                            Images
                            <svg aria-hidden="true" class="uw-caret" focusable="false">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#uw-symbol-caret-down"></use>
                            </svg>
                            <svg aria-hidden="true" class="uw-caret" focusable="false">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#uw-symbol-caret-up"></use>
                            </svg>
                        </a>
                        <ul aria-hidden="true" aria-label="Images submenu" class="uw-child-menu">
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/search.php">Search Images</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/index.php">Browse Images</a>
                            </li>
                        </ul>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/digital-collections.php">Featured Specimens</a>
                    </li>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/museums.php">Museums</a>
                    </li>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/about.php">About</a>
                    </li>
                    <li>
                        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/help.php">Help</a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- UW-Madison Secondary Menu (Top) -->
        <div class="uw-secondary-nav">
            <nav class="uw-nav-menu uw-nav-menu-secondary" aria-label="Secondary Menu">
                <ul>
                    <li id="menu-news" class="uw-dropdown">
                        <a href="http://news.wisc.edu" aria-haspopup="true" aria-expanded="false">
                            News
                            <svg aria-hidden="true" class="uw-caret" focusable="false">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#uw-symbol-caret-down"></use>
                            </svg>
                            <svg aria-hidden="true" class="uw-caret" focusable="false">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#uw-symbol-caret-up"></use>
                            </svg>
                        </a>
                        <ul aria-hidden="true" aria-label="News submenu" class="uw-child-menu">
                            <li>
                                <a href="https://www.wisc.edu/about/">About</a>
                            </li>
                            <li>
                                <a href="https://map.wisc.edu/">Our Location</a>
                            </li>
                            <li>
                                <a href="https://news.wisc.edu/">News</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="http://today.wisc.edu">Calendar</a>
                    </li>
                    <li>
                        <a href="http://map.wisc.edu">Map</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <?php
    if ($GLOBALS['IS_ADMIN']) {
        ?>
        <!-- BEGIN LOGIN BAR -->
        <div aria-label="Admin Menu" id="right_navbarlinks" class="uw-admin-nav">
            <nav class="uw-nav-menu uw-nav-menu-reverse">
                <ul>
                    <?php
                    if ($GLOBALS['USER_DISPLAY_NAME']) {
                        ?>
                        <li>
                            Welcome <?php echo $GLOBALS['USER_DISPLAY_NAME']; ?>!
                        </li>
                        <li>
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/viewprofile.php">My Profile</a>
                        </li>
                        <li>
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/index.php?submit=logout">Logout</a>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li>
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT'] . "/profile/index.php?refurl=" . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']; ?>">
                                Log In
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/profile/newprofile.php">
                                New Account
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                    <li>
                        <a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitetools.php'>Site Tools</a>
                    </li>
                    <li>
                        <a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitemap.php'>Sitemap</a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- END LOGIN BAR -->
        <?php
    }
    ?>

</header>
