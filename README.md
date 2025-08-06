# Bare Bloom Authentication System

A beautiful and responsive authentication system with Bootstrap 5, featuring login, signup, forgot password, verification, and password reset functionality.

## Features

- ✅ **Complete Authentication Flow**
  - Login
  - Sign Up
  - Forgot Password
  - Email Verification
  - Password Reset
  
- ✅ **Modern UI/UX**
  - Beautiful gradient backgrounds
  - Smooth page transitions
  - Return buttons on all pages
  - Password strength indicator
  - Password visibility toggle
  - Phone mockup illustrations
  
- ✅ **Form Validation**
  - Client-side validation
  - Server-side validation (PHP version)
  - Password strength checking
  - Email format validation
  
- ✅ **Responsive Design**
  - Mobile-first approach
  - Bootstrap 5.3 framework
  - Modern card-based layout

## Files Included

1. **`auth.html`** - Static HTML version with JavaScript functionality
2. **`auth.php`** - PHP version with server-side processing and session management
3. **`README.md`** - This documentation file

## Setup Instructions

### For HTML Version (Static)

1. Simply open `auth.html` in your web browser
2. No server setup required
3. All functionality is client-side only

### For PHP Version (XAMPP)

1. **Install XAMPP**
   - Download from: https://www.apachefriends.org/
   - Install and start Apache

2. **Setup Files**
   ```bash
   # Copy auth.php to your XAMPP htdocs folder
   cp auth.php /path/to/xampp/htdocs/
   ```

3. **Access the Application**
   - Open your browser
   - Navigate to: `http://localhost/auth.php`

4. **Testing the Flow**
   - **Login**: Enter any email and password
   - **Sign Up**: Fill all fields with matching passwords
   - **Forgot Password**: Enter any valid email format
   - **Verify Code**: Enter any 4-digit number (e.g., 1234)
   - **Set Password**: Create a password meeting the requirements

## Page Flow

```
Login Page
├── Sign Up Page → Back to Login
├── Forgot Password Page → Back to Login
    └── Verify Code Page → Back to Forgot Password
        └── Set Password Page → Back to Verify Code
            └── Back to Login (after success)
```

## Return Button Locations

- **Sign Up Page**: Top-right corner → Returns to Login
- **Forgot Password Page**: Top-right corner → Returns to Login  
- **Verify Code Page**: Top-right corner → Returns to Forgot Password
- **Set Password Page**: Top-right corner → Returns to Verify Code

## PHP Session Features

The PHP version includes:

- **Session Management**: Tracks user login state
- **Form Processing**: Server-side validation and processing
- **Page Routing**: Clean URL parameters for navigation
- **Message System**: Success/error feedback
- **Security**: Basic input sanitization

## Customization

### Colors
Modify the CSS variables in the `<style>` section:
```css
:root {
    --primary-color: #007bff;
    --secondary-color: #5d1a1a;
    --success-color: #84fab0;
    --gradient-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-bg-signup: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
}
```

### Logo
Replace the SVG logo in the `.logo-icon` sections with your own logo.

### Database Integration (PHP)
To connect to a real database, modify the form processing sections in `auth.php`:

```php
// Example for login
case 'login':
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Add your database connection and validation here
    // $user = authenticate($email, $password);
    
    if (/* your validation */) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_email'] = $email;
    }
    break;
```

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Dependencies

- Bootstrap 5.3 (CDN)
- Bootstrap Icons (CDN)
- PHP 7.4+ (for PHP version)

## License

Free to use and modify for personal and commercial projects.

## Support

For issues or questions, please check the code comments or modify as needed for your specific requirements.