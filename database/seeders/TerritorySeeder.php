<?php

namespace Database\Seeders;

use App\Models\Territory;
use Illuminate\Database\Seeder;

class TerritorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Tamil Nadu' => [
                'Chennai'   => ['Anna Nagar', 'Adyar', 'T. Nagar', 'Velachery', 'Porur', 'Sholinganallur'],
                'Coimbatore' => ['RS Puram', 'Gandhipuram', 'Peelamedu', 'Saravanampatti'],
                'Madurai'   => ['Anna Nagar', 'KK Nagar', 'Thirunagar'],
                'Salem'     => ['Fairlands', 'Shevapet'],
                'Trichy'    => ['Srirangam', 'Thillai Nagar', 'KK Nagar'],
            ],
            'Karnataka' => [
                'Bengaluru'  => ['Indiranagar', 'Koramangala', 'Whitefield', 'HSR Layout', 'JP Nagar', 'Jayanagar'],
                'Mysuru'    => ['Vijayanagar', 'Kuvempunagar'],
                'Mangaluru' => ['Hampankatta', 'Bejai', 'Kadri'],
                'Hubli'     => ['Vidyanagar', 'Deshpande Nagar'],
            ],
            'Maharashtra' => [
                'Mumbai'    => ['Bandra', 'Andheri', 'Powai', 'Juhu', 'Worli'],
                'Pune'      => ['Koregaon Park', 'Baner', 'Aundh', 'Hadapsar', 'Wakad'],
                'Nagpur'    => ['Civil Lines', 'Dharampeth', 'Sadar'],
                'Nashik'    => ['College Road', 'Gangapur Road'],
            ],
            'Telangana' => [
                'Hyderabad' => ['Banjara Hills', 'Jubilee Hills', 'Gachibowli', 'Madhapur', 'Kondapur', 'Hitec City'],
                'Warangal'  => ['Hanamkonda', 'Kazipet'],
                'Nizamabad' => ['Dichpally', 'Armoor'],
            ],
            'Gujarat' => [
                'Ahmedabad' => ['Navrangpura', 'Satellite', 'Prahlad Nagar', 'Bodakdev', 'Vastrapur'],
                'Surat'     => ['Adajan', 'Vesu', 'Pal', 'Katargam'],
                'Vadodara'  => ['Alkapuri', 'Fatehgunj', 'Sayajigunj'],
                'Rajkot'    => ['Kalawad Road', 'Amin Marg'],
            ],
            'Delhi' => [
                'New Delhi'  => ['Dwarka', 'Rohini', 'Vasant Kunj', 'Greater Kailash', 'Lajpat Nagar'],
                'North Delhi' => ['Model Town', 'Pitampura'],
                'South Delhi' => ['Green Park', 'Saket', 'Malviya Nagar'],
            ],
            'Rajasthan' => [
                'Jaipur'    => ['Vaishali Nagar', 'Mansarovar', 'C-Scheme', 'Malviya Nagar'],
                'Jodhpur'   => ['Shastri Nagar', 'Paota'],
                'Udaipur'   => ['Hiran Magri', 'Fateh Sagar'],
            ],
            'Kerala' => [
                'Kochi'     => ['Kakkanad', 'Edapally', 'Palarivattom', 'Marine Drive'],
                'Thiruvananthapuram' => ['Kowdiar', 'Pattom', 'Vellayambalam'],
                'Kozhikode' => ['Calicut Beach', 'Nadakkavu'],
                'Thrissur'  => ['Swaraj Round', 'Ayyanthole'],
            ],
            'Andhra Pradesh' => [
                'Visakhapatnam' => ['MVP Colony', 'Seethammadhara', 'Madhurawada'],
                'Vijayawada' => ['Benz Circle', 'Moghalrajpuram', 'Gunadala'],
                'Guntur'    => ['Brodipet', 'Arundelpet'],
            ],
            'West Bengal' => [
                'Kolkata'   => ['Salt Lake', 'New Town', 'Park Street', 'Ballygunge', 'Alipore'],
                'Howrah'    => ['Shibpur', 'Salap'],
            ],
        ];

        foreach ($data as $stateName => $districts) {
            $state = Territory::create([
                'type'      => Territory::TYPE_STATE,
                'name'      => $stateName,
                'parent_id' => null,
                'is_active' => true,
            ]);

            foreach ($districts as $districtName => $cities) {
                $district = Territory::create([
                    'type'      => Territory::TYPE_DISTRICT,
                    'name'      => $districtName,
                    'parent_id' => $state->id,
                    'is_active' => true,
                ]);

                foreach ($cities as $cityName) {
                    Territory::create([
                        'type'      => Territory::TYPE_CITY,
                        'name'      => $cityName,
                        'parent_id' => $district->id,
                        'is_active' => true,
                    ]);
                }
            }
        }

        $this->command->info('✅ Territories seeded (' . count($data) . ' states)');
    }
}
