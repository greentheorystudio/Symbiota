<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Indian River Lagoon Species Image Collection</title>
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
    <a name="Top"></a>
    <h2>Indian River Lagoon Species Image Collection</h2>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body"><b>Navigation tips for this gallery:</b></p></td>
        </tr>
    </table>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">- Click on any of the categories listed below in blue to navigate to the desired
                    section.<br>
                    - Species are listed in categories alphabetically by scientific name.
                    <br>
                    - Click on thumbnail images for a larger view.<br>
                    - Where available, click on
                    highlighted scientific names for more information.<br>
                    - <span class="red">Red</span> photo borders indicate non-native species. </p></td>
        </tr>
    </table>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body"><a href="#Birds">BIRDS</a><br>
                    <a href="#BivalveMollusks">BIVALVE MOLLUSKS</a><br>
                    <a href="#Cnidarians">CNIDARIANS</a><br>
                    <a href="#Crustaceans">CRUSTACEANS</a><br>
                    <a href="#Echinoderms">ECHINODERMS</a><br>
                    <a href="#Fishes">FISHES</a><br>
                    <a href="#Gastropods">GASTROPOD MOLLUSKS</a></p>
            </td>
            <td><p class="body"><a href="#Insects">
      INSECTS & SPIDERS</a><br>
      <a href="#Mammals">MAMMALS</a><br>
      <a href="#Algae">MARINE ALGAE</a><br>
      <a href="#Misc">MISCELLANEOUS ORGANISMS</a><br>
      <a href="#Plants">PLANTS</a><br>
      <a href="#Reptiles">REPTILES & AMPHIBIANS</a><br>
      <a href="#Sponges">SPONGES</a></p></td>
        </tr>
    </table>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" class="highlight">
        <tr>
            <td><p class="body"><strong>Image Use:</strong> With the exception of images clearly attributed to other
                    institutions, most photographs in this gallery are subject to the Smithsonian Institution's <a
                            href="http://www.si.edu/termsofuse">Terms of Use</a> policy. Contact the <a
                            href="mailto:IRLWebmaster@si.edu">Webmaster</a><a href="IRLWebmaster@si.edu"></a> to
                    request use for commercial purposes.</p></td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="title"><a name="Birds"></a>Birds</p></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="10" cellspacing="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Agelaius phoeniceus Red-winged Blackbird P Winegar.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Agelaius phoeniceus Red-winged Blackbird P Winegar.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Aix sponsa Wood Duck Ellie VanOs.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Aix sponsa Wood Duck Ellie VanOs.jpg" width="140"
                            height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Alopochen aegyptiaca Egyptian Goose P Boone.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Alopochen aegyptiaca Egyptian Goose P Boone.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Anas acuta Northern Pintail Marc Virgilio.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Anas acuta Northern Pintail Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Agelaius phoeniceus</i><br/>
            <br>
            Red-winged Blackbird<br>
              Photo: P Winegar<br>
          </span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Aix sponsa</i><br/>
            <br>
Wood Duck<br>
Photo: Ellie Van Os</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Alopochen aegyptiaca</i><br/>
            <br>
Egyptian Goose<br>
Photo: P Boone</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Anas acuta</i><br/>
            <br>
Northern Pintail<br>
Photo: Marc Virgilio</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Anas platyrhynchos Mallard Pat Poston.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Anas platyrhynchos Mallard Pat Poston.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Anhinga anhinga Anhinga P Holm.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Anhinga anhinga Anhinga P Holm.jpg" width="140"
                            height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Aphelocoma coerulescens Florida Scrub Jay Marc Virgilio.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Aphelocoma coerulescens Florida Scrub Jay Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Aramus guarauna Limpkin Chick Joel Reynolds.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Aramus guarauna Limpkin Chick Joel Reynolds.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Anas platyrhynchos</i><br/>
            <br>
  Mallard <br>
Photo: Pat Poston</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Anhinga anhinga</i><br/>
            <br>
  Anhinga<br>
Photo: P Holm</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Aphelocoma coerulescens</i><br/>
            <br>
Florida Scrub Jay<br>
Photo: P Winegar</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Aramus guarauna</i><br/>
            <br>
  Limpkin Chick<br>
Photo: Joel Reynolds</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Aramus guarauna Limpkin Marc Virgilio.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Aramus guarauna Limpkin Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Archilochus colubris Ruby-throated Hummingbird L Savary.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Archilochus colubris Ruby-throated Hummingbird L Savary.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Ardea alba Great Egret Z Kappel.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Ardea alba Great Egret Z Kappel.jpg" width="140"
                            height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Ardea herodias Great Blue Heron Bo Rainbolt.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Ardea herodias Great Blue Heron Bo Rainbolt.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Aramus guarauna</i><br/>
            <br>
  Limpkin <br>
Photo: Marc Virgilio</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Archilochus colubris</i><br/>
            <br>
            Ruby-throated Hummingbird<br>
Photo: L Savary</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Ardea alba">Ardea alba</a></i><br/>
            <br>
            Great Egret<br>
Photo: Z Kappel</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Ardea herodias">Ardea herodias</a></i><br/>
            <br>
            Great Blue Heron<br>
Photo: Bo Rainbolt</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Arenaria interpres Ruddy Turnstone 2004 SI.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Arenaria interpres Ruddy Turnstone 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Aythya americana Redhead N Lemmon.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Aythya americana Redhead N Lemmon.jpg" width="140"
                            height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Botaurus lentiginosus American Bittern D Keighley.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Botaurus lentiginosus American Bittern D Keighley.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Bubo virginianus Great Horned Owl Ellie VanOs.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Bubo virginianus Great Horned Owl Ellie VanOs.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Arenaria interpres</i><br/>
            <br>
            Ruddy Turnstone<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Aythya americana</i><br/>
            <br>
            Redhead<br>
Photo: N Lemmon</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Botaurus lentiginosus</i><br/>
            <br>
            American Bittern<br>
Photo: D Keighley</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Bubo virginianus</i><br/>
            <br>
            Great Horned Owl<br>
Photo: Ellie Van Os</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Bubo virginianus Great Horned Owl Juvenile Matthew Sviben.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Bubo virginianus Great Horned Owl Juvenile Matthew Sviben.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Bubulcus ibis Cattle Egret Katie Burke.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Bubulcus ibis Cattle Egret Katie Burke.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Buteo lineatus Red-shouldered Hawk Marc Virgilio.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Buteo lineatus Red-shouldered Hawk Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Butorides virescens Green Heron Patricia Corapi.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Butorides virescens Green Heron Patricia Corapi.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Bubo virginianus</i><br/>
            <br>
Great Horned Owl Juvenile<br>
Photo: Pat Poston</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Bubulcus ibis">Bubulcus ibis</a></i><br/>
            <br>
Cattle Egret<br>
Photo: K Burke</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Buteo lineatus</i><br/>
            <br>
            Red-shouldered Hawk<br>
Photo: Marc Virgilio</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Butorides virescens</i><br/>
            <br>
            Green Heron<br>
Photo: Patricia Corapi</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Calidris alba Sanderling Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Calidris alba Sanderling Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Calidris alpina Dunlin Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Calidris alpina Dunlin Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Calidris canutus Red Knot A Banker.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Calidris canutus Red Knot A Banker.jpg" width="140"
                            height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Calidris mauri Western Sandpiper Marc Virgilio.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Calidris mauri Western Sandpiper Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Calidris alba</i><br/>
            <br>
            Sanderling <br>
Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Calidris alpina</i><br/>
            <br>
            Dunlin<br>
Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Calidris canutus">Calidris canutus</a></i><br/>
            <br>
            Red Knot<br>
Photo: A Banker</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Calidris mauri">Calidris mauri</a></i><br/>
            <br>
Western Sandpiper<br>
Photo: Marc Virgilio</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Caracara cheriway Crested Caracara Arnold Dubin.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Caracara cheriway Crested Caracara Arnold Dubin.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Cardinalis cardinalis Northern Cardinal Crystal Samuel.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Cardinalis cardinalis Northern Cardinal Crystal Samuel.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Cathartes aura Turkey Vulture N Loftin.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Cathartes aura Turkey Vulture N Loftin.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Charadrius vociferus Killdeer Jack Rogers.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Charadrius vociferus Killdeer Jack Rogers.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Caracara cheriway</i><br/>
            <br>
Crested Caracara<br>
Photo: Arnold Dubin</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cardinalis cardinalis</i><br/>
            <br>
Northern Cardinal<br>
Photo: Crystal Samuel</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cathartes aura</i><br/>
            <br>
Turkey Vulture<br>
Photo: N Loftin</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Charadrius vociferus<br/>
          </i><br>
Killdeer <br>
Photo: Jack Rogers</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Chroicocephalus philadelphia Bonaparte's Gull Thomas Dunkerton.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Chroicocephalus philadelphia Bonaparte's Gull Thomas Dunkerton.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Colinus virginianus Northern Bobwhite Juveniles Pat Poston.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Colinus virginianus Northern Bobwhite Juveniles Pat Poston.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Coragyps atratus Black Vulture J Willis.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Coragyps atratus Black Vulture J Willis.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Dendrocygna autumnalis Black-bellied Whistling Duck Edward McEwens.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Dendrocygna autumnalis Black-bellied Whistling Duck Edward McEwens.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Chroicocephalus philadelphia</i><br/>
            <br>
Bonaparte's Gull<br>
Photo: Thomas Dunkerton</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Colinus virginianus</i><br/>
            <br>
Northern Bobwhite Juveniles<br>
Photo: Pat Poston</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Coragyps atratus</i><br/>
            <br>
Black Vulture<br>
Photo: J Willis</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Dendrocygna autumnalis<br/>
          </i><br>
Black-bellied Whistling Duck<br>
Photo: Edward McEwens</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Egretta caerulea Little Blue Heron Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Egretta caerulea Little Blue Heron Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Egretta rufescens Reddish Egret Amy Tripp Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Egretta rufescens Reddish Egret Amy Tripp Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Egretta thula Snowy Egret Jack Rogers.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Egretta thula Snowy Egret Jack Rogers.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Egretta tricolor Tricolored Heron Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Egretta tricolor Tricolored Heron Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Egretta caerulea">Egretta caerulea</a></i><br/>
            <br>
Little Blue Heron<br>
Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Egretta rufescens">Egretta rufescens</a></i><br/>
            <br>
Reddish Egret<br>
Photo: Amy Tripp jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Egretta thula">Egretta thula</a></i><br/>
            <br>
Snowy Egret<br>
Photo: Jack Rogers</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Egretta tricolor">Egretta tricolor</a></i><br/>
            <br>
Tricolored Heron<br>
Photo: jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Eudocimus albus White Ibis John Whiticar.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Eudocimus albus White Ibis John Whiticar.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Gallinula chloropus Common Gallinule J Stone.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Gallinula chloropus Common Gallinule J Stone.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Grus canadensis Sandhill Crane Juvenile Marc Virgilio.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Grus canadensis Sandhill Crane Juvenile Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Grus canadensis Sandhill Crane K Fosselman.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Grus canadensis Sandhill Crane K Fosselman.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Eudocimus albus">Eudocimus albus</a></i><br/>
            <br>
White Ibis<br>
Photo: John Whiticar</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Gallinula chloropus</i><br/>
            <br>
Common Gallinule<br>
Photo: J Stone</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Grus canadensis</i><br/>
            <br>
Sandhill Crane Chicks<br>
Photo: Marc Virgilio</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Grus canadensis</i><br/>
            <br>
Sandhill Crane<br>
Photo: K Fosselman</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Haematopus palliatus American Oystercatcher Ellie VanOs.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Haematopus palliatus American Oystercatcher Ellie VanOs.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Haliaeetus leucocephalus Bald Eagle Edward McEwen.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Haliaeetus leucocephalus Bald Eagle Edward McEwen.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Himantopus mexicanus Black-necked Stilt Ursula Dubrick.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Himantopus mexicanus Black-necked Stilt Ursula Dubrick.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Larus argentatus Herring Gull Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Larus argentatus Herring Gull Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Haematopus palliatus</i><br/>
            <br>
American Oystercatcher<br>
Photo: Ellie Van Os</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Haliaeetus leucocephalus</i><br/>
            <br>
Bald Eagle<br>
Photo: Edward McEwen</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Himantopus mexicanus</i><br/>
            <br>
Black-necked Stilt<br>
Photo: Ursula Dubrick</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Larus argentatus</i><br/>
            <br>
Herring Gull<br>
Photo: jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Larus marinus Great Black-backed Gull Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Larus marinus Great Black-backed Gull Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Leucophaeus atricilla Laughing Gull Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Leucophaeus atricilla Laughing Gull Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Lophodytes cucullatus Hooded Merganser Michael McGinnity.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Lophodytes cucullatus Hooded Merganser Michael McGinnity.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Melanerpes carolinus Red-bellied Woodpecker Ellie VanOs.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Melanerpes carolinus Red-bellied Woodpecker Ellie VanOs.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Larus marinus</i><br/>
            <br>
Great Black-backed Gull<br>
Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Leucophaeus atricilla</i><br/>
            <br>
Laughing Gull<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lophodytes cucullatus</i><br/>
            <br>
Hooded Merganser<br>
Photo: Michael McGinnity</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Melanerpes carolinus<br/>
          </i><br>
Red-bellied Woodpecker<br>
Photo: Ellie Van Os</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Mergus serrator Red-breasted Merganser Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Mergus serrator Red-breasted Merganser Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Mimus polyglottos Northern Mockingbird L Baur.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Mimus polyglottos Northern Mockingbird L Baur.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Mniotilta varia Black-and-White Warbler Ellie VanOs.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Mniotilta varia Black-and-White Warbler Ellie VanOs.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Morus bassanus Northern Gannet Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Morus bassanus Northern Gannet Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Mergus serrator</i><br/>
            <br>
Red-breasted Merganser<br>
Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Mimus polyglottos</i><br/>
            <br>
Northern Mockingbird<br>
Photo: L Baur</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Mniotilta varia</i><br/>
            <br>
Black-and-White Warbler<br>
Photo: Ellie Van Os</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Morus bassanus</i><br/>
            <br>
Northern Gannet<br>
Photo: Joel Wooster jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Mycteria americana Wood Stork R Schneider.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Mycteria americana Wood Stork R Schneider.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Nyctanassa violacea Yellow-crowned Night-Heron 2004 SI.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Nyctanassa violacea Yellow-crowned Night-Heron 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Nycticorax nycticorax Black-crowned Night-Heron Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Nycticorax nycticorax Black-crowned Night-Heron Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Pandion haliaetus Osprey Kathleen McMullen.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Pandion haliaetus Osprey Kathleen McMullen.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Mycteria americana</i><br/>
            <br>
Wood Stork<br>
Photo: R Schneider</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Nyctanassa violacea</i><br/>
            <br>
Yellow-crowned Night Heron<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Nycticorax nycticorax</i><br/>
            <br>
Black-crowned Night Heron<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pandion haliaetus</i><br/>
            <br>
Osprey <br>
Photo: Kathleen McMullen</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Pelecanus erythrorhynchos American White Pelican Joy Carey.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Pelecanus erythrorhynchos American White Pelican Joy Carey.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Pelecanus occidentalis Brown Pelican Joy Carey.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Pelecanus occidentalis Brown Pelican Joy Carey.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Phalacrocorax auritus Double-crested Cormorant J Andreozzi.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Phalacrocorax auritus Double-crested Cormorant J Andreozzi.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Platalea ajaja Roseate Spoonbill Mary White.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Platalea ajaja Roseate Spoonbill Mary White.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pelacanus erythrorhynchos</i><br/>
            <br>
American White Pelican<br>
Photo: Joy Carey</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Pelecanus occidentalis">Pelecanus occidentalis</a></i><br/>
            <br>
Brown Pelican<br>
Photo: Joy Carey</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Phalacrocorax auritus</i><br/>
            <br>
Double-crested Cormorant<br>
Photo: J Andreozzi</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Ajaia ajaia">Platalea ajaja</a></i><br/>
            <br>
Roseate Spoonbill<br>
Photo: Mary White</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Plegadis falcinellus Glossy Ibis Marc Virgilio.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Plegadis falcinellus Glossy Ibis Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Pluvialis squatarola Black-bellied Plover Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Pluvialis squatarola Black-bellied Plover Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Podilymbus podiceps Pied-billed Grebe Marc Virgilio.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Podiceps auritus Horned Grebe Thomas Dunkerton.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Podilymbus podiceps Pied-billed Grebe Marc Virgilio.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Podilymbus podiceps Pied-billed Grebe Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Plegadis falcinellus</i><br/>
            <br>
Glossy Ibis<br>
Photo: Marc Virgilio</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pluvialis squatarola</i><br/>
            <br>
Black-bellied Plover<br>
Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Podiceps auritus</i><br/>
            <br>
Horned Grebe<br>
Photo: Thomas Dunkerton</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Podilymbus podiceps<br/>
          </i><br>
Pied-billed Grebe<br>
Photo: Marc Virgilio</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Quiscalus major Boat-tailed Grackle Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Quiscalus major Boat-tailed Grackle Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Rallus longirostris Clapper Rail 2004 SI.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Rallus longirostris Clapper Rail 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Rynchops niger Black Skimmer Kathleen McMullen.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Rynchops niger Black Skimmer Kathleen McMullen.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Sternula antillarum Least Tern Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Sternula antillarum Least Tern Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Quiscalus major</i><br/>
            <br>
Boat-tailed Grackle<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Rallus longirostris</i><br/>
            <br>
Clapper Rail<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Rynchops niger</i><br/>
            <br>
Black Skimmer<br>
Photo: Kathleen McMullen</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sterna antillarum</i><br/>
            <br>
Least Tern<br>
Photo: jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Sternula antillarum Least Tern Nesting Marc Virgilio.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Sternula antillarum Least Tern Nesting Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Strix varia Barred Owl I Schauer.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Strix varia Barred Owl I Schauer.jpg" width="140"
                            height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Thalasseus maximus Royal Tern Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Thalasseus maximus Royal Tern Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Thalasseus sandvicensis Sandwich Tern Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Thalasseus sandvicensis Sandwich Tern Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sterna antillarum</i><br/>
            <br>
Least Terns Nesting<br>
Photo: Marc Virgilio</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Strix varia</i><br/>
            <br>
Barred Owl<br>
Photo: I Schauer</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Thalasseus maximus</i><br/>
            <br>
Royal Tern<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Thalasseus sandvicensis<br/>
          </i><br>
Sandwich Tern<br>
Photo: Joel Wooster jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Tringa melanoleuca Greater Yellowlegs Marc Virgilio.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Tringa melanoleuca Greater Yellowlegs Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Tringa semipalmata Willet Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Tringa semipalmata Willet Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Birds/Cropped Images/Vireo griseus White-eyed Vireo Beverly Gardner.jpg"><img
                            src="../content/imglib/Birds/Thumbnails/Vireo griseus White-eyed Vireo Beverly Gardner.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tringa melanoleuca</i><br/>
            <br>
Greater Yellowlegs<br>
Photo: Marc Virgilio</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tringa semipalmata</i><br/>
            <br>
Willet <br>
Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Vireo griseus</i><br/>
            <br>
