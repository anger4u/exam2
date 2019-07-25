<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);


class SimpleComp extends CBitrixComponent {
    
    // переопределение параметров
    private function HandlerArParams ()
    {
        $this -> arParams['IBLOCK_CATALOG_ID'] = (int) $this -> arParams['IBLOCK_CATALOG_ID'];
        $this -> arParams['IBLOCK_NEWS_ID'] = (int) $this -> arParams['IBLOCK_NEWS_ID'];
        
        if(!$this -> arParams['CACHE_TIME']) {
            $this -> arParams['CACHE_TIME'] = 3600000;
        }
    }
    
    private function SetarResult ()
    {
        // получение разделов инфоблока продукция      
        $arFilter = array('IBLOCK_ID' => $this -> arParams['IBLOCK_CAT_ID'], 'ACTIVE' => 'Y', '!' . $this -> arParams['USER_PROPERTY'] => false);
        $arSelect = array('ID', 'IBLOCK_ID', 'NAME', $this -> arParams['USER_PROPERTY']);
        $r = CIBlockSection::GetList (array(), $arFilter, false, $arSelect);
        while($res = $r -> Fetch())
        {
            $mixed[] = $res;
        }
        
        //  получаем массив ID разделов с привязкой      
        $arSectionsId = array_column($mixed, 'ID');
        
        // получение элементов инфоблока продукция       
        $arFilter = array('IBLOCK_ID' => $this -> arParams['IBLOCK_CAT_ID'], 'ACTIVE' => 'Y', 'IBLOCK_SECTION_ID' => $arSectionsId);
        // 
        $arSelect = array('ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_PRICE', 'PROPERTY_ARTNUMBER', 'PROPERTY_MATERIAL', 'IBLOCK_SECTION_ID');
        $r = CIBlockElement::GetList (array(), $arFilter, false, false, $arSelect);
        $count = 0;
        while ($res = $r->Fetch())
        {
            // распределение елементов по разделам     
            foreach ($mixed as &$item)
            {
                if ($res['IBLOCK_SECTION_ID'] == $item['ID'])
                {
                    $item['ELEM_ITEMS'][] = $res;
                }
            }
            $count++;
        }
        $this -> arResult['COUNT'] = $count;
        
        // получение списка новостей       
        $arFilter = array('IBLOCK_ID' => $this->arParams['IBLOCK_NEWS_ID'], 'ACTIVE' => 'Y');
        $arSelect = array('ID', 'IBLOCK_ID', 'NAME', 'DATE_ACTIVE_FROM');
        $r = CIBlockElement::GetList (array(), $arFilter, false, false, $arSelect);
        $i = 0;
        while ($res = $r->Fetch())
        {
            // занесение новостей в arResult
            $this -> arResult['NEWS_ITEMS'][$i] = $res;
            // перебор разделов
            foreach ($mixed as &$item1)
            {
                // перебор елементов текущего раздела
                foreach ($item1['UF_NEWS_LINK'] as $item2)
                {
                    if ($item2 == $res['ID'])
                    {
                        $this -> arResult['NEWS_ITEMS'][$i]['SECT_ITEMS'][] = $item1;
                    }
                }
            }
            $i++;
        }
    }
    
    // исполнение компонента 
    public function ExecuteComponent ()
    {
        $this -> HandlerArParams();
        if (!CModule::IncludeModule('iblock'))
        {
            return;
        }
        if ($this -> StartResultCache())
        {
            $this -> SetarResult();
            $this -> IncludeComponentTemplate();
        }
        global $APPLICATION;
        $APPLICATION -> SetTitle(GetMessage('COUNT') . $this -> arResult['COUNT'] . ']');
    }
}

?>