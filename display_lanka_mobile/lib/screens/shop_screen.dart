import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import 'package:provider/provider.dart';
import '../models/store_models.dart';
import '../services/api_service.dart';
import '../widgets/product_card.dart';
import '../providers/settings_provider.dart';
import 'product_details_screen.dart';
import 'search_screen.dart';

class ShopScreen extends StatefulWidget {
  const ShopScreen({super.key});

  @override
  State<ShopScreen> createState() => _ShopScreenState();
}

class _ShopScreenState extends State<ShopScreen> {
  final ApiService _api = ApiService();
  List<Product> _products = [];
  List<Category> _categories = [];
  int? _selectedCategoryId;
  String _selectedSort = 'default';
  bool _isLoading = true;

  final List<Map<String, String>> _sortOptions = [
    {'key': 'default', 'label': 'Default'},
    {'key': 'price_asc', 'label': 'Price: Low → High'},
    {'key': 'price_desc', 'label': 'Price: High → Low'},
    {'key': 'name_asc', 'label': 'Name A–Z'},
  ];

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    if (!mounted) return;
    setState(() => _isLoading = true);
    try {
      final results = await Future.wait([
        _api.getCategories(),
        _api.getProducts(categoryId: _selectedCategoryId),
      ]);
      if (!mounted) return;
      List<Product> products = results[1] as List<Product>;

      // Client-side sorting
      switch (_selectedSort) {
        case 'price_asc':
          products.sort((a, b) => a.price.compareTo(b.price));
          break;
        case 'price_desc':
          products.sort((a, b) => b.price.compareTo(a.price));
          break;
        case 'name_asc':
          products.sort((a, b) =>
              a.name.toLowerCase().compareTo(b.name.toLowerCase()));
          break;
      }

      setState(() {
        _categories = results[0] as List<Category>;
        _products = products;
        _isLoading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final settings = Provider.of<SettingsProvider>(context);
    final isDark = settings.isDark;

    final bgColor = isDark ? const Color(0xFF020617) : const Color(0xFFF8FAFC);
    final navBg = isDark ? const Color(0xFF0F172A) : Colors.white;
    final textPrimary = isDark ? Colors.white : const Color(0xFF0F172A);

    return Scaffold(
      backgroundColor: bgColor,
      body: CustomScrollView(
        physics: const BouncingScrollPhysics(
          parent: AlwaysScrollableScrollPhysics(),
        ),
        slivers: [
          // App Bar
          SliverAppBar(
            pinned: true,
            backgroundColor: navBg,
            elevation: 0,
            toolbarHeight: 64,
            automaticallyImplyLeading: false,
            title: Text(
              'SHOP',
              style: TextStyle(
                fontWeight: FontWeight.w900,
                fontSize: 18,
                letterSpacing: 2,
                color: textPrimary,
              ),
            ),
            actions: [
              IconButton(
                icon: Icon(Icons.search_rounded, color: textPrimary),
                onPressed: () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const SearchScreen()),
                ),
              ),
              _buildSortButton(context, isDark, textPrimary),
              const SizedBox(width: 8),
            ],
          ),

          // Category filter
          SliverToBoxAdapter(
            child: _buildCategoryFilter(context, isDark),
          ),

          // Results count
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 4),
              child: Text(
                _isLoading
                    ? 'Loading...'
                    : '${_products.length} ${_products.length == 1 ? 'PRODUCT' : 'PRODUCTS'} FOUND',
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w700,
                  letterSpacing: 1,
                  color: isDark ? Colors.white38 : const Color(0xFF94A3B8),
                ),
              ),
            ),
          ),

          // Product Grid
          _buildProductGrid(context, isDark),

