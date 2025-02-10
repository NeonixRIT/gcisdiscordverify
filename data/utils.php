<?php
function encode_args_b64($args) {
    $args_str = "";
    foreach ($args as $arg) {
        $encoded_arg = base64_encode($arg);
        $args_str = "{$args_str} {$encoded_arg}";
    }
    $args_str = trim($args_str);
    if (strlen($args_str) >= 2048) {
        die("Arguments too long.");
    }
    return base64_encode($args_str);
}
?>