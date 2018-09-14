<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
<div class="container" id="root">
    <form class="form-horizontal mt-5 row justify-content-md-center" id="ajaxForm" role="form" method="POST" action="/">
        <div class="col-md-8">
            <h2>URL Shortener</h2>
            <hr>
        </div>
        <div class="col-md-8 mb-3" id="result" style="display:none;">
            <h4 class="text-muted">Your short URL</h4>
            <div class="mt-3"><input type="text" id="shorturl" class="form-control form-control-lg" value="Empty" /></div>
            {{ csrf_field() }}
        </div>
        <div class="col-md-6">
            <input type="url" name="url" class="form-control form-control-lg" placeholder="Insert your URL" required autofocus>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-success btn-lg btn-block">GET</button>
        </div>
    </form>
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/jquery.form.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-notify.min.js') }}"></script>
<script>
$('#ajaxForm').ajaxForm({
    beforeSubmit: function() {
        $('#result').hide();
    },
    success: function(res) {
        if ( res.status === 0 ) {
            $.notify({
                title: "Sorry:",
                message: res.msg
            }, {type: 'danger', delay: 3000, z_index:9999});
            return false;
        } else {
            $('#result').fadeIn();
            $('#shorturl').val(res.url);
            $.notify({
                title: "Success:",
                message: res.msg
            }, {type: 'success', delay: 3000,});
        }
    }
});
</script>
</body>
</html>
