import 'package:flutter/material.dart';

enum ToastType { success, error, warning, info }

class ToastHelper {
  static void show(BuildContext context, ToastType type, String message) {
    final colors = {
      ToastType.success: Colors.green,
      ToastType.error: Colors.red,
      ToastType.warning: Colors.orange,
      ToastType.info: Colors.blue,
    };

    final icons = {
      ToastType.success: Icons.check_circle,
      ToastType.error: Icons.error,
      ToastType.warning: Icons.warning,
      ToastType.info: Icons.info,
    };

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            Icon(icons[type], color: Colors.white, size: 20),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                message,
                style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500),
              ),
            ),
          ],
        ),
        backgroundColor: colors[type],
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.fromLTRB(16, 0, 16, 80),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        duration: const Duration(seconds: 3),
      ),
    );
  }

  static void success(BuildContext context, String message) =>
      show(context, ToastType.success, message);

  static void error(BuildContext context, String message) =>
      show(context, ToastType.error, message);

  static void warning(BuildContext context, String message) =>
      show(context, ToastType.warning, message);

  static void info(BuildContext context, String message) =>
      show(context, ToastType.info, message);
}
