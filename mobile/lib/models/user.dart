class User {
  final int id;
  final String name;
  final String phone;
  final String? email;
  final String role;
  final int? businessId;
  final int? branchId;
  final String? avatar;
  final String status;

  User({
    required this.id,
    required this.name,
    required this.phone,
    this.email,
    required this.role,
    this.businessId,
    this.branchId,
    this.avatar,
    this.status = 'active',
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] as int,
      name: json['name'] as String,
      phone: json['phone'] as String,
      email: json['email'] as String?,
      role: json['role'] as String? ?? 'cashier',
      businessId: json['business_id'] as int?,
      branchId: json['branch_id'] as int?,
      avatar: json['avatar'] as String?,
      status: json['status'] as String? ?? 'active',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'phone': phone,
      'email': email,
      'role': role,
      'business_id': businessId,
      'branch_id': branchId,
      'avatar': avatar,
      'status': status,
    };
  }

  bool get isOwner => role == 'owner';
  bool get isAdmin => role == 'admin' || role == 'owner';
  bool get isManager => role == 'manager' || isAdmin;
  bool get isCashier => role == 'cashier';
}
