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
                text-align: center;
                padding: 3px;
                width: 200px;
                height: 50px;
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
                    <td rowspan="4">{{ $artist }}</td>
                    <td rowspan="4">{{ $title }}</td>
                    <td rowspan="2">{{ $songs[0][0] }}</td>
                    <td><iframe width="480" height="245" src={{ $info[0]['adress'] }} frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></td>
                </tr>
                <tr>
                    <td>{{ $info[0]['datetime'] }}</td>
                </tr>
                <tr>
                    <td rowspan="2">{{ $songs[0][1] }}</td>
                    <td><iframe width="480" height="245" src={{ $info[1]['adress'] }} frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></td>
                </tr>
                <tr>
                    <td>{{ $info[1]['datetime'] }}</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>