White-eyed Vireo<br>
Photo: Beverly Gardner</span></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td align="right"><a href="#Top">Back to Top</a></td>
        </tr>
        <tr>
            <td><p class="title"><a name="BivalveMollusks"></a>BIVALVE MOLLUSKS - Clams, mussels, oysters and kin</p>
            </td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="10" cellspacing="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Anadara transversa Transverse Ark Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Abra aequalis Atlantic Abra Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Anadara notabilis Eared Ark Clam Marlo Krisberg Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Anadara notabilis Eared Ark Clam Marlo Krisberg Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Anadara transversa Transverse Ark Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Anadara transversa Transverse Ark Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Anodontia alba Buttercup Lucine Amy Tripp Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Anodontia alba Buttercup Lucine Amy Tripp Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Abra aequalis</i><br><br>
      Atlantic Abra<br>
      Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Anadara notabilis</i><br><br>
      Eared Ark Clam<br>
    Photo: Marlo Krisberg jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Anadara transversa</i><br><br>
      Transverse Ark<br>
    Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Anodontia alba">Anodontia alba</a></i><br><br>
      Buttercup Lucine<br>
    Photo: Amy Tripp jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Anomia simplex Common Jingle Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Anomia simplex Common Jingle Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Arca imbricata Mossy Ark Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Arca imbricata Mossy Ark Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Arcinella cornuta Florida Spiny Jewelbox Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Arcinella cornuta Florida Spiny Jewelbox Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Arcopsis adamsi Adams Ark Marlo Krisberg Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Arcopsis adamsi Adams Ark Marlo Krisberg Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Anomia simplex</i><br><br>
      Common Jingle<br>
      Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Arca imbricata</i><br><br>
        Mossy Ark<br>
    Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Arcinella cornuta</i><br><br>
        Florida Spiny Jewelbox<br>
    Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Arcopsis adamsi</i><br><br>
        Adams Ark<br>
    Photo: Marlo Krisberg jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Argopecten gibbus Bay Scallop Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Argopecten gibbus Bay Scallop Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Atrina seminuda Half-naked Penshell Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Atrina seminuda Half-naked Penshell Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Anadara brasiliana_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Argopecten irradians concentricus_small.jpg"
                        width="141" height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Argopecten gibbus">Argopecten gibbus</a></i><br><br>
      Bay Scallop<br>
      Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Atrina seminuda</i><br><br>
        Half-naked Penshell<br>
    Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Anadara brasiliana</i><br>
        <br>
        Incongruous Ark
<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span
                        class="caption"><i>Argopecten irradians concentricus</i><br>
        <br>
        Florida Gulf Bay Scallop
<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Barbatia candida_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Barnea truncata Atlantic Mud Piddock Brian Marshall Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Barnea truncata Atlantic Mud Piddock Brian Marshall Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Brachidontes exustus Scorched Mussel Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Brachidontes exustus Scorched Mussel Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Caryocorbula contracta_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Barbatia candida</i><br>
        <br>
        White-beard Ark
<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Barnea truncata</i><br>
        <br>
Atlantic Mud Piddock<br>
Photo: Brian Marshall jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Brachidontes exustus">Brachidontes exustus</a></i><br>
        <br>
Scorched Mussel<br>
Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Caryocorbula contracta</i><br>
        <br>
        <br>
Photo: Paula Mikkelson Harbor Branch</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Chama congregata Corrugate Jewelbox Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Chama congregata Corrugate Jewelbox Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Chama macerophylla Leafy Jewelbox Amy Tripp Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Chama macerophylla Leafy Jewelbox Amy Tripp Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Chione cancellata Cross-barred Venus Jax shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Chione cancellata Cross-barred Venus Jax shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Chioneryx grus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Chama congregata</i><br>
        <br>
Corrugate Jewelbox<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Chama macerophylla</i><br>
        <br>
Leafy Jewelbox<br>
Photo: Amy Tripp jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Chione cancellata">Chione cancellata</a></i><br>
        <br>
Cross-barred Venus<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Chioneryx grus</i><br>
        <br>
Gray Pygmy Venus<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Isognomon bicolor Bicolor Purse-oyster Jax Shells.jpg"></a><a
                        href="../content/imglib/Bivalves/Cropped Images/Crassostrea virginica Eastern Oyster Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Crassostrea virginica Eastern Oyster Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Ctena orbiculata_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Cyrtopleura costata Angelwing Joseph Dineen.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Cyrtopleura costata Angelwing Joseph Dineen.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Dinocardium robustum Atlantic Giant Cockle Joseph Dineen.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Dinocardium robustum Atlantic Giant Cockle Joseph Dineen.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Crassostrea virginica">Crassostrea virginica</a></i><br>
        <br>
Eastern Oyster<br>
Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ctena orbiculata</i><br>
        <br>
        Dwarf Tiger Lucine
<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Cyrtopleura costata">Cyrtopleura costata</a></i><br>
        <br>
Angelwing<br>
Photo: Joseph Dineen</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Dinocardium robustum</i><br>
          <br>
Atlantic Giant Cockle<br>
Photo: Joseph Dineen</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Donax variabilis Variable Coquina Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Donax variabilis Variable Coquina Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Geukensia demissa_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Ostreola equestris Crested Oyster Jax Shells.jpg"></a><a
                        href="../content/imglib/Bivalves/Cropped Images/Isognomon alatus Flat Tree-oyster 2004 SI.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Isognomon alatus Flat Tree-oyster 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Isognomon bicolor Bicolor Purse-oyster Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Isognomon bicolor Bicolor Purse-oyster Jax Shells.jpg"
                            alt="" width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Donax variabilis</i><br>
        <br>
Variable Coquina<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Geukensia demissa</i><br>
        <br>
Ribbed Mussel<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Isognomon alatus">Isognomon alatus</a></i><br>
        <br>
Flat Tree-oyster<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Isognomon bicolor</i><br>
        <br>
Bicolor Purse-oyster<br>
Photo: jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Lasaea adansoni_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Lima scabra Flame Scallop L Holly Sweat.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Lima scabra Flame Scallop L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Limaria pellucida_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Lucina pectinata_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lasaea adansoni</i><br>
        <br>
        Reddish Lepton
<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lima scabra</i><br>
        <br>
Flame Scallop<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Limaria pellucida</i><br>
        <br>
Antillean Fileclam<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lucina pectinata</i><br>
        <br>
Thick Lucine<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
        </tr>
        <tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Macoploma tenta_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Macrocallista nimbosa Sunray Venus Amy Tripp Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Macrocallista nimbosa Sunray Venus Amy Tripp Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Mactrotoma fragilis_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Martesia striata_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Macoploma tenta</i><br>
        <br>
        Elongate Macoma
<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Macrocallista nimbosa</i><br>
        <br>
Sunray Venus<br>
Photo: Amy Tripp jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Macrotoma fragilis</i><br>
        <br>
Fragile Surfclam<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Martesia striata</i><br>
        <br>
Striate Piddock <br>
Photo: Paula Mikkelson Harbor Branch</span></td>
        </tr>
        <tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Mercenaria campechiensis Southern Quahog Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Mercenaria campechiensis Southern Quahog Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Mercenaria mercenaria Northern Quahog Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Mercenaria mercenaria Northern Quahog Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Modiolus squamosus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Musculus lateralis_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Mercenaria campechiensis">Mercenaria campechiensis</a></i><br>
        <br>
Southern Quahog<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Mercenaria mercenaria">Mercenaria mercenaria</a></i><br>
        <br>
Northern Quahog<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Modiolus squamosus</i><br>
        <br>
Southern Horse Mussel <br>
Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Musculus lateralis</i><br>
        <br>
        Lateral Mussel
<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
        </tr>
        <tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Mytella charruana Charrua Mussel Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Mytella charruana Charrua Mussel Jax Shells.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Nucula proxima_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Ostreola equestris Crested Oyster Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Ostreola equestris Crested Oyster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Parastarte triquetra_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Mytella charruana">Mytella charruana</a></i><br>
        <br>
Charrua Mussel<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Nucula proxima</i><br><br>
        Atlantic Nutclam<br>
        Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Ostreola equestris">Ostreola equestris</a></i><br>
        <br>
Crested Oyster<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Parastarte triquetra</i><br>
        <br>
        Brown Gemclam
<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
        </tr>
        <tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Parvilucina multilineata_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Periploma margaritaceum_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Perna viridis Asian Green Mussel Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Perna viridis Asian Green Mussel Jax Shells.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Pinna carnea Amber Penshell 2004 SI.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Pinna carnea Amber Penshell 2004 SI.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Parvilucina multilineata</i><br><br>
      Many-lined Lucine<br>
      Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Periploma margaritaceum</i><br><br>
        Unequal Spoonclam<br>
        Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Perna viridis">Perna viridis</a></i><br>
        <br>
Asian Green Mussel<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pinna carnea</i><br>
        <br>
Amber Penshell<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
        </tr>
        <tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Pitar fulminatus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Psammotreta brevifrons_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Pseudochama cristella_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Pteria colymbus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pitar fulminatus</i><br><br>
      Lightning Pitar<br>
      Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Psammotreta brevifrons</i><br><br>
        Short Macoma<br>
        Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pseudochama cristella</i><br><br>
        Atlantic Jewel Box<br>
        Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pteria colymbus</i><br>
        <br>
Atlantic Wing-oyster<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
        </tr>
        <tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Semele proficua_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Semele purpurascens_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Sphenia fragilis_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Tagelus divisus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Semele proficua</i><br>
        <br>
      Atlantic Semele<br>
      Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Semele purpurascens</i><br><br>
        Purplish Semele<br>
        Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sphenia fragilis</i><br><br>
        Antillean Sphenia<br>
        Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tagelus divisus</i><br>
        <br>
Purplish Tagelus<br>
Photo: Paula Mikkelson Harbor Branch</span></td>
        </tr>
        <tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Tagelus plebeius Stout Tagelus Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Tagelus plebeius Stout Tagelus Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Tellidora cristata_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Bivalves/Cropped Images/Trachycardium egmontianum Florida Pricklycockle Amy Tripp Jax Shells.jpg"><img
                            src="../content/imglib/Bivalves/Thumbnails/Trachycardium egmontianum Florida Pricklycockle Amy Tripp Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tagelus plebeius</i><br><br>
      Stout Tagelus<br>
      Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tellidora cristata</i><br>
        <br>
        White-crest Tellin<br>
    Photo: Paula Mikkelson Harbor Branch</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Trachycardium egmontianum</i><br><br>
        Florida Pricklycockle<br>
    Photo: Amy Tripp jaxshells.com</span></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td align="right"><a href="#Top">Back to Top</a></td>
        </tr>
        <tr>
            <td><p class="title"><a name="Cnidarians"></a>CNIDARIANS - Anemones, corals, jellyfishes and kin</p></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Cnidarians/Cropped Images/Actinia bermudensis Maroon Anemone L Holly Sweat.jpg"><img
                            src="../content/imglib/Cnidarians/Thumbnails/Actinia bermudensis Maroon Anemone L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Cnidarians/Cropped Images/Astrangia poculata Northern Star Coral Wes Pratt.jpg"><img
                            src="../content/imglib/Cnidarians/Thumbnails/Astrangia poculata Northern Star Coral Wes Pratt.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Cnidarians/Cropped Images/Calliactis tricolor Hermit Anemone Jax Shells.jpg"><img
                            src="../content/imglib/Cnidarians/Thumbnails/Calliactis tricolor Hermit Anemone Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Cnidarians/Cropped Images/Carijoa riisei Snowflake Coral L Holly Sweat.jpg"><img
                            src="../content/imglib/Cnidarians/Thumbnails/Carijoa riisei Snowflake Coral L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Actinia bermudensis">Actinia bermudensis</a></i><br><br>
      Maroon Anemone<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Astrangia poculata">Astrangia poculata</a></i><br><br>
      Northern Star Coral<br>
    Photo: Wes Pratt</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Calliactis tricolor">Calliactis tricolor</a></i><br><br>
      Hermit Anemone<br>
    Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Carijoa riisei</i><br><br>
      Snowflake Coral<br>
    Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Cnidarians/Cropped Images/Cassiopea xamachana Upside-down Jellyfish L Holly Sweat.jpg"><img
                            src="../content/imglib/Cnidarians/Thumbnails/Cassiopea xamachana Upside-down Jellyfish L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Chrysaora quinquecirrha_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Diadumene lineata_small.jpg" width="141"
                        height="106" class="red-border"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Leptogorgia virgulata_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cassiopea xamachana</i><br><br>
      Upside-down Jellyfish<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Chrysaora quinquecirrha</i><br>
        <br>
        Sea Nettle
<br>
Photo: Jeff Moore <a href="https://creativecommons.org/licenses/by-nc/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Diadumene lineata</i><br>
        <br>
        Striped Sea Anemone
<br>
Photo: MJ Adams <a href="https://creativecommons.org/licenses/by-nc/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Leptogorgia virgulata</i><br>
        <br>
        Colorful Sea Whip
<br>
Photo: Crabby Taxonomist <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Macrorhynchia philippina.jpeg_small.jpg"
                        width="141" height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Cnidarians/Cropped Images/Oculina diffusa Diffuse Ivory Bush Coral L Holly Sweat.jpg"><img
                            src="../content/imglib/Cnidarians/Thumbnails/Oculina diffusa Diffuse Ivory Bush Coral L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Pennaria disticha_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Cnidarians/Cropped Images/Phyllangia americana Hidden Cup Coral SERTC.jpg"><img
                            src="../content/imglib/Cnidarians/Thumbnails/Phyllangia americana Hidden Cup Coral SERTC.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Macrorhynchia philippina</i><br>
        <br>
Stinging Hydroid<br>
Photo: Anne Hoggett <a href="https://creativecommons.org/licenses/by/3.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Oculina diffusa">Oculina diffusa</a></i><br>
        <br>
Diffuse Ivory Bush Coral
    Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pennaria disticha</i><br>
        <br>
        Feathered Hydroid<br>
Photo: Alberto Garcia <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Phyllangia americana">Phyllangia americana</a></i><br>
        <br>
Hidden Cup Coral<br>
    Photo: SERTC</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Cnidarians/Cropped Images/Physalia physalis Portuguese Man o' War Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Cnidarians/Thumbnails/Physalia physalis Portuguese Man o' War Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Cnidarians/Cropped Images/Porpita porpita Blue Button Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Cnidarians/Thumbnails/Porpita porpita Blue Button Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Cnidarians/Cropped Images/Renilla reniformis Sea Pansy Jax Shells.jpg"><img
                            src="../content/imglib/Cnidarians/Thumbnails/Renilla reniformis Sea Pansy Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Cnidarians/Cropped Images/Siderastrea radians Lesser Starlet Coral Guillermo Diaz Pulido.jpg"><img
                            src="../content/imglib/Cnidarians/Thumbnails/Siderastrea radians Lesser Starlet Coral Guillermo Diaz Pulido.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Physalia physalis</i><br>
        <br>
Portuguese Man o'War
    Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Porpita porpita</i><br>
        <br>
Blue Button<br>
      Photo: Joel Wooster<br>jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Renilla reniformis</i><br>
        <br>
Sea Pansy<br>
    Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Siderastrea radians">Siderastrea radians</a></i><br>
        <br>
Lesser Starlet Coral<br>
    Photo: Guillermo Diaz Pulido</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Cnidarians/Cropped Images/Stomolophus meleagris Cannonball Jelly Jax Shells.jpg"><img
                            src="../content/imglib/Cnidarians/Thumbnails/Stomolophus meleagris Cannonball Jelly Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Cnidarians/Cropped Images/Velella velella By-the-wind Sailor Jax Shells.jpg"><img
                            src="../content/imglib/Cnidarians/Thumbnails/Velella velella By-the-wind Sailor Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Stomolophus meleagris</i><br>
        <br>
Cannonball Jelly<br>
    Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Velella velella</i><br>
        <br>
By-the-Wind Sailor<br>
      Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td align="right"><a href="#Top">Back to Top</a></td>
        </tr>
        <tr>
            <td><p class="title"><a name="Crustaceans"></a>CRUSTACEANS - Crabs, shrimps, lobsters, barnacles and kin</p>
            </td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Alpheus formosus Striped Snapping Shrimp L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Alpheus formosus Striped Snapping Shrimp L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Alpheus heterochaelis Bigclaw Snapping Shrimp SERTC.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Alpheus heterochaelis Bigclaw Snapping Shrimp SERTC.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Amphibalanus amphitrite Striped Acorn Barnacle L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Amphibalanus amphitrite Striped Acorn Barnacle L Holly Sweat.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Amphibalanus eburneus Ivory Barnacle L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Amphibalanus eburneus Ivory Barnacle L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Alpheus formosus</i><br><br>
      Striped Snapping Shrimp<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Alpheus heterochaelis">Alpheus heterochaelis</a></i><br><br>
      Bigclaw Snapping Shrimp<br>
    Photo: SERTC</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Amphibalanus amphitrite">Amphibalanus amphitrite</a></i><br><br>
      Striped Acorn Barnacle<br>
    Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Amphibalanus eburneus">Amphibalanus eburneus</a></i><br><br>
      Ivory Barnacle<br>
    Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Aratus pisonii Mangrove Tree Crab 2004 SI.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Aratus pisonii Mangrove Tree Crab 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Arenaeus cribrarius Speckled Swimming Crab Jax Shells.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Arenaeus cribrarius Speckled Swimming Crab Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Armases ricordi Humic Marsh Crab 2004 SI.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Armases ricordi Humic Marsh Crab 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Calappa flammea Flame Box Crab L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Calappa flammea Flame Box Crab L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Aratus pisonii">Aratus pisonii</a></i><br><br>
      Mangrove Tree Crab<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Arenaeus cribrarius</i><br><br>
        Speckled Swimming Crab<br>
    Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Armases ricordi</i><br><br>
        Humic Marsh Crab<br>
    Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Calappa flammea</i><br><br>
        Flame Box Crab<br>
    Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Callianassa sp Mud Shrimp Sabine Alshuth.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Callianassa sp Mud Shrimp Sabine Alshuth.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Callinectes ornatus Shelligs L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Callinectes ornatus Shelligs L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Callinectes sapidus Atlantic Blue Crab Jax Shells.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Callinectes sapidus Atlantic Blue Crab Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Charybdis hellerii Indo-Pacific Swimming Crab Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Charybdis hellerii Indo-Pacific Swimming Crab Joel Wooster Jax Shells.jpg"
                            width="140" height="105" class="red-border"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Callianassa </i>sp.<br>
      Mud Shrimp<br>
      Photo: Sabine Alshuth</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Callinectes ornatus">Callinectes ornatus</a></i><br><br>
        Shelligs<br>
    Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Callinectes sapidus">Callinectes sapidus</a></i><br><br>
        Atlantic Blue Crab<br>
    Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Charybdis hellerii">Charybdis hellerii</a></i><br><br>
        Indo-Pacific Swimming Crab<br>
    Photo: Joel Wooster jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Clibanarius vittatus Thinstripe Hermit T Garcia.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Clibanarius vittatus Thinstripe Hermit T Garcia.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Coenobita clypeatus Caribbean Hermit Crab David Fischer.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Coenobita clypeatus Caribbean Hermit Crab David Fischer.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Coronis excavatrix Sabine Alshuth.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Coronis excavatrix Sabine Alshuth.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Dromidia antillensis Hairy Sponge Crab Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Dromidia antillensis Hairy Sponge Crab Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Clibanarius vittatus">Clibanarius vittatus</a></i><br><br>
      Thinstripe Hermit<br>
      Photo: T Garcia</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Coenobita clypeatus</i><br><br>
        Caribbean Hermit Crab<br>
    Photo: David Fischer</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Coronis excavatrix</i><br><br>
        Mantis Shrimp<br>
    Photo: Sabine Alshuth</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Dromidia antillensis</i><br><br>
        Hairy Sponge Crab<br>
    Photo: Joel Wooster jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Emerita talpoida Atlantic Sand Crab Jax Shells.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Emerita talpoida Atlantic Sand Crab Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Epialtus dilatatus Winged Mime Crab L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Epialtus dilatatus Winged Mime Crab L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Eriphia gonagra Warty Crab Jax Shells.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Eriphia gonagra Warty Crab Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Goniopsis cruentata Mangrove Root Crab 2004 SI.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Goniopsis cruentata Mangrove Root Crab 2004 SI.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Emerita talpoida</i><br><br>
      Atlantic Mole Crab<br>
      Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Epialtus dilatatus</i><br><br>
        Winged Mime Crab<br>
    Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Eriphia gonagra</i><br><br>
        Warty Crab<br>
    Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Goniopsis cruentata</i><br><br>
        Mangrove Root Crab<br>
    Photo: &copy; 2004 Smithsonian Institution</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Heterocrypta granulata_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Lepas anatifera Gooseneck Barnacle L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Lepas anatifera Gooseneck Barnacle L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Libinia dubia Longnose Spider Crab L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Libinia dubia Longnose Spider Crab L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Limulus polyphemus Horseshoe Crab L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Limulus polyphemus Horseshoe Crab L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Heterocrypta granulata</i><br>
        <br>
