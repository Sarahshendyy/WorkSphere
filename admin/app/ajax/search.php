<?php

# database connection file
include '../../connection.php';

if (isset($_SESSION['user_id'])) {
    $user_id=$_SESSION['user_id'];
    if (isset($_POST['key'])) {

        $key = mysqli_real_escape_string($connect, $_POST['key']);
        $searchKey = "%$key%";

        $sql = "SELECT * FROM `users`
                  WHERE `user_id` != '$user_id' 
                AND (`users`.`name` LIKE '$searchKey' )";
        
        $run_search = mysqli_query($connect, $sql);

        if (mysqli_num_rows($run_search) > 0) {

            while ($user = mysqli_fetch_assoc($run_search)) {
                # Skip the current logged-in user
        ?>
                <li class="list-group-item">
                    <a href="chat.php?user=<?= $user['user_id'] ?>"
                       class="d-flex justify-content-between align-items-center p-2">
                        <div class="d-flex align-items-center">
                            <img src="img/<?= $user['image'] ?>"
                                 class="w-10 rounded-circle">
                            <h3 class="fs-xs m-2">
                                <?= $user['name'] ?>
                            </h3>            	
                        </div>
                    </a>
                </li>
        <?php
            }
        } 
        ?>
            
        <?php
        
    }
} else {
    header("Location: ../../login.php");
    exit;
}
?>