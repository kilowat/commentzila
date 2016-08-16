<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("alienspro.commentzila")){
	echo 'WORNING! commentizla_module not find';
	exit;
}
 

$comment = new commentzila();

$arParams["CURRENT_URL"]= $APPLICATION->GetCurPage();


$comment->createdIdParam($arParams);

$arResult["COUNT"] = $comment->count(
		$arParams["ELEMENT_ID"],
    $arParams["IBLOCK_TYPE"],
		$arParams["IBLOCK_ID"],
     SITE_ID
);

$rsUser = CUser::GetByID($USER->GetID());
$arUser = $rsUser->Fetch();
$arResult["CURRENT_USER"] = $arUser;

$this->IncludeComponentTemplate();

?>