# Screenshot Guide for API Tutorial Slides

## 📸 How to Take Screenshots

### Method 1: Using Postman (Recommended)

1. **Open Postman** and create a new request
2. **Set up the request** with the correct URL and headers
3. **Send the request** and wait for response
4. **Take screenshot** of the entire Postman window showing:
   - Request details (URL, method, headers)
   - Request body (if applicable)
   - Response body with JSON
   - Status code

### Method 2: Using Browser Developer Tools

1. **Open browser** and go to `http://localhost:8080/tests/html/api_test_suite.html`
2. **Open Developer Tools** (F12)
3. **Go to Network tab**
4. **Click test buttons** and watch requests
5. **Take screenshots** of the Network tab showing requests/responses

### Method 3: Using cURL + Terminal

1. **Run cURL commands** from the test scripts
2. **Take screenshots** of terminal output
3. **Include both command and response**

## 📋 Required Screenshots

### 1. User Registration (`01_register_success.png`)
- **URL:** `POST http://localhost:8080/api/users/register`
- **Body:** `{"username": "testuser", "email": "test@example.com", "password": "password123", "first_name": "Test", "last_name": "User"}`
- **Expected:** 201 Created with token and user data

### 2. User Login (`02_login_success.png`)
- **URL:** `POST http://localhost:8080/api/users/login`
- **Body:** `{"username": "student1", "password": "password"}`
- **Expected:** 200 OK with token and user data

### 3. User Profile (`03_profile_success.png`)
- **URL:** `GET http://localhost:8080/api/users/profile`
- **Headers:** `Authorization: Bearer <token>`
- **Expected:** 200 OK with user profile and enrollments

### 4. Course Creation (`04_create_course_success.png`)
- **URL:** `POST http://localhost:8080/api/courses`
- **Headers:** `Authorization: Bearer <instructor_token>`
- **Body:** `{"title": "Test Course", "course_code": "ASE999", "description": "Test description"}`
- **Expected:** 201 Created with course data

### 5. Course Listing (`05_list_courses_success.png`)
- **URL:** `GET http://localhost:8080/api/courses`
- **Expected:** 200 OK with array of courses

### 6. Course Update (`06_update_course_success.png`)
- **URL:** `PUT http://localhost:8080/api/courses/1`
- **Headers:** `Authorization: Bearer <instructor_token>`
- **Body:** `{"title": "Updated Course Title"}`
- **Expected:** 200 OK with updated course data

### 7. User Enrollment (`07_enroll_success.png`)
- **URL:** `POST http://localhost:8080/api/enrollments`
- **Headers:** `Authorization: Bearer <student_token>`
- **Body:** `{"course_id": 2}`
- **Expected:** 201 Created with enrollment data

### 8. Enrollment Listing (`08_list_enrollments_success.png`)
- **URL:** `GET http://localhost:8080/api/enrollments`
- **Headers:** `Authorization: Bearer <student_token>`
- **Expected:** 200 OK with array of enrollments

## 🛠️ Screenshot Tips

### Postman Setup:
1. **Create Collection** called "ASE230 API Tests"
2. **Add all 8 requests** with proper names
3. **Set up environment variables** for base URL and tokens
4. **Use consistent formatting** for all screenshots

### Screenshot Quality:
- **High resolution** (at least 1920x1080)
- **Clear text** - ensure JSON is readable
- **Consistent cropping** - show relevant parts only
- **Good contrast** - avoid dark themes that don't print well

### File Naming:
- Use descriptive names: `01_register_success.png`
- Include test number and endpoint name
- Add `_success` or `_error` suffix

## 📄 Converting to PDF

### Method 1: Using Marp CLI (Recommended)

1. **Install Marp CLI:**
   ```bash
   npm install -g @marp-team/marp-cli
   ```

2. **Convert to PDF:**
   ```bash
   marp docs/api_tutorial_slides.md --pdf --output presentation/api_tutorial.pdf
   ```

### Method 2: Using Marp VS Code Extension

1. **Install Marp extension** in VS Code
2. **Open** `docs/api_tutorial_slides.md`
3. **Click "Marp"** in the status bar
4. **Export as PDF**

### Method 3: Using Online Marp

1. **Go to** [https://web.marp.app/](https://web.marp.app/)
2. **Paste** the markdown content
3. **Export as PDF**

## 📁 File Organization

```
presentation/
├── screenshots/
│   ├── 01_register_success.png
│   ├── 02_login_success.png
│   ├── 03_profile_success.png
│   ├── 04_create_course_success.png
│   ├── 05_list_courses_success.png
│   ├── 06_update_course_success.png
│   ├── 07_enroll_success.png
│   └── 08_list_enrollments_success.png
├── api_tutorial.pdf
└── README.md
```

## 🚀 Quick Start Commands

### Take Screenshots:
1. Open Postman
2. Import the test collection
3. Run each test and screenshot
4. Save to `presentation/screenshots/`

### Convert to PDF:
```bash
# Install Marp CLI
npm install -g @marp-team/marp-cli

# Convert slides to PDF
marp docs/api_tutorial_slides.md --pdf --output presentation/api_tutorial.pdf
```

### Verify PDF:
```bash
# Check if PDF was created
ls -la presentation/api_tutorial.pdf
```

## 📝 Notes

- **Test all endpoints** before taking screenshots
- **Use consistent tokens** across screenshots
- **Include both success and error examples** where relevant
- **Ensure all screenshots are clear** and professional-looking
- **Test PDF conversion** to ensure quality
