import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/api_service.dart';
import '../services/notification_service.dart';

class AuthProvider extends ChangeNotifier {
  final ApiService _api = ApiService();
  
  Map<String, dynamic>? _user;
  String? _token;
  bool _isLoading = false;

  Map<String, dynamic>? get user => _user;
  String? get token => _token;
  bool get isAuthenticated => _token != null;
  bool get isLoading => _isLoading;

  AuthProvider() {
    loadUserFromStorage();
  }

  Future<void> loadUserFromStorage() async {
    _isLoading = true;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      _token = prefs.getString('auth_token');
      final userString = prefs.getString('auth_user');
      
      if (_token != null && userString != null) {
        _user = jsonDecode(userString);
        if (_user != null && _user!['id'] != null) {
          final prefix = _user!['user_type'] == 'admin' ? 'admin_' : 'customer_';
          NotificationService.loginUser("$prefix${_user!['id']}");
        }
        // Refresh profile data from live site database
        await refreshProfile();
      }
    } catch (e) {
      debugPrint('Auto-login session restore failed: $e');
      await logout();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> refreshProfile() async {
    if (_token == null) return;
    try {
      final freshProfile = await _api.getMe(_token!);
      _user = freshProfile;
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('auth_user', jsonEncode(_user));
      
      if (_user != null && _user!['id'] != null) {
        final prefix = _user!['user_type'] == 'admin' ? 'admin_' : 'customer_';
        NotificationService.loginUser("$prefix${_user!['id']}");
      }
    } catch (e) {
      debugPrint('Failed to refresh user profile data: $e');
    }
  }

  Future<void> login(String email, String password) async {
    _isLoading = true;
    notifyListeners();

    try {
      final result = await _api.login(email, password);
      if (result != null) {
        _token = result['token'];
        _user = result['user'];
        
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', _token!);
        await prefs.setString('auth_user', jsonEncode(_user));

        if (_user != null && _user!['id'] != null) {
          final prefix = _user!['user_type'] == 'admin' ? 'admin_' : 'customer_';
          NotificationService.loginUser("$prefix${_user!['id']}");
        }
      }
    } catch (e) {
      _token = null;
      _user = null;
      rethrow;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> register(String name, String email, String password) async {
    _isLoading = true;
    notifyListeners();

    try {
      final result = await _api.register(name, email, password);
      if (result != null) {
        _token = result['token'];
        _user = result['user'];
        
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', _token!);
        await prefs.setString('auth_user', jsonEncode(_user));

        if (_user != null && _user!['id'] != null) {
          final prefix = _user!['user_type'] == 'admin' ? 'admin_' : 'customer_';
          NotificationService.loginUser("$prefix${_user!['id']}");
        }
      }
    } catch (e) {
      _token = null;
      _user = null;
      rethrow;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> logout() async {
    _isLoading = true;
    notifyListeners();

    try {
      if (_token != null) {
        await _api.logout(_token!);
      }
    } catch (e) {
      debugPrint('Server-side logout token invalidation failed: $e');
    } finally {
      await NotificationService.logoutUser();
      _token = null;
      _user = null;
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove('auth_token');
      await prefs.remove('auth_user');
      _isLoading = false;
      notifyListeners();
    }
  }
}
