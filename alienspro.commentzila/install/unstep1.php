<?if(!check_bitrix_sessid()) return;?>
<?
IncludeModuleLangFile(__FILE__);
//echo CAdminMessage::ShowNote("Unstep1-message");
?>
<div>
  <form action="<?echo $APPLICATION->GetCurPage()?>">
    <input type="hidden" name="lang" value="<?=LANG; ?>">
		<input type="hidden" name="id" value="alienspro.commentzila">
		<input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="sessid" value="<?=bitrix_sessid()?>">
		<input type="hidden" name="step" value="2">
    <div style="display:table">
      <div style="dispay:table-row;">
        <div style="display:table-cell;vertical-align: middle;"><label><?=GetMessage("COMMENTZILA_DELETE_TABLE")?></label></div>
        <div style="display:table-cell;vertical-align: middle;"><input type="checkbox" name="DELETE_TABLE"></div>
      </div>
      </div>
    <br>
    <div>
      <input type="submit" name="" value="<?=GetMessage("COMMENTZILA_CONFIRM")?>">
    </div>
  </form>
</div>