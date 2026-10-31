## Project Overview
This is a Laravel-based CRM for Dust2Glow Cleaning Services.

## Development Guidelines

1. Always inspect existing code before making changes.
2. Understand existing business logic before modifying any feature.
3. Follow the existing coding conventions and architecture.
4. Avoid unnecessary changes to unrelated modules.
5. Do not duplicate existing functionality.
6. Maintain backward compatibility.
7. Form Request Validation: Always isolate validation into a FormRequest class.
8. Use database transactions when multiple related database operations must succeed or fail together.
9. Write or update PEST tests for new functionality.  Try to test Service Class methods/ tiny methods directly in unit/feature tests so that be confirm that the method are ok.
10. Never modify production environment configurations.
11. Try to follow SOLID Principle for good practice.
12. Service Class Isolation: Always separate business logic into a dedicated Service class and make the controller clean. 
13. Success and Error message will display by SweatAlert. 


## Before modifying the quotation module:

2. Inspect related Models, Controllers, Services, Requests, Events, Listeners, Factory, Seeder and Views.
3. Understand database relationships.
4. Identify dependencies with other modules.
5. Explain the proposed implementation before coding.

## Important Rule

Never assume how an existing feature works.
Verify its actual implementation from the source code.
