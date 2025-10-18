#!/bin/bash
# Test 3: User Profile (GET /users/profile) - SECURE

echo "Testing User Profile API..."
echo "=========================="

# First get a token
TOKEN=$(curl -s -X POST http://localhost:8080/api/users/login \
  -H "Content-Type: application/json" \
  -d '{"username": "student1", "password": "password"}' | jq -r '.token')

echo "Using token: ${TOKEN:0:50}..."

curl -X GET http://localhost:8080/api/users/profile \
  -H "Authorization: Bearer $TOKEN" | jq '.'

echo -e "\nExpected: success: true, user profile, enrollments"
echo "====================================================="
