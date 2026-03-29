<?php
function log_action($user_id, $user_role, $action, $con) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $query = "INSERT INTO logs (user_id, user_role, action, ip_address, user_agent) 
              VALUES ('$user_id', '$user_role', '$action', '$ip', '$user_agent')";
    mysqli_query($con, $query);
}
?>