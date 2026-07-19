import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../utils/format_utils.dart';

/// VAT / tax calculator matching the reference screen: Amount, Tax Rate %
/// (defaults to Tanzania's 18% VAT), and an Inclusive/Exclusive toggle.
class TaxCalculatorScreen extends StatefulWidget {
  const TaxCalculatorScreen({super.key});

  @override
  State<TaxCalculatorScreen> createState() => _TaxCalculatorScreenState();
}

class _TaxCalculatorScreenState extends State<TaxCalculatorScreen> {
  final _amountCtrl = TextEditingController();
  final _rateCtrl = TextEditingController(text: '18');
  String _type = 'Inclusive';

  double? _taxAmount;
  double? _netAmount;
  double? _grossAmount;

  void _calculate() {
    final amount = double.tryParse(_amountCtrl.text) ?? 0;
    final rate = double.tryParse(_rateCtrl.text) ?? 0;

    if (amount <= 0) {
      setState(() {
        _taxAmount = null;
        _netAmount = null;
        _grossAmount = null;
      });
      return;
    }

    if (_type == 'Inclusive') {
      final net = amount / (1 + rate / 100);
      setState(() {
        _netAmount = net;
        _taxAmount = amount - net;
        _grossAmount = amount;
      });
    } else {
      final tax = amount * rate / 100;
      setState(() {
        _taxAmount = tax;
        _netAmount = amount;
        _grossAmount = amount + tax;
      });
    }
  }

  @override
  void dispose() {
    _amountCtrl.dispose();
    _rateCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Tax Calculator')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _label('Amount'),
            TextField(controller: _amountCtrl, keyboardType: TextInputType.number, onChanged: (_) => _calculate(), decoration: const InputDecoration(prefixText: 'TSh ')),
            const SizedBox(height: 16),
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _label('Tax Rate'),
                      TextField(controller: _rateCtrl, keyboardType: TextInputType.number, onChanged: (_) => _calculate(), decoration: const InputDecoration(suffixText: '%')),
                    ],
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _label('Type'),
                      InkWell(
                        onTap: _pickType,
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
                          decoration: BoxDecoration(borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.divider)),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(_type, style: const TextStyle(fontWeight: FontWeight.w600)),
                              const Icon(Icons.chevron_right, size: 18, color: AppColors.textHint),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 24),
            if (_taxAmount != null) _resultCard(),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: _calculate,
              style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
              child: const Text('Calculate Tax'),
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
              const Text('Tax Amount', style: TextStyle(fontWeight: FontWeight.w600)),
              Text(FormatUtils.currency(_taxAmount!), style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: AppColors.primary)),
            ],
          ),
          const Divider(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Net Amount', style: TextStyle(color: AppColors.textSecondary)),
              Text(FormatUtils.currency(_netAmount!), style: const TextStyle(fontWeight: FontWeight.w700)),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Gross Amount', style: TextStyle(color: AppColors.textSecondary)),
              Text(FormatUtils.currency(_grossAmount!), style: const TextStyle(fontWeight: FontWeight.w700)),
            ],
          ),
        ],
      ),
    );
  }

  void _pickType() {
    showModalBottomSheet(
      context: context,
      builder: (ctx) => Column(
        mainAxisSize: MainAxisSize.min,
        children: ['Inclusive', 'Exclusive'].map((o) => ListTile(
              title: Text(o),
              trailing: _type == o ? const Icon(Icons.check, color: AppColors.primary) : null,
              onTap: () {
                setState(() => _type = o);
                Navigator.pop(ctx);
                _calculate();
              },
            )).toList(),
      ),
    );
  }
}
