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
    return await _dio.post('/pos/checkout', data: data);
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
    return await _dio.get('/reports', queryParameters: params);
  }

  // Profile endpoints
  Future<Response> getProfile() async {
    return await _dio.get('/profile');
  }

  Future<Response> updateProfile(Map<String, dynamic> data) async {
    return await _dio.put('/profile', data: data);
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
}