          const SliverToBoxAdapter(child: SizedBox(height: 32)),
        ],
      ),
    );
  }

  Widget _buildSortButton(
      BuildContext context, bool isDark, Color textPrimary) {
    return PopupMenuButton<String>(
      icon: Icon(Icons.sort_rounded, color: textPrimary),
      color: isDark ? const Color(0xFF1E293B) : Colors.white,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      onSelected: (val) {
        setState(() => _selectedSort = val);
        _loadData();
      },
      itemBuilder: (_) => _sortOptions
          .map((o) => PopupMenuItem<String>(
                value: o['key'],
                child: Row(
                  children: [
                    if (_selectedSort == o['key'])
                      const Icon(Icons.check_rounded,
                          size: 16, color: Color(0xFF6366F1)),
                    if (_selectedSort != o['key'])
                      const SizedBox(width: 16),
                    const SizedBox(width: 8),
                    Text(
                      o['label']!,
                      style: TextStyle(
                        fontWeight: FontWeight.w700,
                        color: isDark ? Colors.white : const Color(0xFF0F172A),
                        fontSize: 13,
                      ),
                    ),
                  ],
                ),
              ))
          .toList(),
    );
  }

  Widget _buildCategoryFilter(BuildContext context, bool isDark) {
    final bg = isDark ? const Color(0xFF0F172A) : Colors.white;

    if (_isLoading && _categories.isEmpty) {
      return SizedBox(
        height: 60,
        child: ListView.builder(
          scrollDirection: Axis.horizontal,
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
          itemCount: 5,
          itemBuilder: (_, __) => Shimmer.fromColors(
            baseColor: isDark ? const Color(0xFF1E293B) : Colors.grey[200]!,
            highlightColor: isDark ? const Color(0xFF334155) : Colors.grey[50]!,
            child: Container(
              width: 70,
              margin: const EdgeInsets.only(right: 8),
              decoration: BoxDecoration(
                color: bg,
                borderRadius: BorderRadius.circular(20),
              ),
            ),
          ),
        ),
      );
    }

    return SizedBox(
      height: 60,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
        itemCount: _categories.length + 1,
        itemBuilder: (context, index) {
          final isAll = index == 0;
          final category = isAll ? null : _categories[index - 1];
          final isSelected = _selectedCategoryId == category?.id;

          return GestureDetector(
            onTap: () {
              setState(() => _selectedCategoryId = isAll ? null : category?.id);
              _loadData();
            },
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              margin: const EdgeInsets.only(right: 8),
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              decoration: BoxDecoration(
                color: isSelected
                    ? (isDark ? Colors.white : const Color(0xFF0F172A))
                    : (isDark ? const Color(0xFF1E293B) : Colors.white),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(
                  color: isSelected
                      ? Colors.transparent
                      : isDark
                          ? Colors.white.withOpacity(0.06)
                          : Colors.black.withOpacity(0.06),
                ),
              ),
              child: Text(
                isAll ? 'ALL' : category!.name.toUpperCase(),
                style: TextStyle(
                  color: isSelected
                      ? (isDark ? const Color(0xFF0F172A) : Colors.white)
                      : isDark
                          ? Colors.white54
                          : const Color(0xFF64748B),
                  fontWeight: FontWeight.w900,
                  fontSize: 10,
                  letterSpacing: 1,
                ),
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildProductGrid(BuildContext context, bool isDark) {
    if (_isLoading) {
      return SliverPadding(
        padding: const EdgeInsets.symmetric(horizontal: 16),
        sliver: SliverGrid(
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            childAspectRatio: 0.65,
            mainAxisSpacing: 16,
            crossAxisSpacing: 16,
          ),
          delegate: SliverChildBuilderDelegate(
            (_, __) => Shimmer.fromColors(
              baseColor: isDark ? const Color(0xFF1E293B) : Colors.grey[200]!,
              highlightColor: isDark ? const Color(0xFF334155) : Colors.grey[50]!,
              child: Container(
                decoration: BoxDecoration(
                  color: isDark ? const Color(0xFF0F172A) : Colors.white,
                  borderRadius: BorderRadius.circular(28),
                ),
              ),
            ),
            childCount: 6,
          ),
        ),
      );
    }

    if (_products.isEmpty) {
      return SliverFillRemaining(
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.inventory_2_outlined,
                  size: 64,
                  color: isDark ? Colors.white24 : Colors.grey[300]),
              const SizedBox(height: 16),
              Text(
                'NO PRODUCTS FOUND',
                style: TextStyle(
                  fontWeight: FontWeight.w900,
                  letterSpacing: 2,
                  color: isDark ? Colors.white38 : Colors.grey,
                ),
              ),
              const SizedBox(height: 8),
              TextButton(
                onPressed: () {
                  setState(() => _selectedCategoryId = null);
                  _loadData();
                },
                child: const Text(
                  'CLEAR FILTERS',
                  style: TextStyle(
                    color: Color(0xFF6366F1),
                    fontWeight: FontWeight.w900,
                    letterSpacing: 1,
                  ),
                ),
              ),
            ],
          ),
        ),
      );
    }

    return SliverPadding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      sliver: SliverGrid(
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          childAspectRatio: 0.65,
          mainAxisSpacing: 16,
          crossAxisSpacing: 16,
        ),
        delegate: SliverChildBuilderDelegate(
          (context, index) {
            final product = _products[index];
            return Hero(
              tag: 'shop-${product.id}',
              child: ProductCard(
                product: product,
                onTap: () => Navigator.push(
                  context,
                  PageRouteBuilder(
                    pageBuilder: (_, __, ___) =>
                        ProductDetailsScreen(product: product),
                    transitionsBuilder: (_, anim, __, child) =>
                        FadeTransition(opacity: anim, child: child),
                  ),
                ),
              ),
            );
          },
          childCount: _products.length,
        ),
      ),
    );
  }
}
