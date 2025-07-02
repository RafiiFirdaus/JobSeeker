# JobSeeker API Documentation - Data Validation

## Feature A2 - Request Data Validation Implementation ✅

The data validation API endpoints have been successfully implemented with complete functionality for requesting and tracking validation status.

## API Endpoints

### Base URL
```
[domain]/api/v1
```

## Data Validation Endpoints

### 1. Request Data Validation
**Endpoint:** `POST /api/v1/validation`

**Request Parameters:**
- `token` (string) - Authentication token

**Request Body:**
```json
{
    "work_experience": "5 years experience in web development using Laravel and Vue.js",
    "job_category": 1,
    "job_position": "Full Stack Developer",
    "reason_accepted": "I have strong technical skills and proven track record"
}
```

**Response - Success (200):**
```json
{
    "message": "Request data validation sent successful"
}
```

**Response - Invalid Token (401):**
```json
{
    "message": "Unauthorized user"
}
```

**Response - Validation Error (422):**
```json
{
    "message": "Data ada yang kosong",
    "errors": {
        "job_position": ["Job position is required"],
        "reason_accepted": ["Reason for acceptance is required"]
    }
}
```

### 2. Get Society Data Validation
**Endpoint:** `GET /api/v1/validations`

**Request Parameters:**
- `token` (string) - Authentication token

**Example Request:**
```
GET /api/v1/validations?token=e96aaafb6f2f76460b8cc93723bd030e
```

**Response - Success (200):**
```json
{
    "validation": {
        "id": 1,
        "status": "pending",
        "work_experience": "5 years experience in web development",
        "job_category_id": 1,
        "job_position": "Full Stack Developer",
        "reason_accepted": "I have strong technical skills",
        "validator_notes": null,
        "validator": null
    }
}
```

**Response - With Validator (200):**
```json
{
    "validation": {
        "id": 1,
        "status": "accepted",
        "work_experience": "5 years experience in web development",
        "job_category_id": 1,
        "job_position": "Full Stack Developer",
        "reason_accepted": "I have strong technical skills",
        "validator_notes": "Good technical background and experience",
        "validator": {
            "id": 1,
            "name": "John Doe",
            "email": "validator@example.com"
        }
    }
}
```

**Response - Invalid Token (401):**
```json
{
    "message": "Unauthorized user"
}
```

### 3. Get Job Categories
**Endpoint:** `GET /api/v1/job-categories`

**Response - Success (200):**
```json
{
    "job_categories": [
        {
            "id": 1,
            "name": "Computing and ICT",
            "description": null
        },
        {
            "id": 2,
            "name": "Engineering",
            "description": null
        }
    ]
}
```

### 4. Get Validation History
**Endpoint:** `GET /api/v1/validations/history`

**Request Parameters:**
- `token` (string) - Authentication token

**Response - Success (200):**
```json
{
    "validations": [
        {
            "id": 1,
            "status": "accepted",
            "work_experience": "5 years experience",
            "job_category_id": 1,
            "job_category_name": "Computing and ICT",
            "job_position": "Full Stack Developer",
            "reason_accepted": "Strong technical skills",
            "validator_notes": "Approved",
            "created_at": "2025-06-28 14:30:00",
            "validator": {
                "id": 1,
                "name": "John Doe",
                "email": "validator@example.com"
            }
        }
    ]
}
```

## Testing Instructions

### Prerequisites
1. Start the Laravel development server: `php artisan serve`
2. Login to get authentication token: Use `/api/v1/auth/login`

### Test Sequence

#### 1. Get Job Categories (Optional)
```bash
curl -X GET "http://127.0.0.1:8000/api/v1/job-categories"
```

#### 2. Login to Get Token
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"id_card_number":"20210001","password":"121212"}'
```

#### 3. Request Data Validation
```bash
curl -X POST "http://127.0.0.1:8000/api/v1/validation?token=YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "work_experience": "5 years in web development",
    "job_category": 1,
    "job_position": "Full Stack Developer", 
    "reason_accepted": "Strong technical background"
  }'
```

#### 4. Test Empty Fields Error
```bash
curl -X POST "http://127.0.0.1:8000/api/v1/validation?token=YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "work_experience": "Some experience",
    "job_category": 1,
    "job_position": "",
    "reason_accepted": ""
  }'
```

#### 5. Check Validation Status
```bash
curl -X GET "http://127.0.0.1:8000/api/v1/validations?token=YOUR_TOKEN_HERE"
```

#### 6. Test Invalid Token
```bash
curl -X GET "http://127.0.0.1:8000/api/v1/validations?token=invalid_token"
```

## Web Application Features

### Enhanced Web Interface ✅

The web application now includes:

#### 1. Request Data Validation
- URL: `/data-validation/create`
- Features: Form validation, job category selection, experience input

#### 2. View Progress 
- URL: `/data-validation/progress`
- Features: Progress tracking with visual indicators, status badges

#### 3. View Results
- URL: `/data-validation/results` 
- Features: Detailed validation results, validator feedback

#### 4. Navigation
- Dropdown menu for easy access to all validation features

## Functionality Checklist ✅

- ✅ **Authorized User Validation**: API validates token for all requests
- ✅ **Unauthorized User Message**: Returns "Unauthorized user" for invalid tokens
- ✅ **Empty Field Validation**: Shows "Data ada yang kosong" for missing required fields
- ✅ **Progress Display**: Web interface shows validation progress with visual indicators
- ✅ **Results Display**: Complete validation results with validator feedback

## Implementation Features

### Security:
- Token-based authentication for all API endpoints
- Input validation and sanitization
- Proper error handling and user feedback

### Database Integration:
- Real data storage and retrieval
- Relationship with job categories and validators
- Status tracking (pending, accepted, rejected)

### User Experience:
- Clear progress visualization
- Comprehensive result display
- Easy navigation between validation features
- Responsive design for all devices

## Available Test Data

**Test Account:**
- ID Card: `20210001`
- Password: `121212`

**Job Categories:**
- ID 1: Computing and ICT
- ID 2: Engineering
- ID 3: Healthcare
- ID 4: Education

The system is now fully functional and ready for production use with all specified A2 requirements implemented.
