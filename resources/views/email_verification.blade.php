<!DOCTYPE html>
<html>

<head>
    <title>Email Verification - JAO Ministry</title>
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
            /* display: flex;
            flex-direction: column; */
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
            font-size: 20px;
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
            font-size: 1rem;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card mt-5">
                    <div class="card-header text-white">
                        Email Verification - JAO Ministry
                    </div>
                    <div class="card-body">
                        <p>
                            Hello <b>{{ $user['name'] }}<b>,
                        </p>
                        <p style="text-indent: 25px">
                            Welcome to JAO Ministry! We are excited to have you as part of our online group ministry. To
                            create a safe and secure environment for all our members, we kindly request you to complete
                            your registration by verifying your email address. Verifying your email ensures that we can
                            maintain the legibility of accounts and foster a positive experience for everyone involved.
                        </p>
                        <p>
                            Please take a moment to click the button below to verify your email address:
                        </p>
                        <p>
                            @if ($user['base'] == 'l')
                                <a style="cursor: pointer;color: #fff"
                                    href="{{ env('LOCAL_BASE_URL') }}/email-verification/{{ $user['email'] }}/{{$user['base']}}"
                                    class="btn btn-success btn-hover-effect">
                                    Verify Email Address
                                </a>
                            @elseif ($user['base'] == 'd')
                                <a style="cursor: pointer;color: #fff"
                                    href="{{ env('DEPLOYMENT_BASE_URL') }}/email-verification/{{ $user['email'] }}/{{$user['base']}}"
                                    class="btn btn-success btn-hover-effect">
                                    Verify Email Address
                                </a>
                            @endif
                        </p>
                        <p>
                            If you did not sign up for an account with JAO Ministry, you can safely ignore this email.
                        </p>
                        <p>
                            Thank you,
                        </p>
                        <p>
                            JAO Ministry
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
