import 'package:flutter/material.dart';

import '../services/api_client.dart';
import '../widgets/autodoc_loading.dart';

class Notifications extends StatefulWidget {
  const Notifications({super.key, required this.onOpenDocument, this.onLoadingChanged});
  final Future<void> Function(String id, {String? message}) onOpenDocument;
  final ValueChanged<bool>? onLoadingChanged;
  @override
  State<Notifications> createState() => _NotificationsState();
}

class _NotificationsState extends State<Notifications> {
  List<Map<String, dynamic>> items = [];
  bool loading = true, markingRead = false;
  String? error;
  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      loading = true;
      error = null;
    });
    _notifyLoading(items.isEmpty);
    try {
      final response = await ApiClient.instance.get('/mobile/notifications');
      items = List<Map<String, dynamic>>.from(
        (response['notifications'] as List? ?? const []).whereType<Map>().map(
          (e) => Map<String, dynamic>.from(e),
        ),
      );
    } catch (e) {
      error = e.toString();
    } finally {
      if (mounted) {
        setState(() => loading = false);
        _notifyLoading(false);
      }
    }
  }

  void _notifyLoading(bool isLoading) {
    final callback = widget.onLoadingChanged;
    if (callback == null) return;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (mounted) callback(isLoading);
    });
  }

  Future<void> _markAllRead() async {
    setState(() => markingRead = true);
    try {
      await ApiClient.instance.post('/mobile/notifications/read-all');
      await _load();
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text(e.toString())));
      }
    } finally {
      if (mounted) setState(() => markingRead = false);
    }
  }

  Future<void> _open(Map<String, dynamic> item) async {
    final id = item['document_id']?.toString();
    try {
      await ApiClient.instance.post('/mobile/notifications/${item['id']}/read');
    } catch (_) {}
    if (!mounted) return;
    setState(() => item['read_at'] = DateTime.now().toIso8601String());
    if (id != null && id.isNotEmpty) {
      await widget.onOpenDocument(id, message: item['message']?.toString());
      await _load();
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final unread = items.any((item) => item['read_at'] == null);
    if (loading && items.isEmpty) {
      return const SafeArea(child: AutoDocLoading());
    }
    return SafeArea(
      child: RefreshIndicator(
        onRefresh: _load,
        child: ListView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(15),
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Notifications',
                  style: Theme.of(context).textTheme.headlineSmall
                      ?.copyWith(fontWeight: FontWeight.bold),
                ),
                TextButton(
                  onPressed: unread && !markingRead ? _markAllRead : null,
                  child: markingRead
                      ? const SizedBox(
                          width: 16,
                          height: 16,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : const Text('Mark all as read'),
                ),
              ],
            ),
            if (error != null)
              Padding(
                padding: const EdgeInsets.all(8),
                child: Text(error!, style: TextStyle(color: scheme.error)),
              ),
            if (items.isEmpty)
              const Padding(
                padding: EdgeInsets.only(top: 100),
                child: Center(child: Text('No notifications yet.')),
              )
            else
              ...items.map((item) {
                final isUnread = item['read_at'] == null;
                final status =
                    item['status']?.toString().replaceAll('_', ' ') ??
                    'Document update';
                final type = item['document_type']?.toString() ?? 'Document';
                final doc = item['document_name']?.toString() ?? '';
                return Container(
                  margin: const EdgeInsets.only(top: 7),
                  decoration: BoxDecoration(
                    color: isUnread
                        ? scheme.errorContainer.withValues(alpha: .42)
                        : scheme.surface,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: Theme.of(context).dividerColor),
                  ),
                  child: ListTile(
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    leading: Icon(
                      Icons.notifications_outlined,
                      color: scheme.primary,
                    ),
                    title: Text(
                      status.toUpperCase(),
                      style: const TextStyle(fontWeight: FontWeight.bold),
                    ),
                    subtitle: Text(
                      [
                        type,
                        if (doc.isNotEmpty) doc,
                        _date(item['created_at']),
                      ].join('\n'),
                      maxLines: 4,
                      overflow: TextOverflow.ellipsis,
                    ),
                    trailing: isUnread
                        ? const Icon(
                            Icons.circle,
                            color: Color(0xFFD32F2F),
                            size: 9,
                          )
                        : null,
                    onTap: () => _open(item),
                  ),
                );
              }),
          ],
        ),
      ),
    );
  }

  String _date(dynamic value) {
    final d = DateTime.tryParse(value?.toString() ?? '')?.toLocal();
    if (d == null) return '';
    return '${d.year}-${d.month.toString().padLeft(2, '0')}-${d.day.toString().padLeft(2, '0')} ${d.hour.toString().padLeft(2, '0')}:${d.minute.toString().padLeft(2, '0')}';
  }
}
