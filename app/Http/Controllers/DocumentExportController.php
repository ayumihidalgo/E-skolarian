<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DocumentExportController extends Controller
{
    public function export(Request $request)
    {
        // Build the query with the same filtering logic as documentHistory method
        $query = DB::table('submitted_documents')
            ->whereNull('archived_at')
            ->where('status', 'Approved');
        
        // Apply the same filters as in documentHistory method
        if ($request->has('organization') && $request->organization != 'All' && $request->organization != 'Organization') {
            $query->where('control_tag', 'LIKE', $request->organization . '_%');
        }
        
        if ($request->has('type') && $request->type != 'All' && $request->type != 'Type') {
            $query->where('type', $request->type);
        }
        
        // Apply date filtering - THIS IS THE KEY ADDITION
        if ($request->has('start_date') && !empty($request->start_date)) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($request->has('end_date') && !empty($request->end_date)) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }
        
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('subject', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('control_tag', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('type', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Apply the same sorting as in the table view
        if ($request->has('sort_by') && $request->has('sort_dir')) {
            $query->orderBy($request->sort_by, $request->sort_dir);
        } else {
            // Default sorting
            $query->orderBy('created_at', 'desc');
        }
        
        // Get all documents (not paginated for export)
        $documents = $query->get();
        
        // Organization mapping data
        $orgMap = [
            'ACAP' => 'Association of Competent and Aspiring Psychologists',
            'AECES' => 'Association of Electronics and Communications Engineering Students',
            'ELITE' => 'Eligible League of Information Technology Enthusiasts',
            'GIVE' => 'Guild of Imporous and Valuable Educators',
            'JEHRA' => 'Junior Executive of Human Resource Association',
            'JMAP' => 'Junior Marketing Association of the Philippines',
            'JPIA' => 'Junior Philippine Institute of Accountants',
            'PIIE' => 'Philippine Institute of Industrial Engineers',
            'AGDS' => 'Artist Guild Dance Squad',
            'Chorale' => 'PUP SRC Chorale',
            'SIGMA' => 'Supreme Innovators Guild for Mathematics Advancements',
            'TAPNOTCH' => 'Transformation Advocates through Purpose-driven and Noble Objectives Toward Community Holism',
            'OSC' => 'Office of the Student Council',
        ];

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('E-Skolarian')
            ->setLastModifiedBy('E-Skolarian')
            ->setTitle('Document History Report')
            ->setSubject('Document History Report')
            ->setDescription('Document History filtered report generated on ' . Carbon::now()->format('Y-m-d H:i'));
        
        // Set document title
        $sheet->setTitle('Document History');
        
        // Add header with filter information
        $sheet->setCellValue('A1', 'Document History Report');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add filter information
        $row = 3;
        $sheet->setCellValue('A' . $row, 'Generated on: ' . now()->format('F j, Y g:i A'));
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $row++;
        
        // Show applied filters
        $filters = [];
        
        if ($request->has('organization') && $request->organization != 'All' && $request->organization != 'Organization') {
            $filters[] = 'Organization: ' . $request->organization;
        }
        
        if ($request->has('type') && $request->type != 'All' && $request->type != 'Type') {
            $filters[] = 'Type: ' . $request->type;
        }
        
        if ($request->has('search') && !empty($request->search)) {
            $filters[] = 'Search: "' . $request->search . '"';
        }
        
        // Show date range filter prominently
        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $startDisplay = $request->has('start_date_display') ? $request->start_date_display : Carbon::parse($request->start_date)->format('m/d/Y');
            $endDisplay = $request->has('end_date_display') ? $request->end_date_display : Carbon::parse($request->end_date)->format('m/d/Y');
            $filters[] = 'Date Range: ' . $startDisplay . ' - ' . $endDisplay;
        } elseif ($request->has('start_date') && !empty($request->start_date)) {
            $startDisplay = $request->has('start_date_display') ? $request->start_date_display : Carbon::parse($request->start_date)->format('m/d/Y');
            $filters[] = 'From Date: ' . $startDisplay;
        } elseif ($request->has('end_date') && !empty($request->end_date)) {
            $endDisplay = $request->has('end_date_display') ? $request->end_date_display : Carbon::parse($request->end_date)->format('m/d/Y');
            $filters[] = 'Until Date: ' . $endDisplay;
        }
        
        if (!empty($filters)) {
            $sheet->setCellValue('A' . $row, 'Applied Filters:');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $row++;
            
            foreach ($filters as $filter) {
                $sheet->setCellValue('A' . $row, '• ' . $filter);
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $row++;
            }
        } else {
            $sheet->setCellValue('A' . $row, 'No filters applied - showing all approved documents');
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $row++;
        }
        
        $row++; // Add extra space
        
        // Set up table headers
        $headers = ['Control Tag', 'Organization', 'Subject', 'Date Submitted', 'Type', 'Status'];
        $column = 'A';
        
        foreach ($headers as $header) {
            $sheet->setCellValue($column . $row, $header);
            $sheet->getStyle($column . $row)->getFont()->setBold(true);
            $sheet->getStyle($column . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('7A1212'); // Your brand color
            $sheet->getStyle($column . $row)->getFont()->getColor()->setRGB('FFFFFF');
            $column++;
        }
        
        $headerRow = $row;
        $row++;
        
        // Add data rows
        foreach ($documents as $document) {
            // Extract organization name from control tag
            $parts = explode('_', $document->control_tag);
            $acronym = count($parts) > 0 ? $parts[0] : '';
            $orgName = isset($orgMap[$acronym]) ? $orgMap[$acronym] : $acronym;
            
            $sheet->setCellValue('A' . $row, $document->control_tag);
            $sheet->setCellValue('B' . $row, $orgName);
            $sheet->setCellValue('C' . $row, $document->subject);
            $sheet->setCellValue('D' . $row, Carbon::parse($document->created_at)->format('m/d/Y'));
            $sheet->setCellValue('E' . $row, $document->type);
            $sheet->setCellValue('F' . $row, $document->status);
            
            $row++;
        }
        
        // Add total count
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Documents: ' . count($documents));
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        
        // Auto-size columns for better readability
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Add borders to the data table
        $dataStartRow = $headerRow;
        $dataEndRow = $row - 2; // Exclude the total row
        
        if ($dataEndRow >= $dataStartRow) {
            $sheet->getStyle('A' . $dataStartRow . ':F' . $dataEndRow)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
        }
        
        // Create filename with timestamp and filters
        $filename = 'document_history_' . now()->format('Y-m-d_H-i-s');
        
        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $filename .= '_' . Carbon::parse($request->start_date)->format('Ymd') . '_to_' . Carbon::parse($request->end_date)->format('Ymd');
        }
        
        $filename .= '.xlsx';
        
        // Set up the writer and download
        $writer = new Xlsx($spreadsheet);
        
        return response()->stream(
            function() use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }
}