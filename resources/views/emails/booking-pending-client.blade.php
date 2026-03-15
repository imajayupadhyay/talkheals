<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Received</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f4f7; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .wrapper { width: 100%; background-color: #f4f4f7; padding: 40px 0; }
        .container { max-width: 570px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .header { background-color: #f59e0b; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 600; }
        .body { padding: 32px; }
        .body p { color: #51545e; font-size: 16px; line-height: 1.6; margin: 0 0 16px; }
        .detail-box { background-color: #f8f9fa; border-radius: 6px; padding: 20px; margin: 24px 0; }
        .status-badge { display: inline-block; background-color: #fef3c7; color: #92400e; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .footer { padding: 24px 32px; text-align: center; border-top: 1px solid #eaeaec; }
        .footer p { color: #9ca3af; font-size: 13px; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Booking Received</h1>
            </div>
            <div class="body">
                <p>Hi {{ $booking->client->name }},</p>
                <p>Thank you for booking a session with us! We have received your request and it is currently being reviewed.</p>

                <div class="detail-box">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="padding: 8px 0; color: #6b7280; font-size: 14px; font-weight: 600; width: 130px;">Session Type</td>
                            <td style="padding: 8px 0; color: #1f2937; font-size: 14px;">{{ $booking->session_type }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #6b7280; font-size: 14px; font-weight: 600; border-top: 1px solid #e9ecef;">Date</td>
                            <td style="padding: 8px 0; color: #1f2937; font-size: 14px; border-top: 1px solid #e9ecef;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('l, F j, Y') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #6b7280; font-size: 14px; font-weight: 600; border-top: 1px solid #e9ecef;">Time</td>
                            <td style="padding: 8px 0; color: #1f2937; font-size: 14px; border-top: 1px solid #e9ecef;">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} — {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #6b7280; font-size: 14px; font-weight: 600; border-top: 1px solid #e9ecef;">Status</td>
                            <td style="padding: 8px 0; border-top: 1px solid #e9ecef;">
                                <span class="status-badge">Pending Confirmation</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <p>Your booking is currently <strong>pending confirmation</strong>. Once our team reviews and confirms your session, you will receive another email with the meeting details and a link to join.</p>

                <p>If you have any questions in the meantime, please don't hesitate to reach out to us.</p>

                <p style="margin-top: 24px;">Warm regards,<br><strong>{{ config('app.name') }} Team</strong></p>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
