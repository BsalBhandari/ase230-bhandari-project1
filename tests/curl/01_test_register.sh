#!/bin/bash
# Test 1: User Registration (POST /users/register)

echo "Testing User Registration API..."
echo "================================="

curl -X POST http://localhost:8080/api/users/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "testuser_curl",
    "email": "testcurl@example.com",
    "password": "password123",
    "first_name": "Test",
    "last_name": "Curl"
  }' | jq '.'

echo -e "\nExpected: success: true, token, user data"
echo "============================================="
