<?php 

$image = 'C:\Users\vinay\programmingPractice\phpProjects\roomsReservation\writable\uploads\1652073749.png';
$type = pathinfo($image, PATHINFO_EXTENSION);
$data = file_get_contents($image);
$dataUri = 'data:image/' . $type . ';base64,' . base64_encode($data);
$myFile = fopen("a.txt","w");
fwrite($myFile,$dataUri);
fclose($myFile);