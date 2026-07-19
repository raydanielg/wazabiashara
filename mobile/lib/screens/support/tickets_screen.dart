import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';

/// Matches the reference "View your Tickets" / "Report Problem" screen —
/// Open/Closed tabs over an empty-state illustration and a "Report an
/// issue" action that opens a short form.
class TicketsScreen extends StatefulWidget {
  const TicketsScreen({super.key});

  @override
  State<TicketsScreen> createState() => _TicketsScreenState();
}

class _TicketsScreenState extends State<TicketsScreen> with SingleTickerProviderStateMixin {
  late final TabController _tabs;
  final List<String> _openTickets = [];
  final List<String> _closedTickets = [];

  @override
  void initState() {
    super.initState();
    _tabs = TabController(length: 2, vsync: this);
  }

  @override
  void dispose() {
    _tabs.dispose();
    super.dispose();
  }

  void _reportIssue() {
    final titleCtrl = TextEditingController();
    final descCtrl = TextEditingController();

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => Padding(
        padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
        child: Container(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Report an Issue', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
              const SizedBox(height: 20),
              TextField(controller: titleCtrl, autofocus: true, decoration: const InputDecoration(labelText: 'Subject')),
              const SizedBox(height: 12),
              TextField(controller: descCtrl, maxLines: 4, decoration: const InputDecoration(labelText: 'Describe the problem')),
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: () {
                  if (titleCtrl.text.trim().isEmpty) return;
                  setState(() => _openTickets.add(titleCtrl.text.trim()));
                  Navigator.pop(ctx);
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Issue reported — our tech team will get on it'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating),
                  );
                },
                child: const Text('Submit'),
              ),
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('View your Tickets'),
        bottom: TabBar(
          controller: _tabs,
          labelColor: AppColors.primary,
          unselectedLabelColor: AppColors.textSecondary,
          indicatorColor: AppColors.primary,
          labelStyle: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13),
          tabs: const [Tab(text: 'Open Tickets'), Tab(text: 'Closed Tickets')],
        ),
      ),
      body: TabBarView(
        controller: _tabs,
        children: [
          _ticketsList(_openTickets, open: true),
          _ticketsList(_closedTickets, open: false),
        ],
      ),
    );
  }

  Widget _ticketsList(List<String> tickets, {required bool open}) {
    if (tickets.isNotEmpty) {
      return ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: tickets.length,
        separatorBuilder: (_, __) => const SizedBox(height: 10),
        itemBuilder: (ctx, i) => Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.divider)),
          child: Row(
            children: [
              Icon(open ? Icons.flag_outlined : Icons.check_circle_outline, color: open ? AppColors.warning : AppColors.success),
              const SizedBox(width: 12),
              Expanded(child: Text(tickets[i], style: const TextStyle(fontWeight: FontWeight.w600))),
            ],
          ),
        ),
      );
    }

    return Padding(
      padding: const EdgeInsets.all(32),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const SizedBox(height: 40),
          Container(
            width: 100,
            height: 100,
            decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.06), shape: BoxShape.circle),
            child: Icon(Icons.flag_outlined, size: 44, color: AppColors.gold.withValues(alpha: 0.7)),
          ),
          const SizedBox(height: 24),
          const Text('Report Problem', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
          const SizedBox(height: 8),
          const Text(
            "Something not working? Let us know! Click the Report an issue button below, fill in the details, and our tech team will get on it.",
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 13, color: AppColors.textSecondary, height: 1.4),
          ),
          if (open) ...[
            const SizedBox(height: 28),
            ElevatedButton.icon(
              onPressed: _reportIssue,
              icon: const Icon(Icons.add),
              label: const Text('Report an issue'),
              style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
            ),
          ],
        ],
      ),
    );
  }
}
