<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arComponentParameters = array(
	"PARAMETERS" => array(
		"IBLOCK_CAT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_CAT_ID"),
			"TYPE" => "STRING",
		),
		"IBLOCK_NEWS_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_NEWS_ID"),
			"TYPE" => "STRING",
		),
		"USER_PROPERTY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("USER_PROPERTY"),
			"TYPE" => "STRING",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600000),
	),
);
?>
