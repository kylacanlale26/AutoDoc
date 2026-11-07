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
           class="sidebar-link {{ request()->routeIs('admin.verification') ? 'active' : '' }}">
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

        <img src="{{ asset('icons/profile.svg') }}" alt="Profile" class="sidebar-profile-icon">

    </div>

</aside>