<?php

	session_start();

	require_once "database_connection.php";

	if(isset($_SESSION["user_id"])) {
		header("location:home.php");
	}

	if ( isset($_POST['cancel'] ) ) {
        header("Location: index.php");
        return;
    }

    function test_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }

    if ( isset($_POST['user_email']) && isset($_POST['user_password']) ) {
        if ( strlen($_POST['user_email']) < 1 || strlen($_POST['user_password']) < 1 ) {
            unset($_SESSION['name']);
            $_SESSION['error'] = "Email and password are required";
            header("Location: login2.php");
            return;
        } else {
            $email = test_input($_POST["user_email"]);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Email Address is Invalid";
                header("Location: login2.php");
                return;
            } else {
            	$user_password = trim($_POST["user_password"]);
				$user_password = password_hash($user_password, PASSWORD_DEFAULT);
				$stmt = $pdo->prepare('SELECT * FROM register_user WHERE user_email = :user_email');
				$stmt->execute(array( ':user_email' => $_POST['user_email']) );
				$total_row = $stmt->rowCount();
                unset($_SESSION['name']);
                if ($total_row != 0 ) {
                	$result = $stmt->fetchAll();
                	foreach($result as $row)
                	{
                		$_SESSION["register_user_id"] = $row["register_user_id"];
                		$_SESSION["user_name"] = $row["user_name"];
                		$_SESSION['user_email'] = $row["user_email"];
                		$_SESSION["user_password"] = $row["user_password"];
                	}
                	if ( password_verify($_POST["user_password"], $_SESSION["user_password"]) ) {
                	    error_log("Login success ".$_POST['user_email']);
                	    $_SESSION['user_id'] = $_SESSION['register_user_id'];
                	    unset($_SESSION["register_user_id"]);
                	    unset($_SESSION["user_email"]);
                	    unset($_SESSION["user_password"]);
                	    $_SESSION['success'] = "Logged In.";
                	    header("Location: home.php");
                	    return;
                	} else {
                	    error_log("Login fail ".$_POST['user_email']);
                	    $_SESSION['error'] = "Incorrect password";
                	    header("Location: login2.php");
                	    return;
                	}
                } else {
                	$_SESSION['error'] = "Email Address not found";
                	header("Location: login2.php");
                	return;
                }
            }
        }
    }

?>

<!DOCTYPE html>
<html>
	<head>
		<title>#CAFE Login</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script src="http://code.jquery.com/jquery.js"></script>
    	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	</head>
	<body>
		<br />
		<div class="container">
			<h3 align="center">#CAFE</h3>
			<br />
			<?php
			if(isset($_GET["register"])) {
				if($_GET["register"] == 'success') {
					echo '<h1 class="text-success">Email Successfully verified, Registration Process Completed...</h1>';
				}
			}
			?>
			<div class="row">
				<div class="col-md-3">&nbsp;</div>
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Login</h3>
						</div>
						<div class="panel-body">
							<?php
							    if ( isset($_SESSION['error']) ) {
							        echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
							        unset($_SESSION['error']);
							    }
							?>
							<form method="POST" id="login_form">
								<div class="form-group" id="email_area">
									<label>Enter Email Address</label>
									<input type="text" name="user_email" id="user_email" class="form-control" />
									<span id="user_email_error" class="text-danger"></span>
								</div>
								<div class="form-group" id="password_area" style="display:block;">
									<label>Enter password</label>
									<input type="password" name="user_password" id="user_password" class="form-control" />
									<span id="user_password_error" class="text-danger"></span>
								</div>
								<div class="form-group" align="right">
									<input type="submit" name="next" id="next" class="btn btn-primary" value="Login" />&nbsp;&nbsp;&nbsp;
									<a href="logout.php"> Cancel </a>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<br />
		<br />
	</body>
</html>