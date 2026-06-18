import 'package:flutter/material.dart';
import 'package:flutter_inappwebview/flutter_inappwebview.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:io';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const MainApp());
}

class MainApp extends StatelessWidget {
  const MainApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Display Lanka Client',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF1E3A8A), // Navy primary
          brightness: Brightness.light,
          primary: const Color(0xFF1E3A8A),
          secondary: const Color(0xFF10B981), // Emerald secondary
          surface: Colors.white,
        ),
        useMaterial3: true,
        fontFamily: 'Roboto',
      ),
      darkTheme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF1E3A8A),
          brightness: Brightness.dark,
          primary: const Color(0xFF3B82F6), // Lighter blue for dark mode
          secondary: const Color(0xFF34D399),
          surface: const Color(0xFF0F172A), // Slate-900 background
        ),
        useMaterial3: true,
        fontFamily: 'Roboto',
      ),
      themeMode: ThemeMode.system,
      home: const WebviewScreen(),
      debugShowCheckedModeBanner: false,
    );
  }
}

enum SidebarTab { dashboard, pos, storefront, settings }

class WebviewScreen extends StatefulWidget {
  const WebviewScreen({super.key});

  @override
  State<WebviewScreen> createState() => _WebviewScreenState();
}

class _WebviewScreenState extends State<WebviewScreen> {
  final GlobalKey webViewKey = GlobalKey();
  InAppWebViewController? webViewController;
  
  InAppWebViewSettings settings = InAppWebViewSettings(
    isInspectable: true,
    mediaPlaybackRequiresUserGesture: false,
    allowsInlineMediaPlayback: true,
    iframeAllow: "camera; microphone",
    iframeAllowFullscreen: true,
    domStorageEnabled: true,
    javaScriptEnabled: true,
    cacheEnabled: true,
  );

  PullToRefreshController? pullToRefreshController;
  
  // Settings & Configuration
  String baseUrl = "https://client1.displaylanka.shop";
  String currentUrl = "https://client1.displaylanka.shop";
  String defaultView = "/"; // "/" or "/admin" or "/admin/pos"
  
  double progress = 0;
  bool isConnected = true;
  bool hasError = false;
  bool settingsLoaded = false;
  
  // Navigation State
  SidebarTab activeTab = SidebarTab.storefront;
  bool showNativeSettings = false;
  
  // Settings controllers
  final TextEditingController _urlController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadSettings();
    _checkConnectivity();
    
    Connectivity().onConnectivityChanged.listen((List<ConnectivityResult> results) {
       _checkConnectivity();
    });

