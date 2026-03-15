<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Request</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f4f7; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .wrapper { width: 100%; background-color: #f4f4f7; padding: 40px 0; }
        .container { max-width: 570px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .header { background-color: #059669; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 600; }
        .body { padding: 32px; }
        .body p { color: #51545e; font-size: 16px; line-height: 1.6; margin: 0 0 16px; }
        .detail-box { background-color: #f8f9fa; border-radius: 6px; padding: 20px; margin: 24px 0; }
        .notes-box { background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 4px; margin: 16px 0; }
        .notes-box p { margin: 0; color: #92400e; font-size: 14px; }
        .notes-label { font-weight: 600; margin-bottom: 4px; }
        .footer { padding: 24px 32px; text-align: center; border-top: 1px solid #eaeaec; }
        .footer p { color: #9ca3af; font-size: 13px; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>New Booking Request</h1>
            </div>
            <div class="body">
                <p>You have a new booking from <strong>{{ $client->name }}</strong>.</p>

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
                            <td style="padding: 8px 0; color: #6b7280; font-size: 14px; font-weight: 600; border-top: 1px solid #e9ecef;">Client Name</td>
                            <td style="padding: 8px 0; color: #1f2937; font-size: 14px; border-top: 1px solid #e9ecef;">{{ $client->name }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #6b7280; font-size: 14px; font-weight: 600; border-top: 1px solid #e9ecef;">Client Email</td>
                            <td style="padding: 8px 0; color: #1f2937; font-size: 14px; border-top: 1px solid #e9ecef;">{{ $client->email }}</td>
                        </tr>
                    </table>
                </div>

                @if($booking->client_notes)
                    <div class="notes-box">
                        <p class="notes-label">Client Notes:</p>
                        <p>{{ $booking->client_notes }}</p>
                    </div>
                @endif

                <div style="background-color: #fffbeb; border-radius: 6px; padding: 16px; margin: 16px 0; text-align: center;">
                    <p style="color: #92400e; font-size: 14px; font-weight: 600; margin: 0;">This booking is pending your confirmation.</p>
                    <p style="color: #92400e; font-size: 13px; margin: 8px 0 0;">Please review and confirm from the admin panel.</p>
                </div>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
