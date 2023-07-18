

<h6 style="text-align:center;font-size:15px;">Final Zscore Result</h6>
<h6></h6>
<tr>
    <td><b>S.No.</b></td>											
    <td><b>Name Of Parameter</b></td>
 <!-- change the condtion accoring to ro_office type by shreey on date [14-07-2023] -->
 <?php foreach ($result as $eachoff) { ?>
    <?php if ($eachoff['ro_office'] == 'CAL Nagpur' || $eachoff['ro_office'] == 'Nagpur') { ?>
        <th scope="col"><?php echo $eachoff['ro_office']; ?> (<?php echo $office_type = 'CAL'; ?>) Actual Value</th>
        <th scope="col"><?php echo $eachoff['ro_office']; ?> (<?php echo $office_type = 'CAL'; ?>) Zscore</th>
    <?php } else { ?>

        <th scope="col"><?php echo $eachoff['ro_office']; ?> (<?php echo $eachoff['office_type']; ?>) Actual Value</th>
        <th scope="col"><?php echo $eachoff['ro_office']; ?> (<?php echo $eachoff['office_type']; ?>) Zscore</th>
    <?php } ?>
<?php } ?>

</tr>
<?php
if (isset($testarr)) {
    $j = 1;
    $i = 0;
    foreach ($testarr as $eachtest) {
        ?>
        <tr>
            <td padding: 2px;><?php echo $j; ?></td>
            <td><?php echo $testnames[$i]; ?> </td>
            <?php
            $l = 0;
            foreach ($smplList as $eachoff) {
              
                $num = $zscorearr[$i][$l];
               
                ?>
                <td>
                    <?php echo $org_val[$i][$l]; ?>
                </td>
                <td>
                    <?php
                    // numeric value added if condition further else part is running
                    if (is_numeric($num)) {
                        $format = floor($num * 100) / 100;
                        echo $format;
                    } else {
                        echo $num;
                    }
                    ?>
                </td>
                <?php
                $l++;
            }
            ?>
        </tr>
        <?php
        $i++;
        $j++;
    }
}
?>


            
    
                        


                    
                    