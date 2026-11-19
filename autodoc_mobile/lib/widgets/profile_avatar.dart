import 'package:flutter/material.dart';

class ProfileAvatar extends StatelessWidget {
  const ProfileAvatar({
    super.key,
    required this.imageUrl,
    required this.radius,
    this.iconSize,
  });

  final String? imageUrl;
  final double radius;
  final double? iconSize;

  @override
  Widget build(BuildContext context) {
    final url = imageUrl?.trim();
    final icon = Icon(
      Icons.person,
      size: iconSize ?? radius,
      color: Theme.of(context).colorScheme.onSurfaceVariant,
    );

    return CircleAvatar(
      radius: radius,
      backgroundColor: Theme.of(context).colorScheme.surfaceContainerHighest,
      child: url == null || url.isEmpty
          ? icon
          : ClipOval(
              child: SizedBox(
                width: radius * 2,
                height: radius * 2,
                child: Image.network(
                  url,
                  fit: BoxFit.cover,
                  errorBuilder: (context, error, stackTrace) => icon,
                ),
              ),
            ),
    );
  }
}
