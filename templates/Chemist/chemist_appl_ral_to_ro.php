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
    foreach($sub_commodity_data as $sub_commodity){
        
        $sub_commodities_array[$i] = $sub_commodity['commodity_name'];
    $i=$i+1;
    } 
    
    $sub_commodities_list = implode(',',$sub_commodities_array);
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
            	
                   The Asstt. Agriculture Marketing Adviser,<br>
                   Directorate of Marketing and Inspection,<br>
                   Regional Office,<br>
                   <?php echo $ro_office; ?> <br> 
            </td>
        </tr>

        <tr>    
            <td><br>Subject: Training of <?php echo $chemist_fname."&nbsp;".$chemist_lname;?> in the analysis of <?php echo $sub_commodities_list; ?> – reg..
            </td>
        </tr>
                    
        <tr>
            <td><br>Dear Sir,</td><br>
        </tr>   

        <tr>
            <td>
               With reference to the Regional Office, O.M. No. [Number] dated [date], regarding training in the analysis of <?php echo $sub_commodities_list; ?> to <?php echo $chemist_fname."&nbsp;".$chemist_lname;?> sponsored chemist of  <?php echo $firm_name; ?> <?php echo $firm_address; ?> has completed his training from <?php echo $schedule_from;?> to <?php echo $schedule_to;?> in the analysis in of <?php echo $sub_commodities_list; ?>.<br>

               He/she is hereby relieved in on <?php echo $reliving_date;?>.<br>
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
				<?php echo $ral_role;?>,<br>
				Regional Agmark Laboratory,<br> 
				RAL Office.<br>
			</td>
		</tr>
		<tr>
            <td><br></td>
        </tr>
	</table>


	<br>

	


    
	
	
        