Smooth Elbow Crab<br>
Photo: Jonathan Vera Caripe<a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lepas anatifera</i><br>
        <br>
Gooseneck Barnacle<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Libinia dubia">Libinia dubia</a></i><br>
        <br>
Longnose Spider Crab<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Limulus polyphemus">Limulus polyphemus</a></i><br>
        <br>
Horseshoe Crab<br>
Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Megabalanus coccopoma Titan Acorn Barnacle L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Megabalanus coccopoma Titan Acorn Barnacle L Holly Sweat.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Menippe mercenaria Florida Stone Crab Adult with Molt L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Menippe mercenaria Florida Stone Crab Adult with Molt L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Mithraculus sculptus Green Clinging Crab L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Mithraculus sculptus Green Clinging Crab L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Ocypode quadrata Atlantic Ghost Crab Maureen McNally.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Ocypode quadrata Atlantic Ghost Crab Maureen McNally.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Megabalanus coccopoma">Megabalanus coccopoma</a></i><br>
        <br>
Titan Acorn Barnacle<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Menippe mercenaria">Menippe mercenaria</a></i><br>
        <br>
Florida Stone Crab <br> Adult with Molt<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Mithraculus sculptus</i><br>
        <br>
Emerald Clinging Crab<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ocypode quadrata</i><br>
        <br>
Atlantic Ghost Crab<br>
Photo: Maureen McNally</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Pagurus longicarpus Long-clawed Hermit Crab L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Pagurus longicarpus Long-clawed Hermit Crab L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Panulirus argus Caribbean Spiny Lobster L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Panulirus argus Caribbean Spiny Lobster L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Penaeus monodon_small.jpg" width="141"
                        height="106" class="red-border"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Persephona mediterranea Mottled Purse Crab Jax Shells.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Persephona mediterranea Mottled Purse Crab Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Pagurus longicarpus">Pagurus longicarpus</a></i><br>
        <br>
Long-clawed Hermit Crab<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Panulirus argus">Panulirus argus</a></i><br>
        <br>
Caribbean Spiny Lobster<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Penaeus monodon</i><br>
        <br>
Giant Tiger Prawn <br>
Photo: Bill &amp; Mark Bell <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Persephona mediterranea</i><br>
        <br>
Mottled Purse Crab<br>
Photo: jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Petrochirus diogenes Giant Hermit Crab L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Petrochirus diogenes Giant Hermit Crab L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Petrolisthes galathinus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Pilumnus sayi Spineback Hairy Crab L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Pilumnus sayi Spineback Hairy Crab L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Pinnixa chaetopterana_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Petrochirus diogenes">Petrochirus diogenes</a></i><br>
        <br>
Giant Hermit Crab<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pertrolisthes galathinus</i><br>
        <br>
Banded Porcelain Crab<br>
Photo: FWC <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pilumnus sayi</i><br>
        <br>
Spineback Hairy Crab<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pinnixa chaetopterana</i><br>
        <br>
Tube Pea Crab <br>
Photo: Crabby Taxonomist<a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Pinnotheres maculatus Squatter Pea Crab Jax Shells.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Pinnotheres maculatus Squatter Pea Crab Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Plagusia depressa Tidal Spray Crab Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Plagusia depressa Tidal Spray Crab Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Porcellana sayana Spotted Porcelain Crab L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Porcellana sayana Spotted Porcelain Crab L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Portunus gibbesii Iridescent Swimming Crab L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Portunus gibbesii Iridescent Swimming Crab L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Pinnotheres maculatus">Pinnotheres maculatus</a></i><br>
        <br>
Squatter Pea Crab<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Plagusia depressa</i><br>
        <br>
Tidal Spray Crab<br>
Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Porcellana sayana</i><br>
        <br>
Spotted Porcelain Crab<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Portunus gibbesii</i><br>
        <br>
Iridescent Swimming Crab<br>
Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Stenopus hispidus Banded Coral Shrimp 2004 SI.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Stenopus hispidus Banded Coral Shrimp 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Stenorhynchus seticornis Yellowline Arrow Crab Jax Shells.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Stenorhynchus seticornis Yellowline Arrow Crab Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Tetraclita stalactifera Ribbed Barnacle L Holly Sweat.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Tetraclita stalactifera Ribbed Barnacle L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Uca pugilator Sand Fiddler Crab R Gomme.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Uca pugilator Sand Fiddler Crab R Gomme.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Stenopus hispidus</i><br>
        <br>
Banded Coral Shrimp<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Stenorhynchus seticornis</i><br>
        <br>
Yellowline Arrow Crab<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tetraclita stalactifera</i><br>
        <br>
Ribbed Barnacle<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Uca pugilator">Uca pugilator</a></i><br>
        <br>
Sand Fiddler Crab<br>
Photo: R Gomme</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Uca rapax Mudflat Fiddler Crab 2004 SI.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Uca rapax Mudflat Fiddler Crab 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Crustaceans/Cropped Images/Uca thayeri Atlantic Mangrove Fiddler Crab Bjorn Tunberg.jpg"><img
                            src="../content/imglib/Crustaceans/Thumbnails/Uca thayeri Atlantic Mangrove Fiddler Crab Bjorn Tunberg.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Uca rapax">Uca rapax</a></i><br>
        <br>
Mudflat Fiddler Crab<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Uca thayeri">Uca thayeri</a></i><br>
        <br>
Atlantic Mangrove Fiddler<br>
Photo: Bjorn Tunberg</span></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td align="right"><a href="#Top">Back to Top</a></td>
        </tr>
        <tr>
            <td><p class="title"><a name="Echinoderms"></a>ECHINODERMS - Urchins, sea cucumbers, sea stars and brittle
                    stars</p></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Amphiodia pulchella John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Amphiodia pulchella John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Amphioplus thrombodes John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Amphioplus thrombodes John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Arbacia punctulata Purple-spined Sea Urchin L Holly Sweat.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Arbacia punctulata Purple-spined Sea Urchin L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Diadema antillarum Long-spined Sea Urchin L Holly Sweat.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Diadema antillarum Long-spined Sea Urchin L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Amphiodia pulchella">Amphiodia pulchella</a></i><br><br>
      Brittle Star<br>
      Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Amphioplus thrombodes">Amphioplus thrombodes</a></i><br><br>
      Brittle Star<br>
      Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Arbacia punctulata">Arbacia punctulata</a></i><br><br>
      Purple-spined Sea Urchin<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Diadema antillarum</i><br><br>
      Long-spined Sea Urchin<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Echinaster sentus Spiny Sea Star John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Echinaster sentus Spiny Sea Star John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Echinaster spinulosus Small-spine Sea Star L Holly Sweat.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Echinaster spinulosus Small-spine Sea Star L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Echinometra lucunter Rock Boring Urchin L Holly Sweat.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Echinometra lucunter Rock Boring Urchin L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Eucidaris tribuloides Slate Pencil Urchin L Holly Sweat.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Eucidaris tribuloides Slate Pencil Urchin L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Echinaster sentus</i><br><br>
      Spiny Sea Star<br>
      Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Echinaster spinulosus</i><br><br>
        Small-spine Sea Star<br>
        Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Echinometra lucunter">Echinometra lucunter</a></i><br><br>
        Rock Boring Urchin<br>
        Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Eucidaris tribuloides</i><br><br>
        Slate Pencil Urchin<br>
        Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Holothuria arenicola Sand Sea Cucumber John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Holothuria arenicola Sand Sea Cucumber John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Holothuria cubana Grub Sea Cucumber John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Holothuria cubana Grub Sea Cucumber John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Holothuria grisea Gray Sea Cucumber L Holly Sweat.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Holothuria grisea Gray Sea Cucumber L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Isostichopus badionotus Chocolate Chip Sea Cucumber John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Isostichopus badionotus Chocolate Chip Sea Cucumber John E Miller.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Holothuria arenicola</i><br><br>
      Sand Sea Cucumber<br>
      Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Holothuria cubana</i><br><br>
        Grub Sea Cucumber<br>
        Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Holothuria grisea">Holothuria grisea</a></i><br><br>
        Gray Sea Cucumber<br>
        Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Isostichopus badionotus</i><br><br>
        Chocolate Chip Sea Cucumber<br>
        Photo: John E Miller</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Leptosynapta roseola John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Leptosynapta roseola John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Leptosynapta tenuis John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Leptosynapta tenuis John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Luidia clathrata Gray Sea Star L Holly Sweat.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Luidia clathrata Gray Sea Star L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Luidia senegalensis Nine-armed Sea Star Joseph Dineen.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Luidia senegalensis Nine-armed Sea Star Joseph Dineen.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Leptosynapta roseola</i><br><br>
      Sea Cucumber<br>
      Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Leptosynapta tenuis</i><br><br>
        Sea Cucumber<br>
        Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Luidia clathrata">Luidia clathrata</a></i><br><br>
        Gray Sea Star<br>
        Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Luidia senegalensis">Luidia senegalensis</a></i><br><br>
        Nine-armed Sea Star<br>
        Photo: Joseph Dineen</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Lytechinus variegatus Green Sea Urchin L Holly Sweat.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Lytechinus variegatus Green Sea Urchin L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Moira atropos Heart Urchin John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Moira atropos Heart Urchin John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Ophiactis rubropoda Gordon Hendler.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Ophiactis rubropoda Gordon Hendler.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Ophiactis savignyi Savigny's Brittle Star John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Ophiactis savignyi Savigny's Brittle Star John E Miller.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Lytechinus variegatus">Lytechinus variegatus</a></i><br><br>
      Green Sea Urchin<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Moira atropos</i><br><br>
        Heart Urchin<br>
        Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ophiactis rubropoda</i><br><br>
        Brittle Star<br>
        Photo: Gordon Hendler</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ophiactis savignyi</i><br><br>
        Savigny's Brittle Star<br>
        Photo: John E Miller</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Ophionereis reticulata Reticulated Brittle Star John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Ophionereis reticulata Reticulated Brittle Star John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Ophiophragmus filograneus John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Ophiophragmus filograneus John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Ophiothrix angulata Angular Brittlestar John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Ophiothrix angulata Angular Brittlestar John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Oreaster reticulatus Cushioned Star John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Oreaster reticulatus Cushioned Star John E Miller.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ophionereis reticulata</i><br><br>
      Reticulated Brittle Star<br>
      Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Ophiophragmus filograneus">Ophiophragmus filograneus</a></i><br><br>
        Brittle Star<br>
        Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Ophiothrix angulata">Ophiothrix angulata</a></i><br><br>
        Angular Brittlestar<br>
        Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Oreaster reticulatus">Oreaster reticulatus</a></i><br><br>
        Cushioned Sea Star<br>
        Photo: John E Miller</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Paracaudina chilensis obesacauda John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Paracaudina chilensis obesacauda John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Synaptula hydriformis John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Synaptula hydriformis John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Thyonella gemmata Striped Sea Cucumber John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Thyonella gemmata Striped Sea Cucumber John E Miller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Echinoderms/Cropped Images/Tripneustes ventricosus West Indian Sea Egg John E Miller.jpg"><img
                            src="../content/imglib/Echinoderms/Thumbnails/Tripneustes ventricosus West Indian Sea Egg John E Miller.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span
                        class="caption"><i>Paracaudina chilensis obesacauda</i><br><br>
      Sea Cucumber<br>
      Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Synaptula hydriformis">Synaptula hydriformis</a></i><br><br>
        Sea Cucumber<br>
        Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Thyonella gemmata">Thyonella gemmata</a></i><br><br>
        Striped Sea Cucumber<br>
        Photo: John E Miller</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tripneustes ventricosus</i><br><br>
        West Indian Sea Egg<br>
        Photo: John E Miller</span></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="1" cellpadding="1">
        <tr>
            <td align="right"><a href="#Top">Back to Top</a></td>
        </tr>
        <tr>
            <td><p class="title"><a name="Fishes"></a>FISHES - Bony fishes, sharks, skates and rays</p></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Abudefduf saxatilis Sergeant Major Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Abudefduf saxatilis Sergeant Major Juvenile L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Acanthostracion polygonius Honeycomb Cowfish Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Acanthostracion polygonius Honeycomb Cowfish Juvenile L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Acanthostracion quadricornis Scrawled Cowfish 2004 SI.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Acanthostracion quadricornis Scrawled Cowfish 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Acanthurus bahianus Ocean Surgeon Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Acanthurus bahianus Ocean Surgeon Juvenile L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Abudefduf saxatilis">Abudefduf saxatilis</a></i><br><br>
      Sergeant Major Juvenile<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Acanthostracion polygonius</i><br><br>
      Honeycomb Cowfish Juvenile<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span
                        class="caption"><i>Acanthostracion quadricornis</i><br><br>
      Scrawled Cowfish<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Acanthurus bahianus</i><br><br>
      Ocean Surgeon Juvenile<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Acanthurus bahianus Ocean Surgeon L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Acanthurus bahianus Ocean Surgeon L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Acanthurus coeruleus Blue Tang L Holly Sweat.jpg"></a><a
                        href="../content/imglib/Fishes/Cropped Images/Parablennius marmoreus Seaweed Blenny L Holly Sweat.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Acanthurus chirurgus_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Achirus lineatus Lined Sole L Holly Sweat.jpg"></a><a
                        href="../content/imglib/Fishes/Cropped Images/Acanthurus coeruleus Blue Tang L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Acanthurus coeruleus Blue Tang L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Aetobatus narinari Spotted Eagle Ray 2004 SI.jpg"></a><a
                        href="../content/imglib/Fishes/Cropped Images/Achirus lineatus Lined Sole L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Achirus lineatus Lined Sole L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Acanthurus bahianus</i><br><br>
      Ocean Surgeon<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Acanthurus chirurgus</i><br>
        <br>
Doctorfish <br>
Photo: Brain Gratwicke <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Acanthurus coeruleus</i><br>
        <br>
Blue Tang<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Achirus lineatus">Achirus lineatus</a></i><br>
        <br>
Lined Sole<br>
Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Aetobatus narinari Spotted Eagle Ray 2004 SI.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Aetobatus narinari Spotted Eagle Ray 2004 SI.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Raja eglanteria Clearnose Skate Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Ahlia egmontis_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Albula vulpes_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Alectis ciliaris_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Ogcocephalus nasutus Shortnose Batfish 2004 SI.jpg"><span
                            class="caption"><i>Aetobatus narinari</i><br>
          <br>
Spotted Eagle Ray<br>
Photo: &copy; 2004 Smithsonian Institution</span></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Oligoplites saurus Leatherjacket Juvenile L Holly Sweat.jpg"></a><span
                        class="caption"><i>Ahlia egmontis</i><br>
        <br>
Key Worm Eel<br>
Photo:Williams et al. 2010<a href="https://creativecommons.org/licenses/by/2.5/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Ophioblennius atlanticus Redlip Blenny L Holly Sweat.jpg"></a><span
                        class="caption"><i>Albula vulpes</i><br>
        <br>
Bonefish <br>
Photo: Brain Gratwicke <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Alectis ciliaris</i><br>
        <br>
African Pompano <br>
Photo:JT Williams <a href="https://creativecommons.org/licenses/by/2.5/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Alosa sapidissima_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Scarus iseri Striped Parrotfish Male L Holly Sweat.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Aluterus scriptus_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Anchoa hepsetus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Anchoa lyolepis_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Poecilia latipinna Sailfin Molly L Holly Sweat.jpg"><span
                            class="caption"><i>Alosa sapidissima</i><br>
          <br>
American Shad <br>
Photo: Don Flescher</span></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Prionotus carolinus Northern Searobin L Holly Sweat.jpg"></a><span
                        class="caption"><i>Aluterus scriptus</i><br>
        <br>
Scrawled Filefish<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Raja eglanteria Clearnose Skate Hatching L Holly Sweat.jpg"></a><span
                        class="caption"><i>Anchoa hepsetus</i><br>
        <br>
Broad-striped Anchovy<br>
Photo: Brandi Noble, NMFS<a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Anchoa lyolepis</i><br>
        <br>
Dusky Anchovy<br>
Photo: Brandi Noble, NMFS<a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Anguilla rostrata_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Selene vomer Lookdown Intermediate L Holly Sweat.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Anisotremus virginicus_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Antennarius pauciradiatus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Apogon maculatus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Rhinoptera bonasus Cownose Ray Ed Perry.jpg"><span
                            class="caption"></a><span class="caption"><i>Anisotremus virginicus</i><br>
        <br>
Porkfish <br>
Photo:zsispeo, Flickr <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Scartella cristata Molly Miller L Holly Sweat.jpg"></a><span
                        class="caption"><i>Anisotremus virginicus</i><br>
        <br>
Porkfish <br>
Photo:zsispeo, Flickr <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Scarus iseri Striped Parrotfish Female L Holly Sweat.jpg"></a><span
                        class="caption"><i>Antennarius pauciradiatus</i><br>
        <br>
Dwarf Frogfish<br>
Photo:JT Williams <a href="https://creativecommons.org/licenses/by/2.5/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Apogon maculatus</i><br>
        <br>
Flamefish <br>
Photo: Smithsonian Belize Larval Fish Group</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Archosargus probatocephalus_small.jpg"
                        width="141" height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Archosargus probatocephalus Sheepshead Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Archosargus probatocephalus Sheepshead Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><span class="caption"><a
                            href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode"></a></span><a
                        href="../content/imglib/Fishes/Cropped Images/Ariopsis felis Hardhead Catfish Sheri Knott.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Ariopsis felis Hardhead Catfish Sheri Knott.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Astronotus ocellatus_small.jpg" width="141"
                        height="106" class="red-border"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Archosargus probatocephalus">Archosargus probatocephalus</a></i><br>
        <br>
Sheepshead <br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Scorpaena grandicornis Plumed Scorpionfish L Holly Sweat.jpg"></a><span
                        class="caption"><i><a href="../taxa/index.php?taxon=Archosargus probatocephalus">Archosargus probatocephalus</a></i><br>
        <br>
Sheepshead Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Scorpaena plumieri Spotted Scorpionfish 2004 SI.jpg"></a><span
                        class="caption"><i><a href="../taxa/index.php?taxon=Ariopsis felis">Ariopsis felis</a></i><br>
        <br>
Hardhead Catfish<br>
Photo: Sheri Knott</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Astronotus ocellatus</i><br>
        <br>
Oscar <br>
Photo:George Chernilevsky</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Bathygobius soporator Frillfin Goby L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Bathygobius soporator Frillfin Goby L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Bodianus pulchellus Spotfin Hogfish L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Bodianus pulchellus Spotfin Hogfish L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Bodianus rufus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Bothus ocellatus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Sparisoma aurofrenatum Redband Parrotfish Juvenile L Holly Sweat.jpg"></a><span
                        class="caption"><i>Bathygobius soporator</i><br>
        <br>
        Frillfin Goby
