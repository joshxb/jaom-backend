<!DOCTYPE html>
<html>

<head>
    <title>Send Donation - JAO Ministry</title>
    <link rel="icon" type="image/x-icon"
        href="https://github.com/joshxb/jaom-angular/blob/main/src/assets/favicon.png?raw=true">
    <style>
        /* Add a background color for the page */
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            box-sizing: border-box;
            background-color: #f5f5f5;
        }

        /* Center the card on the page */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 100px;
            max-width: 100%;
            width: 100%;
            /* Allow y-axis scrolling for card content */
            overflow-y: auto;
        }

        .card-header {
            background-color: #108898;
            color: white;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            padding: 10px 0;
            border-radius: 10px 10px 0 0;
        }

        .greeting {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #106898;
            margin-bottom: 10px;
        }

        .offer-detail-item {
            margin-bottom: 10px;
            color: #106898;
            font-size: 16px;
            text-indent: 0;
        }

        .offer-detail-item span {
            font-weight: bold;
            color: #333;
        }

        .prayer-offer-text {
            line-height: 1.6;
            color: #333;
            font-size: 16px;
        }

        .appreciation-message {
            margin-top: 20px;
            font-size: 16px;
            text-align: center;
            color: #666;
        }

        /* Add a recommended image for donation transactions */
        .donation-image {
            display: block;
            margin: 20px auto;
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #b0adc5;
        }

        .footer span {
            color: #b0adc5;
        }

        .footer a {
            color: #b0adc5;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Send Donation - JAO Ministry
            </div>
            <div style="padding: 20px 30px">
                <div class="greeting">
                    Hello good day,
                </div>
                <div class="offer-detail-item" style="text-indent: 0">The details of your donation transactions are shown
                    below:</div>
                <div class="offer-detail-item">
                    <span>Fullname:</span>
                    @if (empty($user['fullname']))
                        Joshua Algadipe
                    @else
                        {{ $user['fullname'] }}
                    @endif
                </div>
                <div class="offer-detail-item">
                    <span>Email:</span>
                    @if (empty($user['email']))
                        joshua@gmail.com
                    @else
                        {{ $user['email'] }}
                    @endif

                </div>
                <div class="offer-detail-item">
                    <span>Phone:</span>
                    @if (empty($user['phone']))
                        09458345678
                    @else
                        {{ $user['phone'] }}
                    @endif

                </div>
                <div class="offer-detail-item">
                    <span>Address:</span>
                    @if (empty($user['location']))
                        Vallehermoso, Negros Oriental
                    @else
                        {{ $user['location'] }}
                    @endif

                </div>
                <div class="prayer-offer-text">
                    <div class="offer-detail-item">
                        <b>Payment Method:</b>
                        @if (empty($user['payment_method']))
                            Gcash
                        @else
                            {{ $user['payment_method'] }}
                        @endif
                        <br>

                    </div>
                    <div class="offer-detail-item">
                        <b>Amount:</b>
                        @if (empty($user['amount']))
                            500
                        @else
                            {{ $user['amount'] }}
                        @endif
                        <br><br>

                    </div>
                    <div class="offer-detail-item" style="font-size: 14px">See attachment during transaction process.
                    </div>

                    <div class="appreciation-message">
                        Thank you for your generous donation to JAO Ministry. Your contribution helps us continue our
                        mission
                        and positively impacts countless lives. We deeply appreciate your support and partnership in
                        spreading
                        God's love and compassion. May God bless you abundantly!
                    </div>
                    <!-- Recommended donation image -->

                    <div class="footer">
                        <p>&copy; 2023 JAO Ministry. All rights reserved.</p>
                        <p><span>Contact Us:</span> jaomconnect.info@gmail.com</p>
                    </div>
                </div>
            </div>

        </div>
</body>

</html>
