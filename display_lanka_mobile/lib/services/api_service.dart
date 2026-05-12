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
      final List data = response.data['data'];
      return data.map((json) => Product.fromJson(json)).toList();
    } catch (e) {
      throw Exception('Failed to load products: $e');
    }
  }

  Future<List<Category>> getCategories() async {
    try {
      final response = await _dio.get('categories');
      final List data = response.data;
      return data.map((json) => Category.fromJson(json)).toList();
    } catch (e) {
      throw Exception('Failed to load categories: $e');
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
}