<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Sparisoma aurofrenatum Redband Parrotfish Juvenile L Holly Sweat.jpg"></a><span
                        class="caption"><i>Bodianus pulchellus</i><br>
        <br>
Spotfin Hogfish<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Sphoeroides maculatus Northern Puffer Juvenile L Holly Sweat.jpg"></a><span
                        class="caption"><i>Bodianus rufus</i><br>
        <br>
Spanish Hogfish Juvenile<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Sphoeroides testudineus Checkered Puffer L Holly Sweat.jpg"></a><span
                        class="caption"><i>Bothus ocellatus</i><br>
        <br>
Eyed Flounder<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Brevoortia tyrannus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Tylosurus crocodilus Houndfish 2004 SI.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Bryx dunckeri_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Carangoides bartholomaei_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Caranx crysos_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Brevoortia tyrannus</i><br>
        <br>
Atlantic Menhaden<br>
Photo: Brain Gratwicke <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Bryx dunckeri</i><br>
        <br>
Pugnose Pipefish <br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Carangoides bartholomaei</i><br>
        <br>
Yellow Jack Juvenile<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Caranx crysos</i><br>
        <br>
Blue Runner <br>
Photo: Brandi Noble, NMFS<a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Caranx hippos_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Caranx latus_small.jpg" width="141" height="106">
            </td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Carangoides ruber Bar Jack 2004 SI.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Carangoides ruber Bar Jack 2004 SI.jpg" alt=""
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Caranx ruber_small.jpg" width="141" height="106">
            </td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Caranx hippos</i><br>
        <br>
Crevalle Jack <br>
Photo: Kevin Lawver <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Syngnathus scovelli Gulf Pipefish L Holly Sweat.jpg"></a><span
                        class="caption"><i>Caranx latus</i><br>
        <br>
Horse-eye Jack <br>
Photo: Brain Gratwicke <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Trachinotus falcatus Permit Juvenile L Holly Sweat.jpg"></a><span
                        class="caption"><i>Caranx ruber</i><br>
        <br>
Bar Jack<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Caranx ruber</i><br>
        <br>
Bar Jack <br>
Photo:JT Williams <a href="https://creativecommons.org/licenses/by/2.5/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Carcharhinus limbatus Blacktip Shark 2004 Laurie Penland.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Carcharhinus limbatus Blacktip Shark 2004 Laurie Penland.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Centropomus parallelus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Centropomus undecimalis Common Snook Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Centropomus undecimalis Common Snook Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Centropristis striata_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Carcharhinus limbatus</i><br>
        <br>
Blacktip Shark<br>
Photo: &copy; 2004 Laurie Penland</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Centropomus parallelus</i><br>
        <br>
Fat Snook<br>
Photo: Carla Isobel Elliff <a href="https://creativecommons.org/licenses/by/3.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Centropomus undecimalis">Centropomus undecimalis</a></i><br>
        <br>
Common Snook Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Centropristis striata</i><br>
        <br>
Black Sea Bass <br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Chaetodipterus faber_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Chaetodipterus faber Atlantic Spadefish Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Chaetodipterus faber Atlantic Spadefish Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Chaetodon capistratus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Chaetodon ocellatus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Chaetodipterus faber</i><br>
        <br>
Atlantic Spadefish<br>
Photo:Matthew Hoelscher<a href="https://creativecommons.org/licenses/by-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Chaetodipterus faber</i><br>
        <br>
Atlantic Spadefish Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Chaetodon capistratus</i><br>
        <br>
Foureye Butterflyfish<br>
Photo: Brain Gratwicke <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Chaetodon ocellatus</i><br>
        <br>
Spotfin Butterflyfish<br>
Photo: Brain Gratwicke <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Chasmodes saburrae Florida Blenny L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Chasmodes saburrae Florida Blenny L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Chloroscombrus chrysurus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Citharichthys spilopterus Bay Whiff L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Citharichthys spilopterus Bay Whiff L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Coryphaena hippurus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Chasmodes saburrae">Chasmodes saburrae</a></i><br>
        <br>
Florida Blenny<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Chloroscombrus chrysurus</i><br>
        <br>
Atlantic Bumper<br>
Photo: Brandi Noble, NMFS</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Citharichthys spilopterus">Citharichthys spilopterus</a></i><br>
        <br>
Bay Whiff<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Coryphaena hippurus</i><br>
        <br>
Dolphinfish <br>
Photo: Pablo Cavallari <a href="https://creativecommons.org/licenses/by/3.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Coryphopterus glaucofraenum Bridled Goby L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Coryphopterus glaucofraenum Bridled Goby L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Cryptotomus roseus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Ctenopharyngodon idella_small.jpg" width="141"
                        height="106" class="red-border"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Cynoscion nebulosus Spotted Seatrout Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Cynoscion nebulosus Spotted Seatrout Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Coryphopterus glaucofraenum</i><br>
        <br>
Bridled Goby<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cryptotomus roseus</i><br>
        <br>
Bluelip Parrotfish<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ctenopharyngodon idella</i><br>
        <br>
Grass Carp<br>
Photo:Nassar Halaweh <a href="https://creativecommons.org/licenses/by-nc/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cynoscion nebulosus</i><br>
        <br>
Spotted Seatrout Juvenile<br>
Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Dactyloscopus tridigitatus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Dasyatis americana Southern Stingray Marc Virgilio.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Dasyatis americana Southern Stingray Marc Virgilio.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Doratonotus megalepis Dwarf Wrasse L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Doratonotus megalepis Dwarf Wrasse L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><span class="caption"><a
                            href="https://creativecommons.org/licenses/by/3.0/legalcode"></a></span><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Dorosoma cepedianum_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Dactyloscopus tridigitatus</i><br>
        <br>
Sand Stargazer <br>
Photo:Williams et al. 2010<a href="https://creativecommons.org/licenses/by/2.5/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Dasyatis americana</i><br>
        <br>
Southern Stingray<br>
Photo: Marc Virgilio</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Doratonotus megalepis</i><br>
        <br>
Dwarf Wrasse<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Dorosoma cepedianum</i><br>
        <br>
Gizzard Shad <br>
Photo: Brain Gratwicke <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Dorosoma petenense_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Echeneis naucrates_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Elops saurus_small.jpg" width="141" height="106">
            </td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Enneacanthus gloriosus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Dorosoma pertenese</i><br>
        <br>
Threadfin Shad<br>
Photo: Bill Stagnaro <a href="https://creativecommons.org/licenses/by-sa/3.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Echeneis naucrates</i><br>
        <br>
Sharksucker <br>
Photo:Wusel007,Wikimedia<a href="https://creativecommons.org/licenses/by-sa/3.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Elops saurus</i><br>
        <br>
Ladyfish <br>
Photo: Mike Cline <a href="https://creativecommons.org/licenses/by-sa/4.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Enneacanthus gloriosus</i><br>
        <br>
Bluespotted Sunfish<br>
Photo: Brain Gratwicke <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Epinephelus itajara Goliath Grouper Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Epinephelus itajara Goliath Grouper Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Erimyzon sucetta_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Evorthodus lyricus Lyre Goby L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Evorthodus lyricus Lyre Goby L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Fistularia tabacaria Bluespotted Cornetfish L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Fistularia tabacaria Bluespotted Cornetfish L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Epinephelus itajara</i><br>
        <br>
Goliath Grouper Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Erimyzon sucetta</i><br>
        <br>
Lake Chubsucker <br>
Photo: Howard Jelks</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Evorthodus lyricus</i><br>
        <br>
Lyre Goby<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Fistularia tabacaria</i><br>
        <br>
Bluespotted Cornetfish<br>
Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Floridichthys carpio Goldspotted Killifish L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Floridichthys carpio Goldspotted Killifish L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Fundulus chrysotus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Fundulus heteroclitus heteroclitus_small.jpg"
                        width="141" height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Fundulus similis Longnose Killifish L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Fundulus similis Longnose Killifish L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Floridichthys carpio">Floridichthys carpio</a></i><br>
        <br>
Goldspotted Killifish<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Fundulus chrysotus</i><br>
        <br>
Golden Topminnow<br>
Photo: Matthew Pintar <a href="https://creativecommons.org/licenses/by-sa/4.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span
                        class="caption"><i>Fundulus heteroclitus heteroclitus</i><br>
        <br>
Mummichog <br>
Photo: Brain Gratwicke <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Fundulus similis</i><br>
        <br>
Longnose Killifish<br>
Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Gambusia affinis Mosquitofish L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Gambusia affinis Mosquitofish L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Gerres cinereus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Ginglymostoma cirratum Nurse Shark Marc Virgilio.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Ginglymostoma cirratum Nurse Shark Marc Virgilio.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Gnatholepis thompsoni Goldspot Goby L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Gnatholepis thompsoni Goldspot Goby L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Gambusia affinis">Gambusia affinis</a></i><br>
        <br>
Mosquitofish<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Gerres cinereus</i><br>
        <br>
Yellowfin Mojarra<br>
Photo:P Asman &amp; J Lenoble<a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ginglymostoma cirratum</i><br>
        <br>
Nurse Shark<br>
Photo: Marc Virgilio</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Gnatholepis thompsoni</i><br>
        <br>
Goldspot Goby<br>
Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Gnatholepis thompsoni_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Gobiosoma bosc_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Gobiesox strumosus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Gobioides broussonnetii_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Gnatholepis thompsoni</i><br>
        <br>
Goldspot Goby<br>
Photo:Williams et al. 2010<a href="https://creativecommons.org/licenses/by/2.5/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Gobiosoma bosc</i><br>
        <br>
Naked Goby<br>
Photo: Niclan7, Wikimedia <a href="https://creativecommons.org/licenses/by-sa/3.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Gobiesox strumosus</i><br>
        <br>
Skilletfish <br>
Photo: Niclan7, Wikimedia <a href="https://creativecommons.org/licenses/by-sa/3.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Gobioides broussonnetii</i><br>
        <br>
Violet Goby<br>
Photo: Kitty Kat Katarina, Wikimedia <a
                            href="https://creativecommons.org/licenses/by-sa/4.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Gobionellus oceanicus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Gymnothorax funebris_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Gymnothorax moringa Spotted Moray 2004 SI.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Gymnothorax moringa Spotted Moray 2004 SI.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Gymnothorax vicinus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Gobionellus oceanicus</i><br>
        <br>
Highfin Goby<br>
Photo: Brett Albanese <a href="https://creativecommons.org/licenses/by-nc-nd/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Gymnothorax funebris</i><br>
        <br>
Green Moray <br>
Photo: Laszio Ilyes <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Gymnothorax moringa</i><br>
        <br>
Spotted Moray<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Gymnothorax vicinus</i><br>
        <br>
Purplemouth Moray <br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Haemulon aurolineatum_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Haemulon chrysargyreum_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Haemulon flavolineatum French Grunt 2004 SI.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Haemulon flavolineatum French Grunt 2004 SI.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Haemulon melanurum_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Haemulon aurolineatum</i><br>
        <br>
Tomtate <br>
Photo: Brain Gratwicke <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Haemulon chrysargyreum</i><br>
        <br>
        Smallmouth Grunt
<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Haemulon flavolineatum</i><br>
        <br>
French Grunt<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Haemulon melanurum</i><br>
        <br>
Cottonwick <br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Haemulon plumierii White Grunt Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Haemulon plumierii White Grunt Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Haemulon sciurus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Halichoeres bivittatus Slippery Dick Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Halichoeres bivittatus Slippery Dick Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Halichoeres maculipinna_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Haemulon plumierii</i><br>
        <br>
White Grunt Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Haemulon sciurus</i><br>
        <br>
Bluestriped Grunt <br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Halichoeres bivittatus</i><br>
        <br>
Slippery Dick Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Halichoeres maculipinna</i><br>
        <br>
Clown Wrasse<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Halichoeres poeyi_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Halichoeres radiatus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Harengula jaguana Scaled Sardine L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Harengula jaguana Scaled Sardine L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Hemichromis bimaculatus_small.jpg" width="141"
                        height="106" class="red-border"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Halichoeres poeyi</i><br>
        <br>
Blackear Wrasse Juvenile<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Halichoeres radiatus</i><br>
        <br>
Puddingwife <br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Harengula jaguana</i><br>
        <br>
Scaled Sardine<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Hemichromis bimaculatus</i><br>
        <br>
Jewel Cichlid <br>
Photo: Zhyla <a href="https://creativecommons.org/licenses/by-sa/3.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Hemichromis letourneuxi_small.jpg" width="141"
                        height="106" class="red-border"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Herichthys cyanoguttatum_small.jpg" width="141"
                        height="106" class="red-border"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Heterandria formosa_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Hippocampus erectus Lined Seahorse L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Hippocampus erectus Lined Seahorse L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Hemichromis letourneuxi</i><br>
        <br>
Least Killifish<br>
Photo: Noel Burkhead <a href="https://creativecommons.org/licenses/by-sa/2.5/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Herichthys cyanoguttatum</i><br>
        <br>
Rio Grande Cichlid<br>
Photo: Brian Gratwicke<a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Heterandria formosa</i><br>
        <br>
Least Killifish<br>
Photo: Brian Gratwicke<a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Hippocampus erectus">Hippocampus erectus</a></i><br>
        <br>
Lined Seahorse<br>
Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Hippocampus reidi Longsnout Seahorse 2004 SI.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Hippocampus reidi Longsnout Seahorse 2004 SI.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Hippocampus zosterae_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Histrio histrio_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Holacanthus bermudensis_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Hippocampus reidi</i><br>
        <br>
Longsnout Seahorse<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Hippocampus zosterae</i><br>
        <br>
Dwarf Seahorse<br>
Photo: Will Thomas <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Histrio histrio</i><br>
        <br>
Sargassumfish<br>
Photo: Sean Nash <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Holacanthus bermudensis</i><br>
        <br>
Blue Angelfish <br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Holacanthus bermudensis Blue Angelfish Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Holacanthus bermudensis Blue Angelfish Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Holacanthus ciliaris Queen Angelfish L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Holacanthus ciliaris Queen Angelfish L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Holacanthus tricolor Rock Beauty L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Holacanthus tricolor Rock Beauty L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Hypleurochilus bermudensis_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Holacanthus bermudensis</i><br>
        <br>
Blue Angelfish Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Holacanthus ciliaris</i><br>
        <br>
Queen Angelfish<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Holacanthus tricolor</i><br>
        <br>
Rock Beauty<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Hypleurochillus bermudensis</i><br>
        <br>
Barred Blenny<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Hyporhamphus meeki_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Hyporhamphus unifasciatus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Hypostomus plecostomus Suckermouth Catfish Marc Virgilio.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Hypostomus plecostomus Suckermouth Catfish Marc Virgilio.jpg"
                            alt="" width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Labrisomus nuchipinnis_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Hyporhamphus meeki</i><br>
        <br>
Atlantic Halfbeak <br>
Photo: Brandi Noble, NMFS <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Hyporhamphus unifasciatus</i><br>
        <br>
Common Halfbeak <br>
Photo: Brandi Noble, NMFS</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Hypostomus plecostomus</i><br>
        <br>
Suckermouth Catfish<br>
Photo: Marc Virgilio</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Labrisomus nuchipinnis</i><br>
        <br>
Hairy Blenny<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Lachnolaimus maximus Hogfish Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Lachnolaimus maximus Hogfish Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Lactophrys trigonus Trunkfish Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Lactophrys trigonus Trunkfish Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Lagodon rhomboides Pinfish Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Lagodon rhomboides Pinfish Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Lepisosteus osseus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lachnolaimus maximus</i><br>
        <br>
Hogfish Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lactophrys trigonus</i><br>
        <br>
Trunkfish Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Lagodon rhomboides">Lagodon rhomboides</a></i><br>
        <br>
Pinfish Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lepisosteus osseus</i><br>
        <br>
Longnose Gar <br>
Photo: Mat1583 <a href="https://creativecommons.org/licenses/by-sa/4.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Lepisosteus platyrhincus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Lepomis gulosus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Lepomis macrochirus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Lepomis marginatus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lepisosteus platyrhincus</i><br>
        <br>
Florida Gar <br>
Photo:Raimond Spekking <a href="https://creativecommons.org/licenses/by-sa/4.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lepomis gulosus</i><br>
        <br>
Warmouth <br>
Photo: Andrew Hoffman <a href="https://creativecommons.org/licenses/by-nc-nd/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lepomis macrochirus</i><br>
        <br>
Bluegill <br>
Photo: Eric Engbretson</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lepomis marginatus</i><br>
        <br>
Dollar Sunfish<br>
Photo: Howard Jelks <a href="https://creativecommons.org/licenses/by-sa/2.5/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Lepomis microlophus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Lepomis punctatus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Lucania goodei_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Lutjanus apodus Schoolmaster 2004 SI.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Lutjanus apodus Schoolmaster 2004 SI.jpg" alt=""
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lepomis microlophus</i><br>
        <br>
Redear Sunfish<br>
Photo: Trisha Sears <a href="https://creativecommons.org/licenses/by-sa/3.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lepomis punctatus</i><br>
        <br>
Spotted Sunfish<br>
Photo: Phil's 1stPics, Flickr <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span>
            </td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lucania goodei</i><br>
        <br>
Bluefin Killifish<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lutjanus apodus</i><br>
        <br>
Schoolmaster<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Lutjanus cyanopterus Cubera Snapper 2004 SI.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Lutjanus cyanopterus Cubera Snapper 2004 SI.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Lutjanus griseus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Lutjanus griseus Gray Snapper Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Lutjanus griseus Gray Snapper Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Lutjanus mahogoni_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lutjanus cyanopterus</i><br>
        <br>
Cubera Snapper<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Lutjanus griseus">Lutjanus griseus</a></i><br>
        <br>
Mangrove Snapper <br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Lutjanus griseus">Lutjanus griseus</a></i><br>
        <br>
Mangrove Snapper Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lutjanus mahogoni</i><br>
        <br>
Mahogany Snapper <br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Lutjanus synagris Lane Snapper J Metz.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Lutjanus synagris Lane Snapper J Metz.jpg" alt=""
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Malacoctenus macropus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Malacoctenus triangulatus Saddle Blenny L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Malacoctenus triangulatus Saddle Blenny L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Megalops atlanticus Tarpon 2004 SI.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Megalops atlanticus Tarpon 2004 SI.jpg" alt=""
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Lutjanus synagris">Lutjanus synagris</a></i><br>
        <br>
Lane Snapper<br>
Photo: J Metz</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Malacoctenus macropus</i><br>
        <br>
Rosy Blenny<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Malacoctenus triangulatus</i><br>
        <br>
Saddle Blenny<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Megalops atlanticus">Megalops atlanticus</a></i><br>
        <br>
Tarpon<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Megalops atlanticus Tarpon Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Megalops atlanticus Tarpon Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Microgobius gulosus Clown Goby L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Microgobius gulosus Clown Goby L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Microgobius microlepis_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Micropterus salmoides_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Megalops atlanticus">Megalops atlanticus</a></i><br>
        <br>
Tarpon Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Microgobius gulosus</i><br>
        <br>
Clown Goby<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Microgobius microlepis</i><br>
        <br>
Banner Blenny<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Micropterus salmoides</i><br>
        <br>
