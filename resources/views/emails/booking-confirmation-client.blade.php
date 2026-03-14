<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f4f7; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .wrapper { width: 100%; background-color: #f4f4f7; padding: 40px 0; }
        .container { max-width: 570px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .header { background-color: #4f46e5; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 600; }
        .body { padding: 32px; }
        .body p { color: #51545e; font-size: 16px; line-height: 1.6; margin: 0 0 16px; }
        .detail-box { background-color: #f8f9fa; border-radius: 6px; padding: 20px; margin: 24px 0; }
        .detail-row { display: flex; padding: 8px 0; border-bottom: 1px solid #e9ecef; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #6b7280; font-size: 14px; font-weight: 600; min-width: 120px; }
        .detail-value { color: #1f2937; font-size: 14px; }
        .footer { padding: 24px 32px; text-align: center; border-top: 1px solid #eaeaec; }
        .footer p { color: #9ca3af; font-size: 13px; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Booking Confirmed</h1>
            </div>
            <div class="body">
                <p>Hi {{ $booking->client->name }},</p>
                <p>Your session has been successfully booked! Here are the details:</p>

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
                            <td style="padding: 8px 0; color: #6b7280; font-size: 14px; font-weight: 600; border-top: 1px solid #e9ecef;">With</td>
                            <td style="padding: 8px 0; color: #1f2937; font-size: 14px; border-top: 1px solid #e9ecef;">{{ $admin->name }}</td>
                        </tr>
                    </table>
                </div>

                @if($booking->google_meet_link)
                    <div style="text-align: center; margin: 24px 0;">
                        <a href="{{ $booking->google_meet_link }}" style="display: inline-block; background-color: #4f46e5; color: #ffffff; padding: 14px 32px; border-radius: 6px; text-decoration: none; font-size: 16px; font-weight: 600;">Join Google Meet</a>
                    </div>
                @endif

                <p>We look forward to speaking with you. If you need to reschedule or cancel, please contact us as soon as possible.</p>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
