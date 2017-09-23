<?php	header('Cache-Control: no-cache, must-revalidate');
	require("lib/lib.php");
	
	$referer = "http://dpekloges.gr/apps/vreg/p2.php?SID=" . $_SESSION['RegistrationSID'];
	if($_SERVER['HTTP_REFERER']!=$referer){
		//die('<h2>System Error!</h2>');
	}

	session_start();
	if($_SESSION['RegistrationSID'] != $_GET['SID']){
		 //die('<h2>System Error!</h2>');	
	}

		
?>

<!DOCTYPE html>
<html class="no-js">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Εγγραφή</title> 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <link rel="stylesheet" href="assets/css/Form-Select---Full-Date---Month-Day-Year.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/header1.css">
    <link rel="stylesheet" href="assets/css/header2.css">
    <link rel="stylesheet" href="assets/css/Navigation-Clean1.css">
    <link rel="stylesheet" href="assets/css/Registration-Form-with-Photo.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/dist/css/formValidation.min.css">    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="assets/dist/js/formValidation.min.js"></script>
    <script src="assets/dist/js/framework/bootstrap.min.js"></script>
    <script src="assets/dist/js/language/el_GR.js"></script>    
    <style type="text/css">
    	.register-photo {
			padding: 0px!important;
		}
        #RegistrationForm .form-control-feedback {
            pointer-events: auto;
        }
        #RegistrationForm .form-control-feedback:hover {
            cursor: pointer;
        }
    </style> 
  
    
    <!-- Import google fonts - Heading first/ text second -->
    <link href='http://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700' rel='stylesheet' type='text/css'>
    <!-- Css files -->
    <!-- Icons -->
    <link href="css/icons.css" rel="stylesheet" />
    <!-- Bootstrap stylesheets (included template modifications) -->
    <link href="css/bootstrap.css" rel="stylesheet" />
    <!-- Plugins stylesheets (all plugin custom css) -->
    <link href="css/plugins.css" rel="stylesheet" />
    
