/*
1.purpose: this file uses AJAX to send the selected path to the server and to receive the path data to display.
2. authors: Group2
*/
$(document).ready( function(){
var gobalData;
$("#DetailsForm").submit( function(event){

	var vmsg = validateEditDetails(); 
	
	if(!vmsg){
		$("#detailResult").html("<h3 style='color:red;'>No path data selected!! Please select a path data to display.</h3>");	 
	}else {
		
		$.post("formEditPath_ajax.php", $(this).serialize(), onNewPost);
		event.preventDefault();
	}

});
    

});
var onEditPost = function(response){
    
    if(response.status == "OK"){
       alert(response.msg);
        $("#DetailsForm").submit();
    }else {
        var msg ="";
        for(m in response.msg){
         msg +=response.msg[m] + "\n";
        }
        alert(msg);
    }
   
    
    
}
    

var onNewPost = function(response){
    console.log(response);
    globalData = response;
    
    if (response.status == "OK"){ 
        		
                try{
                  
                    $("#detailResult").html("<div id=\"pathInfoDiv\"></div><div id=\"midInfoDiv\"></div><div id=\"endInfoDiv\"></div><div id=\"modal\" style=\"display:none;\"></div>");
                    
                }catch(e){
                    console.log(e.message);
                }
           		
       		}
           	$("#pathInfoDiv").html("<table id=\"pathInfoTable\" border=\"1\"><tr><th>Path Name</th><th>Operating Frequency</th><th>Description</td><th>Note</th><th>Action</th></tr></table>");
			for (r in response.pathInfo){
                $("#ptFreq-" + response.pathInfo[r].pt_id).html(response.pathInfo[r].pt_frequency);
                $("#ptDesc-" + response.pathInfo[r].pt_id).html(response.pathInfo[r].pt_description);
                $("#ptNote-" + response.pathInfo[r].pt_id).html(response.pathInfo[r].pt_note);
				$("#pathInfoTable").append(
					  "<tr><td>"+ response.pathInfo[r].pt_name					+ "</td><td>" + response.pathInfo[r].pt_frequency
					+ "</td><td>" + response.pathInfo[r].pt_description
					+ "</td><td>" + response.pathInfo[r].pt_note 
                    + "</td><td><input id=\"tmp_ptEdit\" type=\"button\" name=\"tmp_ptEdit\" onclick=\"pathEditForm(" + r + ");\" value=\"Edit\"></td></tr>"
				);
			}
    
            
             $("#midInfoDiv").html("<table id=\"midInfoTable\" border=\"1\"><tr><th>Distance Start To Mid Point</th><th>Ground Height</th><th>Terrain Type</td><th>Obstruction Height</th><th>Obstruction Type</th><th>Action</th></tr></table>");
			for (r in response.midInfo){
				$("#midInfoTable").append(
					  "<tr><td>"+ response.midInfo[r].md_distance					+  "</td><td>" + response.midInfo[r].md_ground_height
					+ "</td><td>" + response.midInfo[r].md_terrain_type
					+ "</td><td>" + response.midInfo[r].md_obstruction_height
                    + "</td><td>" + response.midInfo[r].md_obstruction_type 
                    + "</td><td><input id=\"tmp_ptEdit\" type=\"button\" name=\"tmp_ptEdit\" onclick=\"midEditForm(" + r + ");\" value=\"Edit\"></td></tr>"
				);
			}
    
             $("#endInfoDiv").html("<table id=\"endInfoTable\" border=\"1\"><tr><th>Distance Start To End Point</th><th>Ground Height</th><th>Antenna Height</td><th>Antenna Cable Type</th><th>Antenna Cable Length</th><th>Action</th></tr></table>");
			for (r in response.endInfo){
				$("#endInfoTable").append(
					  "<tr><td>"+ response.endInfo[r].ed_distance					                    
					+ "</td><td>" + response.endInfo[r].ed_ground_height
					+ "</td><td>" + response.endInfo[r].ed_antenna_height
                    + "</td><td>" + response.endInfo[r].ed_antenna_type
                    + "</td><td>" + response.endInfo[r].ed_antenna_length 
                    + "</td><td><input id=\"tmp_ptEdit\" type=\"button\" name=\"tmp_ptEdit\" onclick=\"endEditForm(" + r + ");\" value=\"Edit\"></td></tr>"
				);
			}
	
};
    


function updateForm(event){
	
    $.post("formEditPath_ajax.php", $(this).serialize(), onEditPost);
                
    event.preventDefault();
}
    
