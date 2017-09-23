<?php

header('Content-type: application/json');
header('Cache-Control: no-cache, must-revalidate');
require("lib/lib.php");




$data['Error'] = 0;
$data['ErrorDescr'] = '';
echo json_encode($data);


?>