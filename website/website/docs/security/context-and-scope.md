# Application security assessment: context and scope

## 1. Application context
Interior Studio is a PHP and MySQL web application for an interior-design studio. It combines public information and portfolio pages with authenticated project-management functions for clients, designers and administrators. It is an academic demonstration project, not an independently audited production service.

The reviewed project uses PHP 8+, HTML, CSS and vanilla JavaScript, with MySQL/MariaDB as its relational data store. The team's actual local development environment is Laragon on Windows (Apache, PHP and MySQL/MariaDB). The existing repository README describes XAMPP as an alternative setup; this assessment records the environment actually used.

## 2. Assessment objectives
This assessment applies a secure software development lifecycle (S-SDLC) approach to:
- identify users, assets, critical functions and trust boundaries;
- define testable security requirements and user stories;
- use OWASP guidance to inform threat identification;
- implement and review security controls across the project versions required by the assignment;
- independently verify controls through manual tests and suitable SAST and DAST tools;
- record evidence, limitations, failures and remediation decisions.

This document defines the assessment scope.

## 3. Application roles and permissions

| Role | Intended permissions and functions | Security considerations |
|---|---|---|
| Public visitor | View Home, About, Services, Portfolio and Project Details; submit a contact message; register or log in. | Must not access authenticated dashboards, private project information or administrative functions. |
| Client | Register and authenticate; submit design requests; view their own requests and projects; change password. | Must only access records belonging to their account. A client-supplied record ID must not override server-side ownership checks. |
| Designer | View assigned projects; update progress/status and add project notes. | Must not access projects assigned to another designer or perform administrator-only operations. |
| Administrator | Manage users/roles, projects, categories and design requests; assign clients/designers and manage publication. | Privileged functions must be restricted server-side to authorised administrators. |

These are intended role boundaries based on the project functionality and source reviewed. Verify every permission with direct requests and role-swapping tests; hiding a menu item is not access control.

## 4. Main application workflows

### 4.1 Public browsing and contact
A visitor browses public pages and the portfolio, which displays published projects and supports search/category filtering. The Contact page stores submitted messages in the contact_messages database table. The reviewed application does not currently provide an administrator inbox interface for these messages.

### 4.2 Client registration and design requests
A visitor creates a client account and signs in. A client submits a design request containing information such as title, description, category, budget and preferred date. The client can view their own requests and projects. The application must prevent another client from reading or changing these records by manipulating identifiers.

### 4.3 Administrator management
An administrator manages users/roles, categories, projects and design requests. Project management includes assigning a client and designer, setting status/progress and controlling publication. These operations have high impact because they can alter accounts, assignments and information visible to clients or the public.

### 4.4 Designer project updates
A designer views assigned projects and records progress/status changes and notes. The server must confirm that the authenticated designer is assigned to the selected project before permitting access or modification.

### 4.5 Client project monitoring
A client views project information associated with their account, including progress and updates. Data must be scoped to the authenticated client, rather than relying solely on an ID supplied in a URL or form.

## 5. Sensitive information and important assets

| Asset | Examples / location | Security importance |
|---|---|---|
| Account credentials | users table and password hashes | Hashes must be protected; passwords must never be stored or logged in plaintext. |
| Account and privilege data | Names, email addresses, roles and active status | Unauthorised changes could enable privilege escalation. |
| Session/authentication state | PHP sessions and authentication logic | Session theft or fixation could allow account impersonation. |
| Client requests | design_requests table | May contain private requirements, descriptions, budgets and dates. |
| Projects and assignments | projects table | Could expose client/project details or allow unauthorised changes to ownership, status or publication. |
| Project updates | project_updates table | Notes and progress history must be protected from unauthorised access or modification. |
| Contact submissions | contact_messages table | Contains names, email addresses and message content. |
| Uploaded project images | uploads directory | Must not permit executable content, unsafe paths or unintended access. |
| Database configuration | config/config.php | May contain database credentials and security configuration; must not be publicly retrievable. |
| Schema and sample data | database directory | Reveals data structure; sample accounts and credentials must not be reused in deployment. |


