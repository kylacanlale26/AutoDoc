import 'dart:typed_data';

import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';

import '../services/api_client.dart';

class CompleteProfile extends StatefulWidget {
  const CompleteProfile({super.key, required this.onCompleted});
  final VoidCallback onCompleted;

  @override
  State<CompleteProfile> createState() => _CompleteProfileState();
}

class _CompleteProfileState extends State<CompleteProfile> {
  static const courses = [
    'Bachelor of Arts in History (ABH)',
    'Bachelor of Science in Biology (BSBIO)',
    'Bachelor of Early Childhood Education (BECED)',
    'Bachelor of Elementary Education (BEED)',
    'Bachelor of Physical Education (BPED)',
    'Bachelor of Secondary Education, Major in English (BSED-ENG)',
    'Bachelor of Secondary Education, Major in Filipino (BSED-FIL)',
    'Bachelor of Secondary Education, Major in Math (BSED-MATH)',
    'Bachelor of Secondary Education, Major in Science (BSED-SCI)',
    'Bachelor of Secondary Education, Major in Social Studies (BSED-SS)',
    'Bachelor of Technical-Vocational Teacher Education (BTVTE)',
    'Bachelor of Science in Customs Administration (BSCA)',
    'Bachelor of Science in Information Technology (BSIT)',
    'Bachelor of Science in Hospitality Management (BSHM)',
    'Bachelor of Science in Tourism Management (BSTM)',
  ];
  final _formKey = GlobalKey<FormState>();
  final _studentNumber = TextEditingController();
  String? _course, _year, _section, _error;
  PlatformFile? _picture;
  bool _accepted = false, _saving = false;

  @override
  void dispose() {
    _studentNumber.dispose();
    super.dispose();
  }

  Future<void> _pickPicture() async {
    final result = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: const ['jpg', 'jpeg', 'png'],
      withData: true,
    );
    if (result != null && result.files.isNotEmpty && mounted) {
      if (result.files.first.size > 5 * 1024 * 1024) {
        setState(
          () => _error = 'The profile picture must be no larger than 5 MB.',
        );
        return;
      }
      setState(() {
        _picture = result.files.first;
        _error = null;
      });
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_picture == null) {
      setState(() => _error = 'Choose a JPG or PNG profile picture.');
      return;
    }
    if (!_accepted) {
      setState(() => _error = 'Accept the student terms to continue.');
      return;
    }
    setState(() {
      _saving = true;
      _error = null;
    });
    try {
      await ApiClient.instance.uploadProfile(
        studentNumber: _studentNumber.text.trim(),
        course: _course!,
        yearLevel: _year!,
        section: _section!,
        picture: _picture!,
      );
      if (mounted) widget.onCompleted();
    } catch (error) {
      if (mounted) setState(() => _error = error.toString());
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(
        title: const Text('Complete Student Profile'),
        automaticallyImplyLeading: false,
      ),
      body: SafeArea(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 600),
            child: Form(
              key: _formKey,
              child: ListView(
                padding: const EdgeInsets.all(20),
                children: [
                  Text(
                    'Student information',
                    style: Theme.of(context).textTheme.headlineSmall
                        ?.copyWith(fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    'This account is only for current students. Complete the required details to continue.',
                  ),
                  const SizedBox(height: 18),
                  TextFormField(
                    controller: _studentNumber,
                    decoration: const InputDecoration(
                      labelText: 'Student Number',
                      border: OutlineInputBorder(),
                    ),
                    validator: (v) => v == null || v.trim().isEmpty
                        ? 'Enter your student number.'
                        : null,
                  ),
                  const SizedBox(height: 12),
                  DropdownButtonFormField<String>(
                    initialValue: _course,
                    isExpanded: true,
                    decoration: const InputDecoration(
                      labelText: 'Course / Program',
                      border: OutlineInputBorder(),
                    ),
                    items: courses
                        .map(
                          (v) => DropdownMenuItem(
                            value: v,
                            child: Text(v, overflow: TextOverflow.ellipsis),
                          ),
                        )
                        .toList(),
                    onChanged: (v) => setState(() => _course = v),
                    validator: (v) => v == null ? 'Select your program.' : null,
                  ),
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      Expanded(
                        child: DropdownButtonFormField<String>(
                          initialValue: _year,
                          decoration: const InputDecoration(
                            labelText: 'Year',
                            border: OutlineInputBorder(),
                          ),
                          items: ['1', '2', '3', '4']
                              .map(
                                (v) =>
                                    DropdownMenuItem(value: v, child: Text(v)),
                              )
                              .toList(),
                          onChanged: (v) => setState(() => _year = v),
                          validator: (v) => v == null ? 'Required' : null,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: DropdownButtonFormField<String>(
                          initialValue: _section,
                          decoration: const InputDecoration(
                            labelText: 'Section',
                            border: OutlineInputBorder(),
                          ),
                          items: ['A', 'B', 'C']
                              .map(
                                (v) =>
                                    DropdownMenuItem(value: v, child: Text(v)),
                              )
                              .toList(),
                          onChanged: (v) => setState(() => _section = v),
                          validator: (v) => v == null ? 'Required' : null,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  OutlinedButton.icon(
                    onPressed: _saving ? null : _pickPicture,
                    icon: const Icon(Icons.add_a_photo_outlined),
                    label: Text(
                      _picture?.name ??
                          'Add profile picture (JPG or PNG, max 5 MB)',
                    ),
                  ),
                  if (_picture?.bytes != null)
                    Padding(
                      padding: const EdgeInsets.only(top: 12),
                      child: Center(
                        child: CircleAvatar(
                          radius: 48,
                          backgroundImage: MemoryImage(
                            Uint8List.fromList(_picture!.bytes!),
                          ),
                        ),
                      ),
                    ),
                  const SizedBox(height: 12),
                  CheckboxListTile(
                    value: _accepted,
                    onChanged: _saving
                        ? null
                        : (v) => setState(() => _accepted = v ?? false),
                    contentPadding: EdgeInsets.zero,
                    controlAffinity: ListTileControlAffinity.leading,
                    title: const Text(
                      'I confirm that I am a student and agree to use this account for student document submissions.',
                    ),
                    subtitle: const Text(
                      'Your profile information will be used to associate uploaded documents with your student account.',
                    ),
                    activeColor: scheme.primary,
                  ),
                  if (_error != null)
                    Container(
                      margin: const EdgeInsets.only(bottom: 12),
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: scheme.errorContainer,
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Text(
                        _error!,
                        textAlign: TextAlign.center,
                        style: TextStyle(color: scheme.onErrorContainer),
                      ),
                    ),
                  SizedBox(
                    height: 50,
                    child: FilledButton(
                      onPressed: _saving ? null : _submit,
                      child: _saving
                          ? const SizedBox(
                              width: 22,
                              height: 22,
                              child: CircularProgressIndicator(strokeWidth: 2),
                            )
                          : const Text('Save and Continue'),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
