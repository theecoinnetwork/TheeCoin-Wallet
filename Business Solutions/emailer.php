<?php
/**
 * TheeCoin Native PHP Email Handler
 * Handles email sending with native PHP mail functionality
 * No external APIs or services required
 */

// Prevent direct access
if (!isset($_POST['action']) || $_POST['action'] !== 'send_email') {
    http_response_code(403);
    die('Direct access not allowed');
}

// Set content type to JSON
header('Content-Type: application/json');

// CORS headers for cross-origin requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Email configuration from POST data
$emailConfig = json_decode($_POST['email_config'] ?? '{}', true);
$emailType = $_POST['email_type'] ?? '';
$emailData = json_decode($_POST['email_data'] ?? '{}', true);

// Validate required data
if (!$emailConfig || !$emailType || !$emailData) {
    echo json_encode(['success' => false, 'message' => 'Missing required email data']);
    exit;
}

// Check if emailing is enabled
if (!$emailConfig['enabled']) {
    echo json_encode(['success' => true, 'message' => 'Email notifications disabled']);
    exit;
}

/**
 * Send email using native PHP mail() function or SMTP
 */
function sendEmail($to, $subject, $message, $headers, $smtpConfig = null) {
    // Use SMTP if configured, otherwise use native mail()
    if ($smtpConfig && $smtpConfig['enabled']) {
        return sendSMTPEmail($to, $subject, $message, $headers, $smtpConfig);
    } else {
        return mail($to, $subject, $message, $headers);
    }
}

/**
 * Send email using SMTP
 */
function sendSMTPEmail($to, $subject, $message, $headers, $smtpConfig) {
    $socket = fsockopen($smtpConfig['host'], $smtpConfig['port'], $errno, $errstr, 30);
    
    if (!$socket) {
        return false;
    }
    
    // Read initial response
    $response = fgets($socket, 1024);
    
    // Send HELO command
    fwrite($socket, "HELO " . $smtpConfig['host'] . "\r\n");
    $response = fgets($socket, 1024);
    
    // Start TLS if required
    if ($smtpConfig['tls']) {
        fwrite($socket, "STARTTLS\r\n");
        $response = fgets($socket, 1024);
        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        
        // Send HELO again after TLS
        fwrite($socket, "HELO " . $smtpConfig['host'] . "\r\n");
        $response = fgets($socket, 1024);
    }
    
    // Authenticate if credentials provided
    if ($smtpConfig['username'] && $smtpConfig['password']) {
        fwrite($socket, "AUTH LOGIN\r\n");
        $response = fgets($socket, 1024);
        
        fwrite($socket, base64_encode($smtpConfig['username']) . "\r\n");
        $response = fgets($socket, 1024);
        
        fwrite($socket, base64_encode($smtpConfig['password']) . "\r\n");
        $response = fgets($socket, 1024);
    }
    
    // Send MAIL FROM
    fwrite($socket, "MAIL FROM: <" . $smtpConfig['from_email'] . ">\r\n");
    $response = fgets($socket, 1024);
    
    // Send RCPT TO
    fwrite($socket, "RCPT TO: <" . $to . ">\r\n");
    $response = fgets($socket, 1024);
    
    // Send DATA command
    fwrite($socket, "DATA\r\n");
    $response = fgets($socket, 1024);
    
    // Send email headers and body
    fwrite($socket, "Subject: " . $subject . "\r\n");
    fwrite($socket, $headers . "\r\n");
    fwrite($socket, "\r\n");
    fwrite($socket, $message . "\r\n");
    fwrite($socket, ".\r\n");
    $response = fgets($socket, 1024);
    
    // Send QUIT
    fwrite($socket, "QUIT\r\n");
    $response = fgets($socket, 1024);
    
    fclose($socket);
    
    return strpos($response, '250') !== false;
}

/**
 * Build email headers
 */
function buildEmailHeaders($fromEmail, $fromName, $isHTML = true) {
    $headers = "From: $fromName <$fromEmail>\r\n";
    $headers .= "Reply-To: $fromEmail\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    if ($isHTML) {
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    } else {
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    }
    
    return $headers;
}

/**
 * Format email message
 */
function formatEmailMessage($template, $data, $isHTML = true) {
    $message = $template;
    
    // Replace template variables
    foreach ($data as $key => $value) {
        $message = str_replace('{{' . $key . '}}', $value, $message);
    }
    
    if ($isHTML) {
        $message = nl2br($message);
        $message = "<html><body>" . $message . "</body></html>";
    }
    
    return $message;
}

