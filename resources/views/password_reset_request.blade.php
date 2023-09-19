<!DOCTYPE html>
<html>

<head>
    <title>Password Reset Request - JAO Ministry</title>
    <link rel="icon" type="image/x-icon" href="https://github.com/joshxb/jaom-angular/blob/main/src/assets/favicon.png?raw=true">
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
                        Forgot Password Request
                    </div>
                    <div class="card-body">
                        <p>
                            @isset($user['email'])
                            Hello <b>{{ $user['email'] }}</b>,
                            @else
                            Hello,
                            @endisset
                        </p>

                        <p style="text-indent: 25px">
                            We have received your request, and you may now take the necessary steps to reset your password. Please click the button below to proceed.
                        </p>
                        <p>
                            @isset($user['base'])
                            @if ($user['base'] == 'l')
                            <a style="cursor: pointer;color: #fff" href="{{ env('F_LOCAL_BASE_URL') }}/forgot-pass-request-confirmation?email={{$user['email']}}&token={{$user['token']}}" class="btn btn-success btn-hover-effect">
                                Reset Password
                            </a>
                            @elseif ($user['base'] == 'd')
                            <a style="cursor: pointer;color: #fff" href="{{ env('F_DEPLOYMENT_BASE_URL') }}/forgot-pass-request-confirmation?email={{$user['email']}}&token={{$user['token']}}" class="btn btn-success btn-hover-effect">
                                Reset Password
                            </a>
                            @endif
                            @else
                            <a style="cursor: pointer;color: #fff" href="#" class="btn btn-success btn-hover-effect">
                                Reset Password
                            </a>
                            @endisset
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
