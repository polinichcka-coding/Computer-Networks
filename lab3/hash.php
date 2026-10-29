<?php
echo "User 1: " . password_hash("password123", PASSWORD_DEFAULT) . "<br>";
echo "User 2: " . password_hash("password456", PASSWORD_DEFAULT) . "<br>";
echo "User 3: " . password_hash("password789", PASSWORD_DEFAULT) . "<br>";
?>