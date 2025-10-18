# ASE230 Project 1 - Project Plan

## Project Overview
This document outlines the plan and structure for ASE230 Project 1.

## Project Structure

```
ase230-bhandari-project1/
├── api/                    # REST API endpoints
│   ├── users.php          # User management endpoints
│   ├── courses.php        # Course management endpoints
│   └── ...
├── config/                # Configuration files
│   └── db.php            # Database connection settings
├── models/                # PHP classes and data models
│   ├── User.php          # User model class
│   ├── Course.php        # Course model class
│   └── ...
├── docs/                  # Documentation
│   ├── plan.md           # This file
│   ├── screenshots/      # Project screenshots
│   └── ...
├── index.php             # Main entry point
├── README.md             # Project README
└── .gitignore            # Git ignore rules
```

## Features to Implement

### Core Features
- [x] User authentication system
- [x] Course management
- [x] Database integration
- [x] REST API endpoints
- [ ] Frontend interface

### Technical Requirements
- [x] PHP backend
- [x] MySQL database
- [x] RESTful API design
- [x] Error handling
- [x] Input validation
- [x] Security measures

## Milestone 2 – Database + Authentication Phase
**What:** Implemented login/register + DB integration  
**When (Plan):** Week 7  
**When (Actual):** October 15, 2025  
**Comment:** Bearer token auth verified via cURL and HTML/JS tests; DB CRUD working with all 8 required APIs implemented and tested.

### Completed Features:
- ✅ **Database Setup**: MySQL database `ase230_project1` with 9 tables
- ✅ **Authentication System**: JWT Bearer token authentication
- ✅ **User Management**: Registration, login, profile retrieval
- ✅ **Course Management**: Create, read, update courses
- ✅ **Enrollment System**: Enroll users, list enrollments
- ✅ **API Security**: Role-based permissions (student/instructor/admin)
- ✅ **Comprehensive Testing**: cURL scripts + HTML/JS test suite

### API Endpoints Implemented:
1. **POST /api/users/register** - User registration
2. **POST /api/users/login** 🔐 - User authentication (SECURE)
3. **GET /api/users/profile** 🔐 - User profile retrieval (SECURE)
4. **POST /api/courses** 🔐 - Course creation (SECURE)
5. **GET /api/courses** - Course listing
6. **PUT /api/courses/{id}** 🔐 - Course update (SECURE)
7. **POST /api/enrollments** 🔐 - User enrollment (SECURE)
8. **GET /api/enrollments** 🔐 - Enrollment listing (SECURE)

### Technical Achievements:
- **NGINX Configuration**: Proper URL routing for API endpoints
- **PHP-FPM Integration**: Working PHP processing
- **Database Schema**: Complete LMS-style database with relationships
- **JWT Authentication**: Custom token generation and validation
- **CORS Support**: Cross-origin requests enabled
- **Error Handling**: Comprehensive error responses
- **Input Validation**: Required field validation and sanitization

## Additional Milestones
- **GitHub repo + tools setup:** 10/18/2025 (Actual) - *Started working earlier, repo setup today*
- **REST API list finalized:** 09/17/2025 (Plan)  
- **Draft CRUD APIs:** 10/01/2025 (Plan)  
- **API testing with curl/HTML:** 10/08/2025 (Plan)  
- **Finalize slides for Project 1:** 10/18/2025 (not Plan)  

## Development Phases

### Phase 1: Setup and Planning
- [x] Create GitHub repository
- [x] Set up folder structure
- [x] Configure database connection
- [x] Design database schema

### Phase 2: Backend Development ✅ COMPLETED
- [x] Create data models (database tables)
- [x] Implement API endpoints (all 8 required)
- [x] Add authentication (JWT Bearer tokens)
- [x] Test API functionality (cURL + HTML/JS tests)

### Phase 3: Frontend Development
- [ ] Create user interface
- [ ] Integrate with API
- [ ] Add responsive design
- [ ] Test user experience

### Phase 4: Testing and Deployment
- [ ] Unit testing
- [ ] Integration testing
- [ ] Performance optimization
- [ ] Deployment preparation

## Database Schema (Draft)

### Users Table
- id (Primary Key)
- username (Unique)
- email (Unique)
- password_hash
- created_at
- updated_at

### Courses Table
- id (Primary Key)
- title
- description
- instructor_id (Foreign Key)
- created_at
- updated_at

## API Endpoints (Draft)

### Users
- GET /api/users - List all users
- POST /api/users - Create new user
- GET /api/users/{id} - Get user by ID
- PUT /api/users/{id} - Update user
- DELETE /api/users/{id} - Delete user

### Courses
- GET /api/courses - List all courses
- POST /api/courses - Create new course
- GET /api/courses/{id} - Get course by ID
- PUT /api/courses/{id} - Update course
- DELETE /api/courses/{id} - Delete course

## Notes
- Update this plan as the project evolves
- Add screenshots and documentation as features are completed
- Keep track of any changes or deviations from the original plan
