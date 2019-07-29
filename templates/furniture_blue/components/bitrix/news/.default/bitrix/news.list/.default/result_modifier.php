<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if ($arParams['SPEC_DATE'] == 'Y') {
    $arResult['SPEC_DATE'] = $arResult['ITEMS'][0]['ACTIVE_FROM'];
    
    $cp = $this -> __component -> SetResultCacheKeys(array('SPEC_DATE'));
}
?>