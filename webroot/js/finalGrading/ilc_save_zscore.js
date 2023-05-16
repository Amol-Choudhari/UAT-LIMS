
$(document).ready(function(){

   
    // $("#get_zscore").hide();
  
    // adde for save selected value and zscore
	// Date:- 17-03-2023
	$("#save_zscore").click(function(e) {

		e.preventDefault();
		var testArr = JSON.parse(JSON.stringify($("#tstarr").val())); 
		var sampleArr =  JSON.parse(JSON.stringify($("#smplarr").val()));
      	var org_val = [];
        var org_val_sub = [];
        var org_val_ind = 0;
		var zscore =[];
        var zscore_sum_sub = [];
        var zscore_sum_ind = 0;
		
		var i=0;
        var j=0;
        var totalCol = $('#save_selected_zscore tr:first td').length;
        var initialOrgIndex = 2;
        var initialZscoreIndex = 3;
      
        $('#save_selected_zscore tr').each(function(index, tr) {
			$(tr).find('td').each (function (index1, td) {
			  
                /*if(index1==1){
                    test_name[i] =$(this).html(); 
                    
                }*/
                // below code added for Shows the current available orignal value of zscore [Date : 04-04-2023]
                if(index1==initialOrgIndex){
                    if(index1==2){
                        org_val_sub = []; 
                        org_val_ind = 0;   
                    }
                    org_val_sub[org_val_ind] = $(this).html(); 
                    initialOrgIndex += 2;
                    org_val_ind++;
                }
               //below code added for zscore or numeric value for the selected option. Shows the current available z score value and option. [Date : 04-04-2023]
                if(index1==initialZscoreIndex){
                    //  zscore[i] = $(this).html();
                    //zscore[i] = $('#org_value').find(":selected").text();     
                    if(index1==3){
                        zscore_sum_sub = []; 
                        zscore_sum_ind = 0;   
                    }
                    var zscore_select_val = ($(this).find('select').length > 0) ? $(this).find('select option:selected').text() : $(this).html();
                    zscore_sum_sub[zscore_sum_ind] = zscore_select_val; 
                    initialZscoreIndex += 2;
                    zscore_sum_ind++;
                }
                
                    if(index1==(totalCol-1)){
                        initialOrgIndex = 2;
                        initialZscoreIndex = 3;
                    }
                    j++;  
			});

           
            org_val[i] = org_val_sub; /*orignal value store in array format */
            zscore[i] = zscore_sum_sub;/* zscore selected option*/
            i++;  
	        
		});

        var stage_sample_code  = $('#stage_sample_code').val();

        /* store the data using ajax */
        $.ajax({
    
            type:'POST',
            url:'../FinalGrading/save_final_zscore',
            data:{testArr:testArr,sampleArr:sampleArr,org_val:org_val,zscore:zscore,stage_sample_code:stage_sample_code},
            async:true,
            cache:false,
            beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function (data){
             
                alert('Successfully Added.'); 
                // location.window.href = "ilc-available-sample-zscore";
                        
            }
            

        });

    });

});



