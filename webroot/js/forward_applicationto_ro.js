jQuery(document).ready(function($) {
  $('.ral_to_ro').DataTable();
	$(function() {
  $("#trainingCompleted").on("click",function() {
    if($('#trainingCompleted').is(':checked')){

      $('#submitbtn').show();
    }else{
    	$('#submitbtn').hide();
    }
    
  });
});



  //datepicker added by laxmi on 28-12-2022
  // The Calender


   $('#sheduleFrom').datepicker({
      setDate: new Date(),
      autoclose: true,
      startDate:'+0d',
      format: 'dd/mm/yyyy',
      
   })
   
   .on('changeDate', function (selected) {
    $('#sheduleTo').val(''); //to change date of from become empty to date added by laxmi on 01-09-2023
      startDate = new Date(selected.date.valueOf());
      //startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
      startDate.setDate(startDate.getDate() + 1);    
      $('#sheduleTo').datepicker('setStartDate', startDate);
   });



  $('#sheduleTo').datepicker({
    
    autoclose: true,
    setDate: new Date(),
    startDate:'+0d',
    format: 'dd/mm/yyyy'
  })
  .on('changeDate', function (selected) { 
    FromEndDate = new Date(selected.date.valueOf());
    FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
   
    $('#sheduleFrom').datepicker('setEndDate', FromEndDate);
});
});





//for confirm dates open quitely reschedule form and set values
$(document).ready(function(){
  var localStorageItem = window.localStorage.getItem('ConfirmClick');
  if(localStorageItem == 'yes'){
    window.localStorage.removeItem('ConfirmClick');
    $("#submitbtnn")[0].click();
  }


//added reschedule submit btn validation by laxmi on 01-09-2023
  $('#submitbtnn').click(function(){
   var fromDate =  $('#sheduleFrom').val();
   var toDate =  $('#sheduleTo').val();
   var returnValue = true;
   if(fromDate == ''){
    $('.err_cv_reshedule_from_date').html("Please select Reschedule From Date");
    returnValue = false;
   }
   if(toDate == ''){
    $('.err_cv_reshedule_to_date').html("Please select Reschedule To Date");
    returnValue = false;
   }


   if(returnValue == false){
    return false;
   }else{
    return true;
   }
  });
});