/// Cash / bank / mobile-money account, matching app/Models/Account.php.
class Account {
  final int id;
  final String name;
  final String type; // cash | bank | mobile_money
  final String? bankName;
  final String? accountNumber;
  final String? phoneNumber;
  final double openingBalance;
  final double currentBalance;
  final bool isActive;

  Account({
    required this.id,
    required this.name,
    required this.type,
    this.bankName,
    this.accountNumber,
    this.phoneNumber,
    this.openingBalance = 0,
    this.currentBalance = 0,
    this.isActive = true,
  });

  factory Account.fromJson(Map<String, dynamic> json) {
    return Account(
      id: json['id'] as int,
      name: json['name'] as String? ?? '',
      type: json['type'] as String? ?? 'cash',
      bankName: json['bank_name'] as String?,
      accountNumber: json['account_number'] as String?,
      phoneNumber: json['phone_number'] as String?,
      openingBalance: (json['opening_balance'] as num?)?.toDouble() ?? 0,
      currentBalance: (json['current_balance'] as num?)?.toDouble() ?? 0,
      isActive: (json['is_active'] as bool?) ?? true,
    );
  }
}
