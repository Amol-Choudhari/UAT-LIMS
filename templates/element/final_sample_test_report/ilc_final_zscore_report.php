
<h6 style="text-align:centet;font-size:15px;">Final Zscore Result</h6>
<h6></h6>
<tr>
    <td><b>S.No.</b></td>											
    <td><b>Name Of Parameter</b></td>
    
    <?php
    foreach($result as $eachoff){ ?>
        <td>Actual Value</td>
        <td><?php echo $eachoff['ro_office']; ?> (<?php echo $eachoff['office_type']; ?>) Zscore</td>
    <?php
    }
    
    ?>

</tr>

<?php		

    if (isset($testarr)) {	

        $j=1;		
        $i=0;	
        foreach ($testarr as $eachtest) { ?>
        
        <tr>
            <td padding: 2px;><?php echo $j; ?></td>   
            <td><?php echo $testnames[$i]; ?> </td>
            <?php

                $l=0;
                
                foreach($smplList as $eachoff){
                    
                ?>
                <?php
                
                $num = $zscorearr[$i][$l];
                //number format in not match display NA
                if(is_numeric($num)){

                    $format = floor($num * 100)/100;
                }else{
                    $format = $num ;
                }
                ?>
                <!-- if value is not numeric show dropdown selected value  Dtae: 20-04-2023-->
                <td>
                    <?php echo $org_val[$i][$l]; ?>
                </td>
                <td>
                    <?php 
                    if(is_numeric($format)){
                        echo $format; 
                    }
                    else{  echo $format; }
                    ?>
                </td>
                
            <?php $l++;	} ?>


        </tr>
                    
    <?php $i++; $j++; } }  ?>
            
                    
                        


                    
                    