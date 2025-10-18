#!/bin/bash
# ASE230 Project 1 - API Test Suite
# Tests all 8 required REST API endpoints

BASE_URL="http://localhost:8080/api"
echo "🚀 Starting ASE230 Project 1 API Tests"
echo "========================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print test results
print_result() {
    local test_name="$1"
    local status="$2"
    local response="$3"
    
    if [ "$status" = "PASS" ]; then
        echo -e "${GREEN}✅ $test_name: PASS${NC}"
    else
        echo -e "${RED}❌ $test_name: FAIL${NC}"
        echo -e "${RED}Response: $response${NC}"
    fi
}

# Function to extract token from response
extract_token() {
    echo "$1" | grep -o '"token":"[^"]*"' | cut -d'"' -f4
}

echo -e "\n${BLUE}1. Testing User Registration (POST /users/register)${NC}"
REGISTER_RESPONSE=$(curl -s -X POST "$BASE_URL/users/register" \
    -H "Content-Type: application/json" \
    -d '{"username": "testuser_api", "email": "testapi@example.com", "password": "password123", "first_name": "Test", "last_name": "API"}')

if echo "$REGISTER_RESPONSE" | grep -q '"success":true'; then
    print_result "User Registration" "PASS" "$REGISTER_RESPONSE"
    REGISTER_TOKEN=$(extract_token "$REGISTER_RESPONSE")
else
    print_result "User Registration" "FAIL" "$REGISTER_RESPONSE"
fi

echo -e "\n${BLUE}2. Testing User Login (POST /users/login)${NC}"
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/users/login" \
    -H "Content-Type: application/json" \
    -d '{"username": "student1", "password": "password"}')

if echo "$LOGIN_RESPONSE" | grep -q '"success":true'; then
    print_result "User Login" "PASS" "$REGISTER_RESPONSE"
    LOGIN_TOKEN=$(extract_token "$LOGIN_RESPONSE")
else
    print_result "User Login" "FAIL" "$LOGIN_RESPONSE"
fi

echo -e "\n${BLUE}3. Testing User Profile (GET /users/profile) - SECURE${NC}"
if [ -n "$LOGIN_TOKEN" ]; then
    PROFILE_RESPONSE=$(curl -s -X GET "$BASE_URL/users/profile" \
        -H "Authorization: Bearer $LOGIN_TOKEN")
    
    if echo "$PROFILE_RESPONSE" | grep -q '"success":true'; then
        print_result "User Profile (Secure)" "PASS" "$PROFILE_RESPONSE"
    else
        print_result "User Profile (Secure)" "FAIL" "$PROFILE_RESPONSE"
    fi
else
    print_result "User Profile (Secure)" "FAIL" "No token available"
fi

echo -e "\n${BLUE}4. Testing Course Creation (POST /courses) - SECURE${NC}"
# Get instructor token
INSTRUCTOR_LOGIN=$(curl -s -X POST "$BASE_URL/users/login" \
    -H "Content-Type: application/json" \
    -d '{"username": "instructor1", "password": "password"}')

INSTRUCTOR_TOKEN=$(extract_token "$INSTRUCTOR_LOGIN")

