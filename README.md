# Task Manager REST API

A RESTful Task Manager API built with **Laravel 13** and **PostgreSQL**, featuring token-based authentication, task management, filtering, sorting, pagination, and user-specific authorization.

## Features

- **Authentication:** User registration, login, logout, and authenticated user profile using Laravel Sanctum.
- **Task management:** Create, retrieve, update, and delete tasks.
- **Task validation:** Validate task titles, descriptions, statuses, and priorities.
- **Filtering:** Filter tasks by status, priority, and creation date.
- **Search:** Search tasks by title.
- **Sorting & pagination:** Sort task lists and control pagination.
- **Task summary:** Retrieve task statistics for the authenticated user.
- **Authorization:** Users can access and manage only their own tasks.
- **Rate limiting:** Protect authentication endpoints and API requests from excessive traffic.
- **API documentation:** Interactive API documentation generated with Scramble.
- **Consistent responses:** Structured JSON responses and validation error handling.

## Tech Stack

- **Framework:** Laravel 13
- **Language:** PHP
- **Database:** PostgreSQL
- **Authentication:** Laravel Sanctum
- **API Documentation:** Scramble
- **API Testing:** Postman
- **Dependency Management:** Composer

## Getting Started

### Prerequisites

- Git
- PHP `^8.3`
- Composer
- PostgreSQL

### Installation

**1. Clone the repository**

```bash
git clone https://github.com/Atia-Farha/Task-Manager-REST-API.git
cd Task-Manager-REST-API
```

**2. Set up the application**

```bash
composer setup
```

This installs Composer dependencies, creates the `.env` file if it doesn't exist, generates the application key, and runs database migrations.

**3. Configure the database**

Update the PostgreSQL credentials in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=task_manager
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

Ensure PostgreSQL is running and the database exists before setup runs its migrations.

### 4. Run database migrations

```bash
php artisan migrate
```

### 5. Start the development server

```bash
php artisan serve
```

The API will be available at:

`http://127.0.0.1:8000`

## API Documentation

This project uses [Scramble](https://scramble.dedoc.co/) to generate interactive API documentation.

After starting the development server, open:

**Documentation UI:** `http://127.0.0.1:8000/docs/api`

**OpenAPI JSON:** `http://127.0.0.1:8000/docs/api.json`

The documentation describes available endpoints, request parameters, response formats, validation rules, and bearer-token authentication.

## API Endpoints

All application endpoints are versioned under `/api/v1`.

### Authentication

| Method | Endpoint           | Description                               | Authentication |
| ------ | ------------------ | ----------------------------------------- | -------------- |
| POST   | `/api/v1/register` | Register a new user                       | Public         |
| POST   | `/api/v1/login`    | Log in and receive an access token        | Public         |
| POST   | `/api/v1/logout`   | Log out and revoke the current token      | Required       |
| GET    | `/api/v1/user`     | Retrieve the authenticated user's profile | Required       |

### Tasks

| Method | Endpoint               | Description                         |
| ------ | ---------------------- | ----------------------------------- |
| GET    | `/api/v1/tasks`        | List the authenticated user's tasks |
| POST   | `/api/v1/tasks`        | Create a task                       |
| GET    | `/api/v1/tasks/{task}` | Retrieve a task                     |
| PUT    | `/api/v1/tasks/{task}` | Update a task                       |
| PATCH  | `/api/v1/tasks/{task}` | Partially update a task             |
| DELETE | `/api/v1/tasks/{task}` | Delete a task                       |

All task endpoints require authentication.

### Task Summary

| Method | Endpoint                | Description                                         |
| ------ | ----------------------- | --------------------------------------------------- |
| GET    | `/api/v1/tasks/summary` | Retrieve task statistics for the authenticated user |

## Authentication

The API uses Laravel Sanctum personal access tokens.

1. Register an account or log in.
2. Copy the access token returned by the API.
3. For protected endpoints, select **Bearer Token** in your API client's authorization settings.
4. Paste the access token and send the request.

Include the token in the following header when making authenticated requests:

```http
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

## Task Data

A task supports the following fields:

| Field         | Description               | Allowed Values                        |
| ------------- | ------------------------- | ------------------------------------- |
| `title`       | Task title                | String, required when creating        |
| `description` | Optional task description | String or `null`                      |
| `status`      | Current task status       | `pending`, `in_progress`, `completed` |
| `priority`    | Task priority             | `low`, `medium`, `high`               |

Tasks are associated with their owners automatically. The API does not rely on client-supplied `user_id` values to assign ownership.

## Filtering, Search & Pagination

The task listing endpoint supports query parameters for filtering, searching, sorting, and pagination.

Example:

```http
GET /api/v1/tasks?status=pending&priority=high&search=Laravel&sort_by=created_at&sort_order=desc&per_page=10
```

Supported parameters include:

| Parameter      | Description                      |
| -------------- | -------------------------------- |
| `status`       | Filter by task status            |
| `priority`     | Filter by priority               |
| `search`       | Search task titles               |
| `created_from` | Filter by earliest creation date |
| `created_to`   | Filter by latest creation date   |
| `sort_by`      | Select a supported sorting field |
| `sort_order`   | Sort direction: `asc` or `desc`  |
| `per_page`     | Number of tasks per page         |

Invalid inputs are rejected through request validation.

## Error Handling

The API uses appropriate HTTP status codes, including:

- `200 OK` — Request completed successfully.
- `201 Created` — Resource created successfully.
- `204 No Content` — Resource deleted successfully, where applicable.
- `401 Unauthorized` — Authentication is missing or invalid.
- `403 Forbidden` — The authenticated user is not authorized to perform the action.
- `404 Not Found` — Requested resource does not exist.
- `422 Unprocessable Entity` — Request validation failed.
- `429 Too Many Requests` — Rate limit exceeded.

Error responses are returned in JSON where applicable.

## Security

- Bearer-token authentication with Laravel Sanctum.
- Password hashing through Laravel's authentication facilities.
- Ownership-based authorization through Laravel policies.
- Request validation before processing data.
- Rate limiting for authentication and API requests.
- Protected task queries scoped to the authenticated user.

## License

This project is intended for learning and educational purposes.
