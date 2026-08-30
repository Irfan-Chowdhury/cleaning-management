# Cleaning Management System Documentation

This documentation describes the current implementation of the Cleaning Management System and will be updated module by module as features are completed.

## Project Context

- Project name: Cleaning Management System
- Project type: Cleaning service booking and management software
- Backend: PHP 8.2, Laravel 12
- Authentication packages: Laravel Fortify, Laravel Sanctum
- Frontend: Blade, HTML, CSS, JavaScript, jQuery, Bootstrap 4
- Database: MySQL

## Documented Modules

- [Service Catalog Management](modules/service-catalog.md)
- [Booking Service Flow](modules/booking-service.md)
- [Customer Management](modules/customer-management.md)
- [Settings Management](modules/settings.md)
- [Holiday Management](modules/holidays.md)

## Reference Documents

- [System Overview](architecture/system-overview.md)
- [Database Design](database/database-design.md)
- [API Documentation](api/api-documentation.md)
- [Technical Decisions](development/technical-decisions.md)

## Documentation Status

The documentation currently reflects the code present in the repository. Items from planning files such as `SRS.md` and `DATABASE.md` are marked as planned or unknown unless they are implemented in migrations, routes, controllers, models, views, or JavaScript.

## Important Unknowns

The following details should be confirmed before documenting later modules:

- Final user roles and permission boundaries.
- Whether service management is admin-only or temporarily public during development.
- Final booking persistence model and booking status lifecycle.
- Payment provider and checkout behavior.
- Availability rules, office shifts, holidays, and time-slot generation.
- Wallet, referral, review reward, and notification business rules.














