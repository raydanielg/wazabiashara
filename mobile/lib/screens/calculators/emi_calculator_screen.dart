import 'dart:math' as math;
import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../utils/format_utils.dart';

/// Standard reducing-balance EMI calculator, matching the reference screen:
/// Loan Amount, Loan Interest Rate (p.a), Loan Tenure (months), and a
/// result panel with monthly EMI, total interest and total payment.
class EmiCalculatorScreen extends StatefulWidget {
  const EmiCalculatorScreen({super.key});

  @override
  State<EmiCalculatorScreen> createState() => _EmiCalculatorScreenState();
}

class _EmiCalculatorScreenState extends State<EmiCalculatorScreen> {
  final _amountCtrl = TextEditingController();
  final _rateCtrl = TextEditingController();
  final _tenureCtrl = TextEditingController();

  double? _emi;
  double? _totalInterest;
  double? _totalPayment;

  void _calculate() {
    final p = double.tryParse(_amountCtrl.text) ?? 0;
    final annualRate = double.tryParse(_rateCtrl.text) ?? 0;
    final months = int.tryParse(_tenureCtrl.text) ?? 0;

    if (p <= 0 || months <= 0) {
      setState(() {
        _emi = null;
        _totalInterest = null;
        _totalPayment = null;
      });
      return;
    }

    final r = annualRate / 12 / 100;
    double emi;
    if (r == 0) {
      emi = p / months;
    } else {
      emi = p * r * math.pow(1 + r, months) / (math.pow(1 + r, months) - 1);
    }
    final total = emi * months;

    setState(() {
      _emi = emi;
      _totalPayment = total;
      _totalInterest = total - p;
    });
  }

  @override
  void dispose() {
    _amountCtrl.dispose();
    _rateCtrl.dispose();
    _tenureCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('EMI Calculator')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _label('Loan Amount'),
            TextField(
              controller: _amountCtrl,
              keyboardType: TextInputType.number,
              onChanged: (_) => _calculate(),
              decoration: const InputDecoration(prefixText: 'TSh '),
            ),
            const SizedBox(height: 16),
            _label('Loan Interest Rate (p.a)'),
            TextField(
              controller: _rateCtrl,
              keyboardType: TextInputType.number,
              onChanged: (_) => _calculate(),
              decoration: const InputDecoration(suffixText: '%'),
            ),
            const SizedBox(height: 16),
            _label('Loan tenure (months)'),
            TextField(
              controller: _tenureCtrl,
              keyboardType: TextInputType.number,
              onChanged: (_) => _calculate(),
            ),
            const SizedBox(height: 24),
            if (_emi != null) _resultCard(),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: _calculate,
              style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
              child: const Text('Calculate EMI'),
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
          _resultRow('Monthly EMI', FormatUtils.currency(_emi!), emphasize: true),
          const Divider(height: 24),
          _resultRow('Total Interest', FormatUtils.currency(_totalInterest!)),
          const SizedBox(height: 8),
          _resultRow('Total Payment', FormatUtils.currency(_totalPayment!)),
        ],
      ),
    );
  }

  Widget _resultRow(String label, String value, {bool emphasize = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: TextStyle(fontSize: emphasize ? 14 : 13, color: AppColors.textSecondary, fontWeight: emphasize ? FontWeight.w600 : FontWeight.w500)),
        Text(value, style: TextStyle(fontSize: emphasize ? 22 : 14, fontWeight: FontWeight.w800, color: emphasize ? AppColors.primary : AppColors.textPrimary)),
      ],
    );
  }
}
