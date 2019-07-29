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
    private function receiveParams()
    {
        $this->arParams['PROD_IBLOCK_ID'] = intval($this->arParams['PROD_IBLOCK_ID']);
        $this->arParams['CLASS_IBLOCK_ID'] = intval($this->arParams['CLASS_IBLOCK_ID']);
        
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
        $res = CIBlockElement::GetList(array(), $arFilter, $arSelect);
        while ($ob = $res->Fetch())
        {
            $this->firms[] = $ob;
        }
        
        // получение елементов каталога и изменение url детального просмотра
        $arSort = array('NAME' => 'ASC', 'SORT' => 'ASC');
        // добавление фильтра F
        $filter = array();
        if(isset($_GET["F"])){
            $filtr = array(
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
        while ($ob = $res->GetNext())
        {
            $this->elems[] = $ob;
            
        }
    }
    
    // формирование arResult
    private function setResult()
    {
        // проходим циклом по фирмам в arResult
        foreach($this->firms as $key => $firm)
        {
            $this->arResult['FIRMS'][$key]['NAME'] = $firm['NAME']; 
            // проходим циклом по елементам
            foreach($this->elems as $elem)
            {
                // если элемент относится к этой фирме, добавляем елемент в фирму
                if(intval($firm['ID']) === intval($elem['PROPERTY_'.$this->arParams['ELEMENT_PROP_CODE'].'_VALUE']))
                {
                    $arElemProps = implode(' - ', array($elem['NAME'], $elem['PROPERTY_PRICE_VALUE'], $elem['PROPERTY_MATERIAL_VALUE'], $elem['PROPERTY_ARTNUMBER_VALUE']));
                    $this->arResult['FIRMS'][$key]['PRODUCTS'][] = $arElemProps . '('.$elem['DETAIL_PAGE_URL'].')';
                }
            }
        }
        
        //echo '<pre>';var_dump($this->arResult);echo '</pre>';
    }
    
    // исполнение компонента
    public function ExecuteComponent()
    {
        $this -> CheckModules();
         global $USER;
         
        if ($this -> StartResultCache(false, $USER->GetGroups()))
        {
            $this -> receiveParams();
            $this -> firmsList();
            $this -> setResult();
            $this -> SetResultCacheKeys(array(
                'FIRMS',
            ));
            $this -> IncludeComponentTemplate();
        }
        else
	{
		$this->AbortResultCache();
	}
        global $APPLICATION;
        $APPLICATION -> SetTitle(GetMessage('PAGE_TITLE'). '['.count($this->arResult['FIRMS']).']');
    }
}
?>