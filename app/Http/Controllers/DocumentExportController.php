<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DocumentExportController extends Controller
{
    public function export(Request $request)
    {
        // Get filtered documents based on request parameters
        $documents = Document::when($request->organization && $request->organization != 'All', function ($query) use ($request) {
                $query->where('control_tag', 'LIKE', $request->organization . '%');
            })
            ->when($request->type && $request->type != 'All', function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->status && $request->status != 'All', function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('control_tag', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('subject', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('type', 'LIKE', '%' . $request->search . '%');
                });
            })
            ->get();
        
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
            ->setDescription('Document History Report generated on ' . date('Y-m-d'));
        
        // Add headers
        $sheet->setCellValue('A1', 'Document History Report');
        $sheet->mergeCells('A1:F1');
        
        $sheet->setCellValue('A2', 'Generated on: ' . date('Y-m-d'));
        $sheet->mergeCells('A2:F2');
        
        $sheet->setCellValue('A4', 'Control Tag');
        $sheet->setCellValue('B4', 'Organization');
        $sheet->setCellValue('C4', 'Title');
        $sheet->setCellValue('D4', 'Date Submitted');
        $sheet->setCellValue('E4', 'Type');
        $sheet->setCellValue('F4', 'Status');
        
        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '7A1212'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        $sheet->getStyle('A2:F2')->applyFromArray([
            'font' => ['italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        $sheet->getStyle('A4:F4')->applyFromArray($headerStyle);
        
        // Fill data
        $row = 5;
        foreach ($documents as $document) {
            // Extract organization acronym
            $parts = explode('_', $document->control_tag);
            $acronym = count($parts) > 0 ? $parts[0] : '';
            $orgName = isset($orgMap[$acronym]) ? $orgMap[$acronym] : $acronym;
            
            $sheet->setCellValue('A' . $row, $document->control_tag);
            $sheet->setCellValue('B' . $row, $orgName);
            $sheet->setCellValue('C' . $row, $document->subject);
            $sheet->setCellValue('D' . $row, \Carbon\Carbon::parse($document->created_at)->format('m/d/Y'));
            $sheet->setCellValue('E' . $row, $document->type);
            $sheet->setCellValue('F' . $row, $document->status);
            
            // Add status color
            $statusColor = match($document->status) {
                'Approved' => '10B981',
                'Rejected' => 'EF4444',
                default => 'F59E0B'
            };
            
            $sheet->getStyle('F' . $row)->applyFromArray([
                'font' => ['color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $statusColor],
                ],
            ]);
            
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set borders for all data
        $sheet->getStyle('A4:F' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
        
        // Create and save spreadsheet
        $writer = new Xlsx($spreadsheet);
        $filename = 'document_history_' . date('Y-m-d_His') . '.xlsx';
        $path = storage_path('app/public/' . $filename);
        
        // Make sure the directory exists
        if (!file_exists(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0777, true);
        }
        
        $writer->save($path);
        
        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }
}