try {
    $result = ['success' => false, 'message' => 'Unknown error'];
    
    // Handle seller notification
    if ($emailType === 'seller' && $emailConfig['sellerEmail']['enabled']) {
        $sellerConfig = $emailConfig['sellerEmail'];
        $smtpConfig = $emailConfig['smtp'] ?? null;
        
        $subject = str_replace('{{order_date}}', date('Y-m-d H:i:s'), $sellerConfig['subject']);
        
        $customerName = trim(($emailData['firstName'] ?? '') . ' ' . ($emailData['lastName'] ?? ''));
        if (empty($customerName)) {
            $customerName = 'Anonymous';
        }
        
        $customerAddress = implode(', ', array_filter([
            $emailData['address'] ?? '',
            $emailData['city'] ?? '',
            $emailData['state'] ?? '',
            $emailData['zipCode'] ?? '',
            $emailData['country'] ?? ''
        ]));
        
        if (empty($customerAddress)) {
            $customerAddress = 'Not provided';
        }
        
        $templateData = [
            'customer_name' => $customerName,
            'customer_email' => $emailData['email'] ?? 'Not provided',
            'customer_phone' => $emailData['phone'] ?? 'Not provided',
            'customer_address' => $customerAddress,
            'order_amount' => $emailData['order_amount'] ?? '',
            'payment_address' => $emailData['payment_address'] ?? '',
            'order_date' => date('Y-m-d H:i:s'),
            'message' => "New TheeCoin purchase order received!\n\n" .
                        "Customer Details:\n" .
                        "Name: $customerName\n" .
                        "Email: " . ($emailData['email'] ?? 'Not provided') . "\n" .
                        "Phone: " . ($emailData['phone'] ?? 'Not provided') . "\n" .
                        "Address: $customerAddress\n\n" .
                        "Order Details:\n" .
                        "Amount: " . ($emailData['order_amount'] ?? '') . "\n" .
                        "Payment Address: " . ($emailData['payment_address'] ?? '') . "\n" .
                        "Order Date: " . date('Y-m-d H:i:s') . "\n\n" .
                        "Please monitor the payment address for incoming transactions."
        ];
        
        $message = formatEmailMessage($sellerConfig['template'] ?? '{{message}}', $templateData, true);
        
        $fromEmail = $smtpConfig['from_email'] ?? 'noreply@' . $_SERVER['HTTP_HOST'];
        $fromName = $smtpConfig['from_name'] ?? 'TheeCoin Payment System';
        
        $headers = buildEmailHeaders($fromEmail, $fromName, true);
        
        if (sendEmail($sellerConfig['toEmail'], $subject, $message, $headers, $smtpConfig)) {
            $result = ['success' => true, 'message' => 'Seller notification sent successfully'];
        } else {
            $result = ['success' => false, 'message' => 'Failed to send seller notification'];
        }
    }
    
    // Handle customer confirmation
    elseif ($emailType === 'customer' && $emailConfig['customerEmail']['enabled'] && !empty($emailData['email'])) {
        $customerConfig = $emailConfig['customerEmail'];
        $smtpConfig = $emailConfig['smtp'] ?? null;
        
        $subject = str_replace('{{order_amount}}', $emailData['order_amount'] ?? '', $customerConfig['subject']);
        
        $customerName = trim(($emailData['firstName'] ?? '') . ' ' . ($emailData['lastName'] ?? ''));
        if (empty($customerName)) {
            $customerName = 'Valued Customer';
        }
        
        $templateData = [
            'customer_name' => $customerName,
            'from_name' => $customerConfig['fromName'],
            'order_amount' => $emailData['order_amount'] ?? '',
            'payment_address' => $emailData['payment_address'] ?? '',
            'order_date' => date('Y-m-d H:i:s'),
            'message' => "Dear $customerName,\n\n" .
                        "Thank you for your TheeCoin purchase!\n\n" .
                        "Order Details:\n" .
                        "Amount: " . ($emailData['order_amount'] ?? '') . "\n" .
                        "Order Date: " . date('Y-m-d H:i:s') . "\n\n" .
                        "Payment Instructions:\n" .
                        "Please send exactly " . ($emailData['order_amount'] ?? '') . " worth of TheeCoin to the following address:\n" .
                        ($emailData['payment_address'] ?? '') . "\n\n" .
                        "Your order will be processed once payment is confirmed on the TheeCoin network.\n\n" .
                        "If you have any questions, please don't hesitate to contact us.\n\n" .
                        "Best regards,\n" .
                        ($customerConfig['fromName'] ?? 'TheeCoin Payment System')
        ];
        
        $message = formatEmailMessage($customerConfig['template'] ?? '{{message}}', $templateData, true);
        
        $fromEmail = $smtpConfig['from_email'] ?? 'noreply@' . $_SERVER['HTTP_HOST'];
        $fromName = $customerConfig['fromName'] ?? 'TheeCoin Payment System';
        
        $headers = buildEmailHeaders($fromEmail, $fromName, true);
        
        if (sendEmail($emailData['email'], $subject, $message, $headers, $smtpConfig)) {
            $result = ['success' => true, 'message' => 'Customer confirmation sent successfully'];
        } else {
            $result = ['success' => false, 'message' => 'Failed to send customer confirmation'];
        }
    }
    
    else {
        $result = ['success' => true, 'message' => 'Email type disabled or no recipient provided'];
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Email error: ' . $e->getMessage()]);
}
?>
