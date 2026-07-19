import 'dart:convert';
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../config/app_config.dart';

class ApiService {
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;

  late final Dio _dio;
  final _storage = const FlutterSecureStorage();

  ApiService._internal() {
    _dio = Dio(BaseOptions(
      baseUrl: AppConfig.baseUrl + AppConfig.apiVersion,
      connectTimeout: AppConfig.apiTimeout,
      receiveTimeout: AppConfig.apiTimeout,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ));

    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _storage.read(key: AppConfig.tokenKey);
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
      onError: (error, handler) {
        if (error.response?.statusCode == 401) {
          _handleUnauthorized();
        }
        handler.next(error);
      },
    ));
  }

  Dio get dio => _dio;

  Future<void> _handleUnauthorized() async {
    await _storage.delete(key: AppConfig.tokenKey);
    await _storage.delete(key: AppConfig.userKey);
  }

  // Auth endpoints
  Future<Response> login(String email, String password) async {
    return await _dio.post('/login', data: {
      'email': email,
      'password': password,
    });
  }

  Future<Response> register(Map<String, dynamic> data) async {
    return await _dio.post('/register', data: data);
  }

  Future<Response> verifyOtp(String phone, String otp) async {
    return await _dio.post('/verify-otp', data: {
      'phone': phone,
      'otp': otp,
    });
  }

  Future<Response> resendOtp(String phone) async {
    return await _dio.post('/resend-otp', data: {'phone': phone});
  }

  Future<Response> forgotPassword(String email) async {
    return await _dio.post('/forgot-password', data: {'email': email});
  }

  Future<Response> resetPassword(String token, String password, String passwordConfirmation) async {
    return await _dio.post('/reset-password', data: {
      'token': token,
      'password': password,
      'password_confirmation': passwordConfirmation,
    });
  }

  Future<Response> getMe() async {
    return await _dio.get('/me');
  }

  Future<Response> updateMe(Map<String, dynamic> data) async {
    return await _dio.put('/me', data: data);
  }

  Future<Response> changePassword({
    required String currentPassword,
    required String password,
    required String passwordConfirmation,
  }) async {
    return await _dio.post('/change-password', data: {
      'current_password': currentPassword,
      'password': password,
      'password_confirmation': passwordConfirmation,
    });
  }

  Future<void> logout() async {
    try {
      await _dio.post('/logout');
    } finally {
      await _storage.delete(key: AppConfig.tokenKey);
      await _storage.delete(key: AppConfig.userKey);
    }
  }

  // Dashboard endpoints
  Future<Response> getDashboard() async {
    return await _dio.get('/dashboard');
  }

  // Products endpoints
  Future<Response> getProducts({int page = 1, String? search}) async {
    final params = <String, dynamic>{'page': page, 'per_page': AppConfig.pageSize};
    if (search != null) params['search'] = search;
    return await _dio.get('/products', queryParameters: params);
  }

  Future<Response> getProduct(int id) async {
    return await _dio.get('/products/$id');
  }

  Future<Response> createProduct(Map<String, dynamic> data) async {
    return await _dio.post('/products', data: data);
  }

  Future<Response> updateProduct(int id, Map<String, dynamic> data) async {
    return await _dio.put('/products/$id', data: data);
  }

  Future<Response> deleteProduct(int id) async {
    return await _dio.delete('/products/$id');
  }

  // Sales / POS endpoints
  Future<Response> checkout(Map<String, dynamic> data) async {
    return await _dio.post('/sales/checkout', data: data);
  }

  Future<Response> getSales({int page = 1}) async {
    return await _dio.get('/sales', queryParameters: {
      'page': page,
      'per_page': AppConfig.pageSize,
    });
  }

  Future<Response> getSale(int id) async {
    return await _dio.get('/sales/$id');
  }

  // Customers endpoints
  Future<Response> getCustomers({int page = 1, String? search}) async {
    final params = <String, dynamic>{'page': page, 'per_page': AppConfig.pageSize};
    if (search != null) params['search'] = search;
    return await _dio.get('/customers', queryParameters: params);
  }

  // Reports endpoints
  Future<Response> getReports({String? period}) async {
    final params = <String, dynamic>{};
    if (period != null) params['period'] = period;
    return await _dio.get('/reports/chart-data', queryParameters: params);
  }

  Future<Response> getSalesReport({String? from, String? to}) async {
    final params = <String, dynamic>{};
    if (from != null) params['from'] = from;
    if (to != null) params['to'] = to;
    return await _dio.get('/reports/sales', queryParameters: params);
  }

  // Profile endpoints (kept as thin aliases over /business/profile — the
  // only profile endpoint the backend actually exposes)
  Future<Response> getProfile() async {
    return await getBusinessProfile();
  }

  Future<Response> updateProfile(Map<String, dynamic> data) async {
    return await updateBusinessProfile(data);
  }

  // Business setup (runs once, right after register/first login, when the
  // signed-in user has no business_id yet — see BusinessSetupScreen).
  Future<Response> getBusinessTypes() async {
    return await _dio.get('/business/types');
  }

  Future<Response> registerBusiness(Map<String, dynamic> data) async {
    return await _dio.post('/business/register', data: data);
  }

  Future<Response> getBusinessProfile() async {
    return await _dio.get('/business/profile');
  }

  Future<Response> updateBusinessProfile(Map<String, dynamic> data) async {
    return await _dio.put('/business/profile', data: data);
  }

  // Category endpoints (type: item | party | expense | income)
  Future<Response> getCategories(String type) async {
    return await _dio.get('/categories', queryParameters: {'type': type});
  }

  Future<Response> createCategory(Map<String, dynamic> data) async {
    return await _dio.post('/categories', data: data);
  }

  Future<Response> updateCategory(int id, Map<String, dynamic> data) async {
    return await _dio.put('/categories/$id', data: data);
  }

  Future<Response> deleteCategory(int id) async {
    return await _dio.delete('/categories/$id');
  }

  // Cash & Bank account endpoints
  Future<Response> getAccounts() async {
    return await _dio.get('/cash-flow/accounts');
  }

  Future<Response> createAccount(Map<String, dynamic> data) async {
    return await _dio.post('/cash-flow/accounts', data: data);
  }

  Future<Response> deleteAccount(int id) async {
    return await _dio.delete('/cash-flow/accounts/$id');
  }

  // Customers / parties
  Future<Response> createCustomer(Map<String, dynamic> data) async {
    return await _dio.post('/customers', data: data);
  }

  // Sales (creating an invoice from the "Add Sale" screen)
  Future<Response> createSale(Map<String, dynamic> data) async {
    return await _dio.post('/sales', data: data);
  }

  // Payments (Payment In / Payment Out shortcuts)
  Future<Response> getPayments({String? type}) async {
    final params = <String, dynamic>{};
    if (type != null) params['type'] = type;
    return await _dio.get('/payments', queryParameters: params);
  }

  Future<Response> createPayment(Map<String, dynamic> data) async {
    return await _dio.post('/payments', data: data);
  }

  // Purchases (Purchase shortcut)
  Future<Response> getPurchases({int page = 1}) async {
    return await _dio.get('/purchases', queryParameters: {'page': page});
  }

  Future<Response> createPurchase(Map<String, dynamic> data) async {
    return await _dio.post('/purchases', data: data);
  }

  Future<Response> getSuppliers({String? search}) async {
    final params = <String, dynamic>{};
    if (search != null) params['search'] = search;
    return await _dio.get('/suppliers', queryParameters: params);
  }

  // Expenses (Expense shortcut)
  Future<Response> getExpenses() async {
    return await _dio.get('/expenses');
  }

  Future<Response> createExpense(Map<String, dynamic> data) async {
    return await _dio.post('/expenses', data: data);
  }

  // Incomes (Other Income shortcut)
  Future<Response> getIncomes() async {
    return await _dio.get('/incomes');
  }

  Future<Response> createIncome(Map<String, dynamic> data) async {
    return await _dio.post('/incomes', data: data);
  }

  // Cash flow summary (used to populate account pickers with live balances)
  Future<Response> getCashFlow() async {
    return await _dio.get('/cash-flow');
  }

  // Reminders (Add Reminder shortcut)
  Future<Response> getReminders() async {
    return await _dio.get('/reminders');
  }

  Future<Response> createReminder(Map<String, dynamic> data) async {
    return await _dio.post('/reminders', data: data);
  }

  Future<Response> deleteReminder(int id) async {
    return await _dio.delete('/reminders/$id');
  }

  // Stock adjustment (Stock Adjustment shortcut)
  Future<Response> adjustStock(int productId, Map<String, dynamic> data) async {
    return await _dio.post('/products/$productId/adjust-stock', data: data);
  }

  // Notebook (note: not yet exposed by the backend API — these calls are
  // wired up so the Notebook screen works the moment the endpoint ships;
  // until then it falls back to local-only notes).
  Future<Response> getNotes() async {
    return await _dio.get('/notebook');
  }

  Future<Response> createNote(Map<String, dynamic> data) async {
    return await _dio.post('/notebook', data: data);
  }

  Future<Response> updateNote(int id, Map<String, dynamic> data) async {
    return await _dio.put('/notebook/$id', data: data);
  }

  Future<Response> deleteNote(int id) async {
    return await _dio.delete('/notebook/$id');
  }
}
