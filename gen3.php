<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PSME Natcon Bulk Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .btn-icon { background:none;border:none;font-size:1.1em;cursor:pointer; }
    #delegate-area { display: none; }
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
                    <input type="text" class="form-control form-control-lg" id="company-name" required placeholder="e.g., Acme Corporation" />
                </div>
                <div class="col-md-6">
                    <label for="company-address" class="form-label">Company Address</label>
                    <input type="text" class="form-control form-control-lg" id="company-address" required placeholder="e.g., 123 Innovation Drive, Tech City" />
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

            <form id="frmDelegate" class="mt-4" novalidate>
              <input type="hidden" name="temp_id" id="temp_id" />
              <div class="card bg-light border-0 mb-4">
                <div class="card-body text-center">
                  <h5 id="form-title" class="card-title m-0">Add Delegate Information</h5>
                </div>
              </div>
              <div class="row g-3">
                 <div class="col-md-4">
                   <label class="form-label">First Name</label>
                   <input type="text"
                          class="form-control"
                          name="firstname"
                          required
                          pattern="[A-Za-z ]+"
                          title="Letters and spaces only" />
                 </div>
                 <div class="col-md-4">
                   <label class="form-label">Middle Name</label>
                   <input type="text"
                          class="form-control"
                          name="middle"
                          pattern="[A-Za-z ]*"
                          title="Letters and spaces only" />
                 </div>
                 <div class="col-md-4">
                   <label class="form-label">Last Name</label>
                   <input type="text"
                          class="form-control"
                          name="lastname"
                          required
                          pattern="[A-Za-z ]+"
                          title="Letters and spaces only" />
                 </div>
                 <div class="col-md-2">
                   <label class="form-label">Suffix</label>
                   <input type="text" class="form-control" name="suffix" />
                 </div>
                 <div class="col-md-4">
                   <label class="form-label">Date of Birth</label>
                   <input type="date" class="form-control" name="dateofbirth" required />
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Email</label>
                   <input type="email" class="form-control" name="emailid" required />
                 </div>
                 <div class="col-md-4">
                   <label class="form-label">Country</label>
                   <select class="form-select" name="country" id="delegates-country" required>
                     <option value="">Select</option>
                   </select>
                 </div>
                 <div class="col-md-4">
                   <label class="form-label">Mobile Number</label>
                   <input type="text"
                          class="form-control"
                          name="mobilenumber"
                          required
                          pattern="\d+"
                          title="Numbers only" />
                 </div>
                 <div class="col-md-4">
                   <label class="form-label">Region</label>
                   <select class="form-select" name="region" id="region" required>
                     <option value="">Select</option>
                     <option>NCR</option>
                     <option>Luzon</option>
                     <option>Visayas</option>
                     <option>Mindanao</option>
                     <option>International</option>
                   </select>
                 </div>
                 <div class="col-md-4">
                   <label class="form-label">Chapter</label>
                   <select class="form-select" name="chapter" id="chapter" required>
                     <option value="">Select Region First</option>
                   </select>
                 </div>
                 <div class="col-md-4">
                   <label class="form-label">PRC License Type</label>
                   <select class="form-select" name="prc_license_type" required>
                     <option value="">Select</option>
                     <option>Professional Mechanical Engineer</option>
                     <option>Registered Mechanical Engineer</option>
                     <option>Certified Plant Mechanic</option>
                     <option>ME Graduate</option>
                     <option>Other</option>
                   </select>
                 </div>
                 <div class="col-md-4">
                   <label class="form-label">PRC License Number</label>
                   <input type="text" class="form-control" name="prc_license_number" required />
                 </div>
                 <div class="col-md-4">
                   <label class="form-label">PRC License Expiration Date</label>
                   <input type="date" class="form-control" name="prc_license_expiration_date" required />
                 </div>
                 <div class="col-md-4">
                   <label class="form-label">Sector</label>
                   <select class="form-select" name="sector" required>
                     <option value="">Select</option>
                     <option>Government</option>
                     <option>Private</option>
                   </select>
                 </div>
                 <div class="col-md-4">
                   <label class="form-label">Register Type</label>
                   <select class="form-select" name="register_type" required>
                     <option value="">Select</option>
                     <option>Regular</option>
                     <option>Life / Associate Member</option>
                     <option>Guest/Non‑member</option>
                   </select>
                 </div>
                 <div class="col-md-4">
                   <label class="form-label">PWD?</label>
                   <select class="form-select" name="isPWD" required>
                     <option value="">Select</option>
                     <option>Yes</option>
                     <option>No</option>
                   </select>
                 </div>
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
  // --- Data for dropdowns ---
  const chapters = {
      "NCR": ["Embo","Intramuros","Las Piñas-Muntinlupa","Makati","Makati (Host)","Mandaluyong","Manila","Marikina City","Manila West","Metro Manila BFP","Medical Services","Metro North","NCR Academe","NCR Metrosouth","Ortigas-Pasig","Parañaque","Pasay","Pasig","Quezon City Agham","Quezon City","Taguig","Manila (Host)","South Harbor (PCG)","Test","Balara","Logomeap","Makati CBD","Metro Marikina","NCR Port Area","Quezon City Central","Quezon City United"],
      "Luzon": ["Abra","Albay-Legazpi","Bacon-Manito","Baguio-Cordillera","Bataan","Batangas","Bulacan","Cagayan Valley","Camarines Norte (Daet)","Camsur-Naga","Catanduanes","Cavite","Cavite-Carsigma","Cavite-Imbarkaw","Central Laguna","Clark","Isabela-Quirino","Ilocos","La Union","Makban","Masbate","Mindoro","Nueva Ecija","Nueva Viscaya","Palawan","Pampanga (Host)","Pangasinan","Pililla-Jalajala","QBL Host","Quezon Province","Rinconada","Rizal","Rizal - Antipolo","Romblon","Sorsogon (Host)","Tarlac","Western Batangas","Aurora","Benguet-Southwest","Bulacan East","Bulacan North","Mankayan-Mt. Province","Pampanga","Rio Tuba","Subic","Baguio City","Cordillera","Batangas East"],
      "Visayas": ["Aklan","Capiz","Cebu","Cebu Central","Cebu East","Cebu Hotel And Building Engineers","Cebu South","Cebu West","Datu Sikatuna (Bohol)","Isabel Leyte","Kalanggaman","Lapu-Lapu","Mandaue","Negros Del Norte","Negros Occidental","Negros Oriental","Northern Samar","Ormoc-Kananga","Palinpinon","Panay","San Carlos Negros","San Juanico","Toledo","Dumaguete","Cebu North","Iloilo","Metro Bacolod","Negros Island"],
      "Mindanao": ["Agusan","Allah Valley","Bukidnon","Cagayan De Oro","Cotabato","Davao","Davao Central","General Santos City","Lanao Del Sur","Mt. Apo","Pagadian City","Polomolok","Sultan Kudarat","Surigao","Taganito Claver","Wesmin","Zamboanga Del Norte","Misamis Oriental East","Davao Del Sur","Davao Del Norte","Davao Occidental","Iligan Bay","Iligan City","Misamis Occidental","Samal","Sarangani","Zambasulta"],
      "International": ["69th Chapter Singapore","Bahrain","Brunei","Crsa Riyadh","Indonesia","Japan","Jeddah","Ksa Riyadh","Qatar","Saudi Arabia","State Of Kuwait","UAE-Abu Dhabi","United Arab Emirates (UAE)","WRSA Jeddah","Yanbu & Rabigh","Oman","Kuwait"]
    };
