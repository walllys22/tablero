<div class="app-body app-shell">
    @include('layouts.navigation')

    <div class="app-shell-body">
        @include('layouts.sidebar')

        <div class="app-content">
            @isset($header)
                <header class="page-header">
                    <div class="container-fluid py-4">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="app-main">
                {{ $slot }}
            </main>
        </div>
    </div>

    @include('layouts.bottom-navigation')
</div>
