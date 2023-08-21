<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

require_once "config.php";

$username = $password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username)) {
        $errors["username"] = "Please enter username.";
    }

    if (empty($password)) {
        $errors["password"] = "Please enter your password.";
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($link, "SELECT id, username, password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
            if (mysqli_stmt_fetch($stmt) && password_verify($password, $hashed_password)) {
                session_start();
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["username"] = $username;
                header("location: welcome.php");
            } else {
                $errors["login"] = "Invalid username or password.";
            }
        } else {
            $errors["login"] = "Invalid username or password.";
        }
        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php if (!empty($errors["login"])): ?>
            <div class="red-text"><?php echo $errors["login"]; ?></div>
        <?php endif; ?>

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
            <div class="form-group">
                <button type="submit" class="btn">Login</button>
            </div>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>
</body>
</html>
