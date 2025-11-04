<?php

namespace App\Traits;

use Dompdf\Dompdf;
use Dompdf\Options;
use League\Csv\Writer;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait Downloadable
{
    /**
     * Generate CSV download response
     */
    protected function downloadCsv(Collection $data, string $filename, array $headers = []): Response
    {
        $csv = Writer::createFromString('');
        
        // Add UTF-8 BOM for Excel compatibility
        $csv->insertOne(["\xEF\xBB\xBF"]);
        
        if (!empty($data) && !empty($headers)) {
            // Insert headers
            $csv->insertOne($headers);
            
            // Insert data rows
            foreach ($data as $item) {
                if ($item instanceof Model) {
                    $row = $this->formatModelForCsv($item, $headers);
                } else {
                    $row = $this->formatArrayForCsv($item, $headers);
                }
                $csv->insertOne($row);
            }
        }
        
        $filename = $this->sanitizeFilename($filename) . '.csv';
        
        return response($csv->toString(), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
    
    /**
     * Generate PDF download response
     */
    protected function downloadPdf(string $html, string $filename, array $options = []): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);
        $pdfOptions->set('isRemoteEnabled', false); // Security: disable remote content
        $pdfOptions->set('isJavascriptEnabled', false);
        $pdfOptions->set('isFontSubsettingEnabled', true);
        $pdfOptions->set('defaultMediaType', 'print');
        $pdfOptions->set('isCssFloatEnabled', true);
        $pdfOptions->set('chroot', public_path()); // Allow access to public directory for images
        
        // Merge custom options
        foreach ($options as $key => $value) {
            $pdfOptions->set($key, $value);
        }
        
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = $this->sanitizeFilename($filename) . '.pdf';
        
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
    
    /**
     * Generate HTML for PDF from view
     */
    protected function generatePdfHtml(string $view, array $data = []): string
    {
        return view($view, $data)->render();
    }
    
    /**
     * Format model data for CSV export
     */
    protected function formatModelForCsv(Model $model, array $headers): array
    {
        $row = [];
        foreach ($headers as $header) {
            $value = $this->getNestedValue($model, $header);
            $row[] = $this->formatCsvValue($value);
        }
        return $row;
    }
    
    /**
     * Format array data for CSV export
     */
    protected function formatArrayForCsv($item, array $headers): array
    {
        $row = [];
        foreach ($headers as $header) {
            $value = is_array($item) ? ($item[$header] ?? '') : ($item->$header ?? '');
            $row[] = $this->formatCsvValue($value);
        }
        return $row;
    }
    
    /**
     * Get nested value from model (e.g., 'user.name')
     */
    protected function getNestedValue($object, string $key)
    {
        if (str_contains($key, '.')) {
            $keys = explode('.', $key);
            $value = $object;
            foreach ($keys as $nestedKey) {
                $value = $value->$nestedKey ?? '';
                if (empty($value)) break;
            }
            return $value;
        }
        
        return $object->$key ?? '';
    }
    
    /**
     * Format value for CSV (handle dates, numbers, etc.)
     */
    protected function formatCsvValue($value): string
    {
        if (is_null($value)) {
            return '';
        }
        
        if ($value instanceof \Carbon\Carbon) {
            return $value->format('Y-m-d H:i:s');
        }
        
        if (is_numeric($value)) {
            return (string) $value;
        }
        
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        
        // Remove HTML tags if any
        $value = strip_tags((string) $value);
        
        // Escape quotes and handle multiline
        return str_replace(['"', "\n", "\r"], ['""', ' ', ' '], $value);
    }
    
    /**
     * Sanitize filename for download
     */
    protected function sanitizeFilename(string $filename): string
    {
        // Remove dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $filename);
        
        // Add timestamp to make unique
        return $filename . '_' . date('Y_m_d_H_i_s');
    }
    
    /**
     * Get common CSV headers for financial data
     */
    protected function getFinancialCsvHeaders(): array
    {
        return [
            'id' => 'ID',
            'created_at' => 'Date',
            'amount' => 'Amount (RWF)',
            'description' => 'Description',
            'status' => 'Status'
        ];
    }
    
    /**
     * Get common CSV headers for project data
     */
    protected function getProjectCsvHeaders(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Project Name',
            'client_name' => 'Client',
            'contract_value' => 'Contract Value (RWF)',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'status' => 'Status',
            'created_at' => 'Created Date'
        ];
    }
    
    /**
     * Get common CSS for PDF styling
     */
    protected function getPdfStyles(): string
    {
        return "
        <style>
            body { 
                font-family: Arial, sans-serif; 
                font-size: 12px; 
                line-height: 1.4;
                margin: 20px;
            }
            .header { 
                text-align: center; 
                margin-bottom: 30px; 
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }
            .header h1 { 
                color: #333; 
                margin: 0 0 5px 0;
                font-size: 24px;
            }
            .header .subtitle { 
                color: #666; 
                font-size: 14px;
                margin: 0;
            }
            .meta-info {
                margin-bottom: 20px;
                font-size: 11px;
                color: #666;
            }
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-top: 20px;
            }
            th, td { 
                border: 1px solid #ddd; 
                padding: 8px; 
                text-align: left;
                font-size: 11px;
            }
            th { 
                background-color: #f5f5f5; 
                font-weight: bold;
            }
            .text-right { text-align: right; }
            .text-center { text-align: center; }
            .amount { font-weight: bold; color: #2563eb; }
            .total-row { 
                font-weight: bold; 
                background-color: #f0f9ff;
            }
            .footer {
                margin-top: 30px;
                text-align: center;
                font-size: 10px;
                color: #666;
                border-top: 1px solid #ddd;
                padding-top: 10px;
            }
        </style>";
    }
}