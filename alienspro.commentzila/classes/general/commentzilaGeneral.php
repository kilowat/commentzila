<?php
IncludeModuleLangFile(__FILE__);

class CommentzilaGeneral{
 
    public $db;
    public $user;
    protected $_table = 'alienspro_commentzila';
    private $maxLevel;
    

    const IBLOCK_NAME_DEFAULT = "AUTO_GENERATED";
    const IBLOCK_ID_DEFAULT= -1;   
    
    function __construct(){
    
      global $DB;
      global $USER;
        
      $this->db = $DB;
      $this->user = $USER;
    }
    public function create($field=array()){
        $right_key=0;
        $level = 0;
        if($field["parent_id"]>0){
            $res = $this->_getRightKeyAndLevel($field["parent_id"]);
            $right_key = $res["right_key"];
            $level = $res["level"]+1;
            $this->_updateBeforeInsert($right_key);
        }else{
             $right_key = (int)$this->_maxRigthKey()+1;
        }
        $data = array(
            'element_id'=>intval($field["element_id"]),
            'parent_id'=>intval($field["parent_id"]),
            'user_id'=>intval($field["user_id"]),
            'name'=>"'".$this->prepareString($field["name"])."'",
            'msg'=>"'".$this->prepareString($field["msg"])."'",
            'infoblock_id'=>intval($field["infoblock_id"]), 
            'infoblock_type'=>"'".$this->prepareString($field["infoblock_type"])."'",
            'ip'=>"'".$this->prepareString($field["ip"])."'",
            'country'=>"'".$this->prepareString($field["country"])."'",
            'site_id'=>"'".$this->prepareString($field["site_id"])."'",
            'date_t'=>time(),
            'left_key'=>intval($right_key),
            'right_key'=>intval($right_key),
            'level'=>intval($level)
            );
        $id = $this->db->insert($this->_table,$data);
        return $id;
    }
    public function delete($id){
      
        $arrElem = $this->find($id);
        $sql = 'DELETE FROM '.$this->_table.' WHERE left_key >= '.$arrElem["left_key"].' AND right_key <= '.$arrElem["right_key"]; 
        $this->db->Query($sql);
		
        $this->_updateAfterDelete($arrElem["right_key"],$arrElem["left_key"]);
        $this->_deleteLike($id);
    }
    public function find($id){
      
        $sql = 'SELECT * 
                FROM alienspro_commentzila
                WHERE id = '.intval($id);
        $query = $this->db->Query($sql, false);
        $result = $query->Fetch();
        
        return $result;
    }
    private function _maxRigthKey(){
      
        $sql = 'SELECT max(alienspro_commentzila.right_key) as maxRigthKey
                FROM alienspro_commentzila';
        $query = $this->db->Query($sql, false);
        $result = $query->Fetch();
        
        return $result["maxRigthKey"];
    }
    private function _getRightKeyAndLevel($id){
      
        $sql = 'SELECT alienspro_commentzila.right_key as right_key,
                alienspro_commentzila.level as level
                FROM alienspro_commentzila
                WHERE alienspro_commentzila.id ='.$id;
        $query = $this->db->Query($sql, false);
        $result = $query->Fetch();
        if(!$result){
           throw new Exception(GetMessage('MESSAGE_WAS_BEEN_DELETE'), 1);
         
        }
        return $result;        
    }
    private function _updateBeforeInsert($right_key){ 
      
        $sql = 'UPDATE '.$this->_table.' SET right_key = right_key + 2, 
        left_key = IF(left_key > '.$right_key.', left_key + 2, left_key) 
        WHERE right_key >= '.$right_key;
        $query = $this->db->Query($sql);
       
        if(!$query)
            throw new Exception("Error in update rigth_keys", 1);      
    }
    private function _updateAfterDelete($right_key,$left_key){
      
        $sql = 'UPDATE '.$this->_table.' SET left_key = IF(left_key > '.$left_key.', 
            left_key – ('.$right_key.' - '.$left_key.' + 1), 
            left_key), right_key = right_key – ('.$right_key.' - '.$left_key.' + 1) 
            WHERE right_key > '.$right_key;
    }
    private function _checkLike($id, $user_id){
      
        $flag = true;
        $sql = "SELECT comment_id, user_id 
                FROM alienspro_commentzila_comment_like
                WHERE comment_id =".$id;
        $query = $this->db->Query($sql);
 
        $res = $query->Fetch();
        if($res["user_id"]==$user_id || !$this->user->IsAuthorized())
            $flag = false;
        
        return $flag;
                
    }
    private function _updateLikeTable($id,$user_id,$type){
      
        if(!$this->_checkLike($id,$user_id))
            return false;
        
        $rang = 0;
        $data = array(
            'comment_id'=>$id,
            'user_id'=>$user_id,
            );
        
        $query = $this->find($id);
   
        if($type == 'UP'){
            $rang = ++$query["rang"];
        }
        if($type == 'DOWN'){
            $rang = --$query["rang"];
        }
        $this->db->insert("alienspro_commentzila_comment_like",$data);
        $this->db->update(
                $this->_table,
                array("rang"=>$rang),
                "WHERE id = ".$id
            );
        
        return true;
    }
	
    private function _deleteLike($id){
      
        $sql = 'DELETE FROM alienspro_commentzila_comment_like WHERE comment_id='.$id;
        $this->db->Query($sql);
    }
    
    public function getTree($element_id, $iblock_type, $iblock_id, $site_id, $limit=1000, $offset=0, $order="ASC"){

        $sql = "SELECT 
                    ".$this->_table.".id, 
                    ".$this->_table.".name, 
                    ".$this->_table.".level, 
                    ".$this->_table.".msg,
                    ".$this->_table.".country,
                    ".$this->_table.".ip,
                    ".$this->_table.".spam,
                    ".$this->_table.".date_t, 
                    ".$this->_table.".parent_id, 
                    ".$this->_table.".rang,
                    b_user.PERSONAL_PHOTO as avatar
            FROM ".$this->_table."
            
            LEFT JOIN b_user ON b_user.ID=".$this->_table.".user_id            
            WHERE element_id = ".$element_id." AND infoblock_type = '".$iblock_type."' AND infoblock_id = ".$iblock_id." AND site_id = '".$site_id."'
            ORDER BY left_key $order
			LIMIT ".$limit." OFFSET ".$offset;
        $query = $this->db->Query($sql,false);
 
        return $query;
    } 
	
    public function like($id, $user_id, $type=null){
      
        $res = $this->_updateLikeTable($id,$user_id,$type);
        
        return $res;
    }
    public function count($element_id, $iblock_type, $iblock_id, $site_id){
      
        $sql = "SELECT count(".$this->_table.".id) as COUNT
                FROM ".$this->_table."
                WHERE element_id = ".$element_id." AND infoblock_type = '".$iblock_type."' AND infoblock_id = ".$iblock_id." AND site_id = '".$site_id."'";
        $query = $this->db->Query($sql,false);
        $result = $query->Fetch();
        
        return $result["COUNT"];
    }
   
    public function generatorId($str){
     
      $res = 0;
      for($i=0;strlen($str)>$i;$i++){
        $res+=ord($str[$i]);
      }  
     
      return $res;
    }
    
    public function getCommentList($by = "id", $order="asc"){
      $sql = "SELECT id, parent_id, rang, element_id, infoblock_id, infoblock_type, ip, country, site_id, name, msg, spam, from_unixtime(date_t) as date_t  
             FROM {$this->_table} 
             ORDER BY $by $order";
      $query = $this->db->Query($sql);
      return $query;
              
    }

    public function setSpam($id){    
      $this->db->Update($this->_table, array("spam"=>1), "WHERE id = {intval($id)}");
    }
    public function roleBackSpam($id){    
      $this->db->Update($this->_table, array("spam"=>0), "WHERE id = {intval($id)}");
    }    
    public function createdIdParam(&$arParams){
        

      if(empty($arParams["IBLOCK_TYPE"])){
        $arParams["IBLOCK_TYPE"] = self::IBLOCK_NAME_DEFAULT;
      }
      if(empty($arParams["IBLOCK_ID"])){
        $arParams["IBLOCK_ID"] = self::IBLOCK_ID_DEFAULT;
      }
      
      if(empty($arParams["ELEMENT_ID"])){
        if(!empty($arParams["ELEMENT_CODE"])){
            $arParams["ELEMENT_ID"] =  $this->generatorId($arParams["ELEMENT_CODE"]);
        }else{
          $arParams["IBLOCK_TYPE"] = self::IBLOCK_NAME_DEFAULT;
          $arParams["IBLOCK_ID"] = self::IBLOCK_ID_DEFAULT;
          $arParams["ELEMENT_ID"] = $this->generatorId($arParams["CURRENT_URL"]); 
        }
      }   
    }  
    
    private function prepareString($str){
      return $this->db->ForSql(trim($str));
    }
    
}
