# Security requirements and user stories

## 1. Purpose
This document translates application risks into testable security requirements. Map each requirement to implementation changes, positive and negative tests, relevant SAST/DAST evidence and a final verdict.

The assignment requires at least two security user stories per group member. The six stories below provide that baseline for a three-person group. Replace Member A/B/C with the actual group members and record who personally executed each test.

## 2. OWASP guidance
Use the **OWASP Top 10:2025** as a risk-awareness framework, not as a substitute for application-specific threat modelling or testing.

| Category | Relevance to Interior Studio |
|---|---|
| A01:2025 Broken Access Control | Client record ownership, designer assignment checks, admin-only operations and direct-object-reference manipulation. |
| A02:2025 Security Misconfiguration | Directory listing, exposed configuration/database files, unsafe upload storage and insecure deployment defaults. |
| A04:2025 Cryptographic Failures | Password storage, session/cookie protection and protection of sensitive data in transit in a deployed environment. |
| A05:2025 Injection | SQL query handling and output contexts. Prepared statements help with SQL injection but do not prevent every injection or XSS issue. |
| A06:2025 Insecure Design | Abuse controls, request submission limits, safe workflows and resilience against automated misuse. |
| A07:2025 Authentication Failures | Account enumeration, login abuse, password policy and session lifecycle. |
| A08:2025 Software or Data Integrity Failures | Trust boundaries around uploaded files, dependencies and data-changing workflows. |
| A09:2025 Security Logging and Alerting Failures | Recording and investigating relevant authentication and privileged actions without logging secrets. |

