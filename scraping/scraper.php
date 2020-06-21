<?php
include_once('../classes/DbConnection.php');
include_once('../config/symbini.php');
ini_set('max_execution_time', 10000);
//ini_set('error_reporting', E_CORE_ERROR);

class ScrapeManager{

    private $basrUrl = '/var/www/default/htdocs/irlspec/taxa/';
    private $file = '';
    private $sciname = '';
    private $tid = 0;
    private $headingArr = array();
    private $subheadingArr = array();
    private $conn;

    public function __construct() {
        $connection = new DbConnection();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if(!($this->conn === false)) {
            $this->conn->close();
        }
    }

    public function getTaxaFileList() {
        $retArr = array();
        $sql = 'SELECT Files FROM `scraping-taxa-files` ';
        //echo "<div>SQL: ".$sql."</div>";
        $result = $this->conn->query($sql);
        while($r = $result->fetch_object()){
            $retArr[] = $r->Files;
        }
        $result->close();
        return $retArr;
    }

    public function parseAllFiles($fileArr) {
        $this->headingArr = array();
        $this->subheadingArr = array();

        foreach($fileArr as $file){
            $this->setFile($file);
            $this->parseUrl();
        }

        /*foreach($this->subheadingArr as $text){
            echo $text . '<br />';
        }*/

        /*foreach($this->headingArr as $text){
            echo $text . '<br />';
            //$i++;
        }*/

        //$this->saveSubheadingArr();
        //$this->saveHeadingArr();
    }

