#!/bin/bash
# Test 6: Course Update (PUT /courses/{id}) - SECURE

echo "Testing Course Update API..."
echo "==========================="

# Get instructor token
TOKEN=$(curl -s -X POST http://localhost:8080/api/users/login \
  -H "Content-Type: application/json" \
  -d '{"username": "instructor1", "password": "password"}' | jq -r '.token')

echo "Using instructor token: ${TOKEN:0:50}..."

# Update course with ID 1
curl -X PUT http://localhost:8080/api/courses/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "title": "Updated Web Development Fundamentals",
    "description": "Updated description for web development course",
    "credits": 4
  }' | jq '.'

echo -e "\nExpected: success: true, updated course data"
echo "=============================================="
