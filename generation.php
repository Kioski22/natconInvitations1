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
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h1 class="card-title mb-3">Bulk Registration Document Generator</h1>
        <p class="card-text">Generate the correct Excel template for PSME Natcon bulk registration.</p>

        <form action="generate.php" method="post">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="company-name">Company Name</label>
              <input class="form-control" type="text" id="company-name" name="company_name" required>
            </div>

            <div class="col-md-4">
              <label class="form-label" for="delegates-fname">First Name</label>
              <input class="form-control" type="text" id="delegates-fname" name="delegates_fname" required>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="delegates-mname">Middle Name</label>
              <input class="form-control" type="text" id="delegates-mname" name="delegates_mname">
            </div>
            <div class="col-md-4">
              <label class="form-label" for="delegates-lname">Last Name</label>
              <input class="form-control" type="text" id="delegates-lname" name="delegates_lname" required>
            </div>
            <div class="col-md-2">
              <label class="form-label" for="delegates-suffix">Suffix</label>
              <input class="form-control" type="text" id="delegates-suffix" name="delegates_suffix">
            </div>

            <div class="col-md-4">
              <label class="form-label" for="delegates-dob">Date of Birth</label>
              <input class="form-control" type="date" id="delegates-dob" name="delegates_dob" required>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="delegates-emailid">Email</label>
              <input class="form-control" type="email" id="delegates-emailid" name="delegates_emailid" required>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="delegates-country">Country</label>
              <select class="form-select" id="delegates-country" name="delegates_country" required>
                <option value="">Select Country</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="delegates_contactno">Contact No</label>
              <input class="form-control" type="text" id="delegates_contactno" name="delegates_contactno" placeholder="Contact Number" required>
            </div>

            <div class="col-md-4">
              <label class="form-label" for="prcLicenseType">PRC License Type</label>
              <select class="form-select" id="prcLicenseType" name="prcLicenseType" required>
                <option value="">Select License Type</option>
                <option>Professional Mechanical Engineer</option>
                <option>Registered Mechanical Engineer</option>
                <option>Certified Plant Mechanic</option>
                <option>ME Graduate</option>
                <option>Other</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="prcLicenseNo">PRC License No</label>
              <input class="form-control" type="text" id="prcLicenseNo" name="prcLicenseNo" placeholder="PRC License Number" required>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="prcLicenseExpiration">PRC License Expiration</label>
              <input class="form-control" type="date" id="prcLicenseExpiration" name="prcLicenseExpiration" required>
            </div>

            <div class="col-md-4">
              <label class="form-label" for="region">Region</label>
              <select class="form-select" id="region" name="region" required>
                <option value="">Select Region</option>
                <option>NCR</option>
                <option>Luzon</option>
                <option>Visayas</option>
                <option>Mindanao</option>
                <option>International</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="chapter">Chapter</label>
              <select class="form-select" id="chapter" name="chapter" required>
                <option value="">Select Chapter</option>
              </select>
            </div>
          </div>

          <div class="mt-4 text-end">
            <button class="btn btn-primary" type="submit">Generate Document</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const chapters = {
      "NCR":["Embo","Intramuros","Las Piñas-Muntinlupa","Makati","Makati (Host)","Mandaluyong","Manila","Marikina City","Manila West","Metro Manila BFP","Medical Services","Metro North","NCR Academe","NCR Metrosouth","Ortigas-Pasig","Parañaque","Pasay","Pasig","Quezon City Agham","Quezon City","Taguig","Manila (Host)","South Harbor (PCG)","Test","Balara","Logomeap","Makati CBD","Metro Marikina","NCR Port Area","Quezon City Central","Quezon City United"],
      "Luzon":["Abra","Albay-Legazpi","Bacon-Manito","Baguio-Cordillera","Bataan","Batangas","Bulacan","Cagayan Valley","Camarines Norte (Daet)","Camsur-Naga","Catanduanes","Cavite","Cavite-Carsigma","Cavite-Imbarkaw","Central Laguna","Clark","Isabela-Quirino","Ilocos","La Union","Makban","Masbate","Mindoro","Nueva Ecija","Nueva Viscaya","Palawan","Pampanga (Host)","Pangasinan","Pililla-Jalajala","QBL Host","Quezon Province","Rinconada","Rizal","Rizal - Antipolo","Romblon","Sorsogon (Host)","Tarlac","Western Batangas","Aurora","Benguet-Southwest","Bulacan East","Bulacan North","Mankayan-Mt. Province","Pampanga","Rio Tuba","Subic","Baguio City","Cordillera","Batangas East"],
      "Visayas":["Aklan","Capiz","Cebu","Cebu Central","Cebu East","Cebu Hotel And Building Engineers","Cebu South","Cebu West","Datu Sikatuna (Bohol)","Isabel Leyte","Kalanggaman","Lapu-Lapu","Mandaue","Negros Del Norte","Negros Occidental","Negros Oriental","Northern Samar","Ormoc-Kananga","Palinpinon","Panay","San Carlos Negros","San Juanico","Toledo","Dumaguete","Cebu North","Iloilo","Metro Bacolod","Negros Island"],
      "Mindanao":["Agusan","Allah Valley","Bukidnon","Cagayan De Oro","Cotabato","Davao","Davao Central","General Santos City","Lanao Del Sur","Mt. Apo","Pagadian City","Polomolok","Sultan Kudarat","Surigao","Taganito Claver","Wesmin","Zamboanga Del Norte","Misamis Oriental East","Davao Del Sur","Davao Del Norte","Davao Occidental","Iligan Bay","Iligan City","Misamis Occidental","Samal","Sarangani","Zambasulta"],
      "International":["69th Chapter Singapore","Bahrain","Brunei","Crsa Riyadh","Indonesia","Japan","Jeddah","Ksa Riyadh","Qatar","Saudi Arabia","State Of Kuwait","UAE-Abu Dhabi","United Arab Emirates (UAE)","WRSA Jeddah","Yanbu & Rabigh","Oman","Kuwait"]
    };
    const countries=[
      {value:"93",name:"Afghanistan"},{value:"355",name:"Albania"},{value:"213",name:"Algeria"},{value:"1-684",name:"American Samoa"},{value:"376",name:"Andorra"},{value:"244",name:"Angola"},{value:"1-264",name:"Anguilla"},{value:"672",name:"Antarctica"},{value:"1-268",name:"Antigua and Barbuda"},{value:"54",name:"Argentina"},
      {value:"374",name:"Armenia"},{value:"297",name:"Aruba"},{value:"61",name:"Australia"},{value:"43",name:"Austria"},{value:"994",name:"Azerbaijan"},{value:"1-242",name:"Bahamas"},{value:"973",name:"Bahrain"},{value:"880",name:"Bangladesh"},{value:"1-246",name:"Barbados"},{value:"375",name:"Belarus"},
      {value:"32",name:"Belgium"},{value:"501",name:"Belize"},{value:"229",name:"Benin"},{value:"1-441",name:"Bermuda"},{value:"975",name:"Bhutan"},{value:"591",name:"Bolivia"},{value:"599",name:"Bonaire"},{value:"387",name:"Bosnia and Herzegovina"},{value:"267",name:"Botswana"},{value:"47",name:"Bouvet Island"},
      {value:"55",name:"Brazil"},{value:"246",name:"British Indian Ocean Territory"},{value:"673",name:"Brunei Darussalam"},{value:"359",name:"Bulgaria"},{value:"226",name:"Burkina Faso"},{value:"257",name:"Burundi"},{value:"855",name:"Cambodia"},{value:"237",name:"Cameroon"},{value:"1",name:"Canada"},{value:"238",name:"Cape Verde"},
      {value:"1-345",name:"Cayman Islands"},{value:"236",name:"Central African Republic"},{value:"235",name:"Chad"},{value:"56",name:"Chile"},{value:"86",name:"China"},{value:"61",name:"Christmas Island"},{value:"61",name:"Cocos (Keeling) Islands"},{value:"57",name:"Colombia"},{value:"269",name:"Comoros"},{value:"242",name:"Congo"},
      {value:"243",name:"Democratic Republic of the Congo"},{value:"682",name:"Cook Islands"},{value:"506",name:"Costa Rica"},{value:"385",name:"Croatia"},{value:"53",name:"Cuba"},{value:"599",name:"Curacao"},{value:"357",name:"Cyprus"},{value:"420",name:"Czech Republic"},{value:"225",name:"Cote d'Ivoire"},{value:"45",name:"Denmark"},
      {value:"253",name:"Djibouti"},{value:"1-767",name:"Dominica"},{value:"1-809,1-829,1-849",name:"Dominican Republic"},{value:"593",name:"Ecuador"},{value:"20",name:"Egypt"},{value:"503",name:"El Salvador"},{value:"240",name:"Equatorial Guinea"},{value:"291",name:"Eritrea"},{value:"372",name:"Estonia"},{value:"251",name:"Ethiopia"},
      {value:"500",name:"Falkland Islands (Malvinas)"},{value:"298",name:"Faroe Islands"},{value:"679",name:"Fiji"},{value:"358",name:"Finland"},{value:"33",name:"France"},{value:"594",name:"French Guiana"},{value:"689",name:"French Polynesia"},{value:"262",name:"French Southern Territories"},{value:"241",name:"Gabon"},{value:"220",name:"Gambia"},
      {value:"995",name:"Georgia"},{value:"49",name:"Germany"},{value:"233",name:"Ghana"},{value:"350",name:"Gibraltar"},{value:"30",name:"Greece"},{value:"299",name:"Greenland"},{value:"1-473",name:"Grenada"},{value:"590",name:"Guadeloupe"},{value:"1-671",name:"Guam"},{value:"502",name:"Guatemala"},
      {value:"44",name":"Guernsey"},{value:"224",name":"Guinea"},{value:"245",name":"Guinea-Bissau"},{value:"592",name":"Guyana"},{value:"509",name":"Haiti"},{value:"672",name":"Heard Island and McDonald Islands"},{value:"379",name":"Holy See (Vatican City State)"},{value:"504",name":"Honduras"},{value:"852",name":"Hong Kong"},{value:"36",name":"Hungary"},
      {value:"354",name":"Iceland"},{value:"91",name":"India"},{value:"62",name":"Indonesia"},{value:"98",name":"Iran, Islamic Republic of"},{value:"964",name":"Iraq"},{value:"353",name":"Ireland"},{value:"44",name":"Isle of Man"},{value:"972",name":"Israel"},{value":"39",name":"Italy"},{value":}{/* Continues until Zimbabwe */}]

    document.addEventListener("DOMContentLoaded",()=>{
      const regionSelect=document.getElementById("region"),chapterSelect=document.getElementById("chapter"),countrySelect=document.getElementById("delegates-country");
      countries.forEach(c=>{let o=document.createElement("option");o.value=c.value,o.textContent=c.name,countrySelect.appendChild(o)});
      regionSelect.addEventListener("change",function(){chapterSelect.innerHTML='<option value="">Select Chapter</option>';(chapters[this.value]||[]).forEach(ch=>{let o=document.createElement("option");o.value=o.textContent=ch,chapterSelect.appendChild(o)})});
    });
  </script>
</body>
</html>
