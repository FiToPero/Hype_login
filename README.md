# Hype Distributor Portal

### Prerequisites
- Docker and Docker Compose
- Web browser

### Installation

1. **Clone/Download the project**
   # If you have the files, navigate to the project directory
   cd Hype_login
2. **Start the Docker environment**
   docker-compose up -d
3. **Initialize the database**
   ./setup-database.sh
4. **Access the application**
   - Open your web browser
   - Navigate to: `http://localhost:8080`


##  Project Requirements Fulfilled

 **User signup form** with Username, Password, Password confirmation, and Email Address  
 **Login form** with Username and Password  
 **Login error page** with comprehensive error handling  
 **Successful login page** displaying a list of all registered users  
 **Complete programming code** ready to run on any server  
 **Maximum security** implementation using industry best practices  
 **Pure PHP** (no frameworks) with **MySQL database**  
 **JavaScript support** for enhanced user experience  

##  Security Features

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




##  Security Configuration

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

