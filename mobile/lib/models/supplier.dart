/// Supplier, matching app/Models/Supplier.php — used by the Purchase quick-add
/// flow (SupplierController::storePurchase requires a real supplier_id).
class Supplier {
  final int id;
  final String name;
  final String? phone;
  final String? email;
  final double balance;

  Supplier({
    required this.id,
    required this.name,
    this.phone,
    this.email,
    this.balance = 0,
  });

  factory Supplier.fromJson(Map<String, dynamic> json) {
    return Supplier(
      id: json['id'] as int,
      name: json['name'] as String? ?? '',
      phone: json['phone'] as String?,
      email: json['email'] as String?,
      balance: (json['balance'] as num?)?.toDouble() ?? 0,
    );
  }
}
