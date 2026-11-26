# Data-flow diagram

High-level logical data flows and stores identified in the application. Confirm exact fields and endpoint behaviour against code and schema before treating this as implementation evidence.



## Data protection points
- Authenticate and authorise each request before returning data from a protected store.
- Apply client ownership and designer assignment checks to every record lookup and update.
- Use parameterised database queries and validate input server-side.
- Escape untrusted output in the correct HTML context.
- Validate uploads and restrict access to stored files.
- Do not expose database configuration, schema exports or private records through public routes.
