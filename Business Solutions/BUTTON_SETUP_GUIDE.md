# TheeCoin Buy Button - Complete Setup Guide

## Overview

The TheeCoin Buy Button is a complete, self-contained payment solution that allows you to accept TheeCoin payments on any website. It includes:

- ✅ **Customizable payment form** with customer information collection
- ✅ **QR code generation** for easy mobile payments
- ✅ **Native PHP email notifications** (no external APIs required)
- ✅ **Responsive design** that works on all devices
- ✅ **Highly configurable** with extensive customization options
- ✅ **Copy-to-clipboard** functionality for wallet addresses
- ✅ **Professional styling** with modern UI/UX

## Quick Start

1. **Upload files** to your web server:
   - `buy_button.html` - The main button file
   - `emailer.php` - Email handler (for notifications)

2. **Configure your settings** in `buy_button.html`:
   - Set your wallet address
   - Set your price/amount
   - Configure email notifications (optional)

3. **Embed the button** on your website:
   ```html
   <iframe src="path/to/buy_button.html" width="300" height="60" frameborder="0"></iframe>
   ```

## Configuration Guide

### 1. Button Configuration

```javascript
const BUTTON_CONFIG = {
    // Button text (set to empty string "" to hide text)
    buttonText: "Buy with TheeCoin",
    
    // Button image URL (set to empty string "" to not use image)
    buttonImageUrl: "",
    
    // Button size - can be "small", "medium", "large", or custom
    buttonSize: "medium",
    
    // Custom dimensions (only used if buttonSize is not small/medium/large)
    customWidth: "200px",
    customHeight: "50px"
};
```

**Button Size Options:**
- `small`: 8px padding, 14px font
- `medium`: 12px padding, 16px font  
- `large`: 16px padding, 18px font
- `custom`: Uses customWidth/customHeight values

### 2. Payment Configuration

```javascript
const PAYMENT_CONFIG = {
    // Your TheeCoin wallet address for receiving payments
    walletAddress: "1636332663263353930336133373035663",
    
    // Payment amount/price
    amount: "$29.99",
    
    // Currency or description
    currency: "USD"
};
```

### 3. Form Fields Configuration

Control which fields appear in the payment form:

```javascript
const FORM_FIELDS = {
    firstName: true,        // Show first name field
    lastName: true,         // Show last name field
    email: true,            // Show email field
    phone: true,            // Show phone number field
    address: true,          // Show address fields
    city: true,             // Show city field
    state: true,            // Show state/province field
    zipCode: true,          // Show ZIP/postal code field
    country: true           // Show country field
};
```

### 4. Field Requirements Configuration

Control which fields are required (show asterisk *):

```javascript
const FIELD_REQUIREMENTS = {
    firstName: true,        // First name required (shows *)
    lastName: true,         // Last name required (shows *)
    email: true,            // Email required (shows *)
    phone: false,           // Phone optional (no *)
    address: false,         // Address optional (no *)
    city: false,            // City optional (no *)
    state: false,           // State optional (no *)
    zipCode: false,         // ZIP code optional (no *)
    country: false          // Country optional (no *)
};
```

### 5. Modal Configuration

```javascript
const MODAL_CONFIG = {
    // Modal title
    title: "Complete Your Purchase",
    
    // Instructions text
    instructions: "Send the exact amount to the TheeCoin address below to complete your purchase. Your order will be processed once payment is confirmed."
};
```

## Email Notification Setup

The buy button includes native PHP email functionality that works without external APIs or services.

### Basic Email Setup

```javascript
const EMAIL_CONFIG = {
    // Enable email notifications
    enabled: true,
    
    // SMTP Configuration (optional - more reliable)
    smtp: {
        enabled: false,                     // Set to true for SMTP
        host: "smtp.gmail.com",            // SMTP server
        port: 587,                         // SMTP port
        tls: true,                         // Use TLS encryption
        username: "your-email@gmail.com",  // Your email address
        password: "your-app-password",     // Your email password
        from_email: "your-email@gmail.com", // From email address
        from_name: "Your Business Name"    // From name
    },
    
    // Seller notification settings
    sellerEmail: {
        enabled: true,                          // Send email to seller
        toEmail: "seller@example.com",          // Your email address
        subject: "New TheeCoin Purchase Order", // Email subject
        template: "{{message}}"                 // Email template
    },
    
    // Customer notification settings  
    customerEmail: {
        enabled: true,                          // Send confirmation to customer
        fromName: "Your Business Name",         // Your business name
        subject: "Your TheeCoin Purchase Confirmation", // Email subject
        template: "{{message}}"                 // Email template
    }
};
```

### Email Provider Configuration

#### Gmail Setup
```javascript
smtp: {
    enabled: true,
    host: "smtp.gmail.com",
    port: 587,
    tls: true,
    username: "your-email@gmail.com",
    password: "your-app-password",  // Generate at https://myaccount.google.com/apppasswords
    from_email: "your-email@gmail.com",
    from_name: "Your Business Name"
}
```

