/*
1.purpose: this file uses AJAX to send the selected path to the server and to receive the path data to reset.
2. authors: Group2
*/
$(document).ready( function(){

	$("#resetList").submit( function(event){
		
		var vmsg = validateResetSelect(); 
		
		if(!vmsg){
			$("#detailResult").html("<h3> This form contains the following errors</h3><ul class='warning'><li>No path data selected!! Please select a path data to reset</li></ul>");	 
		}else {
			$("#detailResult").html("");
			if(confirm("Are you sure that you want to reset the path data back to original one?")){
				$.post("formResetData.php", $(this).serialize(), onNewPost);
				event.preventDefault();
			}
			else{
				$.get("formDisplayResetList.php");
				event.preventDefault();
			}
		}

	});


	var onNewPost = function(response){
	
		var resultData=response.split('"');

		if(resultData[1]=="errors"){
			
			var text= "<h3> This form contains the following errors</h3><ul class='warning'>";
			for(var i=2;i<resultData.length;i++)
			{
				text+="<li>"+resultData[i]+"</li>";
			}
			text+="</ul>";

			$('#detailResult').html(text);
	
		}
		else{
	
			$("#name"+resultData[2]).html(resultData[3]);
			$("#frequency"+resultData[2]).html(resultData[4]);
			$("#description"+resultData[2]).html(resultData[5]);
			$("#note"+resultData[2]).html(resultData[6]);
			alert("The path data has been reset successfully!")
		}

	};

});


function validateResetSelect(){
	
	var frm = document.forms["resetList"];

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
