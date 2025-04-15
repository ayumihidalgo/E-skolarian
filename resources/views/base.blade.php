<!-- IN THIS SECTION, Since laravel tayo, need muna natin ma-understand kung paano gumamit ng Blade Directives like

    @ yield
    @ if
    @ foreach
    @ include

-->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> ESKOLARIAN </title>

</head>

<body>
    <main>
        @yield('content')
    </main>
</body>

</html>
