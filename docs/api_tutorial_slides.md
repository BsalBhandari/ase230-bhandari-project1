---
marp: true
theme: default
class: lead
paginate: true
backgroundColor: #fff
backgroundImage: url('https://marp.app/assets/hero-background.svg')
---

# ASE230 Project 1 - LMS API Tutorial

## Learning Management System REST API Documentation

**Author:** Bishal Bhandari  
**Course:** ASE230  
**Date:** October 2025

---

# Table of Contents

## 📋 API Endpoints Overview

1. **User Registration** - `POST /api/users/register`
2. **User Login** - `POST /api/users/login` 🔐
3. **User Profile** - `GET /api/users/profile` 🔐
4. **Course Creation** - `POST /api/courses` 🔐
5. **Course Listing** - `GET /api/courses`
6. **Course Update** - `PUT /courses/{id}` 🔐
7. **User Enrollment** - `POST /api/enrollments` 🔐
8. **Enrollment Listing** - `GET /api/enrollments` 🔐

**🔐 = Requires Authentication (Bearer Token)**

---

# Authentication Overview

## 🔐 JWT Bearer Token Authentication

- **Token Type:** JWT (JSON Web Token)
- **Header Format:** `Authorization: Bearer <token>`
- **Expiration:** 24 hours
- **Roles:** `student`, `instructor`, `admin`

