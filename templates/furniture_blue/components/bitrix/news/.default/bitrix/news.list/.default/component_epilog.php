<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if(!empty($arResult['SPEC_DATE'])) {
    $APPLICATION->SetPageProperty('SPECDATE', $arResult['SPEC_DATE']);
}
?>