<aside class="sidebar">

    <nav class="sidebar-nav">

        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('admin.submissions') }}"
           class="sidebar-link {{ request()->routeIs('admin.submissions') ? 'active' : '' }}">
            All Submissions
        </a>

        <a href="{{ route('admin.verification') }}"
           class="sidebar-link {{ request()->routeIs('admin.verification',  'admin.review', 'admin.review.*') ? 'active' : '' }}">
            Document Verification
        </a>

        <a href="{{ route('admin.student-documents') }}"
           class="sidebar-link {{ request()->routeIs('admin.student-documents') ? 'active' : '' }}">
            Student Documents
        </a>

        <a href="{{ route('admin.student-accounts') }}"
           class="sidebar-link {{ request()->routeIs('admin.student-accounts') ? 'active' : '' }}">
            Student Accounts
        </a>

        <a href="{{ route('admin.settings') }}"
           class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
            Settings
        </a>

    </nav>

    <div class="sidebar-user">

        <div class="sidebar-user-info">
            <span>Logged in as:</span>
            <strong>ADMIN</strong>
        </div>

        <div class="sidebar-profile-menu">
            <button
                type="button"
                class="sidebar-profile-toggle"
                aria-label="Open profile menu"
                aria-expanded="false"
                aria-controls="rofileLogoutMenu"
            >
                <img src="{{ asset('icons/profile.svg') }}" alt="" class="sidebar-profile-icon" id="profileToggle">
            </button>

            <div class="sidebar-logout-menu" id="profileLogoutMenu" hidden>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Log out</button>
                </form>
            </div>
        </div>

    </div>

</aside>

@push('scripts')
    @vite('resources/js/sidebar/admin-sidebar.js')
@endpush
