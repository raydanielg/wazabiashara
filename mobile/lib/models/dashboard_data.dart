class DashboardData {
  final double todaySales;
  final double monthSales;
  final int totalProducts;
  final int lowStockCount;
  final int totalCustomers;
  final double cashBalance;
  final double bankBalance;
  final double mobileBalance;
  final List<RecentSale> recentSales;
  final List<TopProduct> topProducts;
  final List<ChartData> salesChart;

  // Home-screen summary fields (matches the "To Receive / To Give / Sales /
  // Purchase / Expense / Total Balance" cards shown on the Home tab).
  final double toReceive;
  final double toGive;
  final double monthPurchases;
  final double monthExpenses;
  final List<ChartData> cashflowIn;
  final List<ChartData> cashflowOut;

  // Per-metric 7-day trend series, used by the tappable KPI detail view
  // (kpi_detail_screen.dart) to draw a proper line chart per card.
  final List<ChartData> purchasesChart;
  final List<ChartData> expensesChart;

  double get totalBalance => cashBalance + bankBalance + mobileBalance;

  DashboardData({
    required this.todaySales,
    required this.monthSales,
    required this.totalProducts,
    required this.lowStockCount,
    required this.totalCustomers,
    required this.cashBalance,
    required this.bankBalance,
    required this.mobileBalance,
    required this.recentSales,
    required this.topProducts,
    required this.salesChart,
    this.toReceive = 0,
    this.toGive = 0,
    this.monthPurchases = 0,
    this.monthExpenses = 0,
    this.cashflowIn = const [],
    this.cashflowOut = const [],
    this.purchasesChart = const [],
    this.expensesChart = const [],
  });

  factory DashboardData.fromJson(Map<String, dynamic> json) {
    return DashboardData(
      todaySales: (json['today_sales'] as num?)?.toDouble() ?? 0,
      monthSales: (json['month_sales'] as num?)?.toDouble() ?? 0,
      totalProducts: (json['total_products'] as num?)?.toInt() ?? 0,
      lowStockCount: (json['low_stock_count'] as num?)?.toInt() ?? 0,
      totalCustomers: (json['total_customers'] as num?)?.toInt() ?? 0,
      cashBalance: (json['cash_balance'] as num?)?.toDouble() ?? 0,
      bankBalance: (json['bank_balance'] as num?)?.toDouble() ?? 0,
      mobileBalance: (json['mobile_balance'] as num?)?.toDouble() ?? 0,
      recentSales: (json['recent_sales'] as List?)
              ?.map((e) => RecentSale.fromJson(e))
              .toList() ??
          [],
      topProducts: (json['top_products'] as List?)
              ?.map((e) => TopProduct.fromJson(e))
              .toList() ??
          [],
      salesChart: (json['sales_chart'] as List?)
              ?.map((e) => ChartData.fromJson(e))
              .toList() ??
          [],
      toReceive: (json['total_receivables'] as num?)?.toDouble() ?? 0,
      toGive: (json['total_payables'] as num?)?.toDouble() ?? 0,
      monthPurchases: (json['month_purchases'] as num?)?.toDouble() ?? 0,
      monthExpenses: (json['month_expenses'] as num?)?.toDouble() ?? 0,
      cashflowIn: (json['cashflow_in'] as List?)
              ?.map((e) => ChartData.fromJson(e))
              .toList() ??
          [],
      cashflowOut: (json['cashflow_out'] as List?)
              ?.map((e) => ChartData.fromJson(e))
              .toList() ??
          [],
      purchasesChart: (json['purchases_chart'] as List?)
              ?.map((e) => ChartData.fromJson(e))
              .toList() ??
          [],
      expensesChart: (json['expenses_chart'] as List?)
              ?.map((e) => ChartData.fromJson(e))
              .toList() ??
          [],
    );
  }
}

class RecentSale {
  final int id;
  final String receiptNo;
  final double total;
  final String paymentMethod;
  final DateTime? date;

  RecentSale({
    required this.id,
    required this.receiptNo,
    required this.total,
    required this.paymentMethod,
    this.date,
  });

  factory RecentSale.fromJson(Map<String, dynamic> json) {
    return RecentSale(
      id: json['id'] as int,
      receiptNo: json['receipt_no'] as String? ?? '',
      total: (json['total'] as num?)?.toDouble() ?? 0,
      paymentMethod: json['payment_method'] as String? ?? 'cash',
      date: json['date'] != null ? DateTime.parse(json['date']) : null,
    );
  }
}

class TopProduct {
  final int id;
  final String name;
  final int qtySold;
  final double revenue;

  TopProduct({
    required this.id,
    required this.name,
    required this.qtySold,
    required this.revenue,
  });

  factory TopProduct.fromJson(Map<String, dynamic> json) {
    return TopProduct(
      id: json['id'] as int,
      name: json['name'] as String? ?? '',
      qtySold: (json['qty_sold'] as num?)?.toInt() ?? 0,
      revenue: (json['revenue'] as num?)?.toDouble() ?? 0,
    );
  }
}

class ChartData {
  final String label;
  final double value;

  ChartData({required this.label, required this.value});

  factory ChartData.fromJson(Map<String, dynamic> json) {
    return ChartData(
      label: json['label'] as String? ?? '',
      value: (json['value'] as num?)?.toDouble() ?? 0,
    );
  }
}
