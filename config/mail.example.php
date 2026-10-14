<?php
/** Copy this file to mail.php and set your SMTP details.
 *  IMPORTANT: Do not commit mail.php or upload it to a public repository.
 *  The config/ folder is blocked from direct web access by .htaccess on Apache.
 */
return [
    'smtp_host' => 'smtp.your-provider.com',
    'smtp_port' => 587,
    'smtp_encryption' => 'tls', // 'tls' for 587, 'ssl' for 465
    'smtp_username' => 'your-smtp-username',
    'smtp_password' => 'YOUR_SMTP_PASSWORD',
    'from_email' => 'info@drive4u.co.za', // must be authorised by SMTP provider
    'from_name' => 'Drive4U Website',
    'to_email' => 'info@drive4u.co.za',
    'to_name' => 'Drive4U Enquiries',
];
