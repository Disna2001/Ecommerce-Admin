import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'webview_tab_screen.dart';
import 'profile_screen.dart';
import '../providers/auth_provider.dart';
import '../providers/wishlist_provider.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _selectedIndex = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final auth = Provider.of<AuthProvider>(context, listen: false);
      if (auth.isAuthenticated && auth.token != null) {
        Provider.of<WishlistProvider>(context, listen: false)
            .fetchWishlists(auth.token!);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context);
    final isAdmin = auth.isAuthenticated && auth.user?['is_admin'] == true;

    // Map the bottom navigation index to the actual IndexedStack screen index.
    final List<int> tabToScreenMap = [
      0, // Tab 0 -> Screen 0 (Home WebView)
      1, // Tab 1 -> Screen 1 (Shop WebView)
      if (isAdmin) 2, // Tab 2 -> Screen 2 (POS WebView)
      3, // Tab 3 -> Screen 3 (Native Profile Screen)
    ];

    // Clamping selected index to make sure we don't index out of bounds when role transitions.
    int currentTabBarIndex = tabToScreenMap.indexOf(_selectedIndex);
    if (currentTabBarIndex == -1) {
      _selectedIndex = 3; // default to profile screen
      currentTabBarIndex = tabToScreenMap.indexOf(_selectedIndex);
    }

    final screens = [
      WebViewTabScreen(
        key: ValueKey('home_${auth.token}'),
        url: auth.isAuthenticated
            ? 'https://client1.displaylanka.shop/auth/checkout-login?token=${auth.token}&redirect=/'
            : 'https://client1.displaylanka.shop',
      ),
      WebViewTabScreen(
        key: ValueKey('shop_${auth.token}'),
        url: auth.isAuthenticated
            ? 'https://client1.displaylanka.shop/auth/checkout-login?token=${auth.token}&redirect=/shop'
            : 'https://client1.displaylanka.shop/shop',
      ),
      WebViewTabScreen(
        key: ValueKey('pos_${auth.token}'),
        url: auth.isAuthenticated
            ? 'https://client1.displaylanka.shop/auth/admin-login?token=${auth.token}&redirect=/admin/pos'
            : 'https://client1.displaylanka.shop', // dummy placeholder URL for guest
      ),
      const ProfileScreen(),
    ];

    return Scaffold(
      body: IndexedStack(
        index: _selectedIndex,
        children: screens,
      ),
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 20,
              offset: const Offset(0, -5),
            ),
          ],
        ),
        child: BottomNavigationBar(
          currentIndex: currentTabBarIndex,
          onTap: (index) => setState(() {
            _selectedIndex = tabToScreenMap[index];
          }),
          type: BottomNavigationBarType.fixed,
          backgroundColor: Colors.white,
          selectedItemColor: const Color(0xFF0F172A),
          unselectedItemColor: const Color(0xFF94A3B8),
          selectedLabelStyle: const TextStyle(fontWeight: FontWeight.w900, fontSize: 10),
          unselectedLabelStyle: const TextStyle(fontWeight: FontWeight.w700, fontSize: 10),
          elevation: 0,
          items: [
            const BottomNavigationBarItem(
              icon: Icon(Icons.home_filled),
              label: 'HOME',
            ),
            const BottomNavigationBarItem(
              icon: Icon(Icons.grid_view_rounded),
              label: 'SHOP',
            ),
            if (isAdmin)
              const BottomNavigationBarItem(
                icon: Icon(Icons.point_of_sale_rounded),
                label: 'POS',
              ),
            const BottomNavigationBarItem(
              icon: Icon(Icons.person_rounded),
              label: 'PROFILE',
            ),
          ],
        ),
      ),
    );
  }
}
