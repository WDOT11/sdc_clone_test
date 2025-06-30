 <h2>Hello {{ $userName }},</h2>

 <p>Welcome to <strong>Smart Tech Insurance</strong>! We’ve set up your account—just follow the link below to log in for the first time:</p>

 <p style="text-align:center">
     <a href="{{ $loginUrl }}"style="padding:10px 20px;background:#3490dc;color:#fff;text-decoration:none;border-radius:6px;">
         Log in to your dashboard
     </a>
 </p>

 <p><strong>Password:</strong> {{ $tempPassword }}</p>
 <p>For security, please change this password.</p>

 <p>If you didn’t request this account, please ignore this email.</p>

 <p>Cheers,<br>Smart Tech Insurance Team</p>
