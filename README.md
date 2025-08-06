# Bare Bloom Authentication System

A beautiful, modern authentication system built with PHP, MySQL, and Bootstrap 5. Features a responsive design with smooth animations and comprehensive authentication flow.

## Features

- ✨ Modern, responsive design with smooth animations
- 🔐 Complete authentication flow (Login, Signup, Forgot Password, Email Verification)
- 📱 Mobile-friendly with animated phone mockups
- 🎨 Beautiful gradient backgrounds and custom illustrations
- 🔒 Secure password handling with strength indicators
- ⏱️ Email verification with countdown timer
- 🌟 Return buttons for easy navigation
- 🎯 Form validation with real-time feedback

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
- Start Apache and MySQL services from XAMPP Control Panel

### 2. Project Setup
1. Copy all project files to your XAMPP `htdocs` folder:
   ```
   C:\xampp\htdocs\barebloom\
   ```

2. Open phpMyAdmin:
   - Go to `http://localhost/phpmyadmin`
   - Create a new database called `barebloom_auth`

3. Import the database:
   - In phpMyAdmin, select the `barebloom_auth` database
   - Go to "Import" tab
   - Upload the `setup_database.sql` file
   - Click "Go" to execute

### 3. Configuration
1. Edit `config.php` if needed:
   - Update `DB_HOST`, `DB_USER`, `DB_PASS` if your MySQL settings are different
   - Update `SITE_URL` to match your local setup

### 4. Access the Application
- Open your browser and go to: `http://localhost/barebloom/index.php`
- You should see the login page

## File Structure

```
barebloom/
├── index.html          # Static HTML version
├── index.php           # PHP version with backend functionality
├── config.php          # Database configuration
├── setup_database.sql  # Database setup script
└── README.md          # This file
```

## Pages and Navigation

### 1. Login Page
- Main entry point
- Demo credentials provided
- "Forgot Password" and "Sign up" links

### 2. Sign Up Page
- Complete registration form
- Password confirmation
- Return button (top-right)
- Terms and conditions checkbox

### 3. Forgot Password Page
- Email input for password reset
- Return button to login page
- Triggers verification code flow

### 4. Verify Code Page
- 4-digit verification code input
- 2-minute countdown timer
- Resend code functionality
- Return button to forgot password page

### 5. Set New Password Page
- Password strength indicator
- Real-time password requirements checking
- Password confirmation
- Return button to verification page

## Features in Detail

### Return Buttons
- Added to all secondary pages (Sign up, Forgot Password, Verify Code, Set Password)
- Located in the top-right corner of the form area
- Smooth hover animations
- Proper navigation flow

### Password Strength Indicator
- Real-time password strength checking
- Visual progress bar with color coding
- Requirements checklist with icons
- Prevents weak passwords

### Form Validation
- Client-side validation with visual feedback
- Server-side validation for security
- AJAX form submissions
- Success/error message display

### Responsive Design
- Mobile-first approach
- Image section hides on mobile devices
- Adaptive typography and spacing
- Touch-friendly interface

## Security Features

- Password hashing with PHP's `password_hash()`
- CSRF protection through form validation
- SQL injection prevention with prepared statements
- XSS protection with input sanitization
- Session management
- Secure password reset flow

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Customization

### Colors
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

### Logo
Replace the SVG logo in the `.logo-icon` sections with your own logo.

### Animations
Modify the CSS keyframes and transitions to adjust animation speeds and effects.

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Make sure MySQL is running in XAMPP
   - Check database credentials in `config.php`
   - Ensure the database exists

2. **Page Not Found**
   - Check that files are in the correct XAMPP htdocs folder
   - Verify the URL path

3. **Styles Not Loading**
   - Check internet connection (CDN resources)
   - Clear browser cache

4. **PHP Errors**
   - Enable error reporting in XAMPP
   - Check PHP error logs

### Development Tips

1. **Testing Email Functionality**
   - Currently emails are logged to PHP error log
   - Integrate PHPMailer for real email sending

2. **Database Integration**
   - Replace demo authentication with real database queries
   - Use the provided `Database` class in `config.php`

3. **Session Management**
   - Implement proper session timeout
   - Add "Remember Me" functionality

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For support and questions, please create an issue in the repository or contact the development team.

---

**Note**: This is a demo authentication system. For production use, additional security measures and proper email integration should be implemented.