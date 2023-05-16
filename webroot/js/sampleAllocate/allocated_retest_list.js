$(document).ready(function(){

  $('#allocated_sample_table').DataTable();
  $('#forwarded_sample_table').DataTable();

});

$("#F_list").hide();
$("#A_list").show();

$('.type').change(function(){

    var type=$(".type:checked").val();
    if(type=='A')
    {
      $("#F_list").hide();
      $("#A_list").show();

    }else{
      $("#A_list").hide();
      $("#F_list").show();

    }


});
