<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{
    /**
     * Show reports dashboard with manual pagination for an array/collection.
     */
    public function index(Request $request)
    {
        $raw = [
            ['id' => 'financial', 'name' => 'Financial Report'],
            ['id' => 'payroll',   'name' => 'Payroll Report'],
            ['id' => 'expenses',  'name' => 'Expense Report'],
            // add more items here if you want multiple pages for testing
        ];

        // Normalize items to objects (title/type/date etc.)
        $items = collect($raw)->map(function ($r) {
            return (object) [
                'id'    => $r['id'] ?? null,
                'title' => $r['name'] ?? ($r['title'] ?? 'Untitled'),
                'type'  => $r['type'] ?? ($r['id'] ?? 'general'),
                'date'  => $r['date'] ?? now()->toDateString(),
            ];
        });

        // Pagination parameters
        $perPage = 10;
        $page = (int) $request->get('page', 1);
        $offset = ($page - 1) * $perPage;

        // Slice items for the current page
        $currentItems = $items->slice($offset, $perPage)->values();

        // Create LengthAwarePaginator
        $paginator = new LengthAwarePaginator(
            $currentItems,
            $items->count(),
            $perPage,
            $page,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Pass paginator to the view so ->links() works
        return view('reports.index', ['reports' => $paginator]);
    }

    /**
     * Show a single report (placeholder)
     */
    public function show(string $id)
    {
        $report = (object)[
            'id'    => $id,
            'title' => ucfirst($id) . ' Report',
            'type'  => $id,
            'date'  => now()->toDateString(),
        ];

        return view('reports.show', compact('report'));
    }
}
