<?if(!check_bitrix_sessid()) return;?>
<?
IncludeModuleLangFile(__FILE__);
echo CAdminMessage::ShowNote(GetMessage("COMMENTZILA_DELETE_OK"));
?>
<div>
  <form action="<?echo $APPLICATION->GetCurPage()?>">
    <input type="submit" name="" value="<?=GetMessage("COMMENTZILA_BACK")?>">
  </form>
</div>