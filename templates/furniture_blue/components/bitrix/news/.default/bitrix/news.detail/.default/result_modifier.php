<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if (intval($arParams['ID_CAN']) > 0) {
    $arSelect = Array('NAME');
    $arFilter = Array("IBLOCK_ID"=>IntVal($arParams['ID_CAN']), "ACTIVE"=>"Y", 'PROPERTY_NEW' => intval($arResult['ID']));
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
    
    if (!empty($res)) {
        while($ob = $res->Fetch()){  
            $arFields = $ob;
        }
            
    $arResult['CANONICAL'] = $arFields['NAME'];
    
    $this -> __component -> SetResultCacheKeys(array('CANONICAL'));
    }
}
?>