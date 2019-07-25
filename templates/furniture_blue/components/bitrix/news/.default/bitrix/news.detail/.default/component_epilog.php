<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if (!empty($arResult['CANONICAL'])) {
    $APPLICATION -> SetPageProperty('ID_CAN', '<link rel="canonical" href="' . $arResult['CANONICAL'] . '">');
}
?>