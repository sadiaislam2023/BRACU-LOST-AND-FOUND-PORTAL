# BRACU Lost and Found Portal

A web-based lost-and-found management system developed as an independent group project by students of BRAC University.

## Project Overview

The **BRACU Lost and Found Portal** is a web application designed to make reporting lost and found belongings, submitting ownership claims, and managing item recovery more organized and convenient.

The platform provides a centralized system where users can report lost or found items, browse reported listings, submit claims with supporting evidence, and communicate through the application. It also includes administrative features to help manage item reports, claims, and related activities.

This project was independently developed as a personal group project to apply web development concepts to a practical problem. Although the project is designed around a BRACU-related use case, it is not an official university platform and is not affiliated with or endorsed by BRAC University.

## Key Features

### 1. User Authentication and Account Management

- User registration and login.
- Password hashing for credential storage.
- User roles for students, faculty members, and administrators.
- Session-based access to authenticated features.
- Role-based access to relevant parts of the application.

### 2. Lost and Found Item Management

- Submit reports for lost and found belongings.
- Provide item descriptions, categories, and location details.
- Include item images and relevant information where supported.
- Browse reported items.
- Review item details and reporting status.
- Support item review and approval through administrative functionality.

### 3. Item Claiming System

- Submit claims for items believed to belong to the claimant.
- Provide claim messages and contact information.
- Upload supporting proof documents.
- Store claim information for review.
- Track claim status through the application.

### 4. Messaging and Communication

- Send and receive messages through the platform.
- Support communication between users.
- Help users exchange relevant information during the item recovery process.

### 5. Notification System

- Display notifications related to platform activities.
- Store notification information for users.
- Support status awareness and communication throughout the process.

### 6. Administrative Functionality

- Provide administrative access to relevant management features.
- Review reported items and associated information.
- Manage item approval and reporting status.
- Support the handling of claims and other platform activities.

### 7. Feedback System

- Allow users to submit feedback.
- Collect suggestions about the platform.
- Support future improvements to usability and functionality.

## Technology Stack

| Technology | Purpose |
|---|---|
| PHP | Server-side application logic |
| MySQL / MariaDB | Relational database management |
| HTML5 | Web page structure |
| CSS3 | Styling and layout |
| JavaScript | Client-side interactivity |
| Bootstrap 5 | Responsive interface components |
| Apache | Local web server |
| XAMPP | Local development environment |
| phpMyAdmin | Database management |
| Git | Version control |
| GitHub | Source code hosting and collaboration |

## Project Structure

The main application is located in the `bracu_lost_and_found` directory.

```text
BRACU-LOST-AND-FOUND-PORTAL/
├── bracu_lost_and_found/
│   ├── db.php
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php
│   ├── claim_item.php
│   ├── navbar.php
│   ├── uploads/
│   └── ...
├── SQL/
│   └── Database SQL resources
└── README.md
```

This is a simplified representation of the project structure. Refer to the repository for the actual files and directories.

## Getting Started

Follow the instructions below to run the application on a local Windows computer.

### Prerequisites

Install the following tools:

