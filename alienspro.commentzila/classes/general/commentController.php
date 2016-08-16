<?php 
IncludeModuleLangFile(__FILE__);

class CommentController{
 
  private $module;
  private $request;
  private $user;
  private $app;
  private $arParams;
  private $offset = 0;
  private $limit = 10;
  private $site_id;
  private $cache_path = "/alienspro/commentzila/";
  private $tabGeo;
  private $treeView;
  public $cache_id;
  
/*********************/ 
  
  const ACTION = "action";
  const OFFSET = "offset";
  const LIMIT = "limit";
  const SHOW = "show";
  const CREATE = "create";
  const DELETE = "delete";
  const UPDATE = "update";
  const LIKE = "like";
  const SET = "set";
  const DOWN = "down";
  const UP = "up";
  const ID = "id";
  const PARAMS = "arParams";
  const CURRENT_URL = "CURRENT_URL";
  const PATH_TO_TEMPLATE = "PATH_TO_TEMPLATE";
  const NAME = "name";
  const MSG = "msg";
  const PARENT_ID = "parent_id";
  const STATUS_ADD = "status_add";
  const ELEMENT_ID = "ELEMENT_ID";
  const IBLOCK_TYPE = "IBLOCK_TYPE";
  const IBLOCK_ID = "IBLOCK_ID";
  const IS_AUTH = "isAuth";
  const SITE = "SITE_ID";
  const CACHE_TIME = "CACHE_TIME";
  const CACHE_TYPE = "CACHE_TYPE";
  const SPAM_COUNT = "SPAM_COUNT";
  const MAX_LEVEL_NUMBER = 10;
  const DEFAULT_LEVEL_NUMBER = 5;
  const DEFAULT_MAX_COUNT_SYMBOL = 5000;
  const MAX_LEVEL_TREE = "MAX_LEVEL_TREE";
  const ORDER = "ORDER";
  const MAX_COUNT_SYMBOL = "MAX_COUNT_SYMBOL";

  public function __construct($module, $request, $treeView ,$site_id, $tabGeo){
    GLOBAL $USER;
    GLOBAL $APPLICATON;
    $this->module = $module;
    $this->tabGeo = $tabGeo;
    $this->request = $request;
    $this->app = $APPLICATON;
    $this->user = $USER;
    $this->arParams = $request[self::PARAMS];
    $module->createdIdParam($this->arParams);
    $this->site_id = $site_id;
    $this->treeView = $treeView;
    
    if(!empty($this->request[self::OFFSET])){
      $this->offset = $_REQUEST[self::OFFSET];
    }
    $this->cache_path.=$this->arParams["ELEMENT_ID"];
    
    if($this->arParams[self::MAX_LEVEL_TREE]>self::MAX_LEVEL_NUMBER || $this->arParams[self::MAX_LEVEL_TREE]<0){
      $this->arParams[self::MAX_LEVEL_TREE] = self::DEFAULT_LEVEL_NUMBER;
    }
    if($this->arParams[self::MAX_COUNT_SYMBOL]<1){
      $this->arParams[self::MAX_COUNT_SYMBOL] = self::DEFAULT_MAX_COUNT_SYMBOL;
    }      
  }  

  public function response(){  
     if($this->arParams[self::CACHE_TYPE]=="N"){
       $this->render();
     }else{
       $this->renderWithCache();
     }
  }
  
  public function create(){
    if(empty($this->request[self::MSG]))
      die();
    
    $this->clearCache();
    $data = array(
      "element_id"=>$this->arParams[self::ELEMENT_ID],
      "infoblock_id"=>$this->arParams[self::IBLOCK_ID],
      "infoblock_type"=>$this->arParams[self::IBLOCK_TYPE],
      "user_id" =>$this->user->GetID(),
      "name"=>$this->getUserName(),
      "msg"=>TruncateText($this->request[self::MSG], $this->arParams[self::MAX_COUNT_SYMBOL]),
      "ip"=>$_SERVER['REMOTE_ADDR'],
      "country"=>$this->tabGeo->country($_SERVER['REMOTE_ADDR']),
      "parent_id"=>$this->request[self::PARENT_ID],
      "site_id"=>$this->site_id
    );

    try{
      $createdId = $this->module->create($data);
      
    }catch(Exception $e){
        echo '<div class="cz_error">'.$e->getMessage().'</div>';
    }
      $this->response();   
  }
  
