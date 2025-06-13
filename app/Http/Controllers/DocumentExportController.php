<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use Dompdf\Dompdf;
use Dompdf\Options;

class DocumentExportController extends Controller
{
    public function export(Request $request)
    {
        // Define the standard document types (same as in AdminDocumentController)
        $standardTypes = [
            'Event Proposal',
            'General Plan of Activities',
            'Reports of Proceedings',
            'Constitution and By-Laws',
            'Fundraising Activities',
            'Request Letter',
            'Petition and Concern',
            'Memorandum of Agreement',
            'Off Campus Activities'
        ];

        // Get current admin user information
        $adminUser = Auth::user();
        $adminUsername = $adminUser ? $adminUser->username : 'Unknown Admin';
        $adminRole = $adminUser ? $adminUser->role_name : 'Unknown Role';

        // Check if specific documents are selected for export
        $selectedDocuments = $request->input('selected_documents', []);
        $isSelectiveExport = !empty($selectedDocuments) && is_array($selectedDocuments);

        // Build the query with LEFT JOIN to get usernames and the same filtering logic as documentHistory method
        $query = DB::table('submitted_documents')
            ->leftJoin('users', 'submitted_documents.user_id', '=', 'users.id')
            ->select('submitted_documents.*', 'users.username')
            ->whereNull('submitted_documents.archived_at')
            ->where('submitted_documents.status', 'Approved');
        
        // If specific documents are selected, filter by those IDs ONLY
        if ($isSelectiveExport) {
            // Sanitize and validate the selected document IDs
            $documentIds = array_filter(array_map('intval', $selectedDocuments));
            if (!empty($documentIds)) {
                $query->whereIn('submitted_documents.id', $documentIds);
            } else {
                // If no valid IDs provided, return empty result
                $documents = collect([]);
            }
        } else {
            // Apply the same filters as in documentHistory method when not selective export
            if ($request->has('organization') && $request->organization != 'All' && $request->organization != 'Organization') {
                $query->where('submitted_documents.control_tag', 'LIKE', $request->organization . '_%');
            }
            
            // Apply type filter - handle "Others" category
            if ($request->has('type') && $request->type != 'All' && $request->type != 'Type') {
                if ($request->type === 'Others') {
                    // Filter for documents with types NOT in the standard list
                    $query->whereNotIn('submitted_documents.type', $standardTypes);
                } else {
                    // Filter for specific standard type
                    $query->where('submitted_documents.type', $request->type);
                }
            }
            
            // Apply date filtering
            if ($request->has('start_date') && !empty($request->start_date)) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $query->where('submitted_documents.created_at', '>=', $startDate);
            }
            
            if ($request->has('end_date') && !empty($request->end_date)) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->where('submitted_documents.created_at', '<=', $endDate);
            }
            
