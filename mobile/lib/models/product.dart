class Product {
  final int id;
  final String name;
  final String? barcode;
  final String? sku;
  final String? image;
  final String unit;
  final double costPrice;
  final double sellingPrice;
  final double? wholesalePrice;
  final int stock;
  final int reorderLevel;
  final String? category;
  final String? expiryDate;
  final String status;

  Product({
    required this.id,
    required this.name,
    this.barcode,
    this.sku,
    this.image,
    this.unit = 'piece',
    required this.costPrice,
    required this.sellingPrice,
    this.wholesalePrice,
    required this.stock,
    this.reorderLevel = 5,
    this.category,
    this.expiryDate,
    this.status = 'active',
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'] as int,
      name: json['name'] as String,
      barcode: json['barcode'] as String?,
      sku: json['sku'] as String?,
      image: json['image'] as String?,
      unit: json['unit'] as String? ?? 'piece',
      costPrice: (json['cost_price'] as num?)?.toDouble() ?? 0,
      sellingPrice: (json['selling_price'] as num?)?.toDouble() ?? 0,
      wholesalePrice: (json['wholesale_price'] as num?)?.toDouble(),
      stock: (json['stock'] as num?)?.toInt() ?? 0,
      reorderLevel: (json['reorder_level'] as num?)?.toInt() ?? 5,
      category: json['category'] as String?,
      expiryDate: json['expiry_date'] as String?,
      status: json['status'] as String? ?? 'active',
    );
  }

  bool get isLowStock => stock <= reorderLevel;
  bool get isOutOfStock => stock <= 0;
  double get profit => sellingPrice - costPrice;
  double get profitMargin => costPrice > 0 ? (profit / costPrice) * 100 : 0;
}
