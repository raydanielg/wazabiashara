/// A selectable business category (Retail Shop, Restaurant, Pharmacy, ...),
/// matching app/Models/BusinessType.php — used by the Business Setup screen.
class BusinessType {
  final int id;
  final String name;
  final String slug;
  final String? icon;

  BusinessType({required this.id, required this.name, required this.slug, this.icon});

  factory BusinessType.fromJson(Map<String, dynamic> json) {
    return BusinessType(
      id: json['id'] as int,
      name: json['name'] as String? ?? '',
      slug: json['slug'] as String? ?? '',
      icon: json['icon'] as String?,
    );
  }
}
