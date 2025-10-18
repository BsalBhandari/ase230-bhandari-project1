#!/bin/bash
# Test 4: Course Creation (POST /courses) - SECURE

echo "Testing Course Creation API..."
echo "=============================="

# Get instructor token
TOKEN=$(curl -s -X POST http://localhost:8080/api/users/login \
  -H "Content-Type: application/json" \
  -d '{"username": "instructor1", "password": "password"}' | jq -r '.token')

echo "Using instructor token: ${TOKEN:0:50}..."

curl -X POST http://localhost:8080/api/courses \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "title": "Advanced Web Development",
    "course_code": "ASE400",
    "description": "Advanced concepts in web development",
    "credits": 4,
    "semester": "Spring",
    "year": 2026
  }' | jq '.'

echo -e "\nExpected: success: true, course data"
echo "======================================"
