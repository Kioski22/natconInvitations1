<?php
require 'db.php';

// Get count of sent invitations
$sql_sent = "SELECT COUNT(*) as total_sent FROM invitations WHERE status='sent'";
$result_sent = $conn->query($sql_sent);
$row_sent = $result_sent->fetch_assoc();
$total_sent = $row_sent['total_sent'];

// Fetch companies from database
$sql_companies = "SELECT DISTINCT company, address FROM invitations WHERE company != '' ORDER BY company";
$result_companies = $conn->query($sql_companies);
$companies = [];
if ($result_companies->num_rows > 0) {
    while ($row = $result_companies->fetch_assoc()) {
        $companies[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PSME Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { overflow-x: hidden; background: #f8f9fa; }
        .sidebar {
            height: 100vh; background: linear-gradient(45deg, #004085, #007bff); color: white;
            position: fixed; width: 220px; box-shadow: 2px 0 5px rgba(0,0,0,0.2);
        }
        .sidebar a {
            color: white; display: block; padding: 12px 20px; text-decoration: none;
        }
        .sidebar a:hover, .sidebar a.active-link { background: rgba(255,255,255,0.3); font-weight: bold; }
        .content { margin-left: 220px; padding: 30px; }
        .dashboard-header { background: linear-gradient(45deg, #004085, #007bff); color: white; padding: 20px; border-radius: 10px; }
        .table th { background: #004085; color: white; }
    </style>
</head>
<body>

<div class="sidebar">
    <h4 class="text-center">Admin Panel</h4>
    <hr style="background: white;">
    <a href="javascript:void(0);" onclick="showTab('dashboardTab')" class="active-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="javascript:void(0);" onclick="showTab('individualTab')"><i class="bi bi-person-lines-fill"></i> Individual</a>
    <a href="javascript:void(0);" onclick="showTab('companyTab')"><i class="bi bi-building"></i> Company</a>
    <a href="javascript:void(0);" onclick="showTab('soaGeneratorTab')"><i class="bi bi-file-earmark-pdf"></i> SOA Generator</a>
</div>

<div class="content">

    <div class="dashboard-header text-center">
        <h2>PSME Invitation Admin Dashboard</h2>
    </div>

    <!-- Dashboard Tab -->
    <div id="dashboardTab">
        <h4>Overall Status</h4>
        <div class="card text-white bg-success mb-3">
            <div class="card-header"><i class="bi bi-envelope-fill"></i> Emails Sent</div>
            <div class="card-body text-center">
                <h1><?= $total_sent ?></h1>
                <p>Total invitations sent</p>
            </div>
        </div>
    </div>

    <!-- Individual Tab -->
    <div id="individualTab" style="display:none;">
        <h4>Individual Invitations</h4>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Event</th><th>Company</th><th>Email</th><th>Name</th><th>Designation</th><th>Address</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT * FROM invitations ORDER BY event, company";
            $result = $conn->query($sql);
            if ($result->num_rows > 0):
                while($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['event']) ?></td>
                    <td><?= htmlspecialchars($row['company']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['designation']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="7" class="text-center">No records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Company Tab -->
    <div id="companyTab" style="display:none;">
        <h4>Company Summary</h4>
        <p>This section is under development.</p>
    </div>

    <!-- SOA Generator Tab -->
<div id="soaGeneratorTab" style="display:none;">
    <h4>Manual SOA Generator</h4>
    <form action="generate_soa_manual.php" method="POST" target="_blank">
        <div class="mb-3">
            <label>Select Company</label>
            <select name="company" id="companySelect" class="form-select" required>
                <option value="" disabled selected>Select company</option>
                <?php foreach ($companies as $comp): ?>
                    <option value="<?= htmlspecialchars($comp['company']) ?>" data-address="<?= htmlspecialchars($comp['address']) ?>">
                        <?= htmlspecialchars($comp['company']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Address</label>
            <input type="text" name="address" id="companyAddress" class="form-control" readonly required>
        </div>

        <div class="mb-3">
            <label>TIN</label>
            <input type="text" name="tin" class="form-control" placeholder="Enter TIN">
        </div>

        <div class="mb-3">
            <label>Business Style</label>
            <input type="text" name="business_style" class="form-control" placeholder="Enter business style">
        </div>

        <div class="mb-3">
            <label>Particulars</label>
            <textarea name="particulars" class="form-control" rows="2" placeholder="Enter particulars"></textarea>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="participantsTable">
                <thead class="table-primary">
                    <tr>
                        <th>No.</th>
                        <th>Item No.</th>
                        <th>Participant Name</th>
                        <th>Type of Membership</th>
                        <th>Registration Fee</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <button type="submit" class="btn btn-primary w-100">Generate SOA PDF</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function showTab(tabId) {
    const tabs = ['dashboardTab', 'individualTab', 'companyTab', 'soaGeneratorTab'];
    tabs.forEach(id => document.getElementById(id).style.display = (id === tabId) ? 'block' : 'none');

    document.querySelectorAll('.sidebar a').forEach(link => {
        link.classList.toggle('active-link', link.getAttribute('onclick').includes(tabId.replace('Tab','')));
    });
}

$(document).ready(function(){
    $('#companySelect').on('change', function(){
        var address = $(this).find(':selected').data('address');
        $('#companyAddress').val(address);

        var company = $(this).val();
        $.ajax({
            url: 'get_participants.php',
            type: 'POST',
            data: {company: company},
            dataType: 'json',
            success: function(data){
                var tbody = $('#participantsTable tbody');
                tbody.empty();
                if(data.length > 0){
                    $.each(data, function(i, name){
                        tbody.append(
                            '<tr>' +
                            '<td>'+(i+1)+'</td>' +
                            '<td><select name="item_number[]" class="form-select" required onchange="updateMembershipType(this)">' +
                                '<option value="">Select</option>' +
                                '<option value="0001">0001</option>' +
                                '<option value="0002">0002</option>' +
                                '<option value="0003">0003</option>' +
                                '<option value="0004">0004</option>' +
                                '<option value="0005">0005</option>' +
                            '</select></td>' +
                            '<td><input type="hidden" name="participants[]" value="'+name+'">'+name+'</td>' +
                            '<td><input type="text" name="membership_type[]" class="form-control" readonly></td>' +
                            '<td><input type="number" name="registration_fee[]" step="0.01" class="form-control" required></td>' +
                            '<td><input type="number" name="amount[]" step="0.01" class="form-control" required></td>' +
                            '</tr>'
                        );
                    });
                } else {
                    tbody.append('<tr><td colspan="6" class="text-center">No participants found.</td></tr>');
                }
            }
        });
    });
});

function updateMembershipType(select){
    const mapping = {
        '0001':'Regular Member',
        '0002':'Reg. Mem. - Senior',
        '0003':'Reg. Mem. - PWD',
        '0004':'Life Member',
        '0005':'Non-Member'
    };
    const membership = mapping[select.value] || '';
    $(select).closest('tr').find('input[name="membership_type[]"]').val(membership);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>