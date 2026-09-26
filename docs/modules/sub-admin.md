# Sub-Admin Management Module Documentation

## Overview

The Sub-Admin Management module enables Super Administrators to create, view, update, and manage operational sub-administrator accounts (`/sub-admins`). Sub-admins are granted access to administrative features and dashboard operations.

---

## CRUD Operations & Visual User Guide

### 1. View Sub-Admins List (READ)
- **Route**: `GET /sub-admins`
- **Controller Action**: `App\Http\Controllers\Admin\SubAdminController@index`
- **Description**: Displays a responsive DataTables list of all registered sub-admin accounts. Includes live search, column sorting, pagination, and action buttons.
- **Visual Image Link**: [Sub-Admin Index Page](https://snipboard.io/oPw5jN.jpg)

![Sub-Admin Index Page](https://snipboard.io/oPw5jN.jpg)

---

### 2. Create New Sub-Admin (CREATE)
- **Route**: `POST /sub-admins`
- **Controller Action**: `App\Http\Controllers\Admin\SubAdminController@store`
- **Steps**:
  1. Click the **"+ Add Sub Admin"** button at the top-right of the page.
  2. Complete the modal form fields:
     - **First Name & Last Name** (Required)
     - **Email Address** (Required, unique)
     - **Phone Number** (Optional)
     - **Password & Confirmation** (Required)
     - **Profile Photo** (Optional file upload)
  3. Click **"Save Sub Admin"** to create the user account.
- **Visual Image Link**: [Create Sub-Admin Modal](https://snipboard.io/hDVP0U.jpg)

![Create Sub-Admin Modal](https://snipboard.io/hDVP0U.jpg)

---

### 3. Edit Sub-Admin Details (UPDATE)
- **Route**: `PUT /sub-admins/{sub_admin}`
- **Controller Action**: `App\Http\Controllers\Admin\SubAdminController@update`
- **Steps**:
  1. Locate the target sub-admin row in the list table.
  2. Click the **Edit** action button (pencil icon).
  3. Update profile fields or replace profile photo.
  4. *Note*: Leave password fields blank unless resetting credentials.
  5. Click **"Update Sub Admin"** to save modifications.
- **Visual Image Link**: [Edit Sub-Admin Modal](https://snipboard.io/YbtpW9.jpg)

![Edit Sub-Admin Modal](https://snipboard.io/YbtpW9.jpg)

---

### 4. Delete Sub-Admin (DELETE)
- **Route**: `DELETE /sub-admins/{sub_admin}`
- **Controller Action**: `App\Http\Controllers\Admin\SubAdminController@destroy`
- **Steps**:
  1. Click the red **Delete** action button (trash bin icon) on the target row.
  2. Confirm deletion in the SweetAlert modal pop-up (*"Are you sure you want to delete this Sub Admin?"*).
  3. Click **"Yes, Delete it!"** to finalize account removal.
- **Visual Image Link**: [Delete Sub-Admin Alert](https://snipboard.io/sQHMJ8.jpg)

![Delete Sub-Admin Alert](https://snipboard.io/sQHMJ8.jpg)
