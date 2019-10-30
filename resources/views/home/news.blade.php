<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $news->title }}</title>
    <meta name="keywords" content="{{ $news->keywords }}">
    <meta name="description" content="{{ $news->desc }}">
</head>
<style>
    img {
        width: 100%;
        display:block;
        float:left
    }
    body {
        margin: 0;
    }
    p {
        margin-block-start: 0em;
        margin-block-end: 0em;
    }
</style>
<body>
    <div>
        {!! $news->content !!}
    </div>
</body>
</html>
