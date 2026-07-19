/// A notebook entry, matching app/Models/Note.php.
class AppNote {
  final int id;
  final String? title;
  final String? content;
  final String color;
  final bool pinned;

  AppNote({
    required this.id,
    this.title,
    this.content,
    this.color = 'gold',
    this.pinned = false,
  });

  factory AppNote.fromJson(Map<String, dynamic> json) {
    return AppNote(
      id: json['id'] as int,
      title: json['title'] as String?,
      content: json['content'] as String?,
      color: json['color'] as String? ?? 'gold',
      pinned: (json['pinned'] as bool?) ?? false,
    );
  }
}
