<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die ();
CJSCore::Init(array("fx"));
?>
<div class="comment-wrapper" id="alienspro_commentzila">
    <div class="comment-header"><?= GetMessage("COMMENTS") ?> - <span id="total-comm"></span></div>
    <div class="form-section">
        <? if ($arParams["USE_AUTH"] == "Y"): ?>
            <? if ($USER->IsAuthorized()): ?>
                <div class="comment-left-section">
                    <div class="row-input">
                        <textarea id="comment-msg" class="textbox" maxlength="<?= $arParams['MAX_COUNT_SYMBOL'] ?>"
                                  placeholder="<?= GetMessage("COMMENTS") ?>"></textarea>
                    </div>
                    <button id="send-comment" class="add-comment-btn"><?= GetMessage("SEND") ?></button>
                </div>
                <div class="comment-rigth-section">
                    <div class="hello-msg"><?= GetMessage("HELLO") ?>
                       <?$user_name = $USER->GetFullName();?>
                        <? if (!empty($user_name)): ?>
                            <?= $USER->GetFullName() ?>
                        <? else: ?>
                            <?= $USER->GetLogin() ?>
                        <? endif ?>
                    </div>
                    <?
                    if (!empty($arResult["CURRENT_USER"]["PERSONAL_PHOTO"]))
                        echo CFile::ShowImage($arResult["CURRENT_USER"]["PERSONAL_PHOTO"], 64, 64);
                    else
                        echo "<img src='" . $this->GetFolder() . "/images/default_userpic.png'>";
                    ?>
                </div>
            <? else: ?>
                <span id="comment-auth-msg"><a href="<?= $arParams["LINK_AUTH"] ?>"
                                               title="<?= GetMessage("AUTH") ?>"><?= GetMessage("LOGIN") ?></a>&nbsp;<?= GetMessage('FOR_ADD_COMMENTS') ?></span>
            <? endif ?>
        <? else: ?>
            <div class="comment-left-section">
                <div class="row-input">
                    <label for="comment-name"><?= GetMessage("YOUR_NAME") ?></label>
                    <input type="text" class="textbox" id="comment-name">
                </div>
                <div class="row-input">
                    <textarea id="comment-msg" class="textbox" maxlength="<?= $arParams['MAX_COUNT_SYMBOL'] ?>"
                              placeholder="<?= GetMessage("COMMENT") ?>"></textarea>
                </div>
                <button id="send-comment" class="add-comment-btn"><?= GetMessage("SEND") ?></button>
            </div>
        <? endif ?>
    </div>
    <div id="comment-messages">
    </div>
    <div id="comment-ajax-load">

    </div>
    <div class="paginator" id="paginator"></div>
</div>

<? $arParams["CURRENT_URL"] = $APPLICATION->GetCurPage() ?>
<? $arParams["PATH_TO_TEMPLATE"] = $this->GetFolder() ?>
<script>
    var params = {
        lang: {
            name: '<?=GetMessage("YOUR_NAME")?>',
            msg: '<?=GetMessage("COMMENT")?>',
            answer: '<?=GetMessage("ANSWER")?>',
        },
        sessid: '<?=bitrix_sessid();?>',
        url: '/bitrix/components/alienspro/commentzila/ajax.php',
        arParams:<?=json_encode($arParams)?>,
    }
    alienspro_commentzila().init(params);
</script>

