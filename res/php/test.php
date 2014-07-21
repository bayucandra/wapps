<?php
$a = [ 'a', 'b', 'c' ];
foreach(z($a) as &$x) {
    $x .= 'q';
}
print_r($a);

function bayu($a)
{
    return $a;
}
?>
