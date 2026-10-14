# Standing Development Rules

Follow these rules for all future development tasks in this project.
1. **SOLID Principles:** Follow SOLID principles where appropriate. Avoid unnecessary abstractions and overengineering.
2. **Form Request Validation:** Always use a dedicated Laravel FormRequest class to validate incoming request data. Include authorization checks when appropriate.
3. **Service Class Isolation:** Keep controllers clean and focused on HTTP responsibilities. Place business logic in dedicated Service classes.
4. **Reusable Business Logic:** Extract common business logic into small, focused, reusable methods within the appropriate Service class.
5. **Testing:** Add or update relevant unit and feature tests for Service methods and important business scenarios. Cover success cases, validation failures, and relevant edge cases.
6. **Notifications:** Use the project's existing SweetAlert implementation to display success and error messages. Follow the existing conventions for AJAX and non-AJAX requests.
7. **Existing Codebase:** Before implementing a feature, inspect the existing code, architecture, naming conventions, routes, database structure, and related implementations. Reuse existing functionality wherever appropriate.
8. **Scope Control:** Do not modify unrelated code, existing features, or behavior. Keep changes focused on the requested task.

# Business Logic: Store Quotation

## URL : /quotations/create

## 1. Store Method 

Implement the quotation store functionality using the following requirements:

- Validate incoming request data using a dedicated FormRequest class.
- Keep the controller clean and delegate business logic to the appropriate Service class.
- Use a database transaction to save the parent quotation and its associated dynamic line items.
- Handle exceptions appropriately. If any part of the operation fails, roll back the transaction and return an appropriate error response.
- Save or synchronize the quotation line items according to the existing database relationships and project conventions.
- Calculate line-item totals, subtotal, tax, and grand total on the server side. Never trust calculated totals submitted by the client.
- Validate quantities, prices, tax rates, and other financial inputs according to the application's business rules.
- Reuse existing models, relationships, helpers, and business logic wherever appropriate.

## 2. PDF Generation

Implement a `generatePdf` method or endpoint to generate and download the quotation PDF invoice.

Requirements:

- Use the existing DomPDF library and follow the project's current PDF-generation conventions.
- Inspect and use the existing implementation at `/my-bookings/{id}/download-pdf` as a reference.
- Include the relevant quotation details and line items in the generated PDF.
- Ensure the quotation data is retrieved and rendered correctly.
- Follow the existing authorization rules so users can only download quotations they are permitted to access.
- Return the generated PDF as a downloadable file.
- Avoid introducing a new PDF library or duplicating existing PDF-generation functionality.

## 3. AJAX Form Submission

Save the quotation using jQuery AJAX.

Requirements:

- Submit the form data asynchronously without a full-page form submission.
- While the request is processing, disable the submit button and display `Saving...`.
- Prevent duplicate submissions while the request is in progress.
- On success, display the success message using the existing SweetAlert implementation and redirect to `/quotations`.
- On validation or server errors, display an appropriate error message using the existing SweetAlert implementation.
- If the request fails, restore the submit button to its original state so the user can try again.
- Follow the project's existing AJAX response format and error-handling conventions.

# Implementation Guidelines

- Inspect the relevant existing files before making changes.
- Reuse the project's existing architecture and conventions.
- Do not create unnecessary classes, methods, routes, or database changes.
- Do not modify unrelated features.
- After implementation, summarize the files changed and explain the main changes.
- Report the tests performed and their results.
