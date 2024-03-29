<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader,
	Bitrix\Iblock,
	Bitrix\Main\Localization\Loc;

if(!$USER->IsAuthorized())
{
	return;
}

// ВАЛИДАЦИЯ ПАРАМЕТРОВ КОМПОНЕНТА
if(!isset($arParams['CACHE_TIME']))
{
	$arParams['CACHE_TIME'] = 360000;
}
$arParams['NEWS_IBLOCK_ID'] = (int) $arParams['NEWS_IBLOCK_ID'];
//------------------------------------------------------------
if($this->StartResultCache(false) && intval($arParams["NEWS_IBLOCK_ID"]) > 0)
{
	if(!Loader::includeModule("iblock"))
	{	$this -> AbortResultCache();
		return;
	}
	
	// ПОЛУЧАЕМ ДАННЫЕ ТЕКУЩЕГО ПОЛЬЗОВАТЕЛЯ
	$sortCurUser = array();
	$orderCurUser ='sort';
	$filterCurUser = array('ID' => $USER->GetID());
	$paramCurUser['SELECT'] = array($arParams['AUTHOR_TYPE_CODE']);
	$rsCurUser = CUser::GetList($sortCurUser, $orderCurUser, $filterCurUser, $paramCurUser);
	// все поля текущего пользователя
	$curUser = $rsCurUser->GetNext();
	
	echo '<pre>';
	//var_dump($curUser);
	echo '</pre>';
//------------------------------------------------------------
	// ПОЛУЧАЕМ СПИСОК ПОЛЬЗОВАТЕЛЕЙ
	$sortUsers = array();
	$orderUsers = 'sort';
	$filterUsers = array(
		$arParams['AUTHOR_TYPE_CODE'] => $curUser[$arParams['AUTHOR_TYPE_CODE']],
		'!ID' => $curUser['ID'] 
	);
	$paramUsers['SELECT'] = array($arParams['AUTHOR_TYPE_CODE']);
	
	$users = array();
	$rsUsers = CUser::GetList($sortUsers, $orderUsers, $filterUsers, $paramUsers);

	while($arUser = $rsUsers->GetNext())
	{
		$users[] = $arUser;
	}
//------------------------------------------------------------
	// ПОЛУЧАЕМ СПИСОК НОВОСТЕЙ
	$arSelectNews = array(
		"ID",
		"IBLOCK_ID",
		"NAME",
		'DATE_ACTIVE_FROM',
	);
	$arFilterNews = array(
		"IBLOCK_ID" => $arParams["NEWS_IBLOCK_ID"],
		"ACTIVE" => "Y",
		'PROPERTY_'.$arParams['AUTHOR_CODE'] => array_column($users, 'ID'),
		'!PROPERTY_'.$arParams['AUTHOR_CODE'] => $curUser[$arParams['AUTHOR_CODE']],
	);
	$arSortNews = array(
		"NAME" => "ASC"
	);
	// массив новостей
	$news = array();
	$rsNews = CIBlockElement::GetList($arSortNews, $arFilterNews, false, false, $arSelectNews);
	while($arNew = $rsNews->GetNextElement())
	{
		$newProps = $arNew->GetProperties(false, array('AUTHOR'));
		$newFields = $arNew->GetFields();
		$newFields[$arParams['AUTHOR_CODE']] = $newProps['AUTHOR']['VALUE'];
		$news[] = $newFields;
		
	}
	
	echo '<pre>';
	//print_r($fields);
	echo '</pre>';
	
	echo '<pre>';
	var_dump($news);
	echo '</pre>';
//------------------------------------------------------------
	// ФОРМИРУМЕМ arResult
	$newsCount = 0;
	foreach($users as $uKey => $user)
	{
		$arResult['USERS'][$uKey]['ID'] = $user['ID'];
		$arResult['USERS'][$uKey]['LOGIN'] = $user['LOGIN'];
		foreach($news as $nKey => $new)
		{
			$newsCount++;
			if((int) $user['ID'] === (int) $new['PROPERTY_'.$arParams['AUTHOR_CODE'].'_VALUE'])
			{
				$arResult['USERS'][$uKey]['USER_NEWS'][$nKey]['NAME'] = $new['NAME'];
				$arResult['USERS'][$uKey]['USER_NEWS'][$nKey]['DATE_ACTIVE_FROM'] = $new['DATE_ACTIVE_FROM'];
			}
		}
	}
	$arResult['NEWS_COUNT'] = $newsCount;
	
	$this->SetResultCacheKeys(array(
		'USERS',
		'NEWS_COUNT'
	));
//var_export([__FILE__.__LINE__, $arResult]);
	$this->includeComponentTemplate();
	
}


$APPLICATION->SetTitle(GetMessage('NEWS_TITLE') . '['.$arResult['NEWS_COUNT'].']');
?>