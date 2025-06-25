<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use PhpOffice\PhpWord\PhpWord;

class PhpWordService {

    public static function addFontStyle($phpWord, $name, $styleArr): void
    {
        $phpWord->addFontStyle($name, $styleArr);
    }

    public static function addImage($parent, $path, $styleArr): void
    {
        $parent->addImage($path, $styleArr);
    }

    public static function addLine($parent, $styleArr): void
    {
        $parent->addLine($styleArr);
    }

    public static function addLink($parent, $target, $styleName): void
    {
        $parent->addLink($target, $styleName);
    }

    public static function addParagraphStyle($phpWord, $name, $styleArr): void
    {
        $phpWord->addParagraphStyle($name, $styleArr);
    }

    public static function addTableRow($parent): void
    {
        $parent->addRow();
    }

    public static function addTableStyle($phpWord, $name, $styleArr, $colStyleArr): void
    {
        $phpWord->addTableStyle($name, $styleArr, $colStyleArr);
    }

    public static function addText($parent, $text, $styleName): void
    {
        $parent->addText($text, $styleName);
    }

    public static function addTextBreak($parent): void
    {
        $parent->addTextBreak();
    }

    public static function getPhpWord(): PhpWord
    {
        return new PhpWord();
    }

    public static function getSection($phpWord, $styleArr)
    {
        return $phpWord->addSection($styleArr);
    }

    public static function getTable($parent, $styleName)
    {
        return $parent->addTable($styleName);
    }

    public static function getTableCell($parent, $width, $styleArr)
    {
        return $parent->addCell($width, $styleArr);
    }

    public static function getTextRun($parent, $styleName)
    {
        return $parent->addTextRun($styleName);
    }

    public static function saveDocument($phpWord, $fullPath): void
    {
        $phpWord->save($fullPath, 'Word2007');
    }
}
