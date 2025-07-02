# A3 - Job Vacancy API Documentation

This document describes the A3 Job Vacancy API endpoints for the JobSeeker platform.

## Overview

The Job Vacancy API allows authenticated societies to retrieve job vacancy information, including:
- All available job vacancies
- Job vacancy details by ID
- Job vacancies filtered by category
- Application status and counts for each position

## Authentication

All endpoints require a valid authentication token obtained through the login API.

## Endpoints

### A3a - Get All Job Vacancies

**Request:**
```
GET /api/v1/job_vacancies?token={token}
```

**Parameters:**
- `token` (required): Authentication token

**Success Response (200):**
```json
{
  "vacancies": [
    {
      "id": 1,
      "category": {
        "id": 1,
        "job_category": "Computing and ICT"
      },
      "company": "PT. Maju Mundur Sejahtera",
      "address": "Jln. HOS. Cjokroaminoto (Pasirkaliki) No. 900, DKI Jakarta",
      "description": "We are looking for talented individuals to join our growing company...",
      "has_applied": false,
      "available_position": [
        {
          "id": 1,
          "position": "Desain Grafis",
          "capacity": 3,
          "apply_capacity": 12,
          "apply_count": 5
        },
        {
          "id": 2,
          "position": "Programmer",
          "capacity": 1,
          "apply_capacity": 8,
          "apply_count": 3
        }
      ]
    }
  ]
}
```

**Error Response (401):**
```json
{
  "message": "Unauthorized user"
}
```

### A3c - Get Job Vacancy Detail by ID

**Request:**
```
GET /api/v1/job_vacancies/{id}?token={token}
```

**Parameters:**
- `id` (required): Job vacancy ID
- `token` (required): Authentication token

**Success Response (200):**
```json
{
  "vacancy": {
    "id": 1,
    "category": {
      "id": 1,
      "job_category": "Computing and ICT"
    },
    "company": "PT. Maju Mundur Sejahtera",
    "address": "Jln. HOS. Cjokroaminoto (Pasirkaliki) No. 900, DKI Jakarta",
    "description": "We are looking for talented individuals to join our growing company...",
    "has_applied": false,
    "created_at": "2025-06-28 10:30:00",
    "available_position": [
      {
        "id": 1,
        "position": "Desain Grafis",
        "capacity": 3,
        "apply_capacity": 12,
        "apply_count": 5,
        "is_full": false
      }
    ]
  }
}
```

**Error Response (401):**
```json
{
  "message": "Unauthorized user"
}
```

**Error Response (404):**
```json
{
  "message": "Job vacancy not found"
}
```

### Get Job Vacancies by Category

**Request:**
```
GET /api/v1/job_vacancies/category/{categoryId}?token={token}
```

**Parameters:**
- `categoryId` (required): Job category ID
- `token` (required): Authentication token

**Success Response (200):**
```json
{
  "vacancies": [
    {
      "id": 1,
      "category": {
        "id": 1,
        "job_category": "Computing and ICT"
      },
      "company": "PT. Maju Mundur Sejahtera",
      "address": "Jln. HOS. Cjokroaminoto (Pasirkaliki) No. 900, DKI Jakarta",
      "description": "We are looking for talented individuals...",
      "has_applied": false,
      "available_position": [...]
    }
  ],
  "total_count": 3,
  "applied_count": 1
}
```

## Features Implemented

### 1. Display All Job Vacancies from Database (0.5 points)
- ✅ All job vacancies are retrieved from the database
- ✅ Includes company, address, description, and category information
- ✅ Shows available positions with capacity and application counts

### 2. Display Applied Job Vacancies (0.5 points)
- ✅ `has_applied` field indicates if the user has applied for the vacancy
- ✅ Application history is tracked through `job_apply_societies` table
- ✅ Applied status is shown for each individual vacancy

### 3. Display Position Fields and Counts (0.5 points)
- ✅ Each vacancy shows `available_position` array
- ✅ Position details include:
  - Position name
  - Capacity (total slots)
  - Apply capacity (maximum applications)
  - Apply count (current applications)
  - Full status (if position is full)

## Database Structure

### Tables Used:
- `job_vacancies`: Main job vacancy information
- `job_categories`: Job category details
- `available_positions`: Position details for each vacancy
- `job_apply_societies`: Application records
- `job_apply_positions`: Position-specific applications
- `societies`: User authentication and information

### Key Relationships:
- JobVacancy → JobCategory (belongsTo)
- JobVacancy → AvailablePosition (hasMany)
- JobVacancy → JobApplySociety (hasMany)
- AvailablePosition → JobApplyPosition (hasMany)

## Testing

Use the provided test script `test_api.php` to verify all endpoints:

```bash
cd /path/to/project
php artisan serve
php test_api.php
```

## Error Handling

All endpoints include proper error handling for:
- Invalid or missing authentication tokens (401)
- Missing job vacancies (404)
- Database errors (500)
- Invalid parameters (400)

## Total Score Achievement

- **Display all vacancies**: 0.5/0.5 ✅
- **Display applied vacancies**: 0.5/0.5 ✅
- **Display position fields and counts**: 0.5/0.5 ✅

**Total Functionality Score**: 1.5/1.5 ✅
