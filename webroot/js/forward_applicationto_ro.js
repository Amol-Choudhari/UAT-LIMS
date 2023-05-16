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
  $('#sheduleTo').datepicker({
    
    autoclose: true,
    setDate: new Date(),
    startDate:'+0d',
    format: 'dd/mm/yyyy'
  });

  $('#sheduleFrom').datepicker({
    setDate: new Date(),
    autoclose: true,
    startDate:'+0d',
    format: 'dd/mm/yyyy'
  });


});