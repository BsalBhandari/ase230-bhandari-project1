---
marp: true
theme: default
class: lead
paginate: true
backgroundColor: #fff
---

# ASE230 Project 1 - LMS API Tutorial

## How to Use Our School Website API

**Made by:** Bishal Bhandari  
**Class:** ASE230 - Server-Side Programming 
**Date:** October 2025

---

# Table of Contents

## 📋 What This Website Can Do

1. **Sign Up** - `POST /api/users/register`
2. **Log In** - `POST /api/users/login` 🔐
3. **See Your Profile** - `GET /api/users/profile` 🔐
4. **Create a Class** - `POST /api/courses` 🔐
5. **See All Classes** - `GET /api/courses`
6. **Change a Class** - `PUT /courses/{id}` 🔐
7. **Join a Class** - `POST /api/enrollments` 🔐
8. **See Your Classes** - `GET /api/enrollments` 🔐

**🔐 = You need to be logged in**

---

# How to Log In

## 🔐 Getting Your Login Token

- **What it is:** A special password that lasts 24 hours
- **How to use it:** Put it in the header like this: `Authorization: Bearer <token>`
- **Who can use it:** Students, teachers, and admins

### Example:
```bash
curl -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

---

# How to Sign Up

## `POST /api/users/register`

**What it does:** Create a new account  
**Do you need to be logged in?** No  
**What to send:** `application/json`

### What to send:
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

# Sign Up - What You Get Back

## If it worked (201 Created):
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

---

![Sign Up Success Screenshot](screenshots/api:users:register.png)

---

### If it didn't work (409 Conflict):
```json
{
  "error": "Username or email already exists"
}
```

---

# How to Log In

## `POST /api/users/login` 🔐

**What it does:** Get your login token  
**Do you need to be logged in?** No  
**What to send:** `application/json`

### What to send:
```json
{
  "username": "student1",
  "password": "password"
}
```

**Note:** You can use your username or email address

---

# Log In - What You Get Back

## If it worked (200 OK):
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

---

![Log In Success Screenshot](screenshots/api:users:login.png)

---

### If it didn't work (401 Unauthorized):
```json
{
  "error": "Invalid credentials"
}
```

---

# How to See Your Profile

## `GET /api/users/profile` 🔐

**What it does:** Show your info and your classes  
**Do you need to be logged in?** Yes  
**How:** GET

### What to send:
```
Authorization: Bearer <your_token>
```

**What you get:** Your profile + your classes (if you're a student) or classes you teach (if you're a teacher)

---

# Your Profile - What You Get Back

## If it worked (200 OK):
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

---

![Your Profile Screenshot](screenshots/api:users:profile.png)

---

---

# How to Create a Class

## `POST /api/courses` 🔐

**What it does:** Make a new class (teachers and admins only)  
**Do you need to be logged in?** Yes  
**What to send:** `application/json`

### What to send:
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

**You must include:** `title` and `course_code`

---

# Create Class - What You Get Back

## If it worked (201 Created):
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

---

![Create Class Success Screenshot](screenshots/post:api:courses.png)

---

### If it didn't work (403 Forbidden):
```json
{
  "error": "Insufficient permissions"
}
```

---

# How to See All Classes

## `GET /api/courses`

**What it does:** Show all available classes  
**Do you need to be logged in?** No  
**How:** GET

### You can filter by:
- `semester` - Show only certain semester
- `year` - Show only certain year
- `instructor` - Show only certain teacher

### Example:
```
GET /api/courses?semester=Fall&year=2025
```

---

# See All Classes - What You Get Back

## If it worked (200 OK):
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

---

![See All Classes Screenshot](screenshots/get:api:courses.png)

---

---

# How to Change a Class

## `PUT /api/courses/{id}` 🔐

**What it does:** Update an existing class  
**Do you need to be logged in?** Yes  
**What to send:** `application/json`

### What to send:
```json
{
  "title": "Updated Web Development Course",
  "description": "Updated course description",
  "credits": 4
}
```

**Who can do this:** Only the teacher who made the class or admins

---

# Change Class - What You Get Back

## If it worked (200 OK):
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

---

![Change Class Success Screenshot](screenshots/api:courses:1.png)

---

### If it didn't work (404 Not Found):
```json
{
  "error": "Course not found"
}
```

---

# How to Join a Class

## `POST /api/enrollments` 🔐

**What it does:** Sign up for a class  
**Do you need to be logged in?** Yes  
**What to send:** `application/json`

### What to send:
```json
{
  "course_id": 2
}
```

**Note:** You can only join classes for yourself (students only)

---

# Join Class - What You Get Back

## If it worked (201 Created):
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

---

![Join Class Success Screenshot](screenshots/post:api:enrollements.png)

---

### If it didn't work (409 Conflict):
```json
{
  "error": "Already enrolled in this course"
}
```

---

# How to See Your Classes

## `GET /api/enrollments` 🔐

**What it does:** Show all the classes you joined  
**Do you need to be logged in?** Yes  
**How:** GET

### You can filter by:
- `student_id` - Show classes for a certain student (admins and teachers only)
- `course_id` - Show only a certain class
- `status` - Show only active or inactive classes

---

# See Your Classes - What You Get Back

## If it worked (200 OK):
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

---

![See Your Classes Screenshot](screenshots/get:api:enrollements.png)

---

---

# When Things Go Wrong

## Common Error Codes

- **200 OK** - Everything worked fine
- **201 Created** - Something new was made successfully
- **400 Bad Request** - You sent bad information
- **401 Unauthorized** - You need to log in first
- **403 Forbidden** - You don't have permission
- **404 Not Found** - We can't find what you're looking for
- **409 Conflict** - This already exists
- **500 Internal Server Error** - Something broke on our end

---

# Error Messages

## What error messages look like:
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

# Testing the Website

## How to test with cURL

### Sign Up:
```bash
curl -X POST http://localhost:8080/api/users/register \
  -H "Content-Type: application/json" \
  -d '{"username": "testuser", "email": "test@example.com", "password": "password123", "first_name": "Test", "last_name": "User"}'
