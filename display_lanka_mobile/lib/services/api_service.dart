import 'package:flutter/foundation.dart' hide Category;
import 'package:dio/dio.dart';
import '../models/store_models.dart';

class ApiService {
  final Dio _dio = Dio(BaseOptions(
    baseUrl: 'https://client1.displaylanka.shop/api/v1/',
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 10),
  ));

  Future<List<Product>> getProducts({int? categoryId}) async {
    try {
      final response = await _dio.get('products', queryParameters: {
        if (categoryId != null) 'category_id': categoryId,
      });
      if (response.data == null || response.data['data'] == null) return [];
      final List data = response.data['data'];
      return data.map((json) => Product.fromJson(json)).toList();
    } on DioException catch (e) {
      debugPrint('API Error (getProducts): ${e.message}');
      return []; // Return empty list instead of crashing
    } catch (e) {
      debugPrint('General Error (getProducts): $e');
      return [];
    }
  }

  Future<List<Category>> getCategories() async {
    try {
      final response = await _dio.get('categories');
      if (response.data == null) return [];
      final List data = response.data;
      return data.map((json) => Category.fromJson(json)).toList();
    } on DioException catch (e) {
      debugPrint('API Error (getCategories): ${e.message}');
      return [];
    } catch (e) {
      debugPrint('General Error (getCategories): $e');
      return [];
    }
  }

  Future<Map<String, dynamic>?> login(String email, String password) async {
    try {
      final response = await _dio.post('login', data: {
        'email': email,
        'password': password,
        'device_name': 'flutter_mobile',
      });
      return response.data;
    } on DioException catch (e) {
      debugPrint('Login Error: ${e.response?.data['message'] ?? e.message}');
      rethrow;
    } catch (e) {
      debugPrint('Login General Error: $e');
      rethrow;
    }
  }

  Future<Map<String, dynamic>?> register(String name, String email, String password) async {
    try {
      final response = await _dio.post('register', data: {
        'name': name,
        'email': email,
        'password': password,
        'device_name': 'flutter_mobile',
      });
      return response.data;
    } on DioException catch (e) {
      debugPrint('Register Error: ${e.response?.data['message'] ?? e.message}');
      rethrow;
    } catch (e) {
      debugPrint('Register General Error: $e');
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getMe(String token) async {
    try {
      final response = await _dio.get('me', 
        options: Options(headers: {'Authorization': 'Bearer $token'})
      );
      return response.data;
    } catch (e) {
      throw Exception('Failed to load profile: $e');
    }
  }

  Future<void> logout(String token) async {
    try {
      await _dio.post('logout', 
        options: Options(headers: {'Authorization': 'Bearer $token'})
      );
    } catch (e) {
      throw Exception('Logout failed: $e');
    }
  }

  Future<List<dynamic>> getOrders(String token) async {
    try {
      final response = await _dio.get('orders', 
        options: Options(headers: {'Authorization': 'Bearer $token'})
      );
      return response.data as List<dynamic>;
    } on DioException catch (e) {
      debugPrint('API Error (getOrders): ${e.message}');
      throw Exception(e.response?.data['message'] ?? 'Failed to load orders');
    } catch (e) {
      debugPrint('General Error (getOrders): $e');
      throw Exception('Failed to load orders');
    }
  }

  Future<Map<String, dynamic>> getSettings() async {
    try {
      final response = await _dio.get('settings');
      return response.data;
    } catch (e) {
      throw Exception('Failed to load settings: $e');
    }
  }

  Future<void> forgotPassword(String email) async {
    try {
      await _dio.post('forgot-password', data: {'email': email});
    } on DioException catch (e) {
      throw Exception(e.response?.data['message'] ?? 'Failed to send reset link');
    }
  }

  Future<void> verifyOtp(String email, String otp) async {
    try {
      await _dio.post('verify-otp', data: {
        'email': email,
        'otp': otp,
      });
    } on DioException catch (e) {
      throw Exception(e.response?.data['message'] ?? 'Invalid OTP');
    }
  }
}
