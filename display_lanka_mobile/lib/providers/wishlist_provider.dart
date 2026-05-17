import 'package:flutter/material.dart';
import '../services/api_service.dart';

class WishlistProvider extends ChangeNotifier {
  final ApiService _api = ApiService();
  
  Set<int> _wishedStockIds = {};
  bool _isLoading = false;

  bool get isLoading => _isLoading;
  Set<int> get wishedStockIds => _wishedStockIds;

  bool isWished(int stockId) {
    return _wishedStockIds.contains(stockId);
  }

  Future<void> fetchWishlists(String token) async {
    _isLoading = true;
    notifyListeners();

    try {
      final wishlists = await _api.getWishlists(token);
      _wishedStockIds = wishlists.map((w) => int.parse(w['id'].toString())).toSet();
    } catch (e) {
      debugPrint('Failed to fetch wishlists: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> toggleWishlist(String token, int stockId) async {
    // Optimistic UI Update
    final wasWished = _wishedStockIds.contains(stockId);
    if (wasWished) {
      _wishedStockIds.remove(stockId);
    } else {
      _wishedStockIds.add(stockId);
    }
    notifyListeners();

    try {
      final isWished = await _api.toggleWishlist(token, stockId);
      if (isWished) {
        _wishedStockIds.add(stockId);
      } else {
        _wishedStockIds.remove(stockId);
      }
    } catch (e) {
      // Revert Optimistic Update on failure
      if (wasWished) {
        _wishedStockIds.add(stockId);
      } else {
        _wishedStockIds.remove(stockId);
      }
      debugPrint('Failed to toggle wishlist: $e');
    } finally {
      notifyListeners();
    }
  }

  void clear() {
    _wishedStockIds.clear();
    notifyListeners();
  }
}
