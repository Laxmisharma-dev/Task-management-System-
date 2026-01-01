# TaskFlow - PHP MySQL Backend Setup

## Overview
This is a complete PHP and MySQL authentication system for the TaskFlow application. It includes user registration, login, logout, and session management.

## Files Created

### Backend Files
- **config.php** - Database configuration and connection
- **setup_database.php** - Database setup script (run once)
- **login.php** - Login authentication endpoint
- **register.php** - User registration endpoint
- **logout.php** - Logout endpoint
- **check_auth.php** - Check authentication status

### Frontend Files (Updated)
- **logintask.html** - Login page with backend integration
- **register.html** - Registration page with backend integration

## Setup Instructions

### 1. Start XAMPP
Make sure both **Apache** and **MySQL** services are running in XAMPP Control Panel.

### 2. Setup Database
Run the database setup script **ONCE** by visiting:
```
http://localhost/work/setup_database.php
```

This will:
- Create the `taskflow_db` database
- Create the `users` table
- Create the `tasks` table

### 3. Database Configuration
The default configuration in `config.php` is:
- **Host:** localhost
- **User:** root
- **Password:** (empty)
- **Database:** task_m

If your MySQL has different credentials, update `config.php` accordingly.

## Usage

### Registration
1. Visit: `http://localhost/work/register.html`
2. Fill in:
   - Full Name
   - Email Address
   - Password (minimum 6 characters)
3. Click "Create Account"
4. On success, you'll be redirected to the dashboard

### Login
1. Visit: `http://localhost/work/logintask.html`
2. Enter your email and password
3. Click "Log In"
4. On success, you'll be redirected to the dashboard

### Logout
Call the logout endpoint:
```javascript
fetch('logout.php').then(() => {
    window.location.href = 'logintask.html';
});
```

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

### Tasks Table
```sql
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)
```

## Security Features

- **Password Hashing:** Uses PHP's `password_hash()` with bcrypt
- **SQL Injection Protection:** Uses prepared statements
- **Email Validation:** Server-side email format validation
- **Session Management:** Secure session handling
- **Input Sanitization:** All inputs are trimmed and validated

## API Endpoints

### POST /login.php
**Request:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "Login successful",
    "user": {
        "id": 1,
        "email": "user@example.com",
        "name": "John Doe"
    }
}
```

**Response (Error):**
```json
{
    "success": false,
    "message": "Invalid email or password"
}
```

### POST /register.php
**Request:**
```json
{
    "full_name": "John Doe",
    "email": "user@example.com",
    "password": "password123"
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "Registration successful",
    "user": {
        "id": 1,
        "email": "user@example.com",
        "name": "John Doe"
    }
}
```

**Response (Error):**
```json
{
    "success": false,
    "message": "Email already registered"
}
```

### GET /check_auth.php
**Response (Authenticated):**
```json
{
    "authenticated": true,
    "user": {
        "id": 1,
        "email": "user@example.com",
        "name": "John Doe"
    }
}
```

**Response (Not Authenticated):**
```json
{
    "authenticated": false
}
```

### GET /logout.php
**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

## Testing

1. **Test Registration:**
   - Go to `http://localhost/work/register.html`
   - Create a new account
   - Verify redirection to dashboard

2. **Test Login:**
   - Go to `http://localhost/work/logintask.html`
   - Login with your credentials
   - Verify redirection to dashboard

3. **Test Validation:**
   - Try registering with an existing email
   - Try logging in with wrong password
   - Try short passwords (< 6 characters)

## Troubleshooting

### "Connection failed" error
- Make sure MySQL is running in XAMPP
- Check database credentials in `config.php`

### "Database not found" error
- Run `setup_database.php` first

### Form doesn't submit
- Check browser console for JavaScript errors
- Make sure Apache is running
- Verify file paths are correct

### Session not persisting
- Check if PHP sessions are enabled
- Verify `session_start()` is called in `config.php`

## Next Steps

To protect your dashboard and other pages, add this at the top of each protected page:

```php
<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: logintask.html');
    exit;
}

// User is authenticated, get user data
$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];
$user_name = $_SESSION['user_name'];
?>
```

## Support

For issues or questions, check:
1. XAMPP error logs
2. Browser console
3. PHP error logs in XAMPP
