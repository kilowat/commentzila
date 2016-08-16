<?
global $DB;
$db_type = strtolower($DB->type);
CModule::AddAutoloadClasses(
    'alienspro.commentzila',
      array(
      'commentzila' => 'classes/'.$db_type.'/commentzila.php' ,
      'commentController'=>'classes/general/commentController.php',
      'TabGeo'=>'classes/general/tabgeo.php',
     )
);

?>