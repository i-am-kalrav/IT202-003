<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (isset($_POST["submit"])) {
    $email = se($_POST, "email", null, false);
    $password = trim(se($_POST, "password", null, false));

    $isValid = true;
    if (!isset($email) || !isset($password)) {
        flash("Must provide email/username and password", "warning");
        $isValid = false;
    }
    if (strlen($password) < 3) {
        flash("Password must be 3 or more characters", "warning");
        $isValid = false;
    }
    //$email = sanitize_email($email);  <-------- Because login with both email and username
    /*if (!is_valid_email($email)) {
        flash("Invalid email", "warning");
        $isValid = false;
    }*/
    if ($isValid) {
        //do our registration
        $db = getDB();
        //$stmt = $db->prepare("INSERT INTO Users (email, password) VALUES (:email, :password)");
        //$hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("SELECT id, email, IFNULL(username, email) as `username`, password from Users where email = :email or username = :email LIMIT 1");
        try {
            $stmt->execute([":email" => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $upass = $user["password"];
                if (password_verify($password, $upass)) {
                    flash("Login successful", "success");
                    unset($user["password"]);
                    //save user info
                    $_SESSION["user"] = $user;
                    //lookup roles assigned to this user
                    $stmt = $db->prepare("SELECT Roles.name FROM Roles 
                    JOIN UserRoles on Roles.id = UserRoles.role_id 
                    where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                    $stmt->execute([":user_id" => $user["id"]]);
                    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    //save roles or empty array
                    if ($roles) {
                        $_SESSION["user"]["roles"] = $roles;
                    } else {
                        $_SESSION["user"]["roles"] = [];
                    }
                    //echo "<pre>" . var_export($_SESSION, true) . "</pre>";

                    get_or_create_account(); //applies directly to the session, make sure it's called after the session is set

                    refresh_last_login();
                    //put the function here as it's the least frequent "activation" that won't go too long without running
                    //calc_winners_or_expire();//This can cost up to ~100 queries
                    die(header("Location: home.php"));
                } else {
                    //flash("Passwords don't match");
                    flash("Incorrect password");
                }
            } else {
                flash("User doesn't exist");
            }
        } catch (Exception $e) {
            echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            flash("An unexpected error occurred, please try again", "danger");
        }
    }
}
?>
<div class="container-fluid">
    <h1>Login</h1>
    <form method="POST" onsubmit="return validate(this);">
        <div class="mb-3">
            <label class="form-label" for="email">Email/Username: </label>
            <input class="form-control" type="text" id="email" name="email" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" required />
        </div>
        <div class="mb-3">
            <label class="form-label" for="pw">Password: </label>
            <input class="form-control" type="password" id="pw" name="password" required />
        </div>
        <div class="mb-3">
            <input class="btn btn-primary" type="submit" name="submit" value="Login" />
        </div>
    </form>
</div>
<script>
    function validate(form) {
        let email = form.email.value;
        let password = form.password.value;
        let isValid = true;
        if (email) {
            email = email.trim();
        }
        if (password) {
            password = password.trim();
        }
        /*if (email.indexOf("@") === -1) {
            isValid = false;
            alert("Invalid email");
        }*/
        if (password.length < 3) {
            isValid = false;
            alert("Password must be 3 or more characters");
        }
        return isValid;
    }
</script>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>
