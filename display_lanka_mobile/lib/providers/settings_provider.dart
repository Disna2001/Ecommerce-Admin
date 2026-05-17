import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';

class SettingsProvider extends ChangeNotifier {
  final ApiService _api = ApiService();
  
  String _siteName = 'Display Lanka';
  String? _logoUrl;
  Color _primaryColor = const Color(0xFF0F172A);
  Color _accentColor = const Color(0xFF6366F1);
  String? _supportEmail;
  String? _supportPhone;
  bool _isLoading = true;
  bool _isDark = false;

  String get siteName => _siteName;
  String? get logoUrl => _logoUrl;
  Color get primaryColor => _primaryColor;
  Color get accentColor => _accentColor;
  String? get supportEmail => _supportEmail;
  String? get supportPhone => _supportPhone;
  bool get isLoading => _isLoading;
  bool get isDark => _isDark;

  ThemeData get themeData => AppTheme.dynamicTheme(_primaryColor, _accentColor, _isDark);

  void toggleTheme() {
    _isDark = !_isDark;
    notifyListeners();
  }

  Future<void> fetchSettings() async {
    _isLoading = true;
    notifyListeners();

    try {
      final settings = await _api.getSettings();
      
      _siteName = settings['site_name'] ?? 'Display Lanka';
      _logoUrl = settings['logo_url'];
      if (_logoUrl != null && !_logoUrl!.startsWith('http')) {
        _logoUrl = 'https://client1.displaylanka.shop${_logoUrl!.startsWith('/') ? '' : '/'}$_logoUrl';
      }
      
      if (settings['primary_color'] != null) {
        _primaryColor = _parseHexColor(settings['primary_color']) ?? const Color(0xFF0F172A);
      }
      if (settings['secondary_color'] != null) {
        _accentColor = _parseHexColor(settings['secondary_color']) ?? const Color(0xFF6366F1);
      }
      
      _supportEmail = settings['support_email'];
      _supportPhone = settings['support_phone'];
    } catch (e) {
      debugPrint('Failed to load dynamic site settings: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Color? _parseHexColor(String hexString) {
    try {
      final buffer = StringBuffer();
      if (hexString.length == 6 || hexString.length == 7) buffer.write('ff');
      buffer.write(hexString.replaceFirst('#', ''));
      return Color(int.parse(buffer.toString(), radix: 16));
    } catch (e) {
      return null;
    }
  }
}
