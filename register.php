<?php
require_once "config.php";

$username = $password = $confirm_password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if (empty($username)) {
        $errors["username"] = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors["username"] = "Username can only contain letters, numbers, and underscores.";
    } else {
        $stmt = mysqli_prepare($link, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            $errors["username"] = "This username is already taken.";
        }
        mysqli_stmt_close($stmt);
    }

    if (empty($password)) {
        $errors["password"] = "Please enter a password.";
    } elseif (strlen($password) < 6) {
        $errors["password"] = "Password must have at least 6 characters.";
    }

    if (empty($confirm_password)) {
        $errors["confirm_password"] = "Please confirm password.";
    } elseif ($password != $confirm_password) {
        $errors["confirm_password"] = "Password did not match.";
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($link, "INSERT INTO users (username, password) VALUES (?, ?)");
        $param_password = password_hash($password, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "ss", $username, $param_password);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

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
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-field">
                <input type="text" name="username" value="<?php echo $username; ?>">
                <label for="username">Username</label>
                <span class="red-text"><?php echo $errors["username"]; ?></span>
            </div>
            <div class="input-field">
                <input type="password" name="password">
                <label for="password">Password</label>
                <span class="red-text"><?php echo $errors["password"]; ?></span>
            </div>
            <div class="input-field">
                <input type="password" name="confirm_password">
                <label for="confirm_password">Confirm Password</label>
                <span class="red-text"><?php echo $errors["confirm_password"]; ?></span>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Submit</button>
                <button type="reset" class="btn grey">Reset</button>
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>
