<?php 
    // สร้าง URL พร้อมกับ status parameter เพื่อให้หน้าปลายทางแสดง toast notification
    function redirectWithStatus($redirectUrl, $statusType, $message) {
        $encodedMessage = urlencode($message);
        $separator = strpos($redirectUrl, '?') === false ? '?' : '&';
        $fullUrl = $redirectUrl . $separator . 'status=' . $statusType . '&message=' . $encodedMessage;
        header('Location: ' . $fullUrl);
        exit;
    }
?>