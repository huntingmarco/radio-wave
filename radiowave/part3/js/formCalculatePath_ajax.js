/*
1.purpose: this file uses AJAX to send the selected path to the server and to receive the path data to display
and finally display the results of the caculation including a table and a graph for the selected path data.
2. authors: Group2
*/
var calc ="";
$(document).ready( function(){
    
$("#calculateForm").submit( function(event){

	var vmsg = validateCalculateDetails(); 
	
	if(vmsg!==""){
		$("#detailResult").html("<h3> This form contains the following errors</h3><ul class='warning'>"+vmsg+"</ul></h3>");	 
    
    }
    else {
		
		$.post("formCalculatePath_ajax.php", $(this).serialize(), onNewPost);
		event.preventDefault();
	}

});
    

});

var onNewPost = function(response){
  //  console.log(response);

    if (response.status == "OK"){ 
        document.getElementById("factors").value="";
        $("input:radio[name='list_select[]']").each(function(i) {
            this.checked = false;
         }); 		
        try{       
            $("#detailResult").html("<div id=\"pathLoss\"></div><div id=\"pathGraph\" ></div><div id=\"pathInfoDiv\"></div><div id=\"midInfoDiv\"></div><div id=\"endInfoDiv\"></div>");

        }catch(e){
            console.log(e.message);
        }
    

    }
        
    $("#pathLoss").html("<h3>[ Calculation Results ]</h3><p>Path Attenuation (dB): "+response.calculate.pathAttenuation.toFixed(1)+"</p>");
    calc = response;
    //Graph Start 

    $("#pathGraph").html('<canvas id="canvas"></canvas>');
    
    var config = {
        type: 'line',
        data: {
            labels: calc.label,
            datasets: [{
                label: 'Path',
                backgroundColor: window.chartColors.red,
                borderColor: window.chartColors.red,
                data: calc.calculate.antenna,
                fill: false
            }, {
                label: 'Gnd + Obs',
                fill: false,
                backgroundColor: window.chartColors.blue,
                borderColor: window.chartColors.blue,
                data: calc.calculate.apparentGroundHeight
            }, {
                label: '1st Freznel',
                fill: false,
                backgroundColor: window.chartColors.green,
                borderColor: window.chartColors.green,
                data: calc.calculate.totalApparentHeight
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: response.pathInfo[0].pt_name+" with curvature "+response.curvature
            },
             scales: {
                yAxes: [{
                    ticks: {
                        min: 0
                    }
                }]
            }
        }
    };

    var ctx = document.getElementById('canvas').getContext('2d');
	const charts  = new Chart(ctx, config);
    //Graph END

    
    $("#pathInfoDiv").html("<table id=\"pathInfoTable\" border=\"1\"><tr><th>Path Name</th><th>Operating Frequency</th><th>Description</td><th>Note</th></tr></table>");
    for (r in response.pathInfo){
        $("#ptFreq-" + response.pathInfo[r].pt_id).html(response.pathInfo[r].pt_frequency);
        $("#ptDesc-" + response.pathInfo[r].pt_id).html(response.pathInfo[r].pt_description);
        $("#ptNote-" + response.pathInfo[r].pt_id).html(response.pathInfo[r].pt_note);
        $("#pathInfoTable").append(
                "<tr><td>"+ response.pathInfo[r].pt_name					+ "</td><td>" + response.pathInfo[r].pt_frequency
            + "</td><td>" + response.pathInfo[r].pt_description
            + "</td><td>" + response.pathInfo[r].pt_note+"</td></tr>"
        );
    }

    
    $("#midInfoDiv").html("<table id=\"midInfoTable\" border=\"1\"><tr><th>Distance Start To Mid Point</th><th>Ground Height</th><th>Terrain Type</td><th>Obstruction Height</th><th>Obstruction Type</th><th>Curvature Height</th><th>Apparent Ground and Obstruction Height</th><th>1st Freznel Zone</th><th>Total Clearance Height</th></tr></table>");
    for (var r=0;r<Object.keys(response.midInfo).length;r++){
        $("#midInfoTable").append(
                "<tr><td>"+ response.midInfo[r].md_distance.toFixed(4)					
            + "</td><td>" + response.midInfo[r].md_ground_height.toFixed(4)
            + "</td><td>" + response.midInfo[r].md_terrain_type
            + "</td><td>" + response.midInfo[r].md_obstruction_height.toFixed(4)
            + "</td><td>" + response.midInfo[r].md_obstruction_type
            + "</td><td>" + response.calculate.curvatureHeight[r].toFixed(4)
            + "</td><td>" + response.calculate.apparentGroundHeight[r+1].toFixed(4)
            + "</td><td>" + response.calculate.firstFreznelZone[r].toFixed(4)
            +"</td><td>" + response.calculate.totalApparentHeight[r+1].toFixed(4)+"</td></tr>" 
        );
    }

    $("#endInfoDiv").html("<table id=\"endInfoTable\" border=\"1\"><tr><th>Distance Start To End Point</th><th>Ground Height</th><th>Antenna Height</td><th>Antenna Cable Type</th><th>Antenna Cable Length</th></tr></table>");
    for (r in response.endInfo){
        $("#endInfoTable").append(
                "<tr><td>"+ response.endInfo[r].ed_distance					                    
            + "</td><td>" + response.endInfo[r].ed_ground_height
            + "</td><td>" + response.endInfo[r].ed_antenna_height
            + "</td><td>" + response.endInfo[r].ed_antenna_type
            + "</td><td>" + response.endInfo[r].ed_antenna_length +"</td></tr>"
        );
    }
	
};
 

function validateCalculateDetails(){
    var frm = document.forms["calculateForm"];
    var factors=document.getElementById("factors");
    var msg = "";
    var radio=false;
    for (var i = 0;i < frm.elements.length;i++)
    {
       if (frm.elements[i].type && frm.elements[i].type == "radio")
       {
         if (frm.elements[i].checked)
          {
            radio=true;
          }
       }
    }

    if(!radio){
        msg+="<li>No path data selected!! Please select a path data to calculate.</li>";
    }
    
    if(factors.value==""){
        msg+="<li>No earth curvature factor selected!! Please select an earth curvature factor to calculate.</li>";
    }

    return msg;

}
    

