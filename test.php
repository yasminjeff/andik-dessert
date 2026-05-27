<?php
$hash = '$2y$04$RqK8H1Z3xJ9mN2pL5vW7OeYbT6sA4dF0gH8iJ2kM3nP1qR5tU7wX9';
$pass = 'password';

if (password_verify($pass, $hash)) {
    echo "MATCH!";
} else {
    echo "NO MATCH";
}
?>