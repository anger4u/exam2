<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arComponentParameters = array(
	"PARAMETERS" => array(
		"PROD_IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PROD_IBLOCK_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"CLASS_IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLASS_IBLOCK_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"DETAIL_URL_TEMPLATE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("DETAIL_URL_TEMPLATE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"ELEMENT_PROP_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ELEMENT_PROP_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>360000),
	),
);
?>