function pathEditForm(id){
    
    $("#modal").html("<div id=\"modal-content\"></div>");
    $("#modal-content").html("<form id=\"UpdateForm\"><h2>Edit Path Information</h2><p id=\"error_modal\"></p><table id=\"editModalTable\" border=\"1\"><tr><th>Field Name</th><th>Value</th></tr</table></form>");     
    for (r in globalData.pathInfo){
        if(r == id){
            $("#editModalTable").append(
				    "<tr><td>Path Name: </td><td>"+ globalData.pathInfo[r].pt_name					
                    + "</td></tr><tr><td>Frequency: </td><td><input type=\"text\" name=\"ptFrequency\" value=\""+ globalData.pathInfo[r].pt_frequency
					+ "\"></td></tr><tr><td>Description: </td><td><input type=\"text\" name=\"ptDescription\" value=\"" + globalData.pathInfo[r].pt_description
					+ "\"></td></tr><tr><td>Note: </td><td><input type=\"text\" name=\"ptNote\" value=\"" + globalData.pathInfo[r].pt_note 
                    + "\"></td></tr><tr><td colspan='2'><input type=\"hidden\" name=\"action\" value=\"update\"><input type=\"hidden\" name=\"ptId\" value=\""+ globalData.pathInfo[r].pt_id +"\"><input id=\"saveModalData\" type=\"submit\" name=\"saveModalData\" value=\"Save\"><input id=\"tmp_ptEdit\" type=\"button\" name=\"tmp_ptEdit\" onclick=\"clearModal();\" value=\"Cancel\"></td></tr>"
				);
        }
        
    }
    
    
    $("#UpdateForm").on( "submit", updateForm);
    $("#modal").show();
    
    
}  

function midEditForm(id){
    
    $("#modal").html("<div id=\"modal-content\"></div>");
    $("#modal-content").html("<form method=\"POST\" id=\"UpdateForm\"><h2>Edit Mid Point Information</h2><p id=\"error_modal\"></p><table id=\"editModalTable\" border=\"1\"><tr><th>Field Name</th><th>Value</th></tr</table></form>");     
    for (r in globalData.midInfo){
        if(r == id){
            $("#editModalTable").append(
				    "<tr><td>Distance Start to Mid Point: </td><td>"+ globalData.midInfo[r].md_distance					
                    + "</td><tr><td>Ground Height: </td><td><input type=\"text\" name=\"mdGroundHeight\" value=\""+ globalData.midInfo[r].md_ground_height
					+ "\"></td></tr><tr><td>Terrain Type: </td><td><input type=\"text\" name=\"mdTerrainType\" value=\"" + globalData.midInfo[r].md_terrain_type
					+ "\"></td></tr><tr><td>Obstruction Height: </td><td><input type=\"text\" name=\"mdObstructionHeight\" value=\"" + globalData.midInfo[r].md_obstruction_height 
                    + "\"></td></tr><tr><td>Obstruction Type: </td><td><input type=\"text\" name=\"mdObstructionType\" value=\"" + globalData.midInfo[r].md_obstruction_type 
                    + "\"></td></tr><tr><td colspan='2'><input type=\"hidden\" name=\"action\" value=\"update\"><input type=\"hidden\" name=\"mdId\" value=\""+ globalData.midInfo[r].md_id +"\"><input id=\"saveModalData\" type=\"submit\" name=\"saveModalData\" value=\"Save\"><input id=\"tmp_ptEdit\" type=\"button\" name=\"tmp_ptEdit\" onclick=\"clearModal();\" value=\"Cancel\"></td></tr>"
				);
        }
        
    }
     $("#UpdateForm").on( "submit", updateForm);
    $("#modal").show();
    
}    

function endEditForm(id){
    
    $("#modal").html("<div id=\"modal-content\"></div>");
    $("#modal-content").html("<form method=\"POST\" id=\"UpdateForm\"><h2>Edit End Point Information</h2><p id=\"error_modal\"></p><table id=\"editModalTable\" border=\"1\"><tr><th>Field Name</th><th>Value</th></tr</table></form>");     
    for (r in globalData.endInfo){
        if(r == id){
            $("#editModalTable").append(
				    "<tr><td>Distance Start to End Point: </td><td>"+ globalData.endInfo[r].ed_distance					
                    + "</td><tr><td>Ground Height: </td><td><input type=\"text\" name=\"edGroundHeight\" value=\""+ globalData.endInfo[r].ed_ground_height
					+ "\"></td></tr><tr><td>Antenna Height: </td><td><input type=\"text\" name=\"edAntennaHeight\" value=\"" + globalData.endInfo[r].ed_antenna_height
					+ "\"></td></tr><tr><td>Antenna Cable Type: </td><td><input type=\"text\" name=\"edAntennaCableType\" value=\"" + globalData.endInfo[r].ed_antenna_type 
                    + "\"></td></tr><tr><td>Antenna Cable Length: </td><td><input type=\"text\" name=\"edAntennaCableLength\" value=\"" + globalData.endInfo[r].ed_antenna_length
                    + "\"></td></tr><tr><td colspan='2'><input type=\"hidden\" name=\"edId\" value=\""+ globalData.endInfo[r].ed_id +"\"><input type=\"hidden\" name=\"action\" value=\"update\"><input id=\"saveModalData\" type=\"submit\" name=\"saveModalData\" value=\"Save\"><input id=\"tmp_ptEdit\" type=\"button\" name=\"tmp_ptEdit\" onclick=\"clearModal();\" value=\"Cancel\"></td></tr>"
				);
        }
        
    }
    $("#UpdateForm").on( "submit", updateForm);
    $("#modal").show();
    
}  

function clearModal() {
    $("#modal").hide();
    $("#modal").html("");
}




function validateEditDetails(){
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

