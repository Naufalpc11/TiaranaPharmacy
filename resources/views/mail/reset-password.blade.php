<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; border-radius: 5px; }
        .content { padding: 20px 0; }
        .button { 
            display: inline-block; 
            background-color: #007bff; 
            color: white; 
            padding: 12px 30px; 
            text-decoration: none; 
            border-radius: 5px; 
            margin: 20px 0;
        }
        .footer { color: #666; font-size: 12px; border-top: 1px solid #ddd; padding-top: 20px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Reset Password</h2>
        </div>
        
        <div class="content">
            <p>Halo {{ $user->name }},</p>
            
            <p>Anda meminta untuk mereset password akun Anda. Klik tombol di bawah untuk melanjutkan:</p>
            
            <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            
            <p><strong>Link ini berlaku selama 24 jam.</strong></p>
            
            <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
        </div>
        
        <div class="footer">
            <p>Regards,<br>{{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