#### Outlook/Hotmail Setup
```javascript
smtp: {
    enabled: true,
    host: "smtp-mail.outlook.com",
    port: 587,
    tls: true,
    username: "your-email@outlook.com",
    password: "your-password",
    from_email: "your-email@outlook.com",
    from_name: "Your Business Name"
}
```

#### Yahoo Mail Setup
```javascript
smtp: {
    enabled: true,
    host: "smtp.mail.yahoo.com",
    port: 587,
    tls: true,
    username: "your-email@yahoo.com",
    password: "your-app-password",
    from_email: "your-email@yahoo.com",
    from_name: "Your Business Name"
}
```

### Email Templates

You can customize email templates using these variables:

**Available Template Variables:**
- `{{customer_name}}` - Customer's full name
- `{{customer_email}}` - Customer's email address
- `{{customer_phone}}` - Customer's phone number
- `{{customer_address}}` - Customer's complete address
- `{{order_amount}}` - Order amount/price
- `{{payment_address}}` - TheeCoin wallet address
- `{{order_date}}` - Date and time of order
- `{{from_name}}` - Your business name
- `{{message}}` - Complete formatted message

**Custom Email Template Example:**
```javascript
sellerEmail: {
    enabled: true,
    toEmail: "orders@mybusiness.com",
    subject: "New Order: {{order_amount}} from {{customer_name}}",
    template: `
        <h2>New TheeCoin Order Received!</h2>
        <p><strong>Customer:</strong> {{customer_name}} ({{customer_email}})</p>
        <p><strong>Amount:</strong> {{order_amount}}</p>
        <p><strong>Payment Address:</strong> {{payment_address}}</p>
        <p><strong>Order Date:</strong> {{order_date}}</p>
        <hr>
        <p>Customer Details:</p>
        <p>Phone: {{customer_phone}}</p>
        <p>Address: {{customer_address}}</p>
    `
}
```

## Advanced Customization

### Custom Button Styling

You can modify the CSS in the `<style>` section to customize the button appearance:

```css
.theecoin-buy-button {
    background: linear-gradient(135deg, #your-color1, #your-color2);
    border-radius: 12px;
    font-family: 'Your Font', sans-serif;
    /* Add your custom styles */
}
```

### Custom Modal Styling

```css
.theecoin-modal-content {
    background-color: #your-background-color;
    border-radius: 20px;
    /* Add your custom styles */
}

.theecoin-modal-header {
    background: linear-gradient(135deg, #your-color1, #your-color2);
    /* Add your custom styles */
}
```

### Form Field Customization

```css
.theecoin-form-group input {
    border: 2px solid #your-border-color;
    border-radius: 10px;
    /* Add your custom styles */
}

.theecoin-form-group input:focus {
    border-color: #your-focus-color;
    /* Add your custom styles */
}
```

## Integration Examples

### 1. Direct HTML Integration

```html
<!DOCTYPE html>
<html>
<head>
    <title>My Store</title>
</head>
<body>
    <h1>Buy My Product</h1>
    <p>Price: $29.99</p>
    
    <!-- Include the buy button directly -->
    <iframe src="buy_button.html" width="250" height="50" frameborder="0"></iframe>
</body>
</html>
```

### 2. WordPress Integration

```php
// In your WordPress theme or plugin
function add_theecoin_buy_button() {
    echo '<iframe src="' . get_template_directory_uri() . '/buy_button.html" width="250" height="50" frameborder="0"></iframe>';
}

// Use in your posts/pages
add_shortcode('theecoin_button', 'add_theecoin_buy_button');
```

### 3. E-commerce Integration

```html
<!-- Product page integration -->
<div class="product-info">
    <h2>My Product</h2>
    <p class="price">$29.99</p>
    <div class="payment-options">
        <button class="regular-checkout">Regular Checkout</button>
        <iframe src="buy_button.html" width="200" height="50" frameborder="0"></iframe>
    </div>
</div>
```

## Testing Guide

### 1. Local Testing

1. **Set up local server** (required for PHP email functionality):
   ```bash
   # Using PHP built-in server
   php -S localhost:8000
   
   # Or using XAMPP/WAMP/MAMP
   ```

2. **Configure test settings**:
   ```javascript
   const EMAIL_CONFIG = {
       enabled: true,
       smtp: { enabled: false }, // Use basic mail() for testing
       sellerEmail: {
           enabled: true,
           toEmail: "your-test-email@gmail.com"
       }
   };
   ```

3. **Test the flow**:
   - Click the buy button
   - Fill out the form
   - Submit and check for email notifications

### 2. Production Testing

