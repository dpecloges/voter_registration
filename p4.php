<?php
	header('Cache-Control: no-cache, must-revalidate');
	session_destroy();
	$ID_Number = $_GET['ID_Number'];
?>

<!DOCTYPE html>
<html>

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
</head>
<body style="background-color:white!important;">
    <div class="register-photo" style="background-color:white!important;">
        <div class="form-container">
        	<input type="text" id="focus" />
            <form onsubmit="return false;" id="RegistrationForm">
            	<table>
					<tr>
						<td id="td1" align="center"><h2>Ευχαριστούμε για την εγγραφή σου στους καταλόγους των εθελοντών</h2><br/></td>
					</tr>
					<tr>
						<td align="center"><h4>Ο προσωπικός σου κωδικός εθελοντή είναι <span style="font-size:24px!important;"><?php echo $ID_Number ?></span></h4></td>
					</tr>
				</table>
				
				
            </form>
        </div>
    </div>   
</body>

</html>

<script>
	$(document).ready(function() {
		$("#focus").focus();
		$("#focus").hide();
		
	});
</script>