    pullToRefreshController = PullToRefreshController(
      settings: PullToRefreshSettings(
        color: const Color(0xFF1E3A8A),
      ),
      onRefresh: () async {
        if (Platform.isAndroid) {
          webViewController?.reload();
        } else if (Platform.isIOS) {
          webViewController?.loadUrl(
              urlRequest: URLRequest(url: await webViewController?.getUrl()));
        }
      },
    );
  }

  @override
  void dispose() {
    _urlController.dispose();
    super.dispose();
  }

  Future<void> _loadSettings() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      baseUrl = prefs.getString('baseUrl') ?? "https://client1.displaylanka.shop";
      // Trim trailing slash just in case
      if (baseUrl.endsWith('/')) {
        baseUrl = baseUrl.substring(0, baseUrl.length - 1);
      }
      defaultView = prefs.getString('defaultView') ?? "/";
      _urlController.text = baseUrl;
      
      // Determine initial active tab and start URL based on defaultView
      if (defaultView == "/admin/pos") {
        activeTab = SidebarTab.pos;
        currentUrl = "$baseUrl/admin/pos";
      } else if (defaultView == "/admin") {
        activeTab = SidebarTab.dashboard;
        currentUrl = "$baseUrl/admin";
      } else {
        activeTab = SidebarTab.storefront;
        currentUrl = "$baseUrl/";
      }
      
      settingsLoaded = true;
    });
  }

  Future<void> _saveSettings() async {
    final prefs = await SharedPreferences.getInstance();
    String inputtedUrl = _urlController.text.trim();
    if (inputtedUrl.endsWith('/')) {
      inputtedUrl = inputtedUrl.substring(0, inputtedUrl.length - 1);
    }
    
    await prefs.setString('baseUrl', inputtedUrl);
    await prefs.setString('defaultView', defaultView);
    
    setState(() {
      baseUrl = inputtedUrl;
      // Reload WebView with the new base URL based on active view
      _loadTab(activeTab);
    });

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Connection configurations saved successfully!'),
        backgroundColor: Colors.green,
      ),
    );
  }

  Future<void> _checkConnectivity() async {
    final connectivityResult = await (Connectivity().checkConnectivity());
    setState(() {
      isConnected = !connectivityResult.contains(ConnectivityResult.none);
    });
  }

  Future<void> _clearCache() async {
    if (webViewController != null) {
      await webViewController!.clearCache();
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('App cache cleared.')),
      );
      webViewController!.reload();
    }
  }

  void _loadTab(SidebarTab tab) {
    setState(() {
      activeTab = tab;
      showNativeSettings = false;
      hasError = false;
    });

    String targetPath = "/";
    switch (tab) {
      case SidebarTab.dashboard:
        targetPath = "/admin";
        break;
      case SidebarTab.pos:
        targetPath = "/admin/pos";
        break;
      case SidebarTab.storefront:
        targetPath = "/";
        break;
      case SidebarTab.settings:
        setState(() {
          showNativeSettings = true;
        });
        return;
    }

    final newUrl = "$baseUrl$targetPath";
    webViewController?.loadUrl(urlRequest: URLRequest(url: WebUri(newUrl)));
  }

  bool get _isDesktop {
    if (Platform.isWindows || Platform.isMacOS || Platform.isLinux) return true;
    // Fallback screen size threshold
    return MediaQuery.of(context).size.width > 900;
  }

  @override
  Widget build(BuildContext context) {
    if (!settingsLoaded) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }

    if (!isConnected) {
      return Scaffold(
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.wifi_off_rounded, size: 80, color: Colors.grey),
              const SizedBox(height: 20),
              const Text(
                "No Internet Connection",
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              const Text("Check your connection and try again"),
              const SizedBox(height: 24),
              ElevatedButton.icon(
                onPressed: () {
                  _checkConnectivity();
                  if (webViewController != null && isConnected) {
                    webViewController!.reload();
                  }
                },
                icon: const Icon(Icons.refresh_rounded),
                label: const Text("Retry Connection"),
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
              )
            ],
          ),
        ),
      );
    }

    return Scaffold(
      body: SafeArea(
        child: Row(
          children: [
            // Left sidebar shown only on desktop layout
            if (_isDesktop) _buildSidebar(),
            
            // Main Content Area
            Expanded(
              child: Column(
                children: [
                  // Linear progress bar at top of WebView loading
                  progress < 1.0 && !hasError && !showNativeSettings
                      ? LinearProgressIndicator(
                          value: progress,
                          color: Theme.of(context).colorScheme.primary,
                          backgroundColor: Colors.grey.withOpacity(0.1),
                          minHeight: 3,
                        )
                      : const SizedBox(height: 3),
                  
                  Expanded(
                    child: Stack(
                      children: [
                        // Webview container
                        Opacity(
                          opacity: showNativeSettings ? 0.0 : 1.0,
                          child: IgnorePointer(
                            ignoring: showNativeSettings,
                            child: InAppWebView(
                              key: webViewKey,
                              initialUrlRequest: URLRequest(url: WebUri(currentUrl)),
                              initialSettings: settings,
                              pullToRefreshController: pullToRefreshController,
                              onWebViewCreated: (controller) {
                                webViewController = controller;
                              },
                              onLoadStart: (controller, url) {
                                setState(() {
                                  currentUrl = url.toString();
                                  hasError = false;
                                });
                              },
                              onPermissionRequest: (controller, request) async {
                                return PermissionResponse(
                                    resources: request.resources,
                                    action: PermissionResponseAction.GRANT);
                              },
                              shouldOverrideUrlLoading:
                                  (controller, navigationAction) async {
                                var uri = navigationAction.request.url!;

                                if (![
                                  "http",
                                  "https",
                                  "file",
                                  "chrome",
                                  "data",
                                  "javascript",
                                  "about"
                                ].contains(uri.scheme)) {
                                  if (await canLaunchUrl(uri)) {
                                    await launchUrl(uri);
                                    return NavigationActionPolicy.CANCEL;
                                  }
                                }
                                
                                if (uri.scheme == 'mailto' || uri.scheme == 'tel' || uri.scheme == 'sms') {
                                   if (await canLaunchUrl(uri)) {
                                    await launchUrl(uri);
                                    return NavigationActionPolicy.CANCEL;
                                  }
                                }

                                return NavigationActionPolicy.ALLOW;
                              },
                              onLoadStop: (controller, url) async {
                                pullToRefreshController?.endRefreshing();
                                setState(() {
                                  currentUrl = url.toString();
                                });
                              },
                              onReceivedError: (controller, request, error) {
                                pullToRefreshController?.endRefreshing();
                                // Ignore minor subresource load errors
                                if (request.isForMainFrame ?? true) {
                                  setState(() {
                                    hasError = true;
                                  });
                                }
                              },
                              onProgressChanged: (controller, progress) {
                                if (progress == 100) {
                                  pullToRefreshController?.endRefreshing();
                                }
                                setState(() {
                                  this.progress = progress / 100;
                                });
                              },
                              onUpdateVisitedHistory: (controller, url, androidIsReload) {
                                setState(() {
                                  currentUrl = url.toString();
                                });
                              },
                            ),
                          ),
                        ),
                        
                        // Error loading page widget
                        if (hasError && !showNativeSettings)
                          Container(
                            color: Theme.of(context).scaffoldBackgroundColor,
                            child: Center(
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  const Icon(Icons.error_outline_rounded, size: 70, color: Colors.red),
                                  const SizedBox(height: 16),
                                  const Text(
                                    "Failed to load web page",
                                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                                  ),
                                  const SizedBox(height: 8),
                                  Text(
                                    currentUrl,
                                    style: const TextStyle(fontSize: 12, color: Colors.grey),
                                    textAlign: TextAlign.center,
                                  ),
                                  const SizedBox(height: 24),
                                  ElevatedButton.icon(
                                    onPressed: () {
                                      setState(() {
                                        hasError = false;
                                      });
                                      webViewController?.reload();
                                    },
                                    icon: const Icon(Icons.refresh_rounded),
                                    label: const Text("Reload Page"),
                                  )
                                ],
                              ),
                            ),
                          ),
                          
                        // Native settings screen
                        if (showNativeSettings) _buildSettingsView(),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
      // Floating Control button for mobile interface
      floatingActionButton: !_isDesktop
          ? FloatingActionButton(
              onPressed: _showMobileMenu,
              backgroundColor: Theme.of(context).colorScheme.primary,
              foregroundColor: Colors.white,
              elevation: 6,
              shape: const CircleBorder(),
              child: const Icon(Icons.menu_open_rounded),
            )
          : null,
    );
  }

  // Sidebar layout for desktop view
  Widget _buildSidebar() {
    final theme = Theme.of(context);
    final isDark = theme.brightness == Brightness.dark;

    return Container(
      width: 260,
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF0F172A) : const Color(0xFFF1F5F9),
        border: Border(
          right: BorderSide(
            color: isDark ? Colors.white.withOpacity(0.06) : Colors.black.withOpacity(0.06),
          ),
        ),
      ),
      child: Column(
        children: [
          // Sidebar Header
          Container(
            padding: const EdgeInsets.all(24),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: theme.colorScheme.primary.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(
                    Icons.dashboard_customize_rounded,
                    color: theme.colorScheme.primary,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'DISPLAY LANKA',
                        style: theme.textTheme.titleMedium?.copyWith(
                          fontWeight: FontWeight.bold,
                          letterSpacing: 0.8,
                          fontSize: 14,
                        ),
                      ),
                      Text(
                        'Admin Client',
                        style: theme.textTheme.bodySmall?.copyWith(
                          color: Colors.grey,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          
          const Divider(height: 1),
          const SizedBox(height: 16),
          
          // Sidebar Nav List
          Expanded(
            child: ListView(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              children: [
                _buildSidebarItem(
                  icon: Icons.analytics_outlined,
                  label: 'Dashboard',
                  tab: SidebarTab.dashboard,
                ),
                _buildSidebarItem(
                  icon: Icons.point_of_sale_rounded,
                  label: 'POS Counter',
                  tab: SidebarTab.pos,
                ),
                _buildSidebarItem(
                  icon: Icons.storefront_outlined,
                  label: 'View Storefront',
                  tab: SidebarTab.storefront,
                ),
                const SizedBox(height: 16),
                const Divider(),
                const SizedBox(height: 16),
                _buildSidebarItem(
                  icon: Icons.settings_outlined,
                  label: 'Connection Config',
                  tab: SidebarTab.settings,
                ),
              ],
            ),
          ),
          
          // Sidebar Footer
          Container(
            padding: const EdgeInsets.all(20),
            child: Row(
              children: [
                Container(
                  width: 8,
                  height: 8,
                  decoration: const BoxDecoration(
                    color: Colors.green,
                    shape: BoxShape.circle,
                  ),
                ),
                const SizedBox(width: 8),
                Text(
                  'Connected',
                  style: theme.textTheme.bodySmall?.copyWith(fontSize: 11),
                ),
                const Spacer(),
                Text(
                  'v1.0.0',
                  style: theme.textTheme.bodySmall?.copyWith(color: Colors.grey, fontSize: 10),
                ),
              ],
            ),
          )
        ],
      ),
    );
  }

  Widget _buildSidebarItem({
    required IconData icon,
    required String label,
    required SidebarTab tab,
  }) {
    final theme = Theme.of(context);
    final isSelected = activeTab == tab && (!showNativeSettings || tab == SidebarTab.settings);
    
    return Container(
      margin: const EdgeInsets.only(bottom: 6),
      child: Material(
        color: Colors.transparent,
        child: ListTile(
          onTap: () => _loadTab(tab),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          selected: isSelected,
          selectedTileColor: theme.colorScheme.primary.withOpacity(0.08),
          leading: Icon(
            icon,
            color: isSelected ? theme.colorScheme.primary : Colors.grey,
          ),
          title: Text(
            label,
            style: TextStyle(
              fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
              color: isSelected ? theme.colorScheme.primary : null,
              fontSize: 13,
            ),
          ),
        ),
      ),
    );
  }

  // Native Flutter Connection Settings View
  Widget _buildSettingsView() {
    final theme = Theme.of(context);

    return Container(
      color: theme.scaffoldBackgroundColor,
      padding: const EdgeInsets.all(32),
      child: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 550),
          child: Card(
            elevation: 4,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
            child: Padding(
              padding: const EdgeInsets.all(32),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Connection Settings',
                    style: theme.textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    'Configure connection address and startup actions.',
                    style: theme.textTheme.bodyMedium?.copyWith(color: Colors.grey),
                  ),
                  const SizedBox(height: 28),
                  
                  // URL input
                  Text(
                    'Server Base URL',
                    style: theme.textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 8),
                  TextField(
                    controller: _urlController,
                    decoration: InputDecoration(
                      hintText: 'e.g., https://client1.displaylanka.shop',
                      prefixIcon: const Icon(Icons.lan_outlined),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                    ),
                  ),
                  const SizedBox(height: 20),
                  
                  // Default View Radio
                  Text(
                    'Default Startup View',
                    style: theme.textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 8),
                  Container(
                    decoration: BoxDecoration(
                      border: Border.all(color: Colors.grey.withOpacity(0.3)),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Column(
                      children: [
                        RadioListTile<String>(
                          title: const Text('Storefront (Customer View)', style: TextStyle(fontSize: 13)),
                          value: '/',
                          groupValue: defaultView,
                          onChanged: (val) {
                            if (val != null) setState(() => defaultView = val);
                          },
                        ),
                        const Divider(height: 1),
                        RadioListTile<String>(
                          title: const Text('Admin Dashboard', style: TextStyle(fontSize: 13)),
                          value: '/admin',
                          groupValue: defaultView,
                          onChanged: (val) {
                            if (val != null) setState(() => defaultView = val);
                          },
                        ),
                        const Divider(height: 1),
                        RadioListTile<String>(
                          title: const Text('POS Counter Checkout', style: TextStyle(fontSize: 13)),
                          value: '/admin/pos',
                          groupValue: defaultView,
                          onChanged: (val) {
                            if (val != null) setState(() => defaultView = val);
                          },
                        ),
                      ],
                    ),
                  ),
                  
                  const SizedBox(height: 32),
                  
                  // Action buttons
                  Row(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      OutlinedButton.icon(
                        onPressed: _clearCache,
                        icon: const Icon(Icons.cleaning_services_rounded, size: 16),
                        label: const Text('Clear Cache'),
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                      ),
                      const SizedBox(width: 12),
                      ElevatedButton.icon(
                        onPressed: _saveSettings,
                        icon: const Icon(Icons.save_rounded, size: 16),
                        label: const Text('Save configurations'),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: theme.colorScheme.primary,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  // Mobile navigation overlay menu
  void _showMobileMenu() {
    final theme = Theme.of(context);
    final isDark = theme.brightness == Brightness.dark;

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (context) {
        return Container(
          decoration: BoxDecoration(
            color: isDark ? const Color(0xFF0F172A) : Colors.white,
            borderRadius: const BorderRadius.only(
              topLeft: Radius.circular(28),
              topRight: Radius.circular(28),
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.15),
                blurRadius: 20,
                offset: const Offset(0, -5),
              )
            ],
          ),
          padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Pull Bar indicator
              Container(
                width: 36,
                height: 4,
                margin: const EdgeInsets.only(bottom: 24),
                decoration: BoxDecoration(
                  color: Colors.grey.withOpacity(0.5),
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              
              Text(
                'CLIENT MANAGER',
                style: theme.textTheme.titleSmall?.copyWith(
                  fontWeight: FontWeight.bold,
                  letterSpacing: 1.5,
                  color: Colors.grey,
                ),
              ),
              const SizedBox(height: 24),
              
              // Browser control buttons
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  _buildBrowserControl(
                    icon: Icons.arrow_back_ios_new_rounded,
                    label: 'Back',
                    onTap: () async {
                      Navigator.pop(context);
                      if (await webViewController?.canGoBack() ?? false) {
                        webViewController?.goBack();
                      }
                    },
                  ),
                  _buildBrowserControl(
                    icon: Icons.refresh_rounded,
                    label: 'Refresh',
                    onTap: () {
                      Navigator.pop(context);
                      webViewController?.reload();
                    },
                  ),
                  _buildBrowserControl(
                    icon: Icons.arrow_forward_ios_rounded,
                    label: 'Forward',
                    onTap: () async {
                      Navigator.pop(context);
                      if (await webViewController?.canGoForward() ?? false) {
                        webViewController?.goForward();
                      }
                    },
                  ),
                ],
              ),
              
              const SizedBox(height: 24),
              const Divider(),
              const SizedBox(height: 16),
              
              // App routing shortcuts
              _buildMobileMenuItem(
                icon: Icons.analytics_outlined,
                label: 'Admin Dashboard',
                isSelected: activeTab == SidebarTab.dashboard && !showNativeSettings,
                onTap: () {
                  Navigator.pop(context);
                  _loadTab(SidebarTab.dashboard);
                },
              ),
              _buildMobileMenuItem(
                icon: Icons.point_of_sale_rounded,
                label: 'POS Counter',
                isSelected: activeTab == SidebarTab.pos && !showNativeSettings,
                onTap: () {
                  Navigator.pop(context);
                  _loadTab(SidebarTab.pos);
                },
              ),
              _buildMobileMenuItem(
                icon: Icons.storefront_outlined,
                label: 'Storefront Portal',
                isSelected: activeTab == SidebarTab.storefront && !showNativeSettings,
                onTap: () {
                  Navigator.pop(context);
                  _loadTab(SidebarTab.storefront);
                },
              ),
              _buildMobileMenuItem(
                icon: Icons.settings_outlined,
                label: 'Settings Configuration',
                isSelected: showNativeSettings,
                onTap: () {
                  Navigator.pop(context);
                  _loadTab(SidebarTab.settings);
                },
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildBrowserControl({
    required IconData icon,
    required String label,
    required VoidCallback onTap,
  }) {
    final theme = Theme.of(context);
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        width: 70,
        padding: const EdgeInsets.symmetric(vertical: 8),
        child: Column(
          children: [
            Icon(icon, size: 20, color: theme.colorScheme.primary),
            const SizedBox(height: 4),
            Text(label, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
          ],
        ),
      ),
    );
  }

  Widget _buildMobileMenuItem({
    required IconData icon,
    required String label,
    required bool isSelected,
    required VoidCallback onTap,
  }) {
    final theme = Theme.of(context);
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      child: ListTile(
        onTap: onTap,
        selected: isSelected,
        selectedTileColor: theme.colorScheme.primary.withOpacity(0.08),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        leading: Icon(
          icon,
          color: isSelected ? theme.colorScheme.primary : Colors.grey,
        ),
        title: Text(
          label,
          style: TextStyle(
            fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
            color: isSelected ? theme.colorScheme.primary : null,
            fontSize: 14,
          ),
        ),
      ),
    );
  }
}