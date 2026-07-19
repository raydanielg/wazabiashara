import 'package:flutter/material.dart';
import '../models/dashboard_data.dart';
import '../services/api_service.dart';

class DashboardProvider extends ChangeNotifier {
  final _api = ApiService();

  DashboardData? _data;
  bool _isLoading = false;
  bool _isRefreshing = false;
  String? _error;

  DashboardData? get data => _data;
  bool get isLoading => _isLoading;
  bool get isRefreshing => _isRefreshing;
  String? get error => _error;

  Future<void> fetchDashboard() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final res = await _api.getDashboard();
      if (res.statusCode == 200 && res.data['success'] == true) {
        _data = DashboardData.fromJson(res.data['data']);
      } else {
        _data = _sampleData();
      }
    } catch (e) {
      _data = _sampleData();
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<void> refresh() async {
    _isRefreshing = true;
    notifyListeners();

    try {
      final res = await _api.getDashboard();
      if (res.statusCode == 200 && res.data['success'] == true) {
        _data = DashboardData.fromJson(res.data['data']);
      }
    } catch (_) {}

    _isRefreshing = false;
    notifyListeners();
  }

  DashboardData _sampleData() {
    return DashboardData(
      todaySales: 450000,
      monthSales: 12500000,
      totalProducts: 248,
      lowStockCount: 12,
      totalCustomers: 86,
      cashBalance: 1250000,
      bankBalance: 3400000,
      mobileBalance: 890000,
      recentSales: [
        RecentSale(id: 1, receiptNo: 'RCP-1001', total: 15000, paymentMethod: 'cash', date: DateTime.now().subtract(const Duration(hours: 1))),
        RecentSale(id: 2, receiptNo: 'RCP-1002', total: 22500, paymentMethod: 'm-pesa', date: DateTime.now().subtract(const Duration(hours: 2))),
        RecentSale(id: 3, receiptNo: 'RCP-1003', total: 8500, paymentMethod: 'cash', date: DateTime.now().subtract(const Duration(hours: 3))),
        RecentSale(id: 4, receiptNo: 'RCP-1004', total: 42000, paymentMethod: 'm-pesa', date: DateTime.now().subtract(const Duration(hours: 5))),
        RecentSale(id: 5, receiptNo: 'RCP-1005', total: 18000, paymentMethod: 'cash', date: DateTime.now().subtract(const Duration(hours: 8))),
      ],
      topProducts: [
        TopProduct(id: 1, name: 'Soda 500ml', qtySold: 120, revenue: 360000),
        TopProduct(id: 2, name: 'Rice 1kg', qtySold: 85, revenue: 212500),
        TopProduct(id: 3, name: 'Cooking Oil 1L', qtySold: 64, revenue: 320000),
        TopProduct(id: 4, name: 'Sugar 1kg', qtySold: 52, revenue: 156000),
        TopProduct(id: 5, name: 'Bread', qtySold: 45, revenue: 67500),
      ],
      salesChart: [
        ChartData(label: 'Mon', value: 320000),
        ChartData(label: 'Tue', value: 450000),
        ChartData(label: 'Wed', value: 380000),
        ChartData(label: 'Thu', value: 520000),
        ChartData(label: 'Fri', value: 680000),
        ChartData(label: 'Sat', value: 750000),
        ChartData(label: 'Sun', value: 420000),
      ],
      toReceive: 620000,
      toGive: 245000,
      monthPurchases: 4200000,
      monthExpenses: 980000,
      cashflowIn: [
        ChartData(label: 'Mon', value: 320000),
        ChartData(label: 'Tue', value: 450000),
        ChartData(label: 'Wed', value: 380000),
        ChartData(label: 'Thu', value: 520000),
        ChartData(label: 'Fri', value: 680000),
        ChartData(label: 'Sat', value: 750000),
        ChartData(label: 'Sun', value: 420000),
      ],
      cashflowOut: [
        ChartData(label: 'Mon', value: 180000),
        ChartData(label: 'Tue', value: 210000),
        ChartData(label: 'Wed', value: 150000),
        ChartData(label: 'Thu', value: 260000),
        ChartData(label: 'Fri', value: 300000),
        ChartData(label: 'Sat', value: 340000),
        ChartData(label: 'Sun', value: 190000),
      ],
    );
  }
}
