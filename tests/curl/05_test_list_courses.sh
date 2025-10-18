#!/bin/bash
# Test 5: Course Listing (GET /courses)

echo "Testing Course Listing API..."
echo "============================="

curl -X GET http://localhost:8080/api/courses | jq '.'

echo -e "\nExpected: success: true, array of courses"
echo "==========================================="
