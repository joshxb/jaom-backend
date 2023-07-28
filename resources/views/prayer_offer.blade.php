<!DOCTYPE html>
<html>

<head>
    <title>Prayer Offer - JAO Ministry</title>
    <link rel="icon" type="image/x-icon"
        href="https://github.com/joshxb/jaom-angular/blob/main/src/assets/favicon.png?raw=true">
    <style>
        /* Inline CSS Styles */
        .btn-hover-effect:hover {
            opacity: 0.9;
            transition: all 0.3s;
            text-decoration: none;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .container {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .col-md-8 {
            flex: 0 0 66.66667%;
            max-width: 66.66667%;
        }

        .offset-md-2 {
            margin-left: 16.66667%;
        }

        .card {
            position: relative;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(0, 0, 0, .125);
            border-radius: .25rem;
        }

        .mt-5 {
            margin-top: 3rem;
        }

        .card-header {
            padding: .75rem 1.25rem;
            margin-bottom: 0;
            background-color: rgba(16, 136, 152, 0.866);
            border-bottom: 1px solid rgba(0, 0, 0, .125);
            color: white;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }

        .card-body {
            flex: 1 1 auto;
            padding: 1.25rem;
        }

        .card-body p {
            margin-bottom: 1rem;
        }

        .btn {
            display: inline-block;
            font-weight: 400;
            color: #fff;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            border: 1px solid transparent;
            padding: .375rem .75rem;
            font-size: 16px;
            line-height: 1.5;
            border-radius: .25rem;
            transition: color .15s;
            text-decoration: none;
        }

        .btn-success {
            color: #fff;
            background-color: rgba(16, 136, 152, 0.747);
            border-color: rgba(16, 136, 152, 0.747);
        }

        .text-white {
            color: #fff;
        }

        /* Custom Inline CSS Styles */
        .greeting {
            text-align: left;
            font-size: 16px;
            font-weight: bold;
            color: #106898;
        }

        .offer-details {
            margin-top: 20px;
        }

        .offer-detail-item {
            margin-bottom: 10px;
            color: #106898;
            font-size: 16px;
        }

        .offer-detail-item span {
            font-weight: bold;
            color: #333;
        }

        .prayer-offer {
            margin-top: 20px;
            font-size: 16px;
        }

        .prayer-offer-text {
            /* text-indent: 25px; */
            line-height: 1.6;
            color: #333;
            font-size: 16px;
        }
        .appreciation-message {
            margin-top: 20px;
            font-size: 16px;
            font-style: italic;
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card mt-5">
                    <div class="card-body">
                        <div class="greeting">
                            <p>
                                @isset($user['name'])
                                    Hello <b>{{ $user['name'] }}</b>,
                                @else
                                    Hello,
                                @endisset
                            </p>

                        </div>
                        <div class="offer-details">
                            <div class="offer-detail-item">Your prayer offer was now successfully sent to the ministry
                                and the details are shown below:</div>
                            <div class="offer-detail-item">
                                <span>Name:</span>
                                @isset($user['name2'])
                                    {{ $user['name2'] }}
                                @endisset

                            </div>
                            <div class="offer-detail-item">
                                <span>Email:</span>
                                @if (isset($user['email']))
                                    {{ $user['email'] }}
                                @else
                                    Email not provided
                                @endif

                            </div>
                            <div class="offer-detail-item">
                                <span>Phone:</span>
                                @isset($user['phone'])
                                    {{ $user['phone'] }}
                                @endisset

                            </div>
                            <div class="offer-detail-item">
                                <span>Address:</span>
                                @isset($user['address'])
                                    {{ $user['address'] }}
                                @endisset

                            </div>
                        </div>
                        <div class="prayer-offer">
                            <div class="prayer-offer-text">
                                <b>Prayer Offer:</b>
                                @isset($user['offer'])
                                    {{ $user['offer'] }}
                                @endisset

                            </div>
                        </div>
                        <div class="appreciation-message">
                            <p>Thank you for offering your prayers and sharing your heart with us. Your contribution is
                                deeply appreciated. May God bless us abundantly!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