let   countries =  [
      { value: "Afghanistan", name: "Afghanistan" },
      { value: "Albania", name: "Albania" },
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
countries = countries.map(c => ({ value: c.name, name: c.name }));

  // --- State helpers ---
  const STATE_KEY = 'natconRegistrationState';
  const getInitialState = () => ({ company_name: '', company_address: '', delegates: [] });
  const saveState = s => sessionStorage.setItem(STATE_KEY, JSON.stringify(s));
  const loadState = () => JSON.parse(sessionStorage.getItem(STATE_KEY)) || null;
  const clearState = () => sessionStorage.removeItem(STATE_KEY);

  // --- DOM refs ---
  const companySection = document.getElementById('company-section');
  const delegateArea   = document.getElementById('delegate-area');
  const tblBody        = document.getElementById('tblDelegates').querySelector('tbody');
  const frm            = document.getElementById('frmDelegate');
  const btnInit        = document.getElementById('btnInit');
  const btnSubmit      = document.getElementById('btnSubmit');
  const btnCancel      = document.getElementById('btnCancel');
  const btnGen         = document.getElementById('btnGenerate');
  const inpCoName      = document.getElementById('company-name');
  const inpCoAddress   = document.getElementById('company-address');
  const selRegion      = document.getElementById('region');
  const selChapter     = document.getElementById('chapter');
  const selCountry     = document.getElementById('delegates-country');
  const formTitle      = document.getElementById('form-title');

  // --- Initialize page ---
  function initializePage() {
    // populate country dropdown
    countries.forEach(c => selCountry.add(new Option(c.name, c.value)));
    selCountry.value = 'Philippines';

    // region→chapter linkage
    selRegion.addEventListener('change', () => {
      selChapter.innerHTML = '<option value="">Select Region First</option>';
      (chapters[selRegion.value]||[]).forEach(ch => selChapter.add(new Option(ch,ch)));
    });

    // restore state if any
    const s = loadState();
    if (s && s.company_name) showDelegateArea(s);
  }

  // --- Render table ---
  function renderTable() {
    const s = loadState();
    tblBody.innerHTML = '';
    if (!s || !s.delegates) return;
    s.delegates.forEach(d => {
      const tr = tblBody.insertRow();
      tr.dataset.id = d.temp_id;
      tr.innerHTML = `
        <td>${d.firstname||''}</td>
        <td>${d.lastname||''}</td>
        <td>${d.emailid||''}</td>
        <td>${d.chapter||''}</td>
        <td>
          <button class="btn-icon edit" data-id="${d.temp_id}">✏️</button>
          <button class="btn-icon delete" data-id="${d.temp_id}">🗑️</button>
        </td>`;
    });
    btnGen.style.display = s.delegates.length ? 'inline-block' : 'none';
  }

  // --- Show delegate area ---
  function showDelegateArea(s) {
    document.getElementById('company-name-display').textContent = s.company_name;
    document.getElementById('company-address-display').textContent = s.company_address;
    companySection.style.display = 'none';
    delegateArea.style.display    = 'block';
    renderTable();
  }

  // --- Reset form ---
  function resetForm() {
    frm.reset();
    formTitle.textContent = 'Add Delegate Information';
    btnSubmit.textContent = 'Add Delegate';
    btnSubmit.classList.replace('btn-success','btn-primary');
    btnCancel.style.display = 'none';
    document.getElementById('temp_id').value = '';
  }

  // --- Event listeners ---
  btnInit.addEventListener('click', () => {
    const name = inpCoName.value.trim();
    const addr = inpCoAddress.value.trim();
    if (!name || !addr) {
      alert('Company name and address required');
      return;
    }
    const s = getInitialState();
    s.company_name    = name;
    s.company_address = addr;
    saveState(s);
    showDelegateArea(s);
  });

  btnSubmit.addEventListener('click', () => {
    // 1) Enforce HTML5 validation
    if (!frm.checkValidity()) {
      frm.reportValidity();
      return;
    }

    // 2) Gather data
    const s = loadState(); if (!s) return;
    const tempId = document.getElementById('temp_id').value;
    const data = Object.fromEntries(new FormData(frm).entries());
    // ensure country = its displayed text
    data.country = selCountry.selectedOptions[0].text;

    if (tempId) {
      // update existing
      const idx = s.delegates.findIndex(d => d.temp_id == tempId);
      if (idx > -1) s.delegates[idx] = { ...s.delegates[idx], ...data };
    } else {
      // add new
      data.temp_id = Date.now();
      s.delegates.push(data);
    }

    saveState(s);
    renderTable();
    resetForm();
  });

  tblBody.addEventListener('click', e => {
    const btn = e.target.closest('button');
    if (!btn) return;
    const s = loadState(), id = btn.dataset.id;
    if (btn.classList.contains('delete')) {
      if (confirm('Delete this delegate?')) {
        s.delegates = s.delegates.filter(d => d.temp_id != id);
        saveState(s);
        renderTable();
      }
    } else if (btn.classList.contains('edit')) {
      const d = s.delegates.find(d => d.temp_id == id);
      if (!d) return;
      Object.keys(d).forEach(k => {
        if (frm.elements[k]) frm.elements[k].value = d[k];
      });
      selRegion.dispatchEvent(new Event('change'));
      setTimeout(() => frm.elements.chapter.value = d.chapter, 100);
      formTitle.textContent = 'Editing Delegate Information';
      btnSubmit.textContent = 'Update Delegate';
      btnSubmit.classList.replace('btn-primary','btn-success');
      btnCancel.style.display = 'inline-block';
      frm.scrollIntoView({ behavior: 'smooth' });
    }
  });

  btnCancel.addEventListener('click', resetForm);

  btnGen.addEventListener('click', () => {
    const s = loadState();
    if (!s || !s.delegates.length) {
      alert('No delegates to generate');
      return;
    }
    btnGen.disabled = true;
    btnGen.textContent = 'Generating...';
    fetch('gen3_api.php', {
      method: 'POST',
      headers: { 'Content-Type':'application/json' },
      body: JSON.stringify({
        action: 'finalize_and_generate',
        company_name:    s.company_name,
        company_address: s.company_address,
        delegates:       s.delegates
      })
    })
    .then(r => r.json())
    .then(js => {
      if (js.error) alert('Error: ' + js.error);
      else {
        alert('Success! Downloading...');
        window.location.href = js.file;
        clearState();
        setTimeout(() => location.reload(), 500);
      }
    })
    .catch(() => alert('Could not connect'))
    .finally(() => {
      btnGen.disabled   = false;
      btnGen.textContent = 'Generate Final Document';
    });
  });

  document.addEventListener('DOMContentLoaded', initializePage);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>