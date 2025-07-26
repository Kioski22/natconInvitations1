<?php
ini_set('display_errors', 1); // For debugging ONLY, remove in production
error_reporting(E_ALL);     // For debugging ONLY, remove in production

// index.php
require __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (isset($data['action']) && $data['action'] === 'finalize_and_generate') {
        header('Content-Type: application/json');

        // --- Include the database connection ---
        // The $conn variable is now available from this file.
        require_once __DIR__ . '/db.php';

        $company_name = trim($data['company_name'] ?? '');
        $company_address = trim($data['company_address'] ?? '');
        $delegates = $data['delegates'] ?? [];

        if (empty($company_name) || empty($delegates)) {
            echo json_encode(['error' => 'Company name and at least one delegate are required.']);
            exit;
        }

        // Use a transaction to ensure all or nothing is saved.
        $conn->begin_transaction();

        try {
            // 1. Insert the company
            $excel_filename = preg_replace('/[^a-z0-9]+/i', '', $company_name) . '_natcon_registration.xlsx';
            $stmt = $conn->prepare("INSERT INTO companies (company_name, company_address, excel_filename) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $company_name, $company_address, $excel_filename);
            $stmt->execute();
            $cid = $conn->insert_id; // Get last inserted ID
            $stmt->close();

            // 2. Insert all delegates
            $delegate_fields = [
                'delegates_fname', 'delegates_mname', 'delegates_lname', 'delegates_suffix',
                'delegates_dob', 'delegates_emailid', 'delegates_country', 'delegates_contactno',
                'prcLicenseType', 'prcLicenseNo', 'prcLicenseExpiration',
                'region', 'chapter', 'sector', 'register_type', 'isPWD'
            ];
            $placeholders = implode(',', array_fill(0, count($delegate_fields), '?'));
            $types = 'i' . str_repeat('s', count($delegate_fields)); // i for company_id, s for all other fields

            $sql = "INSERT INTO delegates (company_id, " . implode(',', $delegate_fields) . ") VALUES (?, {$placeholders})";
            $stmt = $conn->prepare($sql);

            foreach ($delegates as $delegate) {
                $params = [$cid];
                foreach ($delegate_fields as $f) {
                    $params[] = !empty($delegate[$f]) ? $delegate[$f] : null;
                }
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
            }
            $stmt->close();

            // 3. Generate the Spreadsheet
            $ss = new Spreadsheet();
            $sh = $ss->getActiveSheet();
            
            $sh->setCellValue('A1', 'Company Name:');
            $sh->setCellValue('B1', $company_name);
            $sh->setCellValue('A2', 'Company Address:');
            $sh->setCellValue('B2', $company_address);
            $sh->mergeCells('B2:E2');
            
            $headers = array_keys($delegates[0]);
            $headers = array_diff($headers, ['temp_id']);
            $sh->fromArray($headers, null, 'A4');
            
            $dataRows = [];
            foreach($delegates as $delegate) {
                unset($delegate['temp_id']);
                $dataRows[] = array_values($delegate);
            }
            $sh->fromArray($dataRows, null, 'A5');
            
            foreach (range('A', $sh->getHighestDataColumn()) as $col) {
                $sh->getColumnDimension($col)->setAutoSize(true);
            }
            
            $headerStyle = ['font' => ['bold' => true]];
            $sh->getStyle('A4:' . $sh->getHighestDataColumn() . '4')->applyFromArray($headerStyle);
            $sh->getStyle('A1:A2')->applyFromArray($headerStyle);

            // 4. Save the file
            $dir = __DIR__ . '/exports';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $filepath = "{$dir}/{$excel_filename}";
            (new Xlsx($ss))->save($filepath);

            $conn->commit();

            echo json_encode(['file' => "exports/{$excel_filename}"]);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['error' => 'An error occurred while saving data: ' . $e->getMessage()]);
        }
        
        $conn->close();
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PSME Natcon Bulk Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .btn-icon { background:none;border:none;font-size:1.1em;cursor:pointer; }
    #delegate-area { display: none; } /* Hide delegate form/table initially */
    .company-info-display { font-size: 1.1em; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">PSME Natcon</a>
    </div>
  </nav>

  <div class="container">
    <div class="card shadow-sm mb-5">
      <div class="card-body">
        <h1 class="mb-3">Bulk Registration Document Generator</h1>
        
        <div id="company-section">
            <p class="mb-2">Enter the company name and address to begin.</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="company-name" class="form-label">Company Name</label>
                    <input type="text" class="form-control form-control-lg" id="company-name" required placeholder="e.g., Acme Corporation"/>
                </div>
                <div class="col-md-6">
                    <label for="company-address" class="form-label">Company Address</label>
                    <input type="text" class="form-control form-control-lg" id="company-address" required placeholder="e.g., 123 Innovation Drive, Tech City"/>
                </div>
            </div>
            <div class="text-end mt-3">
                <button id="btnInit" class="btn btn-primary btn-lg">Start Registration</button>
            </div>
        </div>

        <div id="delegate-area">
            <div class="p-3 mb-4 bg-light rounded-3">
                <h4 id="company-name-display" class="m-0"></h4>
                <p id="company-address-display" class="m-0 text-muted"></p>
            </div>
            
            <table class="table table-bordered table-striped" id="tblDelegates">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Chapter</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <form id="frmDelegate" class="mt-4">
              <input type="hidden" name="temp_id" id="temp_id">
              
              <div class="card bg-light border-0 mb-4">
                <div class="card-body text-center">
                  <h5 id="form-title" class="card-title m-0">Add Delegate Information</h5>
                </div>
              </div>
              <div class="row g-3">
                 <div class="col-md-4"><label class="form-label">First Name</label><input type="text" class="form-control" name="delegates_fname" required /></div>
                 <div class="col-md-4"><label class="form-label">Middle Name</label><input type="text" class="form-control" name="delegates_mname" /></div>
                 <div class="col-md-4"><label class="form-label">Last Name</label><input type="text" class="form-control" name="delegates_lname" required /></div>
                 <div class="col-md-2"><label class="form-label">Suffix</label><input type="text" class="form-control" name="delegates_suffix" /></div>
                 <div class="col-md-4"><label class="form-label">Date of Birth</label><input type="date" class="form-control" name="delegates_dob" required /></div>
                 <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="delegates_emailid" required /></div>
                 <div class="col-md-4"><label class="form-label">Country</label><select class="form-select" name="delegates_country" id="delegates-country" required><option value="">Select</option></select></div>
                 <div class="col-md-4"><label class="form-label">Contact No</label><input type="text" class="form-control" name="delegates_contactno" required /></div>
                 <div class="col-md-4"><label class="form-label">Region</label><select class="form-select" name="region" id="region" required><option value="">Select</option><option>NCR</option><option>Luzon</option><option>Visayas</option><option>Mindanao</option><option>International</option></select></div>
                 <div class="col-md-4"><label class="form-label">Chapter</label><select class="form-select" name="chapter" id="chapter" required><option value="">Select Region First</option></select></div>
                  <div class="col-md-4">
                    <label for="prcLicenseType" class="form-label">PRC License Type</label>
                    <select class="form-select" id="prcLicenseType" name="prcLicenseType" required>
                      <option value="">Select License Type</option>
                      <option value="Professional Mechanical Engineer">Professional Mechanical Engineer</option>
                      <option value="Registered Mechanical Engineer">Registered Mechanical Engineer</option>
                      <option value="Certified Plant Mechanic">Certified Plant Mechanic</option>
                      <option value="ME Graduate">ME Graduate</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                 <div class="col-md-4"><label class="form-label">PRC License No</label><input type="text" class="form-control" name="prcLicenseNo" required /></div>
                 <div class="col-md-4"><label class="form-label">PRC License Expiration</label><input type="date" class="form-control" name="prcLicenseExpiration" required /></div>
                 <div class="col-md-4"><label class="form-label">Sector</label><select class="form-select" name="sector" required><option value="">Select</option><option>Government</option><option>Private</option></select></div>
                 <div class="col-md-4">
                <label for="register_type" class="form-label">Register Type</label>
                <select class="form-select" id="register_type" name="register_type" required>
                  <option value="">Select Register Type</option>
                  <option value="Regular">Regular</option>
                  <option value="Life/Associate">Life/Associate</option>
                  <option value="Guest/Non-member">Guest/Non-member</option>
                </select>
              </div>
                 <div class="col-md-4"><label class="form-label">PWD?</label><select class="form-select" name="isPWD" required><option value="">Select</option><option>Yes</option><option>No</option></select></div>
              </div>
              <div class="mt-4 text-end">
                <button type="button" id="btnCancel" class="btn btn-secondary btn-lg" style="display:none;">Cancel Edit</button>
                <button type="button" id="btnSubmit" class="btn btn-primary btn-lg">Add Delegate</button>
              </div>
            </form>
            <hr class="my-5">
            <div class="mt-4 text-end" id="generate-section">
                 <button type="button" id="btnGenerate" class="btn btn-success btn-lg">Generate Final Document</button>
            </div>
        </div>
      </div>
    </div>
  </div>

<script>
// --- Data for dropdowns (countries, chapters) ---
 const chapters = {
      "NCR": ["Embo","Intramuros","Las Piñas-Muntinlupa","Makati","Makati (Host)","Mandaluyong","Manila","Marikina City","Manila West","Metro Manila BFP","Medical Services","Metro North","NCR Academe","NCR Metrosouth","Ortigas-Pasig","Parañaque","Pasay","Pasig","Quezon City Agham","Quezon City","Taguig","Manila (Host)","South Harbor (PCG)","Test","Balara","Logomeap","Makati CBD","Metro Marikina","NCR Port Area","Quezon City Central","Quezon City United"],
      "Luzon": ["Abra","Albay-Legazpi","Bacon-Manito","Baguio-Cordillera","Bataan","Batangas","Bulacan","Cagayan Valley","Camarines Norte (Daet)","Camsur-Naga","Catanduanes","Cavite","Cavite-Carsigma","Cavite-Imbarkaw","Central Laguna","Clark","Isabela-Quirino","Ilocos","La Union","Makban","Masbate","Mindoro","Nueva Ecija","Nueva Viscaya","Palawan","Pampanga (Host)","Pangasinan","Pililla-Jalajala","QBL Host","Quezon Province","Rinconada","Rizal","Rizal - Antipolo","Romblon","Sorsogon (Host)","Tarlac","Western Batangas","Aurora","Benguet-Southwest","Bulacan East","Bulacan North","Mankayan-Mt. Province","Pampanga","Rio Tuba","Subic","Baguio City","Cordillera","Batangas East"],
      "Visayas": ["Aklan","Capiz","Cebu","Cebu Central","Cebu East","Cebu Hotel And Building Engineers","Cebu South","Cebu West","Datu Sikatuna (Bohol)","Isabel Leyte","Kalanggaman","Lapu-Lapu","Mandaue","Negros Del Norte","Negros Occidental","Negros Oriental","Northern Samar","Ormoc-Kananga","Palinpinon","Panay","San Carlos Negros","San Juanico","Toledo","Dumaguete","Cebu North","Iloilo","Metro Bacolod","Negros Island"],
      "Mindanao": ["Agusan","Allah Valley","Bukidnon","Cagayan De Oro","Cotabato","Davao","Davao Central","General Santos City","Lanao Del Sur","Mt. Apo","Pagadian City","Polomolok","Sultan Kudarat","Surigao","Taganito Claver","Wesmin","Zamboanga Del Norte","Misamis Oriental East","Davao Del Sur","Davao Del Norte","Davao Occidental","Iligan Bay","Iligan City","Misamis Occidental","Samal","Sarangani","Zambasulta"],
      "International": ["69th Chapter Singapore","Bahrain","Brunei","Crsa Riyadh","Indonesia","Japan","Jeddah","Ksa Riyadh","Qatar","Saudi Arabia","State Of Kuwait","UAE-Abu Dhabi","United Arab Emirates (UAE)","WRSA Jeddah","Yanbu & Rabigh","Oman","Kuwait"]
    };
const countries =  [
      { value: "93", name: "Afghanistan" },
      { value: "355", name: "Albania" },
      { value: "213", name: "Algeria" },
      { value: "1-684", name: "American Samoa" },
      { value: "376", name: "Andorra" },
      { value: "244", name: "Angola" },
      { value: "1-268", name: "Antigua and Barbuda" },
      { value: "54", name: "Argentina" },
      { value: "374", name: "Armenia" },
      { value: "297", name: "Aruba" },
      { value: "61", name: "Australia" },
      { value: "43", name: "Austria" },
      { value: "994", name: "Azerbaijan" },
      { value: "1-242", name: "Bahamas" },
      { value: "973", name: "Bahrain" },
      { value: "880", name: "Bangladesh" },
      { value: "1-246", name: "Barbados" },
      { value: "375", name: "Belarus" },
      { value: "32", name: "Belgium" },
      { value: "501", name: "Belize" },
      { value: "229", name: "Benin" },
      { value: "1-441", name: "Bermuda" },
      { value: "975", name: "Bhutan" },
      { value: "591", name: "Bolivia" },
      { value: "387", name: "Bosnia and Herzegovina" },
      { value: "267", name: "Botswana" },
      { value: "47", name: "Bouvet Island" },
      { value: "55", name: "Brazil" },
      { value: "246", name: "British Indian Ocean Territory" },
      { value: "673", name: "Brunei Darussalam" },
      { value: "359", name: "Bulgaria" },
      { value: "226", name: "Burkina Faso" },
      { value: "257", name: "Burundi" },
      { value: "855", name: "Cambodia" },
      { value: "237", name: "Cameroon" },
      { value: "1", name: "Canada" },
      { value: "238", name: "Cape Verde" },
      { value: "1-345", name: "Cayman Islands" },
      { value: "236", name: "Central African Republic" },
      { value: "235", name: "Chad" },
      { value: "56", name: "Chile" },
      { value: "86", name: "China" },
      { value: "61", name: "Christmas Island" },
      { value: "61", name: "Cocos (Keeling) Islands" },
      { value: "57", name: "Colombia" },
      { value: "269", name: "Comoros" },
      { value: "242", name: "Congo" },
      { value: "243", name: "Democratic Republic of the Congo" },
      { value: "682", name: "Cook Islands" },
      { value: "506", name: "Costa Rica" },
      { value: "385", name: "Croatia" },
      { value: "53", name: "Cuba" },
      { value: "599", name: "Curaçao" },
      { value: "357", name: "Cyprus" },
      { value: "420", name: "Czech Republic" },
      { value: "225", name: "Côte d’Ivoire" },
      { value: "45", name: "Denmark" },
      { value: "253", name: "Djibouti" },
      { value: "1-767", name: "Dominica" },
      { value: "1-809", name: "Dominican Republic" },
      { value: "593", name: "Ecuador" },
      { value: "20", name: "Egypt" },
      { value: "503", name: "El Salvador" },
      { value: "240", name: "Equatorial Guinea" },
      { value: "291", name: "Eritrea" },
      { value: "372", name: "Estonia" },
      { value: "251", name: "Ethiopia" },
      { value: "500", name: "Falkland Islands (Malvinas)" },
      { value: "298", name: "Faroe Islands" },
      { value: "679", name: "Fiji" },
      { value: "358", name: "Finland" },
      { value: "33", name: "France" },
      { value: "594", name: "French Guiana" },
      { value: "689", name: "French Polynesia" },
      { value: "241", name: "Gabon" },
      { value: "220", name: "Gambia" },
      { value: "995", name: "Georgia" },
      { value: "49", name: "Germany" },
      { value: "233", name: "Ghana" },
      { value: "350", name: "Gibraltar" },
      { value: "30", name: "Greece" },
      { value: "299", name: "Greenland" },
      { value: "1-473", name: "Grenada" },
      { value: "590", name: "Guadeloupe" },
      { value: "1-671", name: "Guam" },
      { value: "502", name: "Guatemala" },
      { value: "44", name: "Guernsey" },
      { value: "224", name: "Guinea" },
      { value: "245", name: "Guinea-Bissau" },
      { value: "592", name: "Guyana" },
      { value: "509", name: "Haiti" },
      { value: "379", name: "Holy See" },
      { value: "504", name: "Honduras" },
      { value: "852", name: "Hong Kong" },
      { value: "36", name: "Hungary" },
      { value: "354", name: "Iceland" },
      { value: "91", name: "India" },
      { value: "62", name: "Indonesia" },
      { value: "98", name: "Iran" },
      { value: "964", name: "Iraq" },
      { value: "353", name: "Ireland" },
      { value: "44", name: "Isle of Man" },
      { value: "972", name: "Israel" },
      { value: "39", name: "Italy" },
      { value: "1876", name: "Jamaica" },
      { value: "81", name: "Japan" },
      { value: "44", name: "Jersey" },
      { value: "962", name: "Jordan" },
      { value: "7", name: "Kazakhstan" },
      { value: "254", name: "Kenya" },
      { value: "686", name: "Kiribati" },
      { value: "850", name: "North Korea" },
      { value: "82", name: "South Korea" },
      { value: "965", name: "Kuwait" },
      { value: "996", name: "Kyrgyzstan" },
      { value: "856", name: "Laos" },
      { value: "371", name: "Latvia" },
      { value: "961", name: "Lebanon" },
      { value: "266", name: "Lesotho" },
      { value: "231", name: "Liberia" },
      { value: "218", name: "Libya" },
      { value: "423", name: "Liechtenstein" },
      { value: "370", name: "Lithuania" },
      { value: "352", name: "Luxembourg" },
      { value: "853", name: "Macau" },
      { value: "389", name: "Macedonia" },
      { value: "261", name: "Madagascar" },
      { value: "265", name: "Malawi" },
      { value: "60", name: "Malaysia" },
      { value: "960", name: "Maldives" },
      { value: "223", name: "Mali" },
      { value: "356", name: "Malta" },
      { value: "692", name: "Marshall Islands" },
      { value: "596", name: "Martinique" },
      { value: "222", name: "Mauritania" },
      { value: "230", name: "Mauritius" },
      { value: "52", name: "Mexico" },
      { value: "691", name: "Micronesia" },
      { value: "373", name: "Moldova" },
      { value: "377", name: "Monaco" },
      { value: "976", name: "Mongolia" },
      { value: "382", name: "Montenegro" },
      { value: "1664", name: "Montserrat" },
      { value: "212", name: "Morocco" },
      { value: "258", name: "Mozambique" },
      { value: "95", name: "Myanmar" },
      { value: "264", name: "Namibia" },
      { value: "674", name: "Nauru" },
      { value: "977", name: "Nepal" },
      { value: "31", name: "Netherlands" },
      { value: "687", name: "New Caledonia" },
      { value: "64", name: "New Zealand" },
      { value: "505", name: "Nicaragua" },
      { value: "227", name: "Niger" },
      { value: "234", name: "Nigeria" },
      { value: "683", name: "Niue" },
      { value: "672", name: "Norfolk Island" },
      { value: "850", name: "Northern Mariana Islands" },
      { value: "47", name: "Norway" },
      { value: "968", name: "Oman" },
      { value: "92", name: "Pakistan" },
      { value: "680", name: "Palau" },
      { value: "970", name: "Palestine" },
      { value: "507", name: "Panama" },
      { value: "675", name: "Papua New Guinea" },
      { value: "595", name: "Paraguay" },
      { value: "51", name: "Peru" },
      { value: "63", name: "Philippines" },
      { value: "48", name: "Poland" },
      { value: "351", name: "Portugal" },
      { value: "1-787", name: "Puerto Rico" },
      { value: "974", name: "Qatar" },
      { value: "262", name: "Réunion" },
      { value: "40", name: "Romania" },
      { value: "7", name: "Russia" },
      { value: "250", name: "Rwanda" },
      { value: "590", name: "Saint Barthélemy" },
      { value: "290", name: "Saint Helena" },
      { value: "1-869", name: "Saint Kitts and Nevis" },
      { value: "1-758", name: "Saint Lucia" },
      { value: "508", name: "Saint Pierre and Miquelon" },
      { value: "1-784", name: "Saint Vincent and the Grenadines" },
      { value: "685", name: "Samoa" },
      { value: "378", name: "San Marino" },
      { value: "239", name: "Sao Tome and Principe" },
      { value: "966", name: "Saudi Arabia" },
      { value: "221", name: "Senegal" },
      { value: "381", name: "Serbia" },
      { value: "248", name: "Seychelles" },
      { value: "232", name: "Sierra Leone" },
      { value: "65", name: "Singapore" },
      { value: "421", name: "Slovakia" },
      { value: "386", name: "Slovenia" },
      { value: "677", name: "Solomon Islands" },
      { value: "252", name: "Somalia" },
      { value: "27", name: "South Africa" },
      { value: "211", name: "South Sudan" },
      { value: "34", name: "Spain" },
      { value: "94", name: "Sri Lanka" },
      { value: "249", name: "Sudan" },
      { value: "597", name: "Suriname" },
      { value: "268", name: "Swaziland" },
      { value: "46", name: "Sweden" },
      { value: "41", name: "Switzerland" },
      { value: "963", name: "Syria" },
      { value: "886", name: "Taiwan" },
      { value: "992", name: "Tajikistan" },
      { value: "255", name: "Tanzania" },
      { value: "66", name: "Thailand" },
      { value: "670", name: "Timor-Leste" },
      { value: "352", name: "Togo" },
      { value: "676", name: "Tonga" },
      { value: "1-868", name: "Trinidad and Tobago" },
      { value: "216", name: "Tunisia" },
      { value: "90", name: "Turkey" },
      { value: "993", name: "Turkmenistan" },
      { value: "1-649", name: "Turks and Caicos Islands" },
      { value: "688", name: "Tuvalu" },
      { value: "256", name: "Uganda" },
      { value: "380", name: "Ukraine" },
      { value: "971", name: "United Arab Emirates" },
      { value: "44", name: "United Kingdom" },
      { value: "1", name: "United States" },
      { value: "598", name: "Uruguay" },
      { value: "998", name: "Uzbekistan" },
      { value: "678", name: "Vanuatu" },
      { value: "58", name: "Venezuela" },
      { value: "84", name: "Vietnam" },
      { value: "681", name: "Wallis and Futuna" },
      { value: "967", name: "Yemen" },
      { value: "260", name: "Zambia" },
      { value: "263", name: "Zimbabwe" }
    ];
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // --- Constants and State Management ---
    const STATE_KEY = 'natconRegistrationState';
    
    const getInitialState = () => ({
        company_name: '',
        company_address: '',
        delegates: []
    });

    const saveState = (state) => sessionStorage.setItem(STATE_KEY, JSON.stringify(state));
    const loadState = () => JSON.parse(sessionStorage.getItem(STATE_KEY)) || null;
    const clearState = () => sessionStorage.removeItem(STATE_KEY);

    // --- Element References ---
    const companySection = document.getElementById('company-section'),
          delegateArea = document.getElementById('delegate-area'),
          tblBody = document.getElementById('tblDelegates').querySelector('tbody'),
          frm = document.getElementById('frmDelegate'),
          btnInit = document.getElementById('btnInit'),
          btnSubmit = document.getElementById('btnSubmit'),
          btnCancel = document.getElementById('btnCancel'),
          btnGen = document.getElementById('btnGenerate'),
          inpCoName = document.getElementById('company-name'),
          inpCoAddress = document.getElementById('company-address'),
          selRegion = document.getElementById('region'),
          selChapter = document.getElementById('chapter'),
          selCountry = document.getElementById('delegates-country'),
          formTitle = document.getElementById('form-title');

    // --- Helper Functions ---
    const renderTable = () => {
        const state = loadState();
        tblBody.innerHTML = '';
        if (!state || !state.delegates) return;

        state.delegates.forEach(delegate => {
            const tr = tblBody.insertRow();
            tr.dataset.id = delegate.temp_id;
            tr.innerHTML = `
                <td>${delegate.delegates_fname || ''}</td>
                <td>${delegate.delegates_lname || ''}</td>
                <td>${delegate.delegates_emailid || ''}</td>
                <td>${delegate.chapter || ''}</td>
                <td>
                    <button class="btn-icon edit" data-id="${delegate.temp_id}">✏️</button>
                    <button class="btn-icon delete" data-id="${delegate.temp_id}">🗑️</button>
                </td>`;
        });
        
        btnGen.style.display = state.delegates.length > 0 ? 'inline-block' : 'none';
    };
    
    const resetForm = () => {
        frm.reset();
        formTitle.textContent = 'Add Delegate Information';
        btnSubmit.textContent = 'Add Delegate';
        btnSubmit.classList.replace('btn-success', 'btn-primary');
        btnCancel.style.display = 'none';
        document.getElementById('temp_id').value = '';
    };

    const showDelegateArea = (state) => {
        document.getElementById('company-name-display').textContent = state.company_name;
        document.getElementById('company-address-display').textContent = state.company_address;
        companySection.style.display = 'none';
        delegateArea.style.display = 'block';
        renderTable();
    };
    
    // --- Initialization ---
    const initializePage = () => {
        countries.forEach(c => selCountry.add(new Option(c.name, c.value)));
        selCountry.value = "63"; // Default to Philippines

        selRegion.addEventListener('change', function() {
            selChapter.innerHTML = '<option value="">Select Chapter</option>';
            (chapters[this.value] || []).forEach(ch => selChapter.add(new Option(ch, ch)));
        });

        const savedState = loadState();
        if (savedState && savedState.company_name) {
            showDelegateArea(savedState);
        }
    };
    
    initializePage();

    // --- Event Listeners ---
    btnInit.addEventListener('click', () => {
        const companyName = inpCoName.value.trim();
        const companyAddress = inpCoAddress.value.trim();
        if (!companyName || !companyAddress) {
            return alert('Please enter both company name and address.');
        }
        
        const newState = getInitialState();
        newState.company_name = companyName;
        newState.company_address = companyAddress;
        saveState(newState);
        showDelegateArea(newState);
    });

    btnSubmit.addEventListener('click', () => {
        const state = loadState();
        if (!state) return;
        
        const tempId = document.getElementById('temp_id').value;
        const formData = new FormData(frm);
        const delegateData = Object.fromEntries(formData.entries());
        
        if (tempId) { // Update
            const index = state.delegates.findIndex(d => d.temp_id == tempId);
            if (index !== -1) {
                state.delegates[index] = { ...state.delegates[index], ...delegateData };
            }
        } else { // Add
            delegateData.temp_id = Date.now();
            state.delegates.push(delegateData);
        }
        
        saveState(state);
        renderTable();
        resetForm();
    });

    tblBody.addEventListener('click', e => {
        const btn = e.target.closest('button.btn-icon');
        if (!btn) return;
        
        const state = loadState();
        const tempId = btn.dataset.id;
        
        if (btn.classList.contains('delete')) {
            if (!confirm('Delete this delegate?')) return;
            state.delegates = state.delegates.filter(d => d.temp_id != tempId);
            saveState(state);
            renderTable();
        } else if (btn.classList.contains('edit')) {
            const delegate = state.delegates.find(d => d.temp_id == tempId);
            if (!delegate) return;
            
            for (const key in delegate) {
                if (frm.elements[key]) {
                    frm.elements[key].value = delegate[key] || '';
                }
            }
            
            selRegion.dispatchEvent(new Event('change'));
            setTimeout(() => { frm.elements.chapter.value = delegate.chapter; }, 100);
            
            formTitle.textContent = 'Editing Delegate Information';
            btnSubmit.textContent = 'Update Delegate';
            btnSubmit.classList.replace('btn-primary', 'btn-success');
            btnCancel.style.display = 'inline-block';
            frm.scrollIntoView({ behavior: 'smooth' });
        }
    });

    btnCancel.addEventListener('click', resetForm);

    btnGen.addEventListener('click', () => {
        const state = loadState();
        if (!state || state.delegates.length === 0) {
            return alert('No delegates to generate. Please add at least one delegate.');
        }

        btnGen.disabled = true;
        btnGen.textContent = 'Generating...';

        const payload = {
            action: 'finalize_and_generate',
            ...state
        };

        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(js => {
            if (js.error) {
                alert('Error: ' + js.error);
            } else {
                alert('Success! Your registration data has been saved and the Excel file is ready for download.');
                window.location.href = js.file;
                clearState();
                setTimeout(() => window.location.reload(), 1000); 
            }
        })
        .catch(err => {
            console.error('Fetch Error:', err);
            alert('A critical error occurred. Could not connect to the server.');
        })
        .finally(() => {
            btnGen.disabled = false;
            btnGen.textContent = 'Generate Final Document';
        });
    });
});
</script>
</body>
</html>