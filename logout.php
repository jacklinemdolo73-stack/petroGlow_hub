<?php
session_start();
session_unset();
session_destroy();

// Futa na cookies za kivinjari zilizohifadhiwa
if (isset($_COOKIE['logged_in_user'])) {
    setcookie('logged_in_user', '', time() - 3600, '/');
}

// Safisha na kukupeleka login page kwa nguvu
echo "<script>
    localStorage.clear();
    window.location.replace('login.php');
</script>";
exit();
?>