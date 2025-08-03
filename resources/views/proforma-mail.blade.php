<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 1000px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        
        <p>Hello {{ $clients->client_name }},</p>

        <p>Please find your Proforma Invoice attached.</p>

        <p>If you have any questions, feel free to reach out to us. We're here to help!</p>

        <br/>
        <p>Best regards,</p>
       
        <p>PERSONAL SECRETARY </p>
        <p><strong>{{ 'Nduvini AutoWorks Limited' }}</strong></p>
    </div>
</body>
</html>
