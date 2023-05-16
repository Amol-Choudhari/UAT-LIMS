<?php
// to insert chemist applicant entry in allocation table in LIMS created DmiChemistAllocationsTable by laxmi on date 10-01-2023
namespace app\Model\Table;
	use Cake\ORM\Table;
	use App\Model\Model;
	use Cake\ORM\TableRegistry;
	
class DmiChemistAllocationsTable extends Table{
	
	var $name = "DmiChemistAllocations";
	var $useTable = 'dmi_chemist_allocations';
}

?>