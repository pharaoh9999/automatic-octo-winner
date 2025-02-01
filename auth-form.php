<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Authorization</title>
    <style>
        .auth-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            max-width: 400px;
            width: 90%;
        }

        .auth-input {
            width: 100%;
            padding: 12px;
            margin: 1rem 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .auth-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .auth-error {
            color: #dc3545;
            margin-top: 1rem;
            display: none;
        }
    </style>
</head>

<body>
    <!-- Authorization Modal -->
    <div class="auth-modal">
        <h2 style="margin-bottom: 1.5rem; text-align: center;">Admin Authorization Required</h2>
        <form id="authForm">
            <input type="number"
                class="auth-input"
                id="otp"
                name="otp"
                placeholder="Enter 6-digit code"
                required
                pattern="\d{6}">

            <button type="submit" class="auth-button">Verify</button>
            <div class="auth-error" id="errorMessage"></div>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script>
        document.getElementById('authForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.innerHTML = '';

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `otp=${encodeURIComponent(document.getElementById('otp').value)}`
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    errorDiv.innerHTML = `
                <strong>Error:</strong> ${result.message}<br>
                ${result.debug ? JSON.stringify(result.debug, null, 2) : ''}
            `;
                }
            } catch (error) {
                errorDiv.innerHTML = `
            <strong>Fatal Error:</strong> ${error}<br>
            Check console for details
        `;
                console.error('Debug details:', error);
            }
        });
    </script>
</body>

</html>