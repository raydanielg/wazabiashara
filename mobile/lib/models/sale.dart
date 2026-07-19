class Sale {
  final int id;
  final String receiptNo;
  final double subtotal;
  final double discount;
  final double total;
  final double paid;
  final double change;
  final String paymentMethod;
  final String status;
  final String? customerName;
  final DateTime? date;
  final List<SaleItem>? items;

  Sale({
    required this.id,
    required this.receiptNo,
    required this.subtotal,
    required this.discount,
    required this.total,
    required this.paid,
    required this.change,
    required this.paymentMethod,
    this.status = 'completed',
    this.customerName,
    this.date,
    this.items,
  });

  factory Sale.fromJson(Map<String, dynamic> json) {
    return Sale(
      id: json['id'] as int,
      receiptNo: json['receipt_no'] as String? ?? '',
      subtotal: (json['subtotal'] as num?)?.toDouble() ?? 0,
      discount: (json['discount'] as num?)?.toDouble() ?? 0,
      total: (json['total'] as num?)?.toDouble() ?? 0,
      paid: (json['paid'] as num?)?.toDouble() ?? 0,
      change: (json['change'] as num?)?.toDouble() ?? 0,
      paymentMethod: json['payment_method'] as String? ?? 'cash',
      status: json['status'] as String? ?? 'completed',
      customerName: json['customer_name'] as String?,
      date: json['date'] != null ? DateTime.parse(json['date']) : null,
      items: (json['items'] as List?)?.map((e) => SaleItem.fromJson(e)).toList(),
    );
  }
}

class SaleItem {
  final int productId;
  final String name;
  final int qty;
  final double price;
  final double subtotal;

  SaleItem({
    required this.productId,
    required this.name,
    required this.qty,
    required this.price,
    required this.subtotal,
  });

  factory SaleItem.fromJson(Map<String, dynamic> json) {
    return SaleItem(
      productId: json['product_id'] as int,
      name: json['name'] as String? ?? '',
      qty: (json['qty'] as num?)?.toInt() ?? 1,
      price: (json['price'] as num?)?.toDouble() ?? 0,
      subtotal: (json['subtotal'] as num?)?.toDouble() ?? 0,
    );
  }
}
