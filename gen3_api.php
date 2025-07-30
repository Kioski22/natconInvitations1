<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$payload = json_decode(file_get_contents('php://input'), true);
$company_name    = trim($payload['company_name'] ?? '');
$company_address = trim($payload['company_address'] ?? '');
$delegates       = $payload['delegates'] ?? [];

if ($company_name === '' || count($delegates) === 0) {
    echo json_encode(['error' => 'Company name and at least one delegate are required.']);
    exit;
}

// DB column names
$delegate_fields = [
    'firstname',
    'middle',
    'lastname',
    'suffix',
    'dateofbirth',
    'emailid',
    'country',
    'mobilenumber',
    'prc_license_type',
    'prc_license_number',
    'prc_license_expiration_date',
    'region',
    'chapter',
    'sector',
    'register_type',
    'isPWD'
];

// Excel column headers (readable)
$excel_headers = [
    'First Name',
    'Middle',
    'Last Name',
    'Suffix',
    'Date of Birth',
    'Email ID',
    'Country',
    'Mobile Number',
    'PRC License Type',
    'PRC License Number',
    'PRC License Expiration Date',
    'Region',
    'Chapter',
    'Sector',
    'Register Type',
    'PWD'
];

$conn->begin_transaction();

try {
    // Insert company
    $excel_filename = preg_replace('/[^a-z0-9]+/i','', $company_name) . '_natcon_registration.xlsx';
    $stmt = $conn->prepare(
        "INSERT INTO companies (company_name, company_address, excel_filename)
         VALUES (?,?,?)"
    );
    $stmt->bind_param('sss', $company_name, $company_address, $excel_filename);
    $stmt->execute();
    $company_id = $conn->insert_id;
    $stmt->close();

    // Insert delegates
    $cols = implode(',', $delegate_fields);
    $placeholders = implode(',', array_fill(0, count($delegate_fields), '?'));
    $types = 'i' . str_repeat('s', count($delegate_fields));
    $sql = "INSERT INTO delegates (company_id, {$cols}) VALUES (? , {$placeholders})";
    $stmt = $conn->prepare($sql);

    foreach ($delegates as $d) {
        $params = [$company_id];
        foreach ($delegate_fields as $col) {
            if ($col === 'isPWD') {
                $params[] = in_array(strtolower($d[$col] ?? ''), ['1','y','yes','true']) ? 1 : 0;
            } else {
                $params[] = $d[$col] ?? null;
            }
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
    }
    $stmt->close();

    // Spreadsheet generation
    $ss = new Spreadsheet();
    $sh = $ss->getActiveSheet();

    // Header row
    $sh->fromArray($excel_headers, null, 'A1')
       ->getStyle('A1:' . $sh->getHighestDataColumn() . '1')
       ->getFont()->setBold(true);

    // Data rows
    $rows = [];
    foreach ($delegates as $d) {
        $row = [];
        foreach ($delegate_fields as $i => $col) {
            $value = $d[$col] ?? '';
            if (in_array($col, ['dateofbirth', 'prc_license_expiration_date']) && $value) {
                $row[] = ExcelDate::stringToExcel($value);
            } else {
                $row[] = $value;
            }
        }
        $rows[] = $row;
    }
    $sh->fromArray($rows, null, 'A2');

    // Auto-size
    foreach (range('A', $sh->getHighestDataColumn()) as $c) {
        $sh->getColumnDimension($c)->setAutoSize(true);
    }

    // Apply MM/DD/YYYY formatting to date columns
    $highestRow = $sh->getHighestRow();
    foreach ($delegate_fields as $i => $col) {
        if (in_array($col, ['dateofbirth', 'prc_license_expiration_date'])) {
            $colIndex = $i + 1;
            $letter = Coordinate::stringFromColumnIndex($colIndex);
            $sh->getStyle("{$letter}2:{$letter}{$highestRow}")
               ->getNumberFormat()->setFormatCode('mm/dd/yyyy');
        }
    }

    // Save Excel file
    $outDir = __DIR__ . '/exports';
    if (!is_dir($outDir)) mkdir($outDir, 0755, true);
    $fullPath = "{$outDir}/{$excel_filename}";
    (new Xlsx($ss))->save($fullPath);

    $conn->commit();
    echo json_encode(['file' => "exports/{$excel_filename}"]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
