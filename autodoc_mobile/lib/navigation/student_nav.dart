import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';

import '../screens/account.dart';
import '../screens/dashboard.dart';
import '../screens/document_detail.dart';
import '../screens/documents.dart';
import '../screens/notifications.dart';
import '../screens/upload.dart';
import '../services/api_client.dart';
import '../widgets/profile_avatar.dart';

class Navigation extends StatefulWidget {
  const Navigation({
    super.key,
    required this.onLogout,
    required this.isDarkMode,
    required this.onToggleTheme,
  });
  final Future<void> Function() onLogout;
  final bool isDarkMode;
  final VoidCallback onToggleTheme;
  @override
  State<Navigation> createState() => _NavState();
}

class _NavState extends State<Navigation> {
  final GlobalKey<ScaffoldState> scaffoldKey = GlobalKey<ScaffoldState>();
  int selectedIndex = 0;
  final Map<int, bool> _pageLoading = {
    0: true,
    1: true,
    2: true,
    3: true,
    4: true,
  };
  Map<String, dynamic> student = {};
  final menuItems = const [
    'Dashboard',
    'My Documents',
    'Upload Documents',
    'Notifications',
    'My Account',
  ];

  @override
  void initState() {
    super.initState();
    _loadStudent();
  }

  Future<void> _loadStudent() async {
    try {
      final value = await ApiClient.instance.get('/mobile/me');
      if (mounted) setState(() => student = value);
    } catch (_) {}
  }

  void _setPageLoading(int index, bool loading) {
    if (!mounted || _pageLoading[index] == loading) return;
    setState(() => _pageLoading[index] = loading);
  }

  void _selectPage(int index) {
    setState(() {
      selectedIndex = index;
      _pageLoading[index] = true;
    });
  }

  Future<void> _openDocument(String id, {String? message}) async {
    await Navigator.of(context).push<void>(
      MaterialPageRoute<void>(
        builder: (_) =>
            DocumentDetail(documentId: id, notificationMessage: message),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final pages = <Widget>[
      Dashboard(
        onUpload: () => _selectPage(2),
        onOpenDocument: _openDocument,
        onLoadingChanged: (loading) => _setPageLoading(0, loading),
      ),
      MyDocuments(
        onOpenDocument: _openDocument,
        onLoadingChanged: (loading) => _setPageLoading(1, loading),
      ),
      Upload(onLoadingChanged: (loading) => _setPageLoading(2, loading)),
      Notifications(
        onOpenDocument: _openDocument,
        onLoadingChanged: (loading) => _setPageLoading(3, loading),
      ),
      Account(onLoadingChanged: (loading) => _setPageLoading(4, loading)),
    ];
    final fullName = [
      student['first_name'],
      student['last_name'],
    ].where((v) => v != null && v.toString().trim().isNotEmpty).join(' ');
    return Scaffold(
      key: scaffoldKey,
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      appBar: _pageLoading[selectedIndex] == true
          ? null
          : AppBar(
        backgroundColor: scheme.surface,
        elevation: 0,
        automaticallyImplyLeading: false,
        shape: const Border(
          bottom: BorderSide(color: Color(0xFF951F1C), width: 3),
        ),
        title: GestureDetector(
          onTap: () => _selectPage(0),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              SvgPicture.asset(
                widget.isDarkMode
                    ? 'assets/logo/autodoc-logo-darkmode.svg'
                    : 'assets/logo/autodoc-logo.svg',
                height: 42,
              ),
              const SizedBox(width: 8),
              const Text(
                'AUTO',
                style: TextStyle(
                  fontFamily: 'Arial',
                  fontSize: 22,
                  fontWeight: FontWeight.w600,
                  color: Color(0xFFD32F2F),
                ),
              ),
              Text(
                'DOC',
                style: TextStyle(
                  fontFamily: 'Arial',
                  fontSize: 22,
                  fontWeight: FontWeight.w600,
                  color: scheme.onSurface,
                ),
              ),
            ],
          ),
        ),
        actions: [
          IconButton(
            onPressed: widget.onToggleTheme,
            icon: Icon(
              widget.isDarkMode
                  ? Icons.light_mode_outlined
                  : Icons.dark_mode_outlined,
              color: widget.isDarkMode ? Colors.white : Colors.black,
            ),
          ),
          IconButton(
            onPressed: () => scaffoldKey.currentState?.openDrawer(),
            icon: Icon(Icons.menu, color: scheme.onSurface),
          ),
        ],
            ),
      drawer: Drawer(
        width: MediaQuery.sizeOf(context).width,
        backgroundColor: scheme.surface,
        child: SafeArea(
          child: Column(
            children: [
              Align(
                alignment: Alignment.topRight,
                child: IconButton(
                  onPressed: () => Navigator.pop(context),
                  icon: Icon(Icons.close, color: scheme.onSurface),
                ),
              ),
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 0, 20, 18),
                child: Row(
                  children: [
                    ProfileAvatar(
                      imageUrl: student['profile_picture_url']?.toString(),
                      radius: 28,
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        fullName.isEmpty
                            ? (student['name']?.toString() ?? 'Student')
                            : fullName,
                        style: TextStyle(
                          fontSize: 17,
                          fontWeight: FontWeight.w700,
                          color: scheme.onSurface,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              Expanded(
                child: ListView.builder(
                  itemCount: menuItems.length,
                  itemBuilder: (context, index) => Padding(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 3,
                    ),
                    child: ListTile(
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(13),
                      ),
                      tileColor: selectedIndex == index
                          ? const Color(0x22D32F2F)
                          : null,
                      title: Text(
                        menuItems[index],
                        textAlign: TextAlign.right,
                        style: TextStyle(
                          fontSize: 20,
                          fontWeight: selectedIndex == index
                              ? FontWeight.bold
                              : FontWeight.normal,
                          color: selectedIndex == index
                              ? const Color(0xFFD32F2F)
                              : scheme.onSurface,
                        ),
                      ),
                      onTap: () {
                        _selectPage(index);
                        Navigator.pop(context);
                      },
                    ),
                  ),
                ),
              ),
              Padding(
                padding: const EdgeInsets.only(right: 20, bottom: 18),
                child: Align(
                  alignment: Alignment.centerRight,
                  child: TextButton.icon(
                    onPressed: () async {
                      Navigator.pop(context);
                      await widget.onLogout();
                    },
                    icon: const Icon(Icons.logout, color: Color(0xFFD32F2F)),
                    label: const Text(
                      'Log out',
                      style: TextStyle(color: Color(0xFFD32F2F)),
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
      body: pages[selectedIndex],
    );
  }
}
