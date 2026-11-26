# Trust-boundary diagram

The diagram identifies where data crosses trust levels or components. These boundaries guide threat modelling and test selection.

\`\`\`mermaid
flowchart TB
    subgraph BROWSER["Boundary 1 — User-controlled browser"]
      Public["Public visitor"]
      Client["Authenticated client"]
      Designer["Authenticated designer"]
      Admin["Authenticated administrator"]
      UI["Browser UI, forms, cookies,<br/>URL parameters and uploaded files"]
      Public --> UI
      Client --> UI
      Designer --> UI
      Admin --> UI
    end

    subgraph SERVER["Boundary 2 — Local web server / PHP application"]
      Apache["Apache / Laragon"]
      PHP["PHP request handlers"]
      Session["Authentication and session validation"]
      Access["Role, ownership and assignment checks"]
      Validation["Input, CSRF and upload validation"]
      Apache --> PHP
      PHP --> Session
      PHP --> Access
      PHP --> Validation
    end

    subgraph STORAGE["Boundary 3 — Server-side data stores"]
      DB[("MySQL / MariaDB")]
      FS[("Uploads filesystem")]
      Config["Application configuration"]
    end

    UI -->|"HTTP request: untrusted input"| Apache
    Validation -->|"Validated application operation"| DB
    Access -->|"Authorised data access"| DB
    Validation -->|"Validated file operation"| FS
    PHP -->|"Read protected settings"| Config
\`\`\`

## Trust-boundary assumptions
1. **Browser to server:** treat request data as attacker-controlled, including hidden fields, query parameters, cookies and file metadata.
2. **Authentication boundary:** a valid session identifies an authenticated user, but does not grant access to every record or function.
3. **Privilege boundary:** public, client, designer and administrator roles have different permissions. Enforce these server-side on every protected action.
4. **Application to database:** only the server-side application should access the database. Use parameterised queries and a suitably restricted database account.
5. **Application to filesystem:** validate file names, content and paths. Uploaded files must not become executable application code.
6. **Configuration boundary:** configuration and secrets must remain inaccessible to public HTTP requests and must not be committed as production secrets.

Use these boundaries to derive negative tests, especially unauthenticated access, cross-client access, cross-designer access, privilege escalation and unsafe file handling.
