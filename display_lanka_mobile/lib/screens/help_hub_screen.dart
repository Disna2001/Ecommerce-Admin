import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../providers/settings_provider.dart';

class HelpHubScreen extends StatefulWidget {
  const HelpHubScreen({super.key});

  @override
  State<HelpHubScreen> createState() => _HelpHubScreenState();
}

class _HelpHubScreenState extends State<HelpHubScreen> {
  final List<Map<String, String>> _faqs = [
    {
      'q': 'How do I verify order placement?',
      'a': 'Upon successful initialization, the system records your acquisition immediately and dispatches a verification artifact via email (SMTP).'
    },
    {
      'q': 'What are the payment verification protocols?',
      'a': 'Manual transfer artifacts (receipts) are audited by the administrative core. Automated gateways synchronize status in real-time.'
    },
    {
      'q': 'How can I monitor protocol progress?',
      'a': 'Utilize the Sync Node (Track Order screen) with your protocol identifier (Order #) and registry email for real-time lifecycle tracking.'
    },
    {
      'q': 'What if an asset is depleted (Out of Stock)?',
      'a': 'Add the asset to your local buffer (Wishlist). Our distribution team monitors depletion levels for restocking protocols.'
    },
  ];

  Future<void> _launchUrl(String urlString) async {
    final uri = Uri.parse(urlString);
    try {
      if (await canLaunchUrl(uri)) {
        await launchUrl(uri, mode: LaunchMode.externalApplication);
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Could not launch: $urlString')),
        );
      }
    }
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
          'INTELLIGENCE HUB',
          style: theme.textTheme.titleLarge?.copyWith(
            letterSpacing: 2,
            color: textPrimary,
          ),
        ),
      ),
      body: ListView(
        padding: const EdgeInsets.all(24),
        children: [
          Text(
            'CENTRALIZED SUPPORT NODE',
            style: TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.w900,
              letterSpacing: 2.5,
              color: theme.colorScheme.primary,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'How can we assist you?',
            style: TextStyle(
              fontSize: 28,
              fontWeight: FontWeight.w900,
              letterSpacing: -1,
              color: textPrimary,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            'Access common acquisition protocols, payment guidance, and direct communication channels for operational assistance.',
            style: TextStyle(
              color: textSecondary,
              fontSize: 14,
              height: 1.5,
            ),
          ),
          const SizedBox(height: 32),
          
          // Direct Support Nodes
          Text(
            'DIRECT SUPPORT CHANNELS',
            style: TextStyle(
              fontWeight: FontWeight.w900,
              fontSize: 11,
              letterSpacing: 2,
              color: textSecondary,
            ),
          ),
          const SizedBox(height: 12),
          _buildSupportCard(
            icon: Icons.alternate_email_rounded,
            title: 'EMAIL NODE',
            value: 'support@displaylanka.shop',
            color: Colors.indigo,
            onTap: () => _launchUrl('mailto:support@displaylanka.shop'),
            surfaceColor: surfaceColor,
            textPrimary: textPrimary,
            textSecondary: textSecondary,
          ),
          const SizedBox(height: 12),
          _buildSupportCard(
            icon: Icons.phone_iphone_rounded,
            title: 'VOICE CHANNEL',
            value: '+94 77 123 4567',
            color: Colors.emerald,
            onTap: () => _launchUrl('tel:+94771234567'),
            surfaceColor: surfaceColor,
            textPrimary: textPrimary,
            textSecondary: textSecondary,
          ),
          
          const SizedBox(height: 40),
          
          // FAQ Segment
          Text(
            'FREQUENTLY EXECUTED QUERIES (FAQ)',
            style: TextStyle(
              fontWeight: FontWeight.w900,
              fontSize: 11,
              letterSpacing: 2,
              color: textSecondary,
            ),
          ),
          const SizedBox(height: 12),
          ..._faqs.map((faq) => Container(
                margin: const EdgeInsets.bottom(12),
                decoration: BoxDecoration(
                  color: surfaceColor,
                  borderRadius: BorderRadius.circular(20),
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
                child: Theme(
                  data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
                  child: ExpansionTile(
                    title: Text(
                      faq['q']!,
                      style: TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.bold,
                        color: textPrimary,
                        letterSpacing: 0.5,
                      ),
                    ),
                    iconColor: theme.colorScheme.primary,
                    collapsedIconColor: textSecondary,
                    childrenPadding: const EdgeInsets.only(left: 16, right: 16, bottom: 20),
                    children: [
                      Text(
                        faq['a']!,
                        style: TextStyle(
                          fontSize: 12,
                          color: textSecondary,
                          height: 1.5,
                        ),
                      ),
                    ],
                  ),
                ),
              )),
        ],
      ),
    );
  }

  Widget _buildSupportCard({
    required IconData icon,
    required String title,
    required String value,
    required Color color,
    required VoidCallback onTap,
    required Color surfaceColor,
    required Color textPrimary,
    required Color textSecondary,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: surfaceColor,
          borderRadius: BorderRadius.circular(24),
          border: Border.all(color: color.withOpacity(0.15)),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Icon(icon, color: color, size: 20),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: TextStyle(
                      fontSize: 9,
                      fontWeight: FontWeight.w900,
                      letterSpacing: 2,
                      color: color,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    value,
                    style: TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.bold,
                      color: textPrimary,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),
            Icon(Icons.arrow_forward_ios_rounded, color: textSecondary.withOpacity(0.5), size: 16),
          ],
        ),
      ),
    );
  }
}
