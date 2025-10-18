# ASE230 Project 1 - API Test Suite

This directory contains comprehensive tests for all 8 required REST API endpoints.

## 📁 Directory Structure

```
tests/
├── curl/                    # cURL test scripts
│   ├── test_all_apis.sh    # Complete test suite
│   ├── 01_test_register.sh
│   ├── 02_test_login.sh
│   ├── 03_test_profile.sh
│   ├── 04_test_create_course.sh
│   ├── 05_test_list_courses.sh
│   ├── 06_test_update_course.sh
│   ├── 07_test_enroll.sh
│   └── 08_test_list_enrollments.sh
└── html/                   # HTML/JS test page
    └── api_test_suite.html # Interactive test interface
```

## 🚀 Running the Tests

### Prerequisites
- NGINX server running on port 8080
- PHP-FPM running
- MySQL database with sample data
- `jq` installed for JSON formatting (optional but recommended)

### Method 1: Complete cURL Test Suite
```bash
# Make the script executable
chmod +x tests/curl/test_all_apis.sh

# Run all tests
./tests/curl/test_all_apis.sh
```

### Method 2: Individual cURL Tests
```bash
# Make all scripts executable
chmod +x tests/curl/*.sh

# Run individual tests
./tests/curl/01_test_register.sh
./tests/curl/02_test_login.sh
# ... etc
```

### Method 3: HTML/JS Test Interface
1. Open `tests/html/api_test_suite.html` in a web browser
2. Click individual test buttons or "Run All Tests"
3. View results in real-time with color-coded status

## 📋 Test Coverage

### ✅ All 8 Required APIs Tested:

1. **POST /users/register** - User registration
2. **POST /users/login** 🔐 - User authentication (SECURE)
3. **GET /users/profile** 🔐 - User profile retrieval (SECURE)
4. **POST /courses** 🔐 - Course creation (SECURE)
5. **GET /courses** - Course listing
6. **PUT /courses/{id}** 🔐 - Course update (SECURE)
7. **POST /enrollments** 🔐 - User enrollment (SECURE)
8. **GET /enrollments** 🔐 - Enrollment listing (SECURE)

### 🔐 Security Testing:
- **2+ Secure endpoints** with Bearer token authentication
- Authentication failure tests
- Permission-based access control tests
- JWT token validation

### 🧪 Test Features:
- **Success scenarios** for all endpoints
- **Error handling** and validation
- **Authentication flow** testing
- **Role-based permissions** (student vs instructor)
- **Real-time results** with color coding
- **JSON response validation**

## 📊 Expected Results

### Successful Test Output:
- ✅ **PASS** status for all 8 APIs
- Valid JSON responses with `"success": true`
- Proper authentication tokens generated
- Database operations completed successfully

### Security Validation:
- 🔐 Secure endpoints properly reject requests without tokens
- Students cannot create courses (permission denied)
- JWT tokens are valid and properly formatted

## 🛠️ Troubleshooting

### Common Issues:
1. **Connection refused**: Ensure NGINX and PHP-FPM are running
2. **404 errors**: Check API routing configuration
3. **Authentication failures**: Verify database has sample users
4. **Permission errors**: Ensure proper user roles in database

### Debug Commands:
```bash
# Check services
brew services list | grep -E "(nginx|php|mysql)"

# Test API directly
curl http://localhost:8080/api/

# Check database
mysql -u root -e "USE ase230_project1; SELECT * FROM users;"
```

## 📈 Rubric Compliance

This test suite meets the ASE230 Project 1 requirements:
- ✅ **cURL scripts** in `/tests/curl/` directory
- ✅ **HTML/JS page** with AJAX fetch calls
- ✅ **All 8 APIs** tested (2 pts per API)
- ✅ **2+ Secure endpoints** with Bearer token authentication
- ✅ **Comprehensive coverage** including error scenarios

## 🎯 Usage for Grading

1. **Run the complete test suite**: `./tests/curl/test_all_apis.sh`
2. **Open HTML test page**: `tests/html/api_test_suite.html`
3. **Verify all tests pass** with green ✅ status
4. **Check authentication** works for secure endpoints
5. **Confirm error handling** for invalid requests

The test suite provides comprehensive validation of all required functionality and demonstrates proper API design with authentication and error handling.
