<?php
require 'db.php';

// Get count of sent invitations
$sql_sent = "SELECT COUNT(*) as total_sent FROM invitations WHERE status='sent'";
$result_sent = $conn->query($sql_sent);
$row_sent = $result_sent->fetch_assoc();
$total_sent = $row_sent['total_sent'];

// ✅ Define SQL for fetching companies
$sql_companies = "
    SELECT 
        c.company_id, 
        c.company_name, 
        c.company_address,  -- ✅ this line gets the real address
        c.excel_filename
    FROM companies c
    INNER JOIN delegates d ON c.company_id = d.company_id
    GROUP BY c.company_id, c.company_name, c.company_address, c.excel_filename
    ORDER BY c.company_name
";

// ✅ Run the query
$companies = [];
$result_companies = $conn->query($sql_companies);
if ($result_companies && $result_companies->num_rows > 0) {
    while ($row = $result_companies->fetch_assoc()) {
        $companies[] = $row;
    }
}

// Get SOA Released count (modify this based on actual table structure)
$sql_soa = "SELECT COUNT(*) as total_soa FROM soa_sequence";
$result_soa = $conn->query($sql_soa);
$row_soa = $result_soa->fetch_assoc();
$total_soa = $row_soa['total_soa'];
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
    <a href="javascript:void(0);" onclick="showTab('dashboard')" class="active-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="javascript:void(0);" onclick="showTab('individual')"><i class="bi bi-person-lines-fill"></i> Individual</a>
    <a href="javascript:void(0);" onclick="showTab('company')"><i class="bi bi-building"></i> Company</a>
    <a href="javascript:void(0);" onclick="showTab('soaGenerator')"><i class="bi bi-file-earmark-pdf"></i> SOA Generator</a>
    <a href="javascript:void(0);" onclick="showTab('soaReleased')"><i class="bi bi-check-circle"></i> SOA Released</a>
    <a href="logout.php" class="text-white"><i class="bi bi-box-arrow-right"></i> Logout</a>
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
                <select name="company_id" id="companySelect" class="form-select" required>
                    <option value="" disabled selected>Select company</option>
                    <?php foreach ($companies as $comp): ?>
                       <option 
                            value="<?= htmlspecialchars($comp['company_id']) ?>" 
                            data-address="<?= htmlspecialchars($comp['company_address']) ?>">
                            <?= htmlspecialchars($comp['company_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Address</label>
                <input type="text" name="company_address" id="companyAddress" class="form-control" readonly required>
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

    <!-- SOA Released Tab -->
    <div id="soaReleasedTab" style="display:none;">
        <h4>SOA Released</h4>
        <div class="card text-white bg-primary mb-3">
            <div class="card-header"><i class="bi bi-check-circle"></i> SOA Released</div>
            <div class="card-body text-center">
                <h1><?= $total_soa ?></h1>
                <p>Total SOAs released</p>
            </div>
        </div>

        <!-- Sample table of released SOAs -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>SOA Number</th>
                    <th>Date Released</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT soa_number, created_at FROM soa_sequence ORDER BY id DESC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0):
                while($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['soa_number']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="2" class="text-center">No SOA released records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function showTab(tab) {
    const tabs = ['dashboardTab', 'individualTab', 'companyTab', 'soaGeneratorTab', 'soaReleasedTab'];
    tabs.forEach(id => document.getElementById(id).style.display = (id === tab + 'Tab') ? 'block' : 'none');

    const links = document.querySelectorAll('.sidebar a');
    links.forEach(link => link.classList.remove('active-link'));
    links.forEach(link => {
        if (link.getAttribute('onclick').includes(tab)) {
            link.classList.add('active-link');
        }
    });
}

$('#companySelect').on('change', function(){
    var address = $(this).find(':selected').data('address');
    $('#companyAddress').val(address);

    var company_id = $(this).val(); // use the selected value directly

    $.ajax({
        url: 'get_participants.php',
        type: 'POST',
        data: { company_id: company_id }, // ✅ fixed variable
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
                            '<option value="0006">0006</option>' +
                            '<option value="0007">0007</option>' +
                            '<option value="0008">0008</option>' +
                            '<option value="0009">0009</option>' +
                            '<option value="0010">0010</option>' +
                            '<option value="0011">0011</option>' +
                            '<option value="0012">0012</option>' +
                            '<option value="0013">0013</option>' +
                            '<option value="0014">0014</option>' +
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

function updateMembershipType(select){
    const mapping = {
        '0001': {type: 'Regular Member', price: 3700},
        '0002': {type: 'Reg. Mem. - Senior', price: 3000},
        '0003': {type: 'Reg. Mem. - PWD', price: 3000},
        '0004': {type: 'Life Member', price: 3000},
        '0005': {type: 'Non-Member', price: 4700},
        '0006': {type: 'Early Bird Reg. Member', price: 3200},
        '0007': {type: 'Associate Member', price: 3000},
        '0008': {type: 'EB Reg. Member with 3-D Meal Package', price: 4700},
        '0009': {type: 'Non-Member with 3-D Meal Package', price: 6200},
        '0010': {type: 'New Board Passer', price: 3000},
        '0011': {type: 'Reg. Mem. - Senior with 3-D Meal Package', price: 4500},
        '0012': {type: '3-D Meal Package', price: 1500},
        '0013': {type: 'Life Member with 3-D Meal Package', price: 4500},
        '0014': {type: 'Regular Member with 3-D Meal Package', price: 5200}
    };

    const data = mapping[select.value] || {type: '', price: ''};
    const row = $(select).closest('tr');
    row.find('input[name="membership_type[]"]').val(data.type);
    row.find('input[name="registration_fee[]"]').val(data.price);
    row.find('input[name="amount[]"]').val(data.price);
}

</script>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>