### Example Token Usage:
```bash
curl -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

---

# API Endpoint 1: User Registration

## `POST /api/users/register`

**Purpose:** Create a new user account  
**Authentication:** Not required  
**Content-Type:** `application/json`

### Request Body:
```json
{
  "username": "johndoe",
  "email": "john@example.com",
  "password": "password123",
  "first_name": "John",
  "last_name": "Doe",
  "role": "student"
}
```

---

# User Registration - Response

## Success Response (201 Created):
```json
{
  "success": true,
  "message": "User registered successfully",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": 5,
    "username": "johndoe",
    "email": "john@example.com",
    "first_name": "John",
    "last_name": "Doe",
    "role": "student"
  }
}
```

![Registration Success Screenshot](presentation/screenshots/Screenshot 2025-10-18 at 12.38.03 PM.png)

### Error Response (409 Conflict):
```json
{
  "error": "Username or email already exists"
}
```

---

# API Endpoint 2: User Login

## `POST /api/users/login` 🔐

**Purpose:** Authenticate user and receive JWT token  
**Authentication:** Not required  
**Content-Type:** `application/json`

### Request Body:
```json
{
  "username": "student1",
  "password": "password"
}
```

**Note:** Username can be either username or email address

---

# User Login - Response

## Success Response (200 OK):
```json
{
  "success": true,
  "message": "Login successful",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": 3,
    "username": "student1",
    "email": "student1@ase230.edu",
    "first_name": "Jane",
    "last_name": "Doe",
    "role": "student"
  }
}
```

![Login Success Screenshot](presentation/screenshots/Screenshot 2025-10-18 at 12.39.29 PM.png)

### Error Response (401 Unauthorized):
```json
{
  "error": "Invalid credentials"
}
```

---

# API Endpoint 3: User Profile

## `GET /api/users/profile` 🔐

**Purpose:** Retrieve authenticated user's profile and enrollments  
**Authentication:** Required (Bearer Token)  
**Method:** GET

### Request Headers:
```
Authorization: Bearer <jwt_token>
```

**Returns:** User profile + enrollments (students) or courses taught (instructors)

---

# User Profile - Response

## Success Response (200 OK):
```json
{
  "success": true,
  "user": {
    "id": 3,
    "username": "student1",
    "email": "student1@ase230.edu",
    "first_name": "Jane",
    "last_name": "Doe",
    "role": "student",
    "created_at": "2025-10-15 10:30:00"
  },
  "enrollments": [
    {
      "id": 1,
      "title": "Web Development Fundamentals",
      "course_code": "ASE230",
      "credits": 3,
      "semester": "Fall",
      "year": "2025",
      "enrolled_at": "2025-10-15 10:30:00",
      "status": "active"
    }
  ],
  "courses_taught": []
}
```

![User Profile Screenshot](presentation/screenshots/Screenshot 2025-10-18 at 12.51.21 PM.png)

---

# API Endpoint 4: Course Creation

## `POST /api/courses` 🔐

**Purpose:** Create a new course (instructors/admins only)  
**Authentication:** Required (Bearer Token)  
**Content-Type:** `application/json`

### Request Body:
```json
{
  "title": "Advanced Web Development",
  "course_code": "ASE400",
  "description": "Advanced concepts in web development",
  "credits": 4,
  "semester": "Spring",
  "year": 2026
}
```

**Required Fields:** `title`, `course_code`

---

# Course Creation - Response

## Success Response (201 Created):
```json
{
  "success": true,
  "message": "Course created successfully",
  "course": {
    "id": 4,
    "title": "Advanced Web Development",
    "description": "Advanced concepts in web development",
    "instructor_id": 2,
    "course_code": "ASE400",
    "credits": 4,
    "semester": "Spring",
    "year": "2026",
    "created_at": "2025-10-15 11:00:00",
    "updated_at": "2025-10-15 11:00:00",
    "first_name": "John",
    "last_name": "Smith",
    "username": "instructor1"
  }
}
```

![Course Creation Success Screenshot](presentation/screenshots/Screenshot 2025-10-18 at 12.50.52 PM.png)

### Error Response (403 Forbidden):
```json
{
  "error": "Insufficient permissions"
}
```

---

# API Endpoint 5: Course Listing

## `GET /api/courses`

**Purpose:** Retrieve all available courses  
**Authentication:** Not required  
**Method:** GET

### Query Parameters (Optional):
- `semester` - Filter by semester
- `year` - Filter by year
- `instructor` - Filter by instructor ID

### Example:
```
GET /api/courses?semester=Fall&year=2025
```

---

# Course Listing - Response

## Success Response (200 OK):
```json
{
  "success": true,
  "courses": [
    {
      "id": 1,
      "title": "Web Development Fundamentals",
      "description": "Introduction to web development",
      "instructor_id": 2,
      "course_code": "ASE230",
      "credits": 3,
      "semester": "Fall",
      "year": "2025",
      "created_at": "2025-10-15 10:00:00",
      "updated_at": "2025-10-15 10:00:00",
      "first_name": "John",
      "last_name": "Smith",
      "instructor_username": "instructor1",
      "enrollment_count": 2
    }
  ],
  "count": 1
}
```

![Course Listing Screenshot](presentation/screenshots/Screenshot 2025-10-18 at 12.50.23 PM.png)

---

# API Endpoint 6: Course Update

## `PUT /api/courses/{id}` 🔐

**Purpose:** Update an existing course  
**Authentication:** Required (Bearer Token)  
**Content-Type:** `application/json`

### Request Body:
```json
{
  "title": "Updated Web Development Course",
  "description": "Updated course description",
  "credits": 4
}
```

**Permissions:** Course instructor or admin only

---

# Course Update - Response

## Success Response (200 OK):
```json
{
  "success": true,
  "message": "Course updated successfully",
  "course": {
    "id": 1,
    "title": "Updated Web Development Course",
    "description": "Updated course description",
    "instructor_id": 2,
    "course_code": "ASE230",
    "credits": 4,
    "semester": "Fall",
    "year": "2025",
    "created_at": "2025-10-15 10:00:00",
    "updated_at": "2025-10-15 12:00:00",
    "first_name": "John",
    "last_name": "Smith",
    "username": "instructor1"
  }
}
```

![Course Update Success Screenshot](presentation/screenshots/Screenshot 2025-10-18 at 12.42.32 PM.png)

### Error Response (404 Not Found):
```json
{
  "error": "Course not found"
}
```

---

# API Endpoint 7: User Enrollment

## `POST /api/enrollments` 🔐

**Purpose:** Enroll authenticated user in a course  
**Authentication:** Required (Bearer Token)  
**Content-Type:** `application/json`

### Request Body:
```json
{
  "course_id": 2
}
```

**Note:** Users can only enroll themselves (students)

---

# User Enrollment - Response

## Success Response (201 Created):
```json
{
  "success": true,
  "message": "Successfully enrolled in course",
  "enrollment": {
    "id": 5,
    "student_id": 3,
    "course_id": 2,
    "enrolled_at": "2025-10-15 12:30:00",
    "status": "active",
    "title": "Database Design",
    "course_code": "ASE240",
    "credits": 3,
    "semester": "Fall",
    "year": "2025",
    "first_name": "John",
    "instructor_name": "Smith"
  }
}
```

![Enrollment Success Screenshot](presentation/screenshots/Screenshot 2025-10-18 at 12.45.46 PM.png)

### Error Response (409 Conflict):
```json
{
  "error": "Already enrolled in this course"
}
```

---

# API Endpoint 8: Enrollment Listing

## `GET /api/enrollments` 🔐

**Purpose:** List user's enrollments  
**Authentication:** Required (Bearer Token)  
**Method:** GET

### Query Parameters (Optional):
- `student_id` - Filter by student (admin/instructor only)
- `course_id` - Filter by course
- `status` - Filter by enrollment status

---

# Enrollment Listing - Response

## Success Response (200 OK):
```json
{
  "success": true,
  "enrollments": [
    {
      "id": 1,
      "student_id": 3,
      "course_id": 1,
      "enrolled_at": "2025-10-15 10:30:00",
      "status": "active",
      "title": "Web Development Fundamentals",
      "course_code": "ASE230",
      "credits": 3,
      "semester": "Fall",
      "year": "2025",
      "instructor_first_name": "John",
      "instructor_last_name": "Smith"
    }
  ],
  "count": 1
}
```

![Enrollment Listing Screenshot](presentation/screenshots/Screenshot 2025-10-18 at 12.46.21 PM.png)

---

# Error Handling

## Common HTTP Status Codes

- **200 OK** - Request successful
- **201 Created** - Resource created successfully
- **400 Bad Request** - Invalid request data
- **401 Unauthorized** - Authentication required/failed
- **403 Forbidden** - Insufficient permissions
- **404 Not Found** - Resource not found
- **409 Conflict** - Resource already exists
- **500 Internal Server Error** - Server error

---

# Error Response Format

## Standard Error Response:
```json
{
  "error": "Error message description"
}
```

### Examples:
```json
{
  "error": "Missing required fields: username, password"
}
```

```json
{
  "error": "Authentication required"
}
```

```json
{
  "error": "Course not found"
}
```

---

# Testing the APIs

## cURL Examples

### User Registration:
```bash
curl -X POST http://localhost:8080/api/users/register \
  -H "Content-Type: application/json" \
  -d '{"username": "testuser", "email": "test@example.com", "password": "password123", "first_name": "Test", "last_name": "User"}'
