<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Enquiry</title>
    <!--[if mso]><noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript><![endif]-->
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        table {
            border-collapse: collapse;
        }

        .wrapper {
            width: 100%;
            background-color: #f4f6f9;
            padding: 40px 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 48px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header p {
            color: rgba(255, 255, 255, 0.85);
            margin: 8px 0 0;
            font-size: 14px;
        }

        .badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 12px;
            margin-bottom: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .body {
            padding: 40px 48px;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 0 0 16px;
        }

        .field-row {
            display: flex;
            margin-bottom: 20px;
        }

        .field-card {
            background: #f8f9ff;
            border: 1px solid #e8ebf8;
            border-radius: 8px;
            padding: 16px 20px;
            width: 100%;
        }

        .field-label {
            font-size: 11px;
            font-weight: 600;
            color: #9fa6c0;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .field-value {
            font-size: 15px;
            color: #2d3748;
            font-weight: 500;
            word-break: break-word;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .message-card {
            background: #f8f9ff;
            border: 1px solid #e8ebf8;
            border-left: 4px solid #667eea;
            border-radius: 8px;
            padding: 20px 24px;
            margin-bottom: 20px;
        }

        .message-text {
            font-size: 15px;
            color: #2d3748;
            line-height: 1.7;
            white-space: pre-line;
            margin: 0;
        }

        .meta {
            background: #fafafa;
            border: 1px solid #efefef;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 0;
        }

        .meta-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #9fa6c0;
            margin-bottom: 6px;
        }

        .meta-row:last-child {
            margin-bottom: 0;
        }

        .meta-val {
            color: #6b7280;
            font-weight: 500;
        }

        .divider {
            height: 1px;
            background: #f0f0f0;
            margin: 28px 0;
        }

        .footer {
            background: #f8f9ff;
            padding: 24px 48px;
            text-align: center;
            border-top: 1px solid #eef0f8;
        }

        .footer p {
            margin: 0;
            font-size: 12px;
            color: #aab0c4;
            line-height: 1.6;
        }

        .footer a {
            color: #667eea;
            text-decoration: none;
        }

        @media (max-width: 600px) {

            .body,
            .footer,
            .header {
                padding-left: 24px !important;
                padding-right: 24px !important;
            }

            .grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">

            <!-- Header -->
            <div class="header">
                <div class="badge">📬 New Enquiry</div>
                <h1>You've received a message</h1>
                <p>{{ now()->format('l, d M Y \a\t h:i A') }}</p>
            </div>

            <!-- Body -->
            <div class="body">
                <p class="section-title">Contact Details</p>

                <!-- Name + Email side by side -->
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
                    <tr>
                        <td width="48%" style="vertical-align:top;">
                            <div class="field-card">
                                <div class="field-label">Full Name</div>
                                <div class="field-value">{{ e($enquiry['name']) }}</div>
                            </div>
                        </td>
                        <td width="4%"></td>
                        <td width="48%" style="vertical-align:top;">
                            <div class="field-card">
                                <div class="field-label">Email Address</div>
                                <div class="field-value">
                                    <a href="mailto:{{ e($enquiry['email']) }}"
                                        style="color:#667eea;text-decoration:none;">
                                        {{ e($enquiry['email']) }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Mobile + Subject side by side -->
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
                    <tr>
                        <td width="48%" style="vertical-align:top;">
                            <div class="field-card">
                                <div class="field-label">Mobile</div>
                                <div class="field-value">
                                    <a href="tel:{{ e($enquiry['mobile']) }}"
                                        style="color:#667eea;text-decoration:none;">
                                        {{ e($enquiry['mobile']) }}
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td width="4%"></td>
                        <td width="48%" style="vertical-align:top;">
                            <div class="field-card">
                                <div class="field-label">Subject</div>
                                <div class="field-value">{{ e($enquiry['subject']) }}</div>
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="divider"></div>
                <p class="section-title">Message</p>

                <!-- Message -->
                <div class="message-card">
                    <p class="message-text">{{ e($enquiry['message']) }}</p>
                </div>

                <div class="divider"></div>
                <p class="section-title">Technical Info</p>

                <!-- Meta -->
                <div class="meta">
                    <div class="meta-row">
                        🌐 <span>IP Address:</span>
                        <span class="meta-val">{{ e($enquiry['ip_address'] ?? '—') }}</span>
                    </div>
                    <div class="meta-row">
                        🔗 <span>Source Page:</span>
                        <span class="meta-val">{{ e($enquiry['page_url'] ?? '—') }}</span>
                    </div>
                    <div class="meta-row">
                        🕐 <span>Submitted At:</span>
                        <span class="meta-val">{{ now()->format('d M Y, h:i A') }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>
                    This email was sent automatically from your website enquiry form.<br>
                    To reply to the customer, use:
                    <a href="mailto:{{ e($enquiry['email']) }}">{{ e($enquiry['email']) }}</a>
                </p>
            </div>

        </div>
    </div>
</body>

</html>
