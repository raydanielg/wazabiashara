import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import '../models/dashboard_data.dart';
import '../services/api_service.dart';

class DashboardProvider extends ChangeNotifier {
  final _api = ApiService();

  DashboardData? _data;
  bool _isLoading = false;
  bool _isRefreshing = false;
  String? _error;
  bool _needsBusinessSetup = false;

  DashboardData? get data => _data;
  bool get isLoading => _isLoading;
  bool get isRefreshing => _isRefreshing;
  String? get error => _error;
  bool get needsBusinessSetup => _needsBusinessSetup;

  /// Loads real dashboard data from the backend. On failure this NEVER
  /// substitutes fabricated demo numbers — it surfaces [error] instead so
  /// the screen can show a proper retry/empty state. A user's actual
  /// business may simply be brand new and have all-zero figures, which is
  /// valid real data and different from "the request failed".
  Future<void> fetchDashboard() async {
    _isLoading = true;
    _error = null;
    _needsBusinessSetup = false;
    notifyListeners();

    await _load();

    _isLoading = false;
    notifyListeners();
  }

  Future<void> refresh() async {
    _isRefreshing = true;
    notifyListeners();

    await _load();

    _isRefreshing = false;
    notifyListeners();
  }

  Future<void> _load() async {
    try {
      final res = await _api.getDashboard();
      if (res.statusCode == 200 && res.data['success'] == true) {
        _data = DashboardData.fromJson(res.data['data']);
        _error = null;
        _needsBusinessSetup = false;
      } else {
        _error = res.data is Map ? (res.data['message'] ?? 'Failed to load dashboard') : 'Failed to load dashboard';
      }
    } on DioException catch (e) {
      if (e.response?.statusCode == 422 && e.response?.data is Map && e.response?.data['message'] != null) {
        // Backend returns 422 with a Swahili message specifically when the
        // signed-in user has no business_id yet — route them to setup
        // instead of showing an error or, worse, fake numbers.
        _needsBusinessSetup = true;
        _error = e.response?.data['message'];
      } else if (e.type == DioExceptionType.connectionTimeout ||
          e.type == DioExceptionType.receiveTimeout ||
          e.type == DioExceptionType.connectionError) {
        _error = 'Cannot reach the server. Check your internet connection and try again.';
      } else {
        _error = 'Something went wrong loading your dashboard. Pull down to retry.';
      }
    } catch (_) {
      _error = 'Something went wrong loading your dashboard. Pull down to retry.';
    }
  }

}
