import 'dart:math' as math;
import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../utils/format_utils.dart';

/// Simple vs compound interest calculator matching the reference screen:
/// Principal Amount, Interest Rate (p.a), Time Period (with Year/Month
/// unit), and a result panel.
class InterestCalculatorScreen extends StatefulWidget {
  const InterestCalculatorScreen({super.key});

  @override
  State<InterestCalculatorScreen> createState() => _InterestCalculatorScreenState();
}

class _InterestCalculatorScreenState extends State<InterestCalculatorScreen> {
  final _principalCtrl = TextEditingController();
  final _rateCtrl = TextEditingController();
  final _timeCtrl = TextEditingController();
  String _timeUnit = 'Year';
  bool _compound = false;

  double? _interest;
  double? _total;

  void _calculate() {
    final p = double.tryParse(_principalCtrl.text) ?? 0;
    final r = double.tryParse(_rateCtrl.text) ?? 0;
    final tRaw = double.tryParse(_timeCtrl.text) ?? 0;
    final years = _timeUnit == 'Year' ? tRaw : tRaw / 12;

    if (p <= 0 || years <= 0) {
      setState(() {
        _interest = null;
        _total = null;
      });
      return;
    }

    double interest;
    if (_compound) {
      final total = p * math.pow(1 + r / 100, years);
      interest = total - p;
    } else {
      interest = p * r * years / 100;
    }

    setState(() {
      _interest = interest;
      _total = p + interest;
    });
  }

  @override
  void dispose() {
    _principalCtrl.dispose();
    _rateCtrl.dispose();
    _timeCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Interest Calculator')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _label('Principal Amount'),
            TextField(controller: _principalCtrl, keyboardType: TextInputType.number, onChanged: (_) => _calculate(), decoration: const InputDecoration(prefixText: 'TSh ')),
            const SizedBox(height: 16),
            _label('Interest Rate (p.a)'),
            TextField(controller: _rateCtrl, keyboardType: TextInputType.number, onChanged: (_) => _calculate(), decoration: const InputDecoration(suffixText: '%')),
            const SizedBox(height: 16),
            _label('Time Period'),
            Row(
              children: [
                Expanded(
                  child: TextField(controller: _timeCtrl, keyboardType: TextInputType.number, onChanged: (_) => _calculate()),
                ),
                const SizedBox(width: 10),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 4),
                  decoration: BoxDecoration(borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.divider)),
                  child: DropdownButton<String>(
                    value: _timeUnit,
                    underline: const SizedBox.shrink(),
                    items: const [DropdownMenuItem(value: 'Year', child: Text('Year')), DropdownMenuItem(value: 'Month', child: Text('Month'))],
                    onChanged: (v) {
                      setState(() => _timeUnit = v ?? 'Year');
                      _calculate();
                    },
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Row(
              children: [
                ChoiceChip(
                  label: const Text('Simple'),
                  selected: !_compound,
                  onSelected: (_) { setState(() => _compound = false); _calculate(); },
                  selectedColor: AppColors.primary,
                  labelStyle: TextStyle(color: !_compound ? Colors.white : AppColors.textPrimary, fontWeight: FontWeight.w700),
                ),
                const SizedBox(width: 8),
                ChoiceChip(
                  label: const Text('Compound'),
                  selected: _compound,
                  onSelected: (_) { setState(() => _compound = true); _calculate(); },
                  selectedColor: AppColors.primary,
                  labelStyle: TextStyle(color: _compound ? Colors.white : AppColors.textPrimary, fontWeight: FontWeight.w700),
                ),
              ],
            ),
            const SizedBox(height: 24),
            if (_interest != null) _resultCard(),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: _calculate,
              style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
              child: const Text('Calculate Interest'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _label(String text) => Padding(
        padding: const EdgeInsets.only(bottom: 6),
        child: Text(text, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
      );

  Widget _resultCard() {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.06), borderRadius: BorderRadius.circular(16), border: Border.all(color: AppColors.primary.withValues(alpha: 0.2))),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Interest Earned', style: TextStyle(fontWeight: FontWeight.w600)),
              Text(FormatUtils.currency(_interest!), style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: AppColors.primary)),
            ],
          ),
          const Divider(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Total Amount', style: TextStyle(color: AppColors.textSecondary)),
              Text(FormatUtils.currency(_total!), style: const TextStyle(fontWeight: FontWeight.w800)),
            ],
          ),
        ],
      ),
    );
  }
}
