<!DOCTYPE html>
<html>

<head>
    <title>Bible Quote - JAO Ministry</title>
    <link rel="icon" type="image/x-icon" href="https://github.com/joshxb/jaom-angular/blob/main/src/assets/favicon.png?raw=true">
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        /* Inline CSS Styles */
        .btn-hover-effect:hover {
            opacity: 0.9;
            transition: all 0.3s;
            text-decoration: none;
        }

        body {
            width: 100%;
            height: 100%;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .container {
            width: 100%;
            height: 100%;
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
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(0, 0, 0, 0.071);
            border-radius: .25rem;
            padding: 20px 0;
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
            text-align: center;
            font-size: 15px;
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
            text-align: center;
        }

        .appreciation-message {
            margin-top: 15px;
            font-size: 16px;
            font-style: italic;
            text-align: center;
            color: #666;
        }

        .image-container img {
            margin: 10px;
            width: 30%;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            /* This is the box shadow property */
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }

        .footer .footer-text {
            background-color: rgba(18, 18, 18, 0.441);
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            font-style: italic;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .footer .footer-logo {
            display: block;
            margin-top: 10px;
            background-color: rgba(18, 18, 18, 0.441);
            padding: 5px;
            border-radius: 5px;
        }

        .footer .footer-logo img {
            width: 50px;
            border-radius: 50%;
            display: block;
            margin: 0 auto;
        }

        .footer .footer-message {
            color: #fff;
            background-color: rgba(18, 18, 18, 0.441);
            padding: 10px 5px;
            border-radius: 5px;
            margin-top: 5px;
        }

        .footer .footer-message span {
            color: #fff;
            font-size: 12px;
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
                            Greetings,
                            @if (isset($user['day']))
                            today is {{ $user['day'] }}! We are sharing an enlightening verse with quote for you.
                            @else
                            We are sharing an enlightening quote to all of us.
                            @endif
                            You may take time to read and have a great day to you! 😊
                            <br><div style="color:#666;margin-top:20px"><small>From: JAO Ministry</small></div>
                        </div>
                        <br>
                        <div class="prayer-offer">
                            <div class="prayer-offer-text">
                                <b>
                                    @isset($user['verse'])
                                    {{ $user['verse'] }}
                                    @else
                                    Verse 1
                                    @endisset
                                </b>

                            </div>
                        </div>
                        <div class="appreciation-message">
                            <p>
                                @isset($user['quote'])
                                "{{ $user['quote'] }}"
                                @else
                                "Hello world"
                                @endisset
                            </p>

                        </div>
                        <div class="image-container" style="text-align: center">
                            @isset($user['quote'])
                            @foreach ($user['randomImages'] as $image)
                            <img src="{{ $image }}" alt="Random Image">
                            @endforeach
                            @else
                            <img src="https://github.com/joshxb/joam-project-images/blob/main/337482704_1399156404249401_3014320667708945884_n.jpg?raw=true" alt="Image 1">
                            <img src="https://github.com/joshxb/joam-project-images/blob/main/337600841_1224255664889243_5242546871589928112_n.jpg?raw=true" alt="Image 2">
                            <img src="https://github.com/joshxb/joam-project-images/blob/main/337664640_998857938189489_2265375645851121647_n.jpg?raw=true" alt="Image 3">
                            @endisset
                        </div>
                        <div class="footer">
                            <div class="footer-message">
                                <span>&copy; 2023 JAO Ministry. All rights reserved.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
