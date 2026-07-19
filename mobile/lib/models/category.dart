/// Party / Item / Expense / Income category, matching the `categories`
/// table on the backend (see app/Models/Category.php — `type` column added
/// alongside the existing item-category support).
class AppCategory {
  final int id;
  final String name;
  final String type; // item | party | expense | income
  final int? parentId;
  final String? icon;
  final String? description;

  AppCategory({
    required this.id,
    required this.name,
    required this.type,
    this.parentId,
    this.icon,
    this.description,
  });

  factory AppCategory.fromJson(Map<String, dynamic> json) {
    return AppCategory(
      id: json['id'] as int,
      name: json['name'] as String? ?? '',
      type: json['type'] as String? ?? 'item',
      parentId: json['parent_id'] as int?,
      icon: json['icon'] as String?,
      description: json['description'] as String?,
    );
  }

  Map<String, dynamic> toJson() => {
        'name': name,
        'type': type,
        'parent_id': parentId,
        'icon': icon,
        'description': description,
      };
}