## 6. Critical application functions
1. Authentication and session management: registration, login, logout, password changes and session lifecycle.
2. Role-based authorisation: restrictions on admin, designer and client pages/actions.
3. Record ownership: client access to their own requests/projects and designer access to assigned projects.
4. Administrative management: user/role changes, project and category management, assignment and publication.
5. Design request submission: server-side validation, CSRF protection, persistence and abuse prevention.
6. Project updates: authorised status/progress changes and note creation.
7. File upload/retrieval: validation, naming, storage permissions and safe serving.
8. Database access: parameterised queries, least-privilege access and configuration protection.
9. Public contact handling: validation and safe storage/display of submitted messages.

Failures in authentication, authorisation or ownership can expose or alter multiple users' information. Upload and administrative functions also have elevated impact because they interact with the server filesystem or privileged records.

## 7. Application architecture and boundaries
At a logical level, the application consists of a user's browser, the local Apache web server provided by Laragon, PHP application pages and shared includes, a MySQL/MariaDB database, and the uploads directory used for project images.

The browser is untrusted: client-side validation and submitted identifiers can be manipulated. PHP is the enforcement point for authentication, authorisation and validation. The database and filesystem are separate storage boundaries and should not be directly exposed to visitors. See the architecture and trust-boundary diagrams in the diagrams folder.

## 8. Testing scope

### In scope
- Local application running under Laragon on Windows.
- Public pages, registration, login, logout and password change.
- Role-based access for client, designer and administrator accounts.
- Client ownership and designer assignment checks.
- Design request and contact form handling.
- Project management and project updates.
- Image upload validation and safe file handling.
- Relevant PHP source, shared includes, configuration and SQL schema.
- Manual security tests, source review, SAST and DAST against the local test instance.
- Synthetic accounts and non-sensitive test data.

### Out of scope
- Third-party websites, hosting providers or systems not owned/authorised by the group.
- Denial-of-service, destructive testing, high-volume request flooding or tests that could damage the host.
- Real customer data or uninvolved users' accounts.
- Claims about production infrastructure, hosting configuration, TLS termination, backups or monitoring that the group has not deployed and inspected.


## 9. Preliminary source-review observations

| ID | Preliminary observation | Security relevance | Follow-up verification |
|---|---|---|---|
| OBS-01 | File/path discovery was observed during preliminary browsing, but the available evidence does not establish that Apache directory listing is enabled or sensitive file contents are accessible. | OWASP A02:2025 Security Misconfiguration | Request likely directory paths and sensitive filenames directly; distinguish 403/404 from a directory index or file disclosure. Review effective Apache/Laragon configuration. |
| OBS-02 | Registration returns a specific response when an email address is already registered, which can disclose account existence. Login displays a generic failure message, but reviewed code only calls password_verify after finding an active account; this may create a timing difference for unknown accounts. | OWASP A07:2025 Authentication Failures | Compare registration responses for existing and unused addresses. For login, repeat controlled timing measurements for unknown users and known users with incorrect passwords; do not conclude from one request. |
| OBS-03 | The reviewed design-request endpoint includes CSRF validation and basic required-field checks. No request rate-limiting control was apparent in the endpoint reviewed. | OWASP A06:2025 Insecure Design | Conduct a small controlled repeat-submission test and inspect whether duplicate requests are accepted or throttled. Do not flood the service or claim denial-of-service without evidence. |
| OBS-04 | Reviewed upload code includes MIME checking and a 5 MB size limit. Further safeguards require verification. | OWASP A02:2025; A06:2025 | Test disallowed extensions, MIME/content mismatch, oversize files, unusual filenames and whether uploaded files can execute or escape intended paths. |


## 10. Assessment limitations
This is a bounded academic assessment of a local development instance and repository source. Findings may change as code evolves. A source observation does not establish exploitability, and a passing test does not prove the absence of other weaknesses.
