# Bare Bloom Authentication System

A beautiful, modern authentication system built with PHP sessions (no database required) and Bootstrap 5. Features a responsive design with smooth animations and comprehensive authentication flow.

## Features

- ✨ Modern, responsive design with smooth animations
- 🔐 Complete authentication flow (Login, Signup, Forgot Password, Email Verification)
- 📱 Mobile-friendly with animated phone mockups
- 🎨 Beautiful gradient backgrounds and custom illustrations
- 🔒 Secure password handling with strength indicators
- ⏱️ Email verification with countdown timer
- 🌟 Return buttons for easy navigation
- 🎯 Form validation with real-time feedback
- 💾 **No Database Required** - Uses PHP sessions only

## Demo Credentials

**For testing the login functionality:**
- Email: `admin@barebloom.com`
- Password: `password123`

**For testing password reset:**
- Verification Code: `1234`

## XAMPP Setup Instructions

### 1. Download and Install XAMPP
- Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
- Install XAMPP on your system
- Start **Apache** service from XAMPP Control Panel (MySQL not required)

### 2. Project Setup
1. Copy the `index.php` file to your XAMPP `htdocs` folder:
   ```
   C:\xampp\htdocs\barebloom\index.php
   ```

### 3. Access the Application
- Open your browser and go to: `http://localhost/barebloom/index.php`
- You should see the login page with demo credentials displayed

## File Structure

```
barebloom/
├── index.html          # Static HTML version (optional)
├── index.php           # Main PHP application file
└── README.md          # This file
```

## How It Works

### Session-Based Authentication
- Uses PHP `$_SESSION` to store user login state
- Demo user credentials stored in PHP array
- No database connection required
- Perfect for testing and development

### Demo User Management
The system includes a pre-configured demo user:
```php
$demo_users = [
    'admin@barebloom.com' => [
        'password' => 'password123',
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin@barebloom.com'
    ]
];
```

## Pages and Navigation

### 1. Login Page
- Main entry point with demo credentials displayed
- Session-based authentication
- "Forgot Password" and "Sign up" links

### 2. Sign Up Page
- Complete registration form (simulated)
- Password confirmation validation
- Return button (top-right)
- Terms and conditions checkbox

### 3. Forgot Password Page
- Email input for password reset
- Return button to login page
- Triggers verification code flow

### 4. Verify Code Page
- 4-digit verification code input (`1234` for demo)
- 2-minute countdown timer
- Resend code functionality
- Return button to forgot password page

### 5. Set New Password Page
- Password strength indicator
- Real-time password requirements checking
- Password confirmation validation
- Return button to verification page

### 6. Welcome/Dashboard Page
- Displayed after successful login
- Shows user avatar with first letter of name
- User information display
- Session timestamp
- Logout functionality

## Features in Detail

### Return Buttons
- Added to all secondary pages (Sign up, Forgot Password, Verify Code, Set Password)
- Located in the top-right corner of the form area
- Smooth hover animations with transform effects
- Proper navigation flow between pages

### Session Management
- Secure PHP session handling
- Session variables for user state
- Automatic session cleanup on logout
- Temporary sessions for password reset flow

### Form Validation
- Client-side validation with visual feedback
- Server-side validation for security
- AJAX form submissions with loading states
- Success/error message display with auto-hide

### Password Strength Indicator
- Real-time password strength checking
- Visual progress bar with color coding
- Requirements checklist with icons
- Prevents submission of weak passwords

### Responsive Design
- Mobile-first approach with Bootstrap 5
- Image section hides on mobile devices
- Adaptive typography and spacing
- Touch-friendly interface elements

## Security Features

- PHP session-based authentication
- Input sanitization with `htmlspecialchars()`
- Email validation with `filter_var()`
- XSS protection through proper escaping
- CSRF protection through session validation
- Secure password requirements enforcement

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Customization

### Adding More Users
Edit the `$demo_users` array in `index.php`:
```php
$demo_users = [
    'admin@barebloom.com' => [
        'password' => 'password123',
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin@barebloom.com'
    ],
    'user@example.com' => [
        'password' => 'userpass123',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'user@example.com'
    ]
];
```

### Colors and Styling
Edit CSS custom properties in the `<style>` section:
```css
:root {
    --primary-color: #007bff;
    --secondary-color: #5d1a1a;
    --success-color: #84fab0;
    --gradient-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-bg-signup: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
}
```

### Logo Replacement
Replace the SVG logo in the `.logo-icon` sections with your own logo or image.

### Verification Code
Change the demo verification code:
```php
$_SESSION['verification_code'] = '1234'; // Change to your preferred code
```

## Troubleshooting

### Common Issues

1. **Page Not Found (404)**
   - Ensure Apache is running in XAMPP
   - Check file path: `C:\xampp\htdocs\barebloom\index.php`
   - Verify URL: `http://localhost/barebloom/index.php`

2. **Styles Not Loading**
   - Check internet connection (uses Bootstrap CDN)
   - Clear browser cache
   - Ensure Bootstrap CDN links are accessible

3. **Session Issues**
   - Restart Apache in XAMPP
   - Clear browser cookies
   - Check PHP session configuration

4. **Form Not Submitting**
   - Check browser console for JavaScript errors
   - Ensure AJAX requests are working
   - Verify form names and IDs match

### Development Tips

1. **Enable PHP Error Reporting**
   Add to the top of `index.php` for debugging:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

2. **Session Debugging**
   View session data:
   ```php
   echo '<pre>' . print_r($_SESSION, true) . '</pre>';
   ```

3. **Testing Different Flows**
   - Test all authentication paths
   - Verify return button navigation
   - Check password strength indicators
   - Test responsive design on mobile

## Migration to Database

To convert this to use a real database:

1. Replace the `$demo_users` array with database queries
2. Add password hashing with `password_hash()` and `password_verify()`
3. Implement proper user registration with database inserts
4. Add email functionality for password reset codes
5. Create proper user session management with database tracking

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For support and questions:
- Check the troubleshooting section above
- Ensure XAMPP Apache is running
- Verify file permissions and paths

---

**Note**: This is a demonstration authentication system using PHP sessions. For production use, implement proper database integration, password hashing, and email services.