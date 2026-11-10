<?php
// =========================================================================
// UNIVERSAL CONTACT FORM PROCESSOR TEMPLATE
// =========================================================================

// --- SITE CONFIGURATION ---
$recipient_email = "pamelacochranetherapy@gmail.com";          // Where target form notifications go (Change to client later)
$from_email      = "webmaster@pamcochraneskincare.co.za";      // MUST match an active email mailbox on your Afrihost domain
$site_name       = "Pam Cochrane Skincare";                    // Business name for subject lines

// --- CLOUDFLARE TURNSTILE CONFIGURATION ---
$secret_key      = "0x4AAAAAAF7q6uPausYp6-VqGnn5ko3U8AY";      // Replace with your Cloudflare Turnstile Secret Key

// =========================================================================
// SECURITY & SPAM PREVENTION MEASURES
// =========================================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Honeypot check
    if (!empty($_POST['website_address'])) {
        die("Submission error.");
    }

    // 2. Fast submission check (under 3 seconds)
    $render_time = isset($_POST['form_render_time']) ? intval($_POST['form_render_time']) : 0;
    if (time() - $render_time < 3) {
        die("Submission submitted too fast.");
    }

    // 3. Cloudflare Turnstile Verification
    $turnstile_response = $_POST['cf-turnstile-response'] ?? '';
    
    $verify_url = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
    
    $post_fields = [
        'secret'   => $secret_key,
        'response' => $turnstile_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    // Use cURL for reliable API verification on Afrihost
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $verify_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    curl_close($ch);

    $response_data = json_decode($response, true);

    if (!$response_data || !isset($response_data['success']) || !$response_data['success']) {
        die("Captcha verification failed. Please try again.");
    }

// =========================================================================
// PROCESSING LOGIC
// =========================================================================

    // 4. Sanitize Inputs
    $name    = filter_var(trim($_POST['name'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
    $email   = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $phone   = filter_var(trim($_POST['phone'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
    $message = filter_var(trim($_POST['message'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);

    if (!$email) {
        die("Invalid email address.");
    }

    // 5. Construct Email
    $email_subject = "[$site_name] New Website Inquiry";
    
    $email_content  = "You received a new inquiry from $site_name:\r\n\r\n";
    $email_content .= "Name: $name\r\n";
    $email_content .= "Email: $email\r\n";
    $email_content .= "Phone: $phone\r\n\r\n";  
    $email_content .= "Message:\r\n$message\r\n";

    // Standard headers optimized for cPanel/Linux hosting environments
    $headers  = "From: " . $from_email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if (mail($recipient_email, $email_subject, $email_content, $headers, "-f" . $from_email)) {
        // Redirect back to contact-us.html with success flag
        header("Location: contact-us.html?status=success#contact");
        exit();
    } else {
        // Redirect back to contact-us.html with error flag
        header("Location: contact-us.html?status=error#contact");
        exit();
    }

} else {
    header("Location: contact-us.html");
    exit();
}