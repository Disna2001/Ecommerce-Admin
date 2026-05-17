import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import 'login_screen.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      body: Consumer<AuthProvider>(
        builder: (context, auth, child) {
          return CustomScrollView(
            slivers: [
              _buildAppBar(context),
              _buildProfileHeader(context, auth),
              _buildMenuSection(context, auth),
              if (auth.isAuthenticated) _buildLogoutSection(context, auth),
            ],
          );
        },
      ),
    );
  }

  Widget _buildAppBar(BuildContext context) {
    return SliverAppBar(
      backgroundColor: Colors.white,
      elevation: 0,
      title: Text(
        'MY PROFILE',
        style: Theme.of(
          context,
        ).textTheme.titleLarge?.copyWith(letterSpacing: 2),
      ),
      centerTitle: true,
    );
  }

  Widget _buildProfileHeader(BuildContext context, AuthProvider auth) {
    final theme = Theme.of(context);
    return SliverToBoxAdapter(
      child: Container(
        color: Colors.white,
        padding: const EdgeInsets.all(32),
        child: Column(
          children: [
            Container(
              height: 100,
              width: 100,
              decoration: BoxDecoration(
                color: const Color(0xFFF1F5F9),
                shape: BoxShape.circle,
                border: Border.all(color: theme.colorScheme.primary, width: 3),
              ),
              child: Icon(
                Icons.person_outline_rounded,
                size: 50,
                color: theme.colorScheme.primary,
              ),
            ),
            const SizedBox(height: 16),
            Text(
              auth.isAuthenticated ? (auth.user?['name'] ?? 'User').toUpperCase() : 'GUEST USER',
              style: const TextStyle(
                fontWeight: FontWeight.w900,
                fontSize: 20,
                letterSpacing: 1,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              auth.isAuthenticated ? (auth.user?['email'] ?? '') : 'Sign in to access your registry & sync orders',
              style: const TextStyle(color: Color(0xFF64748B), fontSize: 14),
            ),
            const SizedBox(height: 24),
            if (!auth.isAuthenticated)
              ElevatedButton(
                onPressed: () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => const LoginScreen()),
                ),
                style: ElevatedButton.styleFrom(
                  backgroundColor: theme.colorScheme.primary,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 40,
                    vertical: 16,
                  ),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                  elevation: 0,
                ),
                child: const Text(
                  'SIGN IN TO SYSTEM',
                  style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1.5, fontSize: 12),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildMenuSection(BuildContext context, AuthProvider auth) {
    return SliverPadding(
      padding: const EdgeInsets.all(24),
      sliver: SliverList(
        delegate: SliverChildListDelegate([
          _buildMenuItem(
            Icons.shopping_bag_outlined,
            'MY ORDERS',
            auth.isAuthenticated ? 'Track your shipments' : 'Sign in to track orders',
            auth.isAuthenticated,
          ),
          const SizedBox(height: 16),
          _buildMenuItem(
            Icons.favorite_outline_rounded,
            'WISHLIST',
            'Items you saved',
            true,
          ),
          const SizedBox(height: 16),
          _buildMenuItem(
            Icons.location_on_outlined,
            'ADDRESSES',
            auth.isAuthenticated ? 'Manage delivery locations' : 'Sign in to add addresses',
            auth.isAuthenticated,
          ),
        ]),
      ),
    );
  }

  Widget _buildMenuItem(IconData icon, String title, String subtitle, bool enabled) {
    return Opacity(
      opacity: enabled ? 1.0 : 0.5,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.02),
              blurRadius: 20,
              offset: const Offset(0, 10),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFF8FAFC),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: const Color(0xFF0F172A), size: 20),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                cross         : CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      fontWeight: FontWeight.w900,
                      fontSize: 12,
                      letterSpacing: 1,
                    ),
                  ),
                  Text(
                    subtitle,
                    style: const TextStyle(
                      color: Color(0xFF94A3B8),
                      fontSize: 11,
                    ),
                  ),
                ],
              ),
            ),
            const Icon(
              Icons.arrow_forward_ios_rounded,
              size: 14,
              color: Color(0xFFCBD5E1),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLogoutSection(BuildContext context, AuthProvider auth) {
    return SliverToBoxAdapter(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: auth.isLoading
            ? const Center(child: CircularProgressIndicator())
            : TextButton(
                onPressed: () async {
                  await auth.logout();
                  if (context.mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Logged out successfully.')),
                    );
                  }
                },
                child: const Text(
                  'LOG OUT',
                  style: TextStyle(
                    color: Color(0xFFF43F5E),
                    fontWeight: FontWeight.w900,
                    letterSpacing: 2,
                  ),
                ),
              ),
      ),
    );
  }
}
