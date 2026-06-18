import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'home_screen.dart';
import 'shop_screen.dart';
import 'cart_screen.dart';
import 'wishlist_screen.dart';
import 'profile_screen.dart';
import 'web_admin_screen.dart';
import '../providers/auth_provider.dart';
import '../providers/cart_provider.dart';
import '../providers/wishlist_provider.dart';
import '../providers/settings_provider.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen>
    with WidgetsBindingObserver, TickerProviderStateMixin {
  int _selectedIndex = 0;
  late AnimationController _navAnimController;

  @override
  void initState() {
    super.initState();
    _navAnimController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 300),
    );
    WidgetsBinding.instance.addObserver(this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final auth = Provider.of<AuthProvider>(context, listen: false);
      if (auth.isAuthenticated && auth.token != null) {
        Provider.of<WishlistProvider>(context, listen: false)
            .fetchWishlists(auth.token!);
      }
    });
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    _navAnimController.dispose();
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      Provider.of<SettingsProvider>(context, listen: false).fetchSettings();
    }
  }

  void _onTabTap(int index) {
    HapticFeedback.selectionClick();
    setState(() => _selectedIndex = index);
    Provider.of<SettingsProvider>(context, listen: false).fetchSettings();
  }

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context);
    final cart = Provider.of<CartProvider>(context);
    final wishlist = Provider.of<WishlistProvider>(context);
    final settings = Provider.of<SettingsProvider>(context);
    final isDark = settings.isDark;
    final isAdmin =
        auth.isAuthenticated && auth.user?['user_type'] == 'admin';

    final List<_NavItem> navItems = [
      _NavItem(
        icon: Icons.home_outlined,
        activeIcon: Icons.home_rounded,
        label: 'Home',
      ),
      _NavItem(
        icon: Icons.storefront_outlined,
        activeIcon: Icons.storefront_rounded,
        label: 'Shop',
      ),
      _NavItem(
        icon: Icons.shopping_bag_outlined,
        activeIcon: Icons.shopping_bag_rounded,
        label: 'Cart',
        badge: cart.itemCount > 0 ? '${cart.itemCount}' : null,
      ),
      _NavItem(
        icon: Icons.favorite_border_rounded,
        activeIcon: Icons.favorite_rounded,
        label: 'Saved',
        badge: wishlist.wishedStockIds.isNotEmpty
            ? '${wishlist.wishedStockIds.length}'
            : null,
      ),
      if (isAdmin)
        _NavItem(
          icon: Icons.admin_panel_settings_outlined,
          activeIcon: Icons.admin_panel_settings_rounded,
          label: 'Admin',
          accentColor: Colors.deepOrange,
        ),
      _NavItem(
        icon: Icons.person_outline_rounded,
        activeIcon: Icons.person_rounded,
        label: 'Profile',
      ),
    ];

    // Clamp index if admin tab disappears
    final clampedIndex = _selectedIndex.clamp(0, navItems.length - 1);

    final List<Widget> screens = [
      const HomeScreen(),
      const ShopScreen(),
      const CartScreen(),
      const WishlistScreen(),
      if (isAdmin) WebAdminScreen(token: auth.token ?? ''),
      const ProfileScreen(),
    ];

    final bgColor =
        isDark ? const Color(0xFF020617) : const Color(0xFFF8FAFC);
    final navBg = isDark ? const Color(0xFF0F172A) : Colors.white;
    final primaryColor = isDark ? Colors.white : const Color(0xFF0F172A);

    return Scaffold(
      backgroundColor: bgColor,
      body: IndexedStack(
        index: clampedIndex,
        children: screens,
      ),
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          color: navBg,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(isDark ? 0.3 : 0.06),
              blurRadius: 24,
              offset: const Offset(0, -8),
            ),
          ],
          border: Border(
            top: BorderSide(
              color: isDark
                  ? Colors.white.withOpacity(0.05)
                  : Colors.black.withOpacity(0.05),
              width: 0.5,
            ),
          ),
        ),
        child: SafeArea(
          child: Padding(
            padding:
                const EdgeInsets.symmetric(horizontal: 8, vertical: 8),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: List.generate(navItems.length, (index) {
                final item = navItems[index];
                final isSelected = clampedIndex == index;
                final accent = item.accentColor ?? primaryColor;

                return Expanded(
                  child: GestureDetector(
                    onTap: () => _onTabTap(index),
                    behavior: HitTestBehavior.opaque,
                    child: AnimatedContainer(
                      duration: const Duration(milliseconds: 200),
                      curve: Curves.easeInOut,
                      padding: const EdgeInsets.symmetric(
                          vertical: 8, horizontal: 4),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          // Icon with pill highlight
                          AnimatedContainer(
                            duration: const Duration(milliseconds: 250),
                            curve: Curves.easeOutBack,
                            padding: const EdgeInsets.symmetric(
                                horizontal: 16, vertical: 6),
                            decoration: BoxDecoration(
                              color: isSelected
                                  ? accent.withOpacity(isDark ? 0.15 : 0.08)
                                  : Colors.transparent,
                              borderRadius: BorderRadius.circular(20),
                            ),
                            child: Stack(
                              clipBehavior: Clip.none,
                              children: [
                                AnimatedSwitcher(
                                  duration:
                                      const Duration(milliseconds: 200),
                                  child: Icon(
                                    isSelected
                                        ? item.activeIcon
                                        : item.icon,
                                    key: ValueKey(isSelected),
                                    size: 22,
                                    color: isSelected
                                        ? accent
                                        : isDark
                                            ? Colors.white38
                                            : const Color(0xFF94A3B8),
                                  ),
                                ),
                                if (item.badge != null)
                                  Positioned(
                                    top: -6,
                                    right: -10,
                                    child: Container(
                                      padding: const EdgeInsets.symmetric(
                                          horizontal: 5, vertical: 1),
                                      decoration: BoxDecoration(
                                        color: const Color(0xFF6366F1),
                                        borderRadius:
                                            BorderRadius.circular(10),
                                      ),
                                      child: Text(
                                        item.badge!,
                                        style: const TextStyle(
                                          color: Colors.white,
                                          fontSize: 8,
                                          fontWeight: FontWeight.w900,
                                        ),
                                      ),
                                    ),
                                  ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 3),
                          AnimatedDefaultTextStyle(
                            duration: const Duration(milliseconds: 200),
                            style: TextStyle(
                              fontSize: 9,
                              fontWeight: isSelected
                                  ? FontWeight.w900
                                  : FontWeight.w600,
                              letterSpacing: 0.5,
                              color: isSelected
                                  ? accent
                                  : isDark
                                      ? Colors.white38
                                      : const Color(0xFF94A3B8),
                            ),
                            child: Text(item.label.toUpperCase()),
                          ),
                        ],
                      ),
                    ),
                  ),
                );
              }),
            ),
          ),
        ),
      ),
    );
  }
}

class _NavItem {
  final IconData icon;
  final IconData activeIcon;
  final String label;
  final String? badge;
  final Color? accentColor;

  _NavItem({
    required this.icon,
    required this.activeIcon,
    required this.label,
    this.badge,
    this.accentColor,
  });
}
