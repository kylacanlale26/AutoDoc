
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';

import '../screens/dashboard.dart';
import '../screens/documents.dart';
import '../screens/uploads.dart';
import '../screens/notifications.dart';
import '../screens/account.dart';

class Navigation extends StatefulWidget {
  const Navigation({super.key});

  @override
  State<Navigation> createState() => _NavState();
}

class _NavState extends State<Navigation> {
  final GlobalKey<ScaffoldState> scaffoldKey = GlobalKey<ScaffoldState>();

  bool menuOpen = false;
  int selectedIndex = 0;

  final List<String> menuItems = [
    'Dashboard',
    'My Documents',
    'Upload Documents',
    'Notifications',
    'My Account',
  ];

  final List<Widget> pages = [
    const Dashboard(),
    const MyDocuments(),
    const Upload(),
    const Notifications(),
    const Account(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      key: scaffoldKey,
      backgroundColor: Colors.white,

      onDrawerChanged: (isOpened) {
        setState(() {
          menuOpen = isOpened;
        });
      },

      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        automaticallyImplyLeading: false,
        shape: const Border(
          bottom: BorderSide(
            color: Color(0xFF951F1C),
            width: 3,
          ),
        ),
        title: Row(
          children: [
            SvgPicture.asset(
              'assets/logo/autodoc-logo.svg',
              height: 45,
            ),

            const SizedBox(width: 10),

            const Text(
              'AUTO',
              style: TextStyle(
                fontFamily: 'Arial',
                fontSize: 25,
                fontWeight: FontWeight.w600,
                color: Color(0xFFD32F2F),
              ),
            ),

            const Text(
              'DOC',
              style: TextStyle(
                fontFamily: 'Arial',
                fontSize: 25,
                fontWeight: FontWeight.w600,
                color: Colors.black,
              ),
            ),
          ],
        ),
        actions: [
          const Icon(
            Icons.dark_mode_outlined,
            color: Colors.black,
            size: 28,
          ),

          const SizedBox(width: 10),

          IconButton(
            onPressed: () {
              if (menuOpen) {
                Navigator.pop(context);
              } else {
                scaffoldKey.currentState?.openDrawer();
              }
            },
            icon: Icon(
              menuOpen ? Icons.close : Icons.menu,
              color: Colors.black,
              size: 28,
            ),
          ),
        ],
      ),

      drawer: Drawer(
        width: MediaQuery.of(context).size.width,
        backgroundColor: Colors.white,
        child: SafeArea(
          child: Column(
            children: [
              Expanded(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: List.generate(menuItems.length, (index) {
                    return ListTile(
                      contentPadding: const EdgeInsets.only(right: 32),
                      minVerticalPadding: 15,
                      title: Text(
                        menuItems[index],
                        textAlign: TextAlign.right,
                        style: TextStyle(
                          fontFamily: 'Arial',
                          fontSize: 25,
                          fontWeight: selectedIndex == index
                              ? FontWeight.bold
                              : FontWeight.normal,
                          color: selectedIndex == index
                              ? const Color(0xFF951F1C)
                              : Colors.black,
                        ),
                      ),
                      selected: selectedIndex == index,
                      onTap: () {
                        setState(() {
                          selectedIndex = index;
                        });

                        Navigator.pop(context);
                      },
                    );
                  }),
                ),
              ),

              Padding(
                padding: const EdgeInsets.only(
                  right: 20,
                  bottom: 25,
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.end,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: const [
                        Text(
                          'Logged in as:',
                          style: TextStyle(
                            fontFamily: 'Arial',
                            fontSize: 16,
                            color: Colors.black,
                          ),
                        ),

                        Text(
                          'STUDENT',
                          style: TextStyle(
                            fontFamily: 'Arial',
                            fontSize: 25,
                            color: Colors.black,
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(width: 10),

                    const Icon(
                      Icons.person,
                      color: Color(0xFFB4B4B4),
                      size: 60,
                    ),
                  ],
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
