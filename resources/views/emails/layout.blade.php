<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificação | FRP Rent</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f7;
            color: #51545e;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            width: 100%;
        }
        .email-wrapper {
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: #f4f4f7;
        }
        .email-content {
            width: 100%;
            margin: 0;
            padding: 0;
            max-width: 600px;
            margin: 0 auto;
        }
        .email-masthead {
            padding: 25px 0;
            text-align: center;
        }
        .email-masthead_name {
            font-size: 16px;
            font-weight: bold;
            color: #a8aaaf;
            text-decoration: none;
            text-shadow: 0 1px 0 white;
        }
        .email-body {
            width: 100%;
            margin: 0;
            padding: 0;
            border-top: 1px solid #e7eaec;
            border-bottom: 1px solid #e7eaec;
            background-color: #ffffff;
        }
        .email-body_inner {
            width: 570px;
            margin: 0 auto;
            padding: 35px;
        }
        .button {
            background-color: #3869d4;
            border-top: 10px solid #3869d4;
            border-right: 18px solid #3869d4;
            border-bottom: 10px solid #3869d4;
            border-left: 18px solid #3869d4;
            display: inline-block;
            color: #FFF;
            text-decoration: none;
            border-radius: 3px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
            -webkit-text-size-adjust: none;
            box-sizing: border-box;
        }
        .button--green {
            background-color: #22BC66;
            border-top: 10px solid #22BC66;
            border-right: 18px solid #22BC66;
            border-bottom: 10px solid #22BC66;
            border-left: 18px solid #22BC66;
        }
        .button--red {
            background-color: #FF6136;
            border-top: 10px solid #FF6136;
            border-right: 18px solid #FF6136;
            border-bottom: 10px solid #FF6136;
            border-left: 18px solid #FF6136;
        }
        h1 {
            margin-top: 0;
            color: #333333;
            font-size: 22px;
            font-weight: bold;
            text-align: left;
        }
        p {
            margin-top: 0;
            color: #51545e;
            font-size: 16px;
            line-height: 1.625;
            text-align: left;
        }
        .attributes {
            margin: 0 0 21px;
        }
        .attributes_content {
            background-color: #f4f4f7;
            padding: 16px;
        }
        .attributes_item {
            padding: 0;
        }
        .email-footer {
            width: 570px;
            margin: 0 auto;
            padding: 35px;
            text-align: center;
        }
        .email-footer p {
            color: #a8aaaf;
            font-size: 12px;
            text-align: center;
        }
        @media only screen and (max-width: 600px) {
            .email-body_inner,
            .email-footer {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <!-- Header -->
            <div class="email-masthead">
                <a href="{{ config('app.url') }}" class="email-masthead_name">
                   FRP Rent
                </a>
            </div>

            <!-- Body -->
            <div class="email-body">
                <div class="email-body_inner">
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} FRP Rent. Todos os direitos reservados.</p>
            </div>
        </div>
    </div>
</body>
</html>
