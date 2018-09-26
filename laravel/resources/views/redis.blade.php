<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel+Redis</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            table , td, th {
                border: 1px solid #595959;
                border-collapse: collapse;
            }
            td, th {
                padding: 3px;
                width: 30px;
                height: 25px;
            }
            th {
                background: #f0e6cc;
            }
            .even {
                background: #fbf8f0;
            }
            .odd {
                background: #fefcf9;
            }
        </style>
    </head>
    <body>
        <table>
            <tbody>
                <tr>
                    <td rowspan="4"></td>
                    <td rowspan="4"></td>
                    <td rowspan="2"></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td rowspan="2"></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
