<?php
require 'db.php';
// Get the next SOA number from the database
$next_soa = '';
$sql = "SELECT soa_number FROM soa_sequence ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $last = $row['soa_number'];
    if (is_numeric($last)) {
        $next_soa = str_pad($last + 1, 6, '0', STR_PAD_LEFT);
    } else {
        // If format is like SOA-000123
        if (preg_match('/(\d+)$/', $last, $m)) {
            $next_soa = 'SOA-' . str_pad($m[1] + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $next_soa = 'SOA-000001';
        }
    }
} else {
    $next_soa = 'SOA-000001';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manual SOA Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; font-family: 'Inter', sans-serif; }
        .section-header h4 { font-weight: 600; color: #004085; }
        .card { border-radius: 14px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
        .card-header { background: #004085; color: #fff; font-weight: 500; }
        .form-label { font-weight: 500; }
        .empty-state { text-align: center; color: #adb5bd; }
        .empty-state i { font-size: 2.5rem; }
        .summary-card { background: #fff; border-radius: 12px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="section-header mb-4">
                <h4><i class="bi bi-file-earmark-pdf me-2"></i>Manual SOA Generator</h4>
                <p class="text-muted mb-0">Manually create a Statement of Account with custom participants</p>
            </div>
            <form id="manualSoaForm" action="generate_soa_manual.php" method="POST" target="_blank">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="bi bi-building me-2"></i>Company Information
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">SOA Number</label>
                                        <input type="text" class="form-control" name="soa_number" value="<?= htmlspecialchars($next_soa) ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Date</label>
                                        <input type="text" class="form-control" value="<?= date('Y-m-d') ?>" readonly>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Company Name</label>
                                        <input type="text" class="form-control" name="company_name" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Company Address</label>
                                        <input type="text" class="form-control" name="company_address" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">TIN</label>
                                        <input type="text" class="form-control" name="tin">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Business Style</label>
                                        <input type="text" class="form-control" name="business_style">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Particulars</label>
                                        <textarea class="form-control" name="particulars" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="bi bi-people me-2"></i>Participants
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="participantsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 60px;">#</th>
                                                <th>Name</th>
                                                <th style="width: 200px;">Membership Type</th>
                                                <th style="width: 130px;">Reg. Fee</th>
                                                <th style="width: 130px;">Amount</th>
                                                <th style="width: 60px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="empty-row">
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="empty-state">
                                                        <i class="bi bi-person-plus"></i>
                                                        <div>Add participants below</div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addParticipantBtn">
                                    <i class="bi bi-plus-circle me-1"></i>Add Participant
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="summary-card p-4 mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-calculator me-2 fs-4 text-primary"></i>
                                <h6 class="mb-0">Summary</h6>
                            </div>
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="mb-1" id="participantCount">0</h4>
                                    <small class="text-muted">Participants</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="mb-1" id="totalAmount">₱0.00</h4>
                                    <small class="text-muted">Total Amount</small>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-file-earmark-pdf me-2"></i>Generate SOA PDF
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="clearBtn">
                                <i class="bi bi-arrow-clockwise me-2"></i>Clear Form
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function updateSummary() {
    let count = 0, total = 0;
    $('#participantsTable tbody tr').each(function() {
        if (!$(this).hasClass('empty-row')) {
            count++;
            const amt = parseFloat($(this).find('input[name="amount[]"]').val()) || 0;
            total += amt;
        }
    });
    $('#participantCount').text(count);
    $('#totalAmount').text('₱' + total.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}));
}


function addParticipantRow() {
    if ($('.empty-row').length) $('#participantsTable tbody').empty();
    const idx = $('#participantsTable tbody tr').length; // count only actual participant rows
    $('#participantsTable tbody').append(`
        <tr>
            <td class="text-center fw-bold">${idx + 1}</td>
            <td><input type="text" name="participant_name[]" class="form-control form-control-sm" required></td>
            <td><input type="text" name="membership_type[]" class="form-control form-control-sm" required></td>
            <td><input type="number" name="registration_fee[]" class="form-control form-control-sm amount-input" min="0" step="0.01" required></td>
            <td><input type="number" name="amount[]" class="form-control form-control-sm amount-input" min="0" step="0.01" required></td>
            <td><button type="button" class="btn btn-link text-danger p-0 removeParticipantBtn"><i class="bi bi-trash"></i></button></td>
        </tr>
    `);
    updateParticipantNumbers();
    updateSummary();
}

function updateParticipantNumbers() {
    $('#participantsTable tbody tr').each(function(i) {
        $(this).find('td:first').text(i + 1);
    });
}

$('#participantsTable').on('click', '.removeParticipantBtn', function() {
    $(this).closest('tr').remove();
    if ($('#participantsTable tbody tr').length === 0) {
        $('#participantsTable tbody').html(`
            <tr class="empty-row">
                <td colspan="6" class="text-center py-4">
                    <div class="empty-state">
                        <i class="bi bi-person-plus"></i>
                        <div>Add participants below</div>
                    </div>
                </td>
            </tr>
        `);
    }
    updateParticipantNumbers();
    updateSummary();
});

$('#addParticipantBtn').on('click', addParticipantRow);

$('#participantsTable').on('input', '.amount-input', updateSummary);
$('#participantsTable').on('click', '.removeParticipantBtn', function() {
    $(this).closest('tr').remove();
    if ($('#participantsTable tbody tr').length === 0) {
        $('#participantsTable tbody').html(`
            <tr class="empty-row">
                <td colspan="6" class="text-center py-4">
                    <div class="empty-state">
                        <i class="bi bi-person-plus"></i>
                        <div>Add participants below</div>
                    </div>
                </td>
            </tr>
        `);
    }
    updateSummary();
});

$('#clearBtn').on('click', function() {
    if (confirm('Clear all form data?')) {
        $('#manualSoaForm')[0].reset();
        $('#participantsTable tbody').html(`
            <tr class="empty-row">
                <td colspan="6" class="text-center py-4">
                    <div class="empty-state">
                        <i class="bi bi-person-plus"></i>
                        <div>Add participants below</div>
                    </div>
                </td>
            </tr>
        `);
        updateSummary();
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