  public function isCreateRequest(){
    return $this->request[self::ACTION] === self::CREATE;
  }
  
  public function delete(){
     $this->clearCache();
     $this->module->delete($this->request[self::ID]);
     $this->response();
  }
  
  public function isDeleteRequest(){
    return $this->request[self::ACTION] === self::DELETE;      
  }
  
  public function isShowRequest(){
    return $this->request[self::ACTION] === self::SHOW;
  }
  
  public function isLikeRequest(){
    return $this->request[self::ACTION] === self::LIKE;
  }
  
  public function like(){
    $likeStatus = $this->module->like($this->request[self::ID],$this->user->GetID(), $this->request[self::SET]);
     
    echo json_encode(array(self::STATUS_ADD=>$likeStatus));
  }
  
  private function getUserName(){
     if($this->request[self::NAME]!=self::IS_AUTH){
      $name = $this->request[self::NAME];
      if(empty($name))
        die();
    }else{
      $firstName = $this->user->GetFullName();
      $name = (!empty($firstName))?$firstName:GetMessage('USER').'-'.$this->user->GetID();
    }
    return $name;
  }
  
  private function renderWithCache(){
    $obCache = new CPHPCache;

    $this->cache_id = $this->arParams[self::ELEMENT_ID].
            $this->arParams[self::IBLOCK_TYPE]. 
            $this->arParams[self::IBLOCK_ID].
            $this->site_id.
            $this->limit.
            $this->offset.
            $this->arParams[self::ORDER];
    if($obCache->InitCache($this->arParams[self::CACHE_TIME], $this->cache_id, $this->cache_path)){
      $vars = $obCache->GetVars();
      $obCache->Output();
    }else{   
      $query =$this->module->getTree(
              $this->arParams[self::ELEMENT_ID], 
              $this->arParams[self::IBLOCK_TYPE],
              $this->arParams[self::IBLOCK_ID],
              $this->site_id,
              $this->limit, 
              $this->offset,
              $this->arParams[self::ORDER]
            );

      while($res = $query->GetNext()){
        $arResult["ITEMS"][]=$res;
      }
      $arResult["COUNT"] = $this->module->count(
          $this->arParams[self::ELEMENT_ID],
          $this->arParams[self::IBLOCK_TYPE],
          $this->arParams[self::IBLOCK_ID],
          SITE_ID
      ); 
      if($obCache->StartDataCache()){
         print $this->treeView->renderTree($arResult, -1, $this->arParams);
      
         $obCache->EndDataCache(array()); 
      }  
    }    
  }
  
  private function render(){
    $query =$this->module->getTree(
              $this->arParams[self::ELEMENT_ID], 
              $this->arParams[self::IBLOCK_TYPE],
              $this->arParams[self::IBLOCK_ID],
              $this->site_id,
              $this->limit, 
              $this->offset,
              $this->arParams[self::ORDER]
             );

    while($res = $query->GetNext()){
      $arResult["ITEMS"][]=$res;
    }
    $arResult["COUNT"] = $this->module->count(
      $this->arParams[self::ELEMENT_ID],
      $this->arParams[self::IBLOCK_TYPE],
      $this->arParams[self::IBLOCK_ID],
      SITE_ID
    );  
    print $this->treeView->renderTree($arResult, -1, $this->arParams);            
  }
  
  private function clearCache(){
    $obCache = new CPHPCache();
    $obCache->CleanDir($this->cache_path);
  }
}

