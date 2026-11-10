
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';

class Welcome extends StatelessWidget {
  const Welcome({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
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
          const Padding(
            padding: EdgeInsets.only(right: 15),
            child: Icon(
              Icons.dark_mode_outlined,
              color: Colors.black,
              size: 28,
            ),
          ),
        ],
      ),

      body: Stack(
        children: [
          Positioned.fill(
            child: SvgPicture.asset(
              'assets/images/mcc-bg-light.svg',
              fit: BoxFit.cover,
            ),
          ),

          SafeArea(
            child: SingleChildScrollView(
              child: Padding(
                padding: const EdgeInsets.symmetric(
                  horizontal: 25,
                  vertical: 20,
                ),
                child: Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 15,
                    vertical: 55,
                  ),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.55),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Center(
                        child: Text(
                          'Sign in to Your Account',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            fontSize: 25,
                            fontWeight: FontWeight.w400,
                            color: Colors.black,
                          ),
                        ),
                      ),

                      const SizedBox(height: 14),

                      const Center(
                        child: Text(
                          'Please use your official school email.',
                          style: TextStyle(
                            fontSize: 13,
                            color: Colors.black,
                          ),
                        ),
                      ),

                      const SizedBox(height: 18),

                      Center(
                        child: SizedBox(
                          width: 263,
                          height: 56,
                          child: ElevatedButton(
                            onPressed: () {},
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFFD32F2F),
                              foregroundColor: Colors.white,
                              elevation: 0,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(14),
                              ),
                            ),
                            child: const Text(
                              'Login with Microsoft',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                        ),
                      ),

                      const SizedBox(height: 18),

                      Row(
                        children: [
                          const Expanded(
                            child: Divider(
                              color: Colors.black,
                              thickness: 1,
                            ),
                          ),

                          const SizedBox(width: 10),

                          const Text(
                            'Don’t have an account?',
                            style: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color: Colors.black,
                            ),
                          ),

                          const SizedBox(width: 10),

                          const Expanded(
                            child: Divider(
                              color: Colors.black,
                              thickness: 1,
                            ),
                          ),
                        ],
                      ),

                      const SizedBox(height: 7),

                      const Center(
                        child: Text(
                          'Contact MIS',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.black,
                          ),
                        ),
                      ),

                      const SizedBox(height: 50),

                      Container(
                        width: double.infinity,
                        height: 3,
                        color: const Color(0xFF951F1C),
                      ),

                      const SizedBox(height: 85),

                      const Text(
                        'Student Document Portal',
                        style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.bold,
                          letterSpacing: 1,
                          color: Colors.black,
                        ),
                      ),

                      const SizedBox(height: 5),

                      const Text.rich(
                        TextSpan(
                          children: [
                            TextSpan(
                              text: 'Your Documents,\n',
                              style: TextStyle(
                                color: Colors.black,
                              ),
                            ),

                            TextSpan(
                              text: 'Verified and Secured.',
                              style: TextStyle(
                                color: Color(0xFFD32F2F),
                              ),
                            ),
                          ],
                        ),
                        style: TextStyle(
                          fontSize: 29,
                          height: 0.98,
                          fontWeight: FontWeight.w800,
                        ),
                      ),

                      const SizedBox(height: 8),

                      const Text(
                        'A convenient way to upload, verify, and store your '
                        'required documents in one centralized platform.',
                        style: TextStyle(
                          fontSize: 13,
                          height: 1.4,
                          fontWeight: FontWeight.bold,
                          color: Colors.black,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
