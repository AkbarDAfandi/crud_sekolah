<!DOCTYPE html>
<html>

<head>
    @include('includes.head')
</head>

<body id="page-top">
    <div id="wrapper">
        @include('includes.sidebar')
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <main>
                    @include('includes.nav')
                    @yield('content')
                </main>
            </div>
            <footer>
                @include('includes.foot')
            </footer>
                @include('includes.extras')
        </div>
    </div>
</body>

</html>
