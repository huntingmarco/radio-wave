/*
1.purpose: this file uses AJAX to send the selected path to the server and to receive the path data to display.
2. authors: Group2
*/
$(document).ready( function(){

$("#DetailsForm").submit( function(event){

	var vmsg = validateViewDetails(); 
	
	if(!vmsg){
		$("#detailResult").html("<h3 style='color:red;'>No path data selected!! Please select a path data to display.</h3>");	 
	}else {
		
		$.post("formViewDetails.php", $(this).serialize(), onNewPost);
		event.preventDefault();
	}

});

var onNewPost = function(response){
	$("#detailResult").html(response);
};

});


function validateViewDetails(){
    

var frm = document.forms["DetailsForm"];

var msg = 0;
for (var i = 0;i < frm.elements.length;i++)
{
   if (frm.elements[i].type && frm.elements[i].type == "radio")
   {
     if (frm.elements[i].checked)
      {
         
          return true;
      }
   }
}
   
    return false;
	
	
	
}				
	
function topFunction() {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}	

