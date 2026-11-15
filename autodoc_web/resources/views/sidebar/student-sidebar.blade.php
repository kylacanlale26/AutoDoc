<aside class="sidebar">

    <nav class="sidebar-nav">

        <a href="{{ route('student.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('student.documents') }}"
           class="sidebar-link {{ request()->routeIs('student.documents') ? 'active' : '' }}">
            My Documents
        </a>

        <a href="{{ route('student.upload') }}"
           class="sidebar-link {{ request()->routeIs('student.upload') ? 'active' : '' }}">
            Upload Documents
        </a>

        <a href="{{ route('student.notifications') }}"
           class="sidebar-link {{ request()->routeIs('student.notifications') ? 'active' : '' }}">
            Notifications
        </a>

        <a href="{{ route('student.account') }}"
           class="sidebar-link {{ request()->routeIs('student.account') ? 'active' : '' }}">
            My Account
        </a>

    </nav>

    <div class="sidebar-user">

        <div class="sidebar-user-info">
            <span>Logged in as:</span>
            <strong>STUDENT</strong>
        </div>

        <div class="sidebar-profile-menu">
            <button
                type="button"
                class="sidebar-profile-toggle"
                aria-label="Open profile menu"
                aria-expanded="false"
                aria-controls="profileLogoutMenu"
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
    @vite('resources/js/sidebar/student-sidebar.js')
@endpush
