<!DOCTYPE html>
<html>
    <head>
        <title>YSL Error.</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    
        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 42px;
                margin-bottom: 20px;
            }

            .details {
                font-size: 32px;
                margin-bottom: 40px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">We're Sorry. Something went wrong.</div>
                <div class="details">
	                <p>Details: {{ $exception->getMessage() }}</p>
					<p>Please go <a class="btn btn-info" href="{!! URL::previous() !!}">Back</a> or <a class="btn btn-info" href="/contact">Report Issue</a></p>
                </div>
            </div>
        </div>
    </body>
</html>