if [ -n "$INSTRUCTOR_TOKEN" ]; then
    COURSE_CREATE_RESPONSE=$(curl -s -X POST "$BASE_URL/courses" \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer $INSTRUCTOR_TOKEN" \
        -d '{"title": "API Test Course", "course_code": "ASE999", "description": "Course created via API test", "credits": 3}')
    
    if echo "$COURSE_CREATE_RESPONSE" | grep -q '"success":true'; then
        print_result "Course Creation (Secure)" "PASS" "$COURSE_CREATE_RESPONSE"
        COURSE_ID=$(echo "$COURSE_CREATE_RESPONSE" | grep -o '"id":[0-9]*' | cut -d':' -f2)
    else
        print_result "Course Creation (Secure)" "FAIL" "$COURSE_CREATE_RESPONSE"
    fi
else
    print_result "Course Creation (Secure)" "FAIL" "No instructor token available"
fi

echo -e "\n${BLUE}5. Testing Course Listing (GET /courses)${NC}"
COURSES_RESPONSE=$(curl -s -X GET "$BASE_URL/courses")

if echo "$COURSES_RESPONSE" | grep -q '"success":true'; then
    print_result "Course Listing" "PASS" "$COURSES_RESPONSE"
else
    print_result "Course Listing" "FAIL" "$COURSES_RESPONSE"
fi

echo -e "\n${BLUE}6. Testing Course Update (PUT /courses/{id}) - SECURE${NC}"
if [ -n "$INSTRUCTOR_TOKEN" ] && [ -n "$COURSE_ID" ]; then
    COURSE_UPDATE_RESPONSE=$(curl -s -X PUT "$BASE_URL/courses/$COURSE_ID" \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer $INSTRUCTOR_TOKEN" \
        -d '{"title": "Updated API Test Course", "description": "Updated description", "credits": 4}')
    
    if echo "$COURSE_UPDATE_RESPONSE" | grep -q '"success":true'; then
        print_result "Course Update (Secure)" "PASS" "$COURSE_UPDATE_RESPONSE"
    else
        print_result "Course Update (Secure)" "FAIL" "$COURSE_UPDATE_RESPONSE"
    fi
else
    print_result "Course Update (Secure)" "FAIL" "No token or course ID available"
fi

echo -e "\n${BLUE}7. Testing User Enrollment (POST /enrollments) - SECURE${NC}"
if [ -n "$LOGIN_TOKEN" ] && [ -n "$COURSE_ID" ]; then
    ENROLL_RESPONSE=$(curl -s -X POST "$BASE_URL/enrollments" \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer $LOGIN_TOKEN" \
        -d "{\"course_id\": $COURSE_ID}")
    
    if echo "$ENROLL_RESPONSE" | grep -q '"success":true'; then
        print_result "User Enrollment (Secure)" "PASS" "$ENROLL_RESPONSE"
    else
        print_result "User Enrollment (Secure)" "FAIL" "$ENROLL_RESPONSE"
    fi
else
    print_result "User Enrollment (Secure)" "FAIL" "No token or course ID available"
fi

echo -e "\n${BLUE}8. Testing Enrollment Listing (GET /enrollments) - SECURE${NC}"
if [ -n "$LOGIN_TOKEN" ]; then
    ENROLLMENTS_RESPONSE=$(curl -s -X GET "$BASE_URL/enrollments" \
        -H "Authorization: Bearer $LOGIN_TOKEN")
    
    if echo "$ENROLLMENTS_RESPONSE" | grep -q '"success":true'; then
        print_result "Enrollment Listing (Secure)" "PASS" "$ENROLLMENTS_RESPONSE"
    else
        print_result "Enrollment Listing (Secure)" "FAIL" "$ENROLLMENTS_RESPONSE"
    fi
else
    print_result "Enrollment Listing (Secure)" "FAIL" "No token available"
fi

echo -e "\n${YELLOW}========================================${NC}"
echo -e "${YELLOW}API Test Suite Complete!${NC}"
echo -e "${YELLOW}========================================${NC}"

# Test authentication failures
echo -e "\n${BLUE}Testing Authentication Failures${NC}"
echo -e "\n${BLUE}9. Testing Profile without Token (Should Fail)${NC}"
PROFILE_NO_TOKEN=$(curl -s -X GET "$BASE_URL/users/profile")
if echo "$PROFILE_NO_TOKEN" | grep -q '"error"'; then
    print_result "Profile without Token (Should Fail)" "PASS" "Correctly rejected"
else
    print_result "Profile without Token (Should Fail)" "FAIL" "Should have been rejected"
fi

echo -e "\n${BLUE}10. Testing Course Creation as Student (Should Fail)${NC}"
if [ -n "$LOGIN_TOKEN" ]; then
    STUDENT_COURSE_CREATE=$(curl -s -X POST "$BASE_URL/courses" \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer $LOGIN_TOKEN" \
        -d '{"title": "Student Course", "course_code": "ASE888", "description": "Should fail"}')
    
    if echo "$STUDENT_COURSE_CREATE" | grep -q '"error"'; then
        print_result "Student Course Creation (Should Fail)" "PASS" "Correctly rejected"
    else
        print_result "Student Course Creation (Should Fail)" "FAIL" "Should have been rejected"
    fi
fi

echo -e "\n${GREEN}All tests completed! Check results above.${NC}"
