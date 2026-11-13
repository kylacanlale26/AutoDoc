import 'package:flutter/material.dart';

class Dashboard extends StatelessWidget {
  const Dashboard({super.key});

  @override
  Widget build(BuildContext context) {
    final List<String> recentDocuments = [];

    return Scaffold(
      backgroundColor: Colors.white,

      body: SafeArea(
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.symmetric(
              horizontal: 28,
              vertical: 15,
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Hello, Student!',
                  style: TextStyle(
                    fontSize: 30,
                    fontWeight: FontWeight.bold,
                    color: Colors.black,
                  ),
                ),

                const SizedBox(height: 2),

                const Text(
                  'Manage your documents in one place.',
                  style: TextStyle(
                    fontSize: 14,
                    letterSpacing: 1,
                    color: Colors.black,
                  ),
                ),

                const SizedBox(height: 14),

                Row(
                  children: [
                    Expanded(
                      child: buildStatCard(
                        icon: Icons.description_outlined,
                        iconColor: const Color(0xFF951F1C),
                        count: '8',
                        label: 'Total Documents',
                      ),
                    ),

                    const SizedBox(width: 18),

                    Expanded(
                      child: buildStatCard(
                        icon: Icons.check_circle_outline,
                        iconColor: const Color(0xFF007A3F),
                        count: '5',
                        label: 'Verified',
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 15),

                Row(
                  children: [
                    Expanded(
                      child: buildStatCard(
                        icon: Icons.schedule_outlined,
                        iconColor: const Color(0xFFFFD21F),
                        count: '2',
                        label: 'Pending',
                      ),
                    ),

                    const SizedBox(width: 18),

                    Expanded(
                      child: buildStatCard(
                        icon: Icons.error_outline,
                        iconColor: const Color(0xFF951F1C),
                        count: '1',
                        label: 'Needs Correction',
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 15),

                SizedBox(
                  width: double.infinity,
                  height: 50,
                  child: ElevatedButton(
                    onPressed: () {
                      // Upload functionality
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFD32F2F),
                      foregroundColor: Colors.white,
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(7),
                      ),
                    ),
                    child: const Row(
                      children: [
                        Icon(
                          Icons.file_upload_outlined,
                          size: 20,
                          color: Colors.white,
                        ),

                        SizedBox(width: 8),

                        Text(
                          'Upload a Document',
                          style: TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 1,
                          ),
                        ),

                        Spacer(),

                        Icon(
                          Icons.arrow_forward,
                          size: 23,
                          color: Colors.white,
                        ),
                      ],
                    ),
                  ),
                ),

                const SizedBox(height: 12),

                const Text(
                  'Recent Documents',
                  style: TextStyle(
                    fontSize: 21,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 1,
                    color: Colors.black,
                  ),
                ),

                const SizedBox(height: 15),

                if (recentDocuments.isEmpty)
                  const Center(
                    child: Padding(
                      padding: EdgeInsets.symmetric(vertical: 25),
                      child: Text(
                        'No Recent Submissions',
                        style: TextStyle(
                          fontSize: 14,
                          color: Colors.grey,
                        ),
                      ),
                    ),
                  )
                else
                  Column(
                    children: recentDocuments.map((document) {
                      return ListTile(
                        title: Text(document),
                      );
                    }).toList(),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget buildStatCard({
    required IconData icon,
    required Color iconColor,
    required String count,
    required String label,
  }) {
    return Container(
      height: 150,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFFE0E0E0),
        borderRadius: BorderRadius.circular(18),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            icon,
            color: iconColor,
            size: 24,
          ),

          const SizedBox(height: 3),

          Text(
            count,
            style: const TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: Colors.black,
            ),
          ),

          const SizedBox(height: 2),

          Text(
            label,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: const TextStyle(
              fontSize: 14,
              letterSpacing: 1,
              color: Colors.black,
            ),
          ),
        ],
      ),
    );
  }
}