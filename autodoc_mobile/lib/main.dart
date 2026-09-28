import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:google_fonts/google_fonts.dart';

import 'login.dart';

void main() {
  runApp(const AutoDocApp());
}

class AutoDocApp extends StatelessWidget {
  const AutoDocApp({super.key});

  @override
  Widget build(BuildContext context) {
    return const MaterialApp(
      debugShowCheckedModeBanner: false,
      home: WelcomePage(),
    );
  }
}

class WelcomePage extends StatefulWidget {
  const WelcomePage({super.key, this.onSignIn});

  final VoidCallback? onSignIn;

  @override
  State<WelcomePage> createState() => _WelcomePageState();
}

class _WelcomePageState extends State<WelcomePage> {
  bool _isDarkMode = false;
  bool _isMenuOpen = false;

  static const _brandRed = Color(0xFF8F1D1D);
  static const _links = ['Announcement', 'Contact', 'About Us', 'Sign In'];
  static const _mobileLinks = [
    'Sign In',
    'Announcement',
    'Contact',
    'About Us',
  ];

  void _toggleTheme() {
    setState(() => _isDarkMode = !_isDarkMode);
  }

  void _selectLink(String label) {
    if (label == 'Sign In') {
      widget.onSignIn?.call();
      Navigator.of(context)
          .push(
            MaterialPageRoute<void>(
              builder: (_) => LoginPage(isDarkMode: _isDarkMode),
            ),
          )
          .then((_) {
            if (mounted && _isMenuOpen) {
              setState(() => _isMenuOpen = false);
            }
          });
      return;
    }

    setState(() => _isMenuOpen = false);
  }

  @override
  Widget build(BuildContext context) {
    final isWide = MediaQuery.sizeOf(context).width >= 760;
    final foreground = _isDarkMode ? Colors.white : const Color(0xFF111111);

    return Scaffold(
      backgroundColor: _isDarkMode ? const Color(0xFF121212) : Colors.white,
      body: SafeArea(
        child: Column(
          children: [
            _buildHeader(isWide, foreground),
            if (!isWide && _isMenuOpen)
              Expanded(child: _buildMobileMenu(foreground))
            else
              Expanded(child: _buildHero()),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader(bool isWide, Color foreground) {
    return Container(
      height: 62,
      padding: const EdgeInsets.symmetric(horizontal: 12),
      decoration: BoxDecoration(
        color: _isDarkMode ? const Color(0xFF151515) : Colors.white,
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
              color: Color(0xFFB12A2A),
              fontSize: 23,
              fontWeight: FontWeight.w700,
            ),
          ),
          Text(
            'DOC',
            style: GoogleFonts.raleway(
              color: foreground,
              fontSize: 23,
              fontWeight: FontWeight.w700,
            ),
          ),
          const Spacer(),
          if (isWide)
            ..._links.map(
              (label) => label == 'Sign In'
                  ? TextButton(
                      onPressed: () => _selectLink(label),
                      child: Text(
                        label,
                        style: GoogleFonts.raleway(
                          color: foreground,
                          fontSize: 15,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    )
                  : Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 10),
                      child: Text(
                        label,
                        style: GoogleFonts.raleway(
                          color: foreground,
                          fontSize: 15,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
            ),
          IconButton(
            tooltip: _isDarkMode ? 'Light mode' : 'Dark mode',
            onPressed: _toggleTheme,
            icon: Icon(
              _isDarkMode
                  ? Icons.light_mode_outlined
                  : Icons.dark_mode_outlined,
              color: foreground,
            ),
          ),
          if (!isWide)
            IconButton(
              tooltip: _isMenuOpen ? 'Close menu' : 'Open menu',
              onPressed: () {
                setState(() => _isMenuOpen = !_isMenuOpen);
              },
              icon: Icon(
                _isMenuOpen ? Icons.close : Icons.menu_sharp,
                color: foreground,
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildMobileMenu(Color foreground) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.only(top: 20, right: 24, bottom: 18),
      color: _isDarkMode ? const Color(0xFF151515) : Colors.white,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.end,
        children: _mobileLinks.map((label) {
          final text = Text(
            label.toUpperCase(),
            style: GoogleFonts.raleway(
              color: foreground,
              fontSize: 20,
              letterSpacing: 4,
              fontWeight: FontWeight.w500,
            ),
          );

          return SizedBox(
            height: 53,
            child: label == 'Sign In'
                ? TextButton(
                    onPressed: () => _selectLink(label),
                    style: TextButton.styleFrom(
                      foregroundColor: foreground,
                      padding: const EdgeInsets.symmetric(horizontal: 8),
                      minimumSize: Size.zero,
                      tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                    ),
                    child: text,
                  )
                : Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 8),
                    child: Align(alignment: Alignment.centerRight, child: text),
                  ),
          );
        }).toList(),
      ),
    );
  }

  Widget _buildHero() {
    final textColor = _isDarkMode ? const Color(0xFFB12A2A) : _brandRed;

    return Stack(
      fit: StackFit.expand,
      children: [
        Image.asset(
          'assets/images/mcc-bg.png',
          fit: BoxFit.cover,
          alignment: Alignment.center,
        ),
        if (_isDarkMode) Container(color: Colors.black.withValues(alpha: 0.60)),
        LayoutBuilder(
          builder: (context, constraints) {
            final isNarrow = constraints.maxWidth < 600;
            final horizontalPadding = isNarrow ? 22.0 : 43.0;

            return SingleChildScrollView(
              child: ConstrainedBox(
                constraints: BoxConstraints(minHeight: constraints.maxHeight),
                child: Padding(
                  padding: EdgeInsets.fromLTRB(
                    horizontalPadding,
                    24,
                    horizontalPadding,
                    42,
                  ),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.end,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Education and Formation',
                        style: TextStyle(
                          color: textColor,
                          fontFamily: 'Times New Roman',
                          fontSize: isNarrow ? 21 : 30,
                          fontWeight: FontWeight.bold,
                          shadows: _textShadow(),
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        'Learn, Grow, and Serve',
                        style: TextStyle(
                          color: textColor,
                          fontFamily: 'Times New Roman',
                          fontSize: isNarrow ? 42 : 71,
                          height: 1.05,
                          fontWeight: FontWeight.bold,
                          shadows: _textShadow(),
                        ),
                      ),
                      const SizedBox(height: 12),
                      Text(
                        'Start Here, Be Successful Anywhere. Mabalacat City College '
                        'believes education should shape the whole person equipping '
                        'every Mabalaqueño with the knowledge, skills, and values to '
                        'build a career, realize their full potential, and serve '
                        'their community as a responsible, engaged citizen.',
                        style: TextStyle(
                          color: textColor,
                          fontFamily: 'Times New Roman',
                          fontSize: isNarrow ? 18 : 26,
                          height: 1.4,
                          fontWeight: FontWeight.bold,
                          shadows: _textShadow(),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        ),
      ],
    );
  }

  List<Shadow> _textShadow() {
    return [
      Shadow(
        color: _isDarkMode
            ? Colors.white.withValues(alpha: 0.16)
            : Colors.black.withValues(alpha: 0.18),
        blurRadius: 8,
      ),
    ];
  }
}
