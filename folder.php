<?php

$zip = new ZipArchive;
$zip->open('Downloads/abc.zip', ZipArchive::CREATE);
ini_set('max_execution_time', 300);
$zip->addEmptyDir('newDirectory');
$zip->addFromString('newDirectory/0.jpg', 'https://scontent.xx.fbcdn.net/v/t1.0-9/39065268_509894362785948_3699463796872445952_o.jpg?_nc_cat=0&oh=12e35f7e448b9edaab224493075df52e&oe=5BEF8F40');
$zip->close();

?>