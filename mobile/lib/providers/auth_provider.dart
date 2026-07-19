import 'package:flutter/material.dart';
import 'package:dio/dio.dart';
import '../models/user.dart';
import '../models/business.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';
import '../services/sale_watcher_service.dart';

class AuthProvider extends ChangeNotifier {
  final _api = ApiService();
  final _storage = StorageService();

  User? _user;
  Business? _business;
  bool _isLoading = false;
  bool _isAuthenticated = false;

  User? get user => _user;
  Business? get business => _business;
  bool get isLoading => _isLoading;
  bool get isAuthenticated => _isAuthenticated;

  Future<void> init() async {
    final token = await _storage.getToken();
    if (token != null) {
      final userData = await _storage.getUser();
      if (userData != null) {
        _user = User.fromJson(userData);
        _isAuthenticated = true;
        notifyListeners();
      }
    }
  }

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _api.login(email, password);
      if (res.statusCode == 200 && res.data['success'] == true) {
        final token = res.data['token'] as String;
        final userData = res.data['user'] as Map<String, dynamic>;
        await _storage.saveToken(token);
        await _storage.saveUser(userData);
        _user = User.fromJson(userData);
        _isAuthenticated = true;
        _isLoading = false;
        notifyListeners();
        return true;
      }
      _errorMessage = res.data['message'] as String? ?? 'Login failed';
      _isLoading = false;
      notifyListeners();
      return false;
    } on DioException catch (e) {
      _errorMessage = e.response?.data['message'] as String? ?? 'Network error. Try again.';
      _isLoading = false;
      notifyListeners();
      return false;
    } catch (e) {
      _errorMessage = 'Something went wrong. Try again.';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> register(Map<String, dynamic> data) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _api.register(data);
      if (res.statusCode == 201 && res.data['success'] == true) {
        final token = res.data['token'] as String?;
        final userData = res.data['user'] as Map<String, dynamic>?;
        if (token != null && userData != null) {
          await _storage.saveToken(token);
          await _storage.saveUser(userData);
          _user = User.fromJson(userData);
          _isAuthenticated = true;
        }
        _isLoading = false;
        notifyListeners();
        return true;
      }
      _errorMessage = res.data['message'] as String? ?? 'Registration failed';
      _isLoading = false;
      notifyListeners();
      return false;
    } on DioException catch (e) {
      final errors = e.response?.data['errors'] as Map<String, dynamic>?;
      if (errors != null && errors.isNotEmpty) {
        _errorMessage = errors.values.first as String?;
      } else {
        _errorMessage = e.response?.data['message'] as String? ?? 'Network error. Try again.';
      }
      _isLoading = false;
      notifyListeners();
      return false;
    } catch (e) {
      _errorMessage = 'Something went wrong. Try again.';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> verifyOtp(String phone, String otp) async {
    _isLoading = true;
    notifyListeners();

    try {
      final res = await _api.verifyOtp(phone, otp);
      if (res.statusCode == 200 && res.data['success'] == true) {
        final token = res.data['token'] as String?;
        final userData = res.data['user'] as Map<String, dynamic>?;
        if (token != null && userData != null) {
          await _storage.saveToken(token);
          await _storage.saveUser(userData);
          _user = User.fromJson(userData);
          _isAuthenticated = true;
        }
        _isLoading = false;
        notifyListeners();
        return true;
      }
      _isLoading = false;
      notifyListeners();
      return false;
    } catch (e) {
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  /// Re-syncs [_user] from the backend (via /me) — used right after Business
  /// Setup completes so `user.businessId` reflects the newly-created business
  /// without forcing a full re-login.
  Future<void> refreshUser() async {
    try {
      final res = await _api.dio.get('/me');
      if (res.statusCode == 200 && res.data['success'] == true) {
        final userData = res.data['user'] as Map<String, dynamic>;
        await _storage.saveUser(userData);
        _user = User.fromJson(userData);
        notifyListeners();
      }
    } catch (_) {}
  }

  /// Locally patches the signed-in user's business/branch id right after
  /// Business Setup — avoids a network round trip when we already have the
  /// fresh user object from the setup response.
  void setUser(Map<String, dynamic> userData) {
    _storage.saveUser(userData);
    _user = User.fromJson(userData);
    notifyListeners();
  }

  Future<void> logout() async {
    try {
      await _api.logout();
    } catch (_) {}
    SaleWatcherService.instance.stop();
    _user = null;
    _business = null;
    _isAuthenticated = false;
    await _storage.clearAuth();
    notifyListeners();
  }
}