```

### Authenticated Request:
```bash
curl -X GET http://localhost:8080/api/users/profile \
  -H "Authorization: Bearer <your_token>"
```

---

# Testing Tools

## Available Test Resources

1. **cURL Scripts** - Located in `/tests/curl/`
   - Individual endpoint tests
   - Complete test suite (`test_all_apis.sh`)

2. **HTML/JS Test Interface** - `/tests/html/api_test_suite.html`
   - Interactive web-based testing
   - Real-time results and status

3. **API Documentation** - `http://localhost:8080/api/`
   - Complete endpoint reference
   - Sample requests and responses

---

# Security Features

## Authentication & Authorization

- **JWT Tokens** - Secure token-based authentication
- **Role-Based Access** - Student, Instructor, Admin roles
- **Permission Checks** - Endpoint-level authorization
- **Password Hashing** - Secure password storage
- **Input Validation** - Required field validation
- **CORS Support** - Cross-origin request handling

---

# Database Schema

## Core Tables

- **users** - User accounts and authentication
- **courses** - Course information
- **enrollments** - Student course enrollments
- **assignments** - Course assignments
- **grades** - Student grades
- **quizzes** - Quiz information
- **quiz_questions** - Quiz questions
- **quiz_answers** - Quiz answer options
- **notifications** - User notifications

---

# Best Practices

## API Usage Guidelines

1. **Always include Content-Type header** for POST/PUT requests
2. **Store JWT tokens securely** and include in Authorization header
3. **Handle errors gracefully** - check response status codes
4. **Validate input data** before sending requests
5. **Use HTTPS in production** for secure communication
6. **Implement proper error handling** in your applications

---

# Conclusion

## 🎯 API Summary

✅ **8 Complete REST Endpoints**  
✅ **JWT Authentication System**  
✅ **Role-Based Permissions**  
✅ **Comprehensive Error Handling**  
✅ **Full CRUD Operations**  
✅ **Extensive Testing Suite**  

## 📚 Next Steps

- Frontend integration
- Additional features (assignments, grades)
- Performance optimization
- Production deployment

---

# Thank You!

## Questions & Support

**Repository:** [https://github.com/BsalBhandari/ase230-bhandari-project1](https://github.com/BsalBhandari/ase230-bhandari-project1)

**API Documentation:** `http://localhost:8080/api/`

**Test Suite:** `/tests/` directory

---

# Appendix: Complete cURL Test Suite

## Running All Tests

```bash
# Make executable
chmod +x tests/curl/test_all_apis.sh

# Run complete test suite
./tests/curl/test_all_apis.sh
```

**Expected Output:** All 8 APIs should return ✅ PASS status with proper JSON responses and authentication working correctly.
