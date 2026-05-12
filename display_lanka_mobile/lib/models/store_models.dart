class Product {
  final int id;
  final String name;
  final String? description;
  final double price;
  final String? image;
  final String categoryName;

  Product({
    required this.id,
    required this.name,
    this.description,
    required this.price,
    this.image,
    required this.categoryName,
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'],
      name: json['name'],
      description: json['description'],
      price: double.parse(json['price'].toString()),
      image: json['primary_image_url'], // Laravel model accessor
      categoryName: json['category'] != null ? json['category']['name'] : 'Uncategorized',
    );
  }
}

class Category {
  final int id;
  final String name;
  final int productCount;

  Category({required this.id, required this.name, required this.productCount});

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: json['id'],
      name: json['name'],
      productCount: json['products_count'] ?? 0,
    );
  }
}
