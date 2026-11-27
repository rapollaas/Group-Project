<!--view recorded attempts>
SELECT *
FROM login_attempts
ORDER BY attempted_at DESC;

<!--count recent attepmpts for account>

SELECT COUNT(*) AS recent_attempts
FROM login_attempts
WHERE email = 'admin@interiorstudio.test'
AND ip_address = '::1'
AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE);

<!--Clear records>


DELETE FROM login_attempts;