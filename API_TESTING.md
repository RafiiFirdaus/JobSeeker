# API Testing Instructions

## Backend API Implementation Complete ✅

The JobSeeker platform now has a fully functional backend API for authentication as specified in the requirements.

## Quick Test

### 1. Start the Development Server
```bash
php artisan serve
```

### 2. Test Login API
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"id_card_number":"20210001","password":"121212"}'
```

**Expected Response:**
```json
{
    "name": "Omar Gunawan",
    "born_date": "1990-04-18",
    "gender": "male",
    "address": "Jln. Baranang Siang No. 479, DKI Jakarta",
    "token": "some_generated_hash_token",
    "regional": {
        "id": 1,
        "province": "DKI Jakarta",
        "district": "Central Jakarta"
    }
}
```

### 3. Test with Wrong Credentials
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"id_card_number":"wrong","password":"wrong"}'
```

**Expected Response:**
```json
{
    "message": "ID Card Number or Password incorrect"
}
```

### 4. Test Logout (use token from login response)
```bash
curl -X POST "http://127.0.0.1:8000/api/v1/auth/logout?token=YOUR_TOKEN_HERE"
```

## Web Application Testing

### 1. Visit Login Page
- URL: `http://127.0.0.1:8000/login`
- Test with valid credentials: ID `20210001`, Password `121212`
- Test with invalid credentials to see error message

### 2. Verify Session Management
- After successful login, try accessing `/login` again
- Should redirect to dashboard (cannot return to login page)
- Logout and verify return to home page

## Implementation Checklist ✅

- ✅ **A1a - Login Success**: Returns user data with token (200 status)
- ✅ **A1b - Login Failure**: Returns error message (401 status)  
- ✅ **A1c - Logout Success**: Returns success message (200 status)
- ✅ **A1d - Logout Invalid Token**: Returns error message (401 status)
- ✅ **Web Login**: Real database authentication with proper validation
- ✅ **Error Messages**: Shows "ID or Password incorrect" for wrong credentials
- ✅ **Session Protection**: Cannot return to login page when logged in
- ✅ **Token Security**: MD5 hash tokens generated from ID card + timestamp

## Available Test Accounts

| ID Card Number | Password | Name |
|---|---|---|
| 20210001 | 121212 | Omar Gunawan |
| 20210002 | 121212 | Nilam Sinaga |
| 20210003 | 121212 | Rosman Lailasari |
| 20210004 | 121212 | Ifa Adriansyah |

## Features Implemented

### Security Features:
- Token-based authentication using MD5 hash
- Password verification using Laravel Hash
- Proper session management
- Input validation and error handling
- Token cleanup on logout

### Database Integration:
- Real authentication against `societies` table
- Token storage and management
- Login/logout timestamp tracking
- Regional data relationships

### API Endpoints:
- `POST /api/v1/auth/login` - User login
- `POST /api/v1/auth/logout` - User logout  
- `GET /api/v1/auth/me` - Get user info

The system is now ready for production use and meets all specified requirements for Feature A1 - Login and Logout as society.