</head>
<body style="background-color:white!important;">

    <div class="register-photo" style="background-color:white!important;">
        <div class="form-container">
            <form onsubmit="return false;" id="RegistrationForm">
                Παρακαλούμε να αυτοαξιολογηθείτε απαντώντας στις παρακάτω ερωτήσεις.
				Οι απαντήσεις χρειάζονται μόνο για να σας ανατεθεί κάτι που να σας αρέσει και να μπορείτε να το φέρετε σε πέρας.
				<br/><br/>
				
				<b>• Είστε εξοικειωμένη/ος με τους Η/Υ;</b>  (κλίμακα 0-10)<br/>
				όπου 0 = δεν έρχομαι ποτέ σε επαφή και 10 = οι υπολογιστές είναι το επάγγελμά μου
             	<div class="row">
            		<div class="col-sm-8">
		                <div class="form-group">
		                	<input type="text" id="slider1">	
		                </div>
            		</div>
            	</div>
            	<br/>

				<b>• Είστε εξοικειωμένη/ος με τις οθόνες αφής;</b>  (κλίμακα 0-10)<br/>
				όπου 0 = μου είναι αδύνατον να χειριστώ οθόνη αφής <br/>και 10 = μπορώ να αναζητήσω ένα όνομα στο ΜΑΘΕ ΠΟΥ ΨΗΦΙΖΕΙΣ σε λιγότερο από 30 ''        	
             	<div class="row">
            		<div class="col-sm-8">
		                <div class="form-group">
		                	<input type="text" id="slider2">	
		                </div>
            		</div>
            	</div>
            	<br/>  

				<b>• Είστε εξοικειωμένη/ος με το πληκτρολόγιο;</b>  (κλίμακα 0-10)<br/>
				όπου 0 = δεν χρησιμοποιώ ποτέ πληκτρολόγιο <br/>
				και 10 = μπορώ να αναζητήσω ένα όνομα στο ΜΑΘΕ ΠΟΥ ΨΗΦΙΖΕΙΣ σε λιγότερο από 30''       	
             	<div class="row">
            		<div class="col-sm-8">
		                <div class="form-group">
		                	<input type="text" id="slider3">	
		                </div>
            		</div>
            	</div>
				<br/>     	
				<b>• Χειρίζεστε με άνεση απλές εφαρμογές στο κινητό;</b>  (κλίμακα 0-10)<br/>
				κλίμακα από 0 έως 10 όπου 0 = δεν έχω καμία εξοικείωση και 10 = δεν μου αντιστέκεται καμία εφαρμογή στο κινητό μου       	
             	<div class="row">
            		<div class="col-sm-8">
		                <div class="form-group">
		                	<input type="text" id="slider4">	
		                </div>
            		</div>
            	</div>
            	<br/>
  
				<b>• Είστε οργανωτικός τύπος;</b>  (κλίμακα 0-10)<br/>  
				όπου 0 = τα περιμένω όλα από τους άλλους και 10 = μπορώ να οργανώσω ένα συνέδριο για τις φάλαινες χωρίς καμία βοήθεια<br/>          	
             	<div class="row">
            		<div class="col-sm-8">
		                <div class="form-group">
		                	<input type="text" id="slider5">	
		                </div>
            		</div>
            	</div>
            	<br/>

				<b>• Δημιουργείτε εύκολα καλή σχέση με τους ανθρώπους;</b>  (κλίμακα 0-10)<br/>
				όπου 0 = κατοικώ μόνη/μόνος στην κορυφή ενός βουνού<br/>
				και 10 = μπορώ να ανοίξω κουβέντα με τον οποιονδήποτε, οπουδήποτε για όποιο θέμα νάναι.<br/>            	
             	<div class="row">
            		<div class="col-sm-8">
		                <div class="form-group">
		                	<input type="text" id="slider6">	
		                </div>
            		</div>
            	</div>
            	<br/>

				<b>• Θέλω να συμβάλλω στο νέο φορέα της κεντροαριστεράς:</b><br/>
				<div class="checkbox">
					<label><input type="checkbox" onclick="help1click()" id="Help1" value="">Θέλω να συμβάλω στην οργάνωση και διεξαγωγή των εκλογών του νέου φορέα της κεντροαριστεράς.</label>
				</div>
				<div class="well" id="HelpWell" >
					Την Κυριακή 5 Νοεμβρίου ποια βάρδια προτιμάτε;
					<div class="checkbox">
						<label><input type="checkbox" id="Help1a" value="">7πμ-2μμ</label>
					</div>
					<div class="checkbox">
						<label><input type="checkbox" id="Help1b" value="">2μμ-9μμ</label>
					</div><br/>
					Την Κυριακή 12 Νοεμβρίου ποια βάρδια προτιμάτε;
					<div class="checkbox">
						<label><input type="checkbox" id="Help1c" value="">7πμ-2μμ</label>
					</div>
					<div class="checkbox">
						<label><input type="checkbox" id="Help1d" value="">2μμ-9μμ</label>
					</div>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" id="Help2" value="">Στην επεξεργασία τεκμηριωμένων προτάσεων πολιτικής σε εθνικό επίπεδο</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" id="Help3" value="">Στην επεξεργασία τεκμηριωμένων προτάσεων πολιτικής για την τοπική αυτοδιοίκηση</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" id="Help4" value="">Την συμμετοχή σε δράσεις γύρω από θέματα που αφορούν την πόλη μου ή/και την εργασία μου</label>
				</div>

                <div class="form-group">
                    <input class="form-control" type="text" placeholder="Επάγγελμα" name="Job" id="Job" maxlength="250" />
                </div>
                <div class="form-group">
                	<textarea class="form-control" style="height:80px" placeholder="Άλλα ενδιαφέροντα" id="MiscSkills"></textarea>
                </div>            	    	
            	<br/><br/>
				   <div class="row">
			    	<div class="col-sm-6"><button class="btn btn-danger btn-block"  onclick="ResetData();return false">Επιστροφή στην αρχική</button></div>
				    <div class="col-sm-6"><button class="btn btn-success btn-block" onclick="submitData();return false">Καταχώριση</button></div>
				  </div>
            </form>
        </div>
    </div>



  <!--------------------------------------------------------------- ERROR MODAL --------------------------------------------------------------->
  <div class="modal fade" id="ErrModal" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" align="center">Προσοχή!</h4>
        </div>
        <div class="modal-body">

          <table style="height:100%!important;width:100%!important;border:0">
            <tbody>
              <tr style="height:20px"></tr>
              <tr>
                <td align="center">
                  <div class="alert alert-danger">
                    <span>
                        <div class="row" id="ErrorMsg">&nbsp;</div>
                    </span>
                  </div>
                </td>
              </tr>
              <tr style="height:10px"></tr>
            </tbody>
          </table>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Κλείσιμο</button>
        </div>
      </div>
    </div>
  </div>
  <!-------------------------------------------------------------------------------------------------------------------------------------------->


<script>
 

