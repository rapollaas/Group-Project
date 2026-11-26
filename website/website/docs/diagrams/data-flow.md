# Data-flow diagram

High-level logical data flows and stores identified in the application. Confirm exact fields and endpoint behaviour against code and schema before treating this as implementation evidence.

\`\`\`mermaid
flowchart LR
    Visitor["E1: Public visitor"]
    Client["E2: Client"]
    Designer["E3: Designer"]
    Admin["E4: Administrator"]

    Public["P1: Public pages and portfolio"]
    Auth["P2: Registration, login and session"]
    Submit["P3: Design request and contact handling"]
    AdminOps["P4: Admin management"]
    DesignerOps["P5: Designer project workflow"]
    ClientOps["P6: Client project view"]

    Users[("D1: users")]
    Categories[("D2: categories")]
    Projects[("D3: projects")]
    Requests[("D4: design_requests")]
    Updates[("D5: project_updates")]
    Messages[("D6: contact_messages")]
    Uploads[("D7: uploads filesystem")]

    Visitor --> Public
    Visitor --> Submit
    Visitor --> Auth
    Client --> Auth
    Client --> Submit
    Client --> ClientOps
    Designer --> Auth
    Designer --> DesignerOps
    Admin --> Auth
    Admin --> AdminOps

    Public --> Projects
    Public --> Categories
    Auth <--> Users
    Submit --> Requests
    Submit --> Messages
    Submit --> Categories
    AdminOps <--> Users
    AdminOps <--> Categories
    AdminOps <--> Projects
    AdminOps <--> Requests
    AdminOps --> Uploads
    DesignerOps <--> Projects
    DesignerOps --> Updates
    DesignerOps --> Users
    ClientOps --> Projects
    ClientOps --> Requests
    ClientOps --> Updates
    ClientOps --> Uploads
\`\`\`

## Data protection points
- Authenticate and authorise each request before returning data from a protected store.
- Apply client ownership and designer assignment checks to every record lookup and update.
- Use parameterised database queries and validate input server-side.
- Escape untrusted output in the correct HTML context.
- Validate uploads and restrict access to stored files.
- Do not expose database configuration, schema exports or private records through public routes.
