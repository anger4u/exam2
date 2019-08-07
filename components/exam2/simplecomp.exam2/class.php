<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class SimpleComp2 extends CBitrixComponent
{
    private $firms = array();
    private $elems = array();

    // проверка подключения модулей
    private function CheckModules()
    {
        if (!CModule::IncludeModule('iblock'))
        {
            return;
        }
    }
    
    // получение и переопределение параметров
    private function handlerParams()
    {
        $this->arParams['PROD_IBLOCK_ID'] = (int) $this->arParams['PROD_IBLOCK_ID'];
        $this->arParams['CLASS_IBLOCK_ID'] = (int) $this->arParams['CLASS_IBLOCK_ID'];
        
        if(!$this->arParams['CACHE_TIME']) {
            $this->arParams['CACHE_TIME'] = 3600000;
        }
        // проверка на наличие параметра 'F' 
        if(isset($_GET["F"])){
            $this->arParams['CACHE_TIME'] = 0;
        }
    }
    
    // запросы
    private function firmsList()
    {
        $this->firms = [];
        // получение елементов классификатора
        $arFilter = array(
            'IBLOCK_ID' => $this -> arParams['CLASS_IBLOCK_ID'],
            'ACTIVE' => 'Y',
            'CHECK_PERMISSIONS' => 'Y'
        );
        $arSelect = array(
            'ID',
            'IBLOCK_ID',
            'NAME'
        );
        $arNavParams = Array(
            "nPageSize" => (int) $this->arParams["NAV_COUNT"],
            "bShowAll" => true,
        );
        $arNavigation = CDBResult::GetNavParams($arNavParams);
        $res = CIBlockElement::GetList(array(), $arFilter, false, $arNavigation, $arSelect);
        while ($ob = $res->Fetch())
        {
            $this->firms[] = $ob;
            //echo'<pre>';var_dump($ob);echo'</pre>';
        }
        
         $this->arResult["NAV_STRING"] = $res->GetPageNavStringEx($navComponentObject, "", "", true); //постраничная
        
        // получение елементов каталога и изменение url детального просмотра
        $arSort = array('NAME' => 'ASC', 'SORT' => 'ASC');
        // добавление фильтра F
        $filter = array();
        if(isset($_GET["F"])){
            $filter = array(
                "LOGIC" => "OR",
                array("<=PROPERTY_PRICE" => 1700, "PROPERTY_MATERIAL" => "Дерево, ткань"),
                array("<PROPERTY_PRICE" => 1500, "PROPERTY_MATERIAL" => "Металл, пластик"),
            );
        }
        $arFilter = array(
            'IBLOCK_ID' => $this->arParams['PROD_IBLOCK_ID'],
            'ACTIVE' => 'Y',
            'CHECK_PERMISSIONS' => 'Y',
            '!PROPERTY_'.$this->arParams['ELEMENT_PROP_CODE'] => false,
            $filter
        );
        $arSelect = array(
            'ID',
            'IBLOCK_ID',
            'NAME',
            'DETAIL_PAGE_URL',
            'PROPERTY_PRICE',
            'PROPERTY_MATERIAL',
            'PROPERTY_ARTNUMBER',
            'PROPERTY_'.$this->arParams['ELEMENT_PROP_CODE']
        );
        $res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
        $res->SetUrlTemplates($this->arParams['DETAIL_URL_TEMPLATE']);
        $arrPrice = array();
        while ($ob = $res->GetNext())
        {
            $arItem = $ob;
            $arrPrice[] = $ob['PROPERTY_PRICE_VALUE'];
            
            $arButtons = CIBlock::GetPanelButtons(
                $arItem['IBLOCK_ID'],
                $arItem["ID"],
                0,
                array("SECTION_BUTTONS"=>false, "SESSID"=>false)
            );
            
            $arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
            $arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
            $arItem["ADD_LINK"] = $arButtons["edit"]["add_element"]["ACTION_URL"];
            
            $this->elems[] = $arItem;
            
            //echo '<pre>';var_dump($arButtons);echo '</pre>';
        }
        $this->arResult['MIN_PRICE'] = min($arrPrice);
        $this->arResult['MAX_PRICE'] = max($arrPrice);
        //echo '<pre>';var_dump($arrPrice);echo '</pre>';
    }
    
    // формирование arResult
    private function setResult()
    {
        // проходим циклом по фирмам в arResult
        foreach($this->firms as $fKey => $firm)
        {
            $this->arResult['FIRMS'][$fKey]['NAME'] = $firm['NAME'];
            $this->arResult['FIRMS'][$fKey]['IBLOCK_ID'] = $firm['NAME'];
            // проходим циклом по елементам
            foreach($this->elems as $eKey => $elem)
            {
                // если элемент относится к этой фирме, добавляем елемент в фирму
                if(intval($firm['ID']) === intval($elem['PROPERTY_'.$this->arParams['ELEMENT_PROP_CODE'].'_VALUE']))
                {
                    $arElemProps = implode(' - ', array($elem['NAME'], $elem['PROPERTY_PRICE_VALUE'], $elem['PROPERTY_MATERIAL_VALUE'], $elem['PROPERTY_ARTNUMBER_VALUE']));
                    $this->arResult['FIRMS'][$fKey]['PRODUCTS'][$eKey]['PROPS'] = $arElemProps . '('.$elem['DETAIL_PAGE_URL'].')';
                    $this->arResult['FIRMS'][$fKey]['PRODUCTS'][$eKey]['ADD_LINK'] = $elem['ADD_LINK'];
                    $this->arResult['FIRMS'][$fKey]['PRODUCTS'][$eKey]['EDIT_LINK'] = $elem['EDIT_LINK'];
                    $this->arResult['FIRMS'][$fKey]['PRODUCTS'][$eKey]['DELETE_LINK'] = $elem['DELETE_LINK'];
                    $this->arResult['FIRMS'][$fKey]['PRODUCTS'][$eKey]['IBLOCK_ID'] = $elem['IBLOCK_ID'];
                    $this->arResult['FIRMS'][$fKey]['PRODUCTS'][$eKey]['ID'] = $elem['ID'];
                }
            }
        }
        //echo '<pre>';var_dump($this->arResult);echo '</pre>';
    }
    
    // ДОБАВЛЕНИЕ КНОПКИ В ПОПАП КОМПОНЕНТА
    private function bAdd()
    {
        $res = CIBlock::GetByID($this->arParams['PROD_IBLOCK_ID']);
        $arIcons = Array( //массив кнопок toolbar'a
            Array(
                "ID" => "SIMPLECOMP_BUTTON",
                "TITLE" => 'ИБ в Админке',
                "URL" => '/bitrix/admin/iblock_element_admin.php?IBLOCK_ID=' . $this->arParams['PROD_IBLOCK_ID'] .'&type='.$res->Fetch()['IBLOCK_TYPE_ID'].'&lang='. LANGUAGE_ID .'&find_el_y=Y',
                "IN_PARAMS_MENU" => true,
                //"PARAMS" => array(
                //    'width' => 770,
                //    'height' => 570,
                //    'resize' => true
                //),
                //"ICON" => "bx-context-toolbar-create-icon",
                //"IN_MENU" => true, //показать в подменю компонента
            ),
        );
        $this->AddIncludeAreaIcons($arIcons);
    }
    
    // ТЕГИРОВАННЫЙ КЕШ
    private function tCache()
    {
        if (defined('BX_COMP_MANAGED_CACHE') && is_object($GLOBALS['CACHE_MANAGER']))
        {
            $GLOBALS['CACHE_MANAGER'] -> RegisterTag('iblock_id_3');
        
        //$GLOBALS['CACHE_MANAGER'] -> RegisterTag('iblock_id_3');
        }
    }
    
    // исполнение компонента
    public function ExecuteComponent()
    {
        $this -> CheckModules();
         global $USER;
         
        if ($this -> StartResultCache(false, array($USER->GetGroups(), CDBResult::GetNavParams(Array(
            "nPageSize" => (int) $this->arParams["NAV_COUNT"],
            "bShowAll" => true,
        )))))
        {
            $this -> handlerParams();
            $this -> tCache();
            $this -> firmsList();
            $this -> setResult();
            $this -> SetResultCacheKeys(array(
                'FIRMS',
                'MIN_PRICE',    
                'MAX_PRICE',
            ));
            $this -> IncludeComponentTemplate();
        }
        else
	{
		$this->AbortResultCache();
	}
       
        global $APPLICATION;
        if ($APPLICATION->GetShowIncludeAreas())
		{
            $this -> bAdd();
        }
        $APPLICATION -> SetTitle(GetMessage('PAGE_TITLE'). '['.count($this->arResult['FIRMS']).']');
        $APPLICATION -> AddViewContent('min_price', $this->arResult['MIN_PRICE']);
        $APPLICATION -> AddViewContent('max_price', $this->arResult['MAX_PRICE']);
    }
}
?>