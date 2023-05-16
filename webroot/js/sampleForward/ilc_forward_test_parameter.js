$(document).ready(function () { 
    // added for  change parameterList
    //  Done by shreeya on 07-11-2022 
    var count=0;
    var ralCount=0;
    $("#parameterList").change(function(){
    

      serialrno();

      count = $('#TesTable tr').length+1;
      ralCount = $('#tbodyid tr').length+1;

      // count of test show save button 09-11-2022
      if (count >= 2) {

        if(ralCount >=5){//add for check the count show save button 10-11-2022

          $('#addselectbtn').show();
          $('#flashnote').show();
         
        }
       
       }else{
        $('#ral').hide();
        $('#addselectbtn').hide();
      }


      var test = $('#parameterList option:selected').text();
      var testid = $('#parameterList').val();
     
      /* check duplicate records in table done by shreeya on 07/11/2022 */
      //  duplicate validation for dyanamic select option 
      var checktestexit = '';
      $('#TesTable tr').each(function(index, tr) {
        $(tr).find('td').each (function (index1, td) {
      
          if (testid == $(this).attr('id')) {
            alert('This Test is already selected');
            checktestexit = "yes";
            return false;
          }
          
          
        });

      });
      if (checktestexit == '') {
       var markup = "<tr><td class='text-center'>" + count + "</td><td id='"+testid+"' class='text-center'>" + test + "</td><td class='text-center'><button type='button' class='btn btn-danger remove'><i class='glyphicon glyphicon-remove'></i></td></tr>";
        $("#TesTable").append(markup);
      }
     
 
    });


    // jQuery button click event to remove a row.
    $('#TesTable').on('click', '.remove', function () {

      // Removing the current row.
      $(this).closest('tr').remove();

       count = $('#TesTable tr').length+1;
     
       if (count <= 1) {
         $('#ral').attr('disabled',true);
         $('#addselectbtn').hide();
         $('#flashnote').hide();
       }else{
         $('#ral').attr('disabled',false);
       }
      
       serialrno();
    });


  });

  //added for sr no do not repeted in the  table done 10-01-2023 
  function serialrno(){

    var len= $('#srno tbody tr').length;
  
    for(var i=0; i<len; i++){
  
      $('#srno tbody tr').eq(i).find('td:nth-child(1)').text((i+1))
  
    }
  
  }