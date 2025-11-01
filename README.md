# Hype Distributor Portal

A secure PHP-based distributor login system built for the Hype company test assignment.

## 🎯 Project Requirements Fulfilled

✅ **User signup form** with Username, Password, Password confirmation, and Email Address  
✅ **Login form** with Username and Password  
✅ **Login error page** with comprehensive error handling  
✅ **Successful login page** displaying a list of all registered users  
✅ **Complete programming code** ready to run on any server  
✅ **Maximum security** implementation using industry best practices  
✅ **Pure PHP** (no frameworks) with **MySQL database**  
✅ **JavaScript support** for enhanced user experience  

## 🔒 Security Features

### Authentication & Authorization
- **Argon2ID Password Hashing** - Most secure hashing algorithm
- **CSRF Protection** - Prevents cross-site request forgery attacks
- **Session Security** - Secure cookies, session regeneration, database-stored sessions
- **Account Lockout** - 5 failed attempts = 15-minute lockout
- **Rate Limiting** - IP-based attempt limiting (10 attempts per 15 minutes)
- **Input Validation** - Comprehensive server-side and client-side validation
- **SQL Injection Prevention** - Prepared statements with parameterized queries

### Data Protection
- **Password Strength Requirements** - Uppercase, lowercase, numbers, special characters
- **Email Validation** - Proper email format validation
- **Data Sanitization** - All inputs are sanitized and validated
- **XSS Prevention** - Proper output escaping with htmlspecialchars()

### Monitoring & Logging
- **Login Attempt Logging** - All attempts logged with IP, timestamp, user agent
- **Session Tracking** - Complete session lifecycle management
- **Security Audit Trail** - Failed attempts, lockouts, and suspicious activity logged

## 🚀 Quick Start

### Prerequisites
- Docker and Docker Compose
- Web browser

### Installation

1. **Clone/Download the project**
   ```bash
   # If you have the files, navigate to the project directory
   cd Hype_test
   ```

2. **Start the Docker environment**
   ```bash
   docker-compose up -d
   ```

3. **Initialize the database**
   ```bash
   # Access the PHP container
   docker exec -it php_server bash
   
   # Run the database setup
   mysql -h db -u hype_user -phype_password hype_distributor < database/schema.sql
   ```

4. **Access the application**
   - Open your web browser
   - Navigate to: `http://localhost:8080`

### Default Access
- The application will redirect to the login page initially
- Create a new account using the "Sign up here" link
- After registration, you can log in and access the dashboard

## 📁 Project Structure

```
Hype_test/
├── docker-compose.yml          # Docker services configuration
├── Dockerfile                  # PHP container configuration
├── index.php                   # Application entry point
├── src/
│   └── Auth.php               # Authentication and security class
├── config/
│   └── database.php           # Database configuration and connection
├── database/
│   └── schema.sql            # MySQL database schema
├── public/                    # Web-accessible files
│   ├── login.php             # Login form page
│   ├── signup.php            # User registration page
│   ├── dashboard.php         # Success page with user list
│   └── error.php             # Login error page
├── assets/
│   ├── css/
│   │   └── style.css         # Application styles
│   └── js/
│       ├── login-validation.js    # Login form validation
│       └── signup-validation.js   # Signup form validation
└── README.md                  # This file
```

## 🔧 Configuration

### Database Settings
The application is pre-configured with the following database settings:

- **Host:** db (Docker container)
- **Database:** hype_distributor
- **Username:** hype_user
- **Password:** hype_password
- **Port:** 3306

### Environment Variables
You can modify these in `docker-compose.yml`:

```yaml
environment:
  MYSQL_DATABASE: hype_distributor
  MYSQL_USER: hype_user
  MYSQL_PASSWORD: hype_password
  MYSQL_ROOT_PASSWORD: root_password
```

## 🛡️ Security Configuration

### Password Requirements
- Minimum 8 characters
- At least one uppercase letter (A-Z)
- At least one lowercase letter (a-z)
- At least one number (0-9)
- At least one special character (@$!%*?&)

### Account Lockout Policy
- **Failed Attempts Threshold:** 5 attempts
- **Lockout Duration:** 15 minutes
- **Rate Limiting:** 10 attempts per IP per 15 minutes

### Session Configuration
- **Session Lifetime:** 1 hour
- **Session Regeneration:** Every 5 minutes
- **Secure Cookies:** Enabled
- **HttpOnly Cookies:** Enabled
- **SameSite Policy:** Strict

## 📊 Features Overview

### 1. User Registration (`signup.php`)
- Comprehensive form validation (client-side + server-side)
- Real-time password strength indicator
- Duplicate username/email detection
- CSRF protection
- Secure password hashing

### 2. User Login (`login.php`)
- Secure authentication
- Rate limiting and account lockout
- Session management
- Error handling with security information

### 3. Error Handling (`error.php`)
- Detailed security information
- Recovery options
- Contact information
- Security measures explanation

### 4. Dashboard (`dashboard.php`)
- Welcome message and user information
- Complete list of registered distributors
- Session security indicators
- Secure logout functionality

## 🔍 Testing the Application

### Test User Registration
1. Go to `http://localhost:8080`
2. Click "Sign up here"
3. Fill in the registration form:
   - Username: `testuser`
   - Email: `test@example.com`
   - Password: `SecurePass123!`
   - Confirm Password: `SecurePass123!`
4. Click "Create Account"

### Test Login System
1. Use the credentials from registration
2. Intentionally enter wrong password 3 times to see rate limiting
3. Use correct credentials to access dashboard

### Test Security Features
1. Try SQL injection in login fields
2. Test CSRF by manipulating forms
3. Check password strength requirements
4. Verify session management

## 🐳 Docker Configuration

### Services
- **PHP 8.2** with Apache
- **MySQL 8.4 Alpine** for database
- **Redis** for session storage (optional)
- **Mailpit** for email testing (optional)

### Ports
- **8080:** Web application
- **3306:** MySQL database
- **6379:** Redis (optional)

## 📝 Database Schema

### Users Table
- `id` - Primary key
- `username` - Unique username (3-50 chars)
- `email` - Unique email address
- `password_hash` - Argon2ID hashed password
- `created_at` - Registration timestamp
- `last_login` - Last successful login
- `failed_login_attempts` - Failed attempt counter
- `locked_until` - Account lock expiration
- `is_active` - Account status

### Login Attempts Table
- Tracks all login attempts
- IP address and user agent logging
- Success/failure status
- Timestamp tracking

### Sessions Table
- Secure session management
- IP and user agent validation
- Expiration handling
- Activity tracking

## 🚨 Troubleshooting

### Common Issues

1. **Database Connection Error**
   ```bash
   # Check if containers are running
   docker-compose ps
   
   # Restart containers
   docker-compose restart
   ```

2. **Permission Issues**
   ```bash
   # Fix file permissions
   sudo chown -R 1000:1000 .
   ```

3. **Port Already in Use**
   ```bash
   # Change port in docker-compose.yml
   ports:
     - "8081:8080"  # Use different port
   ```

### Database Reset
```bash
# Stop containers
docker-compose down

# Remove volumes
docker volume rm hype_test_db_data

# Restart
docker-compose up -d
```

## 📞 Support

For technical support or questions about this implementation:

- **Email:** developer@hype-test.local
- **Documentation:** See inline code comments
- **Issues:** Check Docker logs with `docker-compose logs`

## 📜 License

This project is created specifically for the Hype company technical assessment. All code is provided as-is for evaluation purposes.

---

**Built with ❤️ for Hype Company Technical Assessment**  
**Security First • Performance Optimized • Production Ready**