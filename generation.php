<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PSME Natcon Bulk Registration</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">PSME Natcon</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>

  <div class="container">
    <div class="card shadow-sm mb-5">
      <div class="card-body">
        <h1 class="mb-3">Bulk Registration Document Generator</h1>
        <p class="mb-4">Generate the correct Excel template for PSME Natcon bulk registration.</p>

        <!-- Company Name -->
        <div class="mb-4">
          <label for="company-name" class="form-label">Company Name</label>
          <input type="text" class="form-control" id="company-name" name="company_name" required />
        </div>

        <!-- Start Form -->
        <form action="generate.php" method="post">
          <!-- Delegates Info Title -->
          <div class="card bg-light border-0 mb-4">
            <div class="card-body text-center">
              <h5 class="card-title m-0">Delegates Information</h5>
            </div>
          </div>

          <div class="row g-3">
            <!-- Name Fields -->
            <div class="col-md-4">
              <label for="delegates-fname" class="form-label">First Name</label>
              <input type="text" class="form-control" id="delegates-fname" name="delegates_fname" required />
            </div>
            <div class="col-md-4">
              <label for="delegates-mname" class="form-label">Middle Name</label>
              <input type="text" class="form-control" id="delegates-mname" name="delegates_mname" />
            </div>
            <div class="col-md-4">
              <label for="delegates-lname" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="delegates-lname" name="delegates_lname" required />
            </div>
            <div class="col-md-2">
              <label for="delegates-suffix" class="form-label">Suffix</label>
              <input type="text" class="form-control" id="delegates-suffix" name="delegates_suffix" />
            </div>
            <div class="col-md-4">
              <label for="delegates-dob" class="form-label">Date of Birth</label>
              <input type="date" class="form-control" id="delegates-dob" name="delegates_dob" required />
            </div>
            <div class="col-md-6">
              <label for="delegates-emailid" class="form-label">Email</label>
              <input type="email" class="form-control" id="delegates-emailid" name="delegates_emailid" required />
            </div>

            <!-- Country and Contact -->
            <div class="col-md-4">
              <label for="delegates-country" class="form-label">Country</label>
              <select class="form-select" id="delegates-country" name="delegates_country" required>
                <option value="">Select Country</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="delegates_contactno" class="form-label">Contact No</label>
              <input type="text" class="form-control" id="delegates_contactno" name="delegates_contactno" required />
            </div>

            <!-- PRC Section -->
            <div class="form-section row g-3">
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
              <div class="col-md-4">
                <label for="prcLicenseNo" class="form-label">PRC License No</label>
                <input type="text" class="form-control" id="prcLicenseNo" name="prcLicenseNo" required />
              </div>
              <div class="col-md-4">
                <label for="prcLicenseExpiration" class="form-label">PRC License Expiration</label>
                <input type="date" class="form-control" id="prcLicenseExpiration" name="prcLicenseExpiration" required />
              </div>
            </div>

            <!-- Region & Chapter -->
            <div class="form-section row g-3">
              <div class="col-md-4">
                <label for="region" class="form-label">Region</label>
                <select class="form-select" id="region" name="region" required>
                  <option value="">Select Region</option>
                  <option value="NCR">NCR</option>
                  <option value="Luzon">Luzon</option>
                  <option value="Visayas">Visayas</option>
                  <option value="Mindanao">Mindanao</option>
                  <option value="International">International</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="chapter" class="form-label">Chapter</label>
                <select class="form-select" id="chapter" name="chapter" required>
                  <option value="">Select Chapter</option>
                </select>
              </div>
            </div>

            <!-- Other Info -->
            <div class="form-section row g-3">
              <div class="col-md-4">
                <label for="sector" class="form-label">Sector</label>
                <select class="form-select" id="sector" name="sector" required>
                  <option value="">Select Sector</option>
                  <option value="Government">Government</option>
                  <option value="Private">Private</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="register_type" class="form-label">Register Type</label>
                <select class="form-select" id="register_type" name="register_type" required>
                  <option value="">Select Register Type</option>
                  <option value="Regular">Regular</option>
                  <option value="Life/Associate">Life/Associate</option>
                  <option value="Guest/Non-member">Guest/Non-member</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="isPWD" class="form-label">PWD?</label>
                <select class="form-select" id="isPWD" name="isPWD" required>
                  <option value="">Select</option>
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
           <div class="row g-3 mt-4">
            <div class="col-6 col-lg-12"></div>
           <div class="mt-4 text-end">
            <button type="button" class="btn btn-primary btn-lg">
              Add Delegate
            </button>
          </div>
          <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary btn-lg">
              Generate Document
            </button>
          </div>
          </div>

        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const chapters = {
      "NCR": ["Embo","Intramuros","Las Piñas-Muntinlupa","Makati","Makati (Host)","Mandaluyong","Manila","Marikina City","Manila West","Metro Manila BFP","Medical Services","Metro North","NCR Academe","NCR Metrosouth","Ortigas-Pasig","Parañaque","Pasay","Pasig","Quezon City Agham","Quezon City","Taguig","Manila (Host)","South Harbor (PCG)","Test","Balara","Logomeap","Makati CBD","Metro Marikina","NCR Port Area","Quezon City Central","Quezon City United"],
      "Luzon": ["Abra","Albay-Legazpi","Bacon-Manito","Baguio-Cordillera","Bataan","Batangas","Bulacan","Cagayan Valley","Camarines Norte (Daet)","Camsur-Naga","Catanduanes","Cavite","Cavite-Carsigma","Cavite-Imbarkaw","Central Laguna","Clark","Isabela-Quirino","Ilocos","La Union","Makban","Masbate","Mindoro","Nueva Ecija","Nueva Viscaya","Palawan","Pampanga (Host)","Pangasinan","Pililla-Jalajala","QBL Host","Quezon Province","Rinconada","Rizal","Rizal - Antipolo","Romblon","Sorsogon (Host)","Tarlac","Western Batangas","Aurora","Benguet-Southwest","Bulacan East","Bulacan North","Mankayan-Mt. Province","Pampanga","Rio Tuba","Subic","Baguio City","Cordillera","Batangas East"],
      "Visayas": ["Aklan","Capiz","Cebu","Cebu Central","Cebu East","Cebu Hotel And Building Engineers","Cebu South","Cebu West","Datu Sikatuna (Bohol)","Isabel Leyte","Kalanggaman","Lapu-Lapu","Mandaue","Negros Del Norte","Negros Occidental","Negros Oriental","Northern Samar","Ormoc-Kananga","Palinpinon","Panay","San Carlos Negros","San Juanico","Toledo","Dumaguete","Cebu North","Iloilo","Metro Bacolod","Negros Island"],
      "Mindanao": ["Agusan","Allah Valley","Bukidnon","Cagayan De Oro","Cotabato","Davao","Davao Central","General Santos City","Lanao Del Sur","Mt. Apo","Pagadian City","Polomolok","Sultan Kudarat","Surigao","Taganito Claver","Wesmin","Zamboanga Del Norte","Misamis Oriental East","Davao Del Sur","Davao Del Norte","Davao Occidental","Iligan Bay","Iligan City","Misamis Occidental","Samal","Sarangani","Zambasulta"],
      "International": ["69th Chapter Singapore","Bahrain","Brunei","Crsa Riyadh","Indonesia","Japan","Jeddah","Ksa Riyadh","Qatar","Saudi Arabia","State Of Kuwait","UAE-Abu Dhabi","United Arab Emirates (UAE)","WRSA Jeddah","Yanbu & Rabigh","Oman","Kuwait"]
    };

    const countries = [
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

    document.addEventListener("DOMContentLoaded", () => {
      const regionSelect = document.getElementById("region"),
            chapterSelect = document.getElementById("chapter"),
            countrySelect = document.getElementById("delegates-country");

      // Populate countries
      countries.sort((a, b) => a.name.localeCompare(b.name)).forEach(c => {
        const o = document.createElement("option");
        o.value = c.value;
        o.textContent = c.name;
        countrySelect.appendChild(o);
      });

      // Update chapters based on region
      regionSelect.addEventListener("change", function() {
        chapterSelect.innerHTML = '<option value="">Select Chapter</option>';
        (chapters[this.value] || []).forEach(ch => {
          const o = document.createElement("option");
          o.value = ch;
          o.textContent = ch;
          chapterSelect.appendChild(o);
        });
      });
    });
  </script>
</body>
</html>
<?php