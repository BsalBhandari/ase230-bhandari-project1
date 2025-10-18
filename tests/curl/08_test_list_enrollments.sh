#!/bin/bash
# Test 8: Enrollment Listing (GET /enrollments) - SECURE

echo "Testing Enrollment Listing API..."
echo "================================="

# Get student token
TOKEN=$(curl -s -X POST http://localhost:8080/api/users/login \
  -H "Content-Type: application/json" \
  -d '{"username": "student1", "password": "password"}' | jq -r '.token')

echo "Using student token: ${TOKEN:0:50}..."

curl -X GET http://localhost:8080/api/enrollments \
  -H "Authorization: Bearer $TOKEN" | jq '.'

echo -e "\nExpected: success: true, array of enrollments"
echo "==============================================="
