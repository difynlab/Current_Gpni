<p>Dear {{ $mail_data['user_name'] }},</p>

<p>Our technical support team has replied to your support request. Here are the details:</p>

<p><strong>Support Request Details:</strong></p>
<p><strong>Subject:</strong> {{ $mail_data['subject'] }}</p>
<p><strong>Your Message:</strong> {{ $mail_data['initial_message'] }}</p>

<p><strong>Support Reply:</strong></p>
<p>{{ $mail_data['reply_message'] }}</p>

<p>You can view the full conversation and continue the discussion by logging into your account.</p>

<p style="margin-top: 15px; margin-bottom: 3px;">Best regards,</p>

<p style="margin-top: 0px;">GPNi</p>
