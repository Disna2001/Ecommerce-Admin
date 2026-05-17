import 'package:flutter/foundation.dart' hide Category;
import 'package:dio/dio.dart';
import '../models/store_models.dart';

class ApiService {
  final Dio _dio = Dio(BaseOptions(
    baseUrl: 'https://client1.displaylanka.shop/api/v1/',
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 10),
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
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
      String errorMessage = e.message ?? 'Unknown error';
      if (e.response?.data is Map<String, dynamic>) {
        final data = e.response!.data as Map<String, dynamic>;
        if (data.containsKey('errors')) {
          final errors = data['errors'] as Map<String, dynamic>;
          errorMessage = errors.values.first[0].toString();
        } else if (data.containsKey('message')) {
          errorMessage = data['message'].toString();
        }
      }
      debugPrint('Login Error: $errorMessage');
      throw Exception(errorMessage);
    } catch (e) {
      debugPrint('Login General Error: $e');
      throw Exception(e.toString());
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
      String errorMessage = e.message ?? 'Unknown error';
      if (e.response?.data is Map<String, dynamic>) {
        final data = e.response!.data as Map<String, dynamic>;
        if (data.containsKey('errors')) {
          final errors = data['errors'] as Map<String, dynamic>;
          errorMessage = errors.values.first[0].toString();
        } else if (data.containsKey('message')) {
          errorMessage = data['message'].toString();
        }
      }
      debugPrint('Register Error: $errorMessage');
      throw Exception(errorMessage);
    } catch (e) {
      debugPrint('Register General Error: $e');
      throw Exception(e.toString());
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

  Future<List<dynamic>> getWishlists(String token) async {
    try {
      final response = await _dio.get('wishlists', 
        options: Options(headers: {'Authorization': 'Bearer $token'})
      );
      return response.data as List<dynamic>;
    } on DioException catch (e) {
      debugPrint('API Error (getWishlists): ${e.message}');
      throw Exception(e.response?.data['message'] ?? 'Failed to load wishlists');
    } catch (e) {
      debugPrint('General Error (getWishlists): $e');
      throw Exception('Failed to load wishlists');
    }
  }

  Future<bool> toggleWishlist(String token, int stockId) async {
    try {
      final response = await _dio.post('wishlists/toggle',
        data: {'stock_id': stockId},
        options: Options(headers: {'Authorization': 'Bearer $token'})
      );
      return response.data['is_wished'] ?? false;
    } on DioException catch (e) {
      debugPrint('API Error (toggleWishlist): ${e.message}');
      throw Exception(e.response?.data['message'] ?? 'Failed to toggle wishlist');
    } catch (e) {
      debugPrint('General Error (toggleWishlist): $e');
      throw Exception('Failed to toggle wishlist');
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
