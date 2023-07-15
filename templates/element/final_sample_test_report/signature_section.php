
	<table width="100%">
		<!-- add the report no by shreeya on date [14-07-2023] -->
	  <tr>
		<td></td>
		<td align="right"><b>Report No <?php if(isset($test_report)) { echo $test_report[0]['report_no']; } ?></b></td>
	  </tr>	
	  <br><br><br>
	  <tr>
		<td></td>
		<td align="right"><b>(Authorized Signatory/Incharge)</b> <br><br> <?php if(isset($test_report)) { echo $test_report[0]['grade_user_flag'].','.$test_report[0]['ro_office']; } ?></td>
	  </tr>	
	  

	 
	</table>