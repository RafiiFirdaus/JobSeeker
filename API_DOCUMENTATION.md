# JobSeeker API Documentation

## API Authentication System Implementation ✅

The JobSeeker platform now includes a complete backend API system for authentication with token-based security using MD5 hash of ID card numbers.

## API Endpoints

### Base URL
```
[domain]/api/v1
```

## Authentication Endpoints

### 1. Society Login
**Endpoint:** `POST /api/v1/auth/login`

**Request:**
```json
{
    "id_card_number": "20210001",
    "password": "121212"
}
```

**Response - Success (200):**
```json
{
    "name": "Omar Gunawan",
    "born_date": "1990-04-18",
    "gender": "male",
    "address": "Jln. Baranang Siang No. 479, DKI Jakarta",
    "token": "e96aaafb6f2f76460b8cc93723bd030e",
    "regional": {
        "id": 1,
        "province": "DKI Jakarta",
        "district": "Central Jakarta"
    }
}
```

**Response - Failure (401):**
```json
{
    "message": "ID Card Number or Password incorrect"
}
```

### 2. Society Logout
**Endpoint:** `POST /api/v1/auth/logout`

**Request Parameters:**
- `token` (string) - Authentication token

**Example Request:**
```
POST /api/v1/auth/logout?token=e96aaafb6f2f76460b8cc93723bd030e
```

**Response - Success (200):**
```json
{
    "message": "Logout success"
}
```

**Response - Failure (401):**
```json
{
    "message": "Invalid token"
}
```

### 3. Get User Information
**Endpoint:** `GET /api/v1/auth/me`

**Request Parameters:**
- `token` (string) - Authentication token

**Example Request:**
```
GET /api/v1/auth/me?token=e96aaafb6f2f76460b8cc93723bd030e
```

**Response - Success (200):**
```json
{
    "name": "Omar Gunawan",
    "born_date": "1990-04-18",
    "gender": "male",
    "address": "Jln. Baranang Siang No. 479, DKI Jakarta",
    "token": "e96aaafb6f2f76460b8cc93723bd030e",
    "regional": {
        "id": 1,
        "province": "DKI Jakarta",
        "district": "Central Jakarta"
    }
}
```

## Test Accounts

The following test accounts are available for testing:

| ID Card Number | Password | Name | Gender |
|---|---|---|---|
| 20210001 | 121212 | Omar Gunawan | male |
| 20210002 | 121212 | Nilam Sinaga | female |
| 20210003 | 121212 | Rosman Lailasari | male |
| 20210004 | 121212 | Ifa Adriansyah | female |

## Web Application Integration

The web application login system has been updated to integrate with the same authentication system:

### Features Implemented:
- ✅ Real database authentication
- ✅ Password verification using Hash
- ✅ Token generation and storage
- ✅ Session management
- ✅ Proper error messages for incorrect credentials
- ✅ Logout with token cleanup
- ✅ Prevents access to login page when already logged in

### Testing the Web Application:

1. **Visit:** `http://127.0.0.1:8000/login`
2. **Use test credentials:**
   - ID Card Number: `20210001`
   - Password: `121212`
3. **Expected behavior:**
   - Success: Redirects to dashboard with welcome message
   - Failure: Shows "ID Card Number or Password incorrect"
   - Already logged in: Cannot access login page, redirects to dashboard

## API Testing Examples

### Using cURL

**Login:**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"id_card_number":"20210001","password":"121212"}'
```

**Logout:**
```bash
curl -X POST "http://127.0.0.1:8000/api/v1/auth/logout?token=YOUR_TOKEN_HERE"
```

**Get User Info:**
```bash
curl -X GET "http://127.0.0.1:8000/api/v1/auth/me?token=YOUR_TOKEN_HERE"
```

### Using Postman

1. **Login Request:**
   - Method: POST
   - URL: `http://127.0.0.1:8000/api/v1/auth/login`
   - Body (JSON):
     ```json
     {
         "id_card_number": "20210001",
         "password": "121212"
     }
     ```

2. **Copy the token from login response**

3. **Logout Request:**
   - Method: POST
   - URL: `http://127.0.0.1:8000/api/v1/auth/logout?token=YOUR_TOKEN`

## Security Features

- **Token-based Authentication:** MD5 hash tokens for API security
- **Password Hashing:** All passwords stored using Laravel's Hash facade
- **Session Management:** Proper session handling for web application
- **Input Validation:** Server-side validation for all requests
- **Error Handling:** Comprehensive error responses
- **Token Cleanup:** Tokens are cleared on logout

## Database Schema

### New Fields Added to `societies` Table:
- `auth_token` (string, nullable) - Stores authentication token
- `last_login` (timestamp, nullable) - Records last login time
- `last_logout` (timestamp, nullable) - Records last logout time

## Implementation Files

### API Controllers:
- `app/Http/Controllers/Api/AuthController.php` - API authentication logic

### Web Controllers:
- `app/Http/Controllers/AuthController.php` - Updated with real authentication

### Routes:
- `routes/api.php` - API routes definition

### Models:
- `app/Models/Society.php` - Updated with new authentication fields

### Middleware:
- `app/Http/Middleware/ApiAuthMiddleware.php` - API authentication middleware

### Migrations:
- `database/migrations/2025_06_28_141326_add_auth_fields_to_societies_table.php`

## Next Steps

The authentication system is now fully implemented and ready for:
1. Frontend integration (React, Vue, etc.)
2. Mobile app integration
3. Additional API endpoints for job management
4. Advanced security features (token expiration, refresh tokens)

All functionality meets the specified requirements for A1 - Login and Logout as society.
