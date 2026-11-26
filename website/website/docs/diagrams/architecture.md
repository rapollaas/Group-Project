# Application architecture diagram

Logical view of the local Interior Studio application, based on the reviewed repository structure and runtime description. This is not a production deployment diagram.

\`\`\`mermaid
flowchart TB
    U["User browser<br/>Public / Client / Designer / Admin"]
    WEB["Apache web server<br/>Laragon on Windows"]
    APP["PHP application"]
    AUTH["Authentication and PHP sessions"]
    RBAC["Server-side role and ownership checks"]
    LOGIC["Page handlers and application logic<br/>Validation / CSRF / workflows"]
    DB[("MySQL / MariaDB<br/>Users, categories, projects,<br/>requests, updates, contact messages")]
    FS[("Uploads directory<br/>Project image files")]
    STATIC["Static assets<br/>CSS / JavaScript / public images"]

    U -->|"HTTP requests"| WEB
    WEB --> APP
    APP --> AUTH
    APP --> RBAC
    APP --> LOGIC
    LOGIC -->|"PDO database operations"| DB
    LOGIC -->|"Store / retrieve project images"| FS
    WEB --> STATIC
    WEB -->|"HTML responses"| U
\`\`\`

## Component notes
- The browser is untrusted. Form values, URL parameters, cookies and uploaded files must be validated or verified server-side.
- Apache serves the PHP application and static resources in the local Laragon environment.
- PHP performs application logic and is responsible for enforcing authentication, permissions, validation and safe database/file operations.
- MySQL/MariaDB stores application records. The browser should not connect directly to the database.
- The uploads directory stores project images and requires appropriate filesystem permissions and server configuration.

The diagram omits implementation details that have not been independently confirmed. Update it if the architecture changes.
