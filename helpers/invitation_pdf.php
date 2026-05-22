<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';

function findInvitationImage(array $candidates): ?string {
    foreach ($candidates as $candidate) {
        $real = realpath($candidate);
        if ($real && file_exists($real)) {
            return $real;
        }
    }
    return null;
}

function buildIndividualPdf(array $data): string {
    $pdf = new TCPDF('P', 'mm', [215.9, 330.2], true, 'UTF-8', false);
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(false, 0);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->AddPage();
    $img1 = findInvitationImage([
        'invitation/74th NatCon Invitation for Member w_meals_page-0001.png',
        'invitation/1.png'
    ]);
    if (!$img1) {
        throw new Exception('Page 1 background image not found.');
    }
    $pdf->Image($img1, -1, -1, 218, 333, '', '', '', true, 300, '', false, false, 0, true);

    // $pdf->SetFont('helvetica', '', 12);
    // $pdf->SetTextColor(0, 0, 0);
    // $pdf->SetXY(55, 90.5);
    // $pdf->Write(0, ($data['salutation'] ?? '') . ' ' . ($data['full_name'] ?? ''));
    // $pdf->SetXY(55, 95.5);
    // $pdf->Write(0, $data['designation'] ?? '');
    // $pdf->SetXY(55, 100);
    // $pdf->Write(0, $data['company'] ?? '');
    // $pdf->SetXY(55, 105);
    // $pdf->MultiCell(100, 0, $data['address'] ?? '', 0, 'L', false, 1);

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetXY(63.8, 81.8);
    $pdf->Write(0, ' ' . ($data['salutation'] ?? '') . ' ' . ($data['full_name'] ?? '') . ',');

    $pdf->AddPage();
    $img2 = findInvitationImage([
        'invitation/74th NatCon Invitation for Member w_meals_page-0002.png',
        'invitation/2.png'
    ]);
    if (!$img2) {
        throw new Exception('Page 2 background image not found.');
    }
    $pdf->Image($img2, -1, -1, 218, 333, '', '', '', true, 300, '', false, false, 0, true);

     $pdf->AddPage();
    $img3 = findInvitationImage([
        'invitation/74th NatCon Invitation for Member w_meals_page-0003.png',
        'invitation/3.png'
    ]);
    if (!$img3) {
        throw new Exception('Page 3 background image not found.');
    }
    $pdf->Image($img3, -1, -1, 218, 333, '', '', '', true, 300, '', false, false, 0, true);

    return $pdf->Output('', 'S');
}



function buildCompanyPdf(array $data): string {
    $pdf = new TCPDF('P', 'mm', [216, 330], true, 'UTF-8', false);
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(false, 0);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->AddPage();
    $img1 = findInvitationImage([
        __DIR__ . '/../invitation/73rd-NatCon-Invitation-for-FOR-LGU-final_page-0001.jpg'
    ]);
    if (!$img1) {
        throw new Exception('Company page 1 background image not found.');
    }
    $pdf->Image($img1, 0, 0, 216, 330);

    $pdf->SetFont('times', '', 12);
    $pdf->SetXY(55, 75);
    $pdf->Cell(0, 10, $data['full_name'] ?? '', 0, 1, 'L');
    $pdf->SetXY(55, 79);
    $pdf->Cell(0, 10, $data['designation'] ?? '', 0, 1, 'L');
    $pdf->SetXY(55, 83);
    $pdf->Cell(0, 10, $data['company'] ?? '', 0, 1, 'L');
    $pdf->SetXY(55, 89);
    $pdf->MultiCell(150, 10, $data['address'] ?? '', 0, 'L');

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetXY(65, 118.5);
    $pdf->Cell(0, 10, ($data['full_name'] ?? '') . ',', 0, 1, 'L');

    $pdf->AddPage();
    $img2 = findInvitationImage([
        __DIR__ . '/../invitation/73rd-NatCon-Invitation-for-FOR-LGU-final_page-0002.jpg'
    ]);
    if (!$img2) {
        throw new Exception('Company page 2 background image not found.');
    }
    $pdf->Image($img2, 0, 0, 216, 330);

    $pdf->AddPage();
    $img3 = findInvitationImage([
        __DIR__ . '/../invitation/73rd-NatCon-Invitation-for-FOR-LGU-final_page-0003.jpg'
    ]);
    if (!$img3) {
        throw new Exception('Company page 3 background image not found.');
    }
    $pdf->Image($img3, 0, 0, 216, 330);

    return $pdf->Output('', 'S');
}
