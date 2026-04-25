<!DOCTYPE html>
<html>
<head>
    <title>{{ __('Redirecting to eSewa...') }}</title>
</head>
<body>
    <form id="esewaForm" action="{{ $formData['action'] }}" method="{{ $formData['method'] }}">
        @foreach($formData['fields'] as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <script>
        document.getElementById('esewaForm').submit();
    </script>
</body>
</html>