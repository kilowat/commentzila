<?php 
IncludeModuleLangFile(__FILE__);
$aMenu = array(
    "parent_menu" => "global_menu_services",
    "sort"        => 1,                 
    "text"        => GetMessage('COMMENTS'),      
    "title"       => GetMessage('COMMENTS'), 
    "icon"        => "comment_ico", 
    "page_icon"   => "comment_ico", 
    "items_id"    => "comment",  
    "items"       => array(
        array(
            "url" =>"comment_list.php?lang=".LANG,
            "text"=>GetMessage('LIST_COMMENTS'),
            "title"=>GetMessage('LIST_COMMENTS'),
        ),
    ),          
  );
  return $aMenu;
?>