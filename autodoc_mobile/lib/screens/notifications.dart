
import 'package:flutter/material.dart';
import '../navigation/student-nav.dart';

class Notifications extends StatelessWidget {
  const Notifications({super.key});

  @override
  Widget build(BuildContext context) {
    final List<Map<String, String>> notifications = [];

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
        child: Padding(
          padding: const EdgeInsets.symmetric(
            horizontal: 15,
            vertical: 15,
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Notifications',
                    style: TextStyle(
                      fontSize: 29,
                      fontWeight: FontWeight.bold,
                      color: Colors.black,
                    ),
                  ),

                  TextButton(
                    onPressed: () {},
                    child: const Text(
                      'Mark all as read',
                      style: TextStyle(
                        color: Color(0xFFE5252A),
                        fontSize: 13,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 1,
                      ),
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 10),

              if (notifications.isEmpty)
                const Expanded(
                  child: Center(
                    child: Text(
                      'No Notifications',
                      style: TextStyle(
                        fontSize: 15,
                        color: Colors.grey,
                      ),
                    ),
                  ),
                )
              else
                Expanded(
                  child: ListView.builder(
                    itemCount: notifications.length,
                    itemBuilder: (context, index) {
                      final notification = notifications[index];

                      return Padding(
                        padding: const EdgeInsets.only(bottom: 10),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    notification['title']!,
                                    style: const TextStyle(
                                      fontSize: 20,
                                      fontWeight: FontWeight.bold,
                                      letterSpacing: 1,
                                      color: Colors.black,
                                    ),
                                  ),

                                  Text(
                                    notification['description']!,
                                    style: const TextStyle(
                                      fontSize: 13,
                                      letterSpacing: 0.5,
                                      color: Colors.black,
                                    ),
                                  ),

                                  Text(
                                    notification['time']!,
                                    style: const TextStyle(
                                      fontSize: 12,
                                      letterSpacing: 1,
                                      color: Colors.black,
                                    ),
                                  ),
                                ],
                              ),
                            ),

                            if (notification['unread'] == 'true')
                              const Padding(
                                padding: EdgeInsets.only(top: 5),
                                child: Icon(
                                  Icons.circle,
                                  color: Color(0xFFE5252A),
                                  size: 9,
                                ),
                              ),
                          ],
                        ),
                      );
                    },
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }
}
