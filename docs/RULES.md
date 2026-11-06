## Project Overview
This is a Laravel-based CRM for Dust2Glow Cleaning Services.

## Development Guidelines

1. Always inspect existing code before making changes.
2. Understand existing business logic before modifying any feature.
3. Follow the existing coding conventions and architecture.
4. Try to follow SOLID principles for good coding practices.
5. Always separate business logic into dedicated Service classes and keep controllers clean.
6. Always isolate validation logic into dedicated Form Request classes.
7. Avoid unnecessary changes to unrelated modules.
8. Do not duplicate existing functionality.
9. Maintain backward compatibility.
10. Use database transactions when multiple related database operations must succeed or fail together.
11. Never modify production environment configurations.
12. Implement full CRUD functionality using jQuery and AJAX, following existing project patterns.
13. Display success and error messages using SweetAlert.
14. Use Yajra DataTables to display data, following the existing Bootstrap design.
15. Ensure responsive layouts across mobile, tablet, and desktop devices.
16. Write or update PEST tests for new or modified functionality. Try to test Service class methods and smaller individual methods directly through appropriate unit or feature tests to verify their behavior.



## Before modifying the quotation module:

2. Inspect related Models, Controllers, Services, Requests, Events, Listeners, Factory, Seeder and Views.
3. Understand database relationships.
4. Identify dependencies with other modules.
5. Explain the proposed implementation before coding.

## Important Rule

Never assume how an existing feature works.
Verify its actual implementation from the source code.
