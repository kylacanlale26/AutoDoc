
import 'package:flutter/material.dart';

class DocumentItem {
  final String name;
  final String date;
  final String status;

  DocumentItem({
    required this.name,
    required this.date,
    required this.status,
  });
}

class MyDocuments extends StatefulWidget {
  final List<DocumentItem> documents;

  const MyDocuments({
    super.key,
    this.documents = const [],
  });

  @override
  State<MyDocuments> createState() => _MyDocumentsState();
}

class _MyDocumentsState extends State<MyDocuments> {
  final TextEditingController searchController = TextEditingController();

  String selectedFilter = 'All';

  List<DocumentItem> get filteredDocuments {
    return widget.documents.where((document) {
      bool matchesSearch = document.name
          .toLowerCase()
          .contains(searchController.text.toLowerCase());

      bool matchesFilter = selectedFilter == 'All' ||
          document.status == selectedFilter;

      return matchesSearch && matchesFilter;
    }).toList();
  }

  @override
  void dispose() {
    searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Column(
          children: [

            Expanded(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 15),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 12),

                    const Text(
                      'My Documents',
                      style: TextStyle(
                        fontSize: 29,
                        fontWeight: FontWeight.bold,
                        color: Colors.black,
                      ),
                    ),

                    const SizedBox(height: 10),

                    Row(
                      children: [
                        Expanded(
                          child: SizedBox(
                            height: 40,
                            child: TextField(
                              controller: searchController,
                              onChanged: (value) {
                                setState(() {});
                              },
                              decoration: InputDecoration(
                                hintText: 'Search Document',
                                hintStyle: const TextStyle(
                                  fontSize: 15,
                                  letterSpacing: 1,
                                ),
                                prefixIcon: const Icon(
                                  Icons.search,
                                  color: Colors.black,
                                ),
                                contentPadding:
                                    const EdgeInsets.symmetric(vertical: 8),
                                border: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(10),
                                  borderSide: const BorderSide(
                                    color: Colors.black,
                                  ),
                                ),
                                enabledBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(10),
                                  borderSide: const BorderSide(
                                    color: Colors.black,
                                  ),
                                ),
                                focusedBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(10),
                                  borderSide: const BorderSide(
                                    color: Colors.black,
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ),

                        const SizedBox(width: 5),

                        PopupMenuButton<String>(
                          icon: const Icon(
                            Icons.filter_alt_outlined,
                            color: Colors.black,
                            size: 27,
                          ),
                          onSelected: (value) {
                            setState(() {
                              selectedFilter = value;
                            });
                          },
                          itemBuilder: (context) => [
                            const PopupMenuItem(
                              value: 'All',
                              child: Text('All'),
                            ),
                            const PopupMenuItem(
                              value: 'Verified',
                              child: Text('Verified'),
                            ),
                            const PopupMenuItem(
                              value: 'Pending',
                              child: Text('Pending'),
                            ),
                            const PopupMenuItem(
                              value: 'Needs Correction',
                              child: Text('Needs Correction'),
                            ),
                          ],
                        ),
                      ],
                    ),

                    const SizedBox(height: 8),

                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        children: [
                          'All',
                          'Verified',
                          'Pending',
                          'Needs Correction',
                        ].map((filter) {
                          return Padding(
                            padding: const EdgeInsets.only(right: 5),
                            child: GestureDetector(
                              onTap: () {
                                setState(() {
                                  selectedFilter = filter;
                                });
                              },
                              child: Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 10,
                                  vertical: 3,
                                ),
                                decoration: BoxDecoration(
                                  color: selectedFilter == filter
                                      ? const Color(0xFFCCCCCC)
                                      : const Color(0xFFE5E5E5),
                                  borderRadius: BorderRadius.circular(20),
                                ),
                                child: Text(
                                  filter,
                                  style: const TextStyle(
                                    fontSize: 12,
                                    color: Colors.black,
                                  ),
                                ),
                              ),
                            ),
                          );
                        }).toList(),
                      ),
                    ),

                    const SizedBox(height: 15),

                    Expanded(
                      child: filteredDocuments.isEmpty
                          ? Center(
                              child: Text(
                                widget.documents.isEmpty
                                    ? 'No Document Uploaded'
                                    : 'No matching documents found.',
                                style: const TextStyle(
                                  fontSize: 15,
                                  color: Colors.grey,
                                ),
                              ),
                            )
                          : ListView.builder(
                              itemCount: filteredDocuments.length,
                              itemBuilder: (context, index) {
                                final document = filteredDocuments[index];

                                return ListTile(
                                  contentPadding: const EdgeInsets.symmetric(
                                    horizontal: 6,
                                  ),
                                  title: Text(
                                    document.name,
                                    style: const TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                      color: Colors.black,
                                    ),
                                  ),
                                  subtitle: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        document.date,
                                        style: const TextStyle(
                                          fontSize: 10,
                                          color: Colors.black,
                                        ),
                                      ),
                                      Text(
                                        document.status,
                                        style: const TextStyle(
                                          fontSize: 10,
                                          color: Colors.black,
                                        ),
                                      ),
                                    ],
                                  ),
                                  trailing: PopupMenuButton<String>(
                                    icon: const Icon(
                                      Icons.more_vert,
                                      color: Colors.black,
                                      size: 18,
                                    ),
                                    onSelected: (value) {
                                      if (value == 'View') {
                                        // Open document
                                      } else if (value == 'Delete') {
                                        // Delete document
                                      }
                                    },
                                    itemBuilder: (context) => [
                                      const PopupMenuItem(
                                        value: 'View',
                                        child: Text('View'),
                                      ),
                                      const PopupMenuItem(
                                        value: 'Delete',
                                        child: Text('Delete'),
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
          ],
        ),
      ),
    );
  }
}
