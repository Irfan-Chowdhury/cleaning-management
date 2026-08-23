# API Documentation

## Current API Surface

The project has one Laravel API route and one web route that returns JSON for the booking questionnaire.

## Authenticated User

- Endpoint: `/api/user`
- Method: `GET`
- Authentication: Sanctum, `auth:sanctum`
- Purpose: Return the authenticated user associated with the API token/session.

### Example Response

The response shape is Laravel's serialized authenticated user model.

```json
{
  "id": 1,
  "name": "Test User",
  "email": "test@example.com"
}
```

## Service Questionnaire

- Endpoint: `/booking-service/questionnaire/{service}`
- Method: `GET`
- Authentication: none currently enforced
- Purpose: Return service-specific questionnaire data for the booking Step 1 UI.

### Parameters

- `service`: service id resolved through Laravel route model binding.

### Example Response

```json
{
  "service": {
    "id": 1,
    "name": "Commercial Cleaning"
  },
  "questions": [
    {
      "id": 1,
      "title": "What type of commercial property is it?",
      "field_type": "select",
      "required": false,
      "sort_order": 1,
      "options": [
        {
          "id": 1,
          "label": "Office"
        }
      ]
    }
  ]
}
```

### Possible Error Responses

- `404 Not Found` if the service id does not exist.

## Planned APIs

No dedicated booking, payment, referral, wallet, availability, or admin APIs are currently implemented. Add them here as those modules are built.
