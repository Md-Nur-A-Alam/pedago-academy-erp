<?php  
 = file_get_contents('resources/views/welcome.blade.php');  
 = str_replace(chr(39).chr(39), chr(39), );  
file_put_contents('resources/views/welcome.blade.php', );  
echo 'done';  
