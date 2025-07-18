<?php
class DwcArchiverImage{

    public static function getImageArr($schemaType): array
    {
        $fieldArr['coreid'] = 'o.occid';
        $termArr['identifier'] = 'http://purl.org/dc/terms/identifier';
        $fieldArr['identifier'] = 'IFNULL(i.originalurl,i.url) as identifier';
        $termArr['accessURI'] = 'http://rs.tdwg.org/ac/terms/accessURI';
        $fieldArr['accessURI'] = 'IFNULL(i.originalurl,i.url) as accessURI';
        $termArr['thumbnailAccessURI'] = 'http://rs.tdwg.org/ac/terms/thumbnailAccessURI';
        $fieldArr['thumbnailAccessURI'] = 'i.thumbnailurl as thumbnailAccessURI';
        $termArr['goodQualityAccessURI'] = 'http://rs.tdwg.org/ac/terms/goodQualityAccessURI';
        $fieldArr['goodQualityAccessURI'] = 'i.url as goodQualityAccessURI';
        $termArr['rights'] = 'http://purl.org/dc/terms/rights';
        $fieldArr['rights'] = 'c.rights';
        $termArr['Owner'] = 'http://ns.adobe.com/xap/1.0/rights/Owner';	//Institution name
        $fieldArr['Owner'] = 'IFNULL(c.rightsholder,CONCAT(c.collectionname," (",CONCAT_WS("-",c.institutioncode,c.collectioncode),")")) AS owner';
        $termArr['UsageTerms'] = 'http://ns.adobe.com/xap/1.0/rights/UsageTerms';	//Creative Commons BY-SA 3.0 license
        $fieldArr['UsageTerms'] = 'i.copyright AS usageterms';
        $termArr['WebStatement'] = 'http://ns.adobe.com/xap/1.0/rights/WebStatement';	//http://creativecommons.org/licenses/by-nc-sa/3.0/us/
        $fieldArr['WebStatement'] = 'c.accessrights AS webstatement';
        $termArr['caption'] = 'http://rs.tdwg.org/ac/terms/caption';
        $fieldArr['caption'] = 'i.caption';
        $termArr['comments'] = 'http://rs.tdwg.org/ac/terms/comments';
        $fieldArr['comments'] = 'i.notes';
        $termArr['providerManagedID'] = 'http://rs.tdwg.org/ac/terms/providerManagedID';	//GUID
        $fieldArr['providerManagedID'] = 'g.guid AS providermanagedid';
        $termArr['MetadataDate'] = 'http://ns.adobe.com/xap/1.0/MetadataDate';	//timestamp
        $fieldArr['MetadataDate'] = 'i.initialtimestamp AS metadatadate';
        $termArr['format'] = 'http://purl.org/dc/terms/format';		//jpg
        $fieldArr['format'] = 'i.format';
        $termArr['associatedSpecimenReference'] = 'http://rs.tdwg.org/ac/terms/associatedSpecimenReference';	//reference url in portal
        $fieldArr['associatedSpecimenReference'] = '';
        $termArr['type'] = 'http://purl.org/dc/terms/type';		//StillImage
        $fieldArr['type'] = '';
        $termArr['subtype'] = 'http://rs.tdwg.org/ac/terms/subtype';		//Photograph
        $fieldArr['subtype'] = '';
        $termArr['metadataLanguage'] = 'http://rs.tdwg.org/ac/terms/metadataLanguage';	//en
        $fieldArr['metadataLanguage'] = '';

        if($schemaType === 'backup') {
            $fieldArr['rights'] = 'i.copyright';
        }

        $retArr['terms'] = self::trimBySchemaType($termArr, $schemaType);
        $retArr['fields'] = self::trimBySchemaType($fieldArr, $schemaType);
        return $retArr;
    }

    private static function trimBySchemaType($imageArr, $schemaType): array
    {
        $trimArr = array();
        if($schemaType === 'backup'){
            $trimArr = array('Owner', 'UsageTerms', 'WebStatement');
        }
        return array_diff_key($imageArr,array_flip($trimArr));
    }

    public static function getSqlImages($fieldArr, $conditionSql, $redactLocalities, $rareReaderArr): string
    {
        $sql = '';
        if($fieldArr && $conditionSql){
            $sqlFrag = '';
            foreach($fieldArr as $fieldName => $colName){
                if($colName) {
                    $sqlFrag .= ', ' . $colName;
                }
            }
            $sql = 'SELECT '.trim($sqlFrag,', ').
                ' FROM images AS i INNER JOIN omoccurrences AS o ON i.occid = o.occid '.
                'INNER JOIN omcollections AS c ON o.collid = c.collid '.
                'INNER JOIN guidimages AS g ON i.imgid = g.imgid '.
                'INNER JOIN guidoccurrences AS og ON o.occid = og.occid '.
                'LEFT JOIN taxa AS t ON i.tid = t.tid ';
            if(stripos($conditionSql,' te.')){
                $sql .= 'LEFT JOIN taxaenumtree AS te ON i.tid = te.tid ';
            }
            if(strpos($conditionSql,'v.clid')){
                $sql .= 'LEFT JOIN fmvouchers AS v ON o.occid = v.occid ';
            }
            if(strpos($conditionSql,'p.point')){
                $sql .= 'LEFT JOIN omoccurpoints AS p ON o.occid = p.occid ';
            }
            $sql .= $conditionSql;
            if($redactLocalities){
                if($rareReaderArr){
                    $sql .= 'AND (o.localitySecurity = 0 OR o.localitySecurity IS NULL OR c.collid IN('.implode(',',$rareReaderArr).')) ';
                }
                else{
                    $sql .= 'AND (o.localitySecurity = 0 OR o.localitySecurity IS NULL) ';
                }
            }
        }
        //echo $sql; exit;
        return $sql;
    }
}
