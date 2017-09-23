<?php

require("lib/lib.php");


$con = openDB();


$sql = "SELECT
			vreg_municipalities.ID MunicipalityID,
			IF(vreg_municipalities.Region=10, 0, 1) AS Attica,
			vreg_municipalities.Municipality,
			vreg_divisions.Division,
			vreg_municipalities.Division AS DivisionID,
			vreg_municipalities.Region AS RegionID
		FROM
			vreg_municipalities
		INNER JOIN vreg_divisions ON vreg_municipalities.Division = vreg_divisions.ID
		ORDER BY Attica, vreg_divisions.Division, vreg_municipalities.Municipality";

$result = mysqli_query($con, $sql);	
while($row = mysqli_fetch_array($result)){
	$MunicipalityID = $row['MunicipalityID'];
	$Municipality = $row['Municipality'];	
	$DivisionID = $row['DivisionID'];	
	$Division = $row['Division'];

	if($OldDivisionID != $DivisionID){
		$GroupLabel = $row['RegionID']==10 ? "Δήμοι $Division": "Νομός $Division";		
		$FirstGroup = $OldDivisionID == 1 ? "":"</optgroup>";
		$municipalities .= "$FirstGroup<optgroup label='$GroupLabel'>";
	}
	$municipalities .= "<option value='$Division'>$Municipality</option>";
	$OldDivisionID = $DivisionID;
}
$municipalities .= "</optgroup>";
?>


<select class="form-control" id="Municipality">
<option></option>
<?php echo $municipalities ?>
</select>

<script>
	$("#Municipality").change(function(){							
		$("#Division").val($("#Municipality").val());
	});	
</script>