Largemouth Bass <br>
Photo: Robert Pos <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Mugil cephalus Striped Mullet H Chramostova.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Mugil cephalus Striped Mullet H Chramostova.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Mugil cephalus Striped Mullet Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Mugil cephalus Striped Mullet Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Mycteroperca bonaci Black Grouper Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Mycteroperca bonaci Black Grouper Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Myrichthys breviceps_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Mugil cephalus">Mugil cephalus</a></i><br>
        <br>
Striped Mullet<br>
Photo: Pat Poston</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Mugil cephalus">Mugil cephalus</a></i><br>
        <br>
Striped Mullet Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Mycteroperca bonaci">Mycteroperca bonaci</a></i><br>
        <br>
Black Grouper Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Myrichthys ocellatus</i><br>
        <br>
Sharptail Eel<br>
Photo: Cam5455, Wikimedia</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Myrichthys ocellatus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Nes longus_small.jpg" width="141" height="106">
            </td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Notemigonus crysoleucas_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Notropis maculatus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Myrichthys ocellatus</i><br>
        <br>
Goldspotted Eel<br>
Photo: FM M&uuml;ller <a href="https://creativecommons.org/licenses/by-sa/3.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Nes longus</i><br>
        <br>
        Orangespotted Goby
<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Notemigonus crysoleucas</i><br>
        <br>
Golden Shiner<br>
Photo: HowardJelks <a href="https://creativecommons.org/licenses/by-sa/2.5/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Notropis maculatus</i><br>
        <br>
Taillight Shiner<br>
Photo:Noel Burkhead &amp; HowardJelks <a
                            href="https://creativecommons.org/licenses/by-sa/2.5/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Notropis petersoni_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Ogcocephalus cf cubifrons Polka-dot Batfish L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Ogcocephalus cf cubifrons Polka-dot Batfish L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Ogcocephalus nasutus Shortnose Batfish 2004 SI.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Ogcocephalus nasutus Shortnose Batfish 2004 SI.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Oligoplites saurus Leatherjacket Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Oligoplites saurus Leatherjacket Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Notropis petersoni</i><br>
        <br>
Coastal Shiner<br>
Photo:Noel Burkhead &amp; HowardJelks <a
                            href="https://creativecommons.org/licenses/by-sa/2.5/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ogcocephalus cubifrons</i><br>
        <br>
Polka-dot Batfish<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ogcocephalus nasutus</i><br>
        <br>
Shortnose Batfish<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Oligoplites saurus</i><br>
        <br>
Leatherjacket Juvenile<br>
Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Ophioblennius atlanticus Redlip Blenny L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Ophioblennius atlanticus Redlip Blenny L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Opisthonema oglinum_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Opsanus tau_small.jpg" width="141" height="106">
            </td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Oreochromis aureus_small.jpg" width="141"
                        height="106" class="red-border"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ophioblennius atlanticus</i><br>
        <br>
Redlip Blenny<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Opisthonema oglinum</i><br>
        <br>
Atlantic Thread Herring<br>
Photo:Brandi Noble, NMFS<a href="https://creativecommons.org/licenses/by/2.0/legalcode"></a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Opsanus tau</i><br>
        <br>
        Oyster Toadfish
<br>
Photo:Noel Weathers <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Oreochromis aureus</i><br>
        <br>
Blue Tilapia <br>
Photo:Michael R. Hayes <a href="https://creativecommons.org/licenses/by-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Oreochromis mossambicus_small.jpg" width="141"
                        height="106" class="red-border"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Parablennius marmoreus Seaweed Blenny L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Parablennius marmoreus Seaweed Blenny L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Pareques acuminatus_juvenile_small.jpg"
                        width="141" height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Phaeoptyx conklini_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Oreochromis mossambicus</i><br>
        <br>
        Mozambique Tilapia
<br>
Photo:Greg Hume <a href="https://creativecommons.org/licenses/by-sa/3.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Parablennius marmoreus">Parablennius marmoreus</a></i><br>
        <br>
Seaweed Blenny<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pareques acuminatus</i><br>
        <br>
High-hat Juvenile<br>
Photo:Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Phaeoptyx conklini</i><br>
        <br>
        Freckled Cardinalfish
<br>
Photo:JT Williams <a href="https://creativecommons.org/licenses/by/2.5/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Platygillellus rubrocinctus_small.jpg"
                        width="141" height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Poecilia latipinna Sailfin Molly L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Poecilia latipinna Sailfin Molly L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Poecilia reticulata_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Pomacanthus arcuatus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Platygillellus rubrocinctus</i><br>
        <br>
Saddle Stargazer<br>
Photo:Williams et al. 2010<a href="https://creativecommons.org/licenses/by/2.5/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Poecilia latipinna">Poecilia latipinna</a></i><br>
        <br>
Sailfin Molly<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Poecilia reticulata</i><br>
        <br>
        Guppy
<br>
Photo: Deacon et al. 2015 <a href="https://creativecommons.org/licenses/by/4.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pomacanthus arcuatus</i><br>
        <br>
Gray Angelfish<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Pomacanthus paru_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Prionotus carolinus Northern Searobin L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Prionotus carolinus Northern Searobin L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Prionotus scitulus_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Pterois volitans_small.jpg" width="141"
                        height="106" class="red-border"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pomacanthus paru</i><br>
        <br>
French Angelfish<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Prionotus carolinus</i><br>
        <br>
Northern Searobin<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Prionotus scitulus</i><br>
        <br>
        Leopard Searobin
<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pterois volitans</i><br>
        <br>
        Red Lionfish
<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Raja eglanteria Clearnose Skate Hatching L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Raja eglanteria Clearnose Skate Hatching L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Raja eglanteria Clearnose Skate Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Raja eglanteria Clearnose Skate Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Rhinoptera bonasus Cownose Ray Ed Perry.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Rhinoptera bonasus Cownose Ray Ed Perry.jpg" alt=""
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Sardinella aurita_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Raja eglanteria</i><br>
        <br>
Clearnose Skate Hatching<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Raja eglanteria</i><br>
        <br>
Clearnose Skate Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Rhinoptera bonasus">Rhinoptera bonasus</a></i><br>
        <br>
Cownose Ray<br>
Photo: Ed Perry</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sardinella aurita</i><br>
        <br>
        Spanish Sardine
<br>
Photo: Brandi Noble, NMFS <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Sarotherodon melanotheron_small.jpg" width="141"
                        height="106" class="red-border"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Scartella cristata Molly Miller L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Scartella cristata Molly Miller L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Scarus iseri Striped Parrotfish Female L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Scarus iseri Striped Parrotfish Female L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Scarus iseri Striped Parrotfish Male L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Scarus iseri Striped Parrotfish Male L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sarotherodon melanotheron</i><br>
        <br>
        Blackchin Tilapia
<br>
Photo: Noel Burkhead <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Scartella cristata</i><br>
        <br>
Molly Miller<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Scarus iseri</i><br>
        <br>
Striped Parrotfish Female<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Scarus iseri</i><br>
        <br>
Striped Parrotfish Male<br>
Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Scarus taeniopterus Princess Parrotfish Male L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Scarus taeniopterus Princess Parrotfish Male L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Scorpaena grandicornis Plumed Scorpionfish L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Scorpaena grandicornis Plumed Scorpionfish L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Scorpaena plumieri Spotted Scorpionfish 2004 SI.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Scorpaena plumieri Spotted Scorpionfish 2004 SI.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Selar crumenophthalmus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Scarus taeniopterus</i><br>
        <br>
Pricess Parrotfish Male<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Scorpaena grandicornis</i><br>
        <br>
Plumed Scorpionfish<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Scorpaena plumieri</i><br>
        <br>
Spotted Scorpionfish<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Selar crumenophthalmus</i><br>
        <br>
Bigeye Scad<br>
Photo: Brandi Noble, NMFS <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Selene setapinnis_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Selene vomer Lookdown Intermediate L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Selene vomer Lookdown Intermediate L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Selene vomer Lookdown Juvenile B Witherington.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Selene vomer Lookdown Juvenile B Witherington.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Serranus baldwini_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Selene setapinnis<br>
    <br>
    </i>Moonfish<br>
Photo: Brandi Noble, NMFS</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Selene vomer</i><br>
        <br>
Lookdown <br>Intermediate Phase<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Selene vomer</i><br>
        <br>
Lookdown Juvenile<br>
Photo: B Witherington</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Serranus baldwini</i><br>
        <br>
        Lantern Bass
<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Sparisoma aurofrenatum Redband Parrotfish Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Sparisoma aurofrenatum Redband Parrotfish Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Sphoeroides maculatus Northern Puffer Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Sphoeroides maculatus Northern Puffer Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Sphoeroides spengleri_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Sphoeroides testudineus Checkered Puffer L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Sphoeroides testudineus Checkered Puffer L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sparisoma aurofrenatum</i><br>
        <br>
Redband Parrotfish Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sphoeroides maculatus</i><br>
        <br>
Northern Puffer Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sphoeroides spengleri</i><br>
        <br>
Bandtail Puffer <br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Sphoeroides testudineus">Sphoeroides testudineus</a></i><br>
        <br>
Checkered Puffer<br>
Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Sphyraena barracuda Great Barracuda L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Sphyraena barracuda Great Barracuda L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Sphyraena barracuda Great Barracuda Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Sphyraena barracuda Great Barracuda Juvenile L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Stegastes variabilis_small.jpg" width="141"
                        height="106"></td>
            <td align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Stephanolepis hispidus_small.jpg" width="141"
                        height="106"></td>
        </tr>
        <tr>
            <td align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Sphyraena barracuda">Sphyraena barracuda</a></i><br>
        <br>
Great Barracuda<br>
Photo: L Holly Sweat</span></td>
            <td align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Sphyraena barracuda">Sphyraena barracuda</a></i><br>
        <br>
Great Barracuda Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td align="center" valign="top"><span class="caption"><i>Stegastes variabilis</i><br>
        <br>
Cocoa Damselfish<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td align="center" valign="top"><span class="caption"><i>Stephanolepis hispidus</i><br>
        <br>
Planehead Filefish<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Strongylura marina_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Strongylura notata Redfin Needlefish Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Strongylura notata Redfin Needlefish Juvenile L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Symphurus plagiusa Blackcheek Tonguefish L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Symphurus plagiusa Blackcheek Tonguefish L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Syngnathus louisianae Chain Pipefish L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Syngnathus louisianae Chain Pipefish L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Strongylura marina</i><br>
        <br>
Atlantic Needlefish<br>
Photo: Brandi Noble, NMFS <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Strongylura notata">Strongylura notata</a></i><br>
        <br>
Redfin Needlefish Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Symphurus plagiusa</i><br>
        <br>
Blackcheek Tonguefish<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Syngnathus louisianae">Syngnathus louisianae</a></i><br>
        <br>
Chain Pipefish<br>
Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Syngnathus scovelli Gulf Pipefish L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Syngnathus scovelli Gulf Pipefish L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Synodus foetens_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Thalassoma bifasciatum_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Tilapia mariae_small.jpg" width="141"
                        height="106" class="red-border"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Syngnathus scovelli">Syngnathus scovelli</a></i><br>
        <br>
Gulf Pipefish<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Synodus foetens</i><br>
        <br>
        Inshore Lizardfish
<br>
Photo: Brandi Noble, NMFS <a href="https://creativecommons.org/licenses/by/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Thalassoma bifasciatum</i><br>
        <br>
        Bluehead Wrasse
<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tilapia mariae</i><br>
        <br>
Spotted Tilapia<br>
Photo: Andrew Miller <a href="https://creativecommons.org/licenses/by-sa/2.5/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Trachinotus falcatus Permit Juvenile L Holly Sweat.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Trachinotus falcatus Permit Juvenile L Holly Sweat.jpg"
                            alt="" width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Fishes/Cropped Images/Tylosurus crocodilus Houndfish 2004 SI.jpg"><img
                            src="../content/imglib/Fishes/Thumbnails/Tylosurus crocodilus Houndfish 2004 SI.jpg" alt=""
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Ulaema lefroyi_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Xiphophorus hellerii_small.jpg" width="141"
                        height="106" class="red-border"></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Trachinotus falcatus">Trachinotus falcatus</a></i><br>
        <br>
Permit Juvenile<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tylosurus crocodilus</i><br>
        <br>
Houndfish<br>
Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ulaema lefroyi</i><br>
        <br>
        Mottled Mojarra
<br>
Photo: Kevin Bryant <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Xiphophorus hellerii</i><br>
        <br>
        Green Swordtail
<br>
Photo: Andre Karwath <a href="https://creativecommons.org/licenses/by-sa/2.5/legalcode">&copy;</a></span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Xiphophorus maculatus_small.jpg" width="141"
                        height="106" class="red-border"></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Xiphophorus variatus_small.jpg" width="141"
                        height="106" class="red-border"></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Xiphophorus maculatus</i><br>
        <br>
Southern Platyfish<br>
Photo: vxixiv, Flickr <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Xiphophorus variatus</i><br>
        <br>
Variable Platyfish<br>
Photo: Dorenwolf, Flickr <a href="https://creativecommons.org/licenses/by-nc-sa/2.0/legalcode">&copy;</a></span></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="0" cellpadding="11">
        <tr>
            <td align="right"><a href="#Top">Back to Top</a></td>
        </tr>
        <tr>
            <td><p class="title"><a name="Gastropods"></a>GASTROPOD MOLLUSKS - Snails, chitons, limpets, sea slugs and
                    kin</p></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Acteocina canaliculata Channeled Barrel-bubble Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Acteocina canaliculata Channeled Barrel-bubble Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Acteocina atrata.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Acteocina atrata_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Acteon candens Rehder's Baby-bubble Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Acteon candens Rehder's Baby-bubble Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Alaba incerta.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Alaba incerta_small.jpg" width="141"
                            height="106"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Acteocina canaliculata</i><br><br>
      Channeled Barrel-bubble<br>
      Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Acteocina atrata</i><br>
        <br>
        Blackback Barrel-bubble
<br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Acteon candens</i><br>
        <br>
Rehder's Baby-bubble<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Alaba incerta</i><br>
        <br>
Varicose Cerith<br>
Photo: Paula Mikkelson, HBOI</span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Aphelodoris antillensis_small.jpg" width="141"
                        height="106"></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Aplysia brasiliana Sooty Seahare L Holly Sweat.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Aplysia brasiliana Sooty Seahare L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Assiminea succinea Atlantic Assiminea Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Assiminea succinea Atlantic Assiminea Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Astyris lunata Lunar Dovesnail SERTC.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Astyris lunata Lunar Dovesnail SERTC.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td align="center" valign="top"><span class="caption"><i>Aphelodoris antillensis</i><br>
        <br>
        Dorid Nudibranch
<br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td align="center" valign="top"><span class="caption"><i>Aplysia brasiliana</i><br>
        <br>
Sooty Seahare<br>
Photo: L Holly Sweat</span></td>
            <td align="center" valign="top"><span class="caption"><i>Assiminea succinea</i><br>
        <br>
Atlantic Assiminea<br>
Photo: jaxshells.com</span></td>
            <td align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Astyris lunata">Astyris lunata</a></i><br>
        <br>
Lunar Dovesnail<br>
Photo: SERTC</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Batillaria minima West Indian False Cerith Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Batillaria minima West Indian False Cerith Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Berghia stephanieae Berghia Nudibranch L Holly Sweat.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Berghia stephanieae Berghia Nudibranch L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Bittiolum varium Grass Cerith Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Bittiolum varium Grass Cerith Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Blauneria heteroclita Left-hand Melampus Jenny Raven Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Blauneria heteroclita Left-hand Melampus Jenny Raven Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Batillaria minima">Batillaria minima</a></i><br>
        <br>
West Indian False Cerith<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Berghia stephanieae</i><br>
        <br>
Berghia Nudibranch<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Bittiolum varium">Bittiolum varium</a></i><br>
        <br>
Grass Cerith<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Blauneria heteroclita</i><br>
        <br>
Left-hand Melampus<br>
Photo: Jenny Raven jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Bostrycapulus aculeatus Common Spiny Slipper Snail Rachel Collin.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Bostrycapulus aculeatus Common Spiny Slipper Snail Rachel Collin.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Bursatella leachii plei Ragged Seahare L Holly Sweat.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Bursatella leachii plei Ragged Seahare L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Busycotypus spiratus.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Busycotypus spiratus_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Caecum cooperi.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Caecum cooperi_small.jpg" width="141"
                            height="106"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Bostrycapulus aculeatus</i><br>
        <br>
Common Spiny Slipper Snail<br>
Photo: Rachel Collin</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Bursatella leachii plei">Bursatella leachii plei</a></i><br>
        <br>
Ragged Seahare<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Busycon spiratus</i><br>
        <br>
        Pearwhelk
<br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Caecum cooperi</i><br>
        <br>
        Cooper's Atlantic Caecum
<br>
Photo: Paula Mikkelson, HBOI</span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Caecum pulchellum.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Caecum pulchellum_small.jpg" width="141"
                            height="106"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Calyptraea centralis Circular Chinese Hat Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Calyptraea centralis Circular Chinese Hat Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Cancellaria reticulata Common Nutmeg Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Cancellaria reticulata Common Nutmeg Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Cariopsilla pharpa.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Cariopsilla pharpa_small.jpg" width="141"
                            height="106"></a></td>
        </tr>
        <tr>
            <td align="center" valign="top"><span class="caption"><i>Caecum pulchellum</i><br>
        <br>
Beautiful Caecum <br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td align="center" valign="top"><span class="caption"><i>Calyptraea centralis</i><br>
        <br>
Circular Chinese Hat<br>
Photo: jaxshells.com</span></td>
            <td align="center" valign="top"><span class="caption"><i>Cancellaria reticulata</i><br>
        <br>
Common Nutmeg<br>
Photo: jaxshells.com</span></td>
            <td align="center" valign="top"><span class="caption"><i>Cariopsilla pharpa</i><br>
        <br>
Dorid Nudibranch<br>
Photo: Paula Mikkelson, HBOI</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Ceratozona squalida Eastern Surf Chiton Marlo Krisberg Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Ceratozona squalida Eastern Surf Chiton Marlo Krisberg Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Cerithidea scalariformis Ladder Hornsnail Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Cerithidea scalariformis Ladder Hornsnail Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Cerithiopsis greenii.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Cerithiopsis greenii_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Cerithium atratum Dark Cerith Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Cerithium atratum Dark Cerith Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ceratozona squalida</i><br>
        <br>
Eastern Surf Chiton<br>
Photo: Marlo Krisberg jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Cerithidea scalariformis">Cerithidea scalariformis</a></i><br>
        <br>
Ladder Hornsnail<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cerithiopsis greenii</i><br>
        <br>
        <br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cerithium atratum</i><br><br>
        Dark Cerith<br>
        Photo: Joel Wooster jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Cerithium litteratum Stocky Cerith Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Cerithium litteratum Stocky Cerith Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Cerithium lutosum Variable Cerith Color 1 Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Cerithium lutosum Variable Cerith Color 1 Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Cerithium lutosum Variable Cerith Color 2 Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Cerithium lutosum Variable Cerith Color 2 Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Cerithium muscarum Flyspeck Cerith Eggs Amy Tripp Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Cerithium muscarum Flyspeck Cerith Eggs Amy Tripp Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cerithium litteratum</i><br><br>
      Stocky Cerith<br>
      Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cerithium lutosum</i><br><br>
        Variable Cerith <br>
        Color 1<br>
        Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cerithium lutosum</i><br><br>
        Variable Cerith <br>
