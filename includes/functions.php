<?php
	function redirect_to($new_location){
		header("Location: " . $new_location);
		exit;
	}

	function escape_string($string){
		global $connection;
		$escaped_string = mysqli_real_escape_string($connection, $string);
		return $escaped_string;
	}

	function alert_message($message){
		$output = "<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">";
		$output .= $message;
		//$output .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\"> <span aria-hidden=\"true\">&times;</span></button>";
		$output .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" >&times;</button>";
		$output .= "</div>";
		$msg = "<script>console.log(";
		$msg .= $message;
		$msg .= ")</script>";
		echo $msg;

		return $output;
	}

	function confirm_query($result_set)
	{
		if (!$result_set) {
			die("Database query failed.");
		}
	}

function form_errors($errors=array()) {
		$output = "";
		if (!empty($errors)) {
		$output .= "<div class=\"alert alert-danger alert-dismissible fade show\">";
		$output .= "<ul>";
		foreach ($errors as $key => $error) {
			$output .= "<li>";
				$output .= htmlentities($error);
				$output .= "</li>";
		}
		$output .= "</ul>";
		$output .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" >&times;</button>";
		$output .= "</div>";
		}
		return $output;
	}





	function find_all_uers(){
		global $connection;
		$query = "SELECT * ";
		$query .= "FROM users ";
		$query .= "ORDER BY username ASC";
		$user_set = mysqli_query($connection, $query);
		confirm_query($user_set);
		return ($user_set);
	}

	function find_users_by_id($user_id)
	{
		global $connection;

		$safe_user_id = mysqli_real_escape_string($connection, $user_id);

		$query = "SELECT * ";
		$query .= "FROM users{$safe_user_id} ";
		$query .= "LIMIT 1";
		$user_set = mysqli_query($connection, $query);
		confirm_query($user_set);
		if ($admin = mysqli_fetch_assoc($user_set)) {
			return $user;
		} else {
			return null;
		}

	}

	function find_user_by_username($username)
	{
		global $connection;

		$safe_username = mysqli_real_escape_string($connection, $username);

		$query = "SELECT * ";
		$query .= "FROM users ";
		$query .= "WHERE username = '{$safe_username}' ";
		$query .= "LIMIT 1";
		$user_set = mysqli_query($connection, $query);
		confirm_query($user_set);
		if ($user = mysqli_fetch_assoc($user_set)) {
			return $user;
		} else {
			return null;
		}

	}

	function password_encrypt($password){
		$hash_format = "$2y$10$"; //tells php to use Blowfish with a "cost" of 10
		$salt_length = 22; //Blowfish salts should be 22-characters long or more
		$salt = generate_salt($salt_length);
		$format_and_salt = $hash_format . $salt;
		$hash = crypt($password, $format_and_salt);
		return $hash;
	}

	function generate_salt($length){
		// not 100% unique, not 100% random, but good enough for salt
		// MD5 returns 32 characters
		$unique_random_string = md5(uniqid(mt_rand(), true));

		//valid characters for a salt are [a-zA-Z0-9./]
		$base64_string = base64_encode($unique_random_string);

		//But not '+' which is valid in base64 encoding
		$modified_base64_string = str_replace('+', '.', $base64_string);

		//Truncate string to the correct length
		$salt = substr($modified_base64_string, 0, $length);

		return $salt;
	}

	function password_check($password, $existing_hash){
		//existing hash contains format and salt at start
		$hash = crypt($password, $existing_hash);
		if ($hash === $existing_hash){
			return true;
		}else{
			return false;
		}
	}

	function attempt_login($username, $password){
		$admin = find_admin_by_username($username);
		if ($admin) {
			// found admin, now check password
			if(password_check($password, $admin["hashed_password"])){
				//password matches
				return $admin;
			}else{
				//password does not match
				return false;
			}
		}else{
			//admin not found
			return false;
		}
	}

	function logged_in(){
		return isset($_SESSION["admin_id"]);
	}

	function confirm_logged_in(){
		if(!logged_in()){
			redirect_to("login.php");
		}
	}

?>
