import 'package:onesignal_flutter/onesignal_flutter.dart';
import 'package:flutter/foundation.dart';

class NotificationService {
  static bool _isInitialized = false;

  static Future<void> initialize(String appId) async {
    if (_isInitialized) return;
    try {
      // Set debug logging
      OneSignal.Debug.setLogLevel(OSLogLevel.verbose);

      // Initialize OneSignal
      OneSignal.initialize(appId);

      // Request push notification permissions
      await OneSignal.Notifications.requestPermission(true);

      _isInitialized = true;
      debugPrint("OneSignal successfully initialized with App ID: $appId");
    } catch (e) {
      debugPrint("Failed to initialize OneSignal: $e");
    }
  }

  static Future<void> loginUser(String externalId) async {
    try {
      await OneSignal.login(externalId);
      debugPrint("OneSignal linked external user ID: $externalId");
    } catch (e) {
      debugPrint("Failed to login user to OneSignal: $e");
    }
  }

  static Future<void> logoutUser() async {
    try {
      await OneSignal.logout();
      debugPrint("OneSignal external user ID unlinked.");
    } catch (e) {
      debugPrint("Failed to logout user from OneSignal: $e");
    }
  }
}
