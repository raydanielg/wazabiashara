class Business {
  final int id;
  final String name;
  final String? type;
  final String? region;
  final String? logo;
  final String? phone;
  final String? email;
  final String? address;
  final String? currency;
  final bool darkMode;
  final String status;

  Business({
    required this.id,
    required this.name,
    this.type,
    this.region,
    this.logo,
    this.phone,
    this.email,
    this.address,
    this.currency = 'TZS',
    this.darkMode = false,
    this.status = 'active',
  });

  factory Business.fromJson(Map<String, dynamic> json) {
    return Business(
      id: json['id'] as int,
      name: json['name'] as String,
      type: json['type'] as String?,
      region: json['region'] as String?,
      logo: json['logo'] as String?,
      phone: json['phone'] as String?,
      email: json['email'] as String?,
      address: json['address'] as String?,
      currency: json['currency'] as String? ?? 'TZS',
      darkMode: json['dark_mode'] as bool? ?? false,
      status: json['status'] as String? ?? 'active',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'type': type,
      'region': region,
      'logo': logo,
      'phone': phone,
      'email': email,
      'address': address,
      'currency': currency,
      'dark_mode': darkMode,
      'status': status,
    };
  }
}
