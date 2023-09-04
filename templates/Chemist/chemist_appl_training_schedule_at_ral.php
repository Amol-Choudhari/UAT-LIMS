<?php // chemist application pdf template file created by laxmi BHADADE ON 10-1-2023 

//after rescheduling at RAL side training schedule letter generated by laxmi on 09-05-2023 for chemist training module ?>
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
    $commodity_cat_array = array();  
    foreach($sub_commodity_data as $sub_commodity){ 
        
        $sub_commodities_array[$i] = $sub_commodity['commodity_name'];
        if(!empty($commodity_name_list[$i]['category_name'])){
        $commodity_cat_array[$i]  = $commodity_name_list[$i]['category_name'];
        }
    $i=$i+1;
    } 
    
    $sub_commodities_list = implode(',',$sub_commodities_array);
    $commodities_cate_list = implode(',',$commodity_cat_array);
    //set chemist prefix on the basis of middle name type added by laxmi on 01-09-2023
    if(!empty($middle_name_type)){
        if($middle_name_type == 'D/o'){
            $prefix = 'Ms.';
            $his_her = 'her';
            $mam_sir = 'madam';
        }elseif($middle_name_type == 'S/o'){
            $prefix = 'Shri.';
            $his_her = 'his';
            $mam_sir = 'sir';
        }elseif($middle_name_type == 'W/o'){
            $prefix = 'Smt.';
            $his_her = 'her';
            $mam_sir = 'madam';
        }
        
    }
?>
	
    <table width="100%" border="1">
        <!-- if application from sub office show So in title added by laxmi [01-09-2023] -->
        <tr><td align="center" style="padding:5px;"><h4>Letter from <?php if(!empty($office_type)){ echo $office_type; }?> to RAL schedule Training </h4></td></tr>
    </table>

    <table width="100%" border="1">
        <tr><td>Applicant Id: <?php echo $customer_id; ?></td>
            <td align="right">Date: <?php echo date('d/m/Y'); ?></td>
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
            	
                   The Senior Chemist,<br>
                   Regional Agmark Laboratory,<br>
                   <?php echo $ral_office_address; ?> <br> 
            </td>
            <?php if(!empty($profile_photo)){ ?>
            <td align="right">
               <img src="<?php echo $profile_photo; ?>" width="auto" height="80" >
         </td>
         <?php } ?>
        </tr>
     </table> 
     <table  width="100%">
        <tr>    
            <td><br>Subject: Impart training of <?php echo $prefix."&nbsp;". $chemist_fname."&nbsp;". $chemist_lname ;?>, chemist of <?php echo $firmName; ?>, <?php echo $firm_address; ?> for analysis, grading and marking of <?php echo $commodities_cate_list; ?> ( <?php echo $sub_commodities_list; ?> ) under Agmark-reg.</td>
        </tr>
                    
        <tr>
            <td><br>Dear Sir/Madam,</td><br>
        </tr>   

        <tr>
            <td>With reference to above cited subject, it is to inform that <?php echo $firmName; ?>, <?php echo $firm_address; ?> has sponsored his chemist <?php  echo $prefix."&nbsp;". $chemist_fname."&nbsp;". $chemist_lname ;?>  <?php echo $middle_name_type; ?>  <?php echo $parent_name; ?> 
            for training for analysis, grading & marking of <?php echo $commodities_cate_list; ?> ( <?php echo $sub_commodities_list; ?> ) under Agmark.<br>
			
			The training charges of Rs. <?php echo $charges; ?> & necessary documents have been submitted in
            <?php echo $office_type; ?> office, <?php echo $ro_office;?>. In this connection it is requested to provide training to  
             <?php echo $prefix."&nbsp;". $chemist_fname."&nbsp;". $chemist_lname ;?>  <?php echo $middle_name_type; ?>  <?php echo $parent_name; ?> for analysis, grading & marking  of <?php echo $commodities_cate_list; ?> ( <?php echo $sub_commodities_list; ?> ) under Agmark.<br>
           The training has been scheduled from the <?php echo $schedule_from;?> to <?php echo $schedule_to;?>.
	
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
				 <?php echo $ro_fname."&nbsp;". $ro_lname; ?><br>
                 Incharge, <?php echo $office_type; ?> Office<br>
				Directorate of Marketing and Inspection<br>
                <?php echo $ro_office;?> <br>
			</td>
		</tr>
		<tr>
            <td><br></td>
        </tr>
	</table>


	<br>

	<table align="left">	
					
		<tr>
			<td>Copy to:<br> 
			 
            1.<?php echo $firmName; ?>, <?php echo $firm_address; ?> with this instruction to depute your chemist for necessary
                training in Regional Agmark Laboratory <?php echo $ral_office; ?>.<br>
            2.<?php echo $prefix."&nbsp;". $chemist_fname."&nbsp;". $chemist_lname ;?> <?php echo $middle_name_type; ?>  <?php echo $parent_name; ?> <?php echo $address; ?> to impart the training.
			</td>
		</tr>
		<tr>
            <td><br></td>
        </tr>
	</table>

	<br>
	<table align="right">	
					
		<tr>
			<td>
            <?php echo $ro_fname."&nbsp;". $ro_lname; ?><br>
                 Incharge, <?php echo $office_type; ?> Office<br>
				Directorate of Marketing and Inspection<br>
                <?php echo $ro_office;?> <br>
			</td>
		</tr>
		<tr>
            <td><br></td>
        </tr>
	</table>

  
    
	
	
        