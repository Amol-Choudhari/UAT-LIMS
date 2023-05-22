<?php
// to fetch application type for chemist application rejection added new file by laxmi on date 22-05-2023
namespace app\Model\Table;
	use Cake\ORM\Table;
	use App\Model\Model;
	use Cake\ORM\TableRegistry;
	
class DmiApplicationTypesTable extends Table{
	
	var $name = "DmiApplicationTypes";
	var $useTable = 'dmi_application_types';
}

?>