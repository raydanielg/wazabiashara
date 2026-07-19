class Customer {
  final int id;
  final String name;
  final String? phone;
  final String? email;
  final String? address;
  final double openingBalance;
  final double? creditLimit;
  final double currentDebt;
  final String status;

  Customer({
    required this.id,
    required this.name,
    this.phone,
    this.email,
    this.address,
    this.openingBalance = 0,
    this.creditLimit,
    this.currentDebt = 0,
    this.status = 'active',
  });

  factory Customer.fromJson(Map<String, dynamic> json) {
    return Customer(
      id: json['id'] as int,
      name: json['name'] as String,
      phone: json['phone'] as String?,
      email: json['email'] as String?,
      address: json['address'] as String?,
      openingBalance: (json['opening_balance'] as num?)?.toDouble() ?? 0,
      creditLimit: (json['credit_limit'] as num?)?.toDouble(),
      currentDebt: (json['current_debt'] as num?)?.toDouble() ?? 0,
      status: json['status'] as String? ?? 'active',
    );
  }

  bool get hasDebt => currentDebt > 0;
  bool get hasCreditLimit => creditLimit != null && creditLimit! > 0;
}
