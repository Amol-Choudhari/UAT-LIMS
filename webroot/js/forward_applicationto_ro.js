jQuery(document).ready(function($) {
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
      format: 'dd/mm/yyyy'
   })
   .on('changeDate', function (selected) {
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

});