1. **Configure SMTP** for reliable email delivery
2. **Test with small amounts** first
3. **Verify email delivery** to both seller and customer
4. **Check spam folders** if emails don't arrive
5. **Monitor TheeCoin wallet** for incoming payments

## Troubleshooting

### Common Issues

#### 1. Emails Not Sending

**Problem:** No emails are being sent after form submission.

**Solutions:**
- Check PHP error logs for email errors
- Verify SMTP configuration if using SMTP mode
- Ensure `emailer.php` is in the same directory as `buy_button.html`
- Check if your server supports PHP mail() function

#### 2. SMTP Authentication Failed

**Problem:** SMTP emails failing with authentication errors.

**Solutions:**
- Verify username and password are correct
- For Gmail: Use App Passwords instead of regular password
- Check if 2-factor authentication is enabled
- Verify SMTP server and port settings

#### 3. Emails Going to Spam

**Problem:** Emails are being marked as spam.

**Solutions:**
- Set up SPF/DKIM records for your domain
- Use a reputable SMTP service
- Avoid spam-trigger words in email content
- Include proper sender information

#### 4. Modal Not Opening

**Problem:** Buy button doesn't open the payment modal.

**Solutions:**
- Check browser console for JavaScript errors
- Ensure all JavaScript is properly loaded
- Verify there are no conflicting CSS/JS on the page
- Test in different browsers

#### 5. QR Code Not Loading

**Problem:** QR code image doesn't display.

**Solutions:**
- Check internet connection (QR codes use external service)
- Verify wallet address is properly configured
- Check if QR code service is accessible
- Try refreshing the page

### Debug Mode

Add this to your configuration to enable debugging:

```javascript
// Add this for debugging
const DEBUG_MODE = true;

// Modified email function with debug logging
async function sendEmail(emailType, customerData) {
    if (DEBUG_MODE) {
        console.log('Email Type:', emailType);
        console.log('Customer Data:', customerData);
        console.log('Email Config:', EMAIL_CONFIG);
    }
    
    // ... rest of function
}
```

## Server Requirements

### Minimum Requirements
- **PHP 7.0+** (for email functionality)
- **Web server** (Apache, Nginx, or PHP built-in server)
- **Internet connection** (for QR code generation)

### Recommended Requirements
- **PHP 8.0+** for better performance
- **HTTPS/SSL** for secure transactions
- **Email server** or SMTP service for reliable email delivery

### File Permissions
```bash
# Make sure files are readable by web server
chmod 644 buy_button.html
chmod 644 emailer.php

# Make sure directory is accessible
chmod 755 /path/to/your/button/directory
```

## Security Considerations

### 1. Input Validation
- All form inputs are validated client-side and server-side
- Email addresses are validated before sending
- XSS protection through proper escaping

### 2. Email Security
- SMTP passwords should be stored securely
- Use app passwords instead of regular passwords
- Consider using environment variables for sensitive data

### 3. Access Control
- `emailer.php` prevents direct access
- CORS headers configured for cross-origin requests
- Rate limiting through form submission controls

## Performance Optimization

### 1. Caching
- QR codes are generated dynamically but can be cached
- CSS/JS are embedded for faster loading
- Modal content is generated once and reused

### 2. Loading Speed
- Minimal external dependencies
- Optimized CSS and JavaScript
- Responsive images and layouts

### 3. Mobile Optimization
- Touch-friendly interface
- Responsive design for all screen sizes
- Fast loading on mobile networks

## Support and Resources

### Documentation
- **PHP Mail Documentation:** https://www.php.net/manual/en/function.mail.php
- **SMTP Configuration:** Check your email provider's documentation
- **TheeCoin Information:** Visit official TheeCoin resources

### Common Email Provider Settings

| Provider | SMTP Host | Port | TLS | Notes |
|----------|-----------|------|-----|-------|
| Gmail | smtp.gmail.com | 587 | Yes | Requires App Password |
| Outlook | smtp-mail.outlook.com | 587 | Yes | Regular password works |
| Yahoo | smtp.mail.yahoo.com | 587 | Yes | May require App Password |
| Apple iCloud | smtp.mail.me.com | 587 | Yes | Requires App Password |

### Getting Help
- Check PHP error logs for email issues
- Test SMTP settings with a simple PHP script
- Verify DNS settings for your domain
- Consider using a dedicated email service for high volume

---

**🎉 Your TheeCoin Buy Button is now ready to accept payments with professional email notifications!**

## Quick Configuration Checklist

- [ ] Set your wallet address in `PAYMENT_CONFIG.walletAddress`
- [ ] Set your price in `PAYMENT_CONFIG.amount`
- [ ] Configure email settings in `EMAIL_CONFIG`
- [ ] Test email functionality with a test purchase
- [ ] Customize button appearance if needed
- [ ] Upload files to your web server
- [ ] Test the complete purchase flow
- [ ] Monitor your TheeCoin wallet for payments
