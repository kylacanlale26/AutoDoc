import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

import 'services/api_client.dart';
import 'services/auth_service.dart';
import 'navigation/student_nav.dart';
import 'screens/welcome.dart';
import 'screens/complete_profile.dart';

class AutoDoc extends StatefulWidget {
  const AutoDoc({super.key});

  @override
  State<AutoDoc> createState() => _AutoDocState();
}

class _AutoDocState extends State<AutoDoc> {
  static const _preferences = FlutterSecureStorage();
  bool _loading = true;
  bool _signedIn = false;
  bool _profileComplete = false;
  bool _darkMode = false;

  @override
  void initState() {
    super.initState();
    _restoreSession();
    _restoreTheme();
  }

  Future<void> _restoreTheme() async {
    final saved = await _preferences.read(key: 'autodoc_mobile_theme');
    if (mounted && saved != null) setState(() => _darkMode = saved == 'dark');
  }

  Future<void> _toggleTheme() async {
    final next = !_darkMode;
    setState(() => _darkMode = next);
    await _preferences.write(
      key: 'autodoc_mobile_theme',
      value: next ? 'dark' : 'light',
    );
  }

  Future<void> _restoreSession() async {
    final token = await ApiClient.instance.readToken();
    var signedIn = token != null && token.isNotEmpty;
    if (signedIn) {
      try {
        final me = await ApiClient.instance.get('/mobile/me');
        _profileComplete = me['profile_completed_at'] != null;
      } on ApiException catch (error) {
        if (error.statusCode == 401) {
          await ApiClient.instance.clearToken();
          signedIn = false;
        }
      } catch (_) {
        // Keep the session when the server is temporarily unreachable.
      }
    }
    if (!mounted) return;
    setState(() {
      _signedIn = signedIn;
      _loading = false;
    });
  }

  Future<void> _signedInNow() async {
    var profileComplete = false;
    try {
      final me = await ApiClient.instance.get('/mobile/me');
      profileComplete = me['profile_completed_at'] != null;
    } catch (_) {}
    if (mounted) {
      setState(() {
        _signedIn = true;
        _profileComplete = profileComplete;
      });
    }
  }

  void _profileCompleted() {
    if (mounted) setState(() => _profileComplete = true);
  }

  Future<void> _signOut() async {
    await AuthService.instance.signOut();
    if (mounted) setState(() => _signedIn = false);
  }

  @override
  Widget build(BuildContext context) {
    final lightScheme = ColorScheme.fromSeed(
      seedColor: const Color(0xFFD32F2F),
      brightness: Brightness.light,
    );
    final darkScheme = ColorScheme.fromSeed(
      seedColor: const Color(0xFFD32F2F),
      brightness: Brightness.dark,
      surface: const Color(0xFF171717),
    );
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'AutoDoc',
      themeMode: _darkMode ? ThemeMode.dark : ThemeMode.light,
      theme: ThemeData(
        useMaterial3: true,
        colorScheme: lightScheme,
        fontFamily: 'Arial',
        scaffoldBackgroundColor: const Color(0xFFF7F7F7),
        appBarTheme: const AppBarTheme(centerTitle: false),
      ),
      darkTheme: ThemeData(
        useMaterial3: true,
        colorScheme: darkScheme,
        fontFamily: 'Arial',
        scaffoldBackgroundColor: const Color(0xFF171717),
        appBarTheme: const AppBarTheme(centerTitle: false),
      ),
      home: _loading
          ? const _AutoDocLoadingScreen()
          : _signedIn
          ? !_profileComplete
                ? CompleteProfile(onCompleted: _profileCompleted)
                : Navigation(
                    onLogout: _signOut,
                    isDarkMode: _darkMode,
                    onToggleTheme: _toggleTheme,
                  )
          : Welcome(
              onAuthenticated: _signedInNow,
              isDarkMode: _darkMode,
              onToggleTheme: _toggleTheme,
            ),
    );
  }
}

class _AutoDocLoadingScreen extends StatefulWidget {
  const _AutoDocLoadingScreen();

  @override
  State<_AutoDocLoadingScreen> createState() => _AutoDocLoadingScreenState();
}

class _AutoDocLoadingScreenState extends State<_AutoDocLoadingScreen>
    with SingleTickerProviderStateMixin {
  late final AnimationController _bobController;

  @override
  void initState() {
    super.initState();
    _bobController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _bobController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Semantics(
        label: 'Loading AutoDoc',
        liveRegion: true,
        child: Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              AnimatedBuilder(
                animation: _bobController,
                builder: (context, child) => Transform.translate(
                  offset: Offset(0, -5 * _bobController.value),
                  child: child,
                ),
                child: SizedBox(
                  width: 104,
                  height: 104,
                  child: SvgPicture.asset(
                    Theme.of(context).brightness == Brightness.dark
                        ? 'assets/logo/autodoc-logo-darkmode.svg'
                        : 'assets/logo/autodoc-logo.svg',
                  ),
                ),
              ),
              const SizedBox(height: 14),
              Text(
                'Loading AutoDoc',
                style: Theme.of(context).textTheme.titleSmall
                    ?.copyWith(fontWeight: FontWeight.w600, letterSpacing: 0.5),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
