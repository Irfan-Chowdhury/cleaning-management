Yes. You should **keep your existing `.env` database as `cleaning_management`** for normal development and configure a **separate testing database** for PEST.

The clean setup is:

```text
.env
    ↓
cleaning_management          ← normal Laravel app

.env.testing
    ↓
cleaning_management_test     ← PEST tests
```

### 1. Keep your `.env` unchanged

Your existing `.env` can remain:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cleaning_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

Don't change `DB_DATABASE` to the test database.

### 2. Create `.env.testing`

From your Laravel project:

```bash
cp .env .env.testing
```

Then edit:

```bash
nano .env.testing
```

Change only the database name:

```env
APP_ENV=testing

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cleaning_management_test
DB_USERNAME=root
DB_PASSWORD=your_password
```

So you have:

```text
.env
DB_DATABASE=cleaning_management

.env.testing
DB_DATABASE=cleaning_management_test
```

### 3. Create the MySQL test database

```bash
sudo mysql
```

Then:

```sql
CREATE DATABASE cleaning_management_test;
```

Verify:

```sql
SHOW DATABASES;
```

You should have:

```text
cleaning_management
cleaning_management_test
```

Then:

```sql
exit;
```

### 4. Check your `phpunit.xml`

This part is important.

Open:

```bash
nano phpunit.xml
```

Look for database settings such as:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

If you have these, they can override `.env.testing`.

For MySQL testing, you can use:

```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="cleaning_management_test"/>
```

Or, preferably, **don't define `DB_CONNECTION`/`DB_DATABASE` in `phpunit.xml`** and let `.env.testing` control them.

### 5. Clear config cache

Run:

```bash
php artisan config:clear
```

Then verify Laravel is seeing the testing environment:

```bash
php artisan config:show database
```

You want the default connection to be `mysql` and the database to be:

```text
cleaning_management_test
```

### 6. Run migrations on the test database

You generally **don't need** this:

```bash
php artisan migrate --database=testing
```

unless you have specifically defined a connection named `testing` in `config/database.php`.

Since you're using `.env.testing`, the simpler approach is:

```bash
php artisan migrate --env=testing
```

This should migrate:

```text
cleaning_management_test
```

**not**

```text
cleaning_management
```

### 7. Seed the test database

Then:

```bash
php artisan db:seed --env=testing
```

If your `DatabaseSeeder` calls `UserSeeder`, it will create your test users there.

You can verify:

```bash
php artisan tinker --env=testing
```

Then:

```php
DB::table('users')->where('email', 'customer@gmail.com')->first();
```

If it returns the user, your seeder is working.

Exit:

```text
exit
```

### 8. Run your PEST tests

Now:

```bash
php artisan test --filter="Booking"
```

Or the specific file:

```bash
php artisan test tests/Feature/BookingStep1FeatureTest.php
```

---

## One important point about `--database=testing`

The command you were given:

```bash
php artisan migrate --database=testing
```

means Laravel expects a **database connection named `testing`** in `config/database.php`.

For example:

```php
'connections' => [

    'mysql' => [
        // ...
    ],

    'testing' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
        // ...
    ],
],
```

But if you haven't created such a connection, **don't use `--database=testing`**.

For your current setup, I recommend:

```bash
# Normal app
.env → cleaning_management

# Tests
.env.testing → cleaning_management_test
```

and:

```bash
php artisan migrate --env=testing
php artisan db:seed --env=testing
php artisan test --filter="Booking"
```

### Even better for your PEST tests

If your test class uses:

```php
use RefreshDatabase;
```

you normally **shouldn't manually migrate every time**. Laravel will refresh the test database for the test run.

So your test workflow can simply become:

```bash
php artisan test --filter="Booking"
```

after the test database/configuration is correctly set up.
