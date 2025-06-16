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

        // Build the query with LEFT JOIN to get usernames and admin role_name - UPDATED to match documentHistory query
        $query = DB::table('submitted_documents')
            ->leftJoin('users as submitter', 'submitted_documents.user_id', '=', 'submitter.id')
            ->leftJoin('users as admin', 'submitted_documents.received_by', '=', 'admin.id')
            ->select('submitted_documents.*', 'submitter.username', 'admin.role_name')
            ->whereNull('submitted_documents.archived_at')
            ->where('submitted_documents.status', 'Approved')
            ->where('admin.role_name', '!=', 'Student')
            ->whereNotNull('admin.role_name');
        
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
            // UPDATED: Use username for organization filter instead of control_tag
            if ($request->has('organization') && $request->organization != 'All' && $request->organization != 'Organization') {
                $query->where('submitter.username', $request->organization);
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
            
            // Apply search filter (including username search) - UPDATED to use submitter.username
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('submitted_documents.subject', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('submitted_documents.control_tag', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('submitted_documents.type', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('submitter.username', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            // Apply the same sorting as in the table view
            if ($request->has('sort_by') && $request->has('sort_dir')) {
                $column = $request->sort_by;
                $direction = $request->sort_dir;
                
                if ($column === 'organization') {
                    // Sort by username instead of organization acronym
                    $query->orderBy('submitter.username', $direction);
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
        
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Add PUP header section to the report
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'Polytechnic University of the Philippines');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'SANTA ROSA CAMPUS');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue('A3', 'City of Santa Rosa, Laguna');
        $sheet->getStyle('A3')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Get admin's role_name
        $adminRole = Auth::user()->role_name ?? 'Administrator';

        $sheet->mergeCells('A5:F5');
        $sheet->setCellValue('A5', $adminRole); // CHANGED: Use admin's role_name
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A6:F6');
        $sheet->setCellValue('A6', 'Document Repository Report'); // CHANGED from List of Activities
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Dynamic date range display
        $dateRangeText = '';
        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $startDisplay = $request->has('start_date_display') ? $request->start_date_display : Carbon::parse($request->start_date)->format('F j, Y');
            $endDisplay = $request->has('end_date_display') ? $request->end_date_display : Carbon::parse($request->end_date)->format('F j, Y');
            $dateRangeText = $startDisplay . ' - ' . $endDisplay;
        } elseif ($request->has('start_date') && !empty($request->start_date)) {
            $startDisplay = $request->has('start_date_display') ? $request->start_date_display : Carbon::parse($request->start_date)->format('F j, Y');
            $dateRangeText = 'From ' . $startDisplay;
        } elseif ($request->has('end_date') && !empty($request->end_date)) {
            $endDisplay = $request->has('end_date_display') ? $request->end_date_display : Carbon::parse($request->end_date)->format('F j, Y');
            $dateRangeText = 'Until ' . $endDisplay;
        }

        $sheet->mergeCells('A7:F7');
        $sheet->setCellValue('A7', $dateRangeText);
        $sheet->getStyle('A7')->getFont()->setSize(12);
        $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add space after header
        $row = 9;

        // Original report title (could be removed as we now have Document Repository Report above)
        $reportTitle = 'Document Repository Report';
        $sheet->setCellValue('A' . $row, $reportTitle);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Continue with existing code...
        
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
        
        // UPDATED HEADERS: Changed "Status" to "Submitted to"
        $headers = ['Control Tag', 'Organization', 'Title', 'Date Submitted', 'Type', 'Submitted to'];
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
            $sheet->setCellValue('F' . $row, $document->role_name ?? 'N/A'); // CHANGED: Show admin role_name instead of status
            
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
        $sheet->getColumnDimension('F')->setWidth(15); // Submitted to (role_name)

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
        $filename = $isSelectiveExport ? 'selected_documents_' . $adminUsername : 'document_repository_' . $adminUsername; // CHANGED from document_history to document_repository
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

            // Build the query with LEFT JOIN to get usernames and admin role_name - UPDATED to match documentHistory query
            $query = DB::table('submitted_documents')
                ->leftJoin('users as submitter', 'submitted_documents.user_id', '=', 'submitter.id')
                ->leftJoin('users as admin', 'submitted_documents.received_by', '=', 'admin.id')
                ->select('submitted_documents.*', 'submitter.username', 'admin.role_name')
                ->whereNull('submitted_documents.archived_at')
                ->where('submitted_documents.status', 'Approved')
                ->where('admin.role_name', '!=', 'Student')
                ->whereNotNull('admin.role_name');
            
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
                // UPDATED: Use username for organization filter instead of control_tag
                if ($request->has('organization') && $request->organization != 'All' && $request->organization != 'Organization') {
                    $query->where('submitter.username', $request->organization);
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
                
                // Apply search filter (including username search) - UPDATED to use submitter.username
                if ($request->has('search') && !empty($request->search)) {
                    $searchTerm = $request->search;
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('submitted_documents.subject', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('submitted_documents.control_tag', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('submitted_documents.type', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('submitter.username', 'LIKE', "%{$searchTerm}%");
                    });
                }
                
                // Apply the same sorting as in the table view
                if ($request->has('sort_by') && $request->has('sort_dir')) {
                    $column = $request->sort_by;
                    $direction = $request->sort_dir;
                    
                    if ($column === 'organization') {
                        // Sort by username instead of organization acronym
                        $query->orderBy('submitter.username', $direction);
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
            $filename = $isSelectiveExport ? 'selected_documents_' . str_replace(' ', '_', $adminUsername) : 'document_repository_' . str_replace(' ', '_', $adminUsername); 
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
        // Create a dynamic date range string from the dateRangeFilter parameter
        $dateRangeText = '';
        if (!empty($dateRangeFilter)) {
            if (strpos($dateRangeFilter, 'Date Range:') !== false) {
                $dateRangeText = str_replace('Date Range: ', '', $dateRangeFilter);
            } elseif (strpos($dateRangeFilter, 'From Date:') !== false) {
                $dateRangeText = str_replace('From Date: ', 'From ', $dateRangeFilter);
            } elseif (strpos($dateRangeFilter, 'Until Date:') !== false) {
                $dateRangeText = str_replace('Until Date: ', 'Until ', $dateRangeFilter);
            }
        }
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Document Repository Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .university {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .campus {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .location {
            font-size: 12pt;
            margin-bottom: 15px;
        }
        .department {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .date-range {
            font-size: 12pt;
            margin-bottom: 20px;
        }
        .report-info {
            margin-bottom: 20px;
        }
        
        /* TABLE STYLES */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 10pt;
        }
        th {
            background-color: #7A1212;
            color: white;
            padding: 10px;
            border: 1px solid #ddd;
            font-weight: bold;
            text-align: left;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .filters {
            margin-bottom: 15px;
            font-size: 10pt;
        }
        .filters-title {
            font-weight: bold;
        }
        
        /* FIX: SIGNATURE TABLES INSTEAD OF FLOATING DIVS */
        .signature-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 40px;
            margin-bottom: 20px;
        }
        .signature-table td {
            padding: 10px;
            border: none;
            vertical-align: top;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 20px;
        }
        .signature-line {
            width: 80%;
            border-bottom: 1px solid black;
            margin-bottom: 5px;
        }
        .signature-line-right {
            width: 80%;
            border-bottom: 1px solid black;
            margin-bottom: 5px;
            margin-left: auto; /* This pushes the line to the right */
        }
        .signature-name {
            margin-top: 5px;
        }
        .signature-position {
            font-style: italic;
            margin-top: 2px;
        }
        .summary {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            font-size: 9pt;
            font-style: italic;
            text-align: center;
            margin-top: 20px;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header-section">
        <div class="university">Polytechnic University of the Philippines</div>
        <div class="campus">SANTA ROSA CAMPUS</div>
        <div class="location">City of Santa Rosa, Laguna</div>
        <div class="department">' . htmlspecialchars($adminRole) . '</div>
        <div class="report-title">Document Repository Report</div>
        <div class="date-range">' . htmlspecialchars($dateRangeText) . '</div>
    </div>
    
    <div class="report-info">
        <div>Generated by: ' . htmlspecialchars($adminUsername) . ' (' . htmlspecialchars($adminRole) . ')</div>
        <div>Generated on: ' . now()->format('F j, Y g:i A') . '</div>';
        
        if ($isSelectiveExport) {
            $html .= '<div>Export Type: Selected Documents (' . count($documents) . ' selected)</div>';
        }
        
        $html .= '</div>';

        // Add filters section
        if (!empty($filters) || !empty($dateRangeFilter)) {
            $html .= '<div class="filters">
                <div class="filters-title">Applied Filters:</div>';
            
            if (!empty($dateRangeFilter)) {
                $html .= '<div>• ' . htmlspecialchars($dateRangeFilter) . '</div>';
            }
            
            foreach ($filters as $filter) {
                $html .= '<div>• ' . htmlspecialchars($filter) . '</div>';
            }
            
            if ($isSelectiveExport) {
                $html .= '<div style="font-style: italic; margin-top: 5px;">Note: Above filters were active when documents were selected, but export contains only selected documents.</div>';
            }
            
            $html .= '</div>';
        } else {
            if ($isSelectiveExport) {
                $html .= '<div class="filters">No filters were applied when documents were selected - export contains only selected documents.</div>';
            } else {
                $html .= '<div class="filters">No filters applied - showing all approved documents</div>';
            }
        }

        // Table structure
        $html .= '<table>
            <thead>
                <tr>
                    <th>Control Tag</th>
                    <th>Organization</th>
                    <th>Title</th>
                    <th>Date Submitted</th>
                    <th>Type</th>
                    <th>Submitted to</th>
                </tr>
            </thead>
            <tbody>';

        if (count($documents) > 0) {
            foreach ($documents as $document) {
                $actualType = $document->type;
                $html .= '<tr>
                    <td>' . htmlspecialchars($document->control_tag ?? "") . '</td>
                    <td>' . htmlspecialchars($document->username ?? "N/A") . '</td>
                    <td>' . htmlspecialchars($document->subject ?? "") . '</td>
                    <td>' . Carbon::parse($document->created_at)->format("m/d/Y g:i A") . '</td>
                    <td>' . htmlspecialchars($actualType ?? "") . '</td>
                    <td>' . htmlspecialchars($document->role_name ?? "N/A") . '</td>
                </tr>';
            }
        } else {
            $html .= '<tr><td colspan="6" style="text-align: center;">No documents found matching your criteria.</td></tr>';
        }

        $html .= '</tbody>
        </table>';
        
        // Document count summary
        $html .= '<div class="summary">';
        if ($isSelectiveExport) {
            $html .= "Selected Documents: " . count($documents);
        } else {
            $html .= "Total Documents: " . count($documents);
        }
        $html .= '</div>';
        
        // FIX: REPLACING FLOATING DIVS WITH HTML TABLES FOR SIGNATURES
        $html .= '
        <!-- Only row: Notes and Submitted by -->
        <table class="signature-table">
            <tr>
                <td style="width: 50%;">
                    <div class="signature-title">Notes:</div>
                    <div style="height: 50px;"></div>
                </td>
                <td style="width: 50%;" class="text-right">
                    <div class="signature-title">Submitted by:</div>
                    <div class="signature-line-right">&nbsp;</div>
                    <div class="signature-name">Name and Signature</div>
                    <div class="signature-position">' . htmlspecialchars($adminRole) . '</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            Report generated on ' . now()->format("F j, Y g:i A") . '
        </div>
    </body>
    </html>';

        return $html;
    }
}