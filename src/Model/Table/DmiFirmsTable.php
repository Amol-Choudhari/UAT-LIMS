<?php
// to get firm information at LIMS added firm DmiFirmsTable by laxmi Bhadade on 2-01-2023
namespace app\Model\Table;
use Cake\ORM\Table;
use App\Model\Model;
use Cake\ORM\TableRegistry;

class DmiFirmsTable extends Table{
	
	var $name = "DmiFirms";
	var $useTable = 'dmi_firms';
}

?>