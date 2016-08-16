<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
//to do

global $DB;
ClearVars();


$sTableID = "alienspro_commentzila";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

IncludeModuleLangFile(__FILE__);

if(!CModule::IncludeModule("alienspro.commentzila")){
    echo "Commentzila_Module not find";
    exit;
}
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alienspro.commentzila/prolog.php");

$COMMENT_RIGHT = $APPLICATION->GetGroupRight("alienspro.commentzila");

if ($COMMENT_RIGHT <= "D"):
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
endif;

$Comment = new commentzila();
/********************start actions***************************/
if($_GET["action"]==="delete" && $COMMENT_RIGHT =="W" ){
    $Comment->delete($id);
    LocalRedirect("comment_list.php");
}
if($_GET["action"]==="setSpam" && $COMMENT_RIGHT =="W" ){
    $Comment->setSpam($id);
    LocalRedirect("comment_list.php");
}
if($_GET["action"]==="roleBackSpam" && $COMMENT_RIGHT =="W" ){
    $Comment->roleBackSpam($id);
    LocalRedirect("comment_list.php");
}
if(($arID = $lAdmin->GroupAction()) && $COMMENT_RIGHT=="W" && check_bitrix_sessid())
{   
		if($_REQUEST['action_target']=='selected')
		{
				$arID = Array();
				$rsData = $Comment->getCommentList($by, $order);
				while($arRes = $rsData->Fetch())
						$arID[] = $arRes['id'];
		}
		foreach($arID as $ID)
		{
				if(strlen($ID)<=0)
						continue;
				$ID = IntVal($ID);
				switch($_REQUEST['action'])
				{
				case "delete":
						@set_time_limit(0);
						$Comment->delete($ID);
						break;
				case "setSpam":
						@set_time_limit(0);
						$Comment->setSpam($ID);
						break;
				
				case "roleBackSpam":
						@set_time_limit(0);
						$Comment->roleBackSpam($ID);
						break;
				}        
		}
}

/**************************end actions***************************/

$rsData = $Comment->getCommentList($by,$order);

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("COMMENT_PAGE")));

$lAdmin->AddHeaders(array(
	array("id"=>"id", "content"=>"ID", "sort"=>"id", "default"=>true),
	array("id"=>"name", "content"=>GetMessage("AUTHOR"), "sort"=>"name", "default"=>true),
  array("id"=>"msg", "content"=>GetMessage("COMMENT"), "sort"=>"msg", "default"=>true),
  array("id"=>"site_id", "content"=>GetMessage("SITE"), "sort"=>"site_id", "default"=>true),
  array("id"=>"ip", "content"=>"IP", "sort"=>"ip", "default"=>true),
  array("id"=>"spam", "content"=>GetMessage("SPAM"), "sort"=>"spam", "default"=>true),
  array("id"=>"rang", "content"=>GetMessage("LIKE"), "sort"=>"rang", "default"=>true),
  array("id"=>"date_t", "content"=>GetMessage("DATE"), "sort"=>"date_t", "default"=>true),
));

while($arRes = $rsData->NavNext(true, "f_"))
{       
  $arRes["spam"] = ($arRes["spam"])? GetMessage("YES"):GetMessage("NO");
	$row = &$lAdmin->AddRow($f_id, $arRes);
	
	$arActions = Array();
	$arActions[] = array("SEPARATOR"=>true);

  $arActions[] = array("ICON" => "delete", "TEXT" => GetMessage("MAIN_ADMIN_MENU_DELETE"),     
		"ACTION"=>"window.location='comment_list.php?lang=".LANGUAGE_ID."&action=delete&id=$f_id&".bitrix_sessid_get()."'");

  $arActions[] = array("ICON" => "edit", "TEXT" => GetMessage("THIS_SPAM"),     
		"ACTION"=>"window.location='comment_list.php?lang=".LANGUAGE_ID."&action=setSpam&id=$f_id&".bitrix_sessid_get()."'");  
 
  $arActions[] = array("ICON" => "edit", "TEXT" => GetMessage("ROLE_BACK_SPAM"),     
		"ACTION"=>"window.location='comment_list.php?lang=".LANGUAGE_ID."&action=roleBackSpam&id=$f_id&".bitrix_sessid_get()."'");  
 
  if ($COMMENT_RIGHT < "W")
		$row->bReadOnly = True;
	else
		$row->AddActions($arActions);
}
/************** Footer *********************************************/
$lAdmin->AddFooter(array(
	array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
	array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0")));
$aMenu = array(); $aContext = array();
//if ($CALLBACK_RIGHT >= "W"):
	$lAdmin->AddGroupActionTable(Array(
		"delete" => GetMessage("COMMENT_DELETE"),
    "setSpam" => GetMessage("THIS_SPAM"),
    "roleBackSpam" => GetMessage("ROLE_BACK_SPAM"),
		));
	 
//endif;
$lAdmin->AddAdminContextMenu($aMenu);

$lAdmin->CheckListMode();
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$lAdmin->DisplayList();
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");

