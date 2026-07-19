import 'package:flutter/material.dart';
import '../models/user.dart';
import '../models/business.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';

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

  Future<bool> login(String phone, String password) async {
    _isLoading = true;
    notifyListeners();

    try {
      final res = await _api.login(phone, password);
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
      _isLoading = false;
      notifyListeners();
      return false;
    } catch (e) {
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> register(Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();

    try {
      final res = await _api.register(data);
      _isLoading = false;
      notifyListeners();
      return res.statusCode == 201 && res.data['success'] == true;
    } catch (e) {
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    try {
      await _api.logout();
    } catch (_) {}
    _user = null;
    _business = null;
    _isAuthenticated = false;
    await _storage.clearAuth();
    notifyListeners();
  }
}
