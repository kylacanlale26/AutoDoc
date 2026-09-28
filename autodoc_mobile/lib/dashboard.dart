import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:google_fonts/google_fonts.dart';

enum _DashboardSection { dashboard, status, enlistment, evaluation, enrollment }

class DashboardPage extends StatefulWidget {
  const DashboardPage({
    super.key,
    required this.email,
    this.isDarkMode = false,
  });

  final String email;
  final bool isDarkMode;

  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {
  static const _brandRed = Color(0xFF8F1D1D);
  static const _brightRed = Color(0xFFB12A2A);
  static const List<List<String>> _documentRows = [
    ['Evaluation Form', '1.2 MB', 'PDF', '06-21-26', 'In Progress'],
    ['Prospectus', '1 MB', 'PDF', '06-21-26', 'In Progress'],
  ];

  late bool _isDarkMode;
  bool _isMenuOpen = false;
  _DashboardSection _section = _DashboardSection.dashboard;

  Color get _foreground => _isDarkMode ? Colors.white : const Color(0xFF111111);
  Color get _surface => _isDarkMode ? const Color(0xFF1B1B1B) : Colors.white;
  Color get _background =>
      _isDarkMode ? const Color(0xFF121212) : const Color(0xFFF3F5F7);
  Color get _border =>
      _isDarkMode ? const Color(0xFF666666) : const Color(0xFF202020);

  @override
  void initState() {
    super.initState();
    _isDarkMode = widget.isDarkMode;
  }

  void _selectSection(_DashboardSection section) {
    setState(() {
      _section = section;
      _isMenuOpen = false;
    });
  }

  String _sectionLabel(_DashboardSection section) {
    return switch (section) {
      _DashboardSection.dashboard => 'Dashboard',
      _DashboardSection.status => 'Status',
      _DashboardSection.enlistment => 'Enlistment',
      _DashboardSection.evaluation => 'Evaluation',
      _DashboardSection.enrollment => 'Enrollment',
    };
  }

  @override
  Widget build(BuildContext context) {
    final isWide = MediaQuery.sizeOf(context).width >= 760;
    final showDashboardBackground =
        _section == _DashboardSection.dashboard && (isWide || !_isMenuOpen);

    return Scaffold(
      backgroundColor: _background,
      body: SafeArea(
        child: Column(
          children: [
            _buildHeader(isWide),
            Expanded(
              child: Stack(
                fit: StackFit.expand,
                children: [
                  if (showDashboardBackground)
                    Image.asset(
                      'assets/images/mcc-bg-main.png',
                      fit: BoxFit.cover,
                      alignment: Alignment.center,
                    ),
                  if (showDashboardBackground)
                    ColoredBox(
                      color: _isDarkMode
                          ? Colors.black.withValues(alpha: 0.58)
                          : Colors.white.withValues(alpha: 0.86),
                    ),
                  if (!isWide && _isMenuOpen)
                    _buildMobileMenu()
                  else
                    _buildCurrentPage(),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader(bool isWide) {
    Widget desktopItem(String label, {_DashboardSection? section}) {
      final text = Text(
        label,
        style: GoogleFonts.raleway(
          color: _foreground,
          fontSize: 13,
          fontWeight: FontWeight.w600,
        ),
      );

      if (section == null) {
        return Padding(
          padding: const EdgeInsets.symmetric(horizontal: 8),
          child: text,
        );
      }

      return TextButton(
        onPressed: () => _selectSection(section),
        style: TextButton.styleFrom(
          padding: const EdgeInsets.symmetric(horizontal: 7),
        ),
        child: text,
      );
    }

    return Container(
      height: 62,
      padding: const EdgeInsets.symmetric(horizontal: 12),
      decoration: BoxDecoration(
        color: _surface,
        border: const Border(bottom: BorderSide(color: _brandRed, width: 3)),
      ),
      child: Row(
        children: [
          SvgPicture.asset(
            _isDarkMode
                ? 'assets/images/autodoc-logo-darkmode.svg'
                : 'assets/images/autodoc-logo.svg',
            width: 44,
            height: 44,
          ),
          const SizedBox(width: 6),
          Text(
            'AUTO',
            style: GoogleFonts.raleway(
              color: const Color(0xFFB12A2A),
              fontSize: 23,
              fontWeight: FontWeight.w700,
            ),
          ),
          Text(
            'DOC',
            style: GoogleFonts.raleway(
              color: _foreground,
              fontSize: 23,
              fontWeight: FontWeight.w700,
            ),
          ),
          const Spacer(),
          if (isWide) ...[
            desktopItem('Dashboard', section: _DashboardSection.dashboard),
            desktopItem('Announcement'),
            desktopItem('Status', section: _DashboardSection.status),
            desktopItem('About Us'),
            desktopItem('Contact'),
          ],
          IconButton(
            tooltip: _isDarkMode ? 'Light mode' : 'Dark mode',
            onPressed: () => setState(() => _isDarkMode = !_isDarkMode),
            icon: Icon(
              _isDarkMode
                  ? Icons.light_mode_outlined
                  : Icons.dark_mode_outlined,
              color: _foreground,
            ),
          ),
          if (!isWide)
            IconButton(
              tooltip: _isMenuOpen ? 'Close menu' : 'Open menu',
              onPressed: () => setState(() => _isMenuOpen = !_isMenuOpen),
              icon: Icon(
                _isMenuOpen ? Icons.close : Icons.menu_sharp,
                color: _foreground,
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildMobileMenu() {
    Widget item(_DashboardSection section) {
      return SizedBox(
        height: 53,
        child: TextButton(
          onPressed: () => _selectSection(section),
          style: TextButton.styleFrom(
            foregroundColor: _foreground,
            padding: const EdgeInsets.symmetric(horizontal: 8),
            minimumSize: Size.zero,
            tapTargetSize: MaterialTapTargetSize.shrinkWrap,
          ),
          child: Text(
            _sectionLabel(section).toUpperCase(),
            style: GoogleFonts.raleway(
              fontSize: 20,
              letterSpacing: 4,
              fontWeight: FontWeight.w500,
            ),
          ),
        ),
      );
    }

    Widget inertItem(String label) {
      return SizedBox(
        height: 53,
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 8),
          child: Align(
            alignment: Alignment.centerRight,
            child: Text(
              label.toUpperCase(),
              style: GoogleFonts.raleway(
                color: _foreground,
                fontSize: 20,
                letterSpacing: 4,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
        ),
      );
    }

    return ColoredBox(
      color: _surface,
      child: Column(
        children: [
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.only(top: 20, right: 24, bottom: 16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  item(_DashboardSection.dashboard),
                  inertItem('Announcement'),
                  const SizedBox(height: 14),
                  item(_DashboardSection.status),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      item(_DashboardSection.enlistment),
                      item(_DashboardSection.evaluation),
                      item(_DashboardSection.enrollment),
                    ],
                  ),
                  const SizedBox(height: 22),
                  inertItem('About Us'),
                  inertItem('Contact'),
                ],
              ),
            ),
          ),
          Container(
            width: double.infinity,
            padding: const EdgeInsets.fromLTRB(24, 12, 24, 18),
            decoration: BoxDecoration(
              border: Border(
                top: BorderSide(color: _border.withValues(alpha: 0.3)),
              ),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                Icon(
                  Icons.person,
                  size: 38,
                  color: _isDarkMode ? Colors.white70 : Colors.black38,
                ),
                const SizedBox(width: 10),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Logged in as:',
                      style: TextStyle(color: _foreground, fontSize: 14),
                    ),
                    Text(
                      'STUDENT',
                      style: TextStyle(color: _foreground, fontSize: 23),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCurrentPage() {
    return switch (_section) {
      _DashboardSection.dashboard => _buildOverview(),
      _DashboardSection.status => _buildStatusPage(),
      _DashboardSection.enlistment => _buildRequirementsPage(
        _DashboardSection.enlistment,
      ),
      _DashboardSection.evaluation => _buildRequirementsPage(
        _DashboardSection.evaluation,
      ),
      _DashboardSection.enrollment => _buildRequirementsPage(
        _DashboardSection.enrollment,
      ),
    };
  }

  Widget _buildOverview() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(18),
      child: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 900),
          child: Column(
            children: [
              _buildProfile(),
              const SizedBox(height: 22),
              _buildStatusTimeline(compact: true),
              const SizedBox(height: 20),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(color: _surface),
                child: Column(
                  children: [
                    Row(
                      children: [
                        Text(
                          'Documents',
                          style: TextStyle(
                            color: _foreground,
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const Spacer(),
                        Container(
                          width: 140,
                          height: 36,
                          padding: const EdgeInsets.symmetric(horizontal: 8),
                          decoration: BoxDecoration(
                            color: _surface,
                            border: Border.all(color: _border),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Row(
                            children: [
                              Icon(Icons.search, color: _foreground, size: 18),
                              const SizedBox(width: 6),
                              Text(
                                'Search',
                                style: TextStyle(
                                  color: _foreground.withValues(alpha: 0.7),
                                  fontSize: 13,
                                ),
                              ),
                            ],
                          ),
                        ),
                        IconButton(
                          tooltip: 'Download documents',
                          onPressed: _showDownloadMessage,
                          icon: Icon(
                            Icons.file_download_outlined,
                            color: _foreground,
                          ),
                        ),
                      ],
                    ),
                    const Divider(),
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: DataTable(
                        headingRowHeight: 38,
                        dataRowMinHeight: 42,
                        dataRowMaxHeight: 48,
                        horizontalMargin: 8,
                        columnSpacing: 20,
                        headingTextStyle: TextStyle(
                          color: _foreground,
                          fontWeight: FontWeight.bold,
                        ),
                        dataTextStyle: TextStyle(
                          color: _foreground,
                          fontSize: 12,
                        ),
                        columns: const [
                          DataColumn(label: Text('FILE NAME')),
                          DataColumn(label: Text('CAPACITY')),
                          DataColumn(label: Text('FILE TYPE')),
                          DataColumn(label: Text('DATE')),
                          DataColumn(label: Text('STATUS')),
                        ],
                        rows: _documentRows
                            .map(
                              (row) => DataRow(
                                cells: row
                                    .map((value) => DataCell(Text(value)))
                                    .toList(),
                              ),
                            )
                            .toList(),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildProfile() {
    return Column(
      children: [
        const Icon(Icons.account_box, color: _brightRed, size: 120),
        const SizedBox(height: 12),
        Text(
          'Bayani, John Daniel G.',
          textAlign: TextAlign.center,
          style: TextStyle(
            color: _foreground,
            fontSize: 20,
            fontWeight: FontWeight.bold,
          ),
        ),
        Text('2425 - 0885', style: TextStyle(color: _foreground, fontSize: 18)),
        Text('BSIT 3A', style: TextStyle(color: _foreground, fontSize: 17)),
      ],
    );
  }

  Widget _buildStatusTimeline({bool compact = false}) {
    const statuses = ['Enlistment', 'Evaluation', 'Enrollment'];

    return Column(
      children: [
        for (var index = 0; index < statuses.length; index++) ...[
          if (index > 0)
            Container(
              width: 2,
              height: compact ? 14 : 24,
              color: _isDarkMode ? Colors.white38 : Colors.black26,
            ),
          Container(
            width: compact ? 132 : 240,
            padding: EdgeInsets.all(compact ? 8 : 18),
            decoration: BoxDecoration(
              color: _surface,
              border: Border.all(color: _border, width: 1.5),
              borderRadius: BorderRadius.circular(compact ? 5 : 20),
            ),
            child: Column(
              children: [
                Text(
                  statuses[index],
                  style: TextStyle(
                    color: _foreground,
                    fontSize: compact ? 13 : 24,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                if (!compact) ...[
                  const SizedBox(height: 14),
                  Icon(
                    index == 0 ? Icons.document_scanner_outlined : Icons.lock,
                    color: _brightRed,
                    size: 68,
                  ),
                  const SizedBox(height: 8),
                  Text(
                    index == 0 ? 'In Progress' : 'Opens when scheduled',
                    style: TextStyle(color: _foreground, fontSize: 15),
                  ),
                ] else ...[
                  Text(
                    'Successfully uploaded',
                    style: TextStyle(color: _foreground, fontSize: 10),
                  ),
                  const SizedBox(height: 4),
                  const Icon(
                    Icons.check_circle,
                    color: Color(0xFF00B234),
                    size: 21,
                  ),
                  const Text(
                    'Verified',
                    style: TextStyle(color: _brightRed, fontSize: 9),
                  ),
                ],
              ],
            ),
          ),
        ],
      ],
    );
  }

  Widget _buildStatusPage() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Center(
        child: Column(
          children: [
            Text(
              'Status',
              style: TextStyle(
                color: _foreground,
                fontSize: 26,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 24),
            _buildStatusTimeline(),
          ],
        ),
      ),
    );
  }

  Widget _buildRequirementsPage(_DashboardSection section) {
    final label = _sectionLabel(section);
    final isAvailable = section == _DashboardSection.enlistment;

    return SingleChildScrollView(
      padding: const EdgeInsets.fromLTRB(24, 28, 24, 36),
      child: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 700),
          child: Column(
            children: [
              Container(
                width: double.infinity,
                constraints: const BoxConstraints(minHeight: 232),
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: _surface,
                  border: Border.all(color: _border.withValues(alpha: 0.7)),
                  borderRadius: BorderRadius.circular(24),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      '$label Requirements',
                      style: TextStyle(
                        color: _foreground,
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 10),
                    for (var item = 1; item <= 3; item++)
                      Padding(
                        padding: const EdgeInsets.only(bottom: 5),
                        child: Text(
                          '•  Requirement $item',
                          style: TextStyle(color: _foreground, fontSize: 17),
                        ),
                      ),
                  ],
                ),
              ),
              const SizedBox(height: 44),
              Container(
                width: 260,
                constraints: const BoxConstraints(minHeight: 250),
                padding: const EdgeInsets.all(22),
                decoration: BoxDecoration(
                  color: _surface,
                  border: Border.all(color: _border.withValues(alpha: 0.7)),
                  borderRadius: BorderRadius.circular(26),
                ),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      '$label Requirements',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        color: _foreground,
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 18),
                    if (isAvailable) ...[
                      _gradientButton(
                        'Attach',
                        _showAttachMessage,
                        secondary: true,
                      ),
                      const SizedBox(height: 10),
                      _gradientButton('Upload', _showUploadMessage),
                      const SizedBox(height: 12),
                      Text(
                        'Until the deadline',
                        style: TextStyle(color: _foreground, fontSize: 13),
                      ),
                    ] else ...[
                      const Icon(Icons.lock, color: _brightRed, size: 90),
                      const SizedBox(height: 10),
                      Text(
                        'Opens when scheduled',
                        style: TextStyle(color: _foreground, fontSize: 15),
                      ),
                    ],
                  ],
                ),
              ),
              const SizedBox(height: 46),
              Text(
                'Note: Please make sure that you upload the correct and accurate information. Any false information or information that involves using someone else’s identity may result in legal action for identity theft.',
                textAlign: TextAlign.center,
                style: TextStyle(color: _foreground, fontSize: 14, height: 1.5),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _gradientButton(
    String label,
    VoidCallback onPressed, {
    bool secondary = false,
  }) {
    return SizedBox(
      width: double.infinity,
      height: 42,
      child: DecoratedBox(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: secondary
                ? const [Color(0xFFF2F2F2), Color(0xFFCCCCCC)]
                : const [Color(0xFFA91616), Color(0xFFD91F26)],
          ),
          borderRadius: BorderRadius.circular(7),
        ),
        child: ElevatedButton(
          onPressed: onPressed,
          style: ElevatedButton.styleFrom(
            backgroundColor: Colors.transparent,
            foregroundColor: secondary ? Colors.black : Colors.white,
            shadowColor: Colors.transparent,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(7),
            ),
          ),
          child: Text(label),
        ),
      ),
    );
  }

  void _showDownloadMessage() {
    _showMessage('Document downloads are not connected yet.');
  }

  void _showAttachMessage() {
    _showMessage('Document selection is not connected yet.');
  }

  void _showUploadMessage() {
    _showMessage('Document uploads are not connected yet.');
  }

  void _showMessage(String message) {
    ScaffoldMessenger.of(context)
        .showSnackBar(SnackBar(content: Text(message)));
  }
}