    public function parseUrl(){
        $url = $this->basrUrl . $this->file;
        $this->tid = 0;
        $this->sciname = '';

        if($this->urlExists($url)){
            $fh = fopen($url, 'rb');
            $contents = stream_get_contents($fh);
            fclose($fh);

            //Get SciName
            if(preg_match("'<title>(.*?)</title>'si",$contents,$m)){
                if (strpos($m[1], '_') !== false) {
                    $m[1] = str_replace('_',' ',$m[1]);
                }
                $this->sciname = $m[1];
                $this->setTid();
                //echo $this->sciname . '<br />';
                //echo $this->tid . '<br />';
            }

            if($this->sciname && $this->tid){
                echo $this->file . '<br />';

                /*if(preg_match_all('"<p class=\"title\">(.*?)\s*</p>"si',$contents,$m)){
                    foreach($m[0] as $text){
                        if(!in_array($text,$this->subheadingArr)){
                            $this->subheadingArr[] = $text;
                        }
                    }
                }*/

                if(preg_match('"<p class=\"heading\">TAXONOMY</p>(.*?)<p class=\"heading\">HABITAT AND DISTRIBUTION</p>"si',$contents,$m)){
                    if(preg_match_all('"<p class=\"title\">(.*?)\s*</p>"si',$m[1],$t)){
                        foreach($t[1] as $text){
                            echo 'TAXONOMY: ' . $text . '<br />';
                        }
                    }
                }

                if(preg_match('"<p class=\"heading\">HABITAT AND DISTRIBUTION</p>(.*?)<p class=\"heading\">LIFE HISTORY AND POPULATION BIOLOGY</p>"si',$contents,$section)){
                    $habTitleArr = array();
                    if(preg_match_all('"<p class=\"title\">(.*?)\s*</p>"si',$section[1],$t)){
                        foreach($t[1] as $text){
                            $habTitleArr[] = $text;
                        }
                    }

                    foreach($habTitleArr as $title){
                        if(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*<p class=\"title\">"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                        elseif(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*</li>\s*<li>"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                        elseif(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*<p class=\"heading\">"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                    }
                }

                if(preg_match('"<p class=\"heading\">LIFE HISTORY AND POPULATION BIOLOGY</p>(.*?)<p class=\"heading\">PHYSICAL TOLERANCES</p>"si',$contents,$section)){
                    $habTitleArr = array();
                    if(preg_match_all('"<p class=\"title\">(.*?)\s*</p>"si',$section[1],$t)){
                        foreach($t[1] as $text){
                            $habTitleArr[] = $text;
                        }
                    }

                    foreach($habTitleArr as $title){
                        if(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*<p class=\"title\">"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                        elseif(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*</li>\s*<li>"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                        elseif(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*<p class=\"heading\">"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                    }
                }

                if(preg_match('"<p class=\"heading\">PHYSICAL TOLERANCES</p>(.*?)<p class=\"heading\">COMMUNITY ECOLOGY</p>"si',$contents,$section)){
                    $habTitleArr = array();
                    if(preg_match_all('"<p class=\"title\">(.*?)\s*</p>"si',$section[1],$t)){
                        foreach($t[1] as $text){
                            $habTitleArr[] = $text;
                        }
                    }

                    foreach($habTitleArr as $title){
                        if(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*<p class=\"title\">"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                        elseif(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*</li>\s*<li>"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                        elseif(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*<p class=\"heading\">"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                    }
                }

                if(preg_match('"<p class=\"heading\">COMMUNITY ECOLOGY</p>(.*?)<p class=\"heading\">ADDITIONAL INFORMATION</p>"si',$contents,$section)){
                    $habTitleArr = array();
                    if(preg_match_all('"<p class=\"title\">(.*?)\s*</p>"si',$section[1],$t)){
                        foreach($t[1] as $text){
                            $habTitleArr[] = $text;
                        }
                    }

                    foreach($habTitleArr as $title){
                        if(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*<p class=\"title\">"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                        elseif(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*</li>\s*<li>"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                        elseif(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*<p class=\"heading\">"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                    }
                }

                if(preg_match('"<p class=\"heading\">ADDITIONAL INFORMATION</p>(.*?)<p class=\"heading\">REFERENCES</p>"si',$contents,$section)){
                    $habTitleArr = array();
                    if(preg_match_all('"<p class=\"title\">(.*?)\s*</p>"si',$section[1],$t)){
                        foreach($t[1] as $text){
                            $habTitleArr[] = $text;
                        }
                    }

                    foreach($habTitleArr as $title){
                        if(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*<p class=\"title\">"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                        elseif(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*</li>\s*<li>"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                        elseif(preg_match('"<p class=\"title\">'.$title.'</p>(.*?)\s*<p class=\"heading\">"si',$section[1],$m)){
                            echo $title . ': ' . $m[1];
                        }
                    }
                }

                if(preg_match('"<p class=\"heading\">REFERENCES</p>(.*?)</li>\s*</ol>"si',$contents,$section)){
                    echo 'REFERENCES: ' . $section[1] . '<br />';
                }

                /*if(preg_match_all('"<p class=\"heading\">(.*?)\s*</p>"si',$contents,$m)){
                    //$i = 1;
                    foreach($m[0] as $text){
                        //echo $i . ': ' . $text . '<br />';
                        //$i++;

                        if(!in_array($text,$this->headingArr)){
                            $this->headingArr[] = $text;
                        }
                    }
                }*/

                /*if(preg_match_all('"<p class=\"title\">(.*?)\s*</p>"si',$contents,$m)){
                    $i = 1;
                    foreach($m[0] as $text){
                        echo $i . ': ' . $text . '<br />';
                        $i++;
                    }
                }*/

                /*if(preg_match('"<tr>\s*<td class=\"label\">Common Name:</td>\s*<td>(.*?)</td>\s*</tr>"si',$contents,$m)){
                    $commonNameArr = explode("<br />", $m[1]);
                    //$this->processCommonNames($commonNameArr);
                }*/

                /*if(preg_match('"<tr>\s*<td class=\"label\">Synonymy:</td>\s*<td>(.*?)</td>\s*</tr>"si',$contents,$m)){
                    $synonymArr = explode("<br />", $m[1]);
                    //$this->processSynonyms($synonymArr);
                }*/

                /*if((strpos($contents, 'Synonymy:')) !== false) {
                    if((strpos($contents, '<tr><td class="label">Synonymy:</td>')) === false) {
                        echo $this->file . '<br />';
                    }
                }*/

                /*if((strpos($contents, 'Synonymy:')) !== false) {
                    if(preg_match('"<tr>\s*<td class=\"label\">Synonymy:</td>(.*?)</tr>"si',$contents,$m)){
                        echo $this->file . '<br />';
                    }
                }*/

                //Get Images
                /*if(preg_match_all("'<a href=\"../images/(.*?)</span>'si",$contents,$m)){
                    foreach($m[0] as $chunklet){
                        $imgFile = '';
                        $caption = '';
                        if(preg_match("'<img src=\"../images/(.*?)\"'si",$chunklet,$imgText)){
                            $imgFile = $imgText[0];
                        }
                        if(!$imgFile){
                            if(preg_match("'<img border=\"0\" src=\"../images/(.*?)\"'si",$chunklet,$imgText)){
                                $imgFile = $imgText[0];
                            }
                        }
                        if(($pos = strpos($chunklet, '<span class="caption">')) !== false) {
                            $caption = substr($chunklet, $pos + 22);
                        }
                        echo $imgFile . '<br />';
                        echo $caption . '<br />';

                        $this->saveImageRecord($sciname,$imgFile,$caption);
                    }
                }
                elseif(preg_match_all("'<img border=\"0\"(.*?)</span>'si",$contents,$m)){
                    foreach($m[0] as $chunklet){
                        $imgFile = '';
                        $caption = '';
                        if(preg_match("'src=\"../images/(.*?)\"'si",$chunklet,$imgText)){
                            $imgFile = $imgText[0];
                        }
                        if(($pos = strpos($chunklet, '<span class="caption">')) !== false) {
                            $caption = substr($chunklet, $pos + 22);
                        }
                        echo $imgFile . '<br />';
                        echo $caption . '<br />';
                        $this->saveImageRecord($sciname,$imgFile,$caption);
                    }
                }
                elseif(preg_match_all("'<a href=\"images/(.*?)</span>'si",$contents,$m)){
                    foreach($m[0] as $chunklet){
                        $imgFile = '';
                        $caption = '';
                        if(preg_match("'src=\"images/(.*?)\"'si",$chunklet,$imgText)){
                            $imgFile = $imgText[0];
                        }
                        if(($pos = strpos($chunklet, '<span class="caption">')) !== false) {
                            $caption = substr($chunklet, $pos + 22);
                        }
                        echo $imgFile . '<br />';
                        echo $caption . '<br />';
                        $this->saveImageRecord($sciname,$imgFile,$caption);
                    }
                }
                elseif(preg_match_all("'<p class=\"CAPTION\"><img(.*?)</p>'si",$contents,$m)){
                    foreach($m[0] as $chunklet){
                        $imgFile = '';
                        $caption = '';
                        if(preg_match("'src=\"../images/(.*?)\"'si",$chunklet,$imgText)){
                            $imgFile = $imgText[0];
                        }
                        if(($pos = strpos($chunklet, '">')) !== false) {
                            $caption = substr($chunklet, $pos + 22);
                        }
                        echo $imgFile . '<br />';
                        echo $caption . '<br />';
                        $this->saveImageRecord($sciname,$imgFile,$caption);
                    }
                }
                elseif(preg_match_all("'<img src=\"../images(.*?)</span>'si",$contents,$m)){
                    foreach($m[0] as $chunklet){
                        $imgFile = '';
                        $caption = '';
                        if(preg_match("'/(.*?)\"'si",$chunklet,$imgText)){
                            $imgFile = $imgText[0];
                        }
                        if(($pos = strpos($chunklet, '<span class="caption">')) !== false) {
                            $caption = substr($chunklet, $pos + 22);
                        }
                        echo $imgFile . '<br />';
                        echo $caption . '<br />';
                        $this->saveImageRecord($sciname,$imgFile,$caption);
                    }
                }
                elseif(preg_match_all("'<img border=\"0\"(.*?)</font>'si",$contents,$m)){
                    foreach($m[0] as $chunklet){
                        $imgFile = '';
                        $caption = '';
                        if(preg_match("'src=\"../images/(.*?)\"'si",$chunklet,$imgText)){
                            $imgFile = $imgText[0];
                        }
                        if(($pos = strpos($chunklet, '<font size="2">')) !== false) {
                            $caption = substr($chunklet, $pos + 15);
                        }
                        if(($pos = strpos($chunklet, '<font color="#000080" size="2">')) !== false) {
                            $caption = substr($chunklet, $pos + 15);
                        }
                        if(($pos = strpos($chunklet, '<font size="2" color="#000080">')) !== false) {
                            $caption = substr($chunklet, $pos + 31);
                        }
                        echo $imgFile . '<br />';
                        echo $caption . '<br />';
                        $this->saveImageRecord($sciname,$imgFile,$caption);
                    }
                }
                else{
                    //echo $this->file . '<br />';
                }*/

                //Get Species Name & Author
                /*if(preg_match('"<td class=\"label\">Species Name:</td>\s*<td>(.*?)</td>"si',$contents,$m)){
                    if(preg_match('"<span class=\"specie-name\">(.*?)</span>"si',$m[1],$scienName)){
                        $scientificName = $scienName[1];
                        //echo $scientificName . '<br />';
                    }
                    if(($pos = strpos($m[1], "</span>")) !== false) {
                        $author = substr($m[1], $pos + 7);
                        //echo $author . '<br />';
                    }
                }

                if(!$scientificName){
                    if(preg_match('"<td class=\"label\">Species Name:</td>\s*<td>(.*?)</td>"si',$contents,$m)){
                        if(preg_match('"<span class=\"specie-name\">(.*?)</span>"si',$m[1],$scienName)){
                            $scientificName = $scienName[1];
                            //echo $scientificName . '<br />';
                        }
                        if(($pos = strpos($m[1], "</span>")) !== false) {
                            $author = substr($m[1], $pos + 7);
                            //echo $author . '<br />';
                        }
                    }
                }

                if(!$scientificName){
                    echo $this->file . '<br />';
                }*/

                //echo json_encode($this->subheadingArr);
                //echo json_encode($this->headingArr);



                //Get common name
                /*if(preg_match("'<h3>Common name</h3>\s*<p>(.*?)</p>'si",$contents,$m)){
                    $commonArr = explode(',',$m[1]);
                    foreach($commonArr as $v){
                        if(trim($v)){
                            $parsedData['commonName'][] = trim($v);
                        }
                    }
                }*/

                //Get ident_adult
                /*if(preg_match("'<h3>Adult</h3>\s*<p>(.*?)</p>'si",$contents,$m)){
                    if(trim($m[1])){
                        $parsedData['descAdult'][] = preg_replace( '/\s+/', ' ',trim($m[1]));
                    }
                }*/
            }
        }
    }

    public function processCommonNames($inArr){
        $currentNameArr = $this->getCurrentCommonNames();
        $tempArr = array();
        foreach($inArr as $newTempName){
            $tempArr[] = strtolower($newTempName);
        }
        foreach($currentNameArr as $id => $currentName){
            if(in_array(strtolower($currentName),$tempArr)){
                echo $currentName . '<br />';
                foreach($inArr as $newTemp2Name){
                    if(strtolower($newTemp2Name) == strtolower($currentName)){
                        $sql = 'UPDATE taxavernaculars SET VernacularName = "'.$newTemp2Name.'" WHERE VID = '.$id;
                        //echo $sql . '<br />';
                        if(!$this->conn->query($sql)){
                            $status = false;
                        }
                        $index = array_search($newTemp2Name,$inArr,true);
                        unset($inArr[$index]);
                    }
                }
            }
        }
        foreach($inArr as $newName){
            if($newName != 'None'){
                $sql = 'INSERT INTO taxavernaculars(`TID`,`VernacularName`,`Language`,`langid`,`SortSequence`) VALUES ('.$this->tid.',"'.trim($newName).'","English",1,1) ';
                //echo $sql . '<br />';
                if(!$this->conn->query($sql)){
                    $status = false;
                }
            }
        }
    }

    public function processSynonyms($inArr){
        foreach($inArr as $newSynonym){
            $synAuthor = '';
            if((strpos($newSynonym, '</i>')) !== false) {
                $synNameArr = explode("</i>", $newSynonym);
                $synSciname = trim(str_replace('<i>','',$synNameArr[0]));
                $synAuthor = trim($synNameArr[1]);
            }
            elseif((strpos($newSynonym, '</em>')) !== false) {
                $synNameArr = explode("</em>", $newSynonym);
                $synSciname = trim(str_replace('<em>','',$synNameArr[0]));
                $synAuthor = trim($synNameArr[1]);
            }
            else{
                $synSciname = trim($newSynonym);
            }
            if(substr($synAuthor, -1) === ','){
                $synAuthor = substr($synAuthor, 0, -1);
            }
            if($synSciname && $synSciname != 'None'){
                $sql = 'INSERT INTO `scraping-synonyms`(`tid`,`synSciname`,`synAuthor`,`acceptedName`) VALUES ('.$this->tid.',"'.$synSciname.'",'.($synAuthor?'"'.$synAuthor.'"':'null').',"'.$this->sciname.'") ';
                //echo $sql . '<br />';
                if(!$this->conn->query($sql)){
                    $status = false;
                }
            }
        }
    }

    public function getCurrentCommonNames(){
        $returnArr = array();
        $sql = 'SELECT VID, VernacularName FROM taxavernaculars WHERE TID = '.$this->tid;
        $rs = $this->conn->query($sql);
        if($r = $rs->fetch_object()){
            $returnArr[$r->VID] = $r->VernacularName;
        }
        $rs->free();
        return $returnArr;
    }

    public function saveImageRecord($sciname,$image,$caption){
        $status = true;
        $sql = "INSERT INTO `scraping-images`(`sciname`,`imageFile`,`caption`) VALUES ('".$this->cleanInStr($sciname)."','".$this->cleanInStr($image)."','".$this->cleanInStr($caption)."') ";
        if(!$this->conn->query($sql)){
            $status = false;
        }
        return $status;
    }

    public function saveSubheadingArr(){
        $status = true;
        foreach($this->subheadingArr as $heading){
            $sql = "INSERT INTO `scraping-subheading`(`heading`) VALUES ('".$this->cleanInStr($heading)."') ";
            if(!$this->conn->query($sql)){
                $status = false;
            }
        }
        return $status;
    }

    public function saveSciname($name){
        $status = true;
        $sql = "INSERT INTO `scraping-taxa-scinames`(`sciname`) VALUES ('".$this->cleanInStr($name)."') ";
        if(!$this->conn->query($sql)){
            $status = false;
        }
        return $status;
    }

    public function saveHeadingArr(){
        $status = true;
        foreach($this->subheadingArr as $heading){
            $sql = "INSERT INTO `symbiota`.`scraping-heading`(`heading`) VALUES ('".$this->cleanInStr($heading)."') ";
            if(!$this->conn->query($sql)){
                $status = false;
            }
        }
        return $status;
    }

    public function saveCommonName($commonStr){
        //Target tables: taxavernaculars
        $status = true;
        if($commonStr && $this->tid){
            $sql = 'INSERT INTO taxavernaculars (tid,varnacularname,source) '.
                'VALULES('.$this->tid.',"'.$this->cleanInStr($commonStr).'","STRI") ';
            if(!$this->conn->query($sql)){
                $status = false;
            }
        }
        return $status;
    }

    //Setters and getters
    public function setFile($file){
        $this->file = $file;
    }

    public function setTid(){
        $sql = 'SELECT tid FROM taxa WHERE sciname = "'.$this->sciname.'"';
        $rs = $this->conn->query($sql);
        if($r = $rs->fetch_object()){
            $this->tid = $r->tid;
        }
        $rs->free();
    }

    public function getTid(){
        return $this->tid;
    }

    private function urlExists($url) {
        $exists = false;
        if(file_exists($url)){
            return true;
        }

        if(!$exists){
            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_HEADER, false);
            curl_setopt($handle, CURLOPT_FAILONERROR, true);
            curl_setopt($handle, CURLOPT_HTTPHEADER, Array('User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15') ); // request as if Firefox
            curl_setopt($handle, CURLOPT_NOBODY, true);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
            $exists = curl_exec($handle);
            curl_close($handle);
        }

        if(!$exists){
            $exists = (@fclose(@fopen($url, 'rb')));
        }
        return $exists;
    }

    private function cleanInStr($str){
        $newStr = trim($str);
        $newStr = preg_replace('/\s\s+/', ' ',$newStr);
        $newStr = $this->conn->real_escape_string($newStr);
        return $newStr;
    }
}

$parser = new ScrapeManager();
$files = $parser->getTaxaFileList();

if(isset($_POST['formsubmit'])){
    if($_POST['formsubmit'] === 'Parse File') {
        $parser->setFile($_POST['file']);
        $result = $parser->parseUrl();
        //echo json_encode($result);
    }

    if($_POST['formsubmit'] === 'Parse Everything') {
        $parser->parseAllFiles($files);
    }


    /*if(array_key_exists('link',$resultArr)){
        ?>
        <div>
            <div><b>Links</b></div>
            <div style="margin:15px;">
                <?php
                echo '<form name="linksaveform" method="post" action="" target="_blank" >';
                $linkArr = $resultArr['link'];
                foreach($linkArr as $k => $linkStr){
                    echo '<input type="checkbox" name="num-'.$k.'" value="1" /> ';
                    //Allow user to edit value before submitting
                    echo '<input type="text" style="width:300px;" name="val-'.$k.'" value="'.$linkStr['name'].'" /><br />';
                    echo '<input type="text" style="width:700px;" name="val-'.$k.'" value="'.$linkStr['url'].'" /><br />';
                }
                echo '<input type="hidden" name="tid" value="'.$parser->getTid().'" />';
                echo '<input type="submit" name="formsubmit" value="Save Links" ';
                echo '</form>';
                ?>
            </div>
        </div>
        <?php
    }*/
}
?>

<form method="post" action="scraper.php" >
    <select name="file" style="width:200px" >
        <option value="">Select File</option>
        <option value="">----</option>
        <?php
        foreach($files as $file){
            echo '<option value="'.$file.'">'.$file.'</option>';
        }
        ?>
    </select>
    <input type="submit" name="formsubmit" value="Parse File" />
    <input type="submit" name="formsubmit" value="Parse Everything" />
</form>
