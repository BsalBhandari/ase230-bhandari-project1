#!/bin/bash
# Test 2: User Login (POST /users/login) - SECURE

echo "Testing User Login API..."
echo "========================"

curl -X POST http://localhost:8080/api/users/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "student1",
    "password": "password"
  }' | jq '.'

echo -e "\nExpected: success: true, token, user data"
echo "============================================="
