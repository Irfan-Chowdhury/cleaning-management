# Customer Profile Management

## 1. Feature Overview

Customer Profile Management allows authenticated customer users to view and update their profile details, contact information, gender, address, password, and profile picture avatar.

It aligns strictly with the `users` table schema, utilizes Intervention Image v3 for server-side photo cropping and optimization, and isolates validation into a Form Request and business logic into a Service Class.

---

## 2. Technical Architecture

### Architecture Pattern

```text
HTTP Request (POST /customer-profile)
  -> Auth & Gate Middleware
  -> CustomerProfileRequest (Validation & Authorization)
  -> ProfileController@update (HTTP Response Handling)
  -> CustomerProfileService::updateProfile() (Business Logic & Image Intervention)
  -> User Model & Database Update
```

### Components Summary

| Layer | Class / File | Description |
|---|---|---|
| **Route** | `routes/customer.php` | `GET|POST /customer-profile` |
| **Form Request** | `App\Http\Requests\CustomerProfileRequest` | Encapsulates input validation rules & authorization. |
| **Controller** | `App\Http\Controllers\Customer\ProfileController` | Delegates profile logic to service class and returns HTTP/JSON response. |
| **Service** | `App\Services\CustomerProfileService` | Handles profile updates, password hashing, and Intervention Image processing. |
| **Model** | `App\Models\User` | Stores profile attributes and exposes `photo_url` accessor. |
| **View** | `resources/views/pages/customer/profile.blade.php` | Blade view template with referral code tooltip and photo preview. |
| **Header** | `resources/views/components/header.blade.php` | Header component linking Profile and Logout forms dynamically. |
| **Unit Test** | `tests/Unit/CustomerProfileServiceTest.php` | Unit tests directly verifying `CustomerProfileService` logic. |

---

## 3. Data Schema Alignment (`users` table)

The profile form fields strictly reflect the `users` table columns:

- `first_name`: Required string (max 255)
- `last_name`: Nullable string (max 255)
- `email`: Required valid email address (unique in `users` table except self)
- `phone`: Nullable phone string (max 30)
- `gender`: Nullable string (`male`, `female`, `other`)
- `address`: Nullable text address
- `referral_code`: Read-only string field with instant tooltip copy action
- `password` & `password_confirmation`: Optional password update fields (min 8 chars)
- `photo`: Profile photo image file (processed via Intervention Image)

---

## 4. Intervention Image Processing

When a user uploads a new profile picture (`photo` file):

1. `CustomerProfileService::processAndStorePhoto()` receives the uploaded file.
2. `Intervention\Image\Laravel\Facades\Image::read($file)` loads the image.
3. `$image->cover(300, 300)` crops and scales the avatar to a clean 300x300 pixel aspect ratio.
4. The image is saved under `public/assets/images/user_photos/user-{ID}-{TIMESTAMP}-{HASH}.{ext}`.
5. If a previous local photo existed for the user, it is safely deleted from disk.
6. The model updates `$user->photo` with the stored relative path, accessible everywhere via `$user->photo_url`.

---

## 5. UI Features & Referral Copy Tooltip

- **Referral Code Copy**: Clicking the copy button copies the user's referral code to the system clipboard and triggers both a Bootstrap tooltip (`data-toggle="tooltip"`) displaying `"Copied!"` and an animated badge popup that automatically fades out after 1.8 seconds.
- **Real-Time Image Preview**: Selecting a file via the "Change Photo" button instantly updates the avatar preview element before form submission.
- **Header Profile & Logout Integration**:
  - The top header user dropdown displays the user's avatar (`$user->photo_url`) and name.
  - Clicking "Profile" navigates to `/customer-profile`.
  - Clicking "Logout" submits a hidden `@csrf` form to `POST /logout`.

---

## 6. Testing & Quality Assurance

Business logic is tested directly against `CustomerProfileService`:

```bash
# Run PHP syntax verification
php -l app/Http/Requests/CustomerProfileRequest.php
php -l app/Services/CustomerProfileService.php
php -l app/Http/Controllers/Customer/ProfileController.php
php -l tests/Unit/CustomerProfileServiceTest.php
```

All methods in `CustomerProfileService` are unit tested for:
1. Updating text and profile attributes.
2. Hashing updated passwords.
3. Intervention Image crop processing and file storage.
