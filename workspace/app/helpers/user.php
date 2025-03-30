<?php  

function getUser($user_id, $connect) {
    // Always return the first available admin for regular users
    $sql = "SELECT * FROM users WHERE role_id = 4 ORDER BY last_seen DESC LIMIT 1";
    $result = mysqli_query($connect, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        // Return admin data with consistent "Admin Support" name
        return [
            'user_id' => $admin['user_id'],
            'name' => 'Admin Support',
            'image' => $admin['image'],
            'last_seen' => $admin['last_seen'],
            'role_id' => $admin['role_id']
        ];
    }
    return null;
}