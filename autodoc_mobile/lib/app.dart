import 'package:flutter/material.dart';
import 'navigation/student-nav.dart';

class AutoDoc extends StatelessWidget {
  const AutoDoc({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'AutoDoc',
      home: const Navigation(),
    );
  }
}