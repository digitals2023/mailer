<?php
/**
 * Mail Template - HTML email template with placeholder support
 * 
 * Usage: Store this content and use it with the Mailer class.
 * Placeholders like {name}, {company} will be replaced with actual values.
 */

return [
    // === Simple Text Template ===
    'simple_text' => "Hello {name},\n\nThank you for choosing {company_name}.\n\nHere is your message:\n\n{message}\n\nBest regards,\n{company_name} Team",
    
    // === HTML Template ===
    'html' => "<html>
<head><style>
    body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
    .container { max-width: 600px; margin: 0 auto; padding: 40px; }
    .header { background: #2c3e50; color: white; padding: 30px; text-align: center; }
    .content { background: #f8f9fa; padding: 30px; border-radius: 8px; }
    .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
</style></head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Welcome, {name}!</h1>
            <p>Thank you for choosing {company_name}</p>
        </div>
        <div class='content'>
            <p>{message}</p>
            <p>Sent on {date}</p>
        </div>
        <div class='footer'>
            &copy; {year} {company_name}. All rights reserved.
        </div>
    </div>
</body>
</html>",
    
    // === Plain Text Template ===
    'plain' => "Welcome, {name}!\n\nThank you for choosing {company_name}.\n\n{message}\n\nSent on {date}",
    
    // === Newsletter Template ===
    'newsletter' => "<html>
<head><style>
    body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
    .container { max-width: 600px; margin: 0 auto; padding: 40px; }
    .section { background: #ffffff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .section h2 { color: #2c3e50; margin-bottom: 15px; }
    .button { display: inline-block; background: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; }
    .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
</style></head>
<body>
    <div class='container'>
        <h1>Hello {name},</h1>
        <div class='section'>
            <h2>{information_title}</h2>
            <p>{information_description}</p>
        </div>
        <div class='section'>
            <h2>Upcoming Events</h2>
            <ul>
                <li>{event1_date} - {event1_name}</li>
                <li>{event2_date} - {event2_name}</li>
            </ul>
        </div>
        <div class='section'>
            <p>Want to learn more? <a href='{link_url}' class='button'>Visit Our Website</a></p>
        </div>
        <div class='footer'>
            &copy; {year} {company_name}. All rights reserved.<br>
            {company_address}
        </div>
    </div>
</body>
</html>",
    
    // === Welcome Email Template ===
    'welcome' => "<html>
<head><style>
    body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
    .container { max-width: 600px; margin: 0 auto; padding: 40px; }
    .badge { background: #27ae60; color: white; padding: 10px 20px; border-radius: 20px; display: inline-block; font-weight: bold; }
    .cta { background: #3498db; color: white; padding: 15px 30px; text-align: center; border-radius: 8px; margin: 20px 0; }
    .features { list-style: none; padding: 0; }
    .features li { padding: 10px 0; border-bottom: 1px solid #eee; }
    .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
</style></head>
<body>
    <div class='container'>
        <h2>Welcome, {name}! 👋</h2>
        <p>Thanks for joining <strong>{company_name}</strong>. We're excited to have you onboard!</p>
        <div class='badge'>Verified</div>
        <div class='cta'>
            <a href='{signup_link}'>Get Started</a>
        </div>
        <p>Here's what you can do:</p>
        <ul class='features'>
            <li>Create and manage your profile</li>
            <li>Access all features and tools</li>
            <li>Connect with our support team</li>
        </ul>
        <div class='footer'>
            &copy; {year} {company_name}. All rights reserved.<br>
            {company_address}
        </div>
    </div>
</body>
</html>",
];