Color 2<br>
        Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Cerithium muscarum">Cerithium muscarum</a></i><br><br>
        Flyspeck Cerith <br>
        with Eggs<br>
        Photo: Amy Tripp jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Cerithium muscarum Flyspeck Cerith Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Cerithium muscarum Flyspeck Cerith Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Chicoreus brevifrons West Indian Murex L Holly Sweat.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Chicoreus brevifrons West Indian Murex L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Circulus suppressus.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Circulus suppressus_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Costoanachis avara Greedy Dovesnail Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Costoanachis avara Greedy Dovesnail Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Cerithium muscarum">Cerithium muscarum</a></i><br><br>
      Flyspeck Cerith<br>
      Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Chicoreus brevifrons</i><br><br>
        West Indian Murex<br>
        Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Circulus suppressus</i><br>
        <br>
Suppressed Vitrinella<br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Costoanachis avara</i><br>
        <br>
Greedy Dovesnail<br>
Photo: jaxshells.com</span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Costoanachis sparsa Sparse Dovesnail Juveniles Marlo Krisberg Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Costoanachis sparsa Sparse Dovesnail Juveniles Marlo Krisberg Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Crepidula atrasolea Black-footed Slipper Snail Rachel Collin.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Crepidula atrasolea Black-footed Slipper Snail Rachel Collin.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Crepidula ustulatulina Little Speckled Slipper Snail Rachel Collin.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Crepidula ustulatulina Little Speckled Slipper Snail Rachel Collin.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Cymatium pileare_body.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Cymatium pileare_body_small.jpg" width="141"
                            height="106"></a></td>
        </tr>
        <tr>
            <td align="center" valign="top"><span class="caption"><i>Costoanachis sparsa</i><br>
        <br>
Sparse Dovesnail Juveniles<br>
Photo: Marlo Krisberg jaxshells.com</span></td>
            <td align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Crepidula atrasolea">Crepidula atrasolea</a></i><br>
        <br>
Black-footed Slipper Snail<br>
Photo: Rachel Collin</span></td>
            <td align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Crepidula ustulatulina">Crepidula ustulatulina</a></i><br>
        <br>
Little Speckled Slipper Snail<br>
Photo: Rachel Collin</span></td>
            <td align="center" valign="top"><span class="caption"><i>Cymbatium pileare</i><br>
        <br>
Hairy Triton Body<br>
Photo: Paula Mikkelson, HBOI</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Cymatium pileare_ventral.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Cymatium pileare_ventral_small.jpg"
                            width="141" height="106"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Cymatium pileare_dorsal.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Cymatium pileare_dorsal_small.jpg"
                            width="141" height="106"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Diodora cayenensis Cayenne Keyhole Limpet Marlo Krisberg Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Diodora cayenensis Cayenne Keyhole Limpet Marlo Krisberg Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Dendrodoris krebsii.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Dendrodoris krebsii_small.jpg" width="141"
                            height="106"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cymbatium pileare</i><br>
        <br>
Hairy Triton, Ventral Shell<br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cymbatium pileare</i><br>
        <br>
Hairy Triton, Dorsal Shell<br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Diodora cayenensis">Diodora cayenensis</a></i><br><br>
        Cayenne Keyhole Limpet<br>
        Photo: Marlo Krisberg jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Dendrodoris krebsii</i><br>
        <br>
        Dorid Nudibranch
<br>
Photo: Paula Mikkelson, HBOI</span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Diodora jaumei David Kirsh Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Diodora jaumei David Kirsh Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Diodora listeri Lister's Keyhole Limpet Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Diodora listeri Lister's Keyhole Limpet Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Diodora meta Meta Keyhole Limpet Marlo Krisberg Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Diodora meta Meta Keyhole Limpet Marlo Krisberg Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Echinolittorina angustior Slender Periwinkle Phil Poland Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Echinolittorina angustior Slender Periwinkle Phil Poland Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td align="center" valign="top"><span class="caption"><i>Diodora jaumei</i><br>
        <br>
Limpet<br>
Photo: David Kirsh jaxshells.com</span></td>
            <td align="center" valign="top"><span class="caption"><i>Diodora listeri</i><br>
        <br>
Lister's Keyhole Limpet<br>
Photo: jaxshells.com</span></td>
            <td align="center" valign="top"><span class="caption"><i>Diodora meta</i><br>
        <br>
Meta Keyhole Limpet<br>
Photo: Marlo Krisberg jaxshells.com</span></td>
            <td align="center" valign="top"><span class="caption"><i>Echinolittorina angustior</i><br>
        <br>
Slender Periwinkle<br>
Photo: Phil Poland jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Echinolittorina ziczac Zebra Periwinkle Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Echinolittorina ziczac Zebra Periwinkle Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Elysia crispata Lettuce Slug L Holly Sweat.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Elysia crispata Lettuce Slug L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><img
                        src="../content/imglib/2017_Gallery_Thumbnails/Elysia chlorotica_small.jpg" width="141"
                        height="106"></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Eupleura caudata_eggs.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Eupleura caudata_eggs_small.jpg" width="141"
                            height="106"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Echinolittorina ziczac">Echinolittorina ziczac</a></i><br>
        <br>
Zebra Periwinkle<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Elysia crispata</i><br>
        <br>
Lettuce Slug<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Elysia chlorotica</i><br>
        <br>
        Eastern Emerald Elysia
<br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Eupleura caudata</i><br>
        <br>
        Thick-lip Drill Egg Mass
<br>
Photo: Paula Mikkelson, HBOI</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Eupleura caudata_shell.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Eupleura caudata_shell_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Eupleura sulcidentata Sharp-rib Drill Amy Tripp Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Eupleura sulcidentata Sharp-rib Drill Amy Tripp Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Fasciolaria tulipa True Tulip L Holly Sweat.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Fasciolaria tulipa True Tulip L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Granulina ovuliformis Teardrop Marginella David Kirsh.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Granulina ovuliformis Teardrop Marginella David Kirsh.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Eupleura caudata</i><br>
        <br>
Thick-lip Drill <br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Eupleura sulcidentata</i><br><br>
        Sharp-rib Drill<br>
        Photo: Amy Tripp jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Fasciolaria tulipa">Fasciolaria tulipa</a></i><br><br>
        True Tulip<br>
        Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Granulina ovuliformis</i><br><br>
      Teardrop Marginella<br>
      Photo: David Kirshjaxshells.com</span></p></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Haminoea antillarum Antilles Glassy-bubble with Eggs Amy Tripp Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Haminoea antillarum Antilles Glassy-bubble with Eggs Amy Tripp Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Haminoea succinea Amber Glassy-bubble Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Haminoea succinea Amber Glassy-bubble Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Hydatina vesicaria.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Hydatina vesicaria_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Janthina sp Sabrina Bethurum.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Janthina sp Sabrina Bethurum.jpg" width="140"
                            height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Haminoea antillarum</i><br><br>
      Antilles Glassy-bubble<br>
      with Eggs
      <br>
      Photo: Amy Tripp jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Haminoea succinea</i><br><br>
        Amber Glassy-bubble<br>
        Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Hydatina vesicaria</i><br>
        <br>
        <br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Janthina </i>sp.<br>
Janthina Snail<br>
Photo: Sabrina Bethurum</span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Japonactaeon punctostriatus_live_with_eggs.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Japonactaeon punctostriatus_live_with_eggs_small.jpg"
                            width="141" height="106"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Japonactaeon punctostriatus_shell.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Japonactaeon punctostriatus_shell_small.jpg"
                            width="141" height="106"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Leucozonia nassa Chestnut Latirus Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Leucozonia nassa Chestnut Latirus Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Lithopoma tectum West Indian Starsnail L Holly Sweat.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Lithopoma tectum West Indian Starsnail L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td align="center" valign="top"><span class="caption"><i>Japonacteon punctostriatus</i><br>
        <br>
Pitted Baby-bubble with Eggs<br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td align="center" valign="top"><span class="caption"><i>Japonacteon punctostriatus</i><br>
        <br>
Pitted Baby-bubble<br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td align="center" valign="top"><span class="caption"><i>Leucozonia nassa </i><br>
        <br>
Chestnut Latirus<br>
Photo: jaxshells.com</span></td>
            <td align="center" valign="top"><span class="caption"><i>Lithopoma tectum</i><br>
        <br>
West Indian Starsnail<br>
Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Littorina angulifera Mangrove Periwinkle L Holly Sweat.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Littorina angulifera Mangrove Periwinkle L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Lobatus costatus.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Lobatus costatus_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Meioceras cornucopiae.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Meioceras cornucopiae_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Meioceras nitidum David Kirsh Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Meioceras nitidum David Kirsh Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Littorina angulifera">Littorina angulifera</a></i><br>
        <br>
Mangrove Periwinkle<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lobatus costatus</i><br>
        <br>
        Milk Conch
<br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Meioceras cornucopiae</i><br>
        <br>
        <br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Meioceras nitidum</i><br>
      <br>
Photo: David Kirsh jaxshells.com</span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Melampus bidentatus Eastern Melampus Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Melampus bidentatus Eastern Melampus Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Melampus coffeus Coffee Bean Snail L Holly Sweat.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Melampus coffeus Coffee Bean Snail L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Mitrella ocellata White-spot Dovesnail Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Mitrella ocellata White-spot Dovesnail Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/2017_Gallery_Lg_Images/Modulus modulus.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Modulus modulus_small.jpg" width="141"
                            height="106"></a></td>
        </tr>
        <tr>
            <td align="center" valign="top"><span class="caption"><i>Melampus bidentatus</i><br>
        <br>
Eastern Melampus<br>
Photo: jaxshells.com</span></td>
            <td align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Melampus coffeus">Melampus coffeus</a></i><br>
        <br>
Coffee Bean Snail<br>
Photo: L Holly Sweat</span></td>
            <td align="center" valign="top"><span class="caption"><i>Mitrella ocellata</i><br>
        <br>
White-spot Dovesnail<br>
Photo: jaxshells.com</span></td>
            <td align="center" valign="top"><span class="caption"><i>Modulus modulus</i><br>
        <br>
        Buttonsnail
<br>
Photo: Paula Mikkelson, HBOI</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Nassarius obsoletus Eastern Mudsnail Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Nassarius obsoletus Eastern Mudsnail Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Nassarius vibex Bruised Nassa Amy Tripp Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Nassarius vibex Bruised Nassa Amy Tripp Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Natica livida Livid Moonsnail Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Natica livida Livid Moonsnail Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Natica pusilla.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Natica pusilla_small.jpg" width="141"
                            height="106"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Nassarius obsoletus</i><br>
        <br>
Eastern Mudsnail<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Nassarius vibex</i><br>
        <br>
Bruised Nassa<br>
Photo: Amy Tripp jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Natica livida</i><br>
        <br>
Livid Moonsnail<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Natica pusilla</i><br>
        <br>
        <br>
Photo: Paula Mikkelson, HBOI</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Nerita fulgurans Antillean Nerite Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Nerita fulgurans Antillean Nerite Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Nerita tessellata Checkered Nerite Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Nerita tessellata Checkered Nerite Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Nerita versicolor Four-tooth Nerite Marlo Krisberg Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Nerita versicolor Four-tooth Nerite Marlo Krisberg Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Nitidella nitida Glossy Dovesnail Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Nitidella nitida Glossy Dovesnail Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Nerita fulgurans">Nerita fulgurans</a></i><br>
        <br>
Antillean Nerite<br>
Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Nerita tessellata">Nerita tessellata</a></i><br>
        <br>
Checkered Nerite<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Nerita versicolor">Nerita versicolor</a></i><br>
        <br>
Four-tooth Nerite<br>
Photo: Marlo Krisberg jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Nitidella nitida</i><br>
        <br>
Glossy Dovesnail<br>
Photo: jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Oliva sayana.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Oliva sayana_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Olivella mutica Variable Dwarf Olive Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Olivella mutica Variable Dwarf Olive Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Oxynoe antillarum Antilles Oxynoe 2004 SI.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Oxynoe antillarum Antilles Oxynoe 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Parvanachis obesa Fat Dovesnail David Kirsh Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Parvanachis obesa Fat Dovesnail David Kirsh Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Oliva sayana</i><br>
        <br>
        Lettered Olive
<br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Olivella mutica</i><br><br>
        Dwarf Olive<br>
        Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Oxynoe antillarum</i><br><br>
        Antilles Oxynoe<br>
        Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Parvanachis obesa</i><br><br>
        Fat Dovesnail<br>
        Photo: David Kirsh jaxshells.com</span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Parviturboides interruptus Interrupted Vitrinella David Kirsh Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Parviturboides interruptus Interrupted Vitrinella David Kirsh Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Petitilla crosseana.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Petitilla crosseana_small.jpg" width="141"
                            height="106"></a></td>
            <td align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Prunum apicinum Common Atlantic Marginella Marlo Krisberg.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Prunum apicinum Common Atlantic Marginella Marlo Krisberg.jpg"
                            width="140" height="105"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/2017_Gallery_Lg_Images/Pyrgocythara plicosa.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Pyrgocythara plicosa_small.jpg" width="141"
                            height="106"></a></td>
        </tr>
        <tr>
            <td align="center" valign="top"><span class="caption"><i>Parviturboides interruptus</i><br>
        <br>
Interrupted Vitrinella<br>
Photo: David Kirsh jaxshells.com</span></td>
            <td align="center" valign="top"><span class="caption"><i>Petitilla crosseana</i><br>
        <br>
        <br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Prunum apicinum">Prunum apicinum</a></i><br>
        <br>
Common Atlantic Marginella<br>
Photo: Marlo Krisberg jaxshells.com</span></td>
            <td align="center" valign="top"><span class="caption"><i>Pyrgocythara plicosa</i><br>
        <br>
        Plicate Mangelia
<br>
Photo: Paula Mikkelson, HBOI</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Rissoella caribaea.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Rissoella caribaea_small.jpg" width="141"
                            height="106"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Seila adamsii Adam's Miniature Cerith Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Seila adamsii Adam's Miniature Cerith Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Sinum perspectivum White Baby Ear Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Sinum perspectivum White Baby Ear Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Sinum perspectivum White Baby Ear Living Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Sinum perspectivum White Baby Ear Living Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Rissoella caribaea</i><br>
        <br>
        Caribbean Risso
<br>
Photo: Paula Mikkelson, HBOI</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Seila adamsii</i><br>
        <br>
Adam's Miniature Cerith<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sinum perspectivum</i><br>
        <br>
White Baby Ear<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sinum perspectivum</i><br>
        <br>
White Baby Ear <br>
Living Specimen<br>
Photo: jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Siphonaria pectinata Striped False Limpet Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Siphonaria pectinata Striped False Limpet Jax Shells.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Smaragdia viridis Emerald Nerite L Holly Sweat.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Smaragdia viridis Emerald Nerite L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Spurilla sp L Holly Sweat.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Spurilla sp L Holly Sweat.jpg" width="140"
                            height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Strombus gigas Queen Conch John Whiticar.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Strombus gigas Queen Conch John Whiticar.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Siphonaria pectinata">Siphonaria pectinata</a></i><br>
        <br>
Striped False Limpet<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Smaragdia viridis">Smaragdia viridis</a></i><br>
        <br>
Emerald Nerite<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Spurilla </i>sp.<br>
Nudibranch<br>
Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Strombus gigas">Strombus gigas</a></i><br>
        <br>
Queen Conch<br>
Photo: John Whiticar</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Teinostoma biscaynense Biscayne Vitrinella David Kirsh Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Teinostoma biscaynense Biscayne Vitrinella David Kirsh Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Terebra dislocata Eastern Auger Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Terebra dislocata Eastern Auger Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Terebra taurina Flame Auger Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Terebra taurina Flame Auger Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/2017_Gallery_Lg_Images/Triplofusus giganteus.jpg"><img
                            src="../content/imglib/2017_Gallery_Thumbnails/Triplofusus giganteus_small.jpg" width="141"
                            height="106"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Teinostoma biscaynense</i><br>
        <br>
Biscayne Vitrinella<br>
Photo: David Kirsh jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Terebra dislocata</i><br>
        <br>
Eastern Auger<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Terebra taurina</i><br>
        <br>
Flame Auger<br>
Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Triplofusus giganteus</i><br>
        <br>
        Florida Horse Conch
