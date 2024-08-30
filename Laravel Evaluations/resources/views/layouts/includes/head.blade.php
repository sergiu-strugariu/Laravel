<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=0.75, maximum-scale=1.0, user-scalable=no"/>
<meta name="robots" content="noindex, nofollow">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>EUCOM @if(env('APP_ENV') != 'live') ({{env('APP_ENV')}}) @endif</title>

<!-- fonts -->
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,400i,500,500i,700" rel="stylesheet">

<!-- Icons -->
<link rel="shortcut icon" href="{{ asset('assets/img/favicon/favicon.png') }}">
<link rel="stylesheet" href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css" integrity="sha256-aa0xaJgmK/X74WM224KMQeNQC2xYKwlAt08oZqjeF0E=" crossorigin="anonymous" />
<!-- Styles -->
<link href="{{ url('css/styles.min.css') }}" rel='stylesheet' type='text/css'>