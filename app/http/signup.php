<?php  

# check if username, password, name submitted
if(isset($_POST['name']) &&
   isset($_POST['password']) &&
   ){

   # database connection file
   include 'connection.php';
   
   # get data from POST request and store them in var
   $name = $_POST['name'];
   $password = $_POST['password'];
   

   # making URL data format
   $data = 'name='.$name;

   #simple form Validation
   if (empty($name)) {
   	  # error message
   	  $em = "Name is required";

   	  # redirect to 'signup.php' and passing error message
   	  header("Location: ../../signup.php?error=$em");
   	  exit;
   }
   else if(empty($password)){
   	  # error message
   	  $em = "Password is required";

   	  /*
    	redirect to 'signup.php' and 
    	passing error message and data
      */
   	  header("Location: ../../signup.php?error=$em&$data");
   	  exit;
   }else {
   	  # checking the database if the username is taken
   	  $sql = "SELECT `name` 
   	          FROM users 
   	          WHERE name=?";
   	          
      $stmt = $connect->prepare($sql);
      $stmt->execute([$name]);

      if($stmt->rowCount() > 0){
      	$em = "The username ($name) is taken";
      	header("Location: ../../signup.php?error=$em&$data");
   	    exit;
      }else {
      	# Profile Picture Uploading
      	if (isset($_FILES['pp'])) {
      		# get data and store them in var
      		$img_name  = $_FILES['pp']['name'];
      		$tmp_name  = $_FILES['pp']['tmp_name'];
      		$error  = $_FILES['pp']['error'];

      		# if there is not error occurred while uploading
      		if($error === 0){
               
               # get image extension store it in var
      		   $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);

               /** 
				convert the image extension into lower case 
				and store it in var 
				**/
				$img_ex_lc = strtolower($img_ex);

				/** 
				crating array that stores allowed
				to upload image extension.
				**/
				$allowed_exs = array("jpg", "jpeg", "png");

				/** 
				check if the the image extension 
				is present in $allowed_exs array
				**/
				if (in_array($img_ex_lc, $allowed_exs)) {
					/** 
					 renaming the image with user's username
					 like: username.$img_ex_lc
					**/
					$new_img_name = $username. '.'.$img_ex_lc;

					# crating upload path on root directory
					$img_upload_path = '../../uploads/'.$new_img_name;

					# move uploaded image to ./upload folder
                    move_uploaded_file($tmp_name, $img_upload_path);
				}else {
					$em = "You can't upload files of this type";
			      	header("Location: ../../signup.php?error=$em&$data");
			   	    exit;
				}

      		}
      	}

      	// password hashing
      	$password = password_hash($password, PASSWORD_DEFAULT);

      	# if the user upload Profile Picture
      	if (isset($new_img_name)) {

      		# inserting data into database
            $sql = "INSERT INTO users
                    ( `name`, `password`, `image`)
                    VALUES (?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $password, $new_img_name]);
      	}else {
            # inserting data into database
            $sql = "INSERT INTO users
                    (name, password)
                    VALUES (?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $password]);
      	}

      	# success message
      	$sm = "Account created successfully";

      	# redirect to 'index.php' and passing success message
      	header("Location: ../../index.php?success=$sm");
     	exit;
      }

   }
}else {
	header("Location: ../../signup.php");
   	exit;
}