### Recommended references
- [OWASP Top 10:2025](https://owasp.org/Top10/2025/)
- [A01:2025 Broken Access Control](https://owasp.org/Top10/2025/A01_2025-Broken_Access_Control/)
- [A02:2025 Security Misconfiguration](https://owasp.org/Top10/2025/A02_2025-Security_Misconfiguration/)
- [A06:2025 Insecure Design](https://owasp.org/Top10/2025/A06_2025-Insecure_Design/)
- [A07:2025 Authentication Failures](https://owasp.org/Top10/2025/A07_2025-Authentication_Failures/)
- [OWASP Authentication Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html)
- [OWASP Web Security Testing Guide](https://owasp.org/www-project-web-security-testing-guide/latest/)

## 3. Security requirements

| ID | Priority | Requirement | OWASP mapping |
|---|---|---|---|
| SR-01 | High | The web server must not expose directory listings, configuration files, database exports, source-control metadata, backups or other files not intended for public access. | A02 |
| SR-02 | High | Registration and authentication responses must not reveal whether an account exists. Login responses should be consistent in content and avoid avoidable timing differences between unknown accounts and incorrect passwords. | A07 |
| SR-03 | High | Repeated failed authentication attempts must be subject to an appropriate abuse-control mechanism, such as rate limiting, progressive delay or temporary throttling, without enabling trivial permanent account lockout attacks. | A07 |
| SR-04 | High | Design-request submission must validate input server-side, enforce CSRF protection and apply proportionate abuse controls against automated or repeated unwanted submissions. | A06 |
| SR-05 | Critical | Every protected action must enforce server-side role permissions and record ownership/assignment. A user must not access or modify another user's records by changing an identifier or submitting a forged request. | A01 |
| SR-06 | High | Image uploads must be restricted to intended formats and sizes, validated using file content as well as client-supplied metadata, stored using safe generated names, and prevented from executing as server-side code. | A02, A06, A08 |

Priorities are proposed assessment priorities. Justify or adjust them using likelihood, impact and test evidence.

## 4. Security user stories and acceptance criteria

### SEC-01 — Prevent directory and sensitive-file disclosure
**Assigned member:** Member A (replace with actual name)

**User story:** As the application owner, I want the web server to deny directory browsing and direct access to sensitive application files so that visitors cannot discover or retrieve internal configuration, backups or source files.

**Acceptance criteria**
- Directory requests do not return a browsable file index.
- Requests for configuration, SQL exports, backup files and source-control metadata are denied or not found.
- Unauthenticated users cannot access protected admin routes.
- Legitimate public pages and intended public assets remain accessible.

**Suggested tests**
1. Positive: load public Home and Portfolio pages and intended assets.
2. Negative: request relevant directory paths and likely sensitive filenames directly.
3. Negative: attempt an admin route without authentication.
4. Record response status/body and relevant server configuration. A guessed path returning 404 alone does not prove all sensitive files are protected.

### SEC-02 — Prevent account enumeration
**Assigned member:** Member A (replace with actual name)

**User story:** As a registered user, I want registration and login responses to avoid revealing whether an email address has an account so that an attacker cannot build a list of valid users.

**Acceptance criteria**
- Registration does not disclose that an email address is already registered.
- Login failures use a consistent user-facing response for unknown accounts and incorrect passwords.
- The login implementation performs comparable password-hash work for unknown and known accounts, where appropriate, to reduce timing differences.
- Successful registration and login continue to work.

**Suggested tests**
1. Compare registration using an existing email address with registration using a new test address.
2. Compare login using a nonexistent email with a known test email and incorrect password.
3. Repeat timing comparisons enough to reduce noise; compare distributions, not one request.
4. Verify valid login and registration. Use synthetic accounts only.

### SEC-03 — Limit authentication abuse
**Assigned member:** Member B (replace with actual name)

**User story:** As the application owner, I want repeated failed login attempts to be throttled so that automated password guessing is made more difficult without unnecessarily denying service to legitimate users.

**Acceptance criteria**
- Repeated failures from the same relevant source are rate-limited, delayed or otherwise controlled.
- The control cannot be trivially bypassed by refreshing the page or changing a form field.
- Responses do not disclose whether the account exists.
- Legitimate users can authenticate after the defined recovery period.
- Lockout/throttling does not create an easy permanent account-denial mechanism.

**Suggested tests**
1. Establish a normal successful-login baseline.
2. Submit a small controlled sequence of incorrect passwords against a synthetic account.
3. Record when throttling/delay begins and response behaviour.
4. Verify legitimate login recovery. Do not conduct high-volume brute-force testing.

### SEC-04 — Protect design-request submission from abuse
**Assigned member:** Member B (replace with actual name)

**User story:** As the application owner, I want design-request submissions validated and proportionately rate-limited so that automated or repeated requests cannot unnecessarily consume resources or pollute the database.

**Acceptance criteria**
- Required fields and field lengths are validated server-side.
- CSRF validation is enforced for state-changing requests.
- Repeated submissions are subject to an appropriate rate limit or abuse control.
- Invalid requests are rejected safely and do not create database records.
- Valid requests continue to be accepted.

**Suggested tests**
1. Submit a valid request and confirm the expected record is created.
2. Submit missing/oversized fields and an invalid or missing CSRF token; confirm safe rejection.
3. Make a small controlled number of repeat submissions and inspect whether the defined abuse control applies.
4. Check database state to confirm rejected requests were not stored.

### SEC-05 — Enforce role permissions and record ownership
**Assigned member:** Member C (replace with actual name)

**User story:** As a client or designer, I want the application to enforce my permissions and record ownership on the server so that I cannot view or modify another user's projects by changing request parameters.

**Acceptance criteria**
- Unauthenticated users cannot access protected pages or actions.
- Clients can access only their own requests and projects.
- Designers can access and update only projects assigned to them.
- Clients and designers cannot perform administrator-only operations.
- Administrators can perform authorised management tasks.
- Access checks occur on every relevant server-side action, not only in the user interface.

**Suggested tests**
1. Create two synthetic clients and relevant project records; attempt cross-client access by changing IDs in URLs/forms.
2. Sign in as a designer and attempt to access a project assigned to another designer.
3. Attempt admin actions as a client and designer.
4. Confirm authorised users can still access their own/assigned records and administrators can perform intended operations.
5. Capture allowed and denied outcomes.

### SEC-06 — Validate and safely store image uploads
**Assigned member:** Member C (replace with actual name)

**User story:** As an administrator, I want uploaded project images validated and stored safely so that a malicious or malformed upload cannot execute code, overwrite files or escape the intended upload area.

**Acceptance criteria**
- Only explicitly permitted image formats are accepted.
- Server-side validation uses file content/MIME detection rather than trusting browser-supplied filename or content type alone.
- The configured maximum size is enforced server-side.
- Stored filenames are generated safely and cannot contain traversal paths.
- Uploaded content cannot execute as PHP or other server-side code.
- Failed uploads return a safe error and do not leave partial or unsafe files behind.

**Suggested tests**
1. Positive: upload a valid small JPG, PNG or WebP image and confirm it is stored/displayed as intended.
2. Negative: attempt a disallowed extension, mismatched extension/content, oversized file and filename containing path-like characters.
3. Inspect the stored filename and location.
4. Attempt to access an uploaded test file and confirm it is served as data, not executed. Use harmless test content only.

## 5. Requirement-to-test tracking

Complete this table during the assessment. Add rows if a requirement needs multiple tests. Each group member must personally execute at least two substantive manual tests, as required by the assignment.

| Requirement/story | Test IDs | Positive test | Negative test | SAST/DAST evidence | Tester | Verdict |
|---|---|---|---|---|---|---|
| SR-01 / SEC-01 | T-01, T-02 | To complete | To complete | To complete | Member A | Not tested |
| SR-02 / SEC-02 | T-03, T-04 | To complete | To complete | To complete | Member A | Not tested |
| SR-03 / SEC-03 | T-05 | To complete | To complete | To complete | Member B | Not tested |
| SR-04 / SEC-04 | T-06, T-07 | To complete | To complete | To complete | Member B | Not tested |
| SR-05 / SEC-05 | T-08, T-09 | To complete | To complete | To complete | Member C | Not tested |
| SR-06 / SEC-06 | T-10, T-11 | To complete | To complete | To complete | Member C | Not tested |

Use **Verified**, **Failed** or **Inconclusive** only after execution. Until then, retain **Not tested**.

## 6. Test record template
For each manual, SAST or DAST test, record:
- Test ID and linked requirement/story.
- Tester and date/time.
- Application version, Git commit and environment.
- Tool and version, if applicable.
- Preconditions and test account/role.
- Steps and request details, with secrets removed.
- Expected result and actual result.
- Evidence filename/reference (screenshots, logs, scan output or HTTP response).
- Verdict: Verified, Failed or Inconclusive.
- Finding, impact and remediation required.

## 7. Interpretation
A requirement is not satisfied merely because a control is described in the README or appears in source code. Verify behaviour through independent tests. Manually triage SAST findings and reproduce/interpret DAST results in the context of this application. Record false positives and tool limitations.
