# Lead Management Task (Laravel 12)

### 1. Fresh Laravel 12 Installation
- Project initialized using Laravel 12.
- Environment configured properly with a MySQL database.

### 2. Authentication — Login / Logout
- Utilizes Laravel's default UI bootstrap authentication scaffolding.
- The `auth` middleware is applied to the lead routes to prevent unauthorized access.
- You can log in and log out securely.

### 3. Lead Module — Full CRUD & AJAX Requirements
- **Database:** Uses a `leads` table with all the required columns (`enum` for status and source, foreign key `assigned_to` referencing users, text notes, etc.).
- **Controller:** `LeadController` acts as a resourceful controller handling all web routes, along with specialized methods for data fetching and status updates.
- **AJAX Listing:** The main `/leads` index page loads a container without data. Javascript (using Axios) calls `GET /leads/data` to fetch data asynchronously on page load, showing a Bootstrap spinner while loading.
- **AJAX Delete:** Delete actions are performed via AJAX using `DELETE /leads/{lead}`. Upon success, the lead is removed from the DOM and a Bootstrap Toast notification is shown without page reloading.

### 4. Search & Filter
- Search and filtering (Status, Source) are implemented in the `index.blade.php` view.
- Any change in the search input or dropdowns automatically triggers an AJAX request to `/leads/data` preserving the pagination state. 

### 5. Status Update (Quick Action)
- A quick-action dropdown is embedded inside the status badge on the index page.
- Clicking on a status updates the lead's status directly via an AJAX `PATCH /leads/{lead}/status` request without reloading the page.

### 6. Lead Assignment
- The lead creation and edit forms include a dropdown to assign the lead to a specific user.
- The assigned user's name is displayed correctly in both the list and the view pages.

### 7. Export to CSV
- The "Export to CSV" functionality respects the current search, status, and source filters.
- Data is exported via the `GET /leads/export` route which returns a downloadable CSV file.

### 8. Validation & UX
- Laravel Form Requests (`StoreLeadRequest` and `UpdateLeadRequest`) handle data validation.
- All forms use `@error` directives to display inline validation errors and `old()` helper to repopulate data on validation failure.

### 9. Authorization (Basic)
- Handled properly via Laravel Policies (`LeadPolicy`).
- As per instructions, "Only the user who created a lead (or an admin) should be able to delete it." This is done by checking if the authenticated user is an Admin (`is_admin` boolean column) OR if the lead was `created_by` them AND `assigned_to` them.
- Staff members can only view, edit, and export leads that are `assigned_to` them. Admin sees everything.
- No inline role checks are scattered in controllers.

### 10. Custom Helper Function
- Implemented `leadStatusBadge($status)` inside `app/Helpers/LeadHelper.php`.
- The helper returns standard Bootstrap badge HTML for the given status.
- The file is correctly autoloaded via `composer.json` and used consistently across `show.blade.php` and the AJAX JSON response logic.

## Testing Credentials

**Admin User:**
- Email: `admin@example.com`
- Password: `password`

**Staff Users:**
- Email: `staff1@example.com` through `staff5@example.com`
- Password: `password`

## Installation

1. Clone the repository.
2. Run `composer install`.
3. Copy `.env.example` to `.env` and configure your database.
4. Run `php artisan key:generate`.
5. Run `php artisan migrate:fresh --seed` (this will create the dummy users and leads).
6. Run `npm install` and `npm run build` for frontend assets.
7. Start the server with `php artisan serve`.