function submitData(){
	var Help1 = $("#Help1").prop('checked') ? 1: 0;
	var Help1a = $("#Help1a").prop('checked') ? 1: 0;
	var Help1b = $("#Help1b").prop('checked') ? 1: 0;
	var Help1c = $("#Help1c").prop('checked') ? 1: 0;
	var Help1d = $("#Help1d").prop('checked') ? 1: 0;
	var Help2 = $("#Help2").prop('checked') ? 1: 0;
	var Help3 = $("#Help3").prop('checked') ? 1: 0;
	var Help4 = $("#Help4").prop('checked') ? 1: 0;		
	$.post('u3.php', {Help1: Help1, Help1a: Help1a, Help1b: Help1b,
					  Help1b: Help1b, Help1c: Help1c, Help1d: Help1d, 
					  Help2: Help2, Help3: Help3, Help4: Help4,
					  slider1: $("#slider1").val(), slider2: $("#slider2").val(), 
					  slider3: $("#slider3").val(), slider4: $("#slider4").val(), 
					  slider5: $("#slider5").val(), slider6: $("#slider6").val(),
					  Job: $("#Job").val(), MiscSkills: $("#MiscSkills").val()
					}, function(data){
		if(data.Error == 0){
			location.replace("p4.php?ID_Number=" + data.ID_Number);	
		}else{
			$("#ErrorMsg").html(data.ErrorDescr);
			$('#ErrModal').modal('show');
		}
	}, "json");
}

    
function help1click(){
	if($("#Help1").prop('checked')){
		$("#HelpWell").show();
	}else{
		$("#HelpWell").hide();
		$("#Help1a").prop('checked', false);
		$("#Help1b").prop('checked', false);
		$("#Help1c").prop('checked', false);
		$("#Help1d").prop('checked', false);
	}
}  
	
function ResetData(){
	location.replace("p1.php");
}       	

//------------- sliders.js -------------//
$(document).ready(function() {
    $('#RegistrationForm')
     	.on('init.field.fv', function(e, data) {
            var $parent = data.element.parents('.form-group'),
                $icon   = $parent.find('.form-control-feedback[data-fv-icon-for="' + data.field + '"]');
            $icon.on('click.clearing', function() {
                if ($icon.hasClass('glyphicon-remove')) {
                	var reset = data.field != "HomePlace";
                    data.fv.resetField(data.element, reset);
                }
            });
        })    
	    .formValidation({
	        framework: 'bootstrap',
	        icon: {
	            valid: 'glyphicon glyphicon-ok',
	            invalid: 'glyphicon glyphicon-remove',
	            validating: 'glyphicon glyphicon-refresh'
	        }
	    });



	$("#slider1").focus();
	$("#HelpWell").hide();
	$("#slider1").bootstrapSlider({
		id: 'slider-success',
		min:0,
		max: 10,
		value: 0
	});
	$("#slider2").bootstrapSlider({
		id: 'slider-success',
		min:0,
		max: 10,
		value: 0
	});
	$("#slider3").bootstrapSlider({
		id: 'slider-success',
		min:0,
		max: 10,
		value: 0
	});
	$("#slider4").bootstrapSlider({
		id: 'slider-success',
		min:0,
		max: 10,
		value: 0
	});
	$("#slider5").bootstrapSlider({
		id: 'slider-success',
		min:0,
		max: 10,
		value: 0
	});
	$("#slider6").bootstrapSlider({
		id: 'slider-success',
		min:0,
		max: 10,
		value: 0
	});
});     	


</script>


        
        <!-- Javascripts -->
        <!-- Load pace first -->
        <script src="plugins/core/pace/pace.min.js"></script>
        <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
        <!-- Core plugins ( not remove ) -->
        <script src="js/libs/modernizr.custom.js"></script>
        <!-- Handle responsive view functions -->
        <script src="js/jRespond.min.js"></script>
        <!-- Custom scroll for sidebars,tables and etc. -->
        <script src="plugins/core/slimscroll/jquery.slimscroll.min.js"></script>
        <script src="plugins/core/slimscroll/jquery.slimscroll.horizontal.min.js"></script>
        <!-- Remove click delay in touch -->
        <script src="plugins/core/fastclick/fastclick.js"></script>
        <!-- Increase jquery animation speed -->
        <script src="plugins/core/velocity/jquery.velocity.min.js"></script>
        <!-- Quick search plugin (fast search for many widgets) -->
        <script src="plugins/core/quicksearch/jquery.quicksearch.js"></script>
        <!-- Bootbox fast bootstrap modals -->
        <script src="plugins/ui/bootbox/bootbox.js"></script>
        <!-- Other plugins ( load only nessesary plugins for every page) -->
        <script src="plugins/ui/bootstrap-slider/bootstrap-slider.js"></script>
        
        
    </body>
</html>