@php
    $student = auth()->user();
    $studentNameParts = preg_split('/\s+/', trim((string) $student->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $studentFirstName = trim((string) ($student->first_name ?: ($studentNameParts[0] ?? '')));
    $studentLastName = trim((string) ($student->last_name ?: (count($studentNameParts) > 1 ? end($studentNameParts) : '')));
    $studentSidebarName = trim($studentFirstName . ' ' . strtoupper($studentLastName)) ?: 'Student';
    $studentProfilePicture = auth()->user()->profile_picture_path
        ? \Illuminate\Support\Facades\Storage::disk('s3')->temporaryUrl(auth()->user()->profile_picture_path, now()->addMinutes(30))
        : asset('icons/profile.svg');
@endphp

<aside class="sidebar">

    <nav class="sidebar-nav">

        <a href="{{ route('student.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('student.documents') }}"
           class="sidebar-link {{ request()->routeIs('student.documents', 'student.documents.*') ? 'active' : '' }}">
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
            <strong>{{ $studentSidebarName }}</strong>
        </div>

        <div class="sidebar-profile-menu">
            <button
                type="button"
                class="sidebar-profile-toggle"
                aria-label="Open profile menu"
                aria-expanded="false"
                aria-controls="profileLogoutMenu"
            >
                <img src="{{ $studentProfilePicture }}" alt="" class="sidebar-profile-icon" id="profileToggle">
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
