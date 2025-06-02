<?php
// Function to validate password strength
function isValidPassword($password) {
    // Check for a minimum length of 8 characters, one uppercase, one lowercase, one digit, and one special character
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/', $password);
}
?>
