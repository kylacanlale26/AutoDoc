
import 'package:flutter/material.dart';

class Account extends StatelessWidget {
  const Account({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,

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
                        child: const Icon(
                          Icons.account_circle,
                          size: 120,
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
