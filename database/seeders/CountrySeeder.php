<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
            [
                'name' => 'Afghanistan',
                'phone_code' => '+93'
            ],
            [
                'name' => 'Albania',
                'phone_code' => '+355'
            ],
            [
                'name' => 'Algeria',
                'phone_code' => '+213'
            ],
            [
                'name' => 'American Samoa',
                'phone_code' => '+684'
            ],
            [
                'name' => 'Andorra',
                'phone_code' => '+376'
            ],
            [
                'name' => 'Angola',
                'phone_code' => '+244'
            ],
            [
                'name' => 'Anguilla',
                'phone_code' => '+1264'
            ],
            [
                'name' => 'Antarctica',
                'phone_code' => '+672'
            ],
            [
                'name' => 'Antigua',
                'phone_code' => '+1268'
            ],
            [
                'name' => 'Argentina',
                'phone_code' => '+54'
            ],
            [
                'name' => 'Armenia',
                'phone_code' => '+374'
            ],
            [
                'name' => 'Aruba',
                'phone_code' => '+297'
            ],
            [
                'name' => 'Ascension',
                'phone_code' => '+247'
            ],
            [
                'name' => 'Australia',
                'phone_code' => '+61'
            ],
            [
                'name' => 'Australian External Territories',
                'phone_code' => '+672'
            ],
            [
                'name' => 'Austria',
                'phone_code' => '+43'
            ],
            [
                'name' => 'Azerbaijan',
                'phone_code' => '+994'
            ],
            [
                'name' => 'Bahamas',
                'phone_code' => '+1242'
            ],
            [
                'name' => 'Bahrain',
                'phone_code' => '+973'
            ],
            [
                'name' => 'Bangladesh',
                'phone_code' => '+880'
            ],
            [
                'name' => 'Barbados',
                'phone_code' => '+1246'
            ],
            [
                'name' => 'Belarus',
                'phone_code' => '+375'
            ],
            [
                'name' => 'Belgium',
                'phone_code' => '+32'
            ],
            [
                'name' => 'Belize',
                'phone_code' => '+501'
            ],
            [
                'name' => 'Benin',
                'phone_code' => '+229'
            ],
            [
                'name' => 'Bermuda',
                'phone_code' => '+1441'
            ],
            [
                'name' => 'Bhutan',
                'phone_code' => '+975'
            ],
            [
                'name' => 'Bolivia',
                'phone_code' => '+591'
            ],
            [
                'name' => 'Bosnia & Herzegovina',
                'phone_code' => '+387'
            ],
            [
                'name' => 'Botswana',
                'phone_code' => '+267'
            ],
            [
                'name' => 'Brazil',
                'phone_code' => '+55'
            ],
            [
                'name' => 'British Virgin Islands',
                'phone_code' => '+1284'
            ],
            [
                'name' => 'Brunei Darussalam',
                'phone_code' => '+673'
            ],
            [
                'name' => 'Bulgaria',
                'phone_code' => '+359'
            ],
            [
                'name' => 'Burkina Faso',
                'phone_code' => '+226'
            ],
            [
                'name' => 'Burundi',
                'phone_code' => '+257'
            ],
            [
                'name' => 'Cambodia',
                'phone_code' => '+855'
            ],
            [
                'name' => 'Cameroon',
                'phone_code' => '+237'
            ],
            [
                'name' => 'Canada',
                'phone_code' => '+1'
            ],
            [
                'name' => 'Cape Verde Islands',
                'phone_code' => '+238'
            ],
            [
                'name' => 'Cayman Islands',
                'phone_code' => '+1345'
            ],
            [
                'name' => 'Central African Republic',
                'phone_code' => '+236'
            ],
            [
                'name' => 'Chad',
                'phone_code' => '+235'
            ],
            [
                'name' => 'Chatham Islands (New Zealand)',
                'phone_code' => '+64'
            ],
            [
                'name' => 'Chile',
                'phone_code' => '+56'
            ],
            [
                'name' => 'China',
                'phone_code' => '+86'
            ],
            [
                'name' => 'Christmas Island',
                'phone_code' => '+61'
            ],
            [
                'name' => 'Colombia',
                'phone_code' => '+57'
            ],
            [
                'name' => 'Comoros',
                'phone_code' => '+269'
            ],
            [
                'name' => 'Congo',
                'phone_code' => '+242'
            ],
            [
                'name' => 'Congo, the Democratic Republic of the (Zaire)',
                'phone_code' => '+243'
            ],
            [
                'name' => 'Cook Islands',
                'phone_code' => '+682'
            ],
            [
                'name' => 'Costa Rica',
                'phone_code' => '+506'
            ],
            [
                'name' => 'Ivory Coast',
                'phone_code' => '+225'
            ],
            [
                'name' => 'Croatia',
                'phone_code' => '+385'
            ],
            [
                'name' => 'Cuba',
                'phone_code' => '+53'
            ],
            [
                'name' => 'Cyprus',
                'phone_code' => '+357'
            ],
            [
                'name' => 'Czech Republic',
                'phone_code' => '+420'
            ],
            [
                'name' => 'Denmark',
                'phone_code' => '+45'
            ],
            [
                'name' => 'Diego Garcia',
                'phone_code' => '+246'
            ],
            [
                'name' => 'Djibouti',
                'phone_code' => '+253'
            ],
            [
                'name' => 'Dominica',
                'phone_code' => '+1767'
            ],
            [
                'name' => 'Dominican Republic',
                'phone_code' => '+1809'
            ],
            [
                'name' => 'East Timor',
                'phone_code' => '+670'
            ],
            [
                'name' => 'Easter Island',
                'phone_code' => '+56'
            ],
            [
                'name' => 'Ecuador',
                'phone_code' => '+593'
            ],
            [
                'name' => 'Egypt',
                'phone_code' => '+20'
            ],
            [
                'name' => 'El Salvador',
                'phone_code' => '+503'
            ],
            [
                'name' => 'Equatorial Guinea',
                'phone_code' => '+240'
            ],
            [
                'name' => 'Eritrea',
                'phone_code' => '+291'
            ],
            [
                'name' => 'Estonia',
                'phone_code' => '+372'
            ],
            [
                'name' => 'Ethiopia',
                'phone_code' => '+251'
            ],
            [
                'name' => 'Falkland Islands',
                'phone_code' => '+500'
            ],
            [
                'name' => 'Faroe Islands',
                'phone_code' => '+298'
            ],
            [
                'name' => 'Fiji Islands',
                'phone_code' => '+679'
            ],
            [
                'name' => 'Finland',
                'phone_code' => '+358'
            ],
            [
                'name' => 'France',
                'phone_code' => '+33'
            ],
            [
                'name' => 'French Antilles',
                'phone_code' => '+596'
            ],
            [
                'name' => 'French Guiana',
                'phone_code' => '+594'
            ],
            [
                'name' => 'French Polynesia',
                'phone_code' => '+689'
            ],
            [
                'name' => 'Gabonese Republic',
                'phone_code' => '+241'
            ],
            [
                'name' => 'Gambia',
                'phone_code' => '+220'
            ],
            [
                'name' => 'Georgia',
                'phone_code' => '+995'
            ],
            [
                'name' => 'Germany',
                'phone_code' => '+49'
            ],
            [
                'name' => 'Ghana',
                'phone_code' => '+233'
            ],
            [
                'name' => 'Gibraltar',
                'phone_code' => '+350'
            ],
            [
                'name' => 'Greece',
                'phone_code' => '+30'
            ],
            [
                'name' => 'Greenland',
                'phone_code' => '+299'
            ],
            [
                'name' => 'Grenada',
                'phone_code' => '+1473'
            ],
            [
                'name' => 'Guadeloupe',
                'phone_code' => '+590'
            ],
            [
                'name' => 'Guam',
                'phone_code' => '+1671'
            ],
            [
                'name' => 'Guatemala',
                'phone_code' => '+502'
            ],
            [
                'name' => 'Guinea Bissau',
                'phone_code' => '+245'
            ],
            [
                'name' => 'Guinea',
                'phone_code' => '+224'
            ],
            [
                'name' => 'Guyana',
                'phone_code' => '+592'
            ],
            [
                'name' => 'Haiti',
                'phone_code' => '+509'
            ],
            [
                'name' => 'Honduras',
                'phone_code' => '+504'
            ],
            [
                'name' => 'Hong Kong',
                'phone_code' => '+852'
            ],
            [
                'name' => 'Hungary',
                'phone_code' => '+36'
            ],
            [
                'name' => 'Iceland',
                'phone_code' => '+354'
            ],
            [
                'name' => 'India',
                'phone_code' => '+91'
            ],
            [
                'name' => 'Indonesia',
                'phone_code' => '+62'
            ],
            [
                'name' => 'Iran',
                'phone_code' => '+98'
            ],
            [
                'name' => 'Iraq',
                'phone_code' => '+964'
            ],
            [
                'name' => 'Ireland',
                'phone_code' => '+353'
            ],
            [
                'name' => 'Israel',
                'phone_code' => '+972'
            ],
            [
                'name' => 'Italy',
                'phone_code' => '+39'
            ],
            [
                'name' => 'Jamaica',
                'phone_code' => '+1876'
            ],
            [
                'name' => 'Japan',
                'phone_code' => '+81'
            ],
            [
                'name' => 'Jordan',
                'phone_code' => '+962'
            ],
            [
                'name' => 'Kazakhstan',
                'phone_code' => '+7'
            ],
            [
                'name' => 'Kenya',
                'phone_code' => '+254'
            ],
            [
                'name' => 'Kiribati',
                'phone_code' => '+686'
            ],
            [
                'name' => 'Korea (North',
                'phone_code' => '+850'
            ],
            [
                'name' => 'Korea (South',
                'phone_code' => '+82'
            ],
            [
                'name' => 'Kuwait',
                'phone_code' => '+965'
            ],
            [
                'name' => 'Kyrgyz Republic',
                'phone_code' => '+996'
            ],
            [
                'name' => 'Laos',
                'phone_code' => '+856'
            ],
            [
                'name' => 'Latvia',
                'phone_code' => '+371'
            ],
            [
                'name' => 'Lebanon',
                'phone_code' => '+961'
            ],
            [
                'name' => 'Lesotho',
                'phone_code' => '+266'
            ],
            [
                'name' => 'Liberia',
                'phone_code' => '+231'
            ],
            [
                'name' => 'Libya',
                'phone_code' => '+218'
            ],
            [
                'name' => 'Liechtenstein',
                'phone_code' => '+423'
            ],
            [
                'name' => 'Lithuania',
                'phone_code' => '+370'
            ],
            [
                'name' => 'Luxembourg',
                'phone_code' => '+352'
            ],
            [
                'name' => 'Macao',
                'phone_code' => '+853'
            ],
            [
                'name' => 'Macedonia',
                'phone_code' => '+389'
            ],
            [
                'name' => 'Madagascar',
                'phone_code' => '+261'
            ],
            [
                'name' => 'Malawi',
                'phone_code' => '+265'
            ],
            [
                'name' => 'Malaysia',
                'phone_code' => '+60'
            ],
            [
                'name' => 'Maldives',
                'phone_code' => '+960'
            ],
            [
                'name' => 'Mali Republic',
                'phone_code' => '+223'
            ],
            [
                'name' => 'Malta',
                'phone_code' => '+356'
            ],
            [
                'name' => 'Marshall Islands',
                'phone_code' => '+692'
            ],
            [
                'name' => 'Martinique',
                'phone_code' => '+596'
            ],
            [
                'name' => 'Mauritania',
                'phone_code' => '+222'
            ],
            [
                'name' => 'Mauritius',
                'phone_code' => '+230'
            ],
            [
                'name' => 'Mayotte Island',
                'phone_code' => '+269'
            ],
            [
                'name' => 'Mexico',
                'phone_code' => '+52'
            ],
            [
                'name' => 'Micronesia',
                'phone_code' => '+691'
            ],
            [
                'name' => 'Midway Island',
                'phone_code' => '+1808'
            ],
            [
                'name' => 'Moldova',
                'phone_code' => '+373'
            ],
            [
                'name' => 'Monaco',
                'phone_code' => '+377'
            ],
            [
                'name' => 'Mongolia',
                'phone_code' => '+976'
            ],
            [
                'name' => 'Montserrat',
                'phone_code' => '+1664'
            ],
            [
                'name' => 'Morocco',
                'phone_code' => '+212'
            ],
            [
                'name' => 'Mozambique',
                'phone_code' => '+258'
            ],
            [
                'name' => 'Myanmar',
                'phone_code' => '+95'
            ],
            [
                'name' => 'Namibia',
                'phone_code' => '+264'
            ],
            [
                'name' => 'Nauru',
                'phone_code' => '+674'
            ],
            [
                'name' => 'Nepal',
                'phone_code' => '+977'
            ],
            [
                'name' => 'Netherlands',
                'phone_code' => '+31'
            ],
            [
                'name' => 'Netherlands Antilles',
                'phone_code' => '+599'
            ],
            [
                'name' => 'Nevis',
                'phone_code' => '+1869'
            ],
            [
                'name' => 'New Caledonia',
                'phone_code' => '+687'
            ],
            [
                'name' => 'New Zealand',
                'phone_code' => '+64'
            ],
            [
                'name' => 'Nicaragua',
                'phone_code' => '+505'
            ],
            [
                'name' => 'Niger',
                'phone_code' => '+227'
            ],
            [
                'name' => 'Nigeria',
                'phone_code' => '+234'
            ],
            [
                'name' => 'Niue',
                'phone_code' => '+683'
            ],
            [
                'name' => 'Norfolk Island',
                'phone_code' => '+672'
            ],
            [
                'name' => 'Northern Marine Islands',
                'phone_code' => '+1670'
            ],
            [
                'name' => 'Norway',
                'phone_code' => '+47'
            ],
            [
                'name' => 'Oman',
                'phone_code' => '+968'
            ],
            [
                'name' => 'Pakistan',
                'phone_code' => '+92'
            ],
            [
                'name' => 'Palau',
                'phone_code' => '+680'
            ],
            [
                'name' => 'Panama',
                'phone_code' => '+507'
            ],
            [
                'name' => 'Papua New Guinea',
                'phone_code' => '+675'
            ],
            [
                'name' => 'Paraguay',
                'phone_code' => '+595'
            ],
            [
                'name' => 'Peru',
                'phone_code' => '+51'
            ],
            [
                'name' => 'Philippines',
                'phone_code' => '+63'
            ],
            [
                'name' => 'Poland',
                'phone_code' => '+48'
            ],
            [
                'name' => 'Portugal',
                'phone_code' => '+351'
            ],
            [
                'name' => 'Puerto Rico',
                'phone_code' => '+1787'
            ],
            [
                'name' => 'Qatar',
                'phone_code' => '+974'
            ],
            [
                'name' => 'Reunion Island',
                'phone_code' => '+262'
            ],
            [
                'name' => 'Romania',
                'phone_code' => '+40'
            ],
            [
                'name' => 'Russia',
                'phone_code' => '+7'
            ],
            [
                'name' => 'Rwanda',
                'phone_code' => '+250'
            ],
            [
                'name' => 'St. Helena',
                'phone_code' => '+290'
            ],
            [
                'name' => 'St. Kitts/Nevis',
                'phone_code' => '+1869'
            ],
            [
                'name' => 'St Lucia',
                'phone_code' => '+1758'
            ],
            [
                'name' => 'St. Pierre & Miquelon',
                'phone_code' => '+508'
            ],
            [
                'name' => 'St. Vincent & Grenadines',
                'phone_code' => '+1784'
            ],
            [
                'name' => 'San Marino',
                'phone_code' => '+378'
            ],
            [
                'name' => 'Sao Tomo and Principe',
                'phone_code' => '+239'
            ],
            [
                'name' => 'Saudi Arabia',
                'phone_code' => '+966'
            ],
            [
                'name' => 'Senegal',
                'phone_code' => '+221'
            ],
            [
                'name' => 'Serbia & Montenegro',
                'phone_code' => '+381'
            ],
            [
                'name' => 'Seychelles Republic',
                'phone_code' => '+248'
            ],
            [
                'name' => 'Sierra Leone',
                'phone_code' => '+232'
            ],
            [
                'name' => 'Singapore',
                'phone_code' => '+65'
            ],
            [
                'name' => 'Slovak Republic',
                'phone_code' => '+421'
            ],
            [
                'name' => 'Slovenia',
                'phone_code' => '+386'
            ],
            [
                'name' => 'Solomon Islands',
                'phone_code' => '+677'
            ],
            [
                'name' => 'Somalia',
                'phone_code' => '+252'
            ],
            [
                'name' => 'South Africa',
                'phone_code' => '+27'
            ],
            [
                'name' => 'Spain',
                'phone_code' => '+34'
            ],
            [
                'name' => 'Sri Lanka',
                'phone_code' => '+94'
            ],
            [
                'name' => 'Sudan',
                'phone_code' => '+249'
            ],
            [
                'name' => 'Suriname',
                'phone_code' => '+597'
            ],
            [
                'name' => 'Swaziland',
                'phone_code' => '+268'
            ],
            [
                'name' => 'Sweden',
                'phone_code' => '+46'
            ],
            [
                'name' => 'Switzerland',
                'phone_code' => '+41'
            ],
            [
                'name' => 'Syria',
                'phone_code' => '+963'
            ],
            [
                'name' => 'Taiwan',
                'phone_code' => '+886'
            ],
            [
                'name' => 'Tajikistan',
                'phone_code' => '+992'
            ],
            [
                'name' => 'Tanzania',
                'phone_code' => '+255'
            ],
            [
                'name' => 'Thailand',
                'phone_code' => '+66'
            ],
            [
                'name' => 'Togolese Republic',
                'phone_code' => '+228'
            ],
            [
                'name' => 'Tokelau',
                'phone_code' => '+690'
            ],
            [
                'name' => 'Tonga Islands',
                'phone_code' => '+676'
            ],
            [
                'name' => 'Trinidad & Tobago',
                'phone_code' => '+1868'
            ],
            [
                'name' => 'Tunisia',
                'phone_code' => '+216'
            ],
            [
                'name' => 'Turkey',
                'phone_code' => '+90'
            ],
            [
                'name' => 'Turkmenistan',
                'phone_code' => '+993'
            ],
            [
                'name' => 'Turks & Caicos Islands',
                'phone_code' => '+1649'
            ],
            [
                'name' => 'Tuvalu',
                'phone_code' => '+688'
            ],
            [
                'name' => 'Uganda',
                'phone_code' => '+256'
            ],
            [
                'name' => 'Ukraine',
                'phone_code' => '+380'
            ],
            [
                'name' => 'United Arab Emirates',
                'phone_code' => '+971'
            ],
            [
                'name' => 'United Kingdom',
                'phone_code' => '+44'
            ],
            [
                'name' => 'USA',
                'phone_code' => '+1'
            ],
            [
                'name' => 'US Virgin Islands',
                'phone_code' => '+1340'
            ],
            [
                'name' => 'Uruguay',
                'phone_code' => '+598'
            ],
            [
                'name' => 'Uzbekistan',
                'phone_code' => '+998'
            ],
            [
                'name' => 'Vanuatu',
                'phone_code' => '+678'
            ],
            [
                'name' => 'Vietnam',
                'phone_code' => '+84'
            ],
            [
                'name' => 'Venezuela',
                'phone_code' => '+58'
            ],
            [
                'name' => 'Yemen',
                'phone_code' => '+998'
            ],
            [
                'name' => 'Zambia',
                'phone_code' => '+260'
            ],
            [
                'name' => 'Zimbabwe',
                'phone_code' => '+263'
            ],
        ];

        foreach ($countries as $country) {
            if (!Country::where('name', $country['name'])->first()) {
                Country::create([
                    'name' => $country['name'],
                    'phone_code' => $country['phone_code']
                ]);
            }
        }
    }
}
