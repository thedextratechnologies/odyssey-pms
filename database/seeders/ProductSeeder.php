<?php
namespace Database\Seeders;
use App\Models\Product;
use App\Models\ProductAddon;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder {
    public function run(): void {
        $products = [
            ['family'=>'orbit','variant'=>'Orbit Micro','description'=>'Ultra-compact 2-passenger circular lift','capacity_persons'=>2,'door_type'=>'Manual / Swing','base_price'=>350000],
            ['family'=>'orbit','variant'=>'Orbit Standard','description'=>'Standard 2-passenger circular lift for duplex & villas','capacity_persons'=>2,'door_type'=>'Manual / Sliding','base_price'=>420000],
            ['family'=>'orbit','variant'=>'Orbit Max','description'=>'Wide-opening 2-passenger with telescopic sliding door','capacity_persons'=>2,'door_type'=>'Telescopic Sliding','base_price'=>520000],
            ['family'=>'apex','variant'=>'Apex Premium','description'=>'6-passenger premium circular lift with EGSS comfort system','capacity_persons'=>6,'door_type'=>'Multiple Options','base_price'=>850000],
            ['family'=>'nova','variant'=>'Nova Standard','description'=>'6-passenger 360° panoramic lift with automatic doors','capacity_persons'=>6,'door_type'=>'Automatic','base_price'=>1200000],
            ['family'=>'nova','variant'=>'Nova Max','description'=>'Flagship full luxury spec with maximum customisation','capacity_persons'=>6,'door_type'=>'Automatic','base_price'=>1600000],
        ];
        foreach ($products as $p) { Product::create($p); }

        $addons = [
            ['name'=>'Additional Floor Stop','category'=>'floors','price'=>45000,'unit'=>'per_floor'],
            ['name'=>'Premium Glass Cabin Finish','category'=>'finish','price'=>85000,'unit'=>'lump_sum'],
            ['name'=>'Standard Cabin Finish','category'=>'finish','price'=>25000,'unit'=>'lump_sum'],
            ['name'=>'Ambient LED Lighting Package','category'=>'lighting','price'=>18000,'unit'=>'lump_sum'],
            ['name'=>'Premium LED Lighting Package','category'=>'lighting','price'=>35000,'unit'=>'lump_sum'],
            ['name'=>'Quantum Motion™ Drive Upgrade','category'=>'drive','price'=>120000,'unit'=>'lump_sum'],
            ['name'=>'Standard Installation Charges','category'=>'installation','price'=>55000,'unit'=>'lump_sum'],
            ['name'=>'Retrofit Installation Charges','category'=>'installation','price'=>85000,'unit'=>'lump_sum'],
            ['name'=>'AMC 1 Year Standard','category'=>'amc','price'=>18000,'unit'=>'per_year'],
            ['name'=>'AMC 1 Year Extended','category'=>'amc','price'=>28000,'unit'=>'per_year'],
            ['name'=>'AMC 1 Year Premium','category'=>'amc','price'=>45000,'unit'=>'per_year'],
        ];
        foreach ($addons as $a) { ProductAddon::create($a); }
        $this->command->info('✅ Products & add-ons seeded');
    }
}
