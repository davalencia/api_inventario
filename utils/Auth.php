<?php
function isAuthenticated() {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        $token = str_replace('Bearer ', '', $authHeader);

        $storedToken = getenv('API_TOKEN');

        if (!empty($storedToken) && hash_equals($storedToken, $token)) {
            return true;
        }
    }
    return false;
}
?>
