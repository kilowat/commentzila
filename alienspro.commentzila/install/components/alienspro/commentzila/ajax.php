<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
IncludeModuleLangFile(__FILE__);

if(!check_bitrix_sessid()){
    LocalRedirect("/");
}
  
if(!CModule::IncludeModule("alienspro.commentzila")){
	echo 'WORNING! alienspro.commentzila_module not find';
	exit;
}

class TreeView{
    private $user;
    private $padding = 10;
    public function __construct() {
        GLOBAL $USER;
        $this->user = $USER;
    }

    public function renderTree($tree, $currDepth, $arParams) {

        if(empty($tree["ITEMS"]))
            return;
        $currNode = array_shift($tree["ITEMS"]);

        $result = '';
        $spam = ($currNode["spam"])?"spam":"";
        // Going down?
        if ($currNode['level'] > $currDepth) {
            // Yes, prepend
            $padding = $currNode["level"]*$this->padding;
            $result .= '<div class="comment-messages-wrapper" style="padding-left:'.$padding.'px" data-total='.$tree["COUNT"].'>';
        }
        // Going up?
        if ($currNode['level'] < $currDepth) {
            // Yes, close n open
            $result .= str_repeat('</div>', $currDepth - $currNode['level']);
        }

        $img = ($currNode["avatar"]>0)?CFile::ShowImage($currNode["avatar"],64, 64, "border=0", "", false):'<img src='.$arParams["PATH_TO_TEMPLATE"].'/images/default_userpic.png>';
        // Always add the node
        $result.='<div class="comment-messages-list '.$spam.'" id="commentid-'.$currNode["id"].'" data-parentid='.$currNode["parent_id"].' data-id="'.$currNode["id"].'">';
        $result.= 	'<div class="comment-messages-head">';
        $result.= 		'<span class="comment-author">'.$currNode["name"].' - </span>';
        $result.=     '<span class="comment-date">'.date("d.m.Y H:i:s",  $currNode["date_t"]).'</span>';
        $result.=     '<span class="country-ico"><img src="'.$arParams["PATH_TO_TEMPLATE"].'/images/city/'.$currNode["country"].'.png" alt=""></span>';
        $result.= 	'</div>';

        if($currNode["spam"]){
            $result.= '<div class="is-spam">'.GetMessage("IS_SPAM").'</div>';
        }
        $result.= '<div class="comment-center-content">';
        $result.= 	'<div class="comment-avatar">'.$img.'</div>';
        $result.= 	'<div class="comment-messages-body">';
        $result.= 		$currNode["msg"];
        $result.= 	'</div>';
        $result.= 	'<div class="comment-edit">';
        $result.=     	'<div class="comment-buttons">';
        if($arParams[CommentController::MAX_LEVEL_TREE] >=  $currNode["level"]){
            if($arParams["USE_AUTH"] == "N" OR $arParams["USE_AUTH"] == "Y" AND $this->user->IsAuthorized())
                $result.= 			'<a id="send-comment-parent">'.GetMessage("ANSWER").'</a>';
        }


        if($this->user->IsAdmin()){
            $result.=				'<button id="delete-comment">'.GetMessage("DELETE").'</button>';
        }

        $result.= 		'</div>';
        $result.= 		'<div class="comment-like">';

        if($this->user->IsAuthorized()){
            $result.= 			'<button id="like-down"></button>';
        }else{
            $result.= 			'<button id="like-down" disabled="disabled" class="like-disabled"></button>';
        }

        $result.= 			'<span>'.$currNode["rang"].'</span>';

        if($this->user->IsAuthorized()){
            $result.= 			'<button id="like-up"></button>';
        }else{
            $result.= 			'<button id="like-up" disabled="disabled" class="like-disabled"></button>';
        }
        $result.= 		 '</div>';
        $result.= 		'</div>';
        $result.= 	'</div>';
        $result.= '</div>';
        // Anything left?
        if (!empty($tree)) {
            // Yes, recurse
            $result .=  $this->renderTree($tree, $currNode['level'], $arParams);
        }
        else {
            // No, close remaining
            $result .= str_repeat('</div>', $currNode['level'] + 1);
        }
        return $result;
    }
}

  
$comment = new commentzila();
$controller = new commentController($comment, $_POST, new TreeView() ,SITE_ID, new TabGeo());

if($controller->isDeleteRequest()){
  $controller->delete();
}

if($controller->isCreateRequest()){
  $controller->create();
}

if($controller->isShowRequest()){
  $controller->response();
}

if($controller->isLikeRequest()){
	$controller->like();
}
/****************************************************************/
?>

		
	