            // Apply search filter (including username search)
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('submitted_documents.subject', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('submitted_documents.control_tag', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('submitted_documents.type', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('users.username', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            // Apply the same sorting as in the table view
            if ($request->has('sort_by') && $request->has('sort_dir')) {
                $column = $request->sort_by;
                $direction = $request->sort_dir;
                
                if ($column === 'organization') {
                    // Sort by username instead of organization acronym
                    $query->orderBy('users.username', $direction);
                } else {
                    // Prefix columns with table name to avoid ambiguity
                    $query->orderBy('submitted_documents.' . $column, $direction);
                }
            } else {
                // Default sorting
                $query->orderBy('submitted_documents.created_at', 'desc');
            }
        }
        
        // Get documents (not paginated for export)
        if (!isset($documents)) {
            $documents = $query->get();
        }
        
        // Organization mapping data (keeping for reference even though we use username now)
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
        
        // Set document properties with admin information
        $spreadsheet->getProperties()
            ->setCreator('E-Skolarian - ' . $adminUsername . ' (' . $adminRole . ')')
            ->setLastModifiedBy($adminUsername . ' (' . $adminRole . ')')
            ->setTitle('Document History Report')
            ->setSubject('Document History Report')
            ->setDescription('Document History filtered report generated by ' . $adminUsername . ' (' . $adminRole . ') on ' . Carbon::now()->format('Y-m-d H:i'));
        
        // Set document title
        $sheet->setTitle('Document History');
        
        // Add header with filter information
        $reportTitle = 'Document History Report'; // Always use this title
        $sheet->setCellValue('A1', $reportTitle);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add admin and generation information
        $row = 3;
        $sheet->setCellValue('A' . $row, 'Generated by: ' . $adminUsername . ' (' . $adminRole . ')');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Generated on: ' . now()->format('F j, Y g:i A'));
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row)->getFont()->setSize(11);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $row++;
        
        // Add export type information
        if ($isSelectiveExport) {
            $sheet->setCellValue('A' . $row, 'Export Type: Selected Documents (' . count($documents) . ' selected)');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $row++;
        }
        
        // Add a separator line
        $row++;
        
        // Show applied filters (for both selective and non-selective exports)
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
        
        // Show filters section for both types of exports
        if (!empty($filters)) {
            $sheet->setCellValue('A' . $row, 'Applied Filters:');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(11);
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $row++;
            
            foreach ($filters as $filter) {
                $sheet->setCellValue('A' . $row, '• ' . $filter);
                $sheet->getStyle('A' . $row)->getFont()->setSize(10);
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $row++;
            }
            
            // Add note for selective exports
            if ($isSelectiveExport) {
                $sheet->setCellValue('A' . $row, 'Note: Above filters were active when documents were selected, but export contains only selected documents.');
                $sheet->getStyle('A' . $row)->getFont()->setSize(9)->setItalic(true);
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $row++;
            }
        } else {
            if ($isSelectiveExport) {
                $sheet->setCellValue('A' . $row, 'No filters were applied when documents were selected - export contains only selected documents.');
                $sheet->getStyle('A' . $row)->getFont()->setSize(10);
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $row++;
            } else {
                $sheet->setCellValue('A' . $row, 'No filters applied - showing all approved documents');
                $sheet->getStyle('A' . $row)->getFont()->setSize(10);
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $row++;
            }
        }

        $row++; // Add extra space
        
        $headers = ['Control Tag', 'Organization', 'Title', 'Date Submitted', 'Type', 'Status'];
        $column = 'A';
        
        foreach ($headers as $header) {
            $sheet->setCellValue($column . $row, $header);
            $sheet->getStyle($column . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle($column . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('7A1212'); // Your brand color
            $sheet->getStyle($column . $row)->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($column . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($column . $row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $column++;
        }
        
        $headerRow = $row;
        $row++;
        
        // Add data rows
        foreach ($documents as $document) {
            // Show the actual document type value instead of 'Others'
            $actualType = $document->type; // Use the actual type from database
            
            $sheet->setCellValue('A' . $row, $document->control_tag);
            $sheet->setCellValue('B' . $row, $document->username ?? 'N/A'); // Show username instead of organization
            $sheet->setCellValue('C' . $row, $document->subject);
            $sheet->setCellValue('D' . $row, Carbon::parse($document->created_at)->format('m/d/Y g:i A'));
            $sheet->setCellValue('E' . $row, $actualType); // Use actual type value instead of display type
            $sheet->setCellValue('F' . $row, $document->status);
            
            // Apply font size to data rows
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setSize(10);
            
            // Add alternating row colors for better readability
            if (($row - $headerRow) % 2 == 0) {
                $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }
            
            $row++;
        }
        
        // Add summary information
        $row++;
        $summaryText = $isSelectiveExport ? 'Selected Documents: ' . count($documents) : 'Total Documents: ' . count($documents);
        $sheet->setCellValue('A' . $row, $summaryText);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->setCellValue('A' . $row, 'Report generated by: ' . $adminUsername . ' (' . $adminRole . ') on ' . now()->format('F j, Y g:i A'));
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Auto-size columns for better readability
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set minimum column widths for better appearance
        $sheet->getColumnDimension('A')->setWidth(15); // Control Tag
        $sheet->getColumnDimension('B')->setWidth(15); // Username
        $sheet->getColumnDimension('C')->setWidth(30); // Subject
        $sheet->getColumnDimension('D')->setWidth(18); // Date Submitted
        $sheet->getColumnDimension('E')->setWidth(20); // Type
        $sheet->getColumnDimension('F')->setWidth(12); // Status

        // Add borders to the data table
        $dataStartRow = $headerRow;
        $dataEndRow = $row - 3; // Exclude the summary rows

        if ($dataEndRow >= $dataStartRow) {
            $sheet->getStyle('A' . $dataStartRow . ':F' . $dataEndRow)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            
            // Make header border thicker
            $sheet->getStyle('A' . $headerRow . ':F' . $headerRow)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_MEDIUM);
        }
        
        // Set row height for header
        $sheet->getRowDimension($headerRow)->setRowHeight(25);
        
        // Create filename with timestamp, admin, and export type
        $filename = $isSelectiveExport ? 'selected_documents_' . $adminUsername : 'document_history_' . $adminUsername;
        $filename .= '_' . now()->format('Y-m-d_H-i-s');
        
        if (!$isSelectiveExport && $request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
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

    public function exportPdf(Request $request)
    {
        try {
            // Define the standard document types (same as in AdminDocumentController)
            $standardTypes = [
                'Event Proposal',
                'General Plan of Activities',
                'Reports of Proceedings',
                'Constitution and By-Laws',
                'Fundraising Activities',
                'Request Letter',
                'Petition and Concern',
                'Memorandum of Agreement',
                'Off Campus Activities'
            ];

            // Get current admin user information
            $adminUser = Auth::user();
            $adminUsername = $adminUser ? $adminUser->username : 'Unknown Admin';
            $adminRole = $adminUser ? $adminUser->role_name : 'Unknown Role';

            // Check if specific documents are selected for export
            $selectedDocuments = $request->input('selected_documents', []);
            $isSelectiveExport = !empty($selectedDocuments) && is_array($selectedDocuments);

            // Build the query with LEFT JOIN to get usernames and the same filtering logic as documentHistory method
            $query = DB::table('submitted_documents')
                ->leftJoin('users', 'submitted_documents.user_id', '=', 'users.id')
                ->select('submitted_documents.*', 'users.username')
                ->whereNull('submitted_documents.archived_at')
                ->where('submitted_documents.status', 'Approved');
            
            // If specific documents are selected, filter by those IDs ONLY
            if ($isSelectiveExport) {
                // Sanitize and validate the selected document IDs
                $documentIds = array_filter(array_map('intval', $selectedDocuments));
                if (!empty($documentIds)) {
                    $query->whereIn('submitted_documents.id', $documentIds);
                } else {
                    // If no valid IDs provided, return empty result
                    $documents = collect([]);
                }
            } else {
                // Apply the same filters as in documentHistory method when not selective export
                if ($request->has('organization') && $request->organization != 'All' && $request->organization != 'Organization') {
                    $query->where('submitted_documents.control_tag', 'LIKE', $request->organization . '_%');
                }
                
                // Apply type filter - handle "Others" category
                if ($request->has('type') && $request->type != 'All' && $request->type != 'Type') {
                    if ($request->type === 'Others') {
                        // Filter for documents with types NOT in the standard list
                        $query->whereNotIn('submitted_documents.type', $standardTypes);
                    } else {
                        // Filter for specific standard type
                        $query->where('submitted_documents.type', $request->type);
                    }
                }
                
                // Apply date filtering
                if ($request->has('start_date') && !empty($request->start_date)) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $query->where('submitted_documents.created_at', '>=', $startDate);
                }
                
                if ($request->has('end_date') && !empty($request->end_date)) {
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                    $query->where('submitted_documents.created_at', '<=', $endDate);
                }
                
                // Apply search filter (including username search)
                if ($request->has('search') && !empty($request->search)) {
                    $searchTerm = $request->search;
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('submitted_documents.subject', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('submitted_documents.control_tag', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('submitted_documents.type', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('users.username', 'LIKE', "%{$searchTerm}%");
                    });
                }
                
                // Apply the same sorting as in the table view
                if ($request->has('sort_by') && $request->has('sort_dir')) {
                    $column = $request->sort_by;
                    $direction = $request->sort_dir;
                    
                    if ($column === 'organization') {
                        // Sort by username instead of organization acronym
                        $query->orderBy('users.username', $direction);
                    } else {
                        // Prefix columns with table name to avoid ambiguity
                        $query->orderBy('submitted_documents.' . $column, $direction);
                    }
                } else {
                    // Default sorting
                    $query->orderBy('submitted_documents.created_at', 'desc');
                }
            }
            
            // Get documents (not paginated for export)
            if (!isset($documents)) {
                $documents = $query->get();
            }

            // Prepare filter information for display
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
            
            // Date range filter
            $dateRangeFilter = '';
            if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
                $startDisplay = $request->has('start_date_display') ? $request->start_date_display : Carbon::parse($request->start_date)->format('m/d/Y');
                $endDisplay = $request->has('end_date_display') ? $request->end_date_display : Carbon::parse($request->end_date)->format('m/d/Y');
                $dateRangeFilter = 'Date Range: ' . $startDisplay . ' - ' . $endDisplay;
            } elseif ($request->has('start_date') && !empty($request->start_date)) {
                $startDisplay = $request->has('start_date_display') ? $request->start_date_display : Carbon::parse($request->start_date)->format('m/d/Y');
                $dateRangeFilter = 'From Date: ' . $startDisplay;
            } elseif ($request->has('end_date') && !empty($request->end_date)) {
                $endDisplay = $request->has('end_date_display') ? $request->end_date_display : Carbon::parse($request->end_date)->format('m/d/Y');
                $dateRangeFilter = 'Until Date: ' . $endDisplay;
            }

            // Generate HTML for PDF
            $html = $this->generatePdfHtml($documents, $filters, $dateRangeFilter, $isSelectiveExport, $adminUsername, $adminRole);

            // Create PDF using dompdf
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', false);
            $options->set('isRemoteEnabled', false);
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            // Create filename (same structure as Excel)
            $filename = $isSelectiveExport ? 'selected_documents_' . str_replace(' ', '_', $adminUsername) : 'document_history_' . str_replace(' ', '_', $adminUsername);
            $filename .= '_' . now()->format('Y-m-d_H-i-s');
            
            if (!$isSelectiveExport && $request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
                $filename .= '_' . Carbon::parse($request->start_date)->format('Ymd') . '_to_' . Carbon::parse($request->end_date)->format('Ymd');
            }
            
            $filename .= '.pdf';

            // Return the PDF as download
            return response()->streamDownload(function() use ($dompdf) {
                echo $dompdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('PDF Export Error: ' . $e->getMessage());
            
            // Return error response
            return response()->json([
                'error' => 'PDF generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generatePdfHtml($documents, $filters, $dateRangeFilter, $isSelectiveExport, $adminUsername, $adminRole)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Document History Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #333;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #7A1212;
            margin-bottom: 10px;
        }
        .admin-info {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .generated-info {
            font-size: 11px;
            margin-bottom: 10px;
        }
        .export-type {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .filters {
            margin-bottom: 15px;
            font-size: 10px;
        }
        .filters-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 5px;
        }
        .filter-item {
            margin-left: 10px;
            margin-bottom: 2px;
        }
        .note {
            font-style: italic;
            font-size: 9px;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }
        th {
            background-color: #7A1212;
            color: white;
            font-weight: bold;
            padding: 8px 4px;
            text-align: center;
            border: 1px solid #000;
        }
        td {
            padding: 6px 4px;
            border: 1px solid #ccc;
            vertical-align: top;
            word-wrap: break-word;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .summary {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            margin-top: 15px;
        }
        .footer {
            text-align: center;
            font-style: italic;
            font-size: 9px;
            margin-top: 10px;
        }
        .col-tag { width: 12%; }
        .col-org { width: 15%; }
        .col-title { width: 35%; }
        .col-date { width: 18%; }
        .col-type { width: 15%; }
        .col-status { width: 5%; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Document History Report</div>
        <div class="admin-info">Generated by: ' . htmlspecialchars($adminUsername) . ' (' . htmlspecialchars($adminRole) . ')</div>
        <div class="generated-info">Generated on: ' . now()->format('F j, Y g:i A') . '</div>';
        
        if ($isSelectiveExport) {
            $html .= '<div class="export-type">Export Type: Selected Documents (' . count($documents) . ' selected)</div>';
        }
        
        $html .= '</div>';

        // Add filters section (same as Excel)
        if (!empty($filters) || !empty($dateRangeFilter)) {
            $html .= '<div class="filters">
                <div class="filters-title">Applied Filters:</div>';
            
            if (!empty($dateRangeFilter)) {
                $html .= '<div class="filter-item">• ' . htmlspecialchars($dateRangeFilter) . '</div>';
            }
            
            foreach ($filters as $filter) {
                $html .= '<div class="filter-item">• ' . htmlspecialchars($filter) . '</div>';
            }
            
            if ($isSelectiveExport) {
                $html .= '<div class="note">Note: Above filters were active when documents were selected, but export contains only selected documents.</div>';
            }
            
            $html .= '</div>';
        } else {
            if ($isSelectiveExport) {
                $html .= '<div class="filters">No filters were applied when documents were selected - export contains only selected documents.</div>';
            } else {
                $html .= '<div class="filters">No filters applied - showing all approved documents</div>';
            }
        }

        // Add table (same structure as Excel)
        $html .= '<table>
            <thead>
                <tr>
                    <th class="col-tag">Control Tag</th>
                    <th class="col-org">Organization</th>
                    <th class="col-title">Title</th>
                    <th class="col-date">Date Submitted</th>
                    <th class="col-type">Type</th>
                    <th class="col-status">Status</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($documents as $document) {
            $actualType = $document->type; // Use actual type from database
            $html .= '<tr>
                <td>' . htmlspecialchars($document->control_tag) . '</td>
                <td>' . htmlspecialchars($document->username ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($document->subject) . '</td>
                <td>' . Carbon::parse($document->created_at)->format('m/d/Y g:i A') . '</td>
                <td>' . htmlspecialchars($actualType) . '</td>
                <td>' . htmlspecialchars($document->status) . '</td>
            </tr>';
        }

        $html .= '</tbody>
        </table>
        
        <div class="summary">';
        
        if ($isSelectiveExport) {
            $html .= 'Selected Documents: ' . count($documents);
        } else {
            $html .= 'Total Documents: ' . count($documents);
        }
        
        $html .= '</div>
        
        <div class="footer">
            Report generated by: ' . htmlspecialchars($adminUsername) . ' (' . htmlspecialchars($adminRole) . ') on ' . now()->format('F j, Y g:i A') . '
        </div>
    </body>
    </html>';

        return $html;
    }
}