```

### Get your profile (you need to be logged in):
```bash
curl -X GET http://localhost:8080/api/users/profile \
  -H "Authorization: Bearer <your_token>"
```

---

# Testing Tools

## What you can use to test

1. **cURL Scripts** - Located in `/tests/curl/`
   - Test each part one by one
   - Test everything at once (`test_all_apis.sh`)

2. **Web Test Page** - `/tests/html/api_test_suite.html`
   - Test using a web page
   - See results right away

3. **Website Help** - `http://localhost:8080/api/`
   - See all the things you can do
   - See examples of requests and responses

---

# Security Features

## How we keep things safe

- **Login Tokens** - Safe password system
- **Different User Types** - Student, Teacher, Admin roles
- **Permission Checks** - Only certain people can do certain things
- **Password Protection** - Passwords are stored safely
- **Input Checking** - We check all the information you send
- **Cross-Origin Support** - Works from different websites

---

# Database Tables

## What information we store

- **users** - User accounts and login info
- **courses** - Class information
- **enrollments** - Which students are in which classes
- **assignments** - Class homework
- **grades** - Student grades
- **quizzes** - Quiz information
- **quiz_questions** - Quiz questions
- **quiz_answers** - Quiz answer choices
- **notifications** - Messages for users

---

# Best Ways to Use This

## Tips for using the website

1. **Always include Content-Type header** when sending information
2. **Keep your login token safe** and put it in the Authorization header
3. **Check if things worked** - look at the response codes
4. **Check your information** before sending requests
5. **Use HTTPS in real life** for safe communication
6. **Handle errors nicely** in your applications

---

# Summary

## 🎯 What we built

✅ **8 Complete Website Features**  
✅ **Safe Login System**  
✅ **Different User Types**  
✅ **Good Error Handling**  
✅ **Full Create/Read/Update/Delete**  
✅ **Lots of Testing Tools**  

## 📚 What's next

- Make a website frontend
- Add more features (homework, grades)
- Make it faster
- Put it on the internet

---

# Thank You!

## Questions & Help

**Code Repository:** [https://github.com/BsalBhandari/ase230-bhandari-project1](https://github.com/BsalBhandari/ase230-bhandari-project1)

**Website Help:** `http://localhost:8080/api/`

**Test Tools:** `/tests/` folder

---

# Extra: Complete Test Commands

## Running All Tests

```bash
# Make the test file work
chmod +x tests/curl/test_all_apis.sh

# Run all tests
./tests/curl/test_all_apis.sh
```

**What should happen:** All 8 website features should work ✅ and show proper JSON responses with login working correctly.
