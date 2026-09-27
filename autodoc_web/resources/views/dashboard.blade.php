<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" type="image/png" href="{{ asset('images/autodoc-logo.png') }}">
    <title>AutoDoc</title>

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>

<!-- header -->
 <header class="top-header">

    <div class="brand">
        <img id="brandLogo" src="{{ asset('icons/autodoc-logo.svg') }}" alt="AUTODOC Logo">
        <span class="auto">AUTO</span><span class="doc">DOC</span>
    </div>

    <div class="header-actions">
        <button type="button" class="header-icon" aria-label="Notifications">
            <img id="notificationIcon" src="{{ asset('icons/notif-light.svg') }}" alt="Notifications">
        </button>

        <button type="button" id="themeToggle" class="header-icon" aria-label="Toggle theme">
            <img id="themeIcon" src="{{ asset('icons/dark-mode.svg') }}" alt="Toggle Theme">
        </button>
    </div>

</header>

<!-- side nav -->
 <aside class="side-nav">
    <nav class="navigation">
        <a href="{{ route('dashboard') }}" class="nav-link active">Dashboard</a>
        <a href="#" class="nav-link">Announcement</a>
        <div class="nav-section">
            <p class="nav-title">Status</p>
            <a href="#enlistment" class="nav-link">Enlistment</a>
            <a href="#evaluation" class="nav-link">Evaluation</a>
            <a href="#enrollment" class="nav-link">Enrollment</a>
        </div>
        <div class="nav-section bottom-links">
            <a href="#" class="nav-link">About Us</a>
            <a href="#" class="nav-link">Contact</a>
        </div>
    </nav>

    <div class="logged-user">
        <div class="logged-user-info">
            <span>Logged in as:</span>
            <strong>{{ strtoupper(Auth::user()->role) }}</strong>
        </div>
        <button type="button" class="profile-button" id="profileButton">
            <img src="{{ asset('icons/person.svg') }}" alt="Profile" class="person-icon">
        </button>
        <div class="profile-menu" id="profileMenu">
            <a href="{{ route('profile.edit') }}" class="profile-menu-item">
                Profile
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="profile-menu-item logout-button">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- main page -->
<main class="dashboard-content">
    <div class="dashboard-background"></div>
    <div class="dashboard-overlay"></div>
    <section class="dashboard-inner">
        <div class="student-card">
            <div class="student-header">
                <img src="{{ asset('icons/account.svg') }}" alt="Profile" class="account-icon">
                <div class="student-information">
                    <h1>{{ auth()->user()->name ?? 'Lacanlale, Kyla G.' }}</h1>
                    <span>2425 - 6011 BSIT 3A</span>
                </div>
            </div>

            <div class="status-container">
                <div class="status-card">
                    <h3>Enlistment</h3>
                    <p>Successfully uploaded</p>
                    <img src="{{ asset('icons/status-verified.svg') }}" alt="Verified" class="verified-icon">
                    <span>Verified</span>
                </div>
                <div class="status-line"></div>
                <div class="status-card">
                    <h3>Evaluation</h3>
                    <p>Successfully uploaded</p>
                    <img src="{{ asset('icons/status-verified.svg') }}" alt="Verified" class="verified-icon">
                    <span>Verified</span>
                </div>
                <div class="status-line"></div>
                <div class="status-card">
                    <h3>Enrollment</h3>
                    <p>Successfully uploaded</p>
                    <img src="{{ asset('icons/status-verified.svg') }}" alt="Verified" class="verified-icon">
                    <span>Verified</span>
                </div>
            </div>
        </div>

        <div class="documents-card">
            <div class="documents-header">
                <h2>Documents</h2>
                <div class="document-actions">
                    <div class="search-box">
                        <img id="searchIcon" src="{{ asset('icons/search-light.svg') }}" alt="Search" class="search-icon">
                        <input type="text" id="documentSearch" placeholder="Search">
                    </div>
                    <button type="button" class="download-button" aria-label="Download documents">
                        <img id="downloadIcon" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                    </button>
                </div>
            </div>

            <div class="documents-table-wrapper">
                <table class="documents-table">
                    <thead>
                        <tr>
                            <th>FILE NAME</th>
                            <th>Capacity</th>
                            <th>File Type</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="documentTableBody">
                        <tr>
                            <td>Evaluation Form</td>
                            <td>1.2 MB</td>
                            <td>PDF</td>
                            <td>06-21-26</td>
                            <td>In Progress</td>
                        </tr>
                        <tr>
                            <td>Prospectus</td>
                            <td>1 MB</td>
                            <td>PDF</td>
                            <td>06-21-26</td>
                            <td>In Progress</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- enlistment -->
    <section id="enlistment" class="requirements-page">
        <div class="requirements-section">
            <div class="requirement-upload-card">
                <h2>Enlistment<br>Requirements</h2>
                <button type="button" class="attach-button">Attach</button>
                <button type="button" class="upload-button">Upload</button>
                <p class="requirement-deadline">Until &lt;date&gt;</p>
            </div>

            <div class="requirements-card">
                <h2>Enlistment Requirements</h2>
                <ul>
                    <li>Requirement 1</li>
                    <li>Requirement 2</li>
                    <li>Requirement 3</li>
                </ul>
            </div>
        </div>

        <p class="requirement-note">
            Note: Please make sure that you upload the correct and accurate information.
            Any false information or information that involves using someone else's identity
            may result in legal action for identity theft.
        </p>
    </section>

    <!-- evaluation -->
     <section id="evaluation" class="requirements-page">
        <div class="requirements-section">
            <div class="requirement-upload-card">
                <h2>Evaluation<br>Requirements</h2>
                <img src="{{ asset('icons/lock.svg') }}" alt="Enrollment Locked" class="enrollment-lock">
                <p class="requirement-deadline">Opens on &lt;date&gt;</p>
            </div>


            <div class="requirements-card">
                <h2>Evaluation Requirements</h2>
                <ul>
                    <li>Requirement 1</li>
                    <li>Requirement 2</li>
                    <li>Requirement 3</li>
                </ul>
            </div>
        </div>

        <p class="requirement-note">
            Note: Please make sure that you upload the correct and accurate information.
            Any false information or information that involves using someone else's identity
            may result in legal action for identity theft.
        </p>
    </section>

    <!-- enrollment -->
     <section id="enrollment" class="requirements-page">
        <div class="requirements-section">
            <div class="requirement-upload-card">
                <h2>Enrollment<br>Requirements</h2>
                <img src="{{ asset('icons/lock.svg') }}" alt="Enrollment Locked" class="enrollment-lock">
                <p class="requirement-deadline">Opens on &lt;date&gt;</p>
            </div>

            <div class="requirements-card">
                <h2>Enrollment Requirements</h2>
                <ul>
                    <li>Requirement 1</li>
                    <li>Requirement 2</li>
                    <li>Requirement 3</li>
                </ul>
            </div>
        </div>

        <p class="requirement-note">
            Note: Please make sure that you upload the correct and accurate information.
            Any false information or information that involves using someone else's identity
            may result in legal action for identity theft.
        </p>
    </section>

    <!-- status overview -->
    <section id="status-overview" class="status-overview-page">
        <div class="status-overview-card">
            <h3>Enlistment</h3>
            <img src="{{ asset('icons/scan.svg') }}" alt="In Progress" class="status-overview-icon">
            <p>In Progress</p>
        </div>

        <div class="status-overview-card">
            <h3>Evaluation</h3>
            <img src="{{ asset('icons/lock.svg') }}" alt="Locked" class="status-overview-icon">
            <p>Opens on &lt;date&gt;</p>
        </div>

        <div class="status-overview-card">
            <h3>Enrollment</h3>
            <img src="{{ asset('icons/lock.svg') }}" alt="Locked" class="status-overview-icon">
            <p>Opens on &lt;date&gt;</p>
        </div>
    </section>
</main>

<script src="{{ asset('js/dashboard.js') }}"></script>

</body>
</html>