- [XAMPP](https://www.apachefriends.org/) — provides Apache, PHP, and MySQL/MariaDB services.
- [Git](https://git-scm.com/) — for cloning the repository.
- [Visual Studio Code](https://code.visualstudio.com/) or another code editor.
- A modern web browser.

### Step 1: Clone the Repository

Open PowerShell or Command Prompt and run:

```bash
git clone https://github.com/sadiaislam2023/BRACU-LOST-AND-FOUND-PORTAL.git
```

### Step 2: Place the Project in the XAMPP Directory

Move or copy the cloned repository into your XAMPP `htdocs` directory.

For a typical Windows installation, the directory is:

```text
C:\xampp\htdocs\BRACU-LOST-AND-FOUND-PORTAL
```

The main application folder should then be available at:

```text
C:\xampp\htdocs\BRACU-LOST-AND-FOUND-PORTAL\bracu_lost_and_found
```

If XAMPP is installed in a different location, use the corresponding `htdocs` directory on your computer.

### Step 3: Start Apache and MySQL

1. Open the XAMPP Control Panel.
2. Start Apache.
3. Start MySQL.
4. Confirm that both services are running.

### Step 4: Create the Database

1. Open [phpMyAdmin](http://localhost/phpmyadmin/).
2. Create a database named `bracu_lost_and_found`.
3. Select the database.
4. Open the **Import** tab.
5. Choose the appropriate SQL file provided in the repository.
6. Click **Import** or **Go**, depending on the interface.
7. Confirm that the required tables have been created.

If the SQL file is stored in a different directory, select its actual location in the repository.

### Step 5: Configure the Database Connection

Open `bracu_lost_and_found/db.php` and configure the database connection for your local environment.

A typical default XAMPP configuration uses:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "bracu_lost_and_found";
```

These are example settings for a default local installation. Use your actual MySQL credentials if they differ, and preserve the existing connection logic in `db.php`.

### Step 6: Open the Application

Open your browser and visit:

```text
http://localhost/BRACU-LOST-AND-FOUND-PORTAL/bracu_lost_and_found/
```

If your folder name or installation path is different, adjust the URL accordingly.

### Step 7: Test the Features

Once the application opens, you can test the available functionality:

1. Register an account using an eligible email address.
2. Log in with the registered credentials.
3. Submit a lost or found item report.
4. Browse the available item listings.
5. Submit an ownership claim with the required information.
6. Test proof document uploads where available.
7. Review claim status and notifications.
8. Test messaging and feedback features.
9. Check administrative functionality using an authorized administrator account.

Access to certain features depends on the user's role and the application's implementation.

## Database Overview

The application uses a relational database named `bracu_lost_and_found`.

The main tables include:

| Table | Purpose |
|---|---|
| `users` | Stores user account information and roles |
| `reported_items` | Stores reported lost and found items |
| `item_claims` | Stores item ownership claims and related information |
| `messages` | Stores messages exchanged between users |
| `notifications` | Stores notification information |
| `feedback` | Stores submitted feedback |

The database schema can be inspected through phpMyAdmin. Use the SQL resources included in the repository to initialize the database.

## Security Considerations

Security is important because the application handles user accounts, contact information, claims, and uploaded documents.

Recommended practices include:

- Store passwords using secure password hashing.
- Use prepared statements for database operations involving user input.
- Validate user input and escape output appropriately.
- Enforce authentication and role-based authorization on the server.
- Validate uploaded files and restrict file types and sizes.
- Protect uploaded documents and sensitive user information from unauthorized access.
- Keep database credentials and other secrets out of public repositories.
- Avoid displaying sensitive information in error messages.

These are recommended security practices; the actual protection provided depends on the implementation in the source code.

## Future Improvements

Potential enhancements include:

- Advanced search and filtering for reported items.
- More detailed claim tracking and status updates.
- Improved notification workflows.
- Enhanced mobile responsiveness and accessibility.
- Additional upload validation and document protection.
- Expanded administrative reports and analytics.
- Deployment to a suitable PHP and MySQL hosting environment.

These are possible future enhancements rather than claims of existing functionality.

## Contributors

This project was developed collaboratively by the following group members:

- **Sadia Islam** — [GitHub Profile](https://github.com/sadiaislam2023)
- **Maliha Nigar** — [GitHub Profile](https://github.com/MALIHANIGAR)
- **Adrija Paul** — [GitHub Profile](https://github.com/adri-ja02)

## Repository

[View the BRACU Lost and Found Portal on GitHub](https://github.com/sadiaislam2023/BRACU-LOST-AND-FOUND-PORTAL)

## Disclaimer

This is an independent personal group project developed for educational and demonstration purposes. It is **not an official BRAC University project, service, or platform**, and it is not affiliated with, endorsed by, or maintained by BRAC University.

The name describes the project's intended use case and does not imply official university ownership or authorization.

## License

No open-source license is specified in this README. Unless a license is added to the repository, the project should not be assumed to be available for unrestricted reuse, modification, or redistribution.
