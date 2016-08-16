<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Loader as Loader;

if(!Loader::includeModule("iblock")){
	return;
}

$arIBlockTypes = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));

while($arr=$rsIBlock->Fetch()){
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("COMMENTZILA_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockTypes,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"NAME" => GetMessage("COMMENTZILA_IBLOCK_ID"),
			"PARENT" => "BASE",
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y"
		),
		"ELEMENT_ID" => array(
			"NAME" => GetMessage("COMMENTZILA_ELEMENT_ID"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
		),
		"ELEMENT_CODE" => array(
			"NAME" => GetMessage("COMMENTZILA_ELEMENT_CODE"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => ''
		),
		"ORDER" => array(
			"NAME" => GetMessage("COMMENTZILA_ORDER"),
			"PARENT" => "BASE",
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => array(
            "ASC"=>GetMessage("COMMENTZILA_ORDER_ASC"),
            "DESC"=>GetMessage("COMMENTZILA_ORDER_DESC")
          )
		),
		"MAX_LEVEL_TREE" => array(
			"NAME" => GetMessage("COMMENTZILA_MAX_LEVEL_DEPTH"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => 5
		),
		"MAX_COUNT_SYMBOL" => array(
			"NAME" => GetMessage("COMMENTZILA_MAX_COUNT_SYMBOL"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => 500
		),
		"TIME_OUT_ADD" => array(
			"NAME" => GetMessage("COMMENTZILA_TIME_OUT_BETWEEN_ADD"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => 5000
		),
	  	"CACHE_TIME"  =>  array("DEFAULT"=>36000000),
			"USE_AUTH"  =>  array(
	        "PARENT"    =>  "BASE",
	        "NAME"      =>  GetMessage("COMMENTZILA_NEED_AUTH"),
	        "TYPE"      =>  "CHECKBOX",
				"DEFAULT"=>"N",
	    ),
		"LINK_AUTH" => array(
			"NAME" => GetMessage("COMMENTZILA_AUTH_LINK"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => ''
		),
	)
);

?>