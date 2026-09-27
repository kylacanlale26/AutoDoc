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

    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
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
        <div class="nav-section">
            <a href="#schedule" class="nav-link">Schedule</a>
            <a href="#forms" class="nav-link">Requirement<br>Forms</a>
            <a href="#submission" class="nav-link">Submission</a>
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
    <!-- summary -->
    <section id="submissions" class="submissions-page">
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-icon-circle">
                        <img class="theme-icon" data-light="{{ asset('icons/check-light.svg') }}" data-dark="{{ asset('icons/check-dark.svg') }}" src="{{ asset('icons/check-light.svg') }}" alt="Verified">
                    </span>
                    <span class="stat-label">Verified</span>
                </div>
                <div class="stat-card-bottom">50</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-icon-circle">
                        <img class="theme-icon" data-light="{{ asset('icons/x-light.svg') }}" data-dark="{{ asset('icons/x-dark.svg') }}" src="{{ asset('icons/x-light.svg') }}" alt="Returned">
                    </span>
                    <span class="stat-label">Returned</span>
                </div>
                <div class="stat-card-bottom">19</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-icon-circle">
                        <img class="theme-icon" data-light="{{ asset('icons/clock-light.svg') }}" data-dark="{{ asset('icons/clock-dark.svg') }}" src="{{ asset('icons/clock-light.svg') }}" alt="Flagged">
                    </span>
                    <span class="stat-label">Flagged</span>
                </div>
                <div class="stat-card-bottom">07</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-icon-circle">
                        <img class="theme-icon" data-light="{{ asset('icons/clock-light.svg') }}" data-dark="{{ asset('icons/clock-dark.svg') }}" src="{{ asset('icons/clock-light.svg') }}" alt="Pending">
                    </span>
                    <span class="stat-label">Pending</span>
                </div>
                <div class="stat-card-bottom">11</div>
            </div>
        </div>

        <div class="submissions-card">
            <div class="submissions-header">
                <h2>Submissions</h2>
            </div>

            <div class="submissions-table-wrapper">
                <table class="submissions-table">
                    <thead>
                        <tr>
                            <th>Student No.</th>
                            <th>Name</th>
                            <th>Course and Section</th>
                            <th>Submission Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="submissionsTableBody">
                        <tr>
                            <td>2425-0467</td>
                            <td>Nisperos, Joy M.</td>
                            <td>BSIT 3A</td>
                            <td>08/31/2026</td>
                            <td>Verified</td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/update-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2627-7894</td>
                            <td>Nanay Mo K. Albo</td>
                            <td>BSIT 1B</td>
                            <td>07/13/2026</td>
                            <td>Pending</td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/update-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2425-4928</td>
                            <td>Pwede Bang M. Nato</td>
                            <td>BSIT 3C</td>
                            <td>07/11/2026</td>
                            <td>Returned</td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/update-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2324-2231</td>
                            <td>Genshin I. Mpact</td>
                            <td>BSIT 4B</td>
                            <td>08/21/2026</td>
                            <td>Flagged</td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/update-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2526-9472</td>
                            <td>Pogi Mo S. Rodney</td>
                            <td>BSIT 2C</td>
                            <td>08/06/2026</td>
                            <td>Verified</td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/update-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- schedule -->
    <section id="schedule" class="schedule-page">
        <div class="schedule-row">

            <div class="schedule-card">
                <h2>Enlistment</h2>
                <p class="schedule-status" data-status-for="enlistment">Currently Open</p>
                <p class="schedule-date" data-date-for="enlistment">09/26/2026</p>
                <button type="button" class="update-schedule-button" data-title="Enlistment" data-target="enlistment">
                    Update Date and Time
                </button>
            </div>

            <div class="schedule-card">
                <h2>Evaluation</h2>
                <p class="schedule-status" data-status-for="evaluation">Will Open On</p>
                <p class="schedule-date" data-date-for="evaluation">10/16/2026</p>
                <button type="button" class="update-schedule-button" data-title="Evaluation" data-target="evaluation">
                    Update Date and Time
                </button>
            </div>

            <div class="schedule-card">
                <h2>Enrollment</h2>
                <p class="schedule-status" data-status-for="enrollment">Will Open On</p>
                <p class="schedule-date" data-date-for="enrollment">10/22/2026</p>
                <button type="button" class="update-schedule-button" data-title="Enrollment" data-target="enrollment">
                    Update Date and Time
                </button>
            </div>

        </div>
    </section>

    <!-- schedule modal -->
    <div class="modal-overlay" id="scheduleModalOverlay">
        <div class="modal-box">
            <h2 id="scheduleModalTitle">Enlistment</h2>

            <form id="scheduleModalForm">
                <label for="scheduleDateInput">Date</label>
                <input type="date" id="scheduleDateInput" required>

                <label for="scheduleTimeInput">Time</label>
                <input type="time" id="scheduleTimeInput" required>

                <div class="modal-actions">
                    <button type="button" class="modal-cancel-button" id="scheduleModalCancel">Cancel</button>
                    <button type="submit" class="update-schedule-button modal-save-button">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- requirements list -->
    <section id="requirements-list" class="requirements-list-page">

        <div class="requirements-panel">
            <h2 class="requirements-panel-title">Enlistment</h2>

            <div class="requirements-list-card">
                <div class="requirements-list-header">List of Requirements</div>

                <div class="requirement-row">
                    <span class="requirement-name">Prospectus/ Curriculum</span>
                    <div class="requirement-actions">
                        <button type="button" class="attach-pill-button">Attach</button>
                        <button type="button" class="upload-pill-button">Upload</button>
                    </div>
                </div>

                <div class="requirement-row">
                    <span class="requirement-name">Clearance</span>
                    <div class="requirement-actions">
                        <button type="button" class="attach-pill-button">Attach</button>
                        <button type="button" class="upload-pill-button">Upload</button>
                    </div>
                </div>

                <div class="requirement-row">
                    <span class="requirement-name">Advisement Slip</span>
                    <div class="requirement-actions">
                        <button type="button" class="attach-pill-button">Attach</button>
                        <button type="button" class="upload-pill-button">Upload</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="requirements-panel">
            <h2 class="requirements-panel-title">Evaluation</h2>

            <div class="requirements-list-card">
                <div class="requirements-list-header">List of Requirements</div>

                <div class="requirement-row">
                    <span class="requirement-name">Prospectus/ Curriculum</span>
                    <div class="requirement-actions">
                        <button type="button" class="attach-pill-button">Attach</button>
                        <button type="button" class="upload-pill-button">Upload</button>
                    </div>
                </div>

                <div class="requirement-row">
                    <span class="requirement-name">Student Profile</span>
                    <div class="requirement-actions">
                        <button type="button" class="attach-pill-button">Attach</button>
                        <button type="button" class="upload-pill-button">Upload</button>
                    </div>
                </div>

                <div class="requirement-row">
                    <span class="requirement-name">Advisement Slip</span>
                    <div class="requirement-actions">
                        <button type="button" class="attach-pill-button">Attach</button>
                        <button type="button" class="upload-pill-button">Upload</button>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- submissions list -->
    <section id="submissions-list" class="submissions-list-page">

        <div class="submissions-list-header">
            <h1>SUBMISSIONS</h1>
            <div class="submissions-list-actions">
                <button type="button" class="filter-button" aria-label="Filter">
                    <img class="theme-icon" data-light="{{ asset('icons/filter-light.svg') }}" data-dark="{{ asset('icons/filter-dark.svg') }}" src="{{ asset('icons/filter-light.svg') }}" alt="Filter">
                </button>
                <div class="submissions-search-box">
                    <img class="theme-icon" data-light="{{ asset('icons/search-light.svg') }}" data-dark="{{ asset('icons/search-dark.svg') }}" src="{{ asset('icons/search-light.svg') }}" alt="Search" class="search-icon">
                    <input type="text" id="submissionsListSearch" placeholder="Search">
                </div>
            </div>
        </div>

        <div class="submissions-list-panel">
            <div class="submissions-list-table-wrapper">
                <table class="submissions-list-table">
                    <thead>
                        <tr>
                            <th>Student No.</th>
                            <th>Name</th>
                            <th>Course and Section</th>
                            <th>Submission Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="submissionsListTableBody">
                        <tr>
                            <td>2425-1288</td>
                            <td>Abacial, John Arcie B.</td>
                            <td>BSIT 3A</td>
                            <td>08/31/2026</td>
                            <td><span class="status-badge status-verified">Verified</span></td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/updae-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2425-0538</td>
                            <td>Agustin, Kian Gabriel P.</td>
                            <td>BSIT 3A</td>
                            <td>07/13/2026</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/updae-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2425-0518</td>
                            <td>Alonzo, Joab P.</td>
                            <td>BSIT 3A</td>
                            <td>07/26/2026</td>
                            <td><span class="status-badge status-verified">Verified</span></td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/updae-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2324-0630</td>
                            <td>Basilio, Stephen Sedrick C.</td>
                            <td>BSIT 3A</td>
                            <td>08/13/2026</td>
                            <td><span class="status-badge status-flagged">Flagged</span></td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/updae-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2526-0433</td>
                            <td>Cruz, Alden Euan Raine B.</td>
                            <td>BSIT 3A</td>
                            <td>08/01/2026</td>
                            <td><span class="status-badge status-verified">Verified</span></td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/updae-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2425-1175</td>
                            <td>Escoto, Febbie Ann C.</td>
                            <td>BSIT 3A</td>
                            <td>01/12/2026</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/updae-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2324-2231</td>
                            <td>Genshin, Impac T.</td>
                            <td>BSIT 4B</td>
                            <td>08/21/2026</td>
                            <td><span class="status-badge status-flagged">Flagged</span></td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/updae-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2627-7894</td>
                            <td>Nanay, Mo Kalb O.</td>
                            <td>BSIT 1B</td>
                            <td>07/13/2026</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/updae-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2425-0467</td>
                            <td>Nisperos, Joy M.</td>
                            <td>BSIT 3A</td>
                            <td>08/31/2026</td>
                            <td><span class="status-badge status-verified">Verified</span></td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/updae-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2526-9472</td>
                            <td>Pogi, Mo Sir Rodne Y.</td>
                            <td>BSIT 2C</td>
                            <td>08/06/2026</td>
                            <td><span class="status-badge status-verified">Verified</span></td>
                            <td class="action-cell">
                                <button type="button" class="row-action" aria-label="Download">
                                    <img class="theme-icon" data-light="{{ asset('icons/download-light.svg') }}" data-dark="{{ asset('icons/download-dark.svg') }}" src="{{ asset('icons/download-light.svg') }}" alt="Download">
                                </button>
                                <button type="button" class="row-action" aria-label="Update">
                                    <img class="theme-icon" data-light="{{ asset('icons/update-light.svg') }}" data-dark="{{ asset('icons/updae-dark.svg') }}" src="{{ asset('icons/update-light.svg') }}" alt="Update">
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </section>
</main>

<script src="{{ asset('js/admin-dashboard.js') }}"></script>

</body>
</html>