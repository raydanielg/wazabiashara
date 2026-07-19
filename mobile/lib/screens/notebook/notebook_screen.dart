import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/note.dart';
import '../../services/api_service.dart';
import '../../widgets/loading_widget.dart';

/// Matches the reference "Notebook" screen: a sticky-note style grid with
/// an empty state ("No notes here yet!") and a floating add action.
class NotebookScreen extends StatefulWidget {
  /// When true (used by the Home screen's "Add Note" shortcut), the "New
  /// Note" sheet opens immediately instead of requiring an extra tap.
  final bool autoAdd;
  const NotebookScreen({super.key, this.autoAdd = false});

  @override
  State<NotebookScreen> createState() => _NotebookScreenState();
}

class _NotebookScreenState extends State<NotebookScreen> {
  final _api = ApiService();
  List<AppNote> _notes = [];
  bool _isLoading = true;

  static const _colors = {
    'gold': AppColors.gold,
    'emerald': AppColors.primary,
    'sky': AppColors.info,
    'rose': AppColors.error,
  };

  @override
  void initState() {
    super.initState();
    _load();
    if (widget.autoAdd) {
      WidgetsBinding.instance.addPostFrameCallback((_) => _editNote());
    }
  }

  Future<void> _load() async {
    setState(() => _isLoading = true);
    try {
      final res = await _api.getNotes();
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? [];
        setState(() => _notes = list.map((e) => AppNote.fromJson(e)).toList());
      }
    } catch (_) {
      // Backend endpoint not live yet — keep whatever notes exist locally.
    }
    setState(() => _isLoading = false);
  }

  Future<void> _editNote({AppNote? existing}) async {
    final titleCtrl = TextEditingController(text: existing?.title ?? '');
    final contentCtrl = TextEditingController(text: existing?.content ?? '');
    String color = existing?.color ?? 'gold';

    final saved = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setSheetState) => Padding(
          padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: Container(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(existing == null ? 'New Note' : 'Edit Note', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
                const SizedBox(height: 20),
                TextField(controller: titleCtrl, autofocus: true, decoration: const InputDecoration(labelText: 'Title')),
                const SizedBox(height: 12),
                TextField(controller: contentCtrl, maxLines: 5, decoration: const InputDecoration(labelText: 'Note')),
                const SizedBox(height: 16),
                Row(
                  children: _colors.keys.map((c) {
                    final selected = color == c;
                    return Padding(
                      padding: const EdgeInsets.only(right: 10),
                      child: InkWell(
                        onTap: () => setSheetState(() => color = c),
                        borderRadius: BorderRadius.circular(20),
                        child: Container(
                          width: 32,
                          height: 32,
                          decoration: BoxDecoration(
                            color: _colors[c],
                            shape: BoxShape.circle,
                            border: selected ? Border.all(color: AppColors.textPrimary, width: 2) : null,
                          ),
                        ),
                      ),
                    );
                  }).toList(),
                ),
                const SizedBox(height: 24),
                ElevatedButton(
                  onPressed: () => Navigator.pop(ctx, true),
                  child: Text(existing == null ? 'Create Note' : 'Save Changes'),
                ),
              ],
            ),
          ),
        ),
      ),
    );

    if (saved != true) return;
    final data = {'title': titleCtrl.text.trim(), 'content': contentCtrl.text.trim(), 'color': color};

    try {
      if (existing == null) {
        await _api.createNote(data);
      } else {
        await _api.updateNote(existing.id, data);
      }
    } catch (_) {}

    setState(() {
      if (existing == null) {
        _notes.insert(0, AppNote(id: DateTime.now().millisecondsSinceEpoch, title: data['title'], content: data['content'], color: color));
      } else {
        final i = _notes.indexWhere((n) => n.id == existing.id);
        if (i >= 0) _notes[i] = AppNote(id: existing.id, title: data['title'], content: data['content'], color: color, pinned: existing.pinned);
      }
    });
  }

  Future<void> _deleteNote(AppNote note) async {
    try {
      await _api.deleteNote(note.id);
    } catch (_) {}
    setState(() => _notes.removeWhere((n) => n.id == note.id));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Notebook'),
        actions: [IconButton(onPressed: () {}, icon: const Icon(Icons.search))],
      ),
      floatingActionButton: _notes.isEmpty
          ? null
          : FloatingActionButton(onPressed: () => _editNote(), backgroundColor: AppColors.primary, child: const Icon(Icons.add)),
      body: _isLoading
          ? const LoadingWidget()
          : _notes.isEmpty
              ? Padding(
                  padding: const EdgeInsets.all(32),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const SizedBox(height: 60),
                      Container(
                        width: 100,
                        height: 100,
                        decoration: BoxDecoration(color: AppColors.divider.withValues(alpha: 0.4), shape: BoxShape.circle),
                        child: const Icon(Icons.note_alt_outlined, size: 44, color: AppColors.textHint),
                      ),
                      const SizedBox(height: 24),
                      const Text('No notes here yet!', style: TextStyle(fontSize: 17, fontWeight: FontWeight.w800)),
                      const SizedBox(height: 8),
                      const Text(
                        'Keep track of your ideas, tasks, and business notes all in one place.',
                        textAlign: TextAlign.center,
                        style: TextStyle(fontSize: 13, color: AppColors.textSecondary),
                      ),
                      const SizedBox(height: 24),
                      ElevatedButton.icon(
                        onPressed: () => _editNote(),
                        icon: const Icon(Icons.note_add_outlined),
                        label: const Text('Create Your First Note'),
                      ),
                    ],
                  ),
                )
              : GridView.builder(
                  padding: const EdgeInsets.all(16),
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(crossAxisCount: 2, mainAxisSpacing: 12, crossAxisSpacing: 12, childAspectRatio: 0.9),
                  itemCount: _notes.length,
                  itemBuilder: (ctx, i) {
                    final n = _notes[i];
                    final color = _colors[n.color] ?? AppColors.gold;
                    return InkWell(
                      onTap: () => _editNote(existing: n),
                      onLongPress: () => _deleteNote(n),
                      borderRadius: BorderRadius.circular(14),
                      child: Container(
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(14), border: Border.all(color: color.withValues(alpha: 0.3))),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(n.title?.isNotEmpty == true ? n.title! : 'Untitled', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14), maxLines: 1, overflow: TextOverflow.ellipsis),
                            const SizedBox(height: 6),
                            Expanded(
                              child: Text(n.content ?? '', style: const TextStyle(fontSize: 12, color: AppColors.textSecondary), maxLines: 6, overflow: TextOverflow.ellipsis),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
    );
  }
}
