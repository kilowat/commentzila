<?
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/alienspro.commentzila/install/index.php");
IncludeModuleLangFile(__FILE__);

class alienspro_commentzila extends CModule{
	public $MODULE_ID = "alienspro.commentzila";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $MODULE_CSS;

	function alienspro_commentzila(){
    $this->PARTNER_NAME = "ALIENS.PRO"; 
    $this->PARTNER_URI = "http://www.aliens.pro";
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

		$this->MODULE_NAME = GetMessage("COMMENTZILA_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("COMMENTZILA_DESC");
	}

	function InstallFiles($arParams = array()){
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alienspro.commentzila/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alienspro.commentzila/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
		CopyDirFiles( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/alienspro.commentzila/install/themes/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes', true, true );
		
    return true;
	}

	function InstallDB($arParams = array()){
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alienspro.commentzila/install/db/".strtolower($DB->type)."/install.sql");
		
		if($this->errors !== false){
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		else{
			return true;
		}
	}
	  
	function UnInstallDB($arParams = array()){
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alienspro.commentzila/install/db/".strtolower($DB->type)."/uninstall.sql");
		if($this->errors !== false){
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		return true;
	}
			
	function UnInstallFiles(){
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alienspro.commentzila/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFilesEx("/bitrix/components/alienspro/commentzila/");
		DeleteDirFiles( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/alienspro.commentzila/install/themes/.default/' , $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes/.default' );  
		DeleteDirFilesEx( '/bitrix/themes/.default/icons/commentzila' );

    return true;
	}
	  


	function DoInstall(){
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallFiles();               
		RegisterModule("alienspro.commentzila");
		$this->InstallDB(false);
		$APPLICATION->IncludeAdminFile(GetMessage("COMMENTZILA_INSTALL_OK"), $DOCUMENT_ROOT."/bitrix/modules/alienspro.commentzila/install/step.php");
		return true;
	}


    function DoUninstall(){
        global $DB, $APPLICATION, $step;
        $FORM_RIGHT = $APPLICATION->GetGroupRight("alienspro.commentzila");
        if ($FORM_RIGHT){
            $step = IntVal($step);
            if($step<2){
                $APPLICATION->IncludeAdminFile(GetMessage("COMMENTZILA_STEP_1"),
                $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alienspro.commentzila/install/unstep1.php");
            }
            elseif($step==2){
                if($_REQUEST["DELETE_TABLE"] === "on"){
                  $this->UnInstallDB();
                } 
                
                $this->UnInstallFiles();
                UnRegisterModule("alienspro.commentzila");
                
                $APPLICATION->IncludeAdminFile(GetMessage("COMMENTZILA_STEP_2"),
                $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alienspro.commentzila/install/unstep2.php");
            }
        }
    }

    function GetModuleRightList()
    {
        global $MESS;
        $arr = array(
            "reference_id" => array("D","R","W"),
            "reference" => array(
                GetMessage("FORM_DENIED"),
                GetMessage("FORM_OPENED"),
                GetMessage("FORM_FULL"))
            );
        return $arr;
    }
  
  
  /*
	function DoUninstall(){
		global $DOCUMENT_ROOT, $APPLICATION, $step;
		$this->UnInstallDB();
		$this->UnInstallFiles();
		UnRegisterModule("alienspro.commentzila");	
		return true;
    
  }
  */
}

?>