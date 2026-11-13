import 'package:flutter/material.dart';
import '../screens/welcome.dart';

class AutoDoc extends StatelessWidget {
  const AutoDoc({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'AutoDoc',
      home: const Welcome(),
    );
  }
}