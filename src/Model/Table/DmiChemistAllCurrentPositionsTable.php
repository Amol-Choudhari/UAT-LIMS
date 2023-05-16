<?php
//to insert chemist application  entry in allocation table added DmiChemistAllCurrentPositionsTable in LIMS by laxmi On 10-01-2023

namespace app\Model\Table;
	use Cake\ORM\Table;
	use App\Model\Model;
	use Cake\ORM\TableRegistry;
	
class DmiChemistAllCurrentPositionsTable extends Table{
	
	var $name = "DmiChemistAllCurrentPositions";
	var $useTable = 'dmi_chemist_all_current_positions';


	public function currentUserUpdate($customer_id,$user_email_id,$current_level)
	{
		$find_row_id = $this->find('all',array('fields'=>'id', 'conditions'=>array('customer_id IS'=>$customer_id),'order'=>array('id DESC')))->first();
		$row_id = $find_row_id['id'];

		$newEntity = $this->newEntity(array(
			'id'=>$row_id,
			'current_level'=>$current_level,
			'current_user_email_id'=>$user_email_id,
			'modified'=>date('Y-m-d H:i:s')
		 ));

		 $this->save($newEntity);
		return true;
	}
}

?>