import 'package:flutter/material.dart';
import '../models/store_models.dart';

class WishlistProvider with ChangeNotifier {
  final Set<int> _wishlistIds = {};
  final List<Product> _items = [];

  List<Product> get items => [..._items];
  bool isFavorite(int productId) => _wishlistIds.contains(productId);

  void toggleFavorite(Product product) {
    if (_wishlistIds.contains(product.id)) {
      _wishlistIds.remove(product.id);
      _items.removeWhere((p) => p.id == product.id);
    } else {
      _wishlistIds.add(product.id);
      _items.add(product);
    }
    notifyListeners();
  }
}
