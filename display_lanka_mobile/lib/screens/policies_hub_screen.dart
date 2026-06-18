import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/settings_provider.dart';

class PoliciesHubScreen extends StatefulWidget {
  const PoliciesHubScreen({super.key});

  @override
  State<PoliciesHubScreen> createState() => _PoliciesHubScreenState();
}

class _PoliciesHubScreenState extends State<PoliciesHubScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  final Map<String, List<Map<String, dynamic>>> _policies = {
    'refund': [
      {
        'title': 'Order cancellations',
        'body': [
          'Orders may be cancelled before the item, subscription, or account access has been delivered or activated.',
          'Once delivery, activation, account provisioning, or digital fulfilment has started, cancellation may no longer be possible.',
        ],
      },
      {
        'title': 'When refunds are considered',
        'body': [
          'Refunds may be reviewed when an order cannot be fulfilled, the wrong item is delivered, duplicate payment is confirmed, or a verified technical issue prevents access.',
          'Requests must include the order number, payment details, and a short explanation so the support team can investigate quickly.',
        ],
      },
      {
        'title': 'Digital products and delivered subscriptions',
        'body': [
          'Because most products sold through this store are digital services, account credentials, software access, gift cards, and already-delivered subscriptions are generally non-returnable once successfully delivered.',
          'Refunds are not guaranteed for change-of-mind purchases after successful delivery.',
        ],
      },
      {
        'title': 'Refund processing time',
        'body': [
          'Approved refunds are processed back to the original payment method or by an agreed settlement method.',
          'Banks and payment gateways may take additional time to reflect the refund after approval.',
        ],
      },
    ],
    'privacy': [
      {
        'title': 'Information we collect',
        'body': [
          'We may collect your name, email address, phone number, delivery details, account information, and payment-related references when you register, contact support, or place an order.',
          'Basic technical data such as device type, browser, IP address, and site activity may also be collected for security, analytics, and service improvement.',
        ],
      },
      {
        'title': 'How we use your information',
        'body': [
          'Your information is used to process orders, confirm payments, provide customer support, send order updates, prevent fraud, and improve the storefront experience.',
          'We only use the data that is reasonably necessary to operate the website and fulfil customer requests.',
        ],
      },
      {
        'title': 'Payments and third-party services',
        'body': [
          'Payments are processed through trusted third-party payment providers. We do not store full card details on this website.',
          'Information may be shared with payment gateways, courier partners, analytics tools, and messaging providers only when required to complete services or comply with legal obligations.',
        ],
      },
      {
        'title': 'Data protection',
        'body': [
          'We take reasonable administrative and technical measures to protect customer information from unauthorized access, misuse, or disclosure.',
          'Customers should also protect their account password and contact us immediately if they suspect unauthorized activity.',
        ],
      },
    ],
    'terms': [
      {
        'title': 'Using the website',
        'body': [
          'By using this website, you agree to use it lawfully and provide accurate account, billing, and order information.',
          'You are responsible for maintaining the confidentiality of your account and any activities performed under it.',
        ],
      },
      {
        'title': 'Products, pricing, and availability',
        'body': [
          'Product descriptions, stock levels, delivery timing, and prices may change without prior notice.',
          'Orders may be declined or cancelled if a product becomes unavailable, pricing is incorrect, or fraud or abuse is suspected.',
        ],
      },
      {
        'title': 'Payments and verification',
        'body': [
          'Orders are only confirmed after successful payment or manual payment verification where applicable.',
          'The store may request additional confirmation for suspicious or incomplete payment records before completing fulfilment.',
        ],
      },
      {
        'title': 'Delivery, returns, and support',
        'body': [
          'Delivery timelines depend on the product type, order review requirements, and any information needed from the customer.',
          'Returns and refund handling follow the published Refund Policy, and support is available through the listed contact channels.',
        ],
      },
    ],
  };

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final settings = Provider.of<SettingsProvider>(context);
    final isDark = settings.isDark;

    final bgColor = isDark ? const Color(0xFF020617) : const Color(0xFFF8FAFC);
    final surfaceColor = isDark ? const Color(0xFF0F172A) : Colors.white;
    final textPrimary = isDark ? Colors.white : const Color(0xFF0F172A);
    final textSecondary = isDark ? Colors.white70 : const Color(0xFF64748B);

    return Scaffold(
      backgroundColor: bgColor,
      appBar: AppBar(
        backgroundColor: surfaceColor,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios_new, color: textPrimary, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'LEGAL REGISTRY',
          style: theme.textTheme.titleLarge?.copyWith(
            letterSpacing: 2,
            color: textPrimary,
          ),
        ),
        bottom: TabBar(
          controller: _tabController,
          labelColor: theme.colorScheme.primary,
          unselectedLabelColor: textSecondary,
          indicatorColor: theme.colorScheme.primary,
          indicatorSize: TabBarIndicatorSize.tab,
          tabs: const [
            Tab(text: 'REFUND'),
            Tab(text: 'PRIVACY'),
            Tab(text: 'TERMS'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildPolicyList(_policies['refund']!, surfaceColor, textPrimary, textSecondary, isDark),
          _buildPolicyList(_policies['privacy']!, surfaceColor, textPrimary, textSecondary, isDark),
          _buildPolicyList(_policies['terms']!, surfaceColor, textPrimary, textSecondary, isDark),
        ],
      ),
    );
  }

  Widget _buildPolicyList(
    List<Map<String, dynamic>> sections,
    Color surfaceColor,
    Color textPrimary,
    Color textSecondary,
    bool isDark,
  ) {
    return ListView.builder(
      padding: const EdgeInsets.all(24),
      itemCount: sections.length,
      itemBuilder: (context, index) {
        final sec = sections[index];
        final bodyText = (sec['body'] as List<String>).join('\n\n');

        return Container(
          margin: const EdgeInsets.only(bottom: 16),
          decoration: BoxDecoration(
            color: surfaceColor,
            borderRadius: BorderRadius.circular(24),
            boxShadow: isDark
                ? []
                : [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.01),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    )
                  ],
          ),
          child: ExpansionTile(
            title: Text(
              sec['title']!,
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.bold,
                color: textPrimary,
                letterSpacing: 0.5,
              ),
            ),
            iconColor: Theme.of(context).colorScheme.primary,
            collapsedIconColor: textSecondary,
            childrenPadding: const EdgeInsets.only(left: 20, right: 20, bottom: 24),
            initiallyExpanded: index == 0,
            children: [
              Text(
                bodyText,
                style: TextStyle(
                  fontSize: 12,
                  color: textSecondary,
                  height: 1.6,
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}