<br>
Photo: Paula Mikkelson, HBOI</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Truncatella pulchella Beautiful Truncatella Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Truncatella pulchella Beautiful Truncatella Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Turbo castanea Chestnut Turban Jim Miller Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Turbo castanea Chestnut Turban Jim Miller Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Turbonilla hemphilli David Kirsh Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Turbonilla hemphilli David Kirsh Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Urosalpinx tampaensis Tampa Drill Marlo Krisberg Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Urosalpinx tampaensis Tampa Drill Marlo Krisberg Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Truncatella pulchella</i><br><br>
      Beautiful Truncatella<br>
      Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Turbo castanea</i><br><br>
        Chestnut Turban<br>
        Photo: Jim Miller jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Turbonilla hemphilli</i><br><br>
        Snail<br>
        Photo: David Kirsh jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Urosalpinx tampaensis</i><br><br>
        Tampa Drill<br>
        Photo: Marlo Krisberg jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Gastropods/Cropped Images/Zebina browniana Smooth Risso Marlo Krisberg Jax Shells.jpg"><img
                            src="../content/imglib/Gastropods/Thumbnails/Zebina browniana Smooth Risso Marlo Krisberg Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Zebina browniana</i><br><br>
      Smooth Risso<br>
      Photo: Marlo Krisberg jaxshells.com</span></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="0" cellpadding="11">
        <tr>
            <td align="right"><a href="#Top">Back to Top</a></td>
        </tr>
        <tr>
            <td><p class="title"><a name="Insects"></a>INSECTS &amp; SPIDERS</p></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Acanthocephala declivis Giant Leaf-footed Bug L Holly Sweat.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Acanthocephala declivis Giant Leaf-footed Bug L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Acharia stimulea Saddleback Caterpillar Marc Virgilio.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Acharia stimulea Saddleback Caterpillar Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Agasicles hygrophila Alligatorweed Flea Beetle A Sosa USDA.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Agasicles hygrophila Alligatorweed Flea Beetle A Sosa USDA.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Agraulis vanillae Gulf Fritillary L Savary.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Agraulis vanillae Gulf Fritillary L Savary.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Acanthocephala declivis</i><br><br>
      Giant Leaf-footed Bug<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Acharia stimulea</i><br><br>
      Saddleback Caterpillar<br>
      Photo: Marc Virgilio</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Agasicles hygrophila">Agasicles hygrophila</a></i><br><br>
      Alligatorweed Flea Beetle<br>
      Photo: Pat Poston</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Agraulis vanillae</i><br><br>
      Gulf Fritillary<br>
      Photo: L Savary</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Anartia jatrophae White Peacock R Murray.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Anartia jatrophae White Peacock R Murray.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Apis mellifera scutellata Africanized Honeybee Scott Bauer USDA.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Apis mellifera scutellata Africanized Honeybee Scott Bauer USDA.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Argiope aurantia Yellow Garden Spider 2004 SI.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Argiope aurantia Yellow Garden Spider 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Ascalapha odorata Black Witch 2004 SI.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Ascalapha odorata Black Witch 2004 SI.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Anartia jatrophae</i><br><br>
      White Peacock<br>
      Photo: R Murray</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Apis mellifera scutellata">Apis mellifera scutellata</a></i><br><br>
        Africanized Honeybee<br>
        Photo: Scott Bauer USDA</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Argiope aurantia</i><br><br>
        Yellow Garden Spider<br>
        Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ascalapha odorata</i><br><br>
        Black Witch<br>
        Photo: &copy; 2004 Smithsonian Institution</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Ascia monuste Great Southern White B Zarella.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Ascia monuste Great Southern White B Zarella.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Battus polydamus Polydamus Swallowtail G Cawley.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Battus polydamus Polydamus Swallowtail G Cawley.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Bombus sp Bumble Bee Roy Sanderfur.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Bombus sp Bumble Bee Roy Sanderfur.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Brachymesia gravida Four-spotted Pennant Thedy Braw.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Brachymesia gravida Four-spotted Pennant Thedy Braw.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ascia monuste</i><br><br>
      Great Southern White<br>
      Photo: B Zarella</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Battus polydamus</i><br><br>
        Polydamus Swallowtail<br>
        Photo: G Cawley</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Bombus </i>sp.<br>
        Bumble Bee<br>
        Photo: Roy Sanderfur</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Brachymesia gravida</i><br><br>
        Four-spotted Pennant<br>
        Photo: Thedy Braw</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Celithemis eponina Halloween Pennant A Beachler.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Celithemis eponina Halloween Pennant A Beachler.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Clemensia albata Little White Lichen Moth L Holly Sweat.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Clemensia albata Little White Lichen Moth L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Danaus gilippus Queen K Burke.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Danaus gilippus Queen K Burke.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Danaus plexippus Monarch D Raulerson.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Danaus plexippus Monarch D Raulerson.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Celithemis eponina</i><br><br>
      Halloween Pennant<br>
      Photo: A Beachler</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Clemensia albata</i><br><br>
        Little White Lichen Moth<br>
        Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Danaus gilippus</i><br><br>
        Queen<br>
        Photo: K Burke</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Danaus plexippus</i><br><br>
        Monarch<br>
        Photo: D Raulerson</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Erythrodiplax berenice Seaside Dragonlet P Winegar.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Erythrodiplax berenice Seaside Dragonlet P Winegar.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Gasteracantha cancriformis Spinybacked Orbweaver 2004 SI.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Gasteracantha cancriformis Spinybacked Orbweaver 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Heliconius charithonia Zebra Longwing Marc Virgilio.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Heliconius charithonia Zebra Longwing Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Junonia evarete Mangrove Buckeye O McMurtrey.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Junonia evarete Mangrove Buckeye O McMurtrey.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Erythrodiplax berenice</i><br><br>
      Seaside Dragonlet<br>
      Photo: P Winegar</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Gasteracantha cancriformis</i><br><br>
        Spinybacked Orbweaver<br>
        Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Heliconius charithonia</i><br><br>
        Zebra Longwing<br>
        Photo: Marc Virgilio</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Junonia evarete</i><br><br>
        Mangrove Buckeye<br>
        Photo: O McMurtrey</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Nephilia clavipes Golden Silk Spider 2004 SI.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Nephilia clavipes Golden Silk Spider 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Orgyia leucostigma White-marked Tussock Moth Caterpillar L Holly Sweat.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Orgyia leucostigma White-marked Tussock Moth Caterpillar L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Papilio cresphontes Giant Swallowtail William Nunn.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Papilio cresphontes Giant Swallowtail William Nunn.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Phocides pigmalion Mangrove Skipper Harry McVay.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Phocides pigmalion Mangrove Skipper Harry McVay.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Nephilia clavipes</i><br><br>
      Golden Silk Spider<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Orgyia leucostigma</i><br><br>
        White-marked Tussock Moth<br>
        Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Papilio cresphontes</i><br><br>
        Giant Swallowtail<br>
        Photo: William Nunn</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Phocides pigmalion</i><br><br>
        Mangrove Skipper<br>
        Photo: Harry McVay</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Phoebis sennae Cloudless Sulphur Marc Virgilio.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Phoebis sennae Cloudless Sulphur Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Romalea microptera Eastern Lubber Grasshopper L Holly Sweat.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Romalea microptera Eastern Lubber Grasshopper L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/InsectsSpiders/Cropped Images/Zelus longipes Milkweed Assassin Bug J Safer.jpg"><img
                            src="../content/imglib/InsectsSpiders/Thumbnails/Zelus longipes Milkweed Assassin Bug J Safer.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Phoebis sennae</i><br><br>
      Cloudless Sulphur<br>
      Photo: Marc Virgilio</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Romalea microptera</i><br><br>
        Eastern Lubber Grasshopper<br>
        Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Zelus longipes</i><br><br>
        Milkweed Assassin Bug<br>
        Photo: J Safer</span></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="0" cellpadding="11">
        <tr>
            <td align="right"><a href="#Top">Back to Top</a></td>
        </tr>
        <tr>
            <td><p class="title"><a name="Mammals"></a>MAMMALS</p></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Mammals/Cropped Images/Dasypus novemcinctus Nine-banded Armadillo J Corhern.jpg"><img
                            src="../content/imglib/Mammals/Thumbnails/Dasypus novemcinctus Nine-banded Armadillo J Corhern.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Mammals/Cropped Images/Lontra canadensis North American River Otter Cheryl Miller City of Stuart.jpg"><img
                            src="../content/imglib/Mammals/Thumbnails/Lontra canadensis North American River Otter Cheryl Miller City of Stuart.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Mammals/Cropped Images/Lynx rufus Bobcat Charles Corbeil.jpg"><img
                            src="../content/imglib/Mammals/Thumbnails/Lynx rufus Bobcat Charles Corbeil.jpg" width="140"
                            height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Mammals/Cropped Images/Odocoileus virginianus White-tailed Deer B Cozza.jpg"><img
                            src="../content/imglib/Mammals/Thumbnails/Odocoileus virginianus White-tailed Deer B Cozza.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Dasypus novemcinctus">Dasypus novemcinctus</a></i><br><br>
      Nine-banded Armadillo<br>
      Photo: J Corhern</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Lontra canadensis">Lontra canadensis</a></i><br><br>
      North American <br>
      River Otter<br>
      Photo: Cheryl Miller <br>
      City of Stuart</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lynx rufus</i><br><br>
      Bobcat<br>
      Photo: Charles Corbeil</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Odocoileus virginianus</i><br><br>
      White-tailed Deer<br>
      Photo: B Cozza</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Mammals/Cropped Images/Procyon lotor Raccoon J Leake.jpg"><img
                            src="../content/imglib/Mammals/Thumbnails/Procyon lotor Raccoon J Leake.jpg" width="140"
                            height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Mammals/Cropped Images/Sciurus carolinensis Eastern Gray Squirrel T Saltmarsh.jpg"><img
                            src="../content/imglib/Mammals/Thumbnails/Sciurus carolinensis Eastern Gray Squirrel T Saltmarsh.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Mammals/Cropped Images/Sus scrofa Wild Boar Juvenile J Brady.jpg"><img
                            src="../content/imglib/Mammals/Thumbnails/Sus scrofa Wild Boar Juvenile J Brady.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Mammals/Cropped Images/Sus scrofa Wild Boar R Gomme.jpg"><img
                            src="../content/imglib/Mammals/Thumbnails/Sus scrofa Wild Boar R Gomme.jpg" width="140"
                            height="105" class="red-border"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Procyon lotor</i><br><br>
      Raccoon<br>
      Photo: J Leake</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sciurus carolinensis</i><br><br>
        Eastern Gray Squirrel<br>
        Photo: T Saltmarsh</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Sus scrofa">Sus scrofa</a></i><br><br>
        Wild Boar Juveniles<br>
        Photo: J Brady</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Sus scrofa">Sus scrofa</a></i><br><br>
        Wild Boar<br>
        Photo: R Gomme</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Mammals/Cropped Images/Sylvilagus palustris paludicola Florida Marsh Rabbit Ray Walton.jpg"><img
                            src="../content/imglib/Mammals/Thumbnails/Sylvilagus palustris paludicola Florida Marsh Rabbit Ray Walton.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Mammals/Cropped Images/Trichechus manatus latirostris Florida Manatee Katie Tripp.jpg"><img
                            src="../content/imglib/Mammals/Thumbnails/Trichechus manatus latirostris Florida Manatee Katie Tripp.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Mammals/Cropped Images/Tursiops truncatus Common Bottlenose Dolphin G Laswell.jpg"><img
                            src="../content/imglib/Mammals/Thumbnails/Tursiops truncatus Common Bottlenose Dolphin G Laswell.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sylvilagus palustris paludicola</i><br><br>
      Florida Marsh Rabbit<br>
      Photo: Ray Walton</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Trichechus manatus latirostris">Trichechus manatus latirostris</a></i><br><br>
        Florida Manatee<br>
        Photo: Katie Tripp</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Tursiops truncatus">Tursiops truncatus</a></i><br><br>
        Common <br>
        Bottlenose Dolphin<br>
        Photo: G Laswell</span></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="0" cellpadding="11">
        <tr>
            <td class="SectionTitle" align="right"><a href="#Top">Back to Top</a></td>
        </tr>
        <tr>
            <td><p class="title"><a name="Algae"></a>MARINE ALGAE</p></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/MarineAlgae/Cropped Images/Acetabularia calyculus Umbrella Alga L Holly Sweat.jpg"><img
                            src="../content/imglib/MarineAlgae/Thumbnails/Acetabularia calyculus Umbrella Alga L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/MarineAlgae/Cropped Images/Batophora oerstedii L Holly Sweat.jpg"><img
                            src="../content/imglib/MarineAlgae/Thumbnails/Batophora oerstedii L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/MarineAlgae/Cropped Images/Botryocladia occidentalis L Holly Sweat.jpg"><img
                            src="../content/imglib/MarineAlgae/Thumbnails/Botryocladia occidentalis L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/MarineAlgae/Cropped Images/Caulerpa mexicana Fern Alga L Holly Sweat.jpg"><img
                            src="../content/imglib/MarineAlgae/Thumbnails/Caulerpa mexicana Fern Alga L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Acetabularia calyculus">Acetabularia calyculus</a></i><br><br>
      Umbrella Alga<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Batophora oerstedii</i><br><br>
      Green Alga<br>
    Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Botryocladia occidentalis</i><br><br>
      Red Alga<br>
    Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Caulerpa mexicana</i><br><br>
      Fern Alga<br>
    Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/MarineAlgae/Cropped Images/Caulerpa prolifera Blade Alga L Holly Sweat.jpg"><img
                            src="../content/imglib/MarineAlgae/Thumbnails/Caulerpa prolifera Blade Alga L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/MarineAlgae/Cropped Images/Caulerpa racemosa Green Grape Alga L Holly Sweat.jpg"><img
                            src="../content/imglib/MarineAlgae/Thumbnails/Caulerpa racemosa Green Grape Alga L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/MarineAlgae/Cropped Images/Caulerpa sertularoides Green Feather Alga L Holly Sweat.jpg"><img
                            src="../content/imglib/MarineAlgae/Thumbnails/Caulerpa sertularoides Green Feather Alga L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/MarineAlgae/Cropped Images/Halimeda discoidea Money Plant L Holly Sweat.jpg"><img
                            src="../content/imglib/MarineAlgae/Thumbnails/Halimeda discoidea Money Plant L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Caulerpa prolifera</i><br><br>
      Blade Alga<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Caulerpa racemosa</i><br><br>
        Green Grape Alga<br>
    Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Caulerpa sertularioides">Caulerpa sertularioides</a></i><br><br>
        Green Feather Alga<br>
    Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Halimeda discoidea</i><br><br>
        Money Plant<br>
    Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/MarineAlgae/Cropped Images/Padina pavonica Peacock's Tail L Holly Sweat.jpg"><img
                            src="../content/imglib/MarineAlgae/Thumbnails/Padina pavonica Peacock's Tail L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/MarineAlgae/Cropped Images/Sargassum fluitans Broad-toothed Gulfweed L Holly Sweat.jpg"><img
                            src="../content/imglib/MarineAlgae/Thumbnails/Sargassum fluitans Broad-toothed Gulfweed L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/MarineAlgae/Cropped Images/Udotea flabellum Mermaid's Fan L Holly Sweat.jpg"><img
                            src="../content/imglib/MarineAlgae/Thumbnails/Udotea flabellum Mermaid's Fan L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/MarineAlgae/Cropped Images/Ulva sp C Cornish.jpg"><img
                            src="../content/imglib/MarineAlgae/Thumbnails/Ulva sp C Cornish.jpg" width="140"
                            height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Padina pavonica">Padina pavonica</a></i><br><br>
      Peacock's Tail<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sargassum fluitans</i><br><br>
        Broad-toothed Gulfweed<br>
    Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Udotea flabellum</i><br><br>
        Mermaid's Fan<br>
    Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ulva </i>sp.<br><br>
        Green Alga<br>
    Photo: C Cornish</span></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="0" cellpadding="11">
        <tr>
            <td align="right"><a href="#Top">Back to Top</a></td>
        </tr>
        <tr>
            <td><p class="title"><a name="Misc"></a>MISCELLANEOUS ORGANISMS</p></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Misc/Cropped Images/Ascidia nigra Black Solitary Tunicate L Holly Sweat.jpg"><img
                            src="../content/imglib/Misc/Thumbnails/Ascidia nigra Black Solitary Tunicate L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Misc/Cropped Images/Chaetopterus variopedatus Parchment Worm Tube Joseph Dineen.jpg"><img
                            src="../content/imglib/Misc/Thumbnails/Chaetopterus variopedatus Parchment Worm Tube Joseph Dineen.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Misc/Cropped Images/Clavelina picta Painted Tunicate 2004 SI.jpg"><img
                            src="../content/imglib/Misc/Thumbnails/Clavelina picta Painted Tunicate 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Misc/Cropped Images/Diopatra cuprea Plumed Worm Tube Joseph Dineen.jpg"><img
                            src="../content/imglib/Misc/Thumbnails/Diopatra cuprea Plumed Worm Tube Joseph Dineen.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Ascidia nigra">Ascidia nigra</a></i><br><br>
      Black Solitary Tunicate<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Chaetopterus variopedatus">Chaetopterus variopedatus</a></i><br><br>
      Parchment Tube Worm<br>
      Photo: Joseph Dineen</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Clavelina picta</i><br><br>
      Painted Tunicate<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Diopatra cuprea</i><br><br>
      Plumed Worm Tube<br>
      Photo: Joseph Dineen</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Misc/Cropped Images/Herpothallon rubrocinctum Red Blanket Lichen L Holly Sweat.jpg"><img
                            src="../content/imglib/Misc/Thumbnails/Herpothallon rubrocinctum Red Blanket Lichen L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Misc/Cropped Images/Maritigrella crozieri Tiger Flatworm L Holly Sweat.jpg"><img
                            src="../content/imglib/Misc/Thumbnails/Maritigrella crozieri Tiger Flatworm L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Misc/Cropped Images/Perophora viridis Honeysuckle Tunicate 2004 SI.jpg"><img
                            src="../content/imglib/Misc/Thumbnails/Perophora viridis Honeysuckle Tunicate 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Misc/Cropped Images/Phragmatopoma caudata Reef Worm Tubes L Holly Sweat.jpg"><img
                            src="../content/imglib/Misc/Thumbnails/Phragmatopoma caudata Reef Worm Tubes L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Herpothallon rubrocinctum</i><br><br>
      Red Blanket Lichen<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Maritigrella crozieri">Maritigrella crozieri</a></i><br><br>
      Tiger Flatworm<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Perophora viridis</i><br><br>
      Honeysuckle Tunicate<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Phragmatopoma caudata</i><br><br>
      Reef Worm Tubes<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Misc/Cropped Images/Zoothamnium niveum Ciliate on Mangrove Leaf L Holly Sweat.jpg"><img
                            src="../content/imglib/Misc/Thumbnails/Zoothamnium niveum Ciliate on Mangrove Leaf L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Zoothamnium niveum">Zoothamnium niveum</a></i><br><br>
      Colonial Ciliate<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="0" cellpadding="11">
        <tr>
            <td align="right"><a href="#Top">Back to Top</a></td>
        </tr>
        <tr>
            <td><p class="title"><a name="Plants"></a>PLANTS - Aquatic and Terrestrial</p></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Acanthocereus tetragonus Triangle Cactus L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Acanthocereus tetragonus Triangle Cactus L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Acer rubrum Red Maple L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Acer rubrum Red Maple L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Acrostichum aureum Golden Leather Fern 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Acrostichum aureum Golden Leather Fern 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Agave sisalana Sisal Hemp Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Agave sisalana Sisal Hemp Joel Wooster Jax Shells.jpg"
                            width="140" height="105" class="red-border"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Acanthocereus tetragonus</i><br><br>
      Triangle Cactus<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Acer rubrum</i><br><br>
      Red Maple<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Acrostichum aureum">Acrostichum aureum</a></i><br><br>
      Golden Leather Fern<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Agave sisalana</i><br><br>
      Sisal Hemp<br>
      Photo: Joel Wooster jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Ardisia escallonioides Island Marlberry L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Ardisia escallonioides Island Marlberry L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Avicennia germinans Black Mangrove Flowers 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Avicennia germinans Black Mangrove Flowers 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Avicennia germinans Black Mangrove L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Avicennia germinans Black Mangrove L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Batis maritima Saltwort 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Batis maritima Saltwort 2004 SI.jpg" width="140"
                            height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ardisia escallonioides</i><br><br>
      Island Marlberry<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Avicennia germinans">Avicennia germinans</a></i><br><br>
      Black Mangrove Flower<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Avicennia germinans">Avicennia germinans</a></i><br><br>
      Black Mangrove<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Batis maritima</i><br><br>
      Saltwort<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Bidens pilosa Common Beggarticks L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Bidens pilosa Common Beggarticks L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Borrichia frutescens Sea Oxeye Daisy Courtney Duschene.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Borrichia frutescens Sea Oxeye Daisy Courtney Duschene.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Bursera simaruba Gumbo Limbo 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Bursera simaruba Gumbo Limbo 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Canavalia rosea Baybean 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Canavalia rosea Baybean 2004 SI.jpg" width="140"
                            height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Bidens pilosa</i><br><br>
      Common Beggarticks<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Borrichia frutescens</i><br><br>
      Sea Oxeye Daisy<br>
      Photo: Courtney Duschene</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Bursera simaruba</i><br><br>
      Gumbo Limbo<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Canavalia rosea</i><br><br>
      Baybean<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Casuarina equisetifolia Australian Pine 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Casuarina equisetifolia Australian Pine 2004 SI.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Cephalanthus occidentalis Buttonbush L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Cephalanthus occidentalis Buttonbush L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Ceratiola ericoides Florida Rosemary Jax Shells.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Ceratiola ericoides Florida Rosemary Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Chamaecrista fasciculata Partridge Pea L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Chamaecrista fasciculata Partridge Pea L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Casuarina equisetifolia">Casuarina equisetifolia</a></i><br><br>
      Australian Pine<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cephalanthus occidentalis</i><br><br>
      Buttonbush<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ceratiola ericoides</i><br><br>
      Florida Rosemary<br>
      Photo: Jax Shells</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Chamaecrista fasciculata</i><br><br>
      Partridge Pea<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Cirsium nuttallii Nuttall's Thistle L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Cirsium nuttallii Nuttall's Thistle L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Cladium jamaicense Sawgrass L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Cladium jamaicense Sawgrass L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Coccoloba uvifera Seagrape Fruit Georgia Schroeder.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Coccoloba uvifera Seagrape Fruit Georgia Schroeder.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Coccoloba uvifera Seagrape L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Coccoloba uvifera Seagrape L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cirsium nuttallii</i><br><br>
      Nuttall's Thistle<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Cladium jamaicense</i><br><br>
      Sawgrass<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Coccoloba uvifera</i><br><br>
      Seagrape Fruit<br>
      Photo: Georgia Schroeder</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Coccoloba uvifera</i><br><br>
      Seagrape<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Conocarpus erectus Buttonwood 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Conocarpus erectus Buttonwood 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Distichlis spicata Saltgrass 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Distichlis spicata Saltgrass 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Drosera capillaris Pink Sundew Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Drosera capillaris Pink Sundew Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Encyclia tampensis Tampa Butterfly Orchid L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Encyclia tampensis Tampa Butterfly Orchid L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Conocarpus erectus</i><br><br>
      Buttonwood<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Distichlis spicata</i><br><br>
      Saltgrass<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Drosera capillaris</i><br><br>
      Pink Sundew<br>
      Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Encyclia tampensis</i><br><br>
      Tampa Butterfly Orchid<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Erithalis fruticosa Blacktorch 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Erithalis fruticosa Blacktorch 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Erythrina herbacea Eastern Coralbean L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Erythrina herbacea Eastern Coralbean L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Euphorbia cyathophora Wild Poinsettia L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Euphorbia cyathophora Wild Poinsettia L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Gaillardia pulchella Indian Blanketflower L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Gaillardia pulchella Indian Blanketflower L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Erithalis fruticosa</i><br><br>
      Blacktorch<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Erythrina herbacea</i><br><br>
      Eastern Coralbean<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Euphorbia cyathophora</i><br><br>
      Wild Poinsettia<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Gaillardia pulchella</i><br><br>
      Indian Blanketflower<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Glandularia maritima Coastal Mock Vervain L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Glandularia maritima Coastal Mock Vervain L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Guilandina bonduc Gray Nicker 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Guilandina bonduc Gray Nicker 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Helianthus debilis Dune Sunflower L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Helianthus debilis Dune Sunflower L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Ilex cassine Dahoon Holly L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Ilex cassine Dahoon Holly L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Glandularia maritima</i><br><br>
      Coastal Mock Vervain<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Guilandina bonduc</i><br><br>
      Gray Nicker<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Helianthus debilis</i><br><br>
      Dune Sunflower<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ilex cassine</i><br><br>
      Dahoon Holly<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Ipomoea pes-caprae Beach Morning Glory Rebecca Sovine.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Ipomoea pes-caprae Beach Morning Glory Rebecca Sovine.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Iris virginica Virginia Iris Jax Shells.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Iris virginica Virginia Iris Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Laguncularia racemosa White Mangrove Flowers SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Laguncularia racemosa White Mangrove Flowers SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Lantana camara Largeleaf Lantana L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Lantana camara Largeleaf Lantana L Holly Sweat.jpg"
                            width="140" height="105" class="red-border"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Ipomoea pes-caprae">Ipomoea pes-caprae</a></i><br><br>
      Beach Morning Glory<br>
      Photo: Rebecca Sovine</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Iris virginica</i><br><br>
      Virginia Iris<br>
      Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Laguncularia racemosa">Laguncularia racemosa</a></i><br><br>
      White Mangrove Flowers<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lantana camara</i><br><br>
      Largeleaf Lantana<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Lonicera sempervirens Coral Honeysuckle L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Lonicera sempervirens Coral Honeysuckle L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Ludwigia peruviana Peruvian Primrose-willow Jax Shells.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Ludwigia peruviana Peruvian Primrose-willow Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Lyonia ferruginea Rusty Lyonia L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Lyonia ferruginea Rusty Lyonia L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Momordica charantia Balsam Apple Seeds L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Momordica charantia Balsam Apple Seeds L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lonicera sempervirens</i><br><br>
      Coral Honeysuckle<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ludwigia peruviana</i><br><br>
      Peruvian <br>
      Primrose-willow<br>
      Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lyonia ferruginea</i><br><br>
      Rusty Lyonia<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Momordica charantia</i><br><br>
      Balsam Apple
      Seeds<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Nephrolepis exaltata Boston Sword Fern L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Nephrolepis exaltata Boston Sword Fern L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Opuntia stricta Erect Pricklypear L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Opuntia stricta Erect Pricklypear L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Passiflora incarnata Purple Passionflower M Dundis.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Passiflora incarnata Purple Passionflower M Dundis.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Pinus palustris Longleaf Pine L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Pinus palustris Longleaf Pine L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Nephrolepis exaltata</i><br><br>
      Boston Sword Fern<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Opuntia stricta</i><br><br>
      Erect Pricklypear<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Passiflora incarnata</i><br><br>
      Purple Passionflower<br>
      Photo: M Dundis</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pinus palustris</i><br><br>
      Longleaf Pine<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Pleopeltis polypodioides var. polypodioides Resurrection Fern Roy Sanderfur.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Pleopeltis polypodioides var. polypodioides Resurrection Fern Roy Sanderfur.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Psychotria nervosa Wild Coffee L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Psychotria nervosa Wild Coffee L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Rhizophora mangle Red Mangrove Flower 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Rhizophora mangle Red Mangrove Flower 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Rhizophora mangle Red Mangrove L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Rhizophora mangle Red Mangrove L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pleopeltis polypodioides </i>var. <i>polypodioides</i><br><br>
      Resurrection Fern<br>
      Photo: Roy Sanderfur</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Psychotria nervosa</i><br><br>
      Wild Coffee<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Rhizophora mangle">Rhizophora mangle</a></i><br><br>
      Red Mangrove Flower<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Rhizophora mangle">Rhizophora mangle</a></i><br><br>
      Red Mangrove<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Ricinus communis Castor Bean Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Ricinus communis Castor Bean Joel Wooster Jax Shells.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Rivina humilis Rouge Plant L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Rivina humilis Rouge Plant L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Sabal palmetto Cabbage Palm L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Sabal palmetto Cabbage Palm L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Salvia coccinea Tropical Sage L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Salvia coccinea Tropical Sage L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ricinus communis</i><br><br>
      Castor Bean <br>
      Photo: Joel Wooster jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Rivina humilis</i><br><br>
      Rouge Plant<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Sabal palmetto">Sabal palmetto</a></i><br><br>
      Cabbage Palm<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Salvia coccinea</i><br><br>
      Tropical Sage<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Senna occidentalis Coffee Senna L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Senna occidentalis Coffee Senna L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Serenoa repens Saw Palmetto L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Serenoa repens Saw Palmetto L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Sesuvium portulacastrum Shoreline Seapurslane 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Sesuvium portulacastrum Shoreline Seapurslane 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Sida acuta Common Wireweed Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Sida acuta Common Wireweed Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Senna occidentalis</i><br><br>
      Coffee Senna<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Serenoa repens">Serenoa repens</a></i><br><br>
      Saw Palmetto<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sesuvium portulacastrum</i><br><br>
      Shoreline Seapurslane<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Sida acuta</i><br><br>
      Common Wireweed<br>
      Photo: Joel Wooster jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Suriana maritima Bay Cedar 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Suriana maritima Bay Cedar 2004 SI.jpg" width="140"
                            height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Suriana maritima Bay Cedar Flower 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Suriana maritima Bay Cedar Flower 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Thalassia testudinum Turtlegrass Flower L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Thalassia testudinum Turtlegrass Flower L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Thalassia testudinum Turtlegrass L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Thalassia testudinum Turtlegrass L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Suriana maritima</i><br><br>
      Bay Cedar<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Suriana maritima</i><br><br>
        Bay Cedar Flower<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Thalassia testudinum">Thalassia testudinum</a></i><br><br>
      Turtlegrass Flower<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Thalassia testudinum">Thalassia testudinum</a></i><br><br>
        Turtlegrass<br>
      Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Thespesia populnea Portia Tree 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Thespesia populnea Portia Tree 2004 SI.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Tillandsia recurvata Ball Moss N Duncklee.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Tillandsia recurvata Ball Moss N Duncklee.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Tillandsia usneoides Spanish Moss Roy Sanderfur.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Tillandsia usneoides Spanish Moss Roy Sanderfur.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Tillandsia utriculata Spreading Airplant Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Tillandsia utriculata Spreading Airplant Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Thespesia populnea</i><br><br>
      Portia Tree<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tillandsia recurvata</i><br><br>
        Ball Moss<br>
        Photo: N Duncklee</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tillandsia usneoides</i><br><br>
        Spanish Moss<br>
        Photo: Roy Sanderfur</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tillandsia utriculata</i><br><br>
        Spreading Airplant<br>
        Photo: Joel Wooster jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Tournefortia gnaphalodes Sea Lavender 2004 SI.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Tournefortia gnaphalodes Sea Lavender 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Tradescantia ohiensis Bluejacket Kimberly Jarvis.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Tradescantia ohiensis Bluejacket Kimberly Jarvis.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Ulmus americana American Elm L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Ulmus americana American Elm L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Utricularia subulata Zigzag Bladderwort Joel Wooster Jax Shells.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Utricularia subulata Zigzag Bladderwort Joel Wooster Jax Shells.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tournefortia gnaphalodes</i><br><br>
      Sea Lavender<br>
      Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Tradescantia ohiensis</i><br><br>
        Bluejacket<br>
        Photo: Kimberly Jarvis</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ulmus americana</i><br><br>
        American Elm<br>
        Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Utricularia subulata</i><br><br>
        Zigzag Bladderwort<br>
        Photo: Joel Wooster jaxshells.com</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Vitis sp Wild Grape L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Vitis sp Wild Grape L Holly Sweat.jpg" width="140"
                            height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Yucca aloifolia Spanish Bayonet Flower Jax Shells.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Yucca aloifolia Spanish Bayonet Flower Jax Shells.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Yucca aloifolia Spanish Bayonet L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Yucca aloifolia Spanish Bayonet L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Plants/Cropped Images/Zanthoxylum clava-herculis Hercules' Club L Holly Sweat.jpg"><img
                            src="../content/imglib/Plants/Thumbnails/Zanthoxylum clava-herculis Hercules' Club L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Vitis </i>sp.<br><br>
      Wild Grape<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Yucca aloifolia</i><br><br>
        Spanish Bayonet Flower<br>
        Photo: jaxshells.com</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Yucca aloifolia</i><br><br>
        Spanish Bayonet<br>
        Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Zanthoxylum clava-herculis</i><br><br>
        Hercules' Club<br>
        Photo: L Holly Sweat</span></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="0" cellpadding="11">
        <tr>
            <td align="right"><a href="#Top">Back to Top</a></td>
        </tr>
        <tr>
            <td><p class="title"><a name="Reptiles"></a>REPTILES &amp; AMPHIBIANS</p></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Agama agama African Redhead Agama L Holly Sweat.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Agama agama African Redhead Agama L Holly Sweat.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Alligator mississippiensis American Alligator Audry Smith.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Alligator mississippiensis American Alligator Audry Smith.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Apalone ferox Florida Softshell Turtle K Jarvis.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Apalone ferox Florida Softshell Turtle K Jarvis.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Caretta caretta Loggerhead Sea Turtle Sabrina Bethurum.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Caretta caretta Loggerhead Sea Turtle Sabrina Bethurum.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Agama agama</i><br><br>
      African Redhead Agama<br>
      Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Alligator mississippiensis</i><br><br>
      American Alligator<br>
      Photo: Audry Smith</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Apalone ferox</i><br><br>
      Florida Softshell Turtle<br>
      Photo: Kimberly Jarvis</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Caretta caretta">Caretta caretta</a></i><br><br>
      Loggerhead Sea Turtle<br>
      Photo: Sabrina Bethurum</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Chelonia mydas Green Sea Turtle R Gomme.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Chelonia mydas Green Sea Turtle R Gomme.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Coluber constrictor priapus Southern Black Racer Sabrina Bethurum.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Coluber constrictor priapus Southern Black Racer Sabrina Bethurum.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Crocodylus acutus American Crocodile 2004 SI.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Crocodylus acutus American Crocodile 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Drymarchon couperi Eastern Indigo Snake C Newby.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Drymarchon couperi Eastern Indigo Snake C Newby.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Chelonia mydas">Chelonia mydas</a></i><br><br>
      Green Sea Turtle<br>
      Photo: R Gomme</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Coluber constrictor priapus</i><br><br>
        Southern Black Racer<br>
        Photo: Sabrina Bethurum</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Crocodylus acutus</i><br><br>
        American Crocodile<br>
        Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Drymarchon couperi<">Drymarchon couperi</a></i><br><br>
        Eastern Indigo Snake<br>
        Photo: C Newby</span></td>
        </tr>
        <tr>
            <td>
                <a href="../content/imglib/ReptilesAmphib/Cropped Images/Elaphe alleghaniensis Eastern Rat Snake Sabrina Bethurum.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Elaphe alleghaniensis Eastern Rat Snake Sabrina Bethurum.jpg"
                            width="140" height="105"></a></td>
            <td>
                <a href="../content/imglib/ReptilesAmphib/Cropped Images/Eretmochelys imbricata Hawksbill Sea Turtle 2004 SI.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Eretmochelys imbricata Hawksbill Sea Turtle 2004 SI.jpg"
                            width="140" height="105"></a></td>
            <td>
                <a href="../content/imglib/ReptilesAmphib/Cropped Images/Gopherus polyphemus Gopher Tortoise Juvenile Marc Virgilio.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Gopherus polyphemus Gopher Tortoise Juvenile Marc Virgilio.jpg"
                            width="140" height="105"></a></td>
            <td>
                <a href="../content/imglib/ReptilesAmphib/Cropped Images/Gopherus polyphemus Gopher Tortoise L Holly Sweat.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Gopherus polyphemus Gopher Tortoise L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Elaphe alleghaniensis</i><br><br>
      Eastern Rat Snake<br>
      Photo: Sabrina Bethurum</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Eretmochelys imbricata</i><br><br>
        Hawksbill Sea Turtle<br>
        Photo: &copy; 2004 Smithsonian Institution</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Gopherus polyphemus">Gopherus polyphemus</a></i><br><br>
        Gopher Tortoise Juvenile<br>
        Photo: Marc Virgilio</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Gopherus polyphemus">Gopherus polyphemus</a></i><br><br>
        Gopher Tortoise<br>
        Photo: L Holly Sweat</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Hyla cinerea Green Treefrog Lorae Simpson.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Hyla cinerea Green Treefrog Lorae Simpson.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Malaclemys terrapin tequesta Florida East Coast Diamondback Terrapin Amy Reaume.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Malaclemys terrapin tequesta Florida East Coast Diamondback Terrapin Amy Reaume.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Micrurus fulvius Eastern Coral Snake HG Kolb.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Micrurus fulvius Eastern Coral Snake HG Kolb.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Norops sagrei Brown Anole Dewlap A Kopf.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Norops sagrei Brown Anole Dewlap A Kopf.jpg"
                            width="140" height="105" class="red-border"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i>Hyla cinerea</i><br><br>
      Green Treefrog<br>
      Photo: Lorae Simpson</span></td>
            <td width="175" align="center" valign="top"><span
                        class="caption"><i>Malaclemys terrapin tequesta</i><br><br>
        Florida East Coast Diamondback Terrapin<br>
        Photo: Amy Reaume</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Micrurus fulvius</i><br><br>
        Eastern Coral Snake<br>
        Photo: HG Kolb</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Norops sagrei">Norops sagrei</a></i><br><br>
        Brown Anole Dewlap
        <br>
        Photo: A Kopf</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Norops sagrei Brown Anole M Hamblin.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Norops sagrei Brown Anole M Hamblin.jpg"
                            width="140" height="105" class="red-border"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Opheodrys aestivus Rough Green Snake L Holly Sweat.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Opheodrys aestivus Rough Green Snake L Holly Sweat.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Ophisaurus ventralis Eastern Glass Lizard Kenneth Pichon.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Ophisaurus ventralis Eastern Glass Lizard Kenneth Pichon.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/ReptilesAmphib/Cropped Images/Pseudemys sp Cooter Tim Ebaugh.jpg"><img
                            src="../content/imglib/ReptilesAmphib/Thumbnails/Pseudemys sp Cooter Tim Ebaugh.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Norops sagrei">Norops sagrei</a></i><br><br>
      Brown Anole<br>
      Photo: M Hamblin</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Opheodrys aestivus</i><br><br>
        Rough Green Snake<br>
        Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Ophisaurus ventralis</i><br><br>
        Eastern Glass Lizard<br>
        Photo: L Holly Sweat</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Pseudemys </i>sp.<br><br>
        Cooter<br>
        Photo: Tim Ebaugh</span></td>
        </tr>
    </table>


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="0" cellpadding="1">
        <tr>
            <td align="right"><a href="#Top">Back to Top</a></td>
        </tr>
        <tr>
            <td><p class="title"><a name="Sponges"></a>SPONGES</p></td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="10" cellpadding="10">
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Sponges/Cropped Images/Dysidea etheria Ethereal Sponge Sven Zea Sponge Guide.jpg"><img
                            src="../content/imglib/Sponges/Thumbnails/Dysidea etheria Ethereal Sponge Sven Zea Sponge Guide.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Sponges/Cropped Images/Halichondria melanodocia John Reed.jpg"><img
                            src="../content/imglib/Sponges/Thumbnails/Halichondria melanodocia John Reed.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Sponges/Cropped Images/Haliclona caerulea Blue Caribbean Sponge Renata Goodridge.jpg"><img
                            src="../content/imglib/Sponges/Thumbnails/Haliclona caerulea Blue Caribbean Sponge Renata Goodridge.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Sponges/Cropped Images/Hymeniacidon heliophila Sun Sponge Klaus Rutzler.jpg"><img
                            src="../content/imglib/Sponges/Thumbnails/Hymeniacidon heliophila Sun Sponge Klaus Rutzler.jpg"
                            width="140" height="105"></a></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Dysidea etheria">Dysidea etheria</a></i><br><br>
      Ethereal Sponge<br>
      Photo: Sven Zea spongeguide.org</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Halichondria melanodocia">Halichondria melanodocia</a></i><br><br>
      Sponge<br>
      Photo: John Reed</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Haliclona caerulea">Haliclona caerulea</a></i><br><br>
      Blue Caribbean Sponge<br>
      Photo: Renata Goodridge</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Hymeniacidon heliophila">Hymeniacidon heliophila</a></i><br><br>
      Sun Sponge<br>
      Photo: Klaus R&uuml;tzler</span></td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Sponges/Cropped Images/Ircinia strobilina Bumpy Ball Sponge Sven Zea Sponge Guide.jpg"><img
                            src="../content/imglib/Sponges/Thumbnails/Ircinia strobilina Bumpy Ball Sponge Sven Zea Sponge Guide.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Sponges/Cropped Images/Lissodendoryx sigmata Micha Ilan Sponge Guide.jpg"><img
                            src="../content/imglib/Sponges/Thumbnails/Lissodendoryx sigmata Micha Ilan Sponge Guide.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top"><a
                        href="../content/imglib/Sponges/Cropped Images/Tedania ignis Fire Sponge Candy Feller.jpg"><img
                            src="../content/imglib/Sponges/Thumbnails/Tedania ignis Fire Sponge Candy Feller.jpg"
                            width="140" height="105"></a></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td width="175" align="center" valign="top"><span class="caption"><i><a href="../taxa/index.php?taxon=Ircinia strobilina">Ircinia strobilina</a></i><br><br>
      Bumpy Ball Sponge<br>
      Photo: Sven Zea spongeguide.org</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i>Lissodendoryx sigmata</i><br><br>
        Sponge<br>
      Photo: Micha Ilan spongeguide.org</span></td>
            <td width="175" align="center" valign="top"><span class="caption"><i><a
                                href="../taxa/index.php?taxon=Tedania ignis">Tedania ignis</a></i><br><br>
        Fire Sponge<br>
      Photo: Candy Feller</span></td>
            <td width="175" align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right"><a href="#Top">Back to Top</a></td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <p class="footer_note">Submit additional information, photos or comments to: <br/>
                    <a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu </a></p>
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
