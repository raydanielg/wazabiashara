import 'package:flutter_contacts/flutter_contacts.dart';

/// Thin wrapper around flutter_contacts — used by AddPartyScreen for both
/// the "Pick from Contacts" button (native picker, no bulk permission
/// needed) and the typeahead suggestions while typing a party name (needs
/// full read access, requested lazily and cached for the session).
class ContactsHelper {
  ContactsHelper._internal();
  static final ContactsHelper instance = ContactsHelper._internal();

  List<Contact>? _cache;
  bool _permissionDenied = false;

  Future<List<Contact>> _ensureLoaded() async {
    if (_cache != null) return _cache!;
    if (_permissionDenied) return [];
    try {
      final granted = await FlutterContacts.requestPermission(readonly: true);
      if (!granted) {
        _permissionDenied = true;
        return [];
      }
      _cache = await FlutterContacts.getContacts(withProperties: true);
      return _cache!;
    } catch (_) {
      // Contacts are a nice-to-have — never block Add Party on this.
      return [];
    }
  }

  /// Matches used for the inline typeahead dropdown while typing a name.
  Future<List<Contact>> search(String query) async {
    if (query.trim().length < 2) return [];
    final contacts = await _ensureLoaded();
    final q = query.toLowerCase();
    return contacts.where((c) => c.displayName.toLowerCase().contains(q)).take(6).toList();
  }

  /// Opens the device's native contact picker UI — doesn't require the bulk
  /// READ_CONTACTS permission on most platforms since the OS handles the
  /// picking itself and only hands back the one contact chosen.
  Future<Contact?> pickOne() async {
    try {
      return await FlutterContacts.openExternalPick();
    } catch (_) {
      return null;
    }
  }
}
