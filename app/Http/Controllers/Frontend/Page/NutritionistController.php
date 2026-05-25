<?php

namespace App\Http\Controllers\Frontend\Page;

use App\Http\Controllers\Controller;
use App\Mail\NutritionistMail;
use App\Models\ContactCoach;
use App\Models\NutritionistContent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class NutritionistController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type ?? null;
        $country = $request->country ?? null;

        $contents = NutritionistContent::find(1);

        $nutritionist_query = User::query()
            // ->where('language', $request->middleware_language_name)
            ->where('is_certified', '1')
            ->where('role', 'student')
            ->where('status', '1')
            ->orderBy('id', 'desc');

        if($type) {
            $nutritionist_query->where($type, '1');
        }

        if($country) {
            $nutritionist_query->where('country', $country);
        }

        if($request->nutritionist) {
            $nutritionist_query->where(function ($query) use ($request) {
                $query->where('first_name', 'like', '%' . $request->nutritionist . '%')
                      ->orWhere('last_name', 'like', '%' . $request->nutritionist . '%')
                      ->orWhere('certificate_number', 'like', '%' . $request->nutritionist . '%')
                      ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $request->nutritionist . '%')
                      ->orderBy('id', 'desc');
            });
        }

        $nutritionists = $nutritionist_query->paginate(20)->appends($request->except(['middleware_language', 'middleware_language_name']));

        // if($nutritionists->isEmpty() && $request->middleware_language_name != 'English') {
        //     $nutritionist_query = User::query()
        //         ->where('language', 'English')
        //         ->where('is_certified', '1')
        //         ->where('role', 'student')
        //         ->where('status', '1');

        //     if($type) {
        //         $nutritionist_query->where($type, '1');
        //     }
    
        //     if($country) {
        //         $nutritionist_query->where('country', $country);
        //     }

        //     if($request->nutritionist) {
        //         $nutritionist_query->where(function ($query) use ($request) {
        //             $query->where('first_name', 'like', '%' . $request->nutritionist . '%')
        //                   ->orWhere('last_name', 'like', '%' . $request->nutritionist . '%')
        //                   ->orWhere('certificate_number', 'like', '%' . $request->nutritionist . '%')
        //                   ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $request->nutritionist . '%');
        //         });
        //     }

        //     $nutritionists = $nutritionist_query->paginate(20)->appends($request->except(['middleware_language', 'middleware_language_name']));
        // }

        $countries = [
            "Afghanistan",
            "Aland Islands",
            "Albania",
            "Algeria",
            "American Samoa",
            "Andorra",
            "Angola",
            "Anguilla",
            "Antarctica",
            "Antigua and Barbuda",
            "Argentina",
            "Armenia",
            "Aruba",
            "Australia",
            "Austria",
            "Azerbaijan",
            "Bahamas",
            "Bahrain",
            "Bangladesh",
            "Barbados",
            "Belarus",
            "Belgium",
            "Belize",
            "Benin",
            "Bermuda",
            "Bhutan",
            "Bolivia",
            "Bonaire, Sint Eustatius and Saba",
            "Bosnia and Herzegovina",
            "Botswana",
            "Bouvet Island",
            "Brazil",
            "British Indian Ocean Territory",
            "Brunei Darussalam",
            "Bulgaria",
            "Burkina Faso",
            "Burundi",
            "Cambodia",
            "Cameroon",
            "Canada",
            "Cape Verde",
            "Cayman Islands",
            "Central African Republic",
            "Chad",
            "Chile",
            "China",
            "Christmas Island",
            "Cocos (Keeling) Islands",
            "Colombia",
            "Comoros",
            "Congo",
            "Congo, Democratic Republic of the Congo",
            "Cook Islands",
            "Costa Rica",
            "Cote D'Ivoire",
            "Croatia",
            "Cuba",
            "Curacao",
            "Cyprus",
            "Czech Republic",
            "Denmark",
            "Djibouti",
            "Dominica",
            "Dominican Republic",
            "Ecuador",
            "Egypt",
            "El Salvador",
            "Equatorial Guinea",
            "Eritrea",
            "Estonia",
            "Ethiopia",
            "Falkland Islands (Malvinas)",
            "Faroe Islands",
            "Fiji",
            "Finland",
            "France",
            "French Guiana",
            "French Polynesia",
            "French Southern Territories",
            "Gabon",
            "Gambia",
            "Georgia",
            "Germany",
            "Ghana",
            "Gibraltar",
            "Greece",
            "Greenland",
            "Grenada",
            "Guadeloupe",
            "Guam",
            "Guatemala",
            "Guernsey",
            "Guinea",
            "Guinea-Bissau",
            "Guyana",
            "Haiti",
            "Heard Island and Mcdonald Islands",
            "Holy See (Vatican City State)",
            "Honduras",
            "Hong Kong",
            "Hungary",
            "Iceland",
            "India",
            "Indonesia",
            "Iran, Islamic Republic of",
            "Iraq",
            "Ireland",
            "Isle of Man",
            "Israel",
            "Italy",
            "Jamaica",
            "Japan",
            "Jersey",
            "Jordan",
            "Kazakhstan",
            "Kenya",
            "Kiribati",
            "Korea, Democratic People's Republic of",
            "Korea, Republic of",
            "Kosovo",
            "Kuwait",
            "Kyrgyzstan",
            "Lao People's Democratic Republic",
            "Latvia",
            "Lebanon",
            "Lesotho",
            "Liberia",
            "Libyan Arab Jamahiriya",
            "Liechtenstein",
            "Lithuania",
            "Luxembourg",
            "Macao",
            "Macedonia, the Former Yugoslav Republic of",
            "Madagascar",
            "Malawi",
            "Malaysia",
            "Maldives",
            "Mali",
            "Malta",
            "Marshall Islands",
            "Martinique",
            "Mauritania",
            "Mauritius",
            "Mayotte",
            "Mexico",
            "Micronesia, Federated States of",
            "Moldova, Republic of",
            "Monaco",
            "Mongolia",
            "Montenegro",
            "Montserrat",
            "Morocco",
            "Mozambique",
            "Myanmar",
            "Namibia",
            "Nauru",
            "Nepal",
            "Netherlands",
            "Netherlands Antilles",
            "New Caledonia",
            "New Zealand",
            "Nicaragua",
            "Niger",
            "Nigeria",
            "Niue",
            "Norfolk Island",
            "Northern Mariana Islands",
            "Norway",
            "Oman",
            "Pakistan",
            "Palau",
            "Palestinian Territory, Occupied",
            "Panama",
            "Papua New Guinea",
            "Paraguay",
            "Peru",
            "Philippines",
            "Pitcairn",
            "Poland",
            "Portugal",
            "Puerto Rico",
            "Qatar",
            "Reunion",
            "Romania",
            "Russian Federation",
            "Rwanda",
            "Saint Barthelemy",
            "Saint Helena",
            "Saint Kitts and Nevis",
            "Saint Lucia",
            "Saint Martin",
            "Saint Pierre and Miquelon",
            "Saint Vincent and the Grenadines",
            "Samoa",
            "San Marino",
            "Sao Tome and Principe",
            "Saudi Arabia",
            "Senegal",
            "Serbia",
            "Serbia and Montenegro",
            "Seychelles",
            "Sierra Leone",
            "Singapore",
            "Sint Maarten",
            "Slovakia",
            "Slovenia",
            "Solomon Islands",
            "Somalia",
            "South Africa",
            "South Georgia and the South Sandwich Islands",
            "South Sudan",
            "Spain",
            "Sri Lanka",
            "Sudan",
            "Suriname",
            "Svalbard and Jan Mayen",
            "Swaziland",
            "Sweden",
            "Switzerland",
            "Syrian Arab Republic",
            "Taiwan, Province of China",
            "Tajikistan",
            "Tanzania, United Republic of",
            "Thailand",
            "Timor-Leste",
            "Togo",
            "Tokelau",
            "Tonga",
            "Trinidad and Tobago",
            "Tunisia",
            "Turkey",
            "Turkmenistan",
            "Turks and Caicos Islands",
            "Tuvalu",
            "Uganda",
            "Ukraine",
            "United Arab Emirates",
            "United Kingdom",
            "United States",
            "United States Minor Outlying Islands",
            "Uruguay",
            "Uzbekistan",
            "Vanuatu",
            "Venezuela",
            "Viet Nam",
            "Virgin Islands, British",
            "Virgin Islands, U.s.",
            "Wallis and Futuna",
            "Western Sahara",
            "Yemen",
            "Zambia",
            "Zimbabwe"
        ];

        return view('frontend.pages.nutritionists', [
            'contents' => $contents,
            'nutritionists' => $nutritionists,
            'filter_nutritionist' => $request->nutritionist,
            'filter_country' => $request->country,
            'countries' => $countries
        ]);
    }

    public function fetch(User $nutritionist)
    {
        $url = url("nutritionists?nutritionist-id=" . $nutritionist->id);

        $qrcode = QrCode::generate($url);

        $html = 'data:image/svg+xml;base64,' . base64_encode($qrcode);

        return response()->json([
            'nutritionist' => $nutritionist,
            'html' => $html,
            'credentials' => userCredentials($nutritionist->id)
        ]);
    }

    public function contact(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'phone' => 'required|regex:/^\+?[0-9]+$/'
        // ]);

        // if($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Message sending failed');
        // }

        $nutritionist = User::find($request->nutritionist);

        $contact_coach = new ContactCoach();
        $data = $request->except('middleware_language', 'middleware_language_name', 'nutritionist');
        $data['user'] = $nutritionist->id;
        $data['date'] = Carbon::now()->toDateString();
        $data['status'] = '1';
        $contact_coach->create($data);

        $mail_data = [
            'nutritionist' => $nutritionist,
            'request' => $data
        ];

        send_email(new NutritionistMail($mail_data, 'user'), $request->email);
        send_email(new NutritionistMail($mail_data, 'nutritionist'), $nutritionist->email);
        send_email(new NutritionistMail($mail_data, 'admin'), config('app.admin_emails'));

        return redirect()->back()->with('success', 'Message sent successfully. We usually reply within 5 working days. Thank you for your patience.');
    }
}