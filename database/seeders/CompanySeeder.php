<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::updateOrCreate(['code' => 'TIMS'], [
            'name'                 => 'TIMS',
            'description'          => 'TIMS Performance Management',
            'theme_color'          => '#6366f1',
            'calculation_strategy' => 'tims',
            'status'               => 'active',
        ]);

        Company::updateOrCreate(['code' => 'FOCUZ'], [
            'name'                 => 'FOCUZ',
            'description'          => 'FOCUZ Performance Management',
            'theme_color'          => '#0ea5e9',
            'calculation_strategy' => 'focuz',
            'status'               => 'active',
        ]);
    }
}
