<?
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("MyHandlersClass", "OnBeforeIBlockElementUpdateHandler"));
AddEventHandler("main", "OnEpilog", Array("MyHandlersClass", "OnEpilogHandler"));
AddEventHandler("main", "OnBeforeEventAdd", array("MyHandlersClass", "OnBeforeEventAddHandler"));
AddEventHandler("main", "OnBuildGlobalMenu", array("MyHandlersClass", "OnBuildGlobalMenuHandler"));

class MyHandlersClass
{
    // создаем обработчик события "OnBeforeIBlockElementUpdate"
    function OnBeforeIBlockElementUpdateHandler(&$arFields)
    {
        if (intval($arFields['IBLOCK_ID']) == IPROD) {
            $arSelect = Array('SHOW_COUNTER');
            $arFilter = Array("IBLOCK_ID"=>$arFields['IBLOCK_ID'], 'ID' => $arFields['ID']);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
            while($ob = $res->GetNextElement())
            {
                $resVal = $ob->GetFields();
            }
            
            if (intval($resVal['SHOW_COUNTER']) > 2)
            {
                global $APPLICATION;
                $APPLICATION->ThrowException('Товар невозможно деактивировать, у него ' . $resVal['SHOW_COUNTER'] . ' просмотров');
                return false;
            }
        }
    }
    
    // создаем обработчик события "OnEpilog"
    function OnEpilogHandler()
    {
        global $APPLICATION;
        if (ERROR_404 == 'Y') {
            CEventLog::Add(array(
                "SEVERITY" => "INFO",
                "AUDIT_TYPE_ID" => "ERROR_404",
                "MODULE_ID" => "main",
                "DESCRIPTION" => $APPLICATION -> GetCurPage(),
             ));
        }
        if (CModule::IncludeModule('iblock'))
        {
            $urlPage = $APPLICATION -> GetCurPage();
            
            $arSelect = Array("PROPERTY_META_TAG_TITLE", "PROPERTY_META_TAG_DESC");
            $arFilter = Array(6, "ACTIVE"=>"Y", 'NAME' => $urlPage);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
            if($arFields = $res->Fetch())
                $APPLICATION -> SetPageProperty('title', $arFields['PROPERTY_META_TAG_TITLE_VALUE']);
                $APPLICATION -> SetPageProperty('description', $arFields['PROPERTY_META_TAG_DESC_VALUE']);
            }  
        }
    }
    // создаем обработчик события "OnBeforeEventAdd"
    function OnBeforeEventAddHandler(&$event, &$lid, &$arFields)
    {
        if($event == 'FEEDBACK_FORM')
        {
            global $USER;
            if($USER -> IsAuthorized())
            {
                $arFields['AUTHOR'] = 'Пользователь авторизован: ' . $USER -> GetId() . '(' . $USER -> GetLogin() . '), ' . $USER -> GetFirstName() . ', данные
из формы: ' . $arFields['AUTHOR'] . '.';
            }
            else
            {
                $arFields['AUTHOR'] = 'Пользователь не авторизован, данные из формы: ' . $arFields['AUTHOR'];
            }
            CEventLog::Add(array(
                "SEVERITY" => "INFO",
                "AUDIT_TYPE_ID" => "AUTHOR_INFO",
                "MODULE_ID" => "main",
                "DESCRIPTION" => 'Замена данных в отсылаемом письме – ' . $arFields['AUTHOR'],
             ));
        }
    }
    // создаем обработчик события "OnBuildGlobalMenu"
    function OnBuildGlobalMenuHandler (&$aGlobalMenu, &$aModuleMenu)
    {
        global $USER;
        if (in_array(5, $USER->GetUserGroupArray()) && !in_array(1, $USER->GetUserGroupArray()))
        {
            $aGlobalMenu = array("global_menu_content" => $aGlobalMenu["global_menu_content"]);
            foreach ($aModuleMenu as $key => $val)
            {
                if ($val['text'] !== 'Новости')
                {
                    unset($aModuleMenu[$key]);
                }
            }
        }
    }
?>