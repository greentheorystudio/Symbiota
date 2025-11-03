<?php
include_once(__DIR__ . '/../models/Collections.php');

class RssService {

    public function getPortalCollectionRss(): string
    {
        $newDoc = new DOMDocument('1.0', 'UTF-8');
        $rootElem = $newDoc->createElement('rss');
        $rootAttr = $newDoc->createAttribute('version');
        $rootAttr->value = '2.0';
        $rootElem->appendChild($rootAttr);
        $newDoc->appendChild($rootElem);
        $channelElem = $newDoc->createElement('channel');
        $rootElem->appendChild($channelElem);
        $titleElem = $newDoc->createElement('title');
        $titleElem->appendChild($newDoc->createTextNode($GLOBALS['DEFAULT_TITLE'] . ' Collections RSS Feed'));
        $channelElem->appendChild($titleElem);
        $urlPathPrefix = ($_SERVER['SERVER_PORT'] === 443 ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $GLOBALS['CLIENT_ROOT'] . '/';
        $linkElem = $newDoc->createElement('link');
        $linkElem->appendChild($newDoc->createTextNode($urlPathPrefix));
        $channelElem->appendChild($linkElem);
        $descriptionElem = $newDoc->createElement('description');
        $descriptionElem->appendChild($newDoc->createTextNode($GLOBALS['DEFAULT_TITLE'] . ' Occurrence Data Collections RSS Feed'));
        $channelElem->appendChild($descriptionElem);
        $languageElem = $newDoc->createElement('language','en-us');
        $channelElem->appendChild($languageElem);
        $collectionsArr = (new Collections)->getCollectionArr();
        foreach($collectionsArr as $cArr){
            if((int)$cArr['recordcnt'] > 0){
                $itemElem = $newDoc->createElement('item');
                $itemAttr = $newDoc->createAttribute('collid');
                $itemAttr->value = $cArr['collid'];
                $itemElem->appendChild($itemAttr);
                $instCode = $cArr['institutioncode'] ?: '';
                if($cArr['collectioncode']) {
                    $instCode .= ($instCode ? ':' : '') . $cArr['collectioncode'];
                }
                $title = $cArr['collectionname'] . ($instCode ? ' (' . $instCode . ')' : '');
                $itemTitleElem = $newDoc->createElement('title');
                $itemTitleElem->appendChild($newDoc->createTextNode($title));
                $itemElem->appendChild($itemTitleElem);
                $itemNameElem = $newDoc->createElement('name');
                $itemNameElem->appendChild($newDoc->createTextNode($cArr['collectionname']));
                $itemElem->appendChild($itemNameElem);
                if($GLOBALS['CLIENT_ROOT'] && strncmp($cArr['icon'], '/', 1) === 0){
                    $cArr['icon'] = $GLOBALS['CLIENT_ROOT'] . $cArr['icon'];
                }
                if($cArr['icon'] && strncmp($cArr['icon'], '/', 1) === 0){
                    $cArr['icon'] = $urlPathPrefix . $cArr['icon'];
                }
                $iconElem = $newDoc->createElement('image');
                if($cArr['icon']){
                    $iconElem->appendChild($newDoc->createTextNode($cArr['icon']));
                    $itemElem->appendChild($iconElem);
                }
                $descTitleElem = $newDoc->createElement('description');
                $descTitleElem->appendChild($newDoc->createTextNode(($cArr['fulldescription'] ? strip_tags($cArr['fulldescription']) : '')));
                $itemElem->appendChild($descTitleElem);
                $guidElem = $newDoc->createElement('guid');
                $guidElem->appendChild($newDoc->createTextNode($cArr['collectionguid']));
                $itemElem->appendChild($guidElem);
                $emlElem = $newDoc->createElement('emllink');
                $emlElem->appendChild($newDoc->createTextNode($urlPathPrefix . 'collections/datasets/emlhandler.php?collid=' . $cArr['collid']));
                $itemElem->appendChild($emlElem);
                $link = $cArr['dwcaurl'];
                if(!$link){
                    $link = $urlPathPrefix . 'collections/misc/collprofiles.php?collid=' . $cArr['collid'];
                }
                $typeTitleElem = $newDoc->createElement('type','DWCA');
                $itemElem->appendChild($typeTitleElem);

                $linkTitleElem = $newDoc->createElement('link');
                $linkTitleElem->appendChild($newDoc->createTextNode($link));
                $itemElem->appendChild($linkTitleElem);
                $dateStr = '';
                if($cArr['managementtype'] === 'Live Data'){
                    $dateStr = date('D, d M Y H:i:s');
                }
                elseif($cArr['uploaddate']){
                    $dateStr = date('D, d M Y H:i:s', strtotime($cArr['uploaddate']));
                }
                $pubDateTitleElem = $newDoc->createElement('pubDate');
                $pubDateTitleElem->appendChild($newDoc->createTextNode($dateStr));
                $itemElem->appendChild($pubDateTitleElem);
                $channelElem->appendChild($itemElem);
            }
        }
        return $newDoc->saveXML();
    }
}
