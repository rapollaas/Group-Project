# Application use-case diagram

This diagram summarises intended role-based functionality. It describes expected use cases, not proof that every permission is correctly enforced.

\`\`\`mermaid
flowchart LR
    Visitor["Public visitor"]
    Client["Client"]
    Designer["Designer"]
    Admin["Administrator"]

    subgraph App["Interior Studio"]
      Browse["Browse public pages and portfolio"]
      Contact["Submit contact message"]
      Register["Register / log in"]
      Request["Submit design request"]
      Own["View own requests and projects"]
      Password["Change password"]
      Assigned["View assigned projects"]
      Update["Update progress / status"]
      Note["Add project update note"]
      Users["Manage users and roles"]
      Categories["Manage categories"]
      Projects["Create / edit / publish projects"]
      Requests["Manage design requests"]
      Assign["Assign client and designer"]
    end

    Visitor --> Browse
    Visitor --> Contact
    Visitor --> Register
    Client --> Register
    Client --> Request
    Client --> Own
    Client --> Password
    Client --> Browse
    Designer --> Register
    Designer --> Assigned
    Designer --> Update
    Designer --> Note
    Admin --> Register
    Admin --> Users
    Admin --> Categories
    Admin --> Projects
    Admin --> Requests
    Admin --> Assign
\`\`\`

## Permission verification
For each use case, test both the intended role and at least one role that should be denied. Verify direct HTTP requests to protected endpoints; interface navigation alone does not demonstrate authorisation.
