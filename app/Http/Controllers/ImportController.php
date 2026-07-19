<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    /**
     * Read an uploaded CSV file into an array of associative rows keyed by
     * the lower-cased header column names in the first row.
     */
    private function readCsv($file): array
    {
        $rows = [];
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return $rows;
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return $rows;
        }

        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        while (($line = fgetcsv($handle)) !== false) {
            if (count($line) === 1 && trim((string) $line[0]) === '') {
                continue; // skip blank lines
            }
            $row = [];
            foreach ($header as $i => $key) {
                $row[$key] = trim((string) ($line[$i] ?? ''));
            }
            $rows[] = $row;
        }

        fclose($handle);
        return $rows;
    }

    public function importParties(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $businessId = auth()->user()->business_id;
        $rows = $this->readCsv($request->file('file'));

        $imported = 0;
        $skipped = [];

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2; // account for header row
            $name = $row['name'] ?? '';

            if ($name === '') {
                $skipped[] = "Row {$rowNum}: missing name";
                continue;
            }

            Customer::create([
                'business_id' => $businessId,
                'name' => $name,
                'phone' => $row['phone'] ?? null,
                'email' => $row['email'] ?? null,
            ]);

            $imported++;
        }

        $result = [
            'type' => 'parties',
            'total' => count($rows),
            'imported' => $imported,
            'skipped' => $skipped,
        ];

        if ($request->expectsJson()) {
            return response()->json(array_merge(['success' => true], $result));
        }

        return redirect()->route('import.index')->with('import_result', $result);
    }

    public function importItems(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $businessId = auth()->user()->business_id;
        $rows = $this->readCsv($request->file('file'));

        $imported = 0;
        $skipped = [];

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2;
            $name = $row['name'] ?? '';

            if ($name === '') {
                $skipped[] = "Row {$rowNum}: missing name";
                continue;
            }

            $categoryId = null;
            $categoryName = $row['category'] ?? '';
            if ($categoryName !== '') {
                $category = Category::firstOrCreate(
                    ['business_id' => $businessId, 'type' => 'item', 'name' => $categoryName],
                    []
                );
                $categoryId = $category->id;
            }

            Product::create([
                'business_id' => $businessId,
                'category_id' => $categoryId,
                'name' => $name,
                'unit' => 'piece',
                'cost_price' => 0,
                'selling_price' => is_numeric($row['price'] ?? null) ? (float) $row['price'] : 0,
                'status' => 'active',
            ]);

            $imported++;
        }

        $result = [
            'type' => 'items',
            'total' => count($rows),
            'imported' => $imported,
            'skipped' => $skipped,
        ];

        if ($request->expectsJson()) {
            return response()->json(array_merge(['success' => true], $result));
        }

        return redirect()->route('import.index')->with('import_result', $result);
    }
}
