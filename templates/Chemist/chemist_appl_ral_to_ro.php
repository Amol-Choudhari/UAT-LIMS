<?php ?>
<style>
    h4 {
        padding: 5px;
        font-family: times;
        font-size: 12pt;
    }

    table{
        padding: 5px;
        font-size: 10pt;
        font-family: times;
    }
</style>

<!-- for multiple commodities added by laxmi on 10-1-2023 -->
<?php  
    $i=0;
    $sub_commodities_array = array();  
    $commodities_cat_array = array();  
    foreach($sub_commodity_data as $sub_commodity){
        
        $sub_commodities_array[$i] = $sub_commodity['commodity_name'];
        if(!empty($commodity_name_list[$i]['category_name'])){
        $commodities_cat_array[$i] = $commodity_name_list[$i]['category_name'];
        }
    $i=$i+1;
    } 
    
    $sub_commodities_list = implode(',',$sub_commodities_array);
    $commodities_cate_list = implode(',',$commodities_cat_array); 
    //set chemist prefix on the basis of middle name type added by laxmi on 04-09-2023
    if(!empty($middle_name_type)){
        if($middle_name_type == 'D/o'){
            $prefix = 'Ms.';
            $his_her = 'her';
            $mam_sir = 'madam';
            $he_her = 'She';
        }elseif($middle_name_type == 'S/o'){
            $prefix = 'Shri.';
            $his_her = 'his';
            $mam_sir = 'sir';
            $he_her = 'He';
        }elseif($middle_name_type == 'W/o'){
            $prefix = 'Smt.';
            $his_her = 'her';
            $mam_sir = 'madam';
            $he_her = 'She';
        }
        
    }
?>
    <table width="100%" border="1">
        <tr><td align="center" style="padding:5px;"><h4>Letter from RAL for Completion of training</h4></td></tr>
    </table>

    <table width="100%" border="1">
        <tr><td>Applicant Id: <?php echo $chemist_id; ?></td>
            <td align="right">Date: <?php  echo date('d/m/Y'); ?></td>
        </tr>
    </table>

    <table width="100%">
        <tr><td></td></tr>
        <tr>
            <td><br>To,</td><br>
        </tr>   
    </table>

    <table  width="100%">
        <tr>
            <td>  
            	
                   The Incharge,<br>
                   Directorate of Marketing and Inspection,<br>
                   <?php echo $office_type; ?> Office,<br>
                   <?php echo $ro_office; ?> <br> 
            </td>
            <?php if(!empty($profile_photo)) { ?>
                <td align="right">
                    <img src="<?php echo $profile_photo ; ?>" width="auto" height="80">
                </td>
                <?php } ?>
        </tr>
            </table>
            <table  width="100%">
        <tr>    
            <td><br>Subject: Training of <?php echo $prefix. "&nbsp;" .$chemist_fname."&nbsp;".$chemist_lname;?> chemist of <?php echo $firm_name; ?> <?php echo $firm_address; ?> for analysis, grading and marking of <?php echo $commodities_cate_list; ?> ( <?php echo $sub_commodities_list; ?> ) under Agmark– reg..
            </td>
        </tr>
                    
        <tr>
            <td><br>Dear Sir/Madam,</td><br>
        </tr>   

        <tr>
            <td>
               With reference to the <?php echo $chemist_id; ?> dated <?php echo date('d/m/Y');?>, regarding providing the training 
                to <?php echo $prefix. "&nbsp;" .$chemist_fname. "&nbsp;".$chemist_lname;?> <?php echo $middle_name_type; ?> <?php echo $parent_name; ?>  for analysis , grading and marking of <?php echo $commodities_cate_list ;?> (<?php echo $sub_commodities_list ;?>) under  Agmark.
                In this  regard it is informed that <?php echo $prefix. "&nbsp;" .$chemist_fname."&nbsp;".$chemist_lname;?> <?php echo $middle_name_type; ?> <?php echo $parent_name; ?> has attended the training for analysis, 
                grading and marking of <?php echo $commodities_cate_list ;?> (<?php echo $sub_commodities_list ;?>) under Agmark from <?php echo $schedule_from; ?> to <?php echo $schedule_to; ?> at Regional Agmark Laboratory <?php echo $ral_office; ?> . 
                <br>
              <?php echo $he_her; ?> is hereby relieved in on <?php echo $reliving_date;?> to report  <?php echo $office_type; ?>  <?php echo $ro_office; ?> for further procedural training. <br>
			</td>
        </tr>
                    
        <tr>
            <td><br></td>
        </tr>
              
    </table>


	<br>
    <table align="right">	
					
		<tr>
			<td>Your’s faithfully<br> 
				<?php echo $ral_fname ."&nbsp;". $ral_lname; ?>,<br>
                Incharge,<br>
				Regional Agmark Laboratory,<br> 
                <?php echo $ral_office; ?><br>
			</td>
		</tr>
		<tr>
            <td><br></td>
        </tr>
	</table><br>
    <table>
    <tr>
        <td>Copy to: <br>
         1.  <?php echo $prefix."&nbsp;" .$chemist_fname."&nbsp;".$chemist_lname;?> <?php echo $middle_name_type; ?> <?php echo $parent_name; ?> <?php echo $address; ?> to report at RO/SO <?php echo $ro_office; ?> for procedural training under AGMARK.
        </td>
    </tr>
    
     </table>


	



    
	
	
        