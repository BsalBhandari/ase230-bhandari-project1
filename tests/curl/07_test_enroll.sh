#!/bin/bash
# Test 7: User Enrollment (POST /enrollments) - SECURE

echo "Testing User Enrollment API..."
echo "============================="

# Get student token
TOKEN=$(curl -s -X POST http://localhost:8080/api/users/login \
  -H "Content-Type: application/json" \
  -d '{"username": "student1", "password": "password"}' | jq -r '.token')

echo "Using student token: ${TOKEN:0:50}..."

curl -X POST http://localhost:8080/api/enrollments \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "course_id": 2
  }' | jq '.'

echo -e "\nExpected: success: true, enrollment data"
echo "=========================================="
