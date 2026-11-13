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

    <div class="sidebar-user" onclick="toggleLogoutPopup()">

        <div class="sidebar-user-info">
            <span>Logged in as:</span>
            <strong>STUDENT</strong>
        </div>

        <div class="profile-popup">
            <img src="{{ asset('icons/profile.svg') }}" alt="Profile" class="sidebar-profile-icon" id="profileToggle">
            <div class="logout-popup" id="logoutPopup">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Sign Out</button>
                </form>
            </div>
    </div>

</aside>