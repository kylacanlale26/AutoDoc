
import 'package:flutter/material.dart';
import '../student-nav.dart';

class Account extends StatelessWidget {
  const Account({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,

      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        automaticallyImplyLeading: false,
        titleSpacing: 15,
        title: RichText(
          text: const TextSpan(
            children: [
              TextSpan(
                text: 'AUTO',
                style: TextStyle(
                  color: Color(0xFFD32F2F),
                  fontSize: 26,
                  fontWeight: FontWeight.bold,
                ),
              ),
              TextSpan(
                text: 'DOC',
                style: TextStyle(
                  color: Colors.black,
                  fontSize: 26,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
        ),
        actions: [
          Builder(
            builder: (context) => IconButton(
              onPressed: () {
                Scaffold.of(context).openDrawer();
              },
              icon: const Icon(
                Icons.menu,
                color: Colors.black,
                size: 32,
              ),
            ),
          ),
          const SizedBox(width: 12),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(4),
          child: Container(
            height: 4,
            color: const Color(0xFF951F1C),
          ),
        ),
      ),

      body: SafeArea(
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.symmetric(
              horizontal: 15,
              vertical: 15,
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'My Account',
                  style: TextStyle(
                    fontSize: 29,
                    fontWeight: FontWeight.bold,
                    color: Colors.black,
                  ),
                ),

                const SizedBox(height: 32),

                Center(
                  child: Column(
                    children: [
                      Container(
                        width: 122,
                        height: 122,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          border: Border.all(
                            color: const Color(0xFFE5252A),
                            width: 8,
                          ),
                        ),
                        child: const Icon(
                          Icons.account_circle_outlined,
                          size: 106,
                          color: Color(0xFFE5252A),
                        ),
                      ),

                      const SizedBox(height: 12),

                      const Text(
                        '<student name>',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          letterSpacing: 1,
                          color: Colors.black,
                        ),
                      ),

                      const Text(
                        '<student program, level, and section>',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          fontSize: 12,
                          letterSpacing: 0.5,
                          color: Colors.black,
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 40),

                const Center(
                  child: Column(
                    children: [
                      Text(
                        'Student ID',
                        style: TextStyle(
                          fontSize: 16,
                          color: Colors.black,
                        ),
                      ),

                      Text(
                        '<id number>',
                        style: TextStyle(
                          fontSize: 16,
                          color: Colors.black,
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 15),

                const Divider(
                  color: Color(0xFFEEEEEE),
                  thickness: 1,
                ),

                const SizedBox(height: 15),

                const Center(
                  child: Column(
                    children: [
                      Text(
                        'MCC Email',
                        style: TextStyle(
                          fontSize: 16,
                          color: Colors.black,
                        ),
                      ),

                      Text(
                        '<email>',
                        style: TextStyle(
                          fontSize: 16,
                          color: Colors.black,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
