<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arComponentParameters = array(
	"PARAMETERS" => array(
		"NEWS_IBLOCK_ID" => array(
			"NAME" => GetMessage("NEWS_IBLOCK_ID"),
			"TYPE" => "STRING",
            "DEFAULT" => "",
		),
        "AUTHOR_CODE" => array(
			"NAME" => GetMessage("AUTHOR_CODE"),
			"TYPE" => "STRING",
            "DEFAULT" => "",
		),
        "AUTHOR_TYPE_CODE" => array(
			"NAME" => GetMessage("AUTHOR_TYPE_CODE"),
			"TYPE" => "STRING",
            "DEFAULT" => "",
		),
        "CACHE_TIME"  =>  Array("DEFAULT"=>360000),
	),
);