<?php
session_start();

// Redirect user to login page if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

$new_password = $confirm_password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if (empty($new_password)) {
        $errors["new_password"] = "Please enter the new password.";
    } elseif (strlen($new_password) < 6) {
        $errors["new_password"] = "Password must have at least 6 characters.";
    }

    if (empty($confirm_password)) {
        $errors["confirm_password"] = "Please confirm the password.";
    } elseif ($new_password != $confirm_password) {
        $errors["confirm_password"] = "Password did not match.";
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($link, "UPDATE users SET password = ? WHERE id = ?");
        $param_password = password_hash($new_password, PASSWORD_DEFAULT);
        $param_id = $_SESSION["id"];
        mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        session_destroy();
        header("location: login.php");
        exit();
    }

    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <p>Please fill out this form to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-field">
                <input type="password" name="new_password" value="<?php echo $new_password; ?>">
                <label for="new_password">New Password</label>
                <span class="red-text"><?php echo $errors["new_password"]; ?></span>
            </div>
            <div class="input-field">
                <input type="password" name="confirm_password">
                <label for="confirm_password">Confirm Password</label>
                <span class="red-text"><?php echo $errors["confirm_password"]; ?></span>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Submit</button>
                <a class="btn-flat" href="welcome.php">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
