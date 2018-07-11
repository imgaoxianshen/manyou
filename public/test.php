<?php
$a = file_get_contents('a.txt');
file_put_contents('a.txt',$a.PHP_EOL.'this is test '.date('Y-m-